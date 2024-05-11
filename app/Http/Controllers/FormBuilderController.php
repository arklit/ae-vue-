<?php

namespace App\Http\Controllers;

use App\Helpers\CommonHelper;
use App\Helpers\FormsConfig;
use App\Models\Article;

class FormBuilderController extends Controller
{
    public function getFormConfig(string $code): \Illuminate\Http\JsonResponse
    {
        $formConfig = FormsConfig::getFormByKey($code);
        return response()->json($formConfig);
    }
}
