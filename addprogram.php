<?php
include("dbconnect.php");
$name = $_GET['programmname'];
session_start();
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
    //Zeit ueberpruefen
    session_unset();      
    session_destroy();
    $_SESSION['LAST_ACTIVITY'] = time(); 
}else{
    if(!isset($_SESSION['status'])){
        echo "Bitte loggen Sie sich ein!";
    }else{
        if($_SESSION['status']>=2){
            $name = preg_replace('/[^a-zA-Z 0-9 \. \_ üÜäÄöÖß .]/', '', $name);
            $name = trim($name);
            $name = htmlentities($name);
            if(mysqli_num_rows($db->query("SELECT * FROM programme WHERE name = '$name'"))==0){
                $insert = "INSERT INTO programme (name) VALUES ('$name')";
                if($db->query($insert) === TRUE) {
                    echo "<span style=\"color:green\">Erfolgreich eingetragen: ".$name."</span>";
                } else {
                    echo "<span style=\"color:red\">Fehlgeschlagen!</span>";
                }
            }       
        }
    }
}
?>