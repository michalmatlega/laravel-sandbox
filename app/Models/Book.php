<?php

namespace App\Models;

use Date;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\Builder as QueryBuilder;

class Book extends Model
{
    use HasFactory;

    public function reviews(): HasMany {
        return $this->hasMany(Review::class);
    }

    public function scopeTitle(Builder $query, string $title): Builder {
        return $query->where('title', 'LIKE', '%' . $title . '%');
    }

    public function scopePopular(Builder $query, ?string $from = null, ?string $to = null): Builder|QueryBuilder {
        return $query->withCount([
            'reviews' => fn(Builder $q) => $this->dateRangeFilter($q, $from, $to),
        ])->orderBy('reviews_count', 'DESC');
    }

    public function scopeHighestRated(Builder $query, ?string $from = null, ?string $to = null): Builder {
        return $query->withAvg([
            'reviews' => fn(Builder $q) => $this->dateRangeFilter($q, $from, $to),
        ], 'rating')->orderBy('reviews_avg_rating', 'DESC');
    }

    public function scopeMinReviews(Builder $query, int $minReviews): Builder {
        return $query->having('reviews_count', '>=', $minReviews);
    }

    private function dateRangeFilter(Builder $query, ?string $from = null, ?string $to = null): Builder {
        if ($from && !$to) {
            $query->where('created_at', '>=', Date::parse($from));
        } elseif (!$from && $to) {
            $query->where('created_at', '<=', Date::parse($to));
        } elseif ($from && $to) {
            $query->whereBetween('created_at', [Date::parse($from), Date::parse($to)]);
        }
        return $query;
    }
}
