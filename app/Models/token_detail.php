<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class token_detail extends Model
{
    use HasFactory;

    /**
     * The connection name for the model.
     *
     * @var string|null
     */
    protected $connection = 'mysql';

    /**
     * The collection associated with the model.
     *
     * @var string
     */
    protected $table = 'token_details';

    protected $primaryKey = 'tokenId';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'tokenName',
        'amount',
        'expiryTime',
        'userMobileNo',
        'status',
    ];
}
