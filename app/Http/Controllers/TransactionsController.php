<?php

namespace App\Http\Controllers;

use App\Models\transactions;
use App\Models\bank_details;
use Illuminate\Http\Request;
use App\Models\token_detail;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Carbon\Carbon;

class TransactionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function addBankdetails(Request $request)
    {
        $this->validate($request, [
            'userMobileNo' => 'required|string|max:12',
            'name' => 'required|string',
            'ifsc' => 'required|string',
            'account' => 'required|string|max:20|min:5',
        ]);

        $insertData =  [
            'name' => $request->name,
            'IFSC' =>  $request->ifsc,
            'account_no' =>  $request->account,
        ];


        $newUser = bank_details::updateOrCreate(
            ['userMobileNo' => $request->userMobileNo,],
            [ 
                'name' => $request->name,
                'IFSC' =>  $request->ifsc,
                'account_no' =>  $request->account,
            ]);

        if ($newUser) {
            return response()->json([
                'success' => true,
                'message' => 'details added Successfully',
                'data' => [],
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'data' => [],
            ], 200);
        }
    }


    public function getBankdetails(Request $request)
    {
        $this->validate($request, [
            'userMobileNo' => 'required|string',
        ]);

        $details = bank_details::where('userMobileNo', $request->userMobileNo)->first();

        if ($details) {

            $data =  [
                'name' => $details->name,
                'ifsc' =>  $details->IFSC,
                'account' =>  $details->account_no,
                'updated_at' => $details->updated_at,
            ];

            return response()->json([
                'success' => true,
                'message' => '',
                'data' => $data,
            ], 200);
        } else {
            return response()->json([
                'success' => true,
                'message' => 'No details found',
                'data' => [],
            ], 200);
        }
    }


    public function getAllTransactions(Request $request)
    {
        $this->validate($request, [
            'userMobileNo' => 'required|string',
        ]);

        $transactions = transactions::where('to', $request->userMobileNo)->get()->sortByDesc('created_at');

        foreach ($transactions as $transaction) {
            $trans[] = [
                'Sender' => $transaction->from,
                'TransactionTime' => $transaction->created_at,
                'amount' => $transaction->amount,
                'Description' => 'RECIEVED('.$transaction->status.')',
                'Token' => $transaction->tokenName,
            ];
        }

        $sentTransactions = transactions::where('from', $request->userMobileNo)->get()->sortByDesc('created_at');

        foreach ($sentTransactions as $transaction) {
            $trans[] = [
                'Reciever' => $transaction->to,
                'TransactionTime' => $transaction->created_at,
                'amount' => $transaction->amount,
                'Description' => 'SENT('.$transaction->status.')',
                'Token' => $transaction->tokenName,
            ];
        }

        if (count($trans) > 0) {
            return response()->json([
                'success' => true,
                'message' => '',
                'data' => $trans,
            ], 200);
        } else {
            return response()->json([
                'success' => true,
                'message' => 'No transaction done Yet',
                'data' => [],
            ], 200);
        }
    }

    public function recieveMoney(Request $request)
    {
        $this->validate($request, [
            'userMobileNo' => 'required|string',
            'amount' => 'required|integer',
            'token' => 'required|string',
        ]);
    
        $key = env('JWT_SECRET_KEY');
        $decoded = JWT::decode($request->token, new Key($key, 'HS256'));


        $token = token_detail::select('tokenId', 'status', 'expiryTime', 'amount', 'userMobileNo')->where('tokenId', $decoded->tokenId)->first();

        if($token->status != 'ACTIVE' || $token->expiryTime < Carbon::now()){
            $message = 'Token is already expired or deleted';
            $data = [];
        } elseif ($token->amount < $request->amount) {
            $message = 'Insufficient amount in token';
            $data = ["required" => $request->amount,
            "available" => $token->amount];
        } else {
            $token->amount -= $request->amount;
            $res = $token->save();

            if (isset($res) && $res) {
                $transaction = [
                    'from' => $token->userMobileNo,
                    'to' => $request->userMobileNo,
                    'amount' => $request->amount,
                    'tokenId' => $token->tokenId,
                    'tokenName' => $token->tokenName,
                    'transactionId' => NULL,
                    'status' => 'TRANSFERED',
                ];
                $trans = transactions::CREATE($transaction);
            }

        }

        if (isset($trans) && $trans) {
            return response()->json([
                'success' => true,
                'message' => 'Amount added successfully, your amount will be add to your registered bank account',
                'data' => [],
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => $message,
                'data' => $data,
            ], 200);
        }
    }
}
