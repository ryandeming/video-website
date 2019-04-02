<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

use App\Episode;
use App\Series;
use App\Mirror;
use App\OpenLoad;
use App\OpenloadRemote;
use App\Streamango;

class EpisodesController extends Controller
{
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Episode  $episode
     * @return \Illuminate\Http\Response
     */
    public function show($id, $name, $episode)
    {

        $episode = Episode::where('series_id', '=', $id)->where('episode', '=', $episode)->first();
        $series = Series::where('id', $id)->first();
        $episode->mirrors;

        //strip extra mirror data
        foreach($episode->mirrors as $mirror)
            $mirror = array_except($mirror, ['created_at', 'episode_id', 'id', 'updated_at']);

        $nextExists = array(
            'next' => 1,
        );

        return View::component('Video', [
            'video' => $episode->only('episode', 'mirrors'),
            'series' => $series->only('name', 'id'),
            'next' => $nextExists,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Episode  $episode
     * @return \Illuminate\Http\Response
     */
    public function edit(Episode $episode)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Episode  $episode
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Episode $episode)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Episode  $episode
     * @return \Illuminate\Http\Response
     */
    public function destroy(Episode $episode)
    {
        //
    }

    public function scrape($anime_id = null, $start_ep = 1, $end_ep = -1) {
        if($anime_id == null) {
            // Get Recent Anime
            return Artisan::call('scrape');
        } else {
            // Go for specific anime
            return Mirror::put($anime_id, true, $start_ep, $end_ep);
        }
    }

    public function openload($series_id, $episode) {
        $whatever = Mirror::openload($series_id, $episode);

        return $whatever;
    }

    public function openloadStatus() {
        $openload = OpenloadRemote::checkStatus();

        return $openload;
    }

    public function streamango($series_id, $episode) {
        $whatever = Mirror::streamango($series_id, $episode);

        return $whatever;
    }

    public function streamangoStatus() {
        $openload = Streamango::checkStatus();

        return $openload;
    }
}
