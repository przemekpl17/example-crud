<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthorRequest;
use App\Http\Resources\AuthorResource;
use App\Models\Author;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AuthorController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $page = $request->query('page', 1);
        $cacheKey = "authors:page={$page}:per={$perPage}";

        $paginated = Cache::remember($cacheKey, 60, function () use ($perPage) {
            return Author::withCount('books')
                ->orderBy('last_name')
                ->paginate($perPage);
        });

        return AuthorResource::collection($paginated)
            ->additional([
                'meta' => [
                    'current_page' => $paginated->currentPage(),
                    'last_page' => $paginated->lastPage(),
                    'per_page' => $paginated->perPage(),
                    'total' => $paginated->total(),
                ],
            ]);
    }

    public function store(AuthorRequest $request): AuthorResource
    {
        $author = Author::create($request->validated());
        return new AuthorResource($author);
    }

    public function show(Author $author): AuthorResource
    {
        $author->load('books');
        return new AuthorResource($author);
    }

    public function update(AuthorRequest $request, Author $author): AuthorResource
    {
        $author->update($request->validated());
        return new AuthorResource($author);
    }

    public function destroy(Author $author): JsonResponse
    {
        $author->delete();
        return response()->json(null, 204);
    }
}
