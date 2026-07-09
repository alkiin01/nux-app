<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Validator;

class ShipmentController extends \App\Http\Controllers\Controller
{


    /**
     * POST /api/shipment/ship-complete
     * Body expected (json):
     * {
     *   "packNum": 137113
     * }
     */
    public function shipComplete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'packNum' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $host_api = self::get_host_api();
        } catch (\RuntimeException $ex) {
            return response()->json([
                'status' => 'error',
                'message' => $ex->getMessage(),
            ], 500);
        }

        $payload = [
            'packNum' => $request->input('packNum'),
         
        ];

        $client = new Client();

        try {
            $response = $client->request('POST', $host_api . 'Shipment/ShipComplete', [
                'json' => $payload,
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'verify' => false,
                'timeout' => 30,
            ]);

            $body = (string) $response->getBody();
            $statusCode = $response->getStatusCode();

            return response()->json([
                'status' => 'success',
                'http_status' => $statusCode,
                'response_body' => json_decode($body, true) ?? $body,
            ], $statusCode);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $message = $e->getMessage();
            $responseBody = null;
            if (method_exists($e, 'hasResponse') && $e->hasResponse()) {
                $responseBody = (string) $e->getResponse()->getBody();
            }
            return response()->json([
                'status' => 'error',
                'message' => $message,
                'response_body' => $responseBody,
            ], 500);
        }
    }
}
