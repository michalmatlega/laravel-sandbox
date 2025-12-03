@php use App\Models\Book; @endphp
@extends('layouts.bookapp')

@section('content')
    <h1 class="mb-10 text-2x1">Books</h1>
    <form method="GET" action="{{ route('books.index') }}" class="mb-4 flex items-center space-x-2">
        <input
            type="text"
            name="title"
            value="{{ request('title') }}"
            placeholder="Search by title"
            class="input h-10"
        />
        <input type="hidden" name="filter" value="{{ request('filter') }}" />
        <button type="submit" class="btn h-10">Search</button>
        <a href="{{ route('books.index') }}" class="btn h-10">Clear</a>

    </form>

    <div class="filter-container mb-4 flex">
        @php
            $filters = [
                '' => 'latest',
                Book::FILTER_POPULAR_LAST_MONTH => 'most popular (last month)',
                Book::FILTER_POPULAR_LAST_6MONTHS => 'most popular (last 6 months)',
                Book::FILTER_HIGHEST_RATED_LAST_MONTH => 'highest rated (last month)',
                Book::FILTER_HIGHEST_RATED_LAST_6MONTHS => 'highest rated (last 6 months)',
            ]
        @endphp

        @foreach($filters as $key => $label)
            <a href="{{ route('books.index', [...request()->query(), 'filter' => $key]) }}" class="{{ ((request('filter') ?? '') === $key) ? 'filter-item-active' : 'filter-item' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <ul>
        @forelse($books as $book)
            <li class="mb-4">
                <div class="book-item">
                    <div
                        class="flex flex-wrap items-center justify-between">
                        <div class="w-full flex-grow sm:w-auto">
                            <a href="{{ route('books.show', $book) }}" class="book-title">{{ $book->title }}</a>
                            <span class="book-author">by {{ $book->author }}</span>
                        </div>
                        <div>
                            <div class="book-rating">
                                {{ number_format($book->reviews_avg_rating, 1) }}
                            </div>
                            <div class="book-review-count">
                                out of {{ $book->reviews_count }} {{ Str::plural('review', $book->reviews_count) }}
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        @empty
            <li class="mb-4">
                <div class="empty-book-item">
                    <p class="empty-text">No books found</p>
                    <a href="{{ route('books.index') }}" class="reset-link">Reset criteria</a>
                </div>
            </li>
        @endforelse
    </ul>
@endsection
