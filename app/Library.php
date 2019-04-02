<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Library extends Model
{
    protected $table = 'libraries';
    protected $attributes = [
        'last_watched_episode' => 1,
        'status' => 1,
        'is_fav' => 0
    ];
    protected $fillable = ['is_fav', 'status', 'last_watched_episode'];

    public function series() {
        return $this->belongsTo(Series::class)->select('id', 'name', 'poster', 'total_eps', 'slug');
    }

    public static function status($status) {
        switch ($status) {
            case 1:
                return 'Watching';
                break;
            case 2:
                return 'Completed';
                break;
            case 3:
                return 'On Hold';
                break;
            case 4:
                return 'Dropped';
                break;
            case 5:
                return 'Plan to Watch';
                break;
            default:
                return;
        }
    }
}
