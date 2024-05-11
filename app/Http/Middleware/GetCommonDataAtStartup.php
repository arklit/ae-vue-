<?php

namespace App\Http\Middleware;

use App\Repositories\CommonRepository;
use Closure;
use Illuminate\Http\Request;

class GetCommonDataAtStartup
{
    public function handle(Request $request, Closure $next)
    {
        $configsData = CommonRepository::take()->getConfigurationData();

        if (!empty($configsData)) {
            $configs = [];
            foreach ($configsData as $datum) {
                $configs[$datum['key']] = $datum['value'];
            }
            config($configs);
        }

        return $next($request);
    }
}
