<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PublisherRequest;
use App\Http\Resources\PublisherResource;
use App\Models\Publisher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PublisherController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $page = $request->query('page', 1);
        $cacheKey = "publishers:page={$page}:per={$perPage}";

        $paginated = Cache::remember($cacheKey, 60, function () use ($perPage) {
            return Publisher::withCount('books')
                ->orderBy('name')
                ->paginate($perPage);
        });

        return PublisherResource::collection($paginated)
            ->additional([
                'meta' => [
                    'current_page' => $paginated->currentPage(),
                    'last_page' => $paginated->lastPage(),
                    'per_page' => $paginated->perPage(),
                    'total' => $paginated->total(),
                ],
            ]);
    }

    public function store(PublisherRequest $request): PublisherResource
    {
        $publisher = Publisher::create($request->validated());
        return new PublisherResource($publisher);
    }

    public function show(Publisher $publisher): PublisherResource
    {
        $publisher->load('books');
        return new PublisherResource($publisher);
    }

    public function update(PublisherRequest $request, Publisher $publisher): PublisherResource
    {
        $publisher->update($request->validated());
        return new PublisherResource($publisher);
    }

    public function destroy(Publisher $publisher): JsonResponse
    {
        $publisher->delete();
        return response()->json(null, 204);
    }
}
