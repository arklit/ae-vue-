<?php

namespace App\Orchid\Layouts\Listeners;

use App\Enums\PagesTypes;
use App\Models\Page;
use App\Models\ProductCategory;
use App\Models\ProductSubCategory;
use Illuminate\Http\Request;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Listener;
use Orchid\Screen\Repository;
use Orchid\Support\Facades\Layout;

class ProductCategoryListener extends Listener
{
    protected $targets = [
        'item.category_id',
    ];

    protected function layouts(): array
    {
        $id = (int)$this->query->get('item.category_id');
        if ($id === 0) {
            $id = session()->get('product_store.category_id');
        }
        return [
            Layout::rows([
                Group::make([
                    Select::make('item.category_id')->title('Категория')
                        ->fromQuery(ProductCategory::query()->active(), 'title', 'id')
                        ->empty('Выберите тип проекта')
                        ->required(),
                    Select::make('item.sub_category_id')->title('Подкатегория')
                        ->fromQuery(ProductSubCategory::query()->active()->whereHas('productCategories', function ($query) use ($id) {
                            $query->where('product_categories.id', $id);
                        }), 'title', 'id')
                        ->empty('Выберите тег проекта')
                        ->required()
                ])
            ])
        ];
    }

    public function handle(Repository $repository, Request $request): Repository
    {
        $data = $request->input('item');

        return $repository
            ->set('item.category_id', $data['category_id'])
            ->set('item.sub_category_id', $data['sub_category_id']);
    }
}
