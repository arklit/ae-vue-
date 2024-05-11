<?php

namespace App\Orchid\Screens;

use App\Enums\OrchidRoutes;
use App\Orchid\Abstractions\EditScreenPattern;
use App\Orchid\Layouts\Repeaters\ScreenRepeater;
use App\Services\ScreenGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Nakukryskin\OrchidRepeaterField\Fields\Repeater;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class CreatorScreen extends EditScreenPattern
{
    public function __construct()
    {
        $this->route = OrchidRoutes::SCREEN_CREATOR;
        $this->redirectTo = $this->route->base();
    }

    public function commandBar()
    {
        return [
            Button::make('Сохранить')->icon('save')->method('save'),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::rows([
                Input::make('item.screen')->title('Screen Name')->required(),
                Input::make('item.model')->title('Model Name'),
                Input::make('item.migration')->title('Migration Name'),
                Repeater::make('item.fields')->layout(ScreenRepeater::class)->title('Fields'),
                Button::make('Сохранить')->method('save'),
            ])
        ];
    }

    public function query()
    {
        $this->name = 'Создание экрана';

        return [
            'robots' => $fileContent ?? '',
        ];
    }

    public function save(Request $request)
    {
        $data = $request->input('item');

        $screenName = $data['screen'];
        $modelName = $data['model'];
        $fields = $data['fields'];

        // Используем сервис для генерации экранов
        $screenGeneratorService = app(ScreenGeneratorService::class);
        $screenGeneratorService->generateListScreen($screenName, $modelName, $fields);
        $screenGeneratorService->generateEditScreen($screenName, $modelName, $fields);
        $screenGeneratorService->generateModel($modelName, strtolower(Str::camel($modelName)), $fields);;

        Alert::info('Files generated successfully.');

        return back();
    }
}
