<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
	<title>Dogs You Love</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
</head>
<body>
	<center>
	<h1>Dogs You Love:</h1>
	<?php
		include 'config.php';
        $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		if (!$conn) {
			die('Could not connect: ' . mysql_error());
		}
			$User = $_SESSION['username'];
			$query = "SELECT Dog_ID
						FROM Dog_Profile
						WHERE Username = '$User'";
			$result = mysqli_query($conn, $query);
			while($row = mysqli_fetch_row($result)) {
				$count = count($row);
				foreach($row as $cell){
					if($count == 0)
						break;
					$Dog = $cell;	
					$count--;
				}
			}
		$query = "SELECT Loves.LovesDog_ID_2, Dog_Profile.Name, Dog_Profile.Description
					FROM Loves
					INNER JOIN Dog_Profile ON Loves.LovesDog_ID_2 = Dog_Profile.Dog_ID
					WHERE Loves.Dog_ID = '$Dog'";
		$result = mysqli_query($conn, $query);
		echo "<h1>$table</h1>";
		echo '<table class = "loves_table"><tr>';
		echo "<th><b>Dog ID</b></th>";
		echo "<th style='padding-right:10px'><b>Dog Name</b></th>";
		echo "<th style='padding-right:10px'><b>Dog Description</b></th>";
		while($row = mysqli_fetch_row($result)) {
			$parameter = $row[0];	
			echo "<tr class='not-first' onclick=\"location.href='objectPage.php?param=$parameter'\">";
			$count = count($row);
			foreach($row as $cell){
				if($count == 0)
					break;
				echo "<td style='padding-right:10px'>$cell</td>";
				$count--;
			}
			echo "</tr>\n";
		}
		echo "</table>";
	?>
</center>
</body>
</html>