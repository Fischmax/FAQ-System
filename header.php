<header class="navbar navbar-static-top" style="border-bottom-width:medium;border-color:#4A0086;">
  <div class="navbar-inner">
      <div class="container">
          <a class="brand" href="faq_intern.php"><img src="img/profit-logo.png" style="height:50px"></a>
          <div class="navbar-collapse pull-right collapse in" aria-expanded="true">
              
              <ul class="nav navbar-nav navbar-right">
                <li><a href="faq_intern.php">Suche</a></li>
                 
                <li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    Verwaltung
                    <b class="caret"></b>
                  </a>
                  <ul class="dropdown-menu">
                    <?php
                    if(!isset($_SESSION['status'])){
                      
                    }else{
                      if($_SESSION['status']>=1){
                        echo "<li><a href=\"pwchange.php\">Passwort ändern</a></li>";
                        echo "<li><a href=\"adminadd.php\">Frage/Antwort hinzufügen</a></li>";
                      }
                      if($_SESSION['status']>=2){
                        
                      }
                      if($_SESSION['status']==3){
                        echo "<li><a href=\"freigabe.php\">Freigabe/Löschung</a></li>";
                        echo "<li><a href=\"admin.php\">Programme/Bereiche/Themen</a></li>";
                        echo "<li><a href=\"adminbenutzer.php\">Benutzer verwalten</a></li>";
                      }
                    }?>
                  </ul>
                </li>
                <li><a href="<?php if(!isset($_SESSION['id'])){echo "login.htm";}else{echo "logout.php";}?>"><?php if(!isset($_SESSION['id'])){echo "Login";}else{echo "Logout";}?></a></li>
              </ul>
          </div>    
      </div>
  </div>
</header>