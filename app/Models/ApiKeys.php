<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ApiKeys extends Model
{
    protected $table = 'api_key';
    use HasFactory;
    protected $fillable = ['name', 'akey', 'creator_id'];
    public $timestamps = false;
}
