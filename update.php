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
        if($_SESSION['status']>=2){
            if (isset($_GET['update'])) {
                mysqli_query($db, "SET NAMES 'utf8'");
          			$toupdate = trim($_GET['update']);
                //br2nl ist hier notwendig, weil die JS-Funktion auf suche.php die Zeilenumbrüche nur umwandeln kann.
          			$frage=br2nl(trim($_GET['frage']));
          			$antwort=br2nl(trim($_GET['antwort']));
                $thema=$_GET['thema'];
          			$id=trim($_GET['update']);
                $date = date('d-m-Y H:i');

                //TODO:checkFrage/checkAntwort
                $themaHist = checkThema($id, $thema, $db);
                $thematext = "";
                if(!$themaHist){
                  $themaQueryNeu = $db->query("SELECT Name FROM Thema where ID = '$thema'");
                  $row = $themaQueryNeu->fetch_row();
                  $themaname = $row[0];
                  $thematext = $date.": Thema wurde auf ".$themaname."(".$thema.") geändert.;";
                }

                //Trägt die Änderungen in die Datenbank ein.
                $such_query = "UPDATE faq
                    SET Frage='$frage', Antwort='$antwort', ThemaID='$thema'
                    WHERE ID='$id'
                    ";                          
                $such_result=$db->query($such_query);

                $histquery = $db->query("SELECT Historie FROM Historie WHERE ID = '$id'");
                if($histquery && $such_result){
                  //Frage wurde bearbeitet, an der Historie wurde noch nichts geaendert.
                  echo "<span style=\"color:darkgreen;\">Erfolgreich bearbeitet!</span><br>";
                  $row = $histquery->fetch_row();
                  $hist = $row[0];
                  if(!$hist){
                    $text = $date.": Historie nachgetragen, da nicht vorhanden: Frage mit ID ".$id." erstellt.;".$date.": Frage von   auf ".$frage." geändert.;".$date.": Antwort von   auf ".$antwort." geändert.;".$thematext;
                    echo "<script>console.log(\"".$text."\")</script>";
                    $inserthist = "INSERT INTO Historie (ID, Historie) VALUES ('$id', '$text')";
                    if($db->query($inserthist) === TRUE){
                      echo "<span style=\"color:darkgreen;\">Es war keine Historie verf&uuml;gbar, daher wurde eine neue erstellt.</span>";
                    }else{
                      echo "<span style=\"color:red;\">Historie konnte nicht erstellt werden.</span>";
                    }
                  }else{
                    $hist .= $date.": Frage auf ".$frage." geändert.;".$date.": Antwort auf ".$antwort." geändert.;".$thematext;
                    $insert_query = "UPDATE Historie SET Historie='$hist' WHERE ID='$id'";                          
            			  $insert_result = $db->query($insert_query);
                    if($insert_result){
                      echo "<span style=\"color:darkgreen;\">Historie abgeändert.</span>";
                    }
                  }
                  
                }else{
                  echo "<span style=\"color:red;\">Bearbeitung fehlgeschlagen!</span>";
                }
            	}
            	elseif (isset($_GET['delete'])) {
               		if(!$db){
              			echo "<span style=\"color:red;\">L&ouml;schvorschlag fehlgeschlagen!";
                    exit("Verbindungsfehler: ".mysqli_connect_error());
              		}else{
                      mysqli_query($db, "SET NAMES 'utf8'");
                			$todelete = $_GET['delete'];
                			$todelete = trim($todelete);
                			$such_query = "UPDATE faq
                					SET Geloescht='1'
                					WHERE ID='$todelete'
                					";
                      $such_result=$db->query($such_query);
                      if($such_result){
                        //Historie abändern
                        $date = date('d-m-Y H:i');                          
                        $hist = $db->query("SELECT Historie FROM Historie WHERE ID = '$todelete'");
                        $row = $hist->fetch_row();
                        $hist = $row[0];
                        $hist .= $date.": Löschen wurde vorgeschlagen von ".$_SESSION['name']." ;";
                        $hist_query = $db->query("UPDATE Historie SET Historie = '$hist' WHERE ID = '$todelete'");
                			  
                        if($hist_query){
                          echo "<span style=\"color:darkgreen;\">Zum L&ouml;schen vorgeschlagen: ".$todelete."</span>";
                        }else{
                          echo "<span style=\"color:red;\">L&ouml;schvorschlag fehlgeschlagen!";
                        }
                      }else{
                        echo "<span style=\"color:red;\">L&ouml;schvorschlag fehlgeschlagen!";
                      }
                      echo "<br/>".$such_result;
        		      }
              }
          }
    	}
}
function br2nl( $input ) {
   return preg_replace('/<br(\s+)?\/?>/i', "\n", $input);
}

//Vergleicht das aktuelle Thema einer Frage mit dem per GET gelieferten Thema und gibt true zurück, falls sie gleich sind.
function checkThema($id, $thema, $db){
  $themaQueryAlt = $db->query("SELECT ThemaID FROM FAQ WHERE ID = '$id'");
  $row = $themaQueryAlt->fetch_row();
  if($row[0] == $thema){
    return true;
  }else{
    return false;
  }
}
?>
</html>