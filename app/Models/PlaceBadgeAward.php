<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlaceBadgeAward extends Model
{
    public $timestamps = false;

    protected $connection = 'finobe';
    protected $table = 'place_badge_awards';

    protected $fillable = ['badge_id', 'user_id', 'awarded_at'];
}
