<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use App\Services\BookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class BookController extends Controller
{
    private BookService $bookService;

    public function __construct(BookService $bookService)
    {
        $this->bookService = $bookService;
    }

    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $page = $request->query('page', 1);
        $cacheKey = "books:page={$page}:per={$perPage}";

        $paginated = Cache::remember($cacheKey, 60, function () use ($perPage) {
            return Book::with(['author', 'publisher'])
                ->orderBy('published_at', 'desc')
                ->paginate($perPage);
        });

        return BookResource::collection($paginated)
            ->additional([
                'meta' => [
                    'current_page' => $paginated->currentPage(),
                    'last_page' => $paginated->lastPage(),
                    'per_page' => $paginated->perPage(),
                    'total' => $paginated->total(),
                ],
            ]);
    }

    public function store(BookRequest $request): BookResource
    {
        $book = DB::transaction(fn() => $this->bookService->createBook($request->validated()));
        return new BookResource($book->load(['author', 'publisher']));
    }

    public function show(Book $book): BookResource
    {
        $book->load(['author', 'publisher']);
        return new BookResource($book);
    }

    public function update(BookRequest $request, Book $book): BookResource
    {
        DB::transaction(fn() => $this->bookService->updateBook($book, $request->validated()));
        return new BookResource($book->load(['author', 'publisher']));
    }

    public function destroy(Book $book): JsonResponse
    {
        $book->delete();
        return response()->json(null, 204);
    }
}
