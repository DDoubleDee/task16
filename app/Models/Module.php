<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Module extends Model
{
    protected $table = 'modules';
    use HasFactory;
    protected $fillable = ['name', 'creator_id'];
    public $timestamps = false;
}
