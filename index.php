<?php
	ob_start();
	session_start();

	session_unset();

	/*
	if ($_SERVER['SERVER_PORT'] != 443) {
		header("Location: https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
	}
	*/

	if($_SESSION['HKL324lew23Kdafdmliun849IP']=='true') header("location: admin.php?view=home");
	
	//header("location: admin.php?view=home");

	include_once("conf/ucs.conf.php");

	$msg = $_REQUEST[msg];

?>
<html>

<head>

<title><?php echo $title;?></title>
<link href="images/logo_icon.png" rel="SHORTCUT ICON" >
<link rel="stylesheet" type="text/css" href="css/style.css" />
<script type="text/javascript" src="scripts/jquery.js"></script>
<script type="text/javascript" src="scripts/jquery-ui-1.8.15.custom.js"></script>
<link rel="stylesheet" type="text/css" href="scripts/jquery-ui-1.8.18.custom.css">

<style type="text/css">

input[type="text"], input[type="password"]{
	padding:2px 5px;	
	margin-bottom:10px;
	font-family:Arial, Helvetica, sans-serif;
}
label, .copyright{
	color:#FFF;	
}
.buttons{
	padding:3px;	
}

</style>

<script type = "text/javascript">

function show() {
    $(document).ready(function() { 
   	 $('#myDiv').fadeIn(2500).delay(1000).fadeOut(1500);
    });
}

</script>

</head>

<body onLoad="show();document.loginform.username.focus()" style="background-color: #42a4f4;">
<div id="myDiv" style="
		display:none;
		z-index:1000;
		position:fixed;">


	<img id="myImage" src = "images/construction_unified.png" width="400" height="175" style="position:relative;">
</div><br>

<div>
  <br>
	
  	<form name=loginform method=post action="login.php"><br>
    <div style="width:120px; height:10px; margin:0px auto;"></div><br>
    <div style="width:275px;border:5px solid #C0C0C0;padding:20px; margin:0px auto; border-radius:10px; -moz-border-radius:10px; -webkit-border-radius:10px;">
		<div style="font-size:10px;text-align:left;">
			<label for="username">Username:</label>
			<input type='text' id='username' name='username' class='textbox'>
				
			<label for="password">Password:</label>
			<input type=password id='password' name='password' class='textbox'>
				
			<?php 
				if(!empty($msg)) echo '<div id="status_update" class="ui-state-error ui-corner-all" style="padding: 0px 0.7em; text-align:left; margin-top:10px;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right:.3em;"></span> '.$msg.'</p></div>'; 
			?> 
			<p><input type=submit name=b value="Login" class="buttons"></p>
		</div>
	</div>
  	</form>
  	<div class="copyright">Copyright &copy; <?php echo "2011 - ".date(Y).'<br><p>'.$title;?></p>Powered by<br>Catague Information Systems Solutions (CISS)<p><a href="http://www.cataguesystems.com">WWW.CATAGUESYSTEMS.COM</a></p></div>
</div>

</body>

</html>