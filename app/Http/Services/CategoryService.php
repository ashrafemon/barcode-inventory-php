<?php
namespace App\Http\Services;

use App\Enums\ResponseTypes;
use App\Models\Category;
use Exception;

class CategoryService
{
    public function __construct(private HelperService $helper)
    {
    }

    public function getDocs($queries = [])
    {
        try {
            $offset    = $queries['offset'] ?? 10;
            $fields    = ['id', 'name', 'status'];
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

            $docs = Category::query()
                ->when(! empty($queries['status']), fn($q) => $q->where('status', $queries['status']))
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
            $fields    = ['id', 'name', 'status'];
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

            if (! $doc = Category::query()->with($relations)->select($fields)->where($condition)->first()) {
                return $this->helper->funcResponse(['type' => ResponseTypes::ERROR, 'code' => 404, 'message' => 'Category not found...']);
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
            if ($doc = Category::query()->create($body)) {
                return $this->helper->funcResponse([
                    'data' => $doc, 'code' => 201, 'message' => 'Category added successfully',
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

            if (! $doc = Category::query()->where($condition)->first()) {
                return $this->helper->funcResponse(['type' => ResponseTypes::ERROR, 'code' => 404, 'message' => 'Category not found...']);
            }

            $doc->update($body);
            return $this->helper->funcResponse([
                'data' => $doc, 'message' => 'Category updated successfully',
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

            if (! $doc = Category::query()->where($condition)->first()) {
                return $this->helper->funcResponse(['type' => ResponseTypes::ERROR, 'code' => 404, 'message' => 'Category not found...']);
            }

            $doc->delete();
            return $this->helper->funcResponse([
                'data' => $doc, 'message' => 'Category deleted successfully',
            ]);
        } catch (Exception $e) {
            return $this->helper->funcResponse([
                'code' => 500,
                'type' => ResponseTypes::SERVER_ERROR,
                'data' => $e,
            ]);
        }
    }
}
