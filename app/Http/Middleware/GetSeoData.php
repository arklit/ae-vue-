<?php

namespace App\Http\Middleware;

use App\Models\Seo;
use Closure;
use Illuminate\Http\Request;

class GetSeoData
{
    public function handle(Request $request, Closure $next)
    {
        $url = request()->path();
        $formattedUrl = $url === '/' ? '/' : '/' . $url . '/';
        $seo = Seo::query()->where('url', $formattedUrl)->first();

        view()->composer(['layout'],
            function ($view) use ($seo) {
                $view->with(compact('seo'));
            }
        );

        return $next($request);
    }

}
