<html>
<head>
<meta charset="utf-8"/>
</head>
<?php
include("dbconnect.php");

session_start();
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
    //Zeit ueberpruefen
    session_destroy();
    echo "<span style=\"color:red;\">Die Session ist abgelaufen. Bitte loggen Sie sich erneut ein.</span>";
    //$_SESSION['LAST_ACTIVITY'] = time(); 
}else{
  	if(!isset($_SESSION['status'])){
        echo "<span style=\"color:red;\">Bitte loggen Sie sich ein!</span>";
    }else{
        mysqli_query($db, "SET NAMES 'utf8'");
        $pwalt = $_GET['pwalt'];
        $pw = $_GET['pw'];
        $name = $_SESSION['name'];
        $hash = hashPW($pw, $name."profit");
        $row = $db->query("SELECT Passwort From Benutzer WHERE Name = '$name'")->fetch_row();
        if(hashPW($pwalt, $name."profit") == $row[0]){
          $insert = "UPDATE benutzer SET Passwort = '$hash' WHERE Name = '$name'";
          if ($db->query($insert) === TRUE) {
            echo "<span style=\"color:darkgreen;\">Passwort erfolgreich geändert.</span>";
          } else {
              echo "<span style=\"color:red;\">Es ist ein Datenbankfehler aufgetreten.</span>";
          }  
        }else{
          echo "<span style=\"color:red;\">Das alte Passwort stimmt nicht!</span>";
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