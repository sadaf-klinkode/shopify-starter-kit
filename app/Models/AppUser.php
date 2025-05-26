<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppUser extends Model
{

    protected $table = 'appusers';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'account_owner',
        'collaborator',
        'email_verified',
        'locale',
        'last_session',
        'last_login_time',
        'myshopify_store_url',
        'store_url',
        'store_name',
        'plan_name',
        'plan_price',
        'plan_activation_time',
        'plan_status',
        'user_id',
        'apps',
        'app_name',
    ];

    protected $casts = [
        'account_owner'         => 'boolean',
        'collaborator'          => 'boolean',
        'email_verified'        => 'boolean',
        'last_login_time'       => 'datetime',
        'plan_activation_time'  => 'datetime',
        'plan_price'            => 'decimal:2',
        'apps'                  => 'array',
    ];
}
