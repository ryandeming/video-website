<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Series extends Model
{
    protected $table = 'series';
    protected $fillable = ['kit_id'];
    protected $hidden = ['created_at', 'updated_at', 'kit_id'];
    
    public function episodes() {
        return $this->hasMany(Episode::class)->select(['series_id', 'episode']);
    }

    public function articleTitles() {
        return $this->hasMany('App\Article')->select(['id', 'title', 'user_id']);
    }

    public static function getSeries($series_id) {
        return Series::where('id', '=', $series_id)->first();
    }

    public static function getPoster($series_id) {
        return Series::select('poster')->where('id', '=', $series_id)->first()->poster;
    }

    public static function getName($series_id) {
        return Series::select('name')->where('id', '=', $series_id)->first()->name;
    }
}
