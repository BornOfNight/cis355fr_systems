<?php 
/* ---------------------------------------------------------------------------
 * filename    : fr_event_update.php
 * author      : George Corser, gcorser@gmail.com
 * modified    : Jeremy Stuart
 * description : This program updates an event (table: fr_events)
 * ---------------------------------------------------------------------------
 */
session_start();
if(!isset($_SESSION["fr_person_id"])){ // if "user" not set,
	session_destroy();
	header('Location: login.php');     // go to login page
	exit;
}

require 'database.php';
require 'functions.php';

$id = $_GET['id'];
error_reporting(0);
if ( !empty($_POST)) { // if $_POST filled then process the form

	# initialize/validate (same as file: fr_event_create.php)

	// initialize user input validation variables
	$dateError = null;
	$timeError = null;
	$locationError = null;
	$descriptionError = null;
    $pictureError = null;
	
	// initialize $_POST variables
	$date =  htmlspecialchars($_POST['event_date']);
	$time =  htmlspecialchars($_POST['event_time']);
	$location =  htmlspecialchars($_POST['event_location']);
	$description =  htmlspecialchars($_POST['event_description']);	
	$picture = $_POST['picture'];
	// initialize $_FILES variables
    $fileName = $_FILES['userfile']['name'];
    $tmpName  = $_FILES['userfile']['tmp_name'];
    $fileSize = $_FILES['userfile']['size'];
    $fileType = $_FILES['userfile']['type'];
    $content = file_get_contents($tmpName);
    

	$valid = true;
		/** validate user input
	if (empty($date)) {
		$dateError = 'Please enter Date';
		$valid = false;
	}
	if (empty($time)) {
		$timeError = 'Please enter Time';
		$valid = false;
	} 		
	if (empty($location)) {
		$locationError = 'Please enter Location';
		$valid = false;
	}		
	if (empty($description)) {
		$descriptionError = 'Please enter Description';
		$valid = false;
	}
	**/
	

	if ($valid) { // if valid user input update the database
	   if($fileSize > 0) { // if file was updated, update all fields


			$pdo = Database::connect();
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$sql = "UPDATE fr_events  set event_date = ?, event_time = ?, event_location = ?, event_description = ?, filename = ?, filesize = ?, filetype = ?, filecontent = ? WHERE id = ?";
			$q = $pdo->prepare($sql);
			$q->execute(array($date,$time,$location,$description,$fileName,$fileSize,$fileType,$content,$id));
			Database::disconnect();
			header("Location: fr_events.php");
		}
		else { // otherwise, update all fields EXCEPT file fields
			$pdo = Database::connect();
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$sql = "UPDATE fr_events  set event_date = ?, event_time = ?, event_location = ?, event_description = ? WHERE id = ?";
			$q = $pdo->prepare($sql);
			$q->execute(array($date,$time,$location,$description, $id));
			Database::disconnect();
			header("Location: fr_events.php");
		}
	}
} else { // if $_POST NOT filled then pre-populate the form
	$pdo = Database::connect();
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql = "SELECT * FROM fr_events where id = ?";
	$q = $pdo->prepare($sql);
	$q->execute(array($id));
	$data = $q->fetch(PDO::FETCH_ASSOC);
	$date = $data['event_date'];
	$time = $data['event_time'];
	$location = $data['event_location'];
	$description = $data['event_description'];
	Database::disconnect();
}



?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <link   href="css/bootstrap.min.css" rel="stylesheet">
    <script src="js/bootstrap.min.js"></script>
	<link rel="icon" href="cardinal_logo.png" type="image/png" />
</head>

<body>
    <div class="container">
		<?php 
			//gets logo
			functions::logoDisplay();
		?>	
		<div class="span10 offset1">
		
			<div class="row">
				<h3>Update Shift Details</h3>
			</div>
	
			<form class="form-horizontal" action="fr_event_update.php?id=<?php echo $id?>" method="post"  enctype="multipart/form-data">
			
				<div class="control-group <?php echo !empty($dateError)?'error':'';?>">
					<label class="control-label">Date</label>
					<div class="controls">
						<input name="event_date" type="date"  placeholder="Date" value="<?php echo !empty($date)?$date:'';?>">
						<?php if (!empty($dateError)): ?>
							<span class="help-inline"><?php echo $dateError;?></span>
						<?php endif; ?>
					</div>
				</div>
			  
				<div class="control-group <?php echo !empty($timeError)?'error':'';?>">
					<label class="control-label">Time</label>
					<div class="controls">
						<input name="event_time" type="time" placeholder="Time" value="<?php echo !empty($time)?$time:'';?>">
						<?php if (!empty($timeError)): ?>
							<span class="help-inline"><?php echo $timeError;?></span>
						<?php endif;?>
					</div>
				</div>
				
				<div class="control-group <?php echo !empty($locationError)?'error':'';?>">
					<label class="control-label">Location</label>
					<div class="controls">
						<input name="event_location" type="text" placeholder="Location" value="<?php echo !empty($location)?htmlspecialchars($location):'';?>">
						<?php if (!empty($locationError)): ?>
							<span class="help-inline"><?php echo $locationError;?></span>
						<?php endif;?>
					</div>
				</div>
				
				<div class="control-group <?php echo !empty($descriptionError)?'error':'';?>">
					<label class="control-label">Description</label>
					<div class="controls">
						<input name="event_description" type="text" placeholder="Description" value="<?php echo !empty($description)?htmlspecialchars($description):'';?>">
						<?php if (!empty($descriptionError)): ?>
							<span class="help-inline"><?php echo $descriptionError;?></span>
						<?php endif;?>
					</div>
				</div>
				
				<div class="control-group <?php echo !empty($pictureError)?'error':'';?>">
					<label class="control-label">Picture</label>
					<div class="controls">
						<input type="hidden" name="MAX_FILE_SIZE" value="16000000">
						<input name="userfile" type="file" id="userfile">
					</div>
				</div>



				<div class="form-actions">
					<button type="submit" class="btn btn-success">Update</button>
					<a class="btn" href="fr_events.php">Back</a>
				</div>
				
			</form>
			
			<div class='control-group col-md-6'>
				<div class="controls ">
					<?php 
					if ($data['filesize'] > 0) 
						echo '<img  height=5%; width=15%; src="data:image/jpeg;base64,' . 
							base64_encode( $data['filecontent'] ) . '" />'; 
					else 
						echo 'No photo on file.';
					?><!-- converts to base 64 due to the need to read the binary files code and display img -->
					</div>
			</div>
			
		</div><!-- end div: class="span10 offset1" -->
				
    </div> <!-- end div: class="container" -->
</body>
</html>