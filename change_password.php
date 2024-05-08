<?php

	$oldpass = $_REQUEST['oldpass'];
	$newpass = $_REQUEST['newpass'];
	$confirmpass = $_REQUEST['confirmpass'];
	$b = $_REQUEST[b];
	
	$hashedpass = md5($oldpass);	
		
	if(!empty($b)) {
	
  	  if(!empty($oldpass) && !empty($newpass) && !empty($confirmpass)) {
		  $sql = "select username from admin_access where username='$registered_username' and password='$hashedpass'";
		  $query = mysql_query($sql);			   	
		  $r = mysql_fetch_array($query);

		  echo mysql_error();
		  		  
		  if(!empty($r)) {
		    if($newpass==$confirmpass && !empty($newpass) && !empty($confirmpass)) {
			    $newpass=md5($newpass);
			    
			    $sql = "update admin_access set password='$newpass' where username='$registered_username'";
			    $query = mysql_query($sql);
				
			    if($query) {				  
			      $msg = 'Your password has been changed';			      
		        }
			    else {				  
			      $msg = mysql_error();
	    		}  
		      }
		      else {			  
		        $msg = 'Your new passwords do not match';
	      	  }
		  }
		  else {			
		    $msg = 'Your old password is incorrect';
	  	  }
	  }
	  else {
		$msg = 'Fill in all fields';
	  }
	}

?>
<form name="change_password_form" method="post" action="">
<div class=form_layout>
	<div class="module_title"><img src='images/key.png'> CHANGE PASSWORD</div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;">
    <table class="form_table" align="center">
    	<tr>
          	<td>&nbsp;</td>
        </tr>
		<tr>
			<td align="right">Old Password :</td>
			<td><input type="password" name="oldpass" class="textbox"></td>
		</tr>
		<tr>
		    <td align="right">New Password :</td>
		    <td><input type="password" name="newpass" class="textbox"></td>
		</tr>
		<tr>
		    <td align="right">Confirm New Password :</td>
		    <td><input type="password" name="confirmpass" class="textbox" /></td>
		</tr>
		<tr>
			<td><br></td>
			<td><input type="submit" name="b" value="Submit" class="buttons"></td>
		</tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
	</table>
    </div>
</div>
</form>