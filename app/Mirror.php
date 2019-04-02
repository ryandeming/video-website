<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Scraper\EpisodeScraper;

class Mirror extends Model
{
    protected $fillable = ['episode_id', 'src', 'host', 'quality', 'subbed'];

    public static function add_mirror($series_id, $episodes, $force = false)
    {
        $txt = '';
        if (is_array($episodes)) {
            foreach ($episodes as $episode) {
                $ep = $episode["episode"];
                foreach ($episode["mirrors"] as $mirrors) {
                    foreach ($mirrors as $mirror) {
                        
                        if (isset($mirror["src"]) && is_array($mirror["src"]) && count($mirror["src"]) > 0) {
                            $src = $mirror["src"][0];
                            $host = Mirror::getHost($src);
                            $quality = $mirror["quality"];
                            $subbed = $mirror["subbed"];
                        } else if (isset($mirrors["src"])) {
                            $src = $mirrors["src"];
                            $host = Mirror::getHost($src);
                            $quality = $mirrors["quality"];
                            $subbed = $mirrors["subbed"];
                        }
                        if (isset($src) && isset($quality) && isset($subbed)) {
                            if ($host == "failed") {
                                $txt .= '<p class="text-error">Episode ' . $ep . ' - ' . $host . ' - Quality' . $quality . ': <strong>host not found</strong>.</p>';
                            } else if (!$subbed) {
                                $txt .= '<p class="text-error">Episode ' . $ep . ' - ' . $host . ' - Quality' . $quality . ': is not <strong>subbed</strong>.</p>';
                            } else {
                                if($ep == '') {
                                    dd($episodes);
                                }
                                $episodeExists = Episode::episodeExists($series_id, $episode);
                                if(!$episodeExists) {
                                    $episode_id = Episode::create([
                                        'series_id' => $series_id,
                                        'episode' => (int)$ep
                                    ]);
                                    $episode_id = $episode_id->id;
                                } else {
                                    $episode_id = $episodeExists;
                                }
                                $exists = Mirror::mirrorExists($episode_id, $host, $quality);
                                if (!$exists) {
                                    Mirror::create([
                                        "episode_id" => $episode_id,
                                        "src" => $src,
                                        "host" => $host,
                                        "quality" => $quality,
                                        "subbed" => $subbed
                                    ]);
                                    $txt .= '<p class="text-success">Episode ' . $ep . ' - ' . $host . ' - Quality' . $quality . ': has been <strong>added</strong>.</p>';
                                } else {
                                    $txt .= '<p class="text-info">Episode ' . $ep . ' - ' . $host . ' - Quality' . $quality . ': this mirror already exists in our database!</p>';
                                }
                            }
                        }
                    }
                }
            }
            return $txt;
        }
        return null;
    }

    public static function put($series_id, $force = false, $episode = 1, $endep = -1)
    {
        $scraper = new EpisodeScraper($series_id);
        $episodes = $scraper->get($episode, $endep);
        //dd($episodes);
        if (!empty($episodes)) {
            $txt = Mirror::add_mirror($series_id, $episodes);
            return '<div class="span12"><p style="text-align: center;">Success! Episodes & Mirrors have been updated!</p>' . $txt . '<hr></div>';
        }
        return '<div class="span12" style="text-align: center;"><p>Failed! We could not find any Episodes or Mirrors!</p></div>';
    }

    public static function openLoad($series_id, $episode) {
        $episode = Episode::select('id')->where('series_id', '=', $series_id)->where('episode', '=', $episode)->first();

        $login = "f8089cabc8201595";
        $key = "fKovhGXS";

        $results = array();

            foreach($episode->mirrors as $mirror) {
                if(!OpenLoad::Exists($mirror->id)) {
                $url = $mirror->src;
                
                $crawlMe = "https://api.openload.co/1/remotedl/add?login=".$login."&key=".$key."&url=".$url;
                $ch = curl_init($crawlMe);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept:application/vnd.api+json, Content-Type:application/vnd.api+json']);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $result = curl_exec($ch);
                curl_close($ch);
                
                echo 'URL: '.$url." QUAL: ".$mirror->quality."<br/>";

                $formatted = json_decode($result);
                array_push($results, $formatted->result);
                if($formatted->status == 200) {
                    OpenLoad::create([
                        "openload_file_id" => $formatted->result->id,
                        "episode_id" => $mirror->episode_id,
                        "mirror_id" => $mirror->id,
                        "quality" => $mirror->quality,
                        "subbed" => $mirror->subbed,
                        "status" => 0,
                    ]);
                }
            }
        }
        
        return $results;
    }

    public static function streamango($series_id, $episode) {
        $episode = Episode::select('id')->where('series_id', '=', $series_id)->where('episode', '=', $episode)->first();

        $login = "TqGnlbxCAM";
        $key = "lP8sesKI";

        $results = array();

        
            foreach($episode->mirrors as $mirror) {
                if(!Streamango::Exists($mirror->id)) {
                $url = $mirror->src;
                $crawlMe = "https://api.fruithosted.net/remotedl/add?login=".$login."&key=".$key."&url=".$url;
                $ch = curl_init($crawlMe);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept:application/vnd.api+json, Content-Type:application/vnd.api+json']);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $result = curl_exec($ch);
                curl_close($ch);
                
                $formatted = json_decode($result);

                array_push($results, $formatted->result);
                if($formatted->status == 200) {
                    Streamango::create([
                        "streamango_file_id" => $formatted->result->id,
                        "episode_id" => $mirror->episode_id,
                        "quality" => $mirror->quality,
                        "mirror_id" => $mirror->id,
                        "subbed" => $mirror->subbed,
                        "status" => 0,
                    ]);
                }
            }
        }
        
        return $results;
    }

    public static function getHost($link)
    {
        if (isset($link) && is_string($link) && strlen($link) > 0) {
            if (strpos($link, "auengine.com") !== false) {
                return "AUEngine";
            } else if (strpos($link, "mp4upload.com") !== false) {
                return "MP4Upload";
            } else if (strpos($link, "videodrive.tv") !== false) {
                return "Videodrive";
            } else if (strpos($link, "videonest.net") !== false) {
                return "Videonest";
            } else if (strpos($link, "veevr.com") !== false) {
                return "Veevr";
            } else if (strpos($link, "putlocker.com") !== false) {
                return "Putlocker";
            } else if (strpos($link, "vidbull.com") !== false) {
                return "Vidbull";
            } else if (strpos($link, "arkvid.tv") !== false) {
                return "Arkvid";
            } else if (strpos($link, "video44.net") !== false) {
                return "Goplayer";
            } else if (strpos($link, "embed.videoweed") !== false) {
                return "Videoweed";
            } else if (strpos($link, "twist.moe") !== false) {
                return "Twist";
            }
        }
        return "failed";
    }

    public static function mirrorExists($episode_id, $host, $quality)
    {
        $mirrors = Mirror::whereRaw('episode_id = ?', $episode_id)->select('quality', 'host')->get();
        if (!empty($mirrors)) {
            foreach ($mirrors as $mirror) {
                if ($mirror->quality == $quality && $mirror->host == $host)
                    return true;
            }
        }
        return false;
    }
}
