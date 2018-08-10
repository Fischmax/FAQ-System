<?php
include("dbconnect.php");
?>
<html>
<head>
<meta http-equiv="Content-type" content="text/html; charset=utf-8"/>
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
?>


<div class="container">
  <?php
    if(!isset($_SESSION['status'])){
      echo "Bitte loggen Sie sich ein.<script language=\"JavaScript\" type=\"text/javascript\">setTimeout(\"location.href='login.htm'\", 3000);</script>";
    }else if($_SESSION['status']<=2){
      echo "Sie haben nicht die notwendige Berechtigung f&uuml;r diesen Bereich.";    
    }else if($_SESSION['status']==3){
      ?>
      <div class="row">
        <h2><u><center>Neuer Benutzer:</center></u></h2>
        <div class="col-lg-12">
          <form action="addbenutzer.php" method="post">
              <div class="form-inline" required>
                  <label for="Name">Benutzername:</label>
                  <input type="text" name="Name" class="form-control" autofocus></input><span style="color:red" id="meldung1"></span>
              </div>
              <div class="form-inline" required>
                  <label for="pw">Passwort:</label>
                  <input type="password" name="pw" class="form-control"></input><span style="color:red" id="meldung2"></span>
              </div>
              <div class="form-inline" required>
                  <label for="pw2">Zweite Eingabe:</label>
                  <input type="password" name="pw2" class="form-control"></input><span style="color:red" id="meldung3"></span>
              </div>
              <div class="form-inline">
                  <label for="status">Benutzerstatus:</label>
                  <select class="form-control" name="status" id="status">
                      <option value="1" selected>Bediener</option>
                      <option value="2">Verantwortlicher</option>
                      <option value="3">Super-User</option>
                  </select><span style="color:red" id="meldung4"></span>
              </div>
              <button type="button" class="btn btn-profit" onclick="checkData(Name.value, pw.value, pw2.value)">Benutzer hinzuf&uuml;gen</button><span id="meldung"></span>
          </form><br><br>
          </div>
      </div>
      <?php
    } 
  ?>
        
        
</div>
</body>
<script src="js/jquery-3.1.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<?php
include("footer.php");
?>
<script>
function checkData(name, pw, pw2){
  var test = true;
  if(name==""){
    $("#meldung1").html("Der Name darf nicht leer sein!");
    test = false;
  }
  if(pw != pw2){
    $("#meldung3").html("Das Passwort stimmt nicht überein!");
    test = false;
  }
  if(($("#status").val())<0 || ($("#status").val())>3){
    $("#meldung4").html("Bitte wählen Sie den Benutzerstatus nochmals aus oder laden Sie die Seite neu!");
    test = false;
  }
  if(pw==""){
    $("#meldung2").html("Bitte geben Sie ein Passwort ein!");
    test = false;
  }
  if(pw2==""){
    $("#meldung3").html("Bitte geben Sie ein Passwort ein!");
    test = false;
  }
  if(test == true){
    $("#meldung1").html("");
    $("#meldung2").html("");
    $("#meldung3").html("");
    $("#meldung4").html("");
    //Do Shit
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("meldung").innerHTML = this.responseText;
        }
    };
    xmlhttp.open("GET", "addbenutzer.php?Name="+name+"&pw="+pw+"&pw2="+pw2+"&status="+$("#status").val(), true);
    xmlhttp.send();
  }
}
</script>
</html>