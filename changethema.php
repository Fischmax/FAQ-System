<?php
include("dbconnect.php");
$alt = $_GET['alt'];
$name = $_GET['neu'];
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
        if($_SESSION['status']==3){
            $name = preg_replace('/[^a-zA-Z 0-9 \. \_ üÜäÄöÖß .]/', '', $name);
            $name = trim($name);
            $name = htmlentities($name);
            if($name == ""){
                echo "<span style=\"color:red\">Der Name darf nicht leer sein!</span>";
            }else if($alt == null || $name == null){
                echo "<span style=\"color:red\">Es ist ein undefinierbarer Fehler aufgetreten.<br>Bitte die Seite erneut laden!</span>";
            }else{
                if(mysqli_num_rows($db->query("SELECT * FROM thema WHERE name = '$name'"))==0){
                    $update = "UPDATE thema SET Name = '$name' WHERE ID = '$alt'";
                    if($db->query($update) === TRUE) {
                        echo "<span style=\"color:green\">Erfolgreich umbenannt in ".$name."</span>";
                    } else {
                        echo "<span style=\"color:red\">Fehlgeschlagen!</span>";
                    }
                } 
            }    
        }
    }
}
?>