<?php

// app/Http/Controllers/Api/ProductController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function getProducts(Request $request)
    {
        try {
            $branch_id = auth()->guard('api')->user()->branch_id;

            // Get the search query from the request
            $searchQuery = $request->input('search');

            // Query products based on branch_id and search query
            $products = Product::with('variants')
                ->when($searchQuery, function ($query) use ($searchQuery) {
                    // If search query is provided, filter based on product name
                    $query->where('name', 'like', '%' . $searchQuery . '%');
                })
                ->get();

            // Get available stock information
            $availableStock = $this->availableStock($branch_id);

            // Add availability status to each product
            $products->each(function ($product) use ($availableStock) {
                $availableProduct = $availableStock->firstWhere('prod_id', $product->id);
                $product->availability = $availableProduct ? 'In Stock' : 'Out Of Stock';
                $product->available_stock = $availableProduct ? $availableProduct->qtty : 0;
            });

            return response()->json([
                'success' => true,
                'data' => $products,
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to retrieve: ' . $e->getMessage(),
            ], 500);
        }
    }

    public static function availableStock($branchId)
    {
        return Product::join('product_variants', 'product_variants.product_id', '=', 'products.id')
            ->leftJoin('order_items', 'order_items.variant_id', '=', 'product_variants.id')
            ->leftJoin('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.branch_id', '=', $branchId)
            ->groupBy('products.id', 'product_variants.attribute_values', 'product_variants.id')
            ->select('products.id as prod_id', 'product_variants.attribute_values as attributes', DB::raw('sum(quantity) as qtty'), 'product_variants.id as variant_id')
            ->get();
    }

    public function getCategories()
    {
        try {
            $categories = ProductCategory::orderBy('name', 'asc')->get();

            return response()->json([
                'success' => true,
                'data' => $categories,
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to retrieve categories: ' . $e->getMessage(),
            ], 500);
        }
    }
}

