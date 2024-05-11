<?php

namespace App\Models;

use App\Interfaces\ProtoInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

abstract class ProtoModel extends Model implements ProtoInterface
{
    use HasFactory;
    use AsSource;
    use Attachable;
    use Filterable;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function getTitle()
    {
        return $this->title ?? $this->name;
    }

    public function getCode()
    {
        return $this->code ?? $this->slug;
    }
}
