<?php
  // BEGINNING OF SETTINGS
    $mysql_server = "localhost";
    $mysql_user = "username";
    $mysql_pass = "password";
    $mysql_db = "database";

    $ikwyd_key = "ikwydkey";
  // ENDING OF SETTINGS

  $conn = new mysqli($mysql_server, $mysql_user, $mysql_pass, $mysql_db);
  $sql1 = "SELECT ip, date FROM trackrem_processrequests";
  $result1 = $conn->query($sql1);

  if ($result1->num_rows > 0) {
    while($row = $result1->fetch_assoc()) {
      $sql2 = "SELECT ip FROM trackrem_iplist WHERE ip = " . $row['ip'];
      $result2 = $conn->query($sql2);
      if ($result2->num_rows > 0) {
        continue;
      }
      $ikwyd_link = "https://api.antitor.com/history/peer?ip=" . $row['ip'] . "&days=30&key=" . $ikwyd_key;
      $ikwyd_array = json_decode(file_get_contents($ikwyd_link));
    }
  } else {
   die("noresults");
  }
?>
