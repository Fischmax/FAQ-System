<?php
include("dbconnect.php");
$name = $_GET['Name'];
$pw = $_GET['pw'];
$pw2 = $_GET['pw2'];
$status = $_GET['status'];

session_start();
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
    //Zeit ueberpruefen
    session_unset();      
    session_destroy();
    $_SESSION['LAST_ACTIVITY'] = time(); 
}else{
    if(!isset($_SESSION['status'])){
        echo "<span style=\"color:red\">Bitte loggen Sie sich ein!</span>";
    }else{
        if($_SESSION['status']==3){
            $_SESSION['LAST_ACTIVITY'] = time();   
        
            $name = mysqli_real_escape_string($db, $name);
            mysqli_query($db, "SET NAMES 'utf8'");
            $name = trim($name);
            //Prüft, ob es den Namen bereits gibt.
            if($name == ""){
                echo "<span style=\"color:red\">Der Benutzername darf nicht leer sein!</span>";
            }else if(mysqli_num_rows($db->query("SELECT * FROM Benutzer WHERE Name = '$name'"))>0){
                echo "<span style=\"color:red\">Es gibt bereits einen Benutzer mit diesem Namen!</span>";
            }else{
                if ($pw === $pw2){
                    $hash = hashPW($pw, $name."profit");
                    $insert = "INSERT INTO benutzer (Name, Passwort, Berechtigung) VALUES ('$name', '$hash', '$status')";
                    if ($db->query($insert) === TRUE) {
                        echo "<span style=\"color:darkgreen\">Erfolgreich eingetragen!</span>";
                    } else {
                        echo "<span style=\"color:red\">Der Benutzer konnte nicht erstellt werden.</span>";
                    }
                }else{
                echo "<span style=\"color:red\">Fehler: Passwörter sind ungleich!</span>";
                }    
            }          
            
            
        }else{
            echo "<span style=\"color:red\">Die erforderliche Berechtigung ist nicht vorhanden!</span>";
        }
    }
}

function hashPW($string, $salt, $runs = 1000, $algorithm = 'sha512') {
    $salt = hash($algorithm, $salt);
    // sha512 runs mal benutzen
    while ($runs--) {
        $string = hash($algorithm, $string.$salt);
    }

    return $string;
}
?>