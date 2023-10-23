<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Subscription extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 'price', 'remaining_publications', 'active',
    ];

    public function scopeActive(Builder $query)
    {
        return $query->where('active', '=', 1);
    }
}
