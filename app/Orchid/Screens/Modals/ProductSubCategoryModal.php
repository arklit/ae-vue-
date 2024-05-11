<?php

namespace App\Orchid\Screens\Modals;

use App\Orchid\Fields\Cropper;
use App\Orchid\Fields\TinyMce;
use Illuminate\Validation\Rules\In;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Rows;
use Orchid\Support\Facades\Layout;

class ProductSubCategoryModal
{
    public static function getModal(): Rows
    {
        return Layout::rows([
            Input::make('item.id')->hidden()->style('margin-bottom: 0'),
            CheckBox::make('item.is_active')->placeholder('Активность')->sendTrueOrFalse()->value(true),
            Input::make('item.title')->title('Заголовок')->required(),
            Input::make('item.code')->title('Код'),
            Input::make('item.sort')->title('Сортировка')->type('number')->value(0)->required(),
            
        ]);
    }
}
