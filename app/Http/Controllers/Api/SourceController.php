<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Source;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController as BaseController;
/**
 * @OA\Schema(
 *     schema="Source",
 *     type="object",
 *     title="Source",
 *     description="A source entity",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Example Source"),
 *     @OA\Property(property="rss_feed_link", type="string", example="http://example.com/rss"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-11-07T12:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-11-07T12:00:00Z")
 * )
 */
class SourceController extends BaseController
{
    /**
     * @OA\Post(
     *     path="/api/sources",
     *     summary="Create a new source",
     *     description="Stores a new source in the database",
     *     tags={"Sources"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "title", "rss_feed_link"},
     *             @OA\Property(property="name", type="string", example="Example Source"),
     *             @OA\Property(property="title", type="string", example="Example Title"),
     *             @OA\Property(property="rss_feed_link", type="string", example="http://example.com/rss")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Source created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Source"
     *             )
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:sources,name',
            'title' => 'required|string',
            'rss_feed_link' => 'required|string',
        ]);

        $category = Source::create([
            'name' => $request->name,
            'title' => $request->title,
            'rss_feed_link' => $request->rss_feed_link,
        ]);
        return $this->sendResponse($category);
    }
    /**
     * @OA\Get(
     *     path="/api/sources",
     *     summary="Get a list of sources",
     *     description="Fetches a list of all sources",
     *     tags={"Sources"},
     *     @OA\Response(
     *         response=200,
     *         description="A list of sources",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Source")
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
        $sources = Source::paginate($perPage);
        return $this->sendPaginatedResponse($sources);
    }

    /**
     * @OA\Get(
     *     path="/api/sources/{id}",
     *     summary="Get source details",
     *     description="Fetches a single source by its ID",
     *     tags={"Sources"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the source",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Source details",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Source"
     *             )
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $source = Source::find($id);
        return $this->sendResponse($source);
    }
}
