<?php
session_start();
include("dbconnect.php");
$programm = $_GET['bereiche'];
if($programm === ""){
    exit("Kein Programm an getpb uebergeben!");
}
if(!$db)
{
  exit("Verbindungsfehler: ".mysqli_connect_error());
}

//Unterscheidung Normal/Admin
//Falls Admin, wird der Parameter a mitgeliefert.                          
if(null!==$_GET['a']){
    echo "<option value=\"0\">Keiner</option>";
}else{
    echo "<option value=\"0\">Alle</option>";
}

$such_query = "SELECT ProgrammID, Name, ID FROM bereiche";
$such_result=mysqli_query($db, $such_query);
while($row = $such_result->fetch_row()){
             if($row[0] === $programm){
                echo "<option value=\"".$row[2]."\">".$row[1]."</option>";   
             }
}
?>