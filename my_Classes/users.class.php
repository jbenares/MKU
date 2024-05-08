<?php
	
	function new_userform() {
		$objResponse = new xajaxResponse();
		$options = new options();

		$newContent = "<br><img src='images/user_add.png'> NEW USER<p>
					<form id=newuserform action=javascript:void(null); onsubmit=xajax_new_user(xajax.getFormValues('newuserform'));toggleBox('demodiv',1);>
						<table class=form_table>
						  <tr>
						  	<td>Last Name: </td>
						  	<td><input type=text name=lname class=textbox></td>
						  </tr>
						  <tr>
						  	<td>First Name: </td>
						  	<td><input type=text name=fname class=textbox></td>
						  </tr>
						  <tr>
						  	<td>Middle Name: </td>
						  	<td><input type=text name=mname class=textbox></td>
						  </tr>
						  <tr>
						  	<td>Access: </td>
						  	<td>".$options->access_options('')."</td>
						  </tr>
						  <tr>
						  	<td>Company: </td>
						  	<td>".$options->company_options('')."</td>
						  </tr>
						  <tr>						    
						  	<td>Username: </td>
						  	<td><input type=text name=username class=textbox></td>
						  </tr>						  		  
						  <tr>
						  	<td></td>
						  	<td>						  	  
						  	  <input type=submit name=b value='Submit' class=buttons>
						  	  <input type=reset value='Clear Form' class=buttons>
						  	</td>
						  </tr>
						</table>
					   </form>";					   
		
		$objResponse->assign("Rdiv","innerHTML", $newContent);
		$objResponse->script("toggleBox('demodiv',0)");
		$objResponse->script("showBox()");
		
		//return $objResponse;	
		return $objResponse;
	}
	
	function new_user($form_data) {
		$objResponse = new xajaxResponse();
		
		if(!empty($form_data[lname]) && !empty($form_data[fname]) &&
			!empty($form_data[mname]) && !empty($form_data[username]) && !empty($form_data[access_type])) {
				
			$id = date("Ymd-his");
			$date_exec = date("Y-m-d H:i:s");
				
			$sql = "insert into admin_access set
					userID='$id',
					user_lname='$form_data[lname]',
					user_fname='$form_data[fname]',
					user_mname='$form_data[mname]',
					username='$form_data[username]',
					access='$form_data[access_type]',
					membered_since='$date_exec',
					companyID='$form_data[company]'";
			
			$query = mysql_query($sql);
			
			$objResponse->script("toggleBox('demodiv',0)");
			
			if($query) {
				$objResponse->alert("Query Successful!");
				$objResponse->script("window.location.reload();");
			}					
			else
				$objResponse->alert(mysql_error());
		}
		else {
			$objResponse->script("toggleBox('demodiv',0)");
			$objResponse->alert("Fill in all fields!");
		}
		
		return $objResponse;  			   
	}
	
	function edit_userform($id) {
		$objResponse = new xajaxResponse();
		$options = new options();
		
		//$objResponse->alert($id);
		
		$getUser= mysql_query("select
						  a.userID,
						  a.user_lname,
						  a.user_fname,
						  a.user_mname,
						  a.username,
						  a.access,
						  a.active,
						  a.companyID,
						  t.name
						from
						  admin_access as a,
						  access_type as t
						where
						  a.access=t.id and
						  a.userID='$id'");
						  
		$rUser = mysql_fetch_array($getUser);
		
		if($rUser[active]==1) $active ='checked';
		else $inactive = 'checked';
						  
		$newContent = "<br><img src='images/user_add.png'> UPDATE USER<p>
					<form id=edituserform action=javascript:void(null); onsubmit=xajax_edit_user(xajax.getFormValues('edituserform'));toggleBox('demodiv',1);>
						<table class=form_table>
						  <tr>
						  	<td>Last Name: </td>
						  	<td>
								<input type=text name=lname class=textbox value='$rUser[user_lname]'>
								<input type=hidden name=userID class=textbox value='$id'>
							</td>
						  </tr>
						  <tr>
						  	<td>First Name: </td>
						  	<td><input type=text name=fname class=textbox value='$rUser[user_fname]'></td>
						  </tr>
						  <tr>
						  	<td>Middle Name: </td>
						  	<td><input type=text name=mname class=textbox value='$rUser[user_mname]'></td>
						  </tr>
						  <tr>
						  	<td>Access: </td>
						  	<td>".$options->access_options($rUser[access])."</td>
						  </tr>	
						  <tr>
						  	<td>Company: </td>
						  	<td>".$options->company_options($rUser[companyID])."</td>
						  </tr>
						  <tr>						    
						  	<td>Username: </td>
						  	<td><input type=text name=username class=textbox value='$rUser[username]'></td>
						  </tr>
						  <tr>						    
						  	<td>Status: </td>
						  	<td>
								<input type=radio name=active value='1' $active> Active
								<input type=radio name=active value='0' $inactive> Inactive
							</td>
						  </tr>			  
						  <tr>
						  	<td></td>
						  	<td>						  	  
						  	  <input type=submit name=b value='Submit' class=buttons>
						  	  <input type=reset value='Clear Form' class=buttons>
						  	</td>
						  </tr>
						</table>
					   </form>";					   
		
		$objResponse->assign("Rdiv","innerHTML", $newContent);
		$objResponse->script("toggleBox('demodiv',0)");
		$objResponse->script("showBox()");
		
		return $objResponse;	
	}
	
	function edit_user($form_data) {
		$objResponse = new xajaxResponse();
		
		if(!empty($form_data[lname]) && !empty($form_data[fname]) &&
			!empty($form_data[mname]) && !empty($form_data[username]) && !empty($form_data[access_type])) {
				
			$id = date("Ymd-his");
				
			$sql = "update admin_access set
						user_lname='$form_data[lname]',
						user_fname='$form_data[fname]',
						user_mname='$form_data[mname]',
						username='$form_data[username]',
						access='$form_data[access_type]',
						active='$form_data[active]',
						companyID='$form_data[company]'
					where
						userID='$form_data[userID]'";
			
			$query = mysql_query($sql);
			
			$objResponse->script("toggleBox('demodiv',0)");
			
			if($query) {
				$objResponse->alert("Query Successful!");
				$objResponse->script("window.location.reload();");
			}					
			else
				$objResponse->alert(mysql_error());
		}
		else {
			$objResponse->script("toggleBox('demodiv',0)");
			$objResponse->alert("Fill in all fields!");
		}
		
		return $objResponse;  			   
	}
	
	function select_to_manage($userID) {
		$objResponse = new xajaxResponse();
		$options = new options();
		
		$getUser = mysql_query("select
									concat(user_lname,', ',user_fname) as fullname
								from
									admin_access
								where
									userID='$userID'");
									
		$rUser = mysql_fetch_array($getUser);

		$newContent = "<br><img src='images/key.png'> CHANGE/RESET PASSWORD<p>
					<form id=newuserform action=javascript:void(null); onsubmit=xajax_save_newPass(xajax.getFormValues('newuserform'));toggleBox('demodiv',1);>
						<table class=form_table>
						  <tr>
						  	<td colspan=2>User : ".$rUser[fullname]."<hr></td>
						  </tr>
						  <tr>
						  	<td>New Password: </td>
						  	<td>
								<input type=password name=newpass class=textbox>
								<input type=hidden name=userID value='".$userID."' class=textbox>
							</td>
						  </tr>
						  <tr>
						  	<td>Confirm New Password: </td>
						  	<td><input type=password name=confirmnewpass class=textbox></td>
						  </tr>						  				  
						  <tr>
						  	<td></td>
						  	<td>						  	  
						  	  <input type=submit name=b value='Submit' class=buttons>
						  	  <input type=reset value='Clear Form' class=buttons>
						  	</td>
						  </tr>
						</table>
					   </form>";					   
		
		$objResponse->assign("Rdiv","innerHTML", $newContent);
		$objResponse->script("toggleBox('demodiv',0)");
		$objResponse->script("showBox()");
		
		//return $objResponse;	
		return $objResponse;
	}
	
	function save_newPass($form_data) {
		$objResponse = new xajaxResponse();
		
		if(!empty($form_data[newpass]) && !empty($form_data[confirmnewpass])) {		  		  
			if($form_data[newpass]==$form_data[confirmnewpass]) {
				$newpass=md5($form_data[newpass]);
				
				$sql = "update admin_access set password='$newpass' where userID='$form_data[userID]'";
				$query = mysql_query($sql);
				
				if($query) {				  
				  $objResponse->alert("Your password has been changed");
				  $objResponse->script("hideBox()");			      
				}
				else {				  
				  $objResponse->alert("mysql_error()");
				}  
			}
			else {			  
				$objResponse->alert("Your new passwords do not match");
			}
		}
		else {
			$objResponse->alert("Fill in all fields");
		}
		
		$objResponse->script("toggleBox('demodiv',0)");
		
		return $objResponse;
	}

?>