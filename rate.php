<?php
session_start();
$db = mysqli_connect("rdbms.strato.de", "U2881227", "7c93md3sh7", "DB2881227");
if(!$db)
{
  exit("Verbindungsfehler: ".mysqli_connect_error());
}
$id = $_GET['id'];
$up = $_GET['up'];
if($id === ""){
    exit("Keine ID an rate uebergeben!");
}

//Neuen Wert erzeugen.
$getquery = "SELECT Ranking FROM faq WHERE ID = '".$id."'";
$wert = mysqli_query($db, $getquery);
$row = $wert->fetch_row();
if($up === "y"){
    $row[0]++;
}else if($up === "n"){
    $row[0]--;
}{
    echo "<script>console.log(\"Ich soll weder hoch- noch runterzaehlen!:(\");</script>";
}//Austauschen
$update_query = "UPDATE faq SET Ranking = '".$row[0]."' WHERE ID = '".$id."'";
if ($db->query($update_query) === TRUE) {
    echo "<script>console.log(\"Rated erfolgreich.\");</script>";
} else {
    echo "<script>console.log(\"Rated fehlgeschlagen:\");</script>".$db->error."";
}

$db->close();
?>