<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class bank_details extends Model
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
    protected $table = 'bank_details';

    protected $primaryKey = 'userMobileNo';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'userMobileNo',
        'account_no',
        'name',
        'IFSC',
    ];
}
