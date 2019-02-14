<!DOCTYPE HTML>
<html>

<head>
  <title>IPL-Home</title>
  <meta name="description" content="website description" />
  <meta name="keywords" content="website keywords, website keywords" />
  <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
  <link rel="stylesheet" type="text/css" href="./style/style.css" title="style" />
</head>

<body>
  <div id="main">
    <div id="header">
      <div id="logo">
        <div id="logo_text">
          <h1><a href="home.php">IPL</a></h1>
        </div>
      </div>
      <div id="menubar">
        <ul id="menu">
          <!-- put class="selected" in the li tag for the selected page - to highlight which page you're on -->
          <li><a href="home.php">Home</a></li>
          <li><a href="teams.php">Teams</a></li>
          <li><a href="players.php">Players</a></li>
          <li><a href="matches.php">Matches</a></li>
          <li><a href="stats.php">Interesting Stuff</a></li>
          <li><a href="login.php">Login</a></li>
          <li class="selected"><a href="player_stats.php">Player Stats</a></li>
        </ul>
      </div>
    </div>
    <div id="site_content">
      <div id="content">
        <h3>Search a player by their name</h3>
        <h4>Search</h4>
        <form method="get" action="player_stats.php" id="search_form">
          <p>
            <input class="search" type="text" name="search_field" placeholder="Enter keywords....." />
            <input name="search" type="image" style="border: 0; margin: 0 0 -9px 5px;" src="style/search.png" alt="Search" title="Search" />
          </p>
        </form>

      <?php
       // Connecting, selecting database
        $dbconn = pg_connect("host=localhost dbname=ipl user=viveksingal password=vivisingal")
        or die('Could not connect: ' . pg_last_error());
        $query='';
        if(isset($_GET["search_field"])) {
           $query="select Player_Name,Player_Id from player where Player_Name ILIKE '%".$_GET['search_field']."%'";
        }
        if($query!='') {
          $result = pg_query($query) or die('Query failed : ' . pg_last_error());
          echo '<form action="player_stats.php" method="get">';
          echo '<select name="playerId" size='.pg_num_rows($result).' >';
          while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
            echo "<option value='".$line['player_id'].$line['player_name']."'>".$line['player_name']."</option>"; 
          }
          echo "</select>";
            echo "<br>";
            echo '<input type="submit" name="player">';
            echo "</form>";
        }
        
        if(isset($_GET['playerId'])) {
          $str=$_GET['playerId'];
          $i=0;
          for (; $i < strlen($str); $i++){
              if(!is_numeric($str[$i])) { 
                break;
              }
          }
          $player_id=substr($str,0,$i);
          $player_name=substr($str,$i);
          $result = pg_query("Select season,sum(runs_scored) as runs from ball where striker=".$player_id."group by season") or die('Query failed : ' . pg_last_error());
          echo "Player Name: ".$player_name;
          echo "<table>\n";
          echo "<tr><th>Year</th><th>Total Runs</th></tr>";
          $total_sum=0;
          while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
            $total_sum+=$line['runs'];
            echo "\t<tr>\n";
            foreach ($line as $col_value) {
              echo "\t\t<td>$col_value</td>\n";
            }
          }
          echo "<tr><td>All</td><td>$total_sum</td></tr>";
          echo "\t</tr>\n";
          echo "</table>\n";
        }
     
      ?>
      </div>
    </div>
    <div id="footer">
    Made by Chinmaya Singh, Vivek Singal and Adarsh Agarwal 
    </div>
  </div>
</body>
</html>


