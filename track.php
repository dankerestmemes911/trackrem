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

  if ($_GET['platform']) {
    $platform = $_GET['platform'];
  } else {
    $platform = "unknown";
  }

  if ($_GET['parent']) {
    $browser = $_GET['parent'];
  } else {
    $browser = "unknown";
  }

  $conn = new mysqli($mysql_server, $mysql_user, $mysql_pass, $mysql_db);
  if ($conn->connect_error) {
    die("MySQL Error: " . $conn->connect_error);
  }

  $sql = "INSERT INTO trackrem_processrequests (ip, date, platform, browser)
  VALUES ('" . $ip . "', '" . $timestamp . "', '" . $platform . "', '" . $browser . "')";

  if ($conn->query($sql) === TRUE) {
    echo "success";
  } else {
    die("Inserting into database failed");
  }
  
  $conn->close();
?>
