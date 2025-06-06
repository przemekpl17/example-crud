<?php

namespace App\Services;

use App\Enums\BookStatus;
use App\Models\Book;
use Illuminate\Support\Carbon;

class BookService
{
    /**
     * Create book
     *
     * @param array $data ['title', 'description', 'published_at', 'author_id', 'publisher_id', 'status', 'price']
     * @return Book
     */
    public function createBook(array $data): Book
    {
        $publishedAt = $data['published_at'] ?? null;
        if ($publishedAt && Carbon::parse($publishedAt)->isFuture()) {
            $data['published_at'] = Carbon::now()->toDateString();
        }

        $data['status'] = $data['status'] ?? BookStatus::Draft->value;

        $book = Book::create($data);

        return $book;
    }

    /**
     * Update book
     *
     * @param Book $book
     * @param array $data
     * @return Book
     */
    public function updateBook(Book $book, array $data): Book
    {
        if (isset($data['published_at']) && Carbon::parse($data['published_at'])->isFuture()) {
            $data['published_at'] = Carbon::now()->toDateString();
        }

        if (!isset($data['status'])) {
            $data['status'] = $book->status->value;
        }

        $book->update($data);
        return $book;
    }
}
