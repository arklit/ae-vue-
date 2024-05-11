<?php

namespace App\Http\Controllers;

use App\Enums\ClientRoutes;
use App\Helpers\CommonHelper;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Services\ArticlesService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function getArticlesList(ArticlesService $articlesService, Request $request)
    {
        $data = $request->all();
        $page = $data->page ?: 1;

        $categories = $articlesService->getCategoriesWithCurrent();
        $articles = $articlesService->getArticlesList($page);
        $paginator = $articles->linkCollection()->paginizate();

        return response()->json(['articles' => $articles->items(), 'categories' => $categories, 'paginator' => $paginator, 'page' => $page]);
    }
}
