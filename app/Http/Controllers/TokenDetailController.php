<?php

namespace App\Http\Controllers;

use App\Models\token_detail;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;



class TokenDetailController extends Controller
{

    public function addToken(Request $request)
    {
        $this->validate($request, [
            'TokenName' => 'required|string|max:255',
            'Amount' => 'required|integer',
            'ExpiryHours' => 'required|integer',
            'userMobileNo' => 'required|digits:10',
        ]);

        $expiry = Carbon::now()->addHours($request->ExpiryHours);
        $insertData =  [
            'tokenName' => $request->TokenName,
            'amount' =>  $request->Amount,
            'expiryTime' =>  $expiry,
            'userMobileNo' =>  $request->userMobileNo,
            'status' => 'ACTIVE',
        ];
        $tokenObj = new token_detail();
        $inserted = $tokenObj->create($insertData);

        $key = env('JWT_SECRET_KEY');
        $payload = [
            'tokenId' => $inserted->id,
            'tokenName' =>  $inserted->tokenName,
            'amount' => $inserted->amount,
            'userMobileNo' => $inserted->userMobileNo,
        ];

        $jwt = JWT::encode($payload, $key, 'HS256');
        // $decoded = JWT::decode($jwt, new Key($key, 'HS256'));

        if ($jwt) {
            return response()->json([
                'success' => true,
                'message' => 'Token generated Successfully',
                'data' => ['token'   => $jwt,],
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'data' => [],
            ], 200);
        }
    }


    public function getAllTokens(Request $request)
    {
        $this->validate($request, [
            'userMobileNo' => 'required|digits:10',
        ]);


        $tokenObj = new token_detail();
        $Query = $tokenObj->where('userMobileNo', $request->userMobileNo)
            ->select(DB::raw("tokenId,tokenName,amount,expiryTime,created_at,status"))
            ->orderby('status');

        $result = $Query->get();

        $key = env('JWT_SECRET_KEY');
        $tokens = [];
        foreach ($result as $token) {
            if ($token->status == 'ACTIVE') {
                $payload = [
                    'tokenId' => $token->tokenId,
                    'tokenName' =>  $token->tokenName,
                    'amount' => $token->amount,
                    'userMobileNo' => $request->userMobileNo,
                ];

                $jwt = JWT::encode($payload, $key, 'HS256');

                $tokens[] = [
                    'tokenName' => $token->tokenName,
                    'amount' => $token->amount,
                    'expiryTime' => $token->expiryTime,
                    'token' => $jwt,
                    'status' => $token->status,
                ];
            } else {
                $tokens[] = [
                    'tokenName' => $token->tokenName,
                    'amount' => $token->amount,
                    // 'expiryTime' => NULL,
                    // 'token' => NULL,
                    'status' => $token->status,
                ];
            }
        }

        if (count($tokens) > 0) {
            return response()->json([
                'success' => true,
                'message' => '',
                'data' => $tokens,
            ], 200);
        } else {
            return response()->json([
                'success' => true,
                'message' => 'No Token Created Yet',
                'data' => [],
            ], 200);
        }
    }

    
    public function deleteToken(Request $request)
    {
        $this->validate($request, [
            'token' => 'required|string',
        ]);

        $key = env('JWT_SECRET_KEY');
        $decoded = JWT::decode($request->token, new Key($key, 'HS256'));


        $token = token_detail::select('tokenId', 'status')->where('tokenId', $decoded->tokenId)->first();

        if($token->status == 'ACTIVE'){
            $token->status = 'DELETED';
            $res = $token->save();
        } else {
            $message = 'Token is already expired or deleted';
        }

        if (isset($res) && $res) {
            return response()->json([
                'success' => true,
                'message' => 'Token deleted successfully, you amount will be refunded to you within hours',
                'data' => [],
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => $message,
                'data' => [],
            ], 200);
        }
    }

    public function updateTokenExpiry(Request $request)
    {
        $this->validate($request, [
            'token' => 'required|string',
            'ExpiryHours' => 'required|integer',
        ]);

        $key = env('JWT_SECRET_KEY');
        $decoded = JWT::decode($request->token, new Key($key, 'HS256'));


        $token = token_detail::select('tokenId', 'status', 'expiryTime')->where('tokenId', $decoded->tokenId)->first();

        if($token->status == 'ACTIVE'){
            $expiry = Carbon::now()->addHours($request->ExpiryHours);
            $token->expiryTime = $expiry;
            $res = $token->save();
        } else {
            $message = 'Token is already expired or deleted';
        }

        if (isset($res) && $res) {
            return response()->json([
                'success' => true,
                'message' => 'Token  updated successfully',
                'data' => [],
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => $message,
                'data' => [],
            ], 200);
        }
    }
}
