<?php

namespace App\Services;

use App\Orchid\Fields\Cropper;
use App\Orchid\Fields\TinyMce;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Code;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Fields\Upload;

class ConfiguratorService
{
    /**
     * @param string $fieldType
     * @param string $name
     * @param string $label
     * @param mixed $value
     * @param ?string $mask
     * @param bool $isRequired
     * @return Field
     */
    public function getField(string $fieldType, string $name, string $label, mixed $value, ?string $mask, bool $isRequired = false): Field
    {
        return match ($fieldType) {
            Input::class =>Input::make($name)->mask($mask ?? '')->title($label)->value($value)->required($isRequired),
            TextArea::class =>TextArea::make($name)->title($label)->value($value)->required($isRequired),
            Cropper::class =>Cropper::make($name)->title($label)->value($value)->targetRelativeUrl()->required($isRequired),
            TinyMce::class =>TinyMce::make($name)->title($label)->value($value)->required($isRequired),
            Upload::class =>Upload::make($name)->title($label)->value($value)->required($isRequired),
            Code::class =>Code::make($name)->title($label)->value($value)->required($isRequired),
            default => null
        };
    }
}
