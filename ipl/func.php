<?php
  $dbconn = pg_connect("host=localhost dbname=ipl user=viveksingal password=vivisingal") or die('Could not connect: ' . pg_last_error());
  function getOutputOfQuery($query) {
    $result = pg_query($query) or die('Query failed: ' . pg_last_error());
    $row = pg_fetch_all($result);
    return $row;
  }
  function insertQuery($query) {
    pg_query($query) or die('Oops!! Something is wrong - ' . pg_last_error());
  }
  function getRowFromQuery($query) {
    $result = pg_query($query) or die('Query failed: ' . pg_last_error());
    $single_row;
    while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
      $single_row=$line;
    }
    return $single_row;
  }
  function createTable($stringArray) {
    $allColumns=array();
    echo "<table>
          <tr><th>S.No</th>";

    foreach ($stringArray as $query ) {
      $result = pg_query($query) or die('Query failed: ' . pg_last_error());
      $lines=pg_fetch_all($result);
      foreach($lines[0] as $key=>$value) {
        echo "<th>$key</th>";
      }
      array_push($allColumns,$lines);
    }
    echo "</tr>";
    $noRows=count($allColumns[0]);
    for($i=0;$i<$noRows;$i++) {
      $serialNo=$i+1;
      echo "<tr><td>$serialNo</td>";
      foreach($allColumns as $lines) {
        foreach($lines[$i] as $key=>$value) {
          echo "<td>$value</td>";
        }
      }
      echo "</tr>";
    }
    echo "</table>";
  }
?>