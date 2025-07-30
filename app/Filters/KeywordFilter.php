<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Http\Request;

class KeywordFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        return $query->where(function ($q) use ($value) {
            $q->where('title', 'like', "%{$value}%")
                ->orWhere('content', 'like', "%{$value}%")
                ->orWhereHas('user', function ($q) use ($value) {
                    $q->where('name', 'like', "%{$value}%");
                });
        });
    }
}
