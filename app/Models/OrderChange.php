<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class OrderChange extends Model
{
    use HasUuids;
    
    protected $fillable = ['order_id', 'changes', 'changed_by'];
}
