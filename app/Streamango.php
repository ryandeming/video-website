<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Streamango extends Model
{

    protected $table = 'streamango_mirrors';
    protected $fillable = ['streamango_file_id', 'episode_id', 'mirror_id', 'quality', 'subbed', 'status'];

    public static function checkStatus() {
        $login = "TqGnlbxCAM";
        $key = "lP8sesKI";

        $streamangos = Streamango::whereRaw('status = ?', 0)->get();
        foreach($streamangos as $streamango) {
            $id = $streamango->streamango_file_id;

            $crawlMe = "https://api.fruithosted.net/remotedl/status?login=".$login."&key=".$key."&id=".$id;
            echo($crawlMe);
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
                        Mirror::create([
                            "episode_id" => $streamango->episode_id,
                            "src" => $stupidHack->url,
                            "host" => "Streamango",
                            "quality" => $streamango->quality,
                            "subbed" => $streamango->subbed
                        ]);
                        $streamango->status = 1;
                        $streamango->save();
                    } elseif($stupidHack->status == "error") {
                        $streamango->status = 2;
                        $streamango->delete();
                    }
                }
            }
        }
    }

    public static function Exists($mirror_id) {
      $mirror = Streamango::where('mirror_id', $mirror_id)->first();

      if(!empty($mirror)) {
          return true;
      }

      return false;
    }

    public static function mirrorExists($episode_id) {
      $episode = Episode::where('id', $episode_id)->first();
      $episode->mirrors;

      foreach($episode->mirrors as $mirror) {
          if($mirror->host == 'Streamango') {
              return true;
          }
      }

      return false;
  }
}
