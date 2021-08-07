<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Devices extends Model
{
    public $table = 'devices';
    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = ['uid', 'app_id', 'language', 'os'];

}
