<?php

namespace App\Enums;

use App\Models\Page;
use Illuminate\Validation\Rule;

enum ModalValidation: string
{
    case ARTICLE_CATEGORY_MODAL = 'ArticleCategoryModal';
    case PRODUCTCATEGORY_MODAL = 'ProductCategoryModal';
    case PRODUCTSUBCATEGORY_MODAL = 'ProductSubCategoryModal';
//case-place
    case SEO_MODAL = 'SeoModal';
    case PAGE_MODAL = 'choosePageType';
    case DELETE_MODAL = 'deleteItem';

    public function getRules($id = null): array
    {
        return match ($this->value) {
            self::ARTICLE_CATEGORY_MODAL->value => [
                'title' => ['bail', 'required', 'max:255'],
                'sort' => ['bail', 'required'],
                'code' => ['bail', 'nullable', 'regex:~^[A-Za-z0-9\-_]*$~', Rule::unique('article_categories')->ignore($id)],
                'description' => ['bail', 'max:255'],
            ],
            self::PRODUCTCATEGORY_MODAL->value => [
			'title' => ['bail', 'required', 'max:255'],
			'sort' => ['bail', 'required'],
			'code' => ['bail', 'nullable' ,'regex:~^[A-Za-z0-9\-_]*$~', Rule::unique('product_categories')->ignore($id)],
            ],
            self::PRODUCTSUBCATEGORY_MODAL->value => [
			'title' => ['bail', 'required', 'max:255'],
			'sort' => ['bail', 'required'],
			'code' => ['bail', 'nullable' ,'regex:~^[A-Za-z0-9\-_]*$~', Rule::unique('product_sub_categories')->ignore($id)],
            ],
//rules-place
            self::SEO_MODAL->value => [
                'url' => ['bail', 'required', Rule::unique('seos')->ignore($id)],
                'title' => ['bail', 'required', 'max:160'],
                'seo_title' => ['bail', 'required', 'max:160'],
            ],
            self::PAGE_MODAL->value => [
                'name' => ['bail', 'required', 'max:160'],
                'code' => ['bail', 'required', 'regex:~^[A-Za-z0-9\-_]+$~', Rule::unique(Page::TABLE_NAME)->ignore($id)],
                'type' => ['bail', 'required'],
            ],
            self::DELETE_MODAL->value => []
        };
    }

    public function getMessages(): array
    {
        return match ($this->value) {
            self::ARTICLE_CATEGORY_MODAL->value => [
                'title.required' => 'Введите заголовок',
                'title.max' => 'Заголовок не может быть длиннее 255 символов',
                'sort.required' => 'Введите порядок сортировки',
                'code.regex' => 'В коде допустимы только цифры и латинские буквы',
                'code.unique' => 'Код должен быть уникальным',
                'description.max' => 'Описание не может быть длиннее 255 символов',
            ],
            self::PRODUCTCATEGORY_MODAL->value => [
			'title.required' => 'Введите заголовок',
			'title.max' => 'Заголовок не может быть длиннее 255 символов',
			'sort.required' => 'Введите порядок сортировки',
			'code.regex' => 'В коде допустимы только цифры и латинские буквы',
			'code.unique' => 'Код должен быть уникальным',
            ],
            self::PRODUCTSUBCATEGORY_MODAL->value => [
			'title.required' => 'Введите заголовок',
			'title.max' => 'Заголовок не может быть длиннее 255 символов',
			'sort.required' => 'Введите порядок сортировки',
			'code.regex' => 'В коде допустимы только цифры и латинские буквы',
			'code.unique' => 'Код должен быть уникальным',
            ],
//messages-place
            self::SEO_MODAL->value => [
                'title.required' => 'Введите заголовок',
                'title.max' => 'Заголовок не может быть длиннее 160 символов',
                'seo_title.required' => 'Введите Seo заголовок',
                'seo_title.max' => 'Seo заголовок не может быть длиннее 160 символов',
                'url.required' => 'Введите URL',
                'url.max' => 'URL не может быть длиннее 60 символов',
                'url.unique' => 'Страница с таким URL уже добавлена',
            ],
            self::PAGE_MODAL->value => [
                'code.required' => 'Укажите код страницы',
                'code.regex' => 'В коде допустимы только цифры и латинские буквы',
                'code.unique' => 'Такой код уже используется',
                'name.required' => 'Введите название',
                'name.max' => 'Название не может быть длиннее 160 символов',
                'type.required' => 'Выберите тип страницы',
            ],
            self::DELETE_MODAL->value => []
        };
    }
}
