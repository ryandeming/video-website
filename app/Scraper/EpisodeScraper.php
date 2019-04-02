<?php
namespace App\Scraper;
set_time_limit(0);

use Goutte\Client;
use App\Series;
use App\Scraper\AnimeInfoScraper;

class EpisodeScraper
{

  private $special_chars = array(":", ";", "!", "?", ".", "(", ")", ",", "'", "[", "]", '@', '"');
  protected $animerush_base_url = "http://www.animerush.tv/";
  protected $twist_base_url = "https://twist.moe";
  protected $client;
  protected $mirrors;
  public $series_id;
  private $loop = true;

  public function __construct($series_id)
  {
      $this->series_id = $series_id;
      $this->client = new Client();
  }

  private function scrape_ar($slug, $startep = 1, $endep = -1) {
    // Build scrape url and start loop
    $slug = str_replace("*", "-", str_replace($this->special_chars, "", strtolower($slug)));
    $url = $this->animerush_base_url . $slug . '-episode-' . $startep;
    $crawler = $this->client->request('GET', $url);
    $this->loop = true;
    while ($this->loop) {
      // Extract page title and episode number
      $episode = $crawler->filter('div.bannertit > h1')->extract('_text');
      if (is_array($episode) && count($episode) > 0) {
          $episode = $episode[0];
      }
      // Make sure a title has been found
      if (is_string($episode)) {
        // Split title and episode number
        $episode = filter_var(explode(' Episode ', $episode)[1], FILTER_SANITIZE_NUMBER_FLOAT);

        // Check if this has gone beyond the final episode we intended to scrape
        if ((int)$endep + 1 == $episode) {
            $this->loop = false;
            echo 'Reached end ep: ' . $endep . '<br/>';
            break;
        }

        // Grab each episode mirror as $node
        $mirrors = $crawler->filter('div#left-column > div#episodes')->each(function (\Symfony\Component\DomCrawler\Crawler $node) {
          $mirrors = array();

          // Filter out the links that match the #episodes div
          $links = $node->filter('div.episode1 > div > div > h3 > a')->links();

          // Iterate through these links to find mirrors
          foreach ($links as $link) {
            // Go to the actual episode page
            $crawler = $this->client->click($link);

            // Default quality to 480 to be safe
            $quality = 480;

            // Check if this mirror has the HDLogo, if it does, it's 720p
            if (count($crawler->filter('div.episode1 > div.episode_on > div > div.hdlogo')) > 0) {
                $quality = 720;
            }

            // Set subbed to false to be safe
            $subbed = false;

            // Check if this mirror is tagged as subbed
            if (count($crawler->filter('div.episode1 > div.episode_on > div > span.mirror-sub.subbed')) > 0) {
                $subbed = true;
            }

            // Extract the actual mirror source from the iframe and push it to the mirrors array
            $src = $crawler->filter('div#left-column > div.player-area > div > div > iframe')->first()->extract('src');
            array_push($mirrors, array("quality" => $quality, "subbed" => $subbed, "src" => $src));
          }

          // Return the array(?)
          return $mirrors;
        });

        // Push the actual episode and mirror into the array
        if (!empty($mirrors) && !empty($episode)) {
          array_push($this->episodes, array(
              "episode" => $episode,
              "mirrors" => $mirrors
          ));
        }

        // If our end episode is further than the amount of episodes that exist exit the loop
        $link = $crawler->filter('div.ep-next > a');
        if (count($link) > 0) {
          $link = $link->link();
          $crawler = $this->client->click($link);
        } else {
          $this->loop = false;
          break;
        }

        // If the episode can't be found
      } else {
          $this->loop = false;
          echo '404 at: ' . $url . '<br/>';
          break;
      }
   }

    // return the mirrors
    return $this->episodes;
  }

  public function scrape_twist($slug, $startep, $endep = -1) {
    // Build scrape url and start loop
    $mirrors = array();
    $slug = str_replace("*", "-", str_replace($this->special_chars, "", strtolower($slug)));
    $url = $this->twist_base_url . '/a/' . $slug . '/' . $startep;
    $crawler = $this->client->request('GET', $url);
    dd($crawler);
    $this->loop = true;
    while ($this->loop) {
      // Extract page title and episode number
      $episode = $crawler->filter('div.series-episode span')->extract('_text');

      if (is_array($episode) && count($episode) > 0) {
          $episode = $episode[0];
      }
      // Make sure a title has been found
      if (is_string($episode)) {
        // Split title and episode number
        $episode = filter_var(explode('Episode ', $episode)[1], FILTER_SANITIZE_NUMBER_FLOAT);

        // Check if this has gone beyond the final episode we intended to scrape
        if ((int)$endep + 1 == $episode) {
            $this->loop = false;
            echo 'Reached end ep: ' . $endep . '<br/>';
            break;
        }
        $src = $this->twist_base_url;
        $src2 = $crawler->filter('video')->first()->extract('src');
        dd($src2);
        $quality = 1080;
        $subbed = true;
        // Extract the actual mirror source from the iframe and push it to the mirrors array
        
        array_push($mirrors, array("quality" => $quality, "subbed" => $subbed, "src" => $src));

        // Push the actual episode and mirror into the array
        if (!empty($mirrors) && !empty($episode)) {
          array_push($this->episodes, array(
              "episode" => $episode,
              "mirrors" => $mirrors
          ));
        }

        // If our end episode is further than the amount of episodes that exist exit the loop
        $links = $crawler->filter('.episode-list li a');
        if (count($links) > 0 && is_array($links)) {
          $link = $links[$episode]->link();
          $crawler = $this->client->click($link);
        } else {
          $this->loop = false;
          break;
        }

        // If the episode can't be found
      } else {
          $this->loop = false;
          echo '404 at: ' . $url . '<br/>';
          break;
      }
   }

    // return the mirrors
    return $this->episodes;
  }

  public function get($episode = 1, $endep = -1) {
    $this->episodes = array();
    $series = Series::find($this->series_id);
    if (empty($series)) {
        return null;
    }
    return $this->scrape_twist($series->slug, $episode, $endep);
  }

  public function uploadOpenload() {

  }
}