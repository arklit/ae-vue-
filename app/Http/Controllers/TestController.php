<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class TestController extends Controller
{

    public function getQuery()
    {
        /*dd(123);
        abort_if(!auth('web')->check(), 404);*/

        $routeCollection = Route::getRoutes();
        $routeList = [];
        foreach ($routeCollection as $route) {
            $uri = $route->uri();
            if (Str::startsWith($uri, 'ajax')) {
                $routeList[] = [
                    'method' => $route->methods()[0],
                    'uri'    => $uri,
                    'demo'   => json_encode($this->getDemoData($uri)),
                ];
            }
        }
        return view('test-query', compact('routeList'));
    }

    private function getDemoData(string $uri)
    {
        return match ($uri) {
            'api/sms/send-code' => [
                'phone' => '79110896773',
            ],
            'api/auth/login' => [
                'phone' => '79110896773',
                'code' => '',
            ],
            'api/auth/check-inn' => [
                'inn' => '7703406864',
            ],
            'api/cabinet/update-user' => [
                'name' => 'test',
                'surname' => 'test',
                'email' => 'test',
                'company' => [
                    'inn' => '123123',
                    'billing_check' => 'test',
                    'corporate_check' => 'test',
                    'address' => 'test',
                    'bank' => 'test',
                    'bik' => 'test',
                ]
            ],
            default => []
        };
    }
}
