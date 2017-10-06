<html>
  <head>
    <title>TRACKREM - Index</title>
  </head>
  <body>
    <table>
      <tr>
        <td>IP Address</td>
        <td>Country</td>
        <td>Flagged as</td>
      <tr>
      <?php
        // BEGINNING OF SETTINGS
        $mysql_server = "mysql.server.org";
        $mysql_user = "iamthensa";
        $mysql_pass = "StalinDidN0th1ng";
        // ENDING OF SETTINGS
    
        function checkflags($row) {
          if ($row['hasxxx'] = "1") {
            $flags = "XXX";
          } elseif ($row['hascp'] = "1") {
            if ($flags) {
              $flags = $flags + "+CP";
            } else {
              $flags = "CP";
            }
          } elseif ($row['isbot']) {
            if ($flags) {
              $flags = $flags + "+BOT";
            } else {
              $flags = "BOT";
            }
          }
        }
        
        $conn = new mysqli($mysql_server, $mysql_user, $mysql_pass);
        $sql = "SELECT ip, isp, country, hasxxx, hascp, isbot, date FROM trackrem_iplist";
        $result = $conn->query($sql);
    
        if ($result->numrows > 0 ) {
          while ($row=$results->fetch_assoc()) {
            echo "<tr><td><a href='ipdata.php?id=" . $row['date'] . "'>" . $row['ip'] . "</a></td><td>" . $row['country'] . "</td><td>" . checkflags($row) . "</td></tr>";
          }
        } else {
          echo "<tr><td>No input</td><td>No input</td><td>No input</td></tr>";
        }
    
        $conn->close();
      ?>
    </table>
  </body>
</html>
