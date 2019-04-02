<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RecentlyWatched extends Model
{
    protected $table = 'recently_watched';
    protected $fillable = ['user_id', 'series_id', 'episode'];

    public function series() {
        return $this->belongsTo(Series::class)->select('id', 'name', 'poster', 'total_eps', 'slug');
    }
}
