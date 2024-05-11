<?php

namespace App\Orchid\Screens\ProductSubCategory;

use App\Enums\OrchidRoutes;
use App\Models\ProductSubCategory;
use App\Orchid\Abstractions\ListScreenPattern;
use App\Orchid\Helpers\OrchidHelper;
use App\Orchid\Screens\Modals\EmptyModal;
use App\Orchid\Screens\Modals\ProductSubCategoryModal;
use App\Traits\ActivitySignsTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class ProductSubCategoryListScreen extends ListScreenPattern
{
    use ActivitySignsTrait;

    public function __construct()
    {
        $this->route = OrchidRoutes::PRODUCT_SUB_CATEGORY;
    }

    public function commandBar(): iterable
    {
        return [
            ModalToggle::make('Добавить')
                ->icon('plus')
                ->method('save')
                ->modal('ProductSubCategoryModal'),
        ];
    }

    public function query(): iterable
    {
        $this->name = $this->route->getTitle();
        $this->model = ProductSubCategory::query();
        return parent::query();
    }

    public function layout(): iterable
    {
        return [
            Layout::table('items', [
                TD::make('id', 'ID'),
                TD::make('is_active', 'Активность')->sort()->filter(
                    Select::make()->options(OrchidHelper::getYesNoArray())->empty()->title('Фильтр активности')
                )->render(fn($item) => $item->is_active ? $this->activeSign : $this->inactiveSign),
                TD::make('title', 'Название')->sort()->filter(),
                TD::make('code', 'Код')->sort()->filter(),
                TD::make('sort', 'Сортировка')->sort(),


                TD::make('created_at', 'Дата')->width(150)->alignRight()->sort()
                    ->filter(DateTimer::make()->title('Фильтр по дате')->format('d-m-Y'))
                    ->render(fn($item) => $item->created_at?->format('d-m-Y')),

                TD::make()->width(10)->alignRight()->cantHide()->render(fn($item) => DropDown::make()->icon('options-vertical')->list([
                    ModalToggle::make('Редактировать')->icon('wrench')->method('save')
                        ->modal('ProductSubCategoryModal')->asyncParameters(['item' => $item->id]),
                    Button::make('Удалить')->icon('trash')->method('deleteItem', ['item' => $item->id, 'title' => $item->getTitle()])
                        ->confirm('Вы действительно хотите удалить запись №' . $item->id . ' - <strong>' . $item->getTitle() . '</strong>?'),
                ])),
            ]),
            Layout::modal('ProductSubCategoryModal', ProductSubCategoryModal::getModal())->title('Добавить ProductSubCategory')
                ->applyButton('Сохранить')->closeButton('Отменить')->async('asyncGetItem'),
            Layout::modal('deleteItem', EmptyModal::class)->title('Удалить запись?')
                ->applyButton('Да')->closeButton('Нет')->async('asyncGetItem'),
        ];
    }

    public function asyncGetItem(ProductSubCategory $item): array
    {
        return [
            'item' => $item,
        ];
    }

    public function save(ProductSubCategory $item, Request $request): \Illuminate\Http\RedirectResponse
    {
        $data = $request->input('item');

        if (empty($data['code'])) {
            $data['code'] = Str::slug($data['title']);
        }

        $item->fill($data)->save();
        Alert::success('Новая страница успешно добавлен');

        return redirect()->route($this->route->list());
    }

    public function deleteItem(ProductSubCategory $item): void
    {
        $id = $item->id;
        $title = $item->getTitle();
        if ($item->productCategories()->exists()) {
            Alert::error("Запись №:$id - '$title' не может быть удалена, так как есть связанные категории товаров!");
            return;
        }
        if ($item->products()->exists()) {
            Alert::error("Запись №:$id - '$title' не может быть удалена, так как есть связанные товары!");
            return;
        }
        $item->delete() ? Alert::success("Запись №:$id - '$title'  успешно удалена!")
            : Alert::error("Произошла ошибка при попытке удалить запись");
    }
}
