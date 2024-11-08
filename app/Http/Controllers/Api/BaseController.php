<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller as Controller;
/**
 * @OA\Info(title="News Aggregator API Documentation", version="1.0")
 */
class BaseController extends Controller
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($result = false)
    {
    	$response = [
            'success' => true,
            'message' => "success",
            'data' => !empty($result) ? $result : null
        ];
        return response()->json($response, 200);
    }

    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($errorMessages = [], $code = 404)
    {
    	$response = [
            'success' => false,
            'message' => "error",
        ];

        if(!empty($errorMessages)){
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code);
    }

    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendPaginatedResponse($data, $code = 200, $message = 'success', $sortColumn = 'created_at', $sortOrder = 'ASC')
    {
        $response = [
            'success' => true,
            'message' => "success",
            'data' => $data->items(), // Get only the paginated data items
            'page_context' => [
                'page' => $data->currentPage(),
                'per_page' => (string) $data->perPage(),
                'has_more_page' => $data->currentPage() < $data->lastPage() ? "true" : "false",
                'sort_column' => $sortColumn,
                'sort_order' => strtoupper($sortOrder),
            ]
        ];

        return response()->json($response, $code);
    }
}