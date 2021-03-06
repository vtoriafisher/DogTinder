<?php
    // Initialize the session
    session_start();
    function gen_uid(){
        return substr(hexdec(uniqid()),0,9);
    }

        if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
        header("location: login.php");
        exit;
    }elseif(isset($_SESSION["username"]) ){
        // Include config file
        $User = $_SESSION["username"];
        require_once "config.php";
    }

?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <link rel="stylesheet" href="overall.css">
</head>
<body>
	<center>
    <div id="cent">
    	<?php
            if (empty($_POST)) {
	    	// display name of dog to swipe on
	        $query = "SELECT Name, Dog_ID FROM Dog_Profile ORDER BY rand() LIMIT 1";
	        $result = mysqli_query($link, $query);
	        if (!$result) {
	            die("Query failed $query");
	        }
	        while($row = mysqli_fetch_row($result)) {
	            $count = count($row);
	            foreach($row as $cell){
	                if($count == 0)
	                    break;
	                echo "<h1>".$cell."</h1>";
	                $Dog_2 = $cell;	
	                $count--;
	            }
	        }
	        mysqli_free_result($result);
	        $query = "SELECT Description FROM Dog_Profile WHERE Dog_ID = '$Dog_2'";
	        $result = mysqli_query($link, $query);
	        while($row = mysqli_fetch_row($result)) {
	            $count = count($row);
	            foreach($row as $cell){
	                if($count == 0)
	                    break;
	                echo "<p>".$cell."</p>";	
	                $count--;
	            }
	        } 
	        mysqli_free_result($result);
            $sqlImg = "SELECT * FROM ImgUpload WHERE Dog_ID='$Dog_2'";
            $resultImg = mysqli_query($link, $sqlImg);
            if(mysqli_num_rows($resultImg) > 0){
                While($rowImg = mysqli_fetch_array($resultImg)){
                    $image = $rowImg['ImgName'];
                    $target_dir = "uploads";
                    chmod("$target_dir/".$image, 0644);
                    echo "<img src='$target_dir/".$image."' style='max-width:400px;max-height:400px;' /><p>";
                }
            }
	        // get ID of OG dog
	        $query = "SELECT Dog_ID FROM Dog_Profile WHERE Username = '$User'";
	        $result = mysqli_query($link, $query);
	        while($row = mysqli_fetch_row($result)) {
	            $count = count($row);
	            foreach($row as $cell){
	                if($count == 0)
	                    break;
	                $Dog_1 = $cell;	
	                $count--;
	            }
	        }
	        mysqli_free_result($result);
	        // creates a key that will be used with the Loves and Hates tables
	        $key = "$Dog_1" . "$Dog_2";

	        // bool that tells you if you can swipe
	        $can_swipe = "T";
	        // if key is in Loves tell the user they already love this dog!
			$query = "SELECT LoveKey
						FROM Loves
						WHERE LoveKey = '$key'";
			$result = mysqli_query($link, $query);
			while($row = mysqli_fetch_row($result)) {
				$count = count($row);
				foreach($row as $cell){
					if($count == 0)
						break;
					else {
						echo "You already love this dog!";	
						$can_swipe = "F";
					}
					$count--;
				}
			}
			// if key is in Hates tell the user they already hate this dog!
			$query = "SELECT HateKey
						FROM Hates
						WHERE HateKey = '$key'";
			$result = mysqli_query($link, $query);
			while($row = mysqli_fetch_row($result)) {
				$count = count($row);
				foreach($row as $cell){
					if($count == 0)
						break;
					else {
						echo "You already hate this dog!";	
						$can_swipe = "F";
					}
					$count--;
				}
			}
                $_SESSION['Dog_1'] = $Dog_1;
                $_SESSION['Dog_2'] = $Dog_2;
                $_SESSION['key'] = $key;
                $_SESSION['can_swipe'] = $can_swipe;
            }
    ?>
    	<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
             <div class="form-group">
			    <p><input type="submit" class="btn btn-primary" value="yes" name="likes"> <input type="submit" class="btn btn-primary" value="no" name="dislikes"><p>
            </div>
            <p>
                <a href="welcome.php" class="btn btn-primary">Return Home</a>
            </p>
        </form>        
    </div>
    <?php
        $Dog_1_fix = $_SESSION['Dog_1'];
        $Dog_2_fix = $_SESSION['Dog_2'];
        $key_fix = $_SESSION['key'];
        $can_swipe_fix = $_SESSION['can_swipe'];
    		if($_SERVER["REQUEST_METHOD"] == "POST"){
                if (isset($_POST['likes'])) {
                    $likes = $_POST['likes'];
                }

                if (isset($_POST['dislikes'])) {
                    $dislikes = $_POST['dislikes'];
                }
		        if(($_POST["likes"])){
		            if ("$can_swipe_fix" == "T") {
                        $sql = "INSERT INTO `Loves` (`Dog_ID`, `LovesDog_ID_2`, `LoveKey`) VALUES(?, ?, ?);";
		                if($stmt = mysqli_prepare($link, $sql)){
		                    // Bind variables to the prepared statement as parameters
		                    mysqli_stmt_bind_param($stmt, "iii", $Dog_1_fix, $Dog_2_fix, $key_fix);
		                    // Set parameters
		                    $param_Dog_1 = $Dog_1_fix;
		                    $param_Dog_2 = $Dog_2_fix;
		                    $param_key = $key_fix;
		                    // Attempt to execute the prepared statement
		                    mysqli_stmt_execute($stmt);
		                }
		            }
		             if(isset($stmt) && is_resource($stmt)){
                        // 4. Release returned data
                        mysqli_free_result($stmt);
                    }
		        }
 
		        if(($_POST["dislikes"])){
		            if ("$can_swipe_fix" == "T") {
                        $sql2 = "INSERT INTO `Hates` (`Dog_ID`, `HatesDog_ID_2`, `HateKey`) VALUES(?, ?, ?);";
		                if($stmt = mysqli_prepare($link, $sql2)){
		                    // Bind variables to the prepared statement as parameters
		                    mysqli_stmt_bind_param($stmt, "iii", $param_Dog_1, $param_Dog_2, $param_key);
		                    // Set parameters
		                    $param_Dog_1 = $Dog_1_fix;
		                    $param_Dog_2 = $Dog_2_fix;
		                    $param_key = $key_fix;
		                    // Attempt to execute the prepared statement
		                    mysqli_stmt_execute($stmt);
		                }
		            }
                    if(isset($stmt) && is_resource($stmt)){
                        // 4. Release returned data
                        mysqli_free_result($stmt);
                    }
		        }
                ?>
            <script type="text/javascript">
                window.location = "swipe.php";
            </script>
        <?php
		    }
		?>
</center>
</body>
</html>
