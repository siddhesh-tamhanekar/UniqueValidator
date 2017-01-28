<?php

/**
 * demo3.php 
 * this demo illustrates how to use advanced features of UValidator like custom function callback * and setting  custom error messages.
 * Author : Siddhesh Tamhanekar.
 * Email : tamhanekar.siddhesh95@gmail.com
 */

 
 

include __DIR__ ."/../src/autoload.php";

use UniqueValidator\UValidator;

$name = "";
$email = "";
$successmsg = "";

function check_name($attrs) 
{
	if($attrs['user_value'] == "UValidator")
		return "you cannot use name `UValidator`(message from custom Callback)";
	else
		return 1;
}

if(isset($_POST['submit'])){	
	# form is submitted
	
	# prepare custom message array.
	$customMessage = array();
	$customMessage['validator'] = array();
	$customMessage['field'] = array();
	$customMessage['validator']['required'] = "%label% is mandatory(custom Error messege validator level)";
	$customMessage['field']['required']['email'] = "Email field is required(field level custom error message.)";
	
	$uv = new UValidator();
	$uv->setCallbackForField("name","check_name");

	# set the name and email variable user don't need to fill the values again.
	$name = $_POST['name'];
	$email = $_POST['email'];
	
	# run validation.
	if(!$uv->validate(__FILE__ ."#formId", $customMessage)) {
		# get the errors 		
		$errors = $uv->getErrors(true);
	
	} else {
		$successmsg = "Your form is submitted successfully";
	}
}
 
?>

<html>
	<head>
	
		<link href="assets/style1.css" rel="stylesheet" />
	
	</head>
	
	<body>
		<header>
			<div class="logo">
				<img src="assets/uvalidator.png">
			</div>
		</header>
		<div class="container" style="width:400px;margin:auto">
		
			<div class="info">enter UValidator in name field to ustom callback testing </div>
			<?php if(isset($errors)): ?>
				<?php foreach($errors as $error): ?>
					<div class=" alert"><?= $error ?> </div>
				<?php endforeach; ?>
			<?php endif; ?>
			<?php if($successmsg): ?>
				<div class="success"><?= $successmsg ?></div>
			<?php endif; ?>
			<form novalidate id="formId" action="" method="post" >
				<div class="col-12">
					<div class="form-group">
						<label>Name</label>

						<input required type='text' pattern="^[a-zA-Z]+$" name='name' value="<?= $name ?>" /> 
					</div>

					<div class="form-group">
						<label>Email</label>
						<input required type='email'  name='email'  value="<?= $email ?>" /> 
					</div>
				
					<input type='hidden' name='submit'>
					<button type="submit"  >Submit </button>
				</div>
			</form>
		</div>
	</body>
</html>