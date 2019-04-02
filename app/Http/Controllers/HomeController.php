<?php

namespace App\Http\Controllers;

use App\Episode;
use App\Series;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $videos = Episode::getRecent(12);

        foreach($videos as $video) {
            // Structure time
            $video->time = Episode::timeElapsedString($video->created_at);

            // Get Series Data
            $video->name = Series::getName($video->series_id);
            $video->img = Series::getPoster($video->series_id);

            // Reduce name length
            if(strlen($video->name) > 32) {
                $video->name = substr($video->name, 0, 32);
            }

            // turn name into slug
            $video->url = strtolower(str_replace(array(" ", "/", "?"), '-', $video->name));

            // remove unnecessary information
            $video = array_except($video, ['created_at', 'id', 'updated_at']);
        }

        return View::component('Home', [
            'videos' => $videos,
        ]);
    }
}
