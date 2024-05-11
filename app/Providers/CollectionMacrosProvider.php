<?php

namespace App\Providers;

use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class CollectionMacrosProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        // этот метод нужен для отрисовки пагинации на клиенте
        Collection::macro('paginizate', function () {
            return $this->map(function ($value) {
                if (!is_null($value['url'])) {
                    $is = preg_match('~(p[0-9]+)?\?page=([0-9])+~', $value['url'], $matches);

                    if ($is) {
                        $replacement = ($matches[2] > 1) ? 'p' . $matches[2] : '';
                        $replacement = empty($matches[1]) ? '/' . $replacement : $replacement;
                        if (Str::contains($value['url'], ['ajax/get-filtered-products']) || Str::contains($value['url'], ['search'])) {
                            $url = \Illuminate\Support\Facades\URL::previous();
                            $cleanUrl = rtrim($url, '/');
                            $value['url'] = '';
                            $value['isAjax'] = true;
                        } else {
                            $value['url'] = preg_replace('~(p[0-9]+)?\?page=([0-9])+~', $replacement, $value['url']);
                        }
                    }
                }
                return $value;
            });
        });

        // этот метод нужен для генерации sitemap.xml
        Collection::macro('getPagesLinks', function (string $route, int $perPage, ?string $defaultRoute = null) {
            $routes = [];
            if (!is_null($defaultRoute)) {
                $routes[] = route($defaultRoute);
            }

            $itemsCount = $this->count();
            $pagesCount = ceil($itemsCount / $perPage);

            if ($pagesCount > 1) {
                $pagesRange = range(2, $pagesCount);
                foreach ($pagesRange as $page) {
                    $routes[] = route($route, [$page]);
                }
            }

            return $routes;
        });

        // этот метод нужен для генерации sitemap.xml
        Collection::macro('getPagesLinksCounts', function (string $pagesRoute, int $perPage, array $paramsKeys = [], ?string $defaultRoute = null) {
            return $this->flatMap(function ($itemRow) use ($pagesRoute, $perPage, $paramsKeys, $defaultRoute) {
                $routes = [];
                if (!is_null($defaultRoute)) {
                    $params = $paramsKeys ? array_map(fn($key) => $itemRow[$key], $paramsKeys) : [];
                    $routes[] = route($defaultRoute, $params);
                }

                $itemsCount = $itemRow['count'];
                if ($itemsCount > $perPage) {
                    $params = $paramsKeys ? array_map(fn($key) => $itemRow[$key], $paramsKeys) : [];
                    foreach (range(2, ceil($itemsCount / $perPage)) as $page) {
                        $routes[] = route($pagesRoute, [...$params, $page]);
                    }
                }

                return $routes;
            })->toArray();
        });
    }
}
