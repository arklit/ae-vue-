<?php

namespace App\Orchid\Screens\Article;

use App\Enums\OrchidRoutes;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Orchid\Abstractions\EditScreenPattern;
use App\Orchid\Fields\Cropper;
use App\Orchid\Fields\TinyMce;
use App\Orchid\Helpers\OrchidValidator;
use App\Orchid\Screens\Modals\EmptyModal;
use App\Traits\CommandBarDeletableTrait;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Fields\Select;

class ArticleEditScreen extends EditScreenPattern
{
    protected string $createTitle = 'Создание Статьи';
    protected string $updateTitle = 'Редактирование Статьи';

    use CommandBarDeletableTrait;

    public function __construct()
    {
        $this->route = OrchidRoutes::ARTICLE;
        $this->routeName = $this->route->list();
    }

    public function query(Article $item): array
    {
        return $this->queryMake($item);
    }

    public function layout(): iterable
    {
        return [
            Layout::rows([
                Group::make([
                    CheckBox::make('item.is_active')->placeholder('Активность')->sendTrueOrFalse()->value(true),
                    Input::make('item.title')->title('Название')->required(),
                    Input::make('item.code')->title('Код'),
                    Input::make('item.sort')->title('Сортировка')->type('number')->value(0)->required(),
                ]),
                Group::make([
                    Select::make('item.category_id')->title('Категория')
                        ->fromQuery(ArticleCategory::query()->active()->orderBy('sort'), 'title', 'id')
                        ->required(),
                    DateTimer::make('item.publication_date')->title('Дата публикации')->required(),
                ]),
                Group::make([
                    TinyMce::make('item.description')->title('Описание')->required(),
                    Cropper::make('item.image')->title('Изображение')->targetRelativeUrl()->required(),
                ])
            ]),
            Layout::modal('deleteItem', EmptyModal::class)->title('Уверены, что хотите удалить запись?')
                ->applyButton('Да')->closeButton('Нет'),
        ];
    }

    public function asyncGetItem(Article $item)
    {
        return [
            'item' => $item,
        ];
    }

    public function save(Article $item, Request $request)
    {
        $data = $request->input('item');

        if (empty($data['code'])) {
            $data['code'] = Str::slug($data['title']);
        }

        $validator = (new OrchidValidator($data))->setIndividualRules($this->getRules(), $this->getMessages())
            ->setUniqueFields($item, ['code' => 'Такой код уже используется'])
            ->validate();

        return $validator->isFail() ? $validator->showErrors($this->route, $item->id) : $this->saveItem($item, $data);
    }

    public function remove(Article $item): RedirectResponse
    {
        return $this->removeItem($item);
    }

    public function getRules(): array
    {
        return [
            'title' => ['bail', 'required', 'max:255'],
            'sort' => ['bail', 'required'],
            'code' => ['bail', 'nullable', 'regex:~^[A-Za-z0-9\-_]*$~'],
            'category_id' => ['bail', 'required', 'max:255'],
            'publication_date' => ['bail', 'required', 'max:255'],
            'description' => ['bail', 'required', 'max:255'],
            'image' => ['bail', 'required', 'max:255'],

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
            'category_id.max' => 'Категория не может быть длиннее 255 символов',
            'category_id.required' => 'Введите Категория',
            'publication_date.max' => 'Дата публикации не может быть длиннее 255 символов',
            'publication_date.required' => 'Введите Дата публикации',
            'description.max' => 'Описание не может быть длиннее 255 символов',
            'description.required' => 'Введите Описание',
            'image.max' => 'Изображение не может быть длиннее 255 символов',
            'image.required' => 'Введите Изображение',

        ];
    }
}

