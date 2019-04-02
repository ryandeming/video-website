<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Upload extends Model
{
    protected $table = 'openload_uploads';
    protected $fillable = ['openload_file_id', 'series_name', 'episode', 'status'];

    public static function linkUploads() {
        $uploads = Upload::where('status', '=', 0)->get();
        //return $uploads;
        $mirrors = array();
        foreach($uploads as $upload) {
            $series = Series::where('name', '=', $upload->series_name)->first();
            if(!$series) {
                return 'No Series Found: '.$upload->series_name;
            }
            $episode = Episode::select('id')->where('series_id', '=', $series->id)->where('episode', '=', $upload->episode)->first();
            if($episode) {
                
            } else {
                $episode = Episode::create([
                    "series_id" => $series->id,
                    "episode" => $upload->episode,
                ]);
            }
            $mirror = Mirror::create([
                'episode_id' => $episode->id,
                'src' => 'https://openload.co/embed/'.$upload->openload_file_id,
                'host' => 'OpenLoad',
                'quality' => 720,
                'subbed' => 1,
            ]);

            $updateUpload = Upload::where('id', '=', $upload->id);
            $data['status'] = 1;
            $updateUpload->update($data);
            array_push($mirrors, $mirror);
        }

        return $mirrors;
    }
}
