<?php
	ob_start();
	session_start();

	session_unset();
	
	/*************************************
	
	Login script customized by Michael Francis C. Catague, MECE, MIT
	
	*************************************/

	include_once("conf/ucs.conf.php");

	$username = $_REQUEST['username'];
	$password = md5($_REQUEST['password']);
	$pw = $_REQUEST['password'];
	$b = $_REQUEST['b'];

	if($b=='Login') {
		
	  $sql = "select
	  	  a.userID,
	  	  a.user_lname,
		  a.user_fname,
		  a.user_mname,
		  a.username,
		  a.access,
		  a.companyID,
		  t.name
	  	from
	      admin_access as a,
		  access_type as t
	 	where
	 	  a.username='$username' and 
		  (a.password='$password' or a.password = '$pw') and
		  a.active='1' and
		  a.access=t.id";

		  echo $sql;

	  $query = mysql_query($sql);

	  $r = mysql_fetch_array($query);
	}	

	if(!empty($r)) {
		
	  $_SESSION['user_lname'] = $r[user_lname];
	  $_SESSION['user_fname'] = $r[user_fname];
	  $_SESSION['user_mname'] = $r[user_mname];	  	  
	  $_SESSION['username'] = $r[username];
	  $_SESSION['userID'] = $r[userID];
	  $_SESSION['access'] = $r[name];
	  $_SESSION['access_id'] = $r[access];
	  $_SESSION['companyID'] = $r[companyID];
	  $_SESSION['HKL324lew23Kdafdmliun849IP'] = 'true';	

	  header("location: admin.php?view=home");	  
	  exit;
	}
	else {
	  //die(mysql_error()); // uncomment to debug
	  header("location: index.php?msg=UNABLE TO GRANT ACCESS");
	  exit;
	}
?>