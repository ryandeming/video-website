<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::get('/', 'HomeController@index');
Route::get('/home', function() {
    return view('welcome');
});

Route::get('/openload/{series_id}/{episode}', 'EpisodesController@openload');
Route::get('/openload/status', 'EpisodesController@openloadStatus');

Route::get('/streamango/{series_id}/{episode}', 'EpisodesController@streamango');
Route::get('/streamango/status', 'EpisodesController@streamangoStatus');

Route::get('/link/uploads', 'UploadsController@link');


// watch
Route::get('/watch/{id}/{name}/{episode}', 'EpisodesController@show');


// scraping
Route::get('/scrape', 'EpisodesController@scrape');
Route::get('/scrape/{id}/{start_ep}/{end_ep}', 'EpisodesController@scrape');
Route::get('/series/scrape/{name}', 'SeriesController@scrape');

// series
Route::get('/series', 'SeriesController@index');
Route::get('/series/{series}', 'SeriesController@show');
Route::get('/series/{series}/edit', 'SeriesController@edit');
Route::put('/series/{series}', 'SeriesController@update');

// searches
Route::get('/series/search/{name}', 'SeriesController@search');
Route::get('/series/genre/{genre}', 'SeriesController@genre');

// profiles
Route::get('/profile/{user}/{username}', 'ProfileController@show');

// library
Route::post('/series/library', 'LibraryController@store');
Route::patch('/series/library/{library}', 'LibraryController@update');

// recently watched
Route::post('/watch/addrecent', 'RecentlyWatchedController@store');
