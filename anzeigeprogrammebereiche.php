<?php
    //Wird per include() eingefügt, daher spare ich mir weitere Aufrufe am Anfang.


    $query = "SELECT Name, ID FROM programme";
    $result = mysqli_query($db, $query);
    echo "<div style=\"float:left\">Hier sieht man alle Programme und die dazugehörigen Bereiche:<br>";
    while($row = $result->fetch_row()){
    	echo "<b style=\"float:left\">".$row[0]."</b><br>";
    	//Lässt sich noch optimieren.
    	getBereich($db, $row[1]);
    };
    echo "</div>";

    function getBereich($db, $id){
    	$such_query = "SELECT ProgrammID, Name, ID FROM bereiche";
		$such_result = mysqli_query($db, $such_query);
		while($row = $such_result->fetch_row()){
			if($row[0] === $id){
			    echo "<span style=\"float:left\">- ".$row[1]."</span><br>";   
			}
		}
    }
?>