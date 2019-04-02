<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $table = 'users';

    public function library() {
        $this->hasMany(Library::class)->only('series_id', 'last_watched_episode', 'status', 'is_fav');
    }

    public function recentlyWatched() {
        $this->hasMany(RecentlyWatched::class)->only('series_id', 'episode', 'created_at')->orderBy('created_at', 'DESC');
    }
}
