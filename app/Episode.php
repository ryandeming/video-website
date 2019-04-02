<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use DateTime;

class Episode extends Model
{
    protected $table = 'episodes';
    //protected $primaryKey = 'episode';
    protected $fillable = ['series_id', 'episode'];
    
    public function mirrors() {
        return $this->hasMany(Mirror::class)->orderBy('quality', 'DESC');
    }

    public static function getRecent($paginate = null) {
        $query = Episode::where(
            'created_at', '>', \Carbon\Carbon::today()->subWeek())
            ->orderBy('created_at', 'DESC')
            ->orderby(DB::raw('CAST(episode AS SIGNED)'),
            'DESC'
        );

        if (isset($paginate))
            return $query->paginate($paginate);
        return $query->get();
    }

    public function getEpisode($series, $episode) {
        return Episode::where('series_id', $series)->where('episode', $episode)->first();
    }

    public static function put($series_id, $episode, $force = false)
    {
        $series = Series::findOrFail($series_id);
        if ($series->status == 1 || $force) {
            $latest = Episode::whereRaw('series_id = ? and episode = ?', array($series->id, $episode))->get();
            if (count($latest) <= 0) {
                Episode::create(
                    array(
                        'series_id' => $series->id,
                        'episode' => $episode
                    )
                );
            }
        }
    }

    public static function episodeExists($series_id, $episode) {
        $episode = Episode::where('series_id', $series_id)->where('episode', $episode)->first();

        if(!empty($episode)) {
            return $episode->id;
        }
        return false;
    }

    public static function timeElapsedString($datetime, $full = false)
    {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }
}
