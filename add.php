<html>
<head>
<meta charset="utf-8"/>
</head>
<body>
<?php
include("dbconnect.php");
function getBreakText($t) {
    return strtr($t, array('\\r\\n' => '<br>', '\\r' => '<br>', '\\n' => '<br>'));
}

$frage = $_POST['frage'];
$antwort = $_POST['antwort'];
$programm = $_POST['programm'];
$bereich = $_POST['bereich'];
$thema = $_POST['thema'];

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
        if($_SESSION['status']>=1){
            $_SESSION['LAST_ACTIVITY'] = time(); 
            
            if(!$db)
            {
              exit("Verbindungsfehler: ".mysqli_connect_error());
            }else{
                //getBreakText($antwort);
                //Sonderzeichen entfernen, nur Zahlen und Buchstaben zulassen
                //$frage = preg_replace('/[^a-zA-Z 0-9 \. \_ üÜöÖäÄß .]/', '', $frage);
                //$antwort = preg_replace('/[^a-zA-Z 0-9 \. \_ üÜöÖäÄß .]/', '', $antwort);
                
                //Leerzeichen am Anfang und Ende entfernen
                $frage = trim($frage);
                $antwort = trim($antwort);
                $frage = mysqli_real_escape_string($db, $frage);
                $antwort = mysqli_real_escape_string($db, $antwort);
                $programm = mysqli_real_escape_string($db, $programm);
                $bereich = mysqli_real_escape_string($db, $bereich);
                $thema = mysqli_real_escape_string($db, $thema);
                mysqli_query($db, "SET NAMES 'utf8'");

                if($_SESSION['status']==2 || $_SESSION['status']==1){
                  $insert = "INSERT INTO faq (Frage, Antwort, ProgrammID, bereichID, geloescht, ThemaID) VALUES ('$frage', '$antwort', '$programm', '$bereich', '2', '$thema')";
                  

                  if ($db->query($insert) === TRUE) {
                    echo "Erfolgreich eingetragen:<br/>Frage:".$frage."</br>Antwort:".$antwort;
                    $frage = utf8_decode($frage);
                    $antwort = utf8_decode($antwort);
                    $id = mysqli_insert_id($db);
                    $date = date('d-m-Y H:i', time());
                    if($thema!="0"){
                        $themaname = $db->query("SELECT name FROM thema WHERE ID = '$thema'");
                        $themaname = $themaname->fetch_row();
                        $themaname = $themaname[0];
                        $themaname = utf8_decode($themaname);
                        $themastring = $date.": Thema auf ".$themaname."(".$thema.") geändert.;";
                    }else{
                        $themastring = "";
                    }
                    if($bereich!="0"){
                        $bereich = $db->query("SELECT name FROM bereiche WHERE ID = '$bereich' AND programmID = '$programm'");
                        $bereich = $bereich->fetch_row();
                        $bereich = $bereich[0];
                        $bereich = utf8_decode($bereich);
                        $bereichstring = $date.": Bereich auf ".$bereich." geändert.;";
                    }else{
                        $bereichstring = "";
                    }
                    if($programm!=0){
                        $programm = $db->query("SELECT name FROM programme WHERE ID = '$programm'");
                        $programm = $programm->fetch_row();
                        $programm = $programm[0];
                        $programm = utf8_decode($programm);
                        $programmstring = $date.": Programm auf ".$programm." geändert.;";    
                    }else{
                        $programmstring = "";
                    }
                    
                    $text = $date.": Frage mit ID ".$id." von ".$_SESSION['name']." erstellt und Freigabe beantragt.;".$date.": Frage auf ".deleteSemi($frage)." geändert.;".$date.": Antwort auf ".deleteSemi($antwort)." geändert.;".$programmstring.$bereichstring.$themastring;
                    $text = utf8_encode($text);
                    echo "<script>console.log(\"".$text."\")</script>";
                    $inserthist = "INSERT INTO Historie (ID, Historie) VALUES ('$id', '$text')";
                    $db->query($inserthist);
                  } else {
                      echo "Error: " . $insert . "<br>" . $db->error;
                  }
                }elseif($_SESSION['status']==3){
                  $insert = "INSERT INTO faq (Frage, Antwort, ProgrammID, bereichID, geloescht, ThemaID) VALUES ('$frage', '$antwort', '$programm', '$bereich', '0', '$thema')";
                                    
                
                	if ($db->query($insert) === TRUE) {
                    echo "Erfolgreich eingetragen:<br/>Frage:".$frage."</br>Antwort:".$antwort;
                    $frage = utf8_decode($frage);
                    $antwort = utf8_decode($antwort);
                    $id = mysqli_insert_id($db);
                    $date = date('d-m-Y H:i', time());
                    if($thema!="0"){
                        $themaname = $db->query("SELECT name FROM thema WHERE ID = '$thema'");
                        $themaname = $themaname->fetch_row();
                        $themaname = $themaname[0];
                        $themaname = utf8_decode($themaname);
                        $themastring = $date.": Thema auf ".$themaname."(".$thema.") geändert.;";
                    }else{
                        $themastring = "";
                    }
                    if($bereich!="0"){
                        $bereich = $db->query("SELECT name FROM bereiche WHERE ID = '$bereich' AND programmID = '$programm'");
                        $bereich = $bereich->fetch_row();
                        $bereich = $bereich[0];
                        $bereich = utf8_decode($bereich);
                        $bereichstring = $date.": Bereich auf ".$bereich." geändert.;";
                    }else{
                        $bereichstring = "";
                    }
                    if($programm!=0){
                        $programm = $db->query("SELECT name FROM programme WHERE ID = '$programm'");
                        $programm = $programm->fetch_row();
                        $programm = $programm[0];
                        $programm = utf8_decode($programm);
                        $programmstring = $date.": Programm auf ".$programm." geändert.;";    
                    }else{
                        $programmstring = "";
                    }
                    $text = $date.": Frage mit ID ".$id." von ".$_SESSION['name']." erstellt und sofort freigegeben.;".$date.": Frage auf ".deleteSemi($frage)." geändert.;".$date.": Antwort auf ".deleteSemi($antwort)." geändert.;".$programmstring.$bereichstring.$themastring;
                    $text = utf8_encode($text);
                    echo "<script>console.log(\"".$text."\")</script>";
                    $inserthist = "INSERT INTO Historie (ID, Historie) VALUES ('$id', '$text')";
                    $db->query($inserthist);
                  } else {
                      echo "Error: " . $insert . "<br>" . $db->error;
                  }
                }
            }
        }
    }
}
echo "<br/><a href=\"faq_intern.php\">Zur&uuml;ck zur Startseite</a>";

//Verhindert, dass die Historie durch unnötige ';' zerstört wird.
function deleteSemi($text){
    return str_replace(";", "", $text);
}
?>
</body>
</html>