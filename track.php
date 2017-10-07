<?php
  // BEGINNING OF SETTINGS
    $mysql_server = "localhost";
    $mysql_user = "username";
    $mysql_pass = "password";
    $mysql_db = "database";
  // ENDING OF SETTINGS

  if ($_GET['ip']) {
    $ip = $_GET["ip"];
  } else {
    die("Necessary input not given");
  }

  $timestamp = time();

  $conn = new mysqli($mysql_server, $mysql_user, $mysql_pass, $mysql_db);
  if ($conn->connect_error) {
    die("MySQL Error: " . $conn->connect_error);
  }

  $sql = "INSERT INTO trackrem_processrequests (ip, date)
  VALUES ('" . $ip . "', '" . $timestamp . "')";

  if ($conn->query($sql) === TRUE) {
    echo "success";
  } else {
    die("Inserting into database failed");
  }
  
  $conn->close();
?>
