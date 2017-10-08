<html>
  <head>
    <title>TRACKREM - Index</title>
  </head>
  <body>
    <a href="process.php">Process temporary entries</a>
    <a href="admin/temporaryentries.php">See temporary entries</a>
    <table>
      <tr>
        <td>IP Address</td>
        <td>Hostname</td>
      <tr>
      <?php
        // BEGINNING OF SETTINGS
        $mysql_server = "mysql.server.org";
        $mysql_user = "iamthensa";
        $mysql_pass = "StalinDidN0th1ng";
        $mysql_db = "ohshit";
        // ENDING OF SETTINGS
        
        $conn = new mysqli($mysql_server, $mysql_user, $mysql_pass, $mysql_db);
        $sql = "SELECT ip, hostname FROM trackrem_computerinfo";
        $result = $conn->query($sql);
    
        if ($result->numrows > 0 ) {
          while ($row=$results->fetch_assoc()) {
            echo "<tr><td><a href='admin/ipdata.php?id=" . $row['ip'] . "'>" . $row['ip'] . "</a></td><td>" . $row['hostname'] . "</td></tr>";
          }
        } else {
          echo "<tr><td>No input</td><td>No input</td><td>No input</td></tr>";
        }
    
        $conn->close();
      ?>
    </table>
  </body>
</html>
