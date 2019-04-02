<?php
namespace App\Scraper;

set_time_limit(0);

//use Goutte\Client;
use App\Series;
// For resizing and saving images, uncomment if you uncomment the code in the save function
//use \Gumlet\ImageResize;

class AnimeInfoScraper
{
  private $special_chars = array(":", ";", "!", "?", ".", "(", ")", ",", "*", '"');

  public $kitsu_base_url = "https://kitsu.io/api/edge/anime?filter[text]="; // [text]=name
  public $kistu_genre_url = "https://kitsu.io/api/edge/anime/";

  private function getJSON($query, $genre = false)
  {
    // Set Client
    //$client = new Client();
    //$client = $client->getClient();

    // Set Query and remove stupid stuff
    $query = preg_replace('/[[:^print:]]/', '', $query);

    echo "crawling: ".$this->kitsu_base_url . '' . $query;
    echo '<br/>';

    // Check if we're doing a genre crawl or a series crawl
    if($genre == true) {
      $crawlMe = $this->kistu_genre_url . '' . $query . '/genres';
    } else {
      $crawlMe = $this->kitsu_base_url . '' . $query;
    }

    // Make actual request  
    $ch = curl_init($crawlMe);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept:application/vnd.api+json, Content-Type:application/vnd.api+json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);

    // If there's a result, return it. If not, null it.
    if($result == null) {
      return null;
    } else {
      return json_decode($result);
    }


    // Just incase something is fucked up
    return null;
  }

  public function get($keyword)
  { // This is our server request
    
    // Replace stupid shit from the keyword
    $id = str_replace(" ", "-", str_replace($this->special_chars, "", strtolower($keyword)));

    // Request keyword from kitsu
    $json_response = $this->getJSON($id);
    
    // This loops through EVERY series returned from kitsu.

    // Uncomment the line below and delete the for each to only get the first series (not recommended)
    // $json = $json_response->data[0]
    foreach($json_response->data as $json) {
      if (!empty($json)) {
        // Need to check if any of this shit is empty and null it out or we throw an error
        // Should be self explanatory, if it isn't, what are you doing here?
        if(empty($json->attributes->titles->en)) {
          $json->attributes->titles->en = null;
        }
        if(empty($json->attributes->titles->en_jp)) {
          $json->attributes->titles->en_jp = null;
        }
        if(empty($json->attributes->startDate)) {
          $json->attributes->startDate = null;
        }
        if(empty($json->attributes->endDate)) {
          $json->attributes->endDate = null;
        }

        // There should always be an original, but ideally we want to store the small one
        // Since we're being assholes and loading images straight from the API onto our frontend
        // Hopefully this isn't the case in the future
        // There's actually code in the save function to store the images on your own server if you feel so inclined
        if(empty($json->attributes->posterImage->small)) {
          $posterImage = $json->attributes->posterImage->original;
        } else {
          $posterImage = $json->attributes->posterImage->small;
        }

        // Set all this information up in an array to save to the database
        $data = array(
            "kit-id" => $json->id,
            "title" => (string)$json->attributes->canonicalTitle,
            "english_title" => (string)$json->attributes->titles->en,
            "synonyms" => (string)$json->attributes->titles->en_jp,
            "total_eps" => (int)$json->attributes->episodeCount,
            "type" => (string)$json->attributes->showType,
            "status" => (string)$json->attributes->status,
            "start_date" => (string)$json->attributes->startDate,
            "end_date" => (string)$json->attributes->endDate,
            "synopsis" => (string)$json->attributes->synopsis,
            "genres" => null,
            "youtube_trailer_id" => $json->attributes->youtubeVideoId,
            "poster" => $posterImage,
            "popularity" => $json->attributes->popularityRank,
        );

        // Since we're already doing this, we might as well grab the genres as well
        $genre_response = $this->getJSON($json->id, true);
        foreach($genre_response->data as $genre) {
          $data['genres'] .= $genre->attributes->name . ', ';
        }
        $this->save($data);
      }
    }
    return null;
  }

  public function save($data)
  {
    //Find anime by the kitsu ID, if it doesn't exist, make a new one.
      $anime = Series::firstOrNew(array('kit_id' => $data["kit-id"]));

      // Set the kit-id and name
      $anime->kit_id = $data["kit-id"];
      $anime->name = $data["title"];

      // Do title checks
      if ($data["title"] != $data["english_title"])
          $anime->english_name = $data["english_title"];
      if (!empty($data["synonyms"])) {
          $synonyms = explode("; ", $data["synonyms"]);
          $anime->alternate_name = $synonyms[0];
      }

      // End date doesn't come in as null, so it's ideal to actually null it if it doesn't exist
      if($data["end_date"] == '') {
        $data["end_date"] = null;
      }

      /* Resize and save images to server (working)
      $newImageName = str_replace(" ", "-", str_replace($this->special_chars, "", strtolower($data["title"])));
      $newImageName = str_replace("--", "-", str_replace("--", "-", $newImageName));
      $img = file_get_contents($data["poster"]);
      $im = imagecreatefromstring($img);
      imagejpeg($im, public_path().'/images/series/'.$newImageName.'.jpg');
      $image = new ImageResize(public_path().'/images/series/'.$newImageName.'.jpg');
      $image->resizeToWidth(280);
      $image->save(public_path().'/images/series/'.$newImageName.'.jpg');
      imagedestroy($im);
      */

      // This may need to be changed manually later
      $slug = str_replace(" ", "-", str_replace($this->special_chars, "", strtolower($anime->name)));
      $slug = preg_replace('/[[:^print:]]/', '', $slug);


      // Set the data and save it
      $anime->poster = $data["poster"];
      $anime->start_date = $data["start_date"];
      $anime->end_date = $data["end_date"];
      $anime->description = $data["synopsis"];
      $anime->total_eps = $data["total_eps"];
      $anime->status = $data["status"];
      $anime->type = $data["type"];
      $anime->genres = $data["genres"];
      $anime->youtube_trailer_id = $data["youtube_trailer_id"];
      $anime->popularity = $data["popularity"];
      $anime->slug = $slug;
      $anime->save();
      echo 'Saved: '.$data['title'];
      echo '<br/>';
  }

}