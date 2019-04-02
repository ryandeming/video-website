<?php

namespace App\Http\Controllers;

use App\RecentlyWatched;
use App\User;
use App\Series;
use App\Library;
use Illuminate\Http\Request;

class RecentlyWatchedController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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

      $data = $this->validate(request(), [
        'user_id'       => 'required',
        'series_id'     => 'required',
        'episode' => 'required',
      ]);

      $recentlywatched = RecentlyWatched::firstOrNew(
        array(
          'user_id' => $data['user_id'],
          'series_id' => $data['series_id'],
          'episode' => $data['episode'],
        )
      );

      $recentlywatched->user_id = $data['user_id'];
      $recentlywatched->series_id = $data['series_id'];
      $recentlywatched->episode = $data['episode'];

      $library = Library::firstOrNew(
        array(
          'user_id' => $data['user_id'],
          'series_id' => $data['series_id'],
        )
      );

      $library->user_id = $data['user_id'];
      $library->series_id = $data['series_id'];
      if($library->status) {
        if($library->status !== 2) {
          if($library->last_watched_episode < $data['episode']) {
            $library->last_watched_episode = $data['episode'];
          }
        }
      }

      $library->save();
      $recentlywatched->save();

      return $data;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\RecentlyWatched  $recentlywatched
     * @return \Illuminate\Http\Response
     */
    public function edit(RecentlyWatched $recentlywatched)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\RecentlyWatched  $recentlywatched
     * @return \Illuminate\Http\Response
     */
    public function update(RecentlyWatched $recentlywatched)
    {
        $data = $this->validate(request(), [
          'user_id'       => 'required',
          'series_id'     => 'required',
          'last_watched_episode' => 'required',
          'is_fav' => 'required',
          'status' => 'required'
        ]);


        $recentlywatched->update($data);

        return $data;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\RecentlyWatched  $recentlywatched
     * @return \Illuminate\Http\Response
     */
    public function destroy(RecentlyWatched $recentlywatched)
    {
        //
    }
}
