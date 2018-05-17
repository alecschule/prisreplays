<?php
   require ("database_connect.php");
   // find results matching search
   $sql_query = "
   SELECT game_id, p1_rating, p2_rating, unit_ids, game_date, replay_code, p1_info.name as p1_name, p2_info.name as p2_name, result
   FROM games
   JOIN users as p1_info on games.p1_id = p1_info.user_id
   JOIN users as p2_info on games.p2_id = p2_info.user_id
   WHERE 1 = 1";

   if ($_GET["player1"]) {
      $temp = "p1_info.name = '" . $_GET["player1"] . "'";
      if ($_GET["player2"]) {
         $temp .= " OR p2_info.name = '".$_GET["player1"] . "'";
         $temp = "(" . $temp . ")";
      }
      $sql_query .= " AND " . $temp;
   }

   if ($_GET["player2"]) {
      $temp = "p2_info.name = '" . $_GET["player2"] . "'";
      if ($_GET["player1"]) {
         $temp .= " OR p1_info.name = '".$_GET["player2"] . "'";
         $temp = "(" . $temp . ")";
      }
      $sql_query .= " AND " . $temp;
   }

   if ($_GET["min-rating"]) {
      if ($_GET["apply-both"]) {
         // both ratings must match
         $sql_query .= " AND p1_rating > ".$_GET["min-rating"]." AND p2_rating > ".$_GET["min-rating"];
      } else {
         // only one rating needs to be above min-rating
         $sql_query .+ " AND (p1_rating > ".$_GET["min-rating"]." OR p2_rating > ".$_GET["min-rating"].")";
      }
   }

   if (isset($_GET["match-result"])) {
      $sql_query .= " AND result = ".$_GET["match-result"];
   }

   if ($_GET["units"]) {
      $unit_map = get_unit_map(); // get map of unit names to unit ids from database
      $desired_units = preg_split("/\R/", urldecode($_GET["units"]));
      foreach ($desired_units as $unit_name) {
         // get unit id that matches unit name
         $unit_id = $unit_map[ucwords($unit_name)];
         $sql_query .= " AND unit_ids LIKE '%".$unit_id."%'";
      }
   }

   $sql_query .= " ORDER BY game_date desc LIMIT 10";

   // offset
   if ($_GET["page"]) {
      $sql_query .= " OFFSET ".$_GET["page"]*10;
   } 

   // TODO sanitize these inputs before making this website public...
   $results = $mysqli->query($sql_query);
   if ($results == false) {
      echo ($mysqli->error);
      exit();
   }

   // Create dictionary to map unit ids to unit names and slugs for displaying results
   $unit_query = "
   SELECT unit_id, name, slug
   FROM units";
   $unit_result = $mysqli->query($unit_query);
   if ($unit_result == false) {
      echo ($mysqli->error);
      exit();
   }
   $unit_map = array();
   while ($row = $unit_result->fetch_assoc()) {
      $unit_map[$row["unit_id"]] = array($row["name"], $row["slug"]);
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
               <li><a href="./advanced-search.php">Search Replays</a></li>
               <li><a href="./replay-submit.php">Submit Replays</a></li>
            </ul>
         </div><!-- /.navbar-collapse -->
      </div><!-- /.container-fluid -->
      </nav>

      <div class="container" id="searched-games">
         <h3 class="text-center">Search Results</h3>
         <?php while ($row = $results->fetch_assoc()):
            // create html to display usernames based on who won
            $p1 = $row['p1_name']." (".$row['p1_rating'].")";
            $p2 = $row['p2_name']." (".$row['p2_rating'].")";
            if ($row['result'] == 0) {
               $p1 = "<span style='color:lightgreen'>".$p1.'</span>';
            }
            if ($row['result'] == 1) {
               $p2 = "<span style='color:lightgreen'>".$p2.'</span>';
            }
         ?>
         <a class="game-link" href="game-details.php?id=<?php echo($row['game_id']); ?>"><div class="row game-details">
               <p><u><?php echo($p1); ?><strong> | </strong><?php echo($p2); ?></u></p>
               <?php foreach (explode("|", $row['unit_ids']) as $unit_id) :?>
               <div class="col-md-1 col-xs-2 pris-unit"><img class="img-responsive" src="unit-art/<?php echo($unit_map[$unit_id][1]) ?>.png"/></div>
               <?php endforeach; ?>
         </div></a>
         <?php endwhile; ?>
      </div>
      <div class="text-center">
         <ul class="pagination">
            <?php
               // create previous and next page links
               $prev = 0;
               $next = 1;
               if ($_GET["page"]) {
                  $prev = $_GET["page"] - 1;
                  $next = $_GET["page"] + 1;
               }
               // new page value overwrites old one
               $prevurl = http_build_query(array_merge($_GET, array("page" => $prev)));
               $nexturl = http_build_query(array_merge($_GET, array("page" => $next)));
            ?>
            <li class="page-item"><a class="page-link" href="?<?php echo $prevurl; ?>">Previous</a></li>
            <li class="page-item"><a class="page-link" href="?<?php echo $nexturl; ?>">Next</a></li>
         </ul>
      </div>


      <!-- JS required for Bootstrap -->
      <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
      <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>
   </body>
</html>
