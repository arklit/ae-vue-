<?php

namespace App\Orchid\Screens;

use App\Enums\OrchidRoutes;
use App\Models\Configurator;
use App\Orchid\Abstractions\EditScreenPattern;
use App\Repositories\CommonRepository;
use App\Services\ConfiguratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Group;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class ConfiguratorEdit extends EditScreenPattern
{
    protected string $createTitle = 'Создание конфигурации';
    protected string $updateTitle = 'Редактирование конфигурации';

    public function __construct()
    {
        $this->route = OrchidRoutes::CONFIGURATOR;
        $this->name = $this->route->getTitle();
        $this->redirectTo = $this->route->base();
    }

    public function commandBar()
    {
        return [
            Button::make('Сохранить')->icon('note')->method('save'),
        ];
    }

    public function query()
    {
        return Configurator::all();
    }

    public function layout(): array
    {
        $config = config('presets.configurator');

        $fields = [];
        $layouts = [];
        foreach ($config as $index => $layout) {
            foreach ($layout['group']['fields'] as $key => $configField) {

                if (!$fieldValue = $this->query()->where('key', $key)->first()) {
                    $fieldValue = new Configurator([
                        'key' => $key,
                        'title' => $configField['title'],
                    ]);
                    $fieldValue->save();
                    $this->query()->push($fieldValue);
                }

                $fields[$index][$key] =
                    (new ConfiguratorService())
                    ->getField(
                        fieldType: $configField['type'],
                        name: 'item.' . $key,
                        label: $configField['title'],
                        value: $fieldValue->value ?: '',
                        mask: $configField['mask'] ?? null,
                        isRequired: $configField['required']);
            }
        }

        // Разбиение на Orchid\Screen\Fields\Group
        foreach ($fields as $key => $fieldsSet) {
            $rows = [];

            $fieldsCount = count($fieldsSet);
            $countInGroup = $config[$key]['group']['count'];
            $layoutTitle = $config[$key]['title'];

            if ($fieldsCount > 1) {
                $fieldsChunked = array_chunk($fieldsSet, $countInGroup);
                foreach ($fieldsChunked as $chunk) {
                    $rows[] = Group::make($chunk);
                }
            } else {
                $rows = [$fieldsSet];
            }

            $layouts[] = Layout::rows($rows)->title($layoutTitle);
        }

        return $layouts;
    }

    public function save(Configurator $item, Request $request)
    {
        $data = $request->input('item');
        try {
            $result = CommonRepository::take()->updateConfigurationData($data);
        } catch (\Exception $e) {
            return redirect()->route($this->route->base())->withInput()->withErrors([$e->getMessage()]);
        }

        if ($result) {
            Alert::success('Данные успешно обновлены');
        } else {
            Alert::error('Произошла ошибка при попытке обновления данных');
        }
    }
}
