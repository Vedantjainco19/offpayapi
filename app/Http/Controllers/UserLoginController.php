<?php

namespace App\Http\Controllers;

use App\Models\user_login;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Carbon\Carbon;
use App\Models\User;
// use Illuminate\Support\Facades\Auth;

class UserLoginController extends Controller
{

    public function login(LoginRequest $request)
    {
        $this->validate($request, [
            'mobileNo' => 'required|digits:10'
        ]);

        $otp = rand(123456, 999999);

        $newUser = user_login::updateOrCreate(
            ['mobileNo'   => $request->mobileNo,],
            [
                'otp' => $otp,
                'otpExpiry' => Carbon::now()->addMinutes(15)
            ]
        );

        // $this->sendOTP($request->mobileNo, $otp);  // Working fine for now

        if ($newUser) {
            return response()->json([
                'success' => true,
                'message' => 'OTP sent to your mobile number',
                'data' => ['mobileNo'   => $request->mobileNo,],
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'data' => [],
            ], 200);
        }
    }

    public function  sendOTP($mobile_no, $otp)
    {
        $url = 'https://2factor.in/API/V1/6d97edcb-5f69-11ed-9c12-0200cd936042/SMS/' . $mobile_no . '/' . $otp . '/Your Verification OTP is';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        $info = curl_getinfo($ch);
        // $http_result = $info ['http_code'];
        curl_close($ch);
    }

    public function verifyLogin(Request $request)
    {
        $this->validate($request, [
            'mobileNo' => 'required|digits:10',
            'otp' => 'required|digits:6'
        ]);



        $data = user_login::where('mobileNo', $request->mobileNo)->firstOrFail();
        $otpExpiry = new Carbon($data->otpExpiry);
        $currentTime = Carbon::now();
        if($data->otp != $request->otp){
            $message = 'Incorrect OTP entered';
        } elseif ($currentTime > $otpExpiry) {
            $message = 'OTP expired';    
        } 

        if (!isset($message)) {
            return response()->json([
                'success' => true,
                'message' => 'Verification Successful',
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

    public function resendOTP(LoginRequest $request)
    {
        $this->validate($request, [
            'mobileNo' => 'required|digits:10'
        ]);

        $data = user_login::where('mobileNo', $request->mobileNo)->firstOrFail();
        $otp = $data->otp;
        $data->otpExpiry =  Carbon::now()->addMinutes(15);
        $res = $data->save();

        // $this->sendOTP($request->mobileNo, $otp);  // Working fine for now

        if ($res) {
            return response()->json([
                'success' => true,
                'message' => 'OTP resent to your mobile number',
                'data' => ['mobileNo'   => $request->mobileNo,],
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'data' => [],
            ], 200);
        }
    }
}
