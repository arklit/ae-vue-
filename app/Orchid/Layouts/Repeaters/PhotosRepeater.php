<?php

namespace App\Orchid\Layouts\Repeaters;

use App\Orchid\Fields\Cropper;
use App\Orchid\Fields\TinyMce;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Rows;

class PhotosRepeater extends Rows
{
    function fields(): array
    {
        return [
            Cropper::make('image')->title('Изображение')->targetRelativeUrl()->required(),
        ];
    }
}
