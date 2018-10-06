<head>
<meta charset="utf-8"/>
<link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="css/profit.css.php">
<link rel="shortcut icon" href="sym.ico">
<title>ProFit Wiki</title>
</head>
<body>
<?php 
session_start();
$_SESSION = array();
session_destroy();
include("header.php");
echo "Logout erfolgreich";
?>
<?php
include("footer.php");
?>
</body>
</html>