<?php

namespace App\Models;

use App\Orchid\Filters\DateCreatedFilter;
use App\Orchid\Filters\IsActiveFilter;
use App\Traits\CodeScopeTrait;
use App\Traits\IsActiveScopeTrait;
use App\Traits\SortedScopeTrait;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Orchid\Filters\Types\Like;

class ProductCategory extends ProtoModel
{
    use IsActiveScopeTrait;
    use SortedScopeTrait;
    use CodeScopeTrait;

    public const TABLE_NAME = 'product_categories';
    protected $table = self::TABLE_NAME;
    protected $allowedSorts = ['is_active', 'sort', 'title', 'code',];
    protected $allowedFilters = ['title' => Like::class, 'code' => Like::class, 'is_active' => IsActiveFilter::class,];

    /*public function proto_relation(): HasMany
    {
        return $this->hasMany(ProtoModel::class, 'proto_foreign_key', 'id');
    }*/

    public function productSubCategories(): BelongsToMany
    {
        return $this->belongsToMany(ProductSubCategory::class, 'product_category_product_sub_category');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id', 'id');
    }
}
