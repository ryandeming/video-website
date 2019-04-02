<?php
namespace App\Scraper;

set_time_limit(600);
use Goutte\Client;
use DB;
use App\Mirror;
use App\Episode;
use App\Scraper\EpisodeScraper;

class RecentAnimeScraper
{

    public static $url = "http://www.animerush.tv/";

    public static function get($url = "http://www.animerush.tv")
    {
        $client = new Client();
        $crawler = $client->request('GET', $url);
        error_log($url);

        $episodes = $crawler->filter('div#episodes')->each(function (\Symfony\Component\DomCrawler\Crawler $node) {
            $subbed = $node->filter('div.episode > div > div > span.episode-sub')->extract('_text');
            $name = $node->filter('div.episode > div > h3 > a')->extract('_text');
            $time = $node->filter('div.episode > div > div > span.episode-meta')->extract('_text');
            return array($subbed, $name, $time);
        });
        $details = array();
        
        if (isset($episodes[0][0]) && isset($episodes[0][1])) {
            for ($i = 0; $i < count($episodes[0][0]); $i++) {
                $subbed = $episodes[0][0][$i];
                $h3 = $episodes[0][1][$i];
                $time = $episodes[0][2][$i];
                $name_and_ep = explode(" - Episode ", $h3);
                if (count($name_and_ep) === 2) {
                    $name = $name_and_ep[0];
                    $episode = filter_var($name_and_ep[1], FILTER_SANITIZE_NUMBER_FLOAT);
                    $series = DB::table('series')->select('id', 'name')->where('name', '=', $name)->take(1)->get();
                    $series_id = 0;
                    if (isset($series[0]) && is_object($series[0])) {
                        $series_id = $series[0]->id;
                    }
                    if (!empty($series_id)) {
                        $r = new PrepareAnime(array(
                            "series_id" => $series_id,
                            "episode" => $episode,
                            "subbed" => $subbed
                        ));
                        array_push($details, $r);
                    } else {
                        echo 'DOESN\'T EXIST -> ' . $name . ' GRABBING NOW';
                        echo '<br/>';
                         $info = new AnimeInfoScraper;
                        $grabbed = $info->get($name);
                        
                    }
                }
            }
        }
        echo count($details);
        return $details;
    }

    public static function scrape()
    {
        $episodes = RecentAnimeScraper::get();
        if (is_array($episodes) && count($episodes) > 0) {
            foreach ($episodes as $episode) {
                $episode->scrape();
            }
        }
    }

}

class PrepareAnime
{

    public $series_id;
    public $episode;
    public $subbed;

    public function __construct($details)
    {
      if (is_array($details)) {
          $this->series_id = $details["series_id"];
          $this->episode = $details["episode"];
          $this->subbed = ($details["subbed"] === "subbed");
      }
    }

    public function scrape()
    {
        Mirror::put($this->series_id, true, $this->episode);
    }

}