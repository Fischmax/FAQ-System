<?php
session_start();
include("dbconnect.php");
if(!$db)
{
  exit("Verbindungsfehler: ".mysqli_connect_error());
}

$query = "SELECT Name, ID FROM thema";
$result = mysqli_query($db, $query);
echo "<option value=\"0\">Kein Thema</option>";
while($row = $result->fetch_row()){
$row[0] = utf8_encode($row[0]);
	echo "<option value=\"".$row[1]."\">".$row[0]."</option>";
}
?>