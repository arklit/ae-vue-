<?php

namespace App\Models;

use App\Orchid\Filters\DateCreatedFilter;
use App\Orchid\Filters\IsActiveFilter;
use App\Traits\CodeScopeTrait;
use App\Traits\IsActiveScopeTrait;
use App\Traits\SortedScopeTrait;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Orchid\Filters\Types\Like;

class Product extends ProtoModel
{
    use IsActiveScopeTrait;
    use SortedScopeTrait;
    use CodeScopeTrait;

    public const TABLE_NAME = 'products';
    protected $table = self::TABLE_NAME;
    protected $allowedSorts = ['is_active', 'sort', 'title', 'code','description', 'photos', ];
    protected $allowedFilters = ['title' => Like::class, 'code' => Like::class, 'is_active' => IsActiveFilter::class,'description' => Like::class, 'photos' => Like::class, ];
    protected $casts = [
        'photos' => 'array',
    ];
    /*public function proto_relation(): HasMany
    {
        return $this->hasMany(ProtoModel::class, 'proto_foreign_key', 'id');
    }*/

    public function productCategory(): HasMany
    {
        return $this->hasMany(ProductCategory::class, 'id', 'category_id');
    }

    public function productSubCategories(): HasMany
    {
        return $this->hasMany(ProductSubCategory::class, 'id', 'sub_category_id');
    }
}
