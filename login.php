<?php
session_start();
include("dbconnect.php");
?>
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
    
    
    if(!isset($_POST['name']) || !isset($_POST['pw'])){
        echo "<script>console.log(\"ID oder Passwort fehlt!\")</script>";
    }else{
        $name = $_POST['name'];
        echo "<script>console.log(\"".$name."\")</script>";
        $pw = $_POST['pw'];
        echo "<script>console.log(\"Name und Passwort vorhanden.\")</script>";
        
        
        $db->set_charset("utf8");  
        $query = "SELECT Nummer, Name, Passwort, Berechtigung FROM benutzer where Name = '".$name."'";
        $result = mysqli_query($db, $query);
      	$erfolg = false;
        if($result!=null){
      		  while($row = $result->fetch_row()){
                $hash = hashPW($pw, $row[1]."profit");
                if($hash === $row[2]){
                    echo "<script>console.log(\"Passwort ist gleich.\")</script>";
                    $erfolg = true;
                    
                    $_SESSION['id'] = $row[0];
                    $_SESSION['name'] = $row[1];
                    $_SESSION['status'] = $row[3];
                    $_SESSION['LAST_ACTIVITY'] = time();   
                }else{
                    echo "<script>console.log(\"Passwort ist nicht korrekt.\")</script>";
                    $erfolg = false;
                }
            }
        }
    }
?>
<?php
include("header.php");
?>


<div class="container" style="text-align:center;">
    <?php 
        if($erfolg == true){
            echo "Login erfolgreich!";
        }else{
            echo "Login fehlgeschlagen. Ist der Name und das Passwort richtig?";
        }
    ?>    
</div>

<?php
    function hashPW($string, $salt, $runs = 1000, $algorithm = 'sha512') {
        $salt = hash($algorithm, $salt);
        // sha512 runs mal benutzen
        while ($runs--) {
            $string = hash($algorithm, $string.$salt);
        }
    
        return $string;
    }
?>
<script src="js/jquery-3.1.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<?php
include("footer.php");
?>
</body>
</html>