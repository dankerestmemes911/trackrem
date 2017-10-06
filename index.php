<html>
  <head>
    <title>TRACKREM - Index</title>
  </head>
  <body>
    <?php
        // BEGINNING OF SETTINGS
        $mysql_server = "mysql.server.org";
        $mysql_user = "iamthensa";
        $mysql_pass = "StalinDidN0th1ng";
        // ENDING OF SETTINGS
    
        $conn = new mysqli($mysql_server, $mysql_user, $mysql_pass);
        $sql = "SELECT ip, isp, country, hasxxx, hascp FROM trackrem_iplist";
        $result = $conn->query($sql);
    
        $conn->close();
    ?>
  </body>
</html>
