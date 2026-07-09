<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Validator;

class PurchaseOrderController extends \App\Http\Controllers\Controller
{
    /**
     * POST /api/purchase-order/update-header
     * Body expected (json):
     * {
     *   "vendorID": "02-0003",
     *   "dueDate": "2025-12-02T00:00:00",
     *   "shipViaCode": "SUP",
     *   "buyerID": "PPIC",
     *   "nik": "270723-001",
     *   "password": "alkiin01"
     * }
     */
      protected  $nik = '270723-001';
       protected $password = 'alkiin01';
    public function updateHeader(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vendorID' => 'required|string',
            'dueDate' => 'required|date',
            'shipViaCode' => 'required|string',
            'buyerID' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Use centralized host provider. Will throw if not configured.
        try {
            $host_api = self::get_host_api();
        } catch (\RuntimeException $ex) {
            return response()->json([
                'status' => 'error',
                'message' => $ex->getMessage(),
            ], 500);
        }

        $payload = [
            'vendorID' => $request->input('vendorID'),
            'dueDate' => $request->input('dueDate'),
            'shipViaCode' => $request->input('shipViaCode'),
            'buyerID' => $request->input('buyerID'),
            'nik' => $this->nik,
            'password' => $this->password,
        ];
        $client = new Client();

        try {
            $response = $client->request('POST', $host_api.'PO/UpdatePOHeader', [
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

    /**
     * POST /api/purchase-order/update-detail
     * Body expected (json):
     * {
     *   "poNum": 9823,
     *   "partNum": "11198-61J02-000-R",
     *   "XOrderQty": "60",
     *   "reqCategory": "PP",
     *   "nik": "270723-001",
     *   "password": "alkiin01"
     * }
     */
    public function updateDetail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'poNum' => 'required|numeric',
            'partNum' => 'required|string',
            'XOrderQty' => 'required|string',
            'reqCategory' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Use centralized host provider. Will throw if not configured.
        try {
            $host_api = self::get_host_api();
        } catch (\RuntimeException $ex) {
            return response()->json([
                'status' => 'error',
                'message' => $ex->getMessage(),
            ], 500);
        }
  
        $payload = [
            'poNum' => $request->input('poNum'),
            'partNum' => $request->input('partNum'),
            'XOrderQty' => $request->input('XOrderQty'),
            'reqCategory' => $request->input('reqCategory'),
            'nik' => $this->nik,
            'password' => $this->password,
        ];

        $client = new Client();

        try {
            $response = $client->request('POST', $host_api.'PO/UpdatePODetail', [
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
