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

    public const FILTER_POPULAR_LAST_MONTH = 'popular_last_month';
    public const FILTER_POPULAR_LAST_6MONTHS = 'popular_last_6months';
    public const FILTER_HIGHEST_RATED_LAST_MONTH = 'highest_rated_last_month';
    public const FILTER_HIGHEST_RATED_LAST_6MONTHS = 'highest_rated_last';

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

    public function scopePopularLastMonth(Builder $query): Builder|QueryBuilder {
        return $query->popular(now()->subMonth(), now())
            ->highestRated(now()->subMonth(), now())
            ->minReviews(2);
    }

    public function scopePopularLast6Months(Builder $query): Builder|QueryBuilder {
        return $query->popular(now()->subMonths(6), now())
            ->highestRated(now()->subMonths(6), now())
            ->minReviews(5);
    }

    public function scopeHighestRatedLastMonth(Builder $query): Builder|QueryBuilder {
        return $query->highestRated(now()->subMonth(), now())
            ->popular(now()->subMonth(), now())
            ->minReviews(2);
    }

    public function scopeHighestRatedLast6Months(Builder $query): Builder|QueryBuilder {
        return $query->highestRated(now()->subMonths(6), now())
            ->popular(now()->subMonths(6), now())
            ->minReviews(5);
    }
}
