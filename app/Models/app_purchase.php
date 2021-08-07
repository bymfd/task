<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class app_purchase extends Model
{
    public $table = 'app_purchase';
    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = ['status', 'expire_date', 'receipt'];

}