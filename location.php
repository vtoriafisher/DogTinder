<?php
// Initialize the session
session_start();
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$locname = $zip = $city = $state = $address = "";
$locname_err = $zip_err = $city_err = $state_err = $address_err = "";
session_start();
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}elseif(isset($_SESSION["username"]) ){
$username = $_SESSION["username"];
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate Location
    if(empty(trim($_POST["locname"]))){
        $locname_err = "Please enter a Location name.";
    } else{
        // Prepare a select statement
        $sql = "SELECT Name FROM `Location` WHERE Name=?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_locname);
            
            // Set parameters
            $param_locname = trim($_POST["locname"]);
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /* store result doesn't matter if it's the same as others in the table */
                mysqli_stmt_store_result($stmt);
                
                   $locname = trim($_POST["locname"]);
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
         
        // Close statement
        mysqli_stmt_close($stmt);
    }
    
	    // Validate Zip
    if(empty(trim($_POST["zip"]))){
        $zip_err = "Please enter a Zip.";
    } else{
        $sql = "SELECT Zip FROM `Location` WHERE Zip=?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, "s", $param_zip);
            
            $param_zip = trim($_POST["zip"]);
            
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                
                   $zip = trim($_POST["zip"]);
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
         
        mysqli_stmt_close($stmt);
    }
	
		    // Validate State
    if(empty(trim($_POST["state"]))){
        $city_err = "Please enter a State.";
    } else{
        $sql = "SELECT State FROM `Location` WHERE State=?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, "s", $param_state);
            
            $param_state = trim($_POST["state"]);
            
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                
                   $state = trim($_POST["state"]);
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
         
        mysqli_stmt_close($stmt);
    }
	
			    // Validate City
    if(empty(trim($_POST["city"]))){
        $city_err = "Please enter a City.";
    } else{
        $sql = "SELECT City FROM `Location` WHERE City=?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, "s", $param_city);
            
            $param_city = trim($_POST["city"]);
            
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                
                   $city = trim($_POST["city"]);
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
         
        mysqli_stmt_close($stmt);
    }
	
	// Validate Address
    if(empty(trim($_POST["address"]))){
        $city_err = "Please enter a Address.";
    } else{
        $sql = "SELECT Address FROM `Location` WHERE Address=?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, "s", $param_address);
            
            $param_address = trim($_POST["address"]);
            
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                
                   $address = trim($_POST["address"]);
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
         
        mysqli_stmt_close($stmt);
    }
	
	// Grab Current Location
	
    
    // Check input errors before inserting in database
    if(empty($locname_err) && empty($zip_err) && empty($city_err) && empty($state_err) && empty($address_err)){
        
        // Prepare an insert statement
        $ski = false;
        $sql = "SELECT Location_ID FROM `Location` WHERE Name=? AND  Zip=?  AND City=?  AND State=?  AND Address=?";
        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, "sssss", $param_locname, $param_zip, $param_city, $param_state, $param_address);
            $param_locname = $locname;
            $param_zip = $zip;
			$param_city = $city;
			$param_state = $state;
            $param_address = $address;
            // Attempt to execute the prepared statement
            if($row = mysqli_stmt_execute($stmt)){
                echo "<p>".$row."</p>";
                mysqli_stmt_store_result($stmt);
                // Check if username exists, if yes then verify password
                if(mysqli_stmt_num_rows($stmt) > 0){ 
                    $ski = true;
                    $query = "SELECT Location_ID FROM Location WHERE Name = '$locname' AND Zip ='$zip' AND STATE='$state' AND City='$city'; ";
                    $result = mysqli_query($link, $query);
                    while($row = mysqli_fetch_row($result)) {
                        $count = count($row);
                        foreach($row as $cell){
                            if($count == 0)
                                break;
                            $location_id = $cell;	
                            $count--;
                        }
                    }
                    mysqli_free_result($result); 
                    $key = "$username" . "$location_id";
                    echo "<p>".$key."</p>";
                    $sql2 = "INSERT INTO `Visited` (`Username`, `Location_ID`, `VisitedKey`) VALUES(?, ?, ?);";
                    if($stmt2 = mysqli_prepare($link, $sql2)){
                            mysqli_stmt_bind_param($stmt2, "sss", $username, $location_id, $key);
                            mysqli_stmt_execute($stmt2);  
                            header("location: location.php");
                    }
                    mysqli_stmt_close($stmt2);
                }
                }
            }
            mysqli_stmt_close($stmt);   
        }
    }
    // Close connection
    mysqli_close($link);
}
?>
 
 <!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=yes">
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <link rel="stylesheet" href="overall.css">
</head>
<body>
    <div id="cent">
        <div id="cent2">
            <h1>Please Insert a Location.</h1>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="form-group <?php echo (!empty($locname_err)) ? 'has-error' : ''; ?>">
                        <label>Location Name</label>
                        <input type="text" name="locname" class="form-control" value="<?php echo $locname; ?>">
                        <span class="help-block"><?php echo $locname_err; ?></span>
                    </div> 
                    <div class="form-group <?php echo (!empty($address_err)) ? 'has-error' : ''; ?>">
                        <label>Address</label>
                        <input type="text" name="address" class="form-control" value="<?php echo $name; ?>">
                        <span class="help-block"><?php echo $address_err; ?></span>
                    </div>    
                    <div class="form-group <?php echo (!empty($city_err)) ? 'has-error' : ''; ?>">
                        <label>City</label>
                        <input type="text" name="city" class="form-control" value="<?php echo $city; ?>">
                        <span class="help-block"><?php echo $city_err; ?></span>
                    </div>
                    <div class="form-group <?php echo (!empty($state_err)) ? 'has-error' : ''; ?>">
                        <label>State</label>
                        <input type="text" name="state" class="form-control" value="<?php echo $state; ?>">
                        <span class="help-block"><?php echo $state_err; ?></span>
                    </div>
                    <div class="form-group <?php echo (!empty($zip_err)) ? 'has-error' : ''; ?>">
                        <label>Zip</label>
                        <input type="text" name="zip" class="form-control" value="<?php echo $zip; ?>">
                        <span class="help-block"><?php echo $zip_err; ?></span>
                    </div>
                                <div class="form-group">
                            <input type="submit" class="btn btn-primary" value="Submit">
                            <input type="reset" class="btn btn-primary" value="Reset">
                        </div>
                </form>
                <p><a href="welcome.php" class="btn btn-primary">HomePage</a><p><p>
        </div>
    </div>
</body>
</html>