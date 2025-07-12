<?php
namespace App\Http\Services;

use App\Enums\ResponseTypes;
use App\Models\Product;
use Exception;
use Illuminate\Support\Facades\Http;

class ProductService
{
    public function __construct(private HelperService $helper, private CategoryService $category)
    {
    }

    public function getDocs($queries = [])
    {
        try {
            $offset    = $queries['offset'] ?? 10;
            $fields    = ['id', 'category_id', 'name', 'barcode', 'status'];
            $relations = [];

            $fnArr = $this->helper->getQueryFieldRelations($queries);
            if (! empty($fnArr['fields'])) {
                $fields = $fnArr['fields'];
            }
            if (! empty($fnArr['relations'])) {
                $relations = $fnArr['relations'];
            }

            $orderKey   = $queries['order_field'] ?? 'created_at';
            $orderValue = $queries['order_by'] ?? 'asc';

            $docs = Product::query()
                ->when(! empty($queries['status']), fn($q) => $q->where('status', $queries['status']))
                ->when(! empty($queries['category_id']), fn($q) => $q->where('category_id', $queries['category_id']))
                ->when(! empty($queries['search']), fn($q) => $q->where('name', 'like', '%' . $queries['search'] . '%'))
                ->orderBy($orderKey, $orderValue);

            if (! empty($queries['get_all']) && $queries['get_all']) {
                $docs = $docs->with($relations)->select($fields)->get();
                return $this->helper->funcResponse(['data' => $docs]);
            }

            $docs = $this->helper->paginate($docs->with($relations)->select($fields)->paginate($offset)->toArray());
            return $this->helper->funcResponse(['data' => $docs]);
        } catch (Exception $e) {
            return $this->helper->funcResponse([
                'code' => 500,
                'type' => ResponseTypes::SERVER_ERROR,
                'data' => $e,
            ]);
        }
    }

    public function getDoc($id, $queries = [])
    {
        try {
            $fields    = ['id', 'name', 'barcode', 'status'];
            $relations = [];

            $fnArr = $this->helper->getQueryFieldRelations($queries);
            if (! empty($fnArr['fields'])) {
                $fields = $fnArr['fields'];
            }
            if (! empty($fnArr['relations'])) {
                $relations = $fnArr['relations'];
            }

            $idKey     = $queries['id_key'] ?? 'id';
            $condition = [$idKey => $id];

            if (! $doc = Product::query()->with($relations)->select($fields)->where($condition)->first()) {
                return $this->helper->funcResponse(['type' => ResponseTypes::ERROR, 'code' => 404, 'message' => 'Product not found...']);
            }
            return $this->helper->funcResponse(['data' => $doc]);
        } catch (Exception $e) {
            return $this->helper->funcResponse([
                'code' => 500,
                'type' => ResponseTypes::SERVER_ERROR,
                'data' => $e,
            ]);
        }
    }

    public function createDoc($body)
    {
        try {
            if ($body['store_type'] === Product::ProductStoreType['FETCH']) {
                return $this->addDocByFetch($body);
            }

            if (Product::query()->where(['barcode' => $body['barcode']])->exists()) {
                return $this->helper->funcResponse(['message' => 'A product has already found with this barcode', 'code' => 400, 'type' => ResponseTypes::ERROR]);
            }

            if ($doc = Product::query()->create($body)) {
                return $this->helper->funcResponse([
                    'data' => $doc, 'code' => 201, 'message' => 'Product added successfully',
                ]);
            }
        } catch (Exception $e) {
            return $this->helper->funcResponse([
                'code' => 500,
                'type' => ResponseTypes::SERVER_ERROR,
                'data' => $e,
            ]);
        }
    }

    public function updateDoc($id, $body, $queries = [])
    {
        try {
            $idKey     = $queries['id_key'] ?? 'id';
            $condition = [$idKey => $id];

            if (! $doc = Product::query()->where($condition)->first()) {
                return $this->helper->funcResponse(['type' => ResponseTypes::ERROR, 'code' => 404, 'message' => 'Product not found...']);
            }

            $doc->update($body);
            return $this->helper->funcResponse([
                'data' => $doc, 'message' => 'Product updated successfully',
            ]);
        } catch (Exception $e) {
            return $this->helper->funcResponse([
                'code' => 500,
                'type' => ResponseTypes::SERVER_ERROR,
                'data' => $e,
            ]);
        }
    }

    public function deleteDoc($id, $queries = [])
    {
        try {
            $idKey     = $queries['id_key'] ?? 'id';
            $condition = [$idKey => $id];

            if (! $doc = Product::query()->where($condition)->first()) {
                return $this->helper->funcResponse(['type' => ResponseTypes::ERROR, 'code' => 404, 'message' => 'Product not found...']);
            }

            $doc->delete();
            return $this->helper->funcResponse([
                'data' => $doc, 'message' => 'Product deleted successfully',
            ]);
        } catch (Exception $e) {
            return $this->helper->funcResponse([
                'code' => 500,
                'type' => ResponseTypes::SERVER_ERROR,
                'data' => $e,
            ]);
        }
    }

    private function addDocByFetch($body)
    {
        $getDocRes = $this->getDoc($body['barcode'], ['id_key' => "barcode"]);
        if ($getDocRes['ok']) {
            return $this->helper->funcResponse($getDocRes);
        }

        $fetchProductRes = $this->fetchProductByBarcode($body['barcode']);
        if (! $fetchProductRes['ok']) {
            return $this->helper->funcResponse($fetchProductRes);
        }

        $product = ['name' => $fetchProductRes['data']['name'] ?? '', 'barcode' => $fetchProductRes['data']['barcode'] ?? '', 'category_id' => null];

        $getCategoryRes = $this->category->getDoc('Uncategorized', ['id_key' => 'name']);
        if ($getCategoryRes['ok']) {
            $product['category_id'] = $getCategoryRes['data']['id'] ?? null;
        } else {
            $createCategoryRes = $this->category->createDoc(['name' => 'Uncategorized', 'status' => true]);
            if (! $createCategoryRes['ok']) {
                return $this->helper->funcResponse($createCategoryRes);
            }
            $product['category_id'] = $createCategoryRes['data']['id'] ?? null;
        }

        try {
            $doc = Product::query()->create($product);
            return $this->helper->funcResponse(['data' => $doc, 'message' => 'Product added successfully', 'code' => 201]);
        } catch (Exception $e) {
            return $this->helper->funcResponse([
                'code' => 500,
                'type' => ResponseTypes::SERVER_ERROR,
                'data' => $e,
            ]);
        }
    }

    private function fetchProductByBarcode($barcode)
    {
        try {
            $res = Http::get("https://products-test-aci.onrender.com/product/{$barcode}");
            if (! $res->ok()) {
                return $this->helper->funcResponse([
                    'code'    => 400,
                    'type'    => ResponseTypes::ERROR,
                    'message' => 'Product not found with this barcode',
                ]);
            }

            $data = $res->json();
            if (! $data['status'] || ! $data['product']) {
                return $this->helper->funcResponse([
                    'code'    => 400,
                    'type'    => ResponseTypes::ERROR,
                    'message' => 'Product not found with this barcode',
                ]);
            }

            $payload = ['name' => $data['product']['description'] ?? '', 'barcode' => $data['product']['barcode'] ?? ''];
            return $this->helper->funcResponse(['data' => $payload]);
        } catch (Exception $e) {
            return $this->helper->funcResponse([
                'code' => 500,
                'type' => ResponseTypes::SERVER_ERROR,
                'data' => $e,
            ]);
        }
    }
}
