<?php
include("dbconnect.php");
function getBreakText($t) {
    return strtr($t, array('\\r\\n' => '<br>', '\\r' => '<br>', '\\n' => '<br>'));
}

$bereich = $_POST['programmID'];
$name = $_POST['bereichname'];
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
        if($_SESSION['status']==2){
            $name = preg_replace('/[^a-zA-Z 0-9 \. \_  ÄäÖöÜüß .]/', '', $name);
            $name = mysqli_real_escape_string($db, $name);
            $name = trim($name);
            $db->set_charset("utf8");
          	$insert = "INSERT INTO bereiche (name, programmID) VALUES ('$name', '$bereich')";
          
          	if ($db->query($insert) === TRUE) {
                echo "Erfolgreich eingetragen:<br/>Bereich:".$name."";
            } else {
                echo "Error: " . $insert . "<br>" . $db->error;
            }
            
        }
    }
}
echo "<br/><a href=\"faq_intern.php\">Zur&uuml;ck zur Startseite</a>";
?>