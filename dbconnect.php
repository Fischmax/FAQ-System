<?php
$db = mysqli_connect("rdbms.strato.de", "U2881227", "7c93md3sh7", "DB2881227");
//$db = mysqli_connect("localhost", "root", "", "DB2881227");
if(!$db)
{
  exit("Verbindungsfehler: ".mysqli_connect_error());
}
?>