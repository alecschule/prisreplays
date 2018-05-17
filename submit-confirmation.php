<?php
   // Process replay submission
   require('database_connect.php');
   $replays_string = $_POST['replays'];
   $replays = preg_split('/\R/', $replays_string);
   foreach ($replays as $replay) {
      add_to_database($replay);
   }

   function add_to_database($replay) { // TODO check if game already exists before adding it
      global $mysqli;
      // fetch replay information with developer provided api
      $url = "http://saved-games-alpha.s3-website-us-east-1.amazonaws.com/" . $replay . ".json.gz";
      // response will be compressed data, so we need a stream wrapper to decode it
      $fetchurl = "compress.zlib://" . $url;
      $jsonstring = file_get_contents($fetchurl, false, stream_context_create(array('http'=>array('header'=>"Accept-Encoding: gzip\r\n"))));
      $data = json_decode($jsonstring, true);

      // prepare information for database insert
      $code = $data["code"];
      $p1_name = $data["playerInfo"][0]["displayName"];
      $p1_id = get_player_id($p1_name);
      $p2_name = $data["playerInfo"][1]["displayName"];
      $p2_id = get_player_id($p2_name);
      $p1_rating = round($data["ratingInfo"]["finalRatings"][0]["displayRating"]);
      $p2_rating = round($data["ratingInfo"]["finalRatings"][1]["displayRating"]);
      $unit_names = [];
      foreach ($data["deckInfo"]["randomizer"][0] as $unit_name) {
         array_push($unit_names, $unit_name);
      }
      // database stores units in a string of ids like "1|67|111|4|5"
      $unit_map = get_unit_map(); // maps unit names to ids
      $unit_string = "";
      foreach ($unit_names as $unit_name) {
         $unit_string .= $unit_map[$unit_name] . "|";
      }
      $unit_string = substr($unit_string, 0, -1); // remove last pipe
      $datetime = date("Y-m-d H:i:s", $data["startTime"]); // unix epoch returned from api
      $result = $data["result"];

      // TODO need better error checking
      // add game to database
      $sql_add = "
      INSERT INTO games(replay_code, p1_id, p2_id, p1_rating, p2_rating, unit_ids, game_date, result)
      VALUES('".$code."',".$p1_id.",".$p2_id.",".$p1_rating.",".$p2_rating.",'".$unit_string."','".$datetime."',".$result.")";

      // TODO sanitize inputs here before making website public
      $result = $mysqli->query($sql_add);
      if ($result == false) {
         echo ($mysqli->error);
         exit();
      }
   }

   function get_player_id($player_name) {
      global $mysqli;
      $sql_lookup = "
      SELECT user_id
      FROM users
      WHERE name = '" . $player_name . "'";
      $result = $mysqli->query($sql_lookup);
      if ($result == false) {
         echo ($mysqli->error);
         exit();
      }
      if ($result->num_rows == 0) { // need to add new user
         $sql_add = "
         INSERT INTO users(name)
         VALUES ('" . $player_name . "')";
         $addresult = $mysqli->query($sql_add);
         if ($addresult == false) {
            echo ($mysqli->error);
            exit();
         }
         $id = $mysqli->insert_id; // id created by auto increment
         return $id; 
      } else { // just return id
         $row = $result->fetch_assoc();
         $id = $row["user_id"];
         return $id;
      }
   }

   function get_unit_map() {
      // returns a dictionary that maps unit names to unit IDs
      global $mysqli;
      $sql_query = "
      SELECT name, unit_id
      FROM units";
      $result = $mysqli->query($sql_query);
      if ($result == false) {
         echo ($mysqli->error);
         exit();
      }
      $return_map = array();
      while ($row = $result->fetch_assoc()) {
         $return_map[$row["name"]] = $row["unit_id"];
      }
      return $return_map;
   }




?>
   
<html>
   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>Prismata Replay Analyzer</title>

      <!-- Bootstrap CSS -->
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
      <!-- Custom CSS -->
      <link rel="stylesheet" href="stylesheet.css" type="text/css"/>
   </head>
   <body>
      <!-- Bootstrap Navbar referenced from https://getbootstrap.com/docs/3.3/components/#navbar -->
      <nav class="navbar navbar-default">
      <div class="container-fluid">
         <!-- Brand and toggle get grouped for better mobile display -->
         <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
               <span class="sr-only">Toggle navigation</span>
               <span class="icon-bar"></span>
               <span class="icon-bar"></span>
               <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="./">PrisReplay</a>
         </div>

         <!-- Collect the nav links, forms, and other content for toggling -->
         <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
            </ul>
            <ul class="nav navbar-nav navbar-right">
               <li><a href="./">Home</a></li>
               <li><a href="advanced-search.php">Search Replays</a></li>
               <li><a href="replay-submit.php">Submit Replays</a></li>
            </ul>
         </div><!-- /.navbar-collapse -->
      </div><!-- /.container-fluid -->
      </nav>

      <p>Please wait...</p>
      <p><?php echo("Replays have finished processing and are ready to be searched"); ?></p>

      <!-- JS required for Bootstrap -->
      <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
      <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>
   </body>
</html>
