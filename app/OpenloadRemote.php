<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OpenloadRemote extends Model
{
    protected $table = 'openload_remote';
    protected $fillable = ['openload_file_id', 'series_slug', 'episode'];

    public static function checkStatus() {
        $login = "f8089cabc8201595";
        $key = "fKovhGXS";

        $openloads = OpenloadRemote::whereRaw('status = ?', 0)->get();
        foreach($openloads as $openload) {
            $id = $openload->openload_file_id;
            $series = Series::where('slug', '=', $openload->series_slug)->first();
            if(!$series) {
                return 'No Series Found: '.$openload->series_slug;
            }
            $episode = Episode::select('id')->where('series_id', '=', $series->id)->where('episode', '=', $openload->episode)->first();

            $crawlMe = "https://api.openload.co/1/remotedl/status?login=".$login."&key=".$key."&id=".$id;
            $ch = curl_init($crawlMe);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept:application/vnd.api+json, Content-Type:application/vnd.api+json']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            curl_close($ch);
            $formatted = json_decode($result);

            if($formatted->status == 200) {
                foreach($formatted->result as $stupidHack) {
                    if($stupidHack->status == "finished") {
                        $stupidHack->url = str_replace('/f/', '/embed/', $stupidHack->url);
                        if (strpos($stupidHack->url, '.html') !== false) {
                            echo 'video deleted';
                        } else {
                            if(!$episode) {
                                $episode = Episode::create([
                                    "series_id" => $series->id,
                                    "episode" => $openload->episode,
                                ]);
                            }
                            Mirror::create([
                                "episode_id" => $episode->id,
                                "src" => $stupidHack->url,
                                "host" => "Openload",
                                "quality" => 720,
                                "subbed" => 1
                            ]);
                            $openload->status = 1;
                            $openload->save();
                        }
                    } elseif($stupidHack->status == "error") {
                        $openload->status = 2;
                        $openload->save();
                    }
                }
            }
        }
        return;
    }
}
