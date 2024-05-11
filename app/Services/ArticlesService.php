<?php

namespace App\Services;

use App\Enums\ClientRoutes;
use App\Models\Article;
use App\Models\ArticleCategory;
use Tabuna\Breadcrumbs\Breadcrumbs;
use Tabuna\Breadcrumbs\Trail;

class ArticlesService
{
    public function getCategoriesWithCurrent(?ArticleCategory $currentCategory = null)
    {
        $categories = ArticleCategory::query()->active()->sorted()->whereHas('articles', fn($q) => $q->active()->publicated())->get();
        if ($currentCategory) {
            $categories->transform(fn($category) => $category->code === $currentCategory->code ? $currentCategory : $category);
        }

        return $categories;
    }

    public function getArticlesList(?int $page = 1, ?string $currentCategoryCode = null)
    {
        $articles = Article::query()
            ->select('*');

        $articles = $currentCategoryCode ?
            $articles->whereHas('category', fn($q) => $q->active()->code($currentCategoryCode)) :
            $articles->whereHas('category', fn($q) => $q->active());

        return $articles->active()
            ->publicated()
            ->publicationSorted()
            ->paginate(perPage: 10, page: $page);
    }
}
