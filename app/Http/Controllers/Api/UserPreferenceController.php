<?php

namespace App\Http\Controllers\Api;

use App\Models\UserPreference;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController as BaseController;
use Illuminate\Support\Facades\Auth;
use App\Models\Article;

/**
 * @OA\Schema(
 *     schema="UserPreference",
 *     type="object",
 *     title="User Preference",
 *     description="A user's preferences for news sources, categories, and authors",
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(
 *         property="preferred_sources",
 *         type="array",
 *         @OA\Items(type="integer", example=1)
 *     ),
 *     @OA\Property(
 *         property="preferred_categories",
 *         type="array",
 *         @OA\Items(type="integer", example=1)
 *     ),
 *     @OA\Property(
 *         property="preferred_authors",
 *         type="array",
 *         @OA\Items(type="integer", example=1)
 *     ),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-11-07T12:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-11-07T12:00:00Z")
 * )
 */
class UserPreferenceController extends BaseController
{
        /**
     * @OA\Post(
     *     path="/api/user/preferences",
     *     summary="Set user preferences",
     *     description="Save user preferences for sources, categories, and authors",
     *     tags={"User Preferences"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="preferred_sources", type="array", @OA\Items(type="integer"), example={1, 2, 3}),
     *             @OA\Property(property="preferred_categories", type="array", @OA\Items(type="integer"), example={4, 5}),
     *             @OA\Property(property="preferred_authors", type="array", @OA\Items(type="integer"), example={6})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Preferences saved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(property="data", ref="#/components/schemas/UserPreference")
     *         )
     *     )
     * )
     */
    public function setPreferences(Request $request)
    {
        $user = Auth::user();
        $validatedData = $request->validate([
            'preferred_sources' => 'array|exists:sources,id',
            'preferred_categories' => 'array|exists:categories,id',
            'preferred_authors' => 'array|exists:authors,id',
        ]);
        $preferences = UserPreference::updateOrCreate(
            ['user_id' => $user->id],
            $validatedData
        );
        return $this->sendResponse($preferences);
    }
    /**
     * @OA\Get(
     *     path="/api/user/preferences",
     *     summary="Get user preferences",
     *     description="Retrieve saved user preferences",
     *     tags={"User Preferences"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="User preferences retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(property="data", ref="#/components/schemas/UserPreference")
     *         )
     *     )
     * )
     */
    public function getPreferences()
    {
        $user = Auth::user();
        $preferences = $user->preferences;
        return $this->sendResponse($preferences);
    }
    /**
     * @OA\Get(
     *     path="/api/user/personalized-feed",
     *     summary="Get personalized news feed",
     *     description="Fetches articles based on user's preferences",
     *     tags={"User Preferences"},
     *     security={{"sanctum": {}}},
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
     *         description="Personalized news feed based on user preferences",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Article")
     *             )
     *         )
     *     )
     * )
     */
    public function personalizedFeed(Request $request)
    {
        $user = Auth::user();
        $preferences = $user->preferences;
        if (empty($preferences)) {
            $this->sendResponse();
        }
        $query = Article::query();
        if (!empty($preferences->preferred_sources)) {
            $query->whereIn('source_id', $preferences->preferred_sources);
        }

        if (!empty($preferences->preferred_categories)) {
            $query->whereIn('category_id', $preferences->preferred_categories);
        }

        if (!empty($preferences->preferred_authors)) {
            $query->whereIn('author_id', $preferences->preferred_authors);
        }
        $perPage = $request->input('per_page', 10);
        $articles = $query->paginate($perPage);
        return $this->sendPaginatedResponse($articles);
    }
}
