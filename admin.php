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
        mysqli_query($db, "SET NAMES 'utf8'");
        if(!isset($_SESSION['status'])){
            echo "Bitte loggen Sie sich ein.<script language=\"JavaScript\" type=\"text/javascript\">setTimeout(\"location.href='login.htm'\", 3000);</script>";
        }else if($_SESSION['status']<3){
            echo "Sie haben nicht die notwendige Berechtigung f&uuml;r diesen Bereich.";
        }else{
                
         if($_SESSION['status']==3){  ?>
             <div class="row">
               <div class="col-xs-4">
                  <h2><u><center>Neues Programm:</center></u></h2>
                    <div class="form-inline">
                        <label for="programmname">Programm:</label>
                        <input type="text" name="programmname" class="form-control" id="addProgramText"/>
                    </div>
                    <span id="mel_prog_add"></span><br>
                    <button type="button" class="btn btn-profit" onclick="addProgram()">Programm hinzufügen</button>
               </div>

               <div class="col-xs-4">
                 <h2><u><center>Programmname ändern:</center></u></h2>
                 <select class="form-control" name="programmalt" id="prog_alt">
                    <?php
                    $query = "SELECT Name, ID FROM programme";
                    $result = mysqli_query($db, $query);
                    while($row = $result->fetch_row()){
                      echo "<option value=\"".$row[1]."\">".$row[0]."</option>";
                    }
                    ?>
                  </select>
                 <div class="form-inline">
                  <label for="programmnameneu">Neuer Name:</label>
                  <input type="text" class="form-control" name="programmnameneu" id="prog_neu"></input> 
                 </div>
                 <span id="mel_prog_umben"></span><br>
                 <button type="button" class="btn btn-profit" onclick="changeProgram(prog_alt.value, prog_neu.value)">Programm umbenennen</button>
               </div>
               <div class="col-xs-4">
                 
               </div>
             </div>
             <div class="row">
                <div class="col-lg-12">   
                    <h2><u><center>Neuer Bereich:</center></u></h2> 
                    <form action="addbereich.php" method="post">
                      <div class="form-inline">
                          <label for="programm">Programm:</label>
                          <select class="form-control" name="programm" id="prog_liste">
                                          
                          <?php
                          $query = "SELECT Name, ID FROM programme";
                          $result = mysqli_query($db, $query);
                          while($row = $result->fetch_row()){
                          	echo "<option value=\"".$row[1]."\">".$row[0]."</option>";
                          }
                          ?>
                          
                          </select>
                      </div>
                      <div class="form-inline">
                          <label for="bereichname"><center>Bereich:</center></label>
                          <input type="text" name="bereichname" class="form-control"/>
                      </div>
                      <button type="submit" class="btn btn-profit">Bereich hinzuf&uuml;gen</button>
                  </form>
               </div>
              </div>
              <div class="row">
                <div class="col-xs-4">
                  <h2><u><center>Neues Thema:</center></u></h2>
                  <div class="form-inline">
                      <label for="themaname"><center>Thema:</center></label>
                      <input type="text" name="bereichname" class="form-control" id="thema"/><div id="themastat"></div>
                  </div>
                  <button type="button" class="btn btn-profit" onclick="addthema()">Thema hinzuf&uuml;gen</button>  
                </div>
                <div class="col-xs-4">
                  <h2><u><center>Programmname ändern:</center></u></h2>
                  <select class="form-control" name="themaalt" id="thema_alt">
                    <?php
                    $query = "SELECT Name, ID FROM thema";
                    $result = mysqli_query($db, $query);
                    while($row = $result->fetch_row()){
                      echo "<option value=\"".$row[1]."\">".$row[0]."</option>";
                    }
                    ?>
                  </select>
                  <div class="form-inline">
                  <label for="themanameneu">Neuer Name:</label>
                  <input type="text" class="form-control" name="themanameneu" id="thema_neu"></input> 
                  </div>
                  <span id="mel_thema_umben"></span><br>
                  <button type="button" class="btn btn-profit" onclick="changeThema(thema_alt.value, thema_neu.value)">Thema umbenennen</button>
                  </div>
                  <div class="col-xs-4">
                  
                </div>
              </div>
          <?php }
        } ?> 
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
</script>
<script>
  function addProgram(){
    var name = document.getElementById('addProgramText').value;
    if(checkProgramName(name, "#mel_prog_add")){
      console.log("checked!");
      var xmlhttp = new XMLHttpRequest();
      xmlhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
              document.getElementById("mel_prog_add").innerHTML = this.responseText;
          }
      };
      xmlhttp.open("GET", "addprogram.php?programmname=" + name, true);
      xmlhttp.send(); 
    }
  }

  function changeProgram(alt, neu){
    console.log(alt+" "+neu);
    if(checkProgramName(neu, "#mel_prog_umben")){
      var xmlhttp = new XMLHttpRequest();
      xmlhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
              document.getElementById("mel_prog_umben").innerHTML = this.responseText;
              $("#prog_neu").val("");
              $('#prog_alt option').filter(function () { return $(this).val() == alt; }).text(neu);
              $('#prog_liste option').filter(function () { return $(this).val() == alt; }).text(neu);
          }
      };
      xmlhttp.open("GET", "changeprogram.php?alt=" + alt + "&neu=" + neu, true);
      xmlhttp.send(); 
    }
  }

  function checkProgramName(name, ref){
    if(name == ""){
      $(ref).html("<span style='color:red'>Der Name darf nicht leer sein!</span>");
      return false;
    }else{
      $(ref).html("");
      return true;
    }
  }

  function changeThema(alt, neu){
    console.log(alt+" "+neu);
    if(checkThemaName(neu)){
      var xmlhttp = new XMLHttpRequest();
      xmlhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
              document.getElementById("mel_thema_umben").innerHTML = this.responseText;
              $("#thema_neu").val("");
              $('#thema_alt option').filter(function () { return $(this).val() == alt; }).text(neu);
          }
      };
      xmlhttp.open("GET", "changethema.php?alt=" + alt + "&neu=" + neu, true);
      xmlhttp.send(); 
    }  
  }

  function checkThemaName(name){
    if(name == ""){
      $("#mel_thema_umben").html("<span style='color:red'>Der Name darf nicht leer sein!</span>");
      return false;
    }else{
      $("#mel_thema_umben").html("");
      return true;
    }
  }

  function addthema(){
    console.log("Anfang addthema()");
    var thema = document.getElementById('thema').value;
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("themastat").innerHTML = this.responseText;
        }
    };
    xmlhttp.open("GET", "addthema.php?thema="+thema, true);
    xmlhttp.send();  
  }
</script>
</html>
<?php

?>