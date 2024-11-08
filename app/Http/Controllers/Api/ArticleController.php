<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController as BaseController;
/**
 * @OA\Schema(
 *     schema="Article",
 *     type="object",
 *     title="Article",
 *     description="An article entity",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Sample Title"),
 *     @OA\Property(property="description", type="string", example="Sample description of the article."),
 *     @OA\Property(property="published_at", type="string", format="date-time", example="2024-11-07T10:00:00Z"),
 *     @OA\Property(property="source", type="object", ref="#/components/schemas/Source"),
 *     @OA\Property(property="category", type="object", ref="#/components/schemas/Category"),
 *     @OA\Property(property="author", type="object", ref="#/components/schemas/Author")
 * )
 */
class ArticleController extends BaseController
{   
/**
     * @OA\Get(
     *     path="/api/articles",
     *     summary="Get a list of articles",
     *     description="Fetches articles with optional filtering, sorting, and pagination.",
     *     tags={"Articles"},
     *     @OA\Parameter(
     *         name="keyword",
     *         in="query",
     *         required=false,
     *         description="Search keyword for title or description",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         required=false,
     *         description="ID of the category to filter articles",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="source_id",
     *         in="query",
     *         required=false,
     *         description="ID of the source to filter articles",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="date",
     *         in="query",
     *         required=false,
     *         description="Date to filter articles (YYYY-MM-DD)",
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         description="Number of articles per page",
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Page number",
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A paginated list of articles",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="integer", example=0),
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Article")
     *             ),
     *             @OA\Property(
     *                 property="page_context",
     *                 type="object",
     *                 @OA\Property(property="page", type="integer", example=1),
     *                 @OA\Property(property="per_page", type="string", example="20"),
     *                 @OA\Property(property="has_more_page", type="string", example="false"),
     *                 @OA\Property(property="sort_column", type="string", example="created_at"),
     *                 @OA\Property(property="sort_order", type="string", example="ASC")
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $query = Article::query();
        if ($request->filled('keyword')) {
            $query->where('title', 'like', '%' . $request->keyword . '%')
                  ->orWhere('description', 'like', '%' . $request->keyword . '%');
        }

        if ($request->filled('category_id')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('id', $request->category_id);
            });
        }

        if ($request->filled('source_id')) {
            $query->whereHas('source', function ($q) use ($request) {
                $q->where('id', $request->source_id);
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('published_at', $request->date);
        }
        $perPage = $request->input('per_page', 10);
        $articles = $query->with(['source', 'category', 'author'])->paginate($perPage);
        return $this->sendPaginatedResponse($articles);

    }
    /**
     * @OA\Get(
     *     path="/api/articles/{id}",
     *     summary="Get article details",
     *     description="Fetches a single article by its ID",
     *     tags={"Articles"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the article",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Article details",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="success", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Article"
     *             )
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $article = Article::with(['source', 'category', 'author'])->find($id);

        if (!$article) {
            return response()->json(['message' => 'Article not found'], 404);
        }
        return $this->sendResponse($article);
    }
}
