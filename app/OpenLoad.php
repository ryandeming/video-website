<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OpenLoad extends Model
{

    protected $table = 'openload_mirrors';
    protected $fillable = ['openload_file_id', 'mirror_id', 'episode_id', 'quality', 'subbed', 'status'];

    public static function Exists($mirror_id) {
        $mirror = OpenLoad::where('mirror_id', $mirror_id)->first();

        if(!empty($mirror)) {
            return true;
        }

        return false;
    }

    public static function mirrorExists($episode_id) {
        $episode = Episode::where('id', $episode_id)->first();
        $episode->mirrors;

        foreach($episode->mirrors as $mirror) {
            if($mirror->host == 'OpenLoad') {
                return true;
            }
        }

        return false;
    }

    public static function linkUploads() {
        $uploads = Upload::all();
        return $uploads;
    }

    /*
    public static function checkStatus() {
        $login = "f8089cabc8201595";
        $key = "fKovhGXS";

        $openloads = OpenLoad::whereRaw('status = ?', 0)->get();
        foreach($openloads as $openload) {
            $id = $openload->openload_file_id;

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
                            Mirror::create([
                                "episode_id" => $openload->episode_id,
                                "src" => $stupidHack->url,
                                "host" => "OpenLoad",
                                "quality" => $openload->quality,
                                "subbed" => $openload->subbed
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
    */
}
