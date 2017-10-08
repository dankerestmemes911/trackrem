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
  $sql1 = "SELECT ip, date, platform, browser, hostname FROM trackrem_processrequests";
  $result1 = $conn->query($sql1);
  txtlog("Connected to MySQL server");

  if ($result1->num_rows > 0) {
    while($row = $result1->fetch_assoc()) {
      $result2 = $conn->query("SELECT ip FROM trackrem_iplist WHERE ip = " . $row['ip']);
      if ($result2->num_rows > 0) {
        txtlog($row['ip'] . " is already in database. Continuing to next IP.");
        continue;
      }
      txtlog("New ip being checked: " . $row['ip']);
      $country_info = json_decode(file_get_contents("http://ip-api.com/json/" . $row['ip']));
      if ($country_info['status'] == "success") {
        txtlog("Found geolocation for " . $row['ip']);
        $data_geoexists = "true";
        $data_country = $country_info['countryCode'];
        $data_region = $country_info['region'];
        $data_city = $country_info['city'];
        $data_latitude = $country_info['lat'];
        $data_longitude = $country_info['lon'];
        $data_zipcode = $country_info['zip'];
      } else {
        txtlog("Could not find geolocation for " . $row['ip']);
        $data_geoexists = "false";
      }
      $ikwyd_exists = json_decode(file_get_contents("https://api.antitor.com/history/exist?ip=" . $row['ip'] . "&key=" . $ikwyd_key));
      if ($ikwyd_exists['exists'] = true) {
        txtlog("IKWYD account exists for " . $row['ip'] . ". Getting data from API.");
        $data_ikwydexists = "true";
        $ikwyd_link = "https://api.antitor.com/history/peer?ip=" . $row['ip'] . "&days=30&key=" . $ikwyd_key;
        $ikwyd_array = json_decode(file_get_contents($ikwyd_link));
        $data_hasxxx = $ikwyd_array['hasPorno'];
        txtlog("Adding public torrents of " . $row['ip'] . " the corresponding table");
        foreach($ikwyd_array->contents as $content) {
          $sql3 = "INSERT INTO trackrem_torrentdl (ip, date, downloadedon, name, size, hash, category)
          VALUES ('" . $row['ip'] . "', '" . time() . "', '" . $content['endDate'] . "', '" . $content->torrent['name'] . "', '" . $content->torrent['size'] . "', '" . $content->torrent['hash'] . "', '" . $content['category'] . "')";
          if ($conn->query($sql) === TRUE) {
            txtlog("Data of " . $content->torrent['name'] . " successfully added to database.");
          } else {
            txtlog("Error uploading data of " . $content->torrent['name'] . " to MySQL server. Continuing");
          }
          $sql3 = "";
        }
      } else {
        txtlog("IKWYD account does not exist for " . $row['ip'] . ". Continuing.");
        $data_ikwydexists = "false";
      }
      txtlog("Inserting found data into database");
      $sql4 = "INSERT INTO trackrem_geolocation (ip, date, country, region, city, lat, lon, zip)
      VALUES ('" . $row['ip'] . "', '" . $time() . "', '" . $data_country . "', '" . $data_region . "', '" . $data_city . "', '" . $data_latitude . "', '" . $data_longitude . "', '" . $data_zipcode . "')";
      $sql4 .= "INSERT INTO trackrem_computerinfo (ip, date, browser, platform, hostname, ikwydexists, geoexists)
      VALUES ('" . $row['ip'] . "', '" . time() . "', '" . $row['browser'] . "', '" . $row['platform'] . "', '" . $row['hostname'] . "', '" . $data_ikwydexists . "', '" . $data_geoexists . "')";
      if ($conn->multi_query($sql4) === TRUE) {
        txtlog("Sucessfully inserted data, continuing.");
      } else {
        die("ERROR: Could not insert data into database");
      }
    }
    txtlog("All IPs have been processed, flushing temporary entries.");
    $sql5 = "DELETE FROM trackrem_processrequests";
    if (mysqli_query($conn, $sql)) {
      txtlog("Flushed table");
    } else {
      die("ERROR: Flushing temporary entries failed. Contact an admin to resolve this manually.");
    }
  } else {
   die("ERROR: No results found");
  }
?>
