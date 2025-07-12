<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Services\HelperService;
use App\Models\Category;
use App\Models\Product;
use Exception;

class ReportController extends Controller
{
    public function __construct(private HelperService $helper)
    {
    }

    public function analytics()
    {
        try {
            $categories = Category::query()
                ->select(['id', 'name'])
                ->orderBy('name', 'asc')
                ->get();
            $categories = $categories->map(fn($item) => [
                'name'          => $item->name,
                'product_count' => $item->products->count(),
            ]);

            $products = Product::query()
                ->select(['category_id', 'name', 'barcode'])
                ->with(['category:id,name'])
                ->latest()
                ->take(10)
                ->get();

            return $this->helper->entity(['categories' => $categories, 'recent_products' => $products]);
        } catch (Exception $e) {
            return $this->helper->serverException($e);
        }
    }
}
