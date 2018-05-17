<?php
   require ("database_connect.php");
   $game_id = $_GET["id"];
   // Get game information
   $sql_query = "
   SELECT p1_rating, p2_rating, unit_ids, replay_code, p1_info.name as p1_name, p2_info.name as p2_name, result
   FROM games
   JOIN users as p1_info on games.p1_id = p1_info.user_id
   JOIN users as p2_info on games.p2_id = p2_info.user_id
   WHERE game_id = ".$game_id;
   $results = $mysqli->query($sql_query);
   if ($results == false) {
      echo ($mysqli->error);
      exit();
   }

   // Create dictionary from units table
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

   $row = $results->fetch_assoc();

   $match_result;
   // Convert database match result code to the string we want to display
   switch($row["result"]) {
      case (0):
         $match_result = "Player 1 Win";
         break;
      case (1):
         $match_result = "Player 2 Win";
         break;
      case (2):
         $match_result = "Draw";
         break;
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

      <div id="game-details-main">
         <div class="row">
            <div class="col-xs-6">Player 1: <?php echo $row["p1_name"]." (".$row["p1_rating"].")" ?></div>
            <div class="col-xs-6">Player 2: <?php echo $row["p2_name"]." (".$row["p2_rating"].")" ?></div>
         </div>
         <p>Match Result: <?php echo $match_result ?></p>
         <h4>Units</h4>
         <div class="row">
            <?php foreach(explode("|", $row["unit_ids"]) as $unit_id) :?>
            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 infopanel"><img class="img-responsive center-block" src="infopanels/<?php echo $unit_map[$unit_id][0] ?>.png"/></div>
            <?php endforeach; ?>
         </div>
         <p class="text-center">Replay code: <?php echo $row["replay_code"]; ?></p>
         <div class="text-center"><a class="btn btn-primary text-center" target="_blank" href="http://play.prismata.net/?r=<?php echo $row["replay_code"]; ?>">View Replay</a></div>
      </div>



      <!-- JS required for Bootstrap -->
      <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
      <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>
   </body>
</html>
