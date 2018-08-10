<?php
include("dbconnect.php");
?>
<html>
<head>
<meta charset="utf-8"/>
<link rel="stylesheet" href="css/bootstrap.min.css" TYPE="text/css"/>
<link rel="stylesheet" href="css/profit.css.php" TYPE="text/css"/>
<link rel="shortcut icon" href="profit.ico">
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
mysqli_query($db, "SET NAMES 'utf8'");
$programmArray = getProgrammArray($db);
$bereichArray = getBereichArray($db);
$themaArray = getThemaArray($db);
?>

<div class="container">
    
    <center><u><h2>Ergebnisse:</h2></u></center>
    <?php
        $suchtags = isset($_POST['suchtags']) ? $_POST['suchtags'] : '';
        if(!isset($_POST['orderranking'])){
            $ranking = "";
            echo "<script>console.log(\"Ranking nicht aktiv!\")</script>";
        }else{
            $ranking = " ORDER BY Ranking DESC";
            echo "<script>console.log(\"Ranking aktiv!\")</script>";
        }
        $pr = isset($_POST['programme']) ? $_POST['programme'] : 'Alle';
        $prbe = isset($_POST['bereiche']) ? $_POST['bereiche'] : 'Alle';
        $thema = isset($_POST['thema']) ? $_POST['thema'] : '-1';
        if($prbe === "Bitte Programm ausw&auml;hlen"){
            $prbe = "Alle";
            echo "<script>console.log('Vorbereitung:Bereich auf Alle gesetzt.')</script>";   
        }
        //Problematische Sonderzeichen entfernen
        mysqli_real_escape_string($db, $ranking);
        mysqli_real_escape_string($db, $pr);
        mysqli_real_escape_string($db, $prbe);
        mysqli_real_escape_string($db, $thema);
        //Leerzeichen am Anfang und Ende entfernen
        $suchtags = trim($suchtags);
        //String in Wörter zerlegen
        $suchtags = explode(" ",$suchtags);
        
        if($suchtags!=null){ 
      	  if(count($suchtags)>1){
            for($i=0; $i<count($suchtags); $i++){
        		  $suchtags[$i] = "+" . $suchtags[$i];
            }
          } 
        $suchtags = implode(" ", $suchtags);
       // utf8_encode($suchtags); 
        }
        
        //Überprüfen, ob es keinen Suchbegriff gibt. Suchstring entsprechend für die Query anlegen.
        if($suchtags == "+" || $suchtags == "+*" || $suchtags == null){
            $suchstring = "";	
        }else{
            //Boolean Mode
            $suchstring = "AND MATCH(Frage, Antwort) AGAINST('".$suchtags."' IN BOOLEAN MODE)";
            //TODO:LIKE
            
        }
        
        //Überprüfen, ob es kein Thema gibt.
        if($thema === "-1"){
          $themastring = "";
        }else{
          $themastring = " AND ThemaID = '$thema'";
        }
        
        //Query erstellen
                         
        if($pr === "0"){
                  $such_query = "SELECT ID, Frage, Antwort, ProgrammID, BereichID, Ranking, ThemaID  FROM faq WHERE Geloescht = '0'".$themastring."".$suchstring."".$ranking."";
                  echo "<script>console.log('Query-Erstellung:1')</script>";    
        }else{
          if($prbe === "0"){
              $such_query = "SELECT ID, Frage, Antwort, ProgrammID, BereichID, Ranking, ThemaID FROM faq WHERE Geloescht = '0' AND ProgrammID = '".$pr."'".$themastring."".$suchstring."".$ranking."";
                  echo "<script>console.log('Query-Erstellung:2')</script>";         
          }else{
              $such_query = "SELECT ID, Frage, Antwort, ProgrammID, BereichID, Ranking, ThemaID FROM faq WHERE Geloescht = '0' AND ProgrammID = '".$pr."' AND BereichID = '".$prbe."'".$themastring."".$suchstring."".$ranking."";
                  echo "<script>console.log('Query-Erstellung:3')</script>";
          }
        }

        //Einzelne Erzeugung der Fragen/Antworten     
        $such_result=mysqli_query($db, $such_query);
      	if($such_result!=null){
      		while($row = $such_result->fetch_row()){
      			?>
            
            <div class="row" class="question">
          			<div class="borderclass" id="row-<?php echo $row[0];?>">
                		<?php
              			   printf ("<div class=\"col-lg-8\">
                                  <b>Frage:</b> <textarea class=\"form-control\" rows='10' name='frage' id=\"frage-%s\">%s</textarea>
                                </div>
                                <div class=\"col-lg-4\">
                                  <b>ID:</b> %s <br>
                                  <b>Programmname:</b> %s<br>
                                  <b>Bereich:</b> %s<br>
                                  <b>Thema:</b> <select name=\"thema\" class=\"form-control\" id=\"thema-%s\"> %s</select><br>
                                  <b>Ranking:</b> <span id=\"ranknr-%s\">%s</span>
                                </div>", 
                                $row[0], $row[1], $row[0], $programmArray[$row[3]], $bereichArray[$row[4]], $row[0], getThemaBox($row[6], $themaArray), $row[0], $row[5]
                              );
              			?>
              			<div class="col-lg-12" style="padding:1em;">
              			   <?php printf ("<b>Antwort:</b> <textarea  class=\"form-control\" rows='15' name='antwort' id=\"antwort-%s\">%s</textarea>", $row[0], $row[2]); ?>
              			</div>
                    <div class="col-lg-4">
              			<button class="btn btn-profit" type="button" onclick="update(<?php echo $row[0];?>)">Bearbeiten</button> 
                    <button class="btn btn-profit" type="button" onclick="deleteID(<?php echo $row[0];?>)">L&ouml;schvorschlag</button> 
                    <button class="btn btn-profit" type="button" onclick="getHistorie(<?php echo $row[0];?>)">Historie</button>
                    <?php printf("<button type=\"button\" class=\"btn btn-profit-thumb\" onclick=\"rate(".$row[0].", this)\"><span class=\"glyphicon glyphicon-thumbs-up\" id=\"glyph-".$row[0]."\"></span></button>");?>
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
      		echo "<br>Keine Ergebnisse gefunden!";
      	}
        
    echo "<br/><a href=\"faq_intern.php\">Zur&uuml;ck zur Startseite</a>";
        
    ?>
</div>
<script src="js/jquery-3.1.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<?php
include("footer.php");
?>
<script>
function rate(id, btn) {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
            
    };
    var abc = "#glyph-"+id;
    console.log($(abc).css("color"));
    if($(abc).css("color")!="rgb(50, 205, 50)"){
      $(abc).css("color","limegreen");
      xmlhttp.open("GET", "rate.php?id="+id+"&up=y", true);
      var rank="#ranknr-"+id;
      var idn=$(rank).text();
      idn = parseInt(idn);
      idn++;
      $(rank).text(idn);
    }else{
      $(abc).css("color","white");
      xmlhttp.open("GET", "rate.php?id="+id+"&up=n", true);
      var rank="#ranknr-"+id;
      var idn=$(rank).text();
      console.log("ID:"+idn+"down");
      idn = parseInt(idn);
      idn--;          
      $(rank).text(idn);
    }
    console.log("Rated!");
    
    xmlhttp.send();
}

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

function toggleQuestion(id){
  var question = "Frage-"+id;
  $("#"+question).slideToggle("slow");
}

function update(id){
      var xmlhttp = new XMLHttpRequest();
      var stat = "status-"+id;
      console.log("Update von: " +id);
      var fr = "frage-"+id;
      var an = "antwort-"+id;
      var th = "thema-"+id;
      var frage = document.getElementById(fr).value.replace(/\n/g,'<br/>');
      var antwort = document.getElementById(an).value.replace(/\n/g,'<br/>');
      var thema = document.getElementById(th).value;
      xmlhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
              document.getElementById(stat).innerHTML = this.responseText;
          }
      };
      xmlhttp.open("GET", "update.php?update=" + id + "&frage=" + frage + "&antwort=" + antwort + "&thema=" + thema, true);
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
      xmlhttp.open("GET", "update.php?delete=" + id, true);
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