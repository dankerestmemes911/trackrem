<?php
  // BEGINNING OF SETTINGS
  $mysql_server = "mysql.server.org";
  $mysql_user = "TheNSA";
  $mysql_pass = "ILoveTracking";
  $mysql_db = "TheDatabase";
  // ENDING OF SETTINGS

  $conn = new mysqli($mysql_server, $mysql_user, $mysql_pass, $mysql_db);
    
  $conn->close();
?>
