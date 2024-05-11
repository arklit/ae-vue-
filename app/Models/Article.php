<?php

namespace App\Models;

use App\Orchid\Filters\DateCreatedFilter;
use App\Orchid\Filters\IsActiveFilter;
use App\Traits\CodeScopeTrait;
use App\Traits\IsActiveScopeTrait;
use App\Traits\ReadyToPublicationScopeTrait;
use App\Traits\SortedByPublicationScopeTrait;
use App\Traits\SortedScopeTrait;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Orchid\Filters\Types\Like;

class Article extends ProtoModel
{
    use IsActiveScopeTrait;
    use SortedScopeTrait;
    use CodeScopeTrait;
    use ReadyToPublicationScopeTrait;
    use SortedByPublicationScopeTrait;

    public const TABLE_NAME = 'articles';
    protected $table = self::TABLE_NAME;
    protected $allowedSorts = ['is_active', 'sort', 'title', 'code','category_id', 'publication_date', 'description', 'image', ];
    protected $allowedFilters = ['title' => Like::class, 'code' => Like::class, 'is_active' => IsActiveFilter::class,'category_id' => Like::class, 'publication_date' => Like::class, 'description' => Like::class, 'image' => Like::class, ];

    public function category(): HasOne
    {
        return $this->hasOne(ArticleCategory::class, 'id', 'category_id');
    }

    /*public function proto_relation(): HasMany
    {
        return $this->hasMany(ProtoModel::class, 'proto_foreign_key', 'id');
    }*/
}
