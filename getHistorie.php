<?php
  include("dbconnect.php");
  $id = $_GET['id'];
  
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
        echo "<script>console.log(\"getHistorie: Rechte sind ok!\");</script>";
        mysqli_query($db, "SET NAMES 'utf8'");
        $id = preg_replace('/\D/', '', $id);
        
        $suche = "SELECT Historie FROM Historie WHERE ID = '$id'";
        @mysqli_query("SET NAMES 'utf8'");
        $such_result=mysqli_query($db, $suche);
  	    if($such_result!=null){
          $row = $such_result->fetch_row();
           echo "<div class=\"panel-heading\">Historie:</div>";
  		     if($row!=null){
            $items = explode(";",$row[0]);
            foreach($items as $item){
              if($item!=""){
                printf("<li class=\"list-group-item\">%s</li>", $item);
              }
            }  
          }else{
              echo "<li class=\"list-group-item\">Keine Historie verf&uuml;gbar!</li>";
          }
          echo "</ul>";
        }else{
          echo "Die erforderliche Berechtigung ist nicht vorhanden!<br>";
          echo "<script>console.log(\"Du hast keine Rechte! ".$_SESSION['status']."\");</script>";
        }
      }
    }
  }
?>