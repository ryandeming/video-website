<?php

namespace App\Http\Controllers;

use App\Library;
use App\User;
use App\Series;
use Illuminate\Http\Request;

class LibraryController extends Controller
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

      $library = new Library;

      $data = $this->validate(request(), [
        'user_id'       => 'required',
        'series_id'     => 'required',
        'last_watched_episode' => 'nullable',
        'is_fav' => 'nullable',
        'status' => 'nullable'
      ]);

      $library->user_id = $data['user_id'];
      $library->series_id = $data['series_id'];
      $library->last_watched_episode = 1;
      $library->is_fav = 0;
      $library->status = 1;

      $library->save();

      return $data;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Library  $library
     * @return \Illuminate\Http\Response
     */
    public function edit(Library $library)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Library  $library
     * @return \Illuminate\Http\Response
     */
    public function update(Library $library)
    {
        $data = $this->validate(request(), [
          'user_id'       => 'required',
          'series_id'     => 'required',
          'last_watched_episode' => 'required',
          'is_fav' => 'required',
          'status' => 'required',
          'rating' => 'required',
        ]);

        $library->rating = $data['rating'];

        $library->update($data);

        return $data;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Library  $library
     * @return \Illuminate\Http\Response
     */
    public function destroy(Library $library)
    {
        //
    }
}
