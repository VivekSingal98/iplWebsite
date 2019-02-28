<?php include 'func2.php'; ?>
<!DOCTYPE HTML>
<html>

<head>
  <title>Stats</title>
  <meta name="description" content="website description" />
  <meta name="keywords" content="website keywords, website keywords" />
  <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
  <link rel="stylesheet" type="text/css" href="style/style.css" title="style" />
</head>

<body>
  <div id="main">
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
          <li class = "selected"><a href="stats.php">Interesting Stats</a></li>
          <li><a href="player_stats.php">Player Stats</a></li>
          <li><a href="login.php">Login</a></li>
        </ul>
      </div>
      <div id="menubar">
        <ul id="menu">
          <li><a href="stats.php?type2=bat">Batting</a></li>
          <li><a href="stats.php?type2=bowl">Bowling</a></li>
        </ul>
      </div>
      <?php
      if($_GET["type2"]=='bat')
      {
      echo "<div id='menubar2'>";
        echo "<ul id='menu'>";
          echo "<li><a href='stats.php?type2=bat&type=MostRuns'>Most Runs</a></li>";
          echo "<li><a href='stats.php?type2=bat&type=MostHS'>Highest Score</a></li>";
          echo "<li><a href='stats.php?type2=bat&type=MostSR'>Highest Strike Rates</a></li>";
          echo "<li><a href='stats.php?type2=bat&type=MostAVG'>Highest Average</a></li>";
          echo "<li><a href='stats.php?type2=bat&type=MostFours'>Most Fours</a></li>";
          echo "<li><a href='stats.php?type2=bat&type=MostSixes'>Most Sixes</a></li>";
          echo "<li><a href='stats.php?type2=bat&type=MostFifties'>Most Fifties</a></li>";
          echo "<li><a href='stats.php?type2=bat&type=MostCenturies'>Most Centuries</a></li>";
          echo "<li><a href='stats.php?type2=bat&type=FastestFifties'>Fastest Fifties</a></li>";
          echo "<li><a href='stats.php?type2=bat&type=FastestCenturies'>Fastest Centuries</a></li>";
        echo "</ul>";
      echo "</div>";
      }
      elseif($_GET["type2"]=='bowl')
      {
      echo "<div id='menubar2'>";
        echo "<ul id='menu'>";
          echo "<li><a href='stats.php?type2=bowl&type=MostWickets'>Most Wickets</a></li>";
          echo "<li><a href='stats.php?type2=bowl&type=MostMaidens'>Most Maidens</a></li>";
          echo "<li><a href='stats.php?type2=bowl&type=MostDotBalls'>Most DotBalls</a></li>";
          //echo "<li><a href='stats.php?type2=bowl&type=LeastRuns'>Least RunsGiven</a></li>";
          echo "<li><a href='stats.php?type2=bowl&type=Average'>Average</a></li>";
          echo "<li><a href='stats.php?type2=bowl&type=EconomyRate'>Economy Rate</a></li>";
          
        echo "</ul>";
      echo "</div>";
      }
      ?>
      
    <div id="site_content2">
    <div id="content">
      <?php

        $common_query="select player_name as Player,total_matches as Matches,total_runs as Total_Runs,highest_score as Highest_Score,
                  Round(average_runs,2) as AVG_Runs,total_balls_faced as Balls_Faced,Round(total_runs*100.0/total_balls_faced,2) as Strike_Rate, num_100 as Centuries,num_50 as Fifties,total_num4 as Fours,total_num6 as Sixes
                  from (
                  select player_name,striker,sum(runs_per_match) as total_runs,sum(balls_faced_permatch) as total_balls_faced,sum(num4_permatch) as total_num4,
                  sum(num6_permatch) as total_num6, max(runs_per_match) as highest_score, AVG(runs_per_match) as average_runs,count(runs_per_match) 
                  filter(where runs_per_match>=50 and runs_per_match<100) as num_50, count(runs_per_match) filter(where runs_per_match>=100) as num_100
                  from (select player_name,ball.striker,sum(runs_scored) as runs_per_match,count(ball_id) as balls_faced_permatch,
                  count(ball_id) filter(where runs_scored=4) as num4_permatch,
                  count(ball_id) filter(where runs_scored=6) as num6_permatch from ball join player on (ball.striker=player.player_id) 
                  group by match_id,ball.striker,player.player_name) as x
                  group by striker,player_name ) as table1
                  join 
                  (select player_id,count(distinct match_id) as total_matches from player_match group by player_id) as table2
                  on striker=player_id where total_runs>=200";
        function mostRuns() {
          $query1= $GLOBALS['common_query'] . " order by total_runs DESC limit 100";
          $queryArray=array($query1);
          createTable($queryArray);
        }
        function mostHS() {
          $query1= $GLOBALS['common_query'] . " order by Highest_Score DESC limit 100";
          $queryArray=array($query1);
          createTable($queryArray); 
        }
        function mostSR() {
          $query1= $GLOBALS['common_query'] . " order by Strike_Rate DESC limit 100";
          $queryArray=array($query1);
          createTable($queryArray);
        }
        function mostAVG(){
          $query1= $GLOBALS['common_query'] . " order by average_runs DESC limit 100";
          $queryArray=array($query1);
          createTable($queryArray); 
        }
        function mostFours() {
          $query1=$GLOBALS['common_query'] . " order by Fours DESC limit 100";
          $queryArray=array($query1);
          createTable($queryArray);
        }
        function mostSixes() {
          $query1=$GLOBALS['common_query'] . " order by Sixes DESC limit 100";
          $queryArray=array($query1);
          createTable($queryArray);
        }
        function mostFifties() {
          $query1=$GLOBALS['common_query'] . " order by Fifties DESC limit 100";
          $queryArray=array($query1);
          createTable($queryArray); 
        }
        function mostCenturies() {
          $query1=$GLOBALS['common_query'] . " order by Centuries DESC limit 100";
          $queryArray=array($query1);
          createTable($queryArray); 
        }


        function fastestFifties() {
           $q_dropView="drop view if exists ball_cumm_runs2;
            drop view if exists ball_cumm_runs1;
            drop view if exists ball_cumm_runs;";
            insertQuery($q_dropView);
          $q1="create view ball_cumm_runs as 
                  select match_id,innings_no,striker,over_id,ball_id,count(ball_id) filter(where extra_type not in ('wides')) over w
                  as num_balls,count(ball_id) filter(where runs_scored=6) over w as num_6s,
                  count(ball_id) filter(where runs_scored=4) over w as num_4s,
                  sum(runs_scored) over w as cumm_runs 
                  from ball where innings_no<=2
                  window w as (partition by match_id,striker order by over_id,ball_id)";
          $q2=  "create view ball_cumm_runs1 as
                  select match_id,innings_no,player.player_name,min(num_balls) as balls_faced,max(num_6s) as num_6s,max(num_4s) as num_4s,max(cumm_runs) as runs
                  from ball_cumm_runs join player on ball_cumm_runs.striker=player.player_id
                  where cumm_runs>=50
                  group by match_id,striker,player.player_name,innings_no
                  order by Balls_Faced
                  limit 100";
          $q3=  "create view ball_cumm_runs2 as
                  select ball_cumm_runs1.match_id,player_name,team_bowling as team_id,venue_id,match_date,balls_faced,num_6s,num_4s,runs
                  from ball_cumm_runs1 join team_match on ball_cumm_runs1.match_id=team_match.match_id and ball_cumm_runs1.innings_no=team_match.innings_no
                  join match on ball_cumm_runs1.match_id=match.match_id";
            insertQuery($q1);
            insertQuery($q2);
            insertQuery($q3);
            $query="select player_name,team_name as Against,venue_name as Venue,match_date,balls_faced,num_6s,num_4s,runs
                  from ball_cumm_runs2 join team on ball_cumm_runs2.team_id=team.team_id
                  join venue on ball_cumm_runs2.venue_id=venue.venue_id
                  order by balls_faced,runs desc,num_6s desc,num_4s desc";
            $queryArray=array($query);
            createTable($queryArray);  
            insertQuery($q_dropView);
          }

          function fastestCentury() {
           $q_dropView="drop view if exists ball_cumm_runs2;
            drop view if exists ball_cumm_runs1;
            drop view if exists ball_cumm_runs;";
            insertQuery($q_dropView);
          $q1="create view ball_cumm_runs as 
                  select match_id,innings_no,striker,over_id,ball_id,count(ball_id) filter(where extra_type not in ('wides')) over w
                  as num_balls,count(ball_id) filter(where runs_scored=6) over w as num_6s,
                  count(ball_id) filter(where runs_scored=4) over w as num_4s,
                  sum(runs_scored) over w as cumm_runs 
                  from ball where innings_no<=2
                  window w as (partition by match_id,striker order by over_id,ball_id)";
          $q2=  "create view ball_cumm_runs1 as
                  select match_id,innings_no,player.player_name,min(num_balls) as balls_faced,max(num_6s) as num_6s,max(num_4s) as num_4s,max(cumm_runs) as runs
                  from ball_cumm_runs join player on ball_cumm_runs.striker=player.player_id
                  where cumm_runs>=100
                  group by match_id,striker,player.player_name,innings_no
                  order by Balls_Faced
                  limit 100";
          $q3=  "create view ball_cumm_runs2 as
                  select ball_cumm_runs1.match_id,player_name,team_bowling as team_id,venue_id,match_date,balls_faced,num_6s,num_4s,runs
                  from ball_cumm_runs1 join team_match on ball_cumm_runs1.match_id=team_match.match_id and ball_cumm_runs1.innings_no=team_match.innings_no
                  join match on ball_cumm_runs1.match_id=match.match_id";
            insertQuery($q1);
            insertQuery($q2);
            insertQuery($q3);
            $query="select player_name,team_name as Against,venue_name as Venue,match_date,balls_faced,num_6s,num_4s,runs
                  from ball_cumm_runs2 join team on ball_cumm_runs2.team_id=team.team_id
                  join venue on ball_cumm_runs2.venue_id=venue.venue_id
                  order by balls_faced,runs desc,num_6s desc,num_4s desc";
            $queryArray=array($query);
            createTable($queryArray); 
            insertQuery($q_dropView);   
          }


         
        if(isset($_GET["type2"])) {
          if(isset($_GET["type"]) and $_GET["type2"]=='bat'){
            switch($_GET["type"]) {
              case 'MostRuns':
                mostRuns();
                break;
              case 'MostHS':
                mostHS();
                break;
              case 'MostSR':
                mostSR();
                break;
              case 'MostAVG':
                mostAVG();
                break;
              case 'MostFours':
                mostFours();
                break; 
              case 'MostSixes':
                mostSixes();
                break;
              case 'MostFifties':
                mostFifties();
                break;
              case 'MostCenturies':
                mostCenturies();
                break;
              case 'FastestFifties':
                fastestFifties();
                break;
              case 'FastestCenturies':
                fastestCentury();
                break;
            }
          } 
          elseif(isset($_GET["type"]) and $_GET["type2"]=='bowl'){
            include 'stats_bowl_q.php';

            $dbconn = pg_connect("host=localhost port=5432 dbname=ipl user=viveksingal password=vivisingal") or die('Could not connect: ' . pg_last_error());
            $result = pg_query($qb10) or die('Query failed: ' . pg_last_error());
            $result = pg_query($qb0) or die('Query failed: ' . pg_last_error());
            $result = pg_query($qb1) or die('Query failed: ' . pg_last_error());
            $result = pg_query($qb2) or die('Query failed: ' . pg_last_error());
            $result = pg_query($qb3) or die('Query failed: ' . pg_last_error());
            $result = pg_query($qb4) or die('Query failed: ' . pg_last_error());
            $result = pg_query($qb5) or die('Query failed: ' . pg_last_error());
            $result = pg_query($qb6) or die('Query failed: ' . pg_last_error());
            $result = pg_query($qb7) or die('Query failed: ' . pg_last_error());
            $result = pg_query($qb8) or die('Query failed: ' . pg_last_error());
            pg_close($dbconn);

            switch($_GET["type"]) {
              case 'MostWickets':
                $qf = $qb9 . ' order by nwicket desc nulls last limit 50';
                $queryArray=array($qf);
                createTable($queryArray);
                break;
              case 'MostMaidens':
                $qf = $qb9 . ' order by nmaiden desc nulls last limit 50';
                $queryArray=array($qf);
                createTable($queryArray);
                break;
              case 'MostDotBalls':
                $qf = $qb9 . ' order by n0 desc nulls last limit 50';
                $queryArray=array($qf);
                createTable($queryArray);
                break;

              /*case 'LeastRuns':
                $qf = $q10 . ' order by nruns limit 50';
                $queryArray=array($qf);
                createTable($queryArray);*/
              case 'Average':
                $qf = $qb9 . ' order by avg limit 50';
                $queryArray=array($qf);
                createTable($queryArray);
                break;

              case 'EconomyRate':
                $qf = $qb9 . ' order by econ desc  nulls last limit 50';
                $queryArray=array($qf);
                createTable($queryArray);
                break;

            } 
            $dbconn = u76("host=localhost port=5432 dbname=ipl user=viveksingal password=vivisingal") or die('Could not connect: ' . pg_last_error());
            $result = pg_query($qb10) or die('Query failed: ' . pg_last_error());
            pg_close($dbconn);
          }
        }
        
      ?>
    </div>
    <div id="footer">
      Made by Chinmaya Singh, Vivek Singal and Adarsh Agarwal
    </div>
  </div>
  </div>

</body>
</html>
