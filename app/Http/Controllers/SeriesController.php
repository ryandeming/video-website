<?php

namespace App\Http\Controllers;

use App\Series;
use App\Library;
use App\Scraper\AnimeInfoScraper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

class SeriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $series = Series::select('name', 'id', 'poster')->orderBy('popularity', 'ASC')->paginate(18);

        return View::component('SeriesHome', [
            'series' => $series,
            'genre' => 'genre',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
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
     * @param  \App\Series  $series
     * @return \Illuminate\Http\Response
     */
    public function show(Series $series)
    {


        $episodes = $series->episodes()->paginate(6);

        $series->genres = substr($series->genres, 0, -2);
        $series->genres = explode(', ', $series->genres);

        if(Auth::user()) {
            $user = Auth::user();
            $library = Library::select('id', 'user_id', 'status', 'last_watched_episode', 'is_fav', 'rating')->where('series_id', $series->id)->where('user_id', $user->id)->first();
        } else {
            $library = null;
        }

        return View::component('Series', [
            'series' => $series,
            'episodes' => $episodes,
            'library' => $library,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Series  $series
     * @return \Illuminate\Http\Response
     */
    public function edit(Series $series)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Series  $series
     * @return \Illuminate\Http\Response
     */
    public function update(Series $series)
    {
        $data = $this->validate(request(), [
            'name'       => 'required',
        ]);

        $series->update($data);

        return $data;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Series  $series
     * @return \Illuminate\Http\Response
     */
    public function destroy(Series $series)
    {
        //
    }

    public function scrape($name) {
        $scraper = new AnimeInfoScraper;
        return $scraper->get($name);
    }

    public function search($query) {
        
        $series = Series::select('name', 'id', 'poster')->where('name', 'LIKE', "%$query%")->orderBy('popularity', 'ASC')->paginate(18);

        return View::component('SeriesHome', [
            'series' => $series,
        ]);
    }

    public function genre($genre) {
        $series = Series::select('name', 'id', 'poster')->where('genres', 'LIKE', "%$genre%")->orderBy('popularity', 'ASC')->paginate(18);

        return View::component('SeriesHome', [
            'series' => $series,
            'genre' => $genre,
        ]);
    }
}
