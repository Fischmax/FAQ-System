<?php
include("dbconnect.php");

$name = $_GET['thema'];
session_start();
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
    //Zeit ueberpruefen
    session_unset();      
    session_destroy();
    $_SESSION['LAST_ACTIVITY'] = time(); 
}else{
    if(!isset($_SESSION['status'])){
        echo "<span style=\"color:red;\">Bitte loggen Sie sich ein!</span>";
    }else{
        if($_SESSION['status']>=1){
            $name = preg_replace('/[^a-zA-Z 0-9 \. \_ üÜäÄöÖß .]/', '', $name);
            $name = mysqli_real_escape_string($db, $name);
            //Leerzeichen am Anfang und Ende entfernen
            $name = trim($name);
            mysqli_query($db, "SET NAMES 'utf8'");
            if(checkThema($name, $db)){
                $insert = "INSERT INTO thema (Name) VALUES ('$name')";
              
              	if ($db->query($insert) === TRUE) {
                    echo "<span style=\"color:darkgreen;\">Erfolgreich eingetragen.</span>";
                } else {
                    echo "<span style=\"color:red;\">Fehlgeschlagen!</span>";
                }
            } 
        }
    }
}

function checkThema($name, $db){
    if($name == ""){
        echo "<span style=\"color:red\">Das Feld darf nicht leer sein!</span>";
        return false;
    }elseif(mysqli_num_rows($db->query("SELECT * FROM Thema WHERE Name = '$name'"))>0){
        echo "<span style=\"color:red\">Das Thema gibt es bereits!</span>";
        echo "<script>$('#thema').find('option[text=\"".$name."\"]').val().attr('selected','selected');</script>";
        return false;
    }else{
        return true;
    }
}
?>