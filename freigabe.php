<?php
include("dbconnect.php");
?>

<html>
<head>
<meta charset="utf-8"/>
<link rel="stylesheet" href="css/bootstrap.min.css" TYPE="text/css"/>
<link rel="stylesheet" href="css/profit.css.php" TYPE="text/css"/>
<link rel="shortcut icon" href="sym.ico">
<title>ProFit Wiki</title>
</head>
<body id="bootstrap-overrides">
<?php
    session_start();
    if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
        //Zeit ueberpruefen
        session_unset();      
        session_destroy();   
    }
    $_SESSION['LAST_ACTIVITY'] = time();
?>
<?php
include("header.php");
?>
<div class="container">
    <?php
    if(isset($_SESSION['status'])){
      if($_SESSION['status']=='3'){
        if(isset($_POST['auswahl'])){
          $auswahl = $_POST['auswahl'];
        }else{
          $auswahl = 1;
        }
        if($auswahl == 1){
          ?><div class="row" style="border:none;">
            <form  method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
              <button type="submit" class="btn btn-profit" name="auswahl" value="1"style="background-color:grey" disabled>Beantrage Löschungen</button>
              <button type="submit" class="btn btn-profit" name="auswahl" value="2">Beantragte Freigaben</button>
            </form>
          </div><?php
        }else{
          ?><div class="row" style="border:none;">
            <form  method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
              <button type="submit" class="btn btn-profit" name="auswahl" value="1">Beantrage Löschungen</button>
              <button type="submit" class="btn btn-profit" name="auswahl" value="2" style="background-color:grey" disabled>Beantragte Freigaben</button>
            </form>
          </div><?php
        }
        mysqli_query($db, "SET NAMES 'utf8'");
        $programmArray = getProgrammArray($db);
        $bereichArray = getBereichArray($db);
        $themaArray = getThemaArray($db);
        $such_query = "SELECT ID, Frage, Antwort, ProgrammID, BereichID, Ranking, ThemaID FROM faq WHERE geloescht = '$auswahl'";
        $such_result=mysqli_query($db, $such_query);
        if($such_result!=null){
        	
            while($row = $such_result->fetch_row()){
          		?>
              
              <div class="row" class="question">
            			<div class="borderclass">
                      		<?php
                            printf ("<div class=\"col-lg-8\">
                              <b>Frage:</b> <textarea class=\"form-control\" name='frage' id=\"frage-%s\">%s</textarea>
                              </div>
                              <div class=\"col-lg-4\">
                              <b>ID:</b> %s <br>
                              <b>Programmname:</b> %s<br>
                              <b>Bereich:</b> %s<br>
                              <b>Thema:</b> <select name=\"thema\" class=\"form-control\" id=\"thema-%s\"> %s</select>
                              <b>Ranking:</b> <span id=\"ranknr-%s\">%s</span>
                              </div>", 
                              $row[0], $row[1], $row[0], $programmArray[$row[3]], $bereichArray[$row[4]], $row[0], getThemaBox($row[6], $themaArray), $row[0], $row[5]
                            );
                    			?>
                    			<div class="col-lg-12" style="padding:1em;">
                    			   <?php printf ("<b>Antwort:</b> <textarea  class=\"form-control\" rows='4' name='antwort' id=\"antwort-%s\">%s</textarea>", $row[0], $row[2]); ?>
                    			</div>
                          <div class="col-lg-4">
                    			<button class="btn btn-profit" type="button" onclick="revokedelete(<?php echo $row[0].", ".$auswahl;?>)"><?php if($auswahl==1){echo "Löschvorschlag ablehnen";}else{echo "Freigabe erteilen";}?></button> 
                          <button class="btn btn-profit" type="button" onclick="deleteID(<?php echo $row[0];?>)">L&ouml;schen</button> 
                          <button class="btn btn-profit" type="button" onclick="getHistorie(<?php echo $row[0];?>)">Historie</button>
                          </div>
                          <div class="col-lg-8">
                            <div id="status-<?php echo $row[0];?>"></div>
                          </div>
                          <div class="col-lg-12">
                            <div class="panel panel-default" id="<?php echo "Historie-".$row[0]; ?>" style="display: none;">
                              <!-- Wird von getHistorie(id) gefuellt -->
                            </div>
                          </div>
                  </div>
              </div>
          		<?php				
          	}
          
        	$such_result->close();
        }else{
        	echo "<br>Keine Einträge zum Löschen vorgemerkt!";
        }
      }else{
        echo "Die notwendige Berechtigung fehlt!";
      }
    }else{
      echo "Bitte loggen Sie sich ein!";
    }
  ?>
