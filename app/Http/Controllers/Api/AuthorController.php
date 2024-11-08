<?php

namespace App\Http\Controllers\Api;

use App\Models\Author;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController as BaseController;
/**
 * @OA\Schema(
 *     schema="Author",
 *     type="object",
 *     title="Author",
 *     description="An author entity",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-11-07T12:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-11-07T12:00:00Z")
 * )
 */
class AuthorController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/authors",
     *     summary="Get a list of authors",
     *     description="Fetches a list of all authors",
     *     tags={"Authors"},
     *     @OA\Response(
     *         response=200,
     *         description="A list of authors",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                  @OA\Items(ref="#/components/schemas/Author")
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
        $perPage = $request->input('per_page', 10);
        $authors = Author::paginate($perPage);
        return $this->sendPaginatedResponse($authors);
    }
}
