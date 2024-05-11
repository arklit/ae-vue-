<?php

namespace App\Orchid\Screens\Product;

use App\Enums\OrchidRoutes;
use App\Models\Product;
use App\Orchid\Abstractions\EditScreenPattern;
use App\Orchid\Fields\Cropper;
use App\Orchid\Fields\TinyMce;
use App\Orchid\Helpers\OrchidValidator;
use App\Orchid\Layouts\Listeners\ProductCategoryListener;
use App\Orchid\Layouts\Repeaters\PhotosRepeater;
use App\Orchid\Screens\Modals\EmptyModal;
use App\Traits\CommandBarDeletableTrait;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Nakukryskin\OrchidRepeaterField\Fields\Repeater;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Fields\Select;

class ProductEditScreen extends EditScreenPattern
{
    protected string $createTitle = 'Создание Товары';
    protected string $updateTitle = 'Редактирование Товары';

    use CommandBarDeletableTrait;

    public function __construct()
    {
        $this->route = OrchidRoutes::PRODUCT;
        $this->routeName = $this->route->list();
    }

    public function query(Product $item): array
    {
        return $this->queryMake($item);
    }

    public function layout(): iterable
    {
        return [
            ProductCategoryListener::class,
            Layout::tabs([
                'Основное' => Layout::rows([
                    Group::make([
                        CheckBox::make('item.is_active')->placeholder('Активность')->sendTrueOrFalse()->value(true),
                        Input::make('item.title')->title('Название')->required(),
                        Input::make('item.code')->title('Код'),
                        Input::make('item.sort')->title('Сортировка')->type('number')->value(0)->required(),
                    ]),

                    TinyMce::make('item.description')->title('Описание')->required(),
                ]),
                'Фотографии' => Layout::rows([
                    Repeater::make('item.photos')->layout(PhotosRepeater::class)->title('Фотографии'),
                ]),
            ]),

            Layout::modal('deleteItem', EmptyModal::class)->title('Уверены, что хотите удалить запись?')
                ->applyButton('Да')->closeButton('Нет'),
        ];
    }

    public function asyncGetItem(Product $item)
    {
        return [
            'item' => $item,
        ];
    }

    public function save(Product $item, Request $request)
    {
        $data = $request->input('item');

        if (empty($data['code'])) {
            $data['code'] = Str::slug($data['title']);
        }

        $validator = (new OrchidValidator($data))->setIndividualRules($this->getRules(), $this->getMessages())
            ->setUniqueFields($item, ['code' => 'Такой код уже используется'])
            ->validate();
        session()->put('product_store.category_id', $data['category_id']);
        return $validator->isFail() ? $validator->showErrors($this->route, $item->id) : $this->saveItem($item, $data);
    }

    public function remove(Product $item): RedirectResponse
    {
        return $this->removeItem($item);
    }

    public function getRules(): array
    {
        return [
            'title' => ['bail', 'required', 'max:255'],
            'sort' => ['bail', 'required'],
            'code' => ['bail', 'nullable', 'regex:~^[A-Za-z0-9\-_]*$~'],
            'description' => ['bail', 'required', 'max:255'],
            'photos' => ['bail', 'required', 'max:255'],

        ];
    }

    public function getMessages(): array
    {
        return [
            'title.required' => 'Введите заголовок',
            'title.max' => 'Заголовок не может быть длиннее 255 символов',
            'sort.required' => 'Введите порядок сортировки',
            'code.regex' => 'В коде допустимы только цифры и латинские буквы',
            'code.unique' => 'Код должен быть уникальным',
            'description.max' => 'Описание не может быть длиннее 255 символов',
            'description.required' => 'Введите Описание',
            'photos.max' => 'Фотографии не может быть длиннее 255 символов',
            'photos.required' => 'Введите Фотографии',

        ];
    }
}