</div>
<script src="js/jquery-3.1.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<?php
include("footer.php");
?>
<script>

function getHistorie(id){
      var xmlhttp = new XMLHttpRequest();
      var hist = "Historie-"+id;
      xmlhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
              document.getElementById(hist).innerHTML = this.responseText;
              $("#"+hist).slideToggle("slow");
          }
      };
      xmlhttp.open("GET", "getHistorie.php?id=" + id, true);
      xmlhttp.send();
}

function revokedelete(id, auswahl){
      var xmlhttp = new XMLHttpRequest();
      var stat = "status-"+id;
      var th = "thema-"+id;
      var thema = document.getElementById(th).value;
      xmlhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
              document.getElementById(stat).innerHTML = this.responseText;
          }
      };
      xmlhttp.open("GET", "delete.php?revoke=" + id + "&thema=" + thema, true);
      xmlhttp.send();
}

function deleteID(id){
      var xmlhttp = new XMLHttpRequest();
      var stat = "status-"+id;
      console.log("Loeschen von: " +id);
      xmlhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
              document.getElementById(stat).innerHTML = this.responseText;
          }
      };
      xmlhttp.open("GET", "delete.php?delete=" + id, true);
      xmlhttp.send();
}
</script>



<?php
function getProgrammArray($db){
      if(!$db)
      {
        exit("Verbindungsfehler: ".mysqli_connect_error());
      }
      $result=mysqli_query($db, "SELECT ID, Name FROM programme");
      $programmArray[0]="Keines";
      while($row = $result->fetch_row()){
          $programmArray[$row[0]]=$row[1];
      }
      echo "<script>console.log('getProgrammArray() end')</script>";
      $result->close();
      
      return $programmArray; 
}

function getBereichArray($db){
      if(!$db)
      {
        exit("Verbindungsfehler: ".mysqli_connect_error());
      }
      $result=mysqli_query($db, "SELECT ID, Name FROM bereiche");
      $bereichArray[0]="Keiner";
      while($row = $result->fetch_row()){
          $bereichArray[$row[0]]=$row[1];
      }
      echo "<script>console.log('getBereichArray() end')</script>";
      $result->close();
      return $bereichArray; 
}

function getThemaArray($db){
  if(!$db)
  {
    exit("Verbindungsfehler: ".mysqli_connect_error());
  }
  $result=mysqli_query($db, "SELECT ID, Name FROM thema");
  $themaArray[0]="Kein Thema";
  while($row = $result->fetch_row()){
      $themaArray[$row[0]]=$row[1];
  }
  echo "<script>console.log('getThemaArray() end')</script>";
  $result->close();
  return $themaArray;   
}

function getThemaBox($selected, $themaArray){
  $ret_string = "";
  if($selected == 0){
    $ret_string = "<option value=\"0\">Kein Thema</option>";
  }else{
    $ret_string = "<option value=\"$selected\">".$themaArray[$selected]."</option>";
  }
  foreach($themaArray as $index => $item){
    if($item === $themaArray[$selected]){

    }else{
      $ret_string .= "<option value=\"".$index."\">".$item."</option>";  
    }
  }
  return $ret_string;
}
?>
</body>
</html>