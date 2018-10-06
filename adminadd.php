<?php
include("dbconnect.php");
?>
<html>
<head>
<meta http-equiv="Content-type" content="text/html; charset=utf-8"/>
<link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="css/profit.css.php">
<link rel="shortcut icon" href="sym.ico">
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
?>


<div class="container">
  <?php
    if(!isset($_SESSION['status'])){
        echo "Bitte loggen Sie sich ein.<script language=\"JavaScript\" type=\"text/javascript\">setTimeout(\"location.href='login.htm'\", 3000);</script>";
    }else if($_SESSION['status']>=1){ ?>
        <div class="row">
          <h2><u><center>Neue Frage/Antwort:</center></u></h2> 
          <div class="col-lg-12">
            <form action="add.php" method="post">
                <div class="form-inline">
                    <label for="thema">Thema:</label>
                    <select name="thema" class="form-control" id="thema">
                        <?php
                            $query = "SELECT Name, ID FROM thema";
                            $result = mysqli_query($db, $query);
                            echo "<option value=\"0\">Kein Thema</option>";
                            while($row = $result->fetch_row()){
                              $row[0] = utf8_encode($row[0]);
                                echo "<option value=\"".$row[1]."\">".$row[0]."</option>";
                            }
                        ?>
                    </select>
                    <label for="themaneu">... oder ein neues Thema hinzufügen: </label>
                    <textarea type="text" name="themaneu" id="themaneu" class="form-control" rows="1"></textarea>
                    <button class="btn btn-profit" type="button" onclick="addThema()">Thema hinzufügen</button>   <span id="themameldung"></span><span>
                </div>
                <div class="form-inline">
                    <label for="frage">Frage:</label>
                    <textarea type="text" name="frage" class="form-control" rows="10" style="width:75%"></textarea>
                </div>
                <div class="form-inline">
                    <label for="antwort">Antwort:</label>
                    <textarea name="antwort" rows="15" class="form-control" style="width:75%"></textarea>
                </div>
                <div class="form-inline">
                    <label for="programm">Programm:</label>
                    <select class="form-control" name="programm" id="programm">
                    <?php
                        $query = "SELECT Name, ID FROM programme";
                        $result = mysqli_query($db, $query);
                        echo "<option value=\"0\">Kein Programm</option>";
                        while($row = $result->fetch_row()){
                        	echo "<option value=\"".$row[1]."\">".$row[0]."</option>";
                        }
                    ?>
                    </select>
                </div>
                <div class="form-inline">
                    <label for="bereich">Bereich:</label>
                    <select class="form-control" id="bereich" name="bereich">
                        <option selected>Keiner</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-profit">
                    Hinzuf&uuml;gen
                </button>
            </form>
          </div>
        </div>
    <?php } ?>
    </div>
</body>
<script src="js/jquery-3.1.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<?php
include("footer.php");
?>
<script>
$("select.selectpicker").dropdown();
</script>
<script>
  $("#programm").change(function() {
  	console.log("Skriptanfang(Admin): programme.change");
    console.log(this.value);
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("bereich").innerHTML = this.responseText;
            console.log(this.responseText);
        }
    };
    xmlhttp.open("GET", "getpb.php?a=true&bereiche="+this.value, true);
    xmlhttp.send();
    
  });

  function addThema(){
    var themaname=document.getElementById("themaneu").value;
    console.log("Erstelle neues Thema mit Name " + themaname);
    //Ruft addthema.php auf und erstellt ein neues Thema.
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("themameldung").innerHTML = this.responseText;
            console.log(this.responseText);
            //Optionbox erneuern
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("thema").innerHTML = this.responseText;
                    console.log(this.responseText);
                    //Option auf das neue Thema setzen
                    $("#thema option:last-child").attr('selected','selected');
                }
            };
            xmlhttp.open("GET", "getth.php", true);
            xmlhttp.send();
        }
    };
    xmlhttp.open("GET", "addthema.php?thema="+themaname, true);
    xmlhttp.send();
    //Feld leeren
    document.getElementById("themaneu").value="";
  }    
</script>
</html>