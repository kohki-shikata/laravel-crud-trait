<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\CrudTrait;

class Category extends Model
{
    use HasFactory, SoftDeletes, CrudTrait;

    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];
}
