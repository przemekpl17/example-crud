<?php

namespace App\Models;

use App\Enums\BookStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'published_at',
        'author_id',
        'publisher_id',
        'status',
        'price',
    ];

    protected $casts = [
        'published_at' => 'date',
        'status' => BookStatus::class,
        'price' => 'decimal:2',
    ];

    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    public function publisher()
    {
        return $this->belongsTo(Publisher::class);
    }
}
