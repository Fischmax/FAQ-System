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
    }else if($_SESSION['status']>=1){
        ?>
        <div class="row">
          <h2><u><center>Eigenes Passwort ändern:</center></u></h2>
          <div class="col-lg-12">
            <div class="form-inline" required>
                <label for="pw">Altes Passwort:</label>
                <input type="password" name="pwalt" class="form-control" id="pwalt" autofocus></input>
            </div>
            <div class="form-inline" required>
                <label for="pw">Passwort:</label>
                <input type="password" name="pw" class="form-control" id="pw"></input>
            </div>
            <div class="form-inline" required>
                <label for="pw2">Zweite Eingabe:</label>
                <input type="password" name="pw2" class="form-control" id="pw2"></input>
            </div>
            <button type="button" class="btn btn-profit" onclick="setpw();">Passwort ändern</button><span id="stat"></span>
          </div>
        </div>
    <?php } 
  ?>
        
        
</div>
</body>
<script src="js/jquery-3.1.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<?php
include("footer.php");
?>
<script>
  function setpw(){
      var xmlhttp = new XMLHttpRequest();
      var pw = $("#pw").val();
      var pw2 = $("#pw2").val();
      var pwalt = $("#pwalt").val();
      if(pw == pw2){
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("stat").innerHTML = this.responseText;
            }
        }
        ;
        xmlhttp.open("GET", "pwchangebg.php?pw=" + pw + "&pwalt=" + pwalt, true);
        xmlhttp.send();
      }
  }
</script>

</html>