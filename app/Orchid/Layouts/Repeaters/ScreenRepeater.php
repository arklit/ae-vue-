<?php

namespace App\Orchid\Layouts\Repeaters;

use App\Orchid\Fields\TinyMce;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Rows;

class ScreenRepeater extends Rows
{
    function fields(): array
    {
        return [
            Input::make('name')->title('Название')->required(),
            Input::make('code')->title('Код')->required(),
            Input::make('type')->title('Тип')->required(),
            CheckBox::make('is_list')->title('Список')->sendTrueOrFalse()->value(true),
            CheckBox::make('is_edit')->title('Редактирование')->sendTrueOrFalse()->value(true),
        ];
    }
}
