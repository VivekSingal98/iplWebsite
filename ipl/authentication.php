<?php
session_start();
$dbconn = pg_connect("host=localhost dbname=ipl user=viveksingal password=vivisingal")
        or die('Could not connect: ' . pg_last_error());
if ( !isset($_POST['usname'], $_POST['pwd']) ) {
  // Could not get the data that should have been sent.
  die ('Username and/or password does not exist!');
}
$usname=$_POST["usname"];
$pwd = $_POST["pwd"];
$query = "SELECT password FROM login WHERE uname ='$usname'";
$result = pg_query($query) or die('Query failed: ' . pg_last_error());
$row = pg_fetch_all($result);
if($row == FALSE){
    die( "<p>Username does not exist!! <br/> Try Again");
} 
if(password_verify($pwd,$row[0]['password'])){
  $_SESSION['loggedin'] = TRUE;
  $_SESSION['name'] = $_POST['usname'];
  echo 'Welcome ' . $_SESSION['name'] . '!';
}
else{
    echo "<p>Your password is wrong! <br/> Try Again";
}
pg_free_result($result);
pg_close($dbconn);
?>