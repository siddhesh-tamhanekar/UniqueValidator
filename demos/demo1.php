<?php
/**
  * Author : Siddhesh Tamhanekar.
  * Email : tamhanekar.siddhesh95@gmail.com
  */

include __DIR__ ."/../src/autoload.php";

use UniqueValidator\UValidator;

if(isset($_POST['submit'])){	
	# form is submitted
	$json = array();
	$uv = new UValidator();
	//sleep(2);
	$initialMem = memory_get_peak_usage();
	$start = microtime(true);
	if(!$uv->validate(__FILE__ ."#demoForm"))
	{
		$json['status'] = "failed";
		$json['errors'] = $uv->getErrors(true);
		$json['postValues'] = $_POST;
		
	}else{
		//echo "success";
		$json['status'] = "success";
	}
	
	$finalMem = memory_get_peak_usage();
	$time_elapsed_secs = 1000*(microtime(true) - $start);
	
	$json['efforts'] =($finalMem - $initialMem)/1024 . " Kbytes and $time_elapsed_secs mili Seconds";
	die(json_encode($json));
	
}

?>

<!doctype html>
<html>
	<head>
	
		<link href="assets/style1.css" rel="stylesheet" />
		<script src="assets/jquery.min.js" ></script>
		<script src="assets/script.js" ></script>
	
	</head>
	
	<body>
		<header>
			<div class="logo">
				<img src="assets/uvalidator.png">
			</div>
		</header>
		<div class="container" >
			<form  novalidate id='demoForm' method="post" action="">
				<div class="col-6">
				
					<div class="form-group">
						<label>Name</label>
						<input required type='text' pattern="^[A-Za-z]+$" name='name' data-label="Your Name"   /> 
					</div>
					
					<div class="form-group">
						<label>Age</label>
						 <input required type='number' min="18" name='age' data-label="age"   /> 
					</div>
					
					<div class="form-group">
						<label>DOB (yyyy/mm/dd)</label>
						<input required type='text' data-uv-date="" data-format="Y/m/d" placeholder="yyyy/mm/dd" name='dob' min='1950/01/01' max="2000/01/01" data-label="Date Of Birth"   />
					</div>
					
					<div class="form-group radio" name="gender"  >
						<label>Gender:</label>
						<input type="radio" name="gender" required value="female">Female
						<input type="radio" name="gender"  value="male">Male
					
					</div>
					
					<div class="form-group" >
						<label> Country </label>
						<select  name="country" required >
							<option value="">Please Select</option>
							<option >India</option>
							<option >U.S.A.</option>
							<option >Russia</option>
							<option >Japan</option>
							<option >China</option>
						</select>
					</div>
					
					<div class="form-group">
						<label>Tell us About Yourself</label>
						<textarea  min="10" style="margin: 10px -20.7656px 10px 0px; width: 548px; height: 142px;" name="description"></textarea>
					</div>
						
				</div>
				
				<div class='col-6'>
					<div class="form-group">
						<label> Favorites Cities </label>
						<select data-label="Favorites Cities" name="fav-cities[]" multiple='multiple' min=1 max=3 >
							<option >L.A.</option>
							<option >New York</option>
							<option >Mumbai</option>
							<option >Delhi</option>
							<option >Menlo Park</option>
						</select>
					</div>
					
					<div class="checkbox form-group">
						<label>Favorite Colors</label>
						<input type="checkbox" min="1" max="3" name='colors[]' value ="blue"> Blue
						<input type="checkbox" name='colors[]' value ="blue"> Green
						<input type="checkbox" name='colors[]' value ="blue"> Red
						<input type="checkbox" name='colors[]' value ="blue"> Pink
					</div>
					
					<div class='form-group' data-uv-group='4' name='friends[]' min="2" pattern='^[A-Za-z]+$' >
						<fieldset>
						<legend> Best Friends (atleast 2 )</legend>
						<input type='text' name='friends[]' />
						<input type='text' name='friends[]' />
						<input type='text' name='friends[]' />
						<input type='text' name='friends[]' />
						</fieldset>
					</div>
					<div class="form-group">
						<label>This textbox  value cannot be changed</label>
						<input type="text" name="readonly"  data-label="Textbox" value="forever" data-uv-readonly>
					</div>
					<div class="form-group">
						<label>The default(pre filled) value is not accepted in below textbox</label>
						<input type="text" name="default_check"  data-label="Textbox" value="default" data-uv-default >
					</div>
				</div>
				<div class="col-12">
				
					<input type='hidden' name='submit'>
					<button type="submit" data-loading="Submitting..." >Submit </button>
				</div>
			</form>
		</div>
	</body>
</html>

	<script>
	if(document.location.hostname !="localhost") {
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-91005956-1', 'auto');
  ga('send', 'pageview');
}
</script>