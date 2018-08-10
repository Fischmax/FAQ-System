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
        if($_SESSION['status']==3){
            if (isset($_GET['revoke'])) {
              mysqli_query($db, "SET NAMES 'utf8'");
        			$id = $_GET['revoke'];
              $thema = $_GET['thema'];
        			if(checkCurrentStatus($db, $id)){
                $query = "UPDATE faq
          					SET geloescht='0', ThemaID='$thema'
          					WHERE ID='$id'
          					";
                $idalt = ($db->query("SELECT geloescht FROM faq WHERE ID = '$id'"))->fetch_row();
                $idalt = $idalt[0];
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

                $result=$db->query($query);
                
                //Historie abändern
                $histquery = $db->query("SELECT Historie FROM Historie WHERE ID = '$id'");
                if($histquery && $result){
                  //Frage wurde bearbeitet, an der Historie wurde noch nichts geaendert.
                  $row = $histquery->fetch_row();
                  $hist = $row[0];
                  if(!$hist){
                    if($idalt == 2){
                      $text = $date.": Historie nachgetragen, da nicht vorhanden: Freigabe der Frage mit ID ".$id." erteilt. ;";
                      echo "<span style=\"color:darkgreen;\">Freigabe erfolgreich erteilt!</span><br>";
                    }else{
                      $text = $date.": Historie nachgetragen, da nicht vorhanden: Löschung der Frage mit ID ".$id." abgelehnt. ;";
                      echo "<span style=\"color:darkgreen;\">Löschung erfolgreich abgelehnt!</span><br>";
                    }
                    $text .= $thematext;
                    echo "<script>console.log(\"".$text."\")</script>";
                    $inserthist = "INSERT INTO Historie (ID, Historie) VALUES ('$id', '$text')";
                    if($db->query($inserthist) === TRUE){
                      echo "<span style=\"color:darkgreen;\">Es war keine Historie verf&uuml;gbar, daher wurde eine neue erstellt.</span>";
                    }else{
                      echo "<span style=\"color:red;\">Historie konnte nicht erstellt werden.</span>";
                    }
                  }else{
                    if($idalt == 2){
                      $hist .= $date.": Freigabe erteilt von ".$_SESSION['name'].".;";
                      echo "<span style=\"color:darkgreen;\">Freigabe erfolgreich erteilt!</span><br>";
                    }elseif($idalt==1){
                      $hist .= $date.": Löschung abgelehnt von ".$_SESSION['name'].".;";
                      echo "<span style=\"color:darkgreen;\">Löschung erfolgreich abgelehnt!</span><br>";
                    }
                    $hist .= $thematext;
                    $insert_query = "UPDATE Historie SET Historie='$hist' WHERE ID='$id'";                          
                    $insert_result = $db->query($insert_query);
                    if($insert_result){
                      echo "<span style=\"color:darkgreen;\">Historie abgeändert.</span>";
                    }
                  }
                }else{
                  echo "<span style=\"color:red;\">Fehlgeschlagen!</span>";
                }
              }else{
                echo "<span style=\"color:red;\">Freigabe/Löschung wurde bereits bearbeitet, aktualisieren Sie die Seite!</span>";
              }
            }
          	elseif (isset($_GET['delete'])) {
             	$id = $_GET['delete'];
              if(checkCurrentStatus($db, $id)){
              	if(!$db){
            			echo "<span style=\"color:red;\">Löschen fehlgeschlagen!";
                  exit("Verbindungsfehler: ".mysqli_connect_error());
            		}else{
                    mysqli_query($db, "SET NAMES 'utf8'");
              			$query = "DELETE FROM faq WHERE ID='$id'";
                    $result=$db->query($query);
                    if($result){
                      //Historie löschen
                      $hist_query = $db->query("DELETE FROM Historie WHERE ID = '$id'");
                      if($hist_query){
                        echo "<span style=\"color:darkgreen;\">Komplett gelöscht!</span>";
                      }else{
                        echo "<span style=\"color:red;\">Löschen der Historie fehlgeschlagens!";
                      }
                    }else{
                      echo "<span style=\"color:red;\">Löschen fehlgeschlagen!";
                    }
      		      }
              }else{
                echo "<span style=\"color:red;\">Freigabe/Löschung wurde bereits bearbeitet, aktualisieren Sie die Seite!</span>";
              }
            }
          }
    	}
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

  function checkCurrentStatus($db, $id){
    $statusQuery = $db->query("SELECT geloescht FROM faq where ID = '$id'");
    $row = $statusQuery->fetch_row();
    if($row[0]==0){
      return false;
    }else{
      return true;
    }
  }
?>
</html>