<?php

namespace App\Orchid\Screens\Article;

use App\Enums\OrchidRoutes;
use App\Models\Article;
use App\Orchid\Abstractions\ListScreenPattern;
use App\Orchid\Helpers\OrchidHelper;
use App\Traits\ActivitySignsTrait;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class ArticleListScreen extends ListScreenPattern
{
    use ActivitySignsTrait;

    public function __construct()
    {
        $this->route = OrchidRoutes::ARTICLE;
        $this->name = $this->route->getTitle();
    }

    public function query(): iterable
    {
        $this->model = Article::query()->filters();
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
                TD::make('category_id', 'Категория')->sort()->filter(),

                TD::make('created_at', 'Дата')->alignRight()->sort()
                    ->filter(DateTimer::make()->title('Фильтр по дате')->format('d-m-Y'))
                    ->render(fn($item) => $item->created_at?->format('d.m.Y')),

                TD::make()->width(10)->alignRight()->cantHide()
                    ->render(fn($item) => DropDown::make()->icon('options-vertical')->list([
                        Link::make(__('Edit'))->icon('wrench')->route(OrchidRoutes::ARTICLE->edit(), $item),
                        Button::make('Удалить')->icon('trash')
                            ->method('deleteItem', ['item' => $item->id, 'title' => $item->getTitle()])
                            ->confirm('Вы действительно хотите удалить запись №' . $item->id . ' - <strong>' . $item->getTitle() . '</strong>?'),
                    ])),
            ]),
        ];
    }

    public function asyncGetItem(Article $item)
    {
        return [
            'item' => $item,
        ];
    }

    public function deleteItem(Article $item)
    {
        $id = $item->id;
        $title = $item->getTitle();

        try {
            $this->detachRelations($item);
            $item->delete();
        } catch (\Exception $exception) {
            Alert::error($exception->getMessage());
        }
        Alert::success("Запись №:$id - '$title'  успешно удалена!");
    }
}
