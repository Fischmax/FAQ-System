<html>
<head>
<meta charset="utf-8"/>
<link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="css/profit.css.php">
<link rel="shortcut icon" href="profit.ico">
<title>ProFit Wiki</title>
</head>
<body>
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
include("dbconnect.php");
?>


<div class="container" style="text-align:center;">
    <div class="col-lg-4">
        <?php include("anzeigeprogrammebereiche.php"); ?>
    </div>
    <div class="col-lg-4">
        <h1><u>Suche:</u></h1> <form action="suche.php" method="post">
        <form>
        <div class="form-inline">
            <label for="suchtags">Suchbegriffe:</label>
            <input type="text" class="form-control" name="suchtags" autofocus></label><br><i>Um sich alle alle Einträge anzeigen zu lassen, kann das Feld auch leer bleiben.</i>
        </div><br>
        <div class="form-inline">
            <label for="thema">Thema:</label>
            <select name="thema" class="form-control" id="thema">
                <?php
                $query = "SELECT Name, ID FROM thema";
                $result = mysqli_query($db, $query);
                echo "<option value=\"-1\">Kein Thema</option>";
                while($row = $result->fetch_row()){
                  $row[0] = utf8_encode($row[0]);
                	echo "<option value=\"".$row[1]."\">".$row[0]."</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-inline">
            <label for="programme">Programm:</label>
            <select class="form-control" name="programme" id="programme" style="margin:4px;">
            <?php
            $query = "SELECT Name, ID FROM programme";
            $result = mysqli_query($db, $query);
            echo "<option value=\"0\">Alle</option>";
            while($row = $result->fetch_row()){
            	echo "<option value=\"".$row[1]."\">".$row[0]."</option>";
            }
            ?>
            </select>
        </div>
        <div class="form-inline">
            <label for="bereiche">Bereich:</label>
            <select class="form-control" name="bereiche" id="bereiche">
              <option value="0" selected>Bitte Programm ausw&auml;hlen</option>
            </select><br/>
        </div>
       
        <div class="checkbox">
            <label><input type="checkbox" name="orderranking" value="orderranking">Nach Ranking sortieren</label>
        </div>
            
        <button type="submit" class="btn btn-profit">Suchen</button>
        </form></br></br>
    </div>
    <div class="col-lg-4"></div>
</div>
<script src="js/jquery-3.1.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<?php 
include("footer.php"); 
?>
</body>
<script>
$("select.selectpicker").dropdown();
</script>
<script>
  $("#programme").change(function() {
  	console.log("Skriptanfang: programme.change");
    console.log(this.value);
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("bereiche").innerHTML = this.responseText;
            console.log(this.responseText);
        }
    };
    xmlhttp.open("GET", "getpb.php?bereiche="+this.value, true);
    xmlhttp.send();
    
  });    
</script>
</html>