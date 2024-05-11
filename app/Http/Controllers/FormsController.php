<?php

namespace App\Http\Controllers;

use App\Helpers\CommonHelper;
use App\Mail\MailService;
use App\Traits\CommonResponsesTrait;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FormsController extends Controller
{
    protected array $params;

    use CommonResponsesTrait;

    /**
     * Метод получения шаблонов вёрстки для стандартных форм
     * @param string $code
     * @return Application|Factory|View
     */
    public function getForm(string $code)
    {
        $this->prepare($code);
        return view($this->params['form_view']);
    }

    /**
     * Метод отправки стандартных форм
     * @param Request $request
     * @param string $code
     * @return JsonResponse
     */
    public function sendForm(Request $request, string $code): JsonResponse
    {
        $this->prepare($code);
        $validator = Validator::make($request->all(), $this->params['rules'], $this->params['messages']);

        if ($validator->fails()) {
            return $this->responseFail(
                collect($validator->errors()->messages())->map(fn($error, $name) => $error[0])->toArray()
            );
        }

        $payload = $validator->validated();
        $mailer = new MailService();
        $mailer->sendMail($payload, $this->params['subject'], $this->params['letter_view'], $this->params['mail_key'], $request->allFiles());

        return $this->responseOk();
    }

    protected function prepare(string $code): void
    {
        $this->params = CommonHelper::getPreset('forms.' . $code);
        abort_if(!$this->params, 404);
    }
}
