<?php
  // BEGINNING OF SETTINGS
    $mysql_server = "localhost";
    $mysql_user = "username";
    $mysql_pass = "password";
    $mysql_db = "database";

    $ikwyd_key = "ikwydkey";
  // ENDING OF SETTINGS

  function txtlog($text) {
    echo "[" . date("h:i:s A", time()) . "] " . $text;
  }

  $conn = new mysqli($mysql_server, $mysql_user, $mysql_pass, $mysql_db);
  $sql1 = "SELECT ip, date FROM trackrem_processrequests";
  $result1 = $conn->query($sql1);
  txtlog("Connected to MySQL server");

  if ($result1->num_rows > 0) {
    while($row = $result1->fetch_assoc()) {
      $result2 = $conn->query("SELECT ip FROM trackrem_iplist WHERE ip = " . $row['ip']);
      if ($result2->num_rows > 0) {
        txtlog($row['ip'] . " is already in database. Continuing to next IP.");
        continue;
      }
      $country_info = json_decode(file_get_contents("http://api.ipaddress.com/iptocountry?format=json&ip=" . $row['ip']));
      $data_country = $country_info['country_code'];
      $ikwyd_exists = json_decode(file_get_contents("https://api.antitor.com/history/exist?ip=" . $row['ip'] . "&key=" . $ikwyd_key));
      if ($ikwyd_exists['exists'] = true) {
        txtlog("IKWYD account exists for " . $row['ip'] . ". Getting data from API.");
        $ikwyd_link = "https://api.antitor.com/history/peer?ip=" . $row['ip'] . "&days=30&key=" . $ikwyd_key;
        $ikwyd_array = json_decode(file_get_contents($ikwyd_link));
        $data_hasxxx = $ikwyd_array['hasPorno'];
        $data_hascp = $ikwyd_array['hasChildPorno'];
        txtlog("Adding public torrents of " . $row['ip'] . " the corresponding table");
        foreach($ikwyd_array->contents as $content) {
          $sql3 = "INSERT INTO trackrem_torrentdl (ip, date, downloadedon , name, size, hash, category)
          VALUES ('" . $row['ip'] . "', '" . time() . "', '" . $content['endDate'] . "', '" . $content->torrent['name'] . "', '" . $content->torrent['size'] . "', '" . $content->torrent['hash'] . "', '" . $content['category'] . "')";
          if ($conn->query($sql) === TRUE) {
            txtlog("Data of " . $content->torrent['name'] . " successfully added to database.");
          } else {
            txtlog("Error uploading data of " . $content->torrent['name'] . " to MySQL server. Continuing");
          }
        }
      } else {
        txtlog("IKWYD account does not exist for " . $row['ip'] . ". Continuing.");
      }
    }
  } else {
   die("ERROR: No results found");
  }
?>
