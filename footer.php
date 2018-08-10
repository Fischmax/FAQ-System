<footer class="navbar navbar-fixed-bottom" style="border-top-width:medium;border-color:#4A0086;">
  <div class="navbar-inner">
      <div class="container" style="margin-top:5px;margin-bottom:5px;">
          <?php if(isset($_SESSION['name'])){echo "<div class=\"col-xs-8\">Sie sind gerade eingeloggt als ".$_SESSION['name']."(".getStatus().").       Ihre Session läuft noch <span id=\"timer\">".getTime()."</span> <span id=\"min\">Minuten</span>!</div><div class=\"col-xs-4\" style=\"text-align:right;\">Version ".getVersion()."</div>";} ?>
      </div>
  </div>
</footer>
<?php
  function getStatus(){
    if($_SESSION['status']==1){
      return "Bediener";
    }elseif($_SESSION['status']==2){
      return "Verantwortlicher";
    }
    elseif($_SESSION['status']==3){
      return "Superuser";
    }
    getVersion();
  }

  function getTime(){
    return (time() - $_SESSION['LAST_ACTIVITY']+1800/60);
  }

  function getVersion(){
    $handle = fopen("changelog.txt", "r");
    if ($handle) {
        $line = fgets($handle);
        fclose($handle);
        return $line;
    } else {
        return "nicht lesbar. Fehler beim Öffnen der Datei.";
    } 
  }
?>
<script>
  setInterval(function() {
      var mins = $("#timer").html();
      mins = parseInt(mins);
      if(mins>0){
        mins--;
        if(mins==1){
          $("#min").html("Minute");
        }else{
          $("#min").html("Minuten");
        }
      }
      $("#timer").html(mins);
  }, 60 * 1000);
</script>