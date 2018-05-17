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

        <div id="search-main">
           <!-- Search Form -->
           <form action="search-results.php" method="get">
              <div class="form-group">
                 <label for="player1">Player:</label>
                 <input type="text" class="form-control" id="player1" name="player1"/>
              </div>
              <div class="form-group">
                 <label for="player2">Player:</label>
                 <input type="text" class="form-control" id="player2" name="player2"/>
              </div>
              <div class="form-group">
                 <label for="min-rating">Minimum Rating:</label>
                 <input type="text" class="form-control" id="min-rating" name="min-rating"/>
                 <label><input type="checkbox" id="apply-both" name="apply-both"/>Apply min rating to both players</label>
              </div>
              <div class="form-group">
                 <label for="match-result">Match Result:</label>
                 <select class="form-control" id="match-result" name="match-result">
                    <option></option>
                    <option value="0">Player 1 Win</option>
                    <option value="1">Player 2 Win</option>
                    <option value="2">Draw</option>
                 </select>
              </div>
              <div class="form-group">
                 <label for="units">Include Units:</label>
                 <textarea class="form-control" id="units" name="units" placeholder="Please type one unit per line"></textarea>
              </div>
              <button type="submit" class="btn btn-default">Submit</button>
           </form>
        </div>

        <!-- JS required for Bootstrap -->
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>
     </body>
  </html>
