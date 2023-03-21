<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\token_detail;
use App\Models\transactions;

class TokenProcessService
{
    /**
     * Generate Files
     *
     * @param $number
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public static function ExpireToken()
    {
        $expiry = Carbon::now();
        $expiredToken = token_detail::where('expiryTime', '<', $expiry->toDateTimeString())
            ->where('status', 'ACTIVE')
            ->get();

        foreach ($expiredToken as $token) {
            $transaction = [
                'from' => 'SYSTEM',
                'to' => $token->userMobileNo,
                'amount' => $token->amount,
                'tokenId' => $token->tokenId,
                'tokenName' => $token->tokenname,
                'transactionId' => NULL,
                'status' => 'REFUNDED',
            ];
            transactions::CREATE($transaction);
        }

        $expiredToken = token_detail::where('expiryTime', '<', $expiry->toDateTimeString())
            ->where('status', 'ACTIVE')
            ->update(['status' => 'EXPIRED']);
    }
}
