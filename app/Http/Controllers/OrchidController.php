<?php

namespace App\Http\Controllers;

use App\Enums\ClientRoutes;
use App\Enums\ModalValidation;
use App\Helpers\CommonHelper;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Services\ArticlesService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class OrchidController extends Controller
{
    public function validateForm(FormRequest $request)
    {
        $formData = $request->input('item');

        if (array_key_exists('title', $formData) && array_key_exists('code', $formData)) {
            if (empty($formData['code']) && !empty($formData['title'])) {
                $formData['code'] = Str::slug($formData['title']);
            }
        }

        if ($formData['modal_id'] === ModalValidation::SEO_MODAL->value && $formData['url']) {
            $formData['url'] = Str::finish(Str::start($formData['url'], '/'), '/');
        }

        $rules = ModalValidation::from($formData['modal_id'])->getRules($formData['id'] ?? null);
        $messages = ModalValidation::from($formData['modal_id'])->getMessages();

        $validator = Validator::make($formData, $rules, $messages);

        if ($validator->fails()) {
            $errors = collect($validator->messages()->messages())->map(fn($error, $name) => $error[0])->toArray();
            return response()->json(['success' => false, 'errors' => $errors]);
        }

        return response()->json(['success' => true]);
    }
}
