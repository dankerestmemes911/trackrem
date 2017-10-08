<?php
  // BEGINNING OF SETTINGS
    $mysql_server = "localhost";
    $mysql_user = "username";
    $mysql_pass = "password";
    $mysql_db = "database";

    $ikwyd_key = "ikwydkey";
    $pinguser = true;
  // ENDING OF SETTINGS

  function txtlog($text) {
    echo "[" . date("h:i:s A", time()) . "] " . $text;
  }

  $conn = new mysqli($mysql_server, $mysql_user, $mysql_pass, $mysql_db);
  $sql1 = "SELECT ip, date, platform, browser FROM trackrem_processrequests";
  $result1 = $conn->query($sql1);
  txtlog("Connected to MySQL server");

  if ($result1->num_rows > 0) {
    while($row = $result1->fetch_assoc()) {
      $result2 = $conn->query("SELECT ip FROM trackrem_iplist WHERE ip = " . $row['ip']);
      if ($result2->num_rows > 0) {
        txtlog($row['ip'] . " is already in database. Continuing to next IP.");
        continue;
      }
      $country_info = json_decode(file_get_contents("http://ip-api.com/json/" . $row['ip']));
      if ($country_info['status'] == "success") {
        txtlog("Found geolocation for " . $row['ip']);
        $data_country = $country_info['countryCode'];
        $data_region = $country_info['region'];
        $data_city = $country_info['city'];
        $data_latitude = $country_info['lat'];
        $data_longitude = $country_info['lon'];
        $data_zipcode = $country_info['zip'];
      } else {
        txtlog("Could not find geolocation for " . $row['ip']);
      }
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
      // FIX PING BY USING API INSTEAD OF EXEC
      if ($pingip = true) {
        txtlog("Trying to ping " . $row['ip']);
        if (var_dump(PHP_OS) = "Linux") {
          exec("host " . $);
        }
      } else {
        txtlog("Pinging is turned off. Won't ping IP.");
      }
    }
  } else {
   die("ERROR: No results found");
  }
?>
