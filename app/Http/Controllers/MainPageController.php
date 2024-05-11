<?php

namespace App\Http\Controllers;

use App\Enums\ClientRoutes;
use App\Helpers\CommonHelper;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductSubCategory;
use App\Services\ArticlesService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MainPageController extends Controller
{
    public function index()
    {
        return view('layout');
    }

    public function getCategories(): JsonResponse
    {
        $categories = ProductCategory::query()->active()->get();

        return response()->json($categories);
    }

    public function getSubCategories(Request $request): JsonResponse
    {
        $categoryId = $request->get('category_id');
        $subCategories = ProductSubCategory::query()->active()->whereHas('productCategories', function ($query) use ($categoryId) {
            $query->active()->where('product_categories.id', $categoryId);
        })->get();

        return response()->json($subCategories);
    }

    public function getProducts(Request $request): JsonResponse
    {
        $subCategoryId = $request->get('sub_category_id');
        $products = Product::query()->active()->whereHas('productSubCategories', function ($query) use ($subCategoryId) {
            $query->active()->where('product_sub_categories.id', $subCategoryId);
        })->with(['productSubCategories' => function ($query) {
            $query->select('id', 'title', 'code');
        }, 'productSubCategories.productCategories' => function ($query) {
            $query->select('product_categories.id', 'title', 'code');
        }])->get();

        return response()->json($products);
    }

    public function getProduct(Request $request): JsonResponse
    {
        $productId = $request->get('product_id');
        $product = Product::query()->active()->find($productId)->load(['productSubCategories' => function ($query) {
            $query->select('id', 'title', 'code');
        }, 'productSubCategories.productCategories' => function ($query) {
            $query->select('product_categories.id', 'title', 'code');
        }]);

        return response()->json($product);
    }
}
