<?php
	
	function new_groupform() {
		$objResponse = new xajaxResponse();
		
		$newContent = "<br><img src='images/group_add.png'> NEW MESSAGING GROUP<p>
					<form id=newgroupform action=javascript:void(null); onsubmit=xajax_new_group(xajax.getFormValues('newgroupform'));toggleBox('demodiv',1);>
						<table class=form_table>
						  <tr>
						  	<td>Group Name: </td>
						  	<td><input type=text name=gname class=textbox></td>
						  </tr>
						  <tr>
						  	<td>Description: </td>
						  	<td><textarea name='description' style='overflow:hidden;width:300px;height:100px;font-size:11px;font-family:Arial;'></textarea></td>
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
	
	function new_group($form_data) {
		$objResponse = new xajaxResponse();
		
		if(!empty($form_data[gname]) && !empty($form_data[description])) {
				
			$sql = "insert into groups set
					name='$form_data[gname]',
					description='$form_data[description]'";
			
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
	
	function edit_groupform($id) {
		$objResponse = new xajaxResponse();
		
		//$objResponse->alert($id);
		
		$sql = mysql_query("select
								groupID,
								name,
								description
							from
								groups
							where
								groupID='$id'");
						  
		$r = mysql_fetch_array($sql);
						  
		$newContent = "<br><img src='images/group_add.png'> UPDATE MESSAGING GROUP<p>
					<form id=editgroupform action=javascript:void(null); onsubmit=xajax_edit_group(xajax.getFormValues('editgroupform'));toggleBox('demodiv',1);>
						<table class=form_table>
						  <tr>
						  	<td>Group Name: </td>
						  	<td>
								<input type=text name=gname value='$r[name]' class=textbox>
								<input type=hidden name=groupID value='$r[groupID]' class=textbox>
							</td>
						  </tr>
						  <tr>
						  	<td>Description: </td>
						  	<td><textarea name='description' style='overflow:hidden;width:300px;height:100px;font-size:11px;font-family:Arial;'>$r[description]</textarea></td>
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
	
	function edit_group($form_data) {
		$objResponse = new xajaxResponse();
		
		if(!empty($form_data[gname]) && !empty($form_data[description])) {
				
			$sql = "update groups set
						name='$form_data[gname]',
						description='$form_data[description]'
					where
						groupID='$form_data[groupID]'";
			
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
	
	function addUsersToGroup($id) {
		$objResponse = new xajaxResponse();	
		
		$q = mysql_query("select name from groups where groupID='$id'");
		$r = mysql_fetch_array($q);
		
		$newContent = "<br><img src='images/group_add.png'> <b>ADD USERS TO ".strtoupper($r[name])." GROUP</b>
					   <p><input type='text' id=addBkey class=textbox onkeyup=\"xajax_showUsersGroup(document.getElementById('addBkey').value,'$id');toggleBox('demodiv',1);\" /><div id='addbookdiv' style='overflow-y:scroll;overflow-x:hidden;width:400px;height:100px;padding:5px;color:#5e6977;background:#EEEEEE;'></div><br><img src='images/group.png'> <b>GROUP MEMBERS</b><div id='selectedgroupdiv' style='overflow-y:scroll;overflow-x:hidden;width:400px;height:100px;padding:5px;color:#5e6977;background:#EEEEEE;'></div></p>";
		
		$objResponse->assign("Rdiv","innerHTML", $newContent);		
		$objResponse->script("toggleBox('demodiv',0)");
		$objResponse->script("showBox()");
		$objResponse->script("xajax_showSelectedUsers('$id')");
		$objResponse->script("document.getElementById('addBkey').focus();");
		
		return $objResponse;
	}
	
	function showUsersGroup($addBkey, $id) {
		$objResponse = new xajaxResponse();
		
		if(!empty($addBkey)) {
			$query = mysql_query("select
										userID,
										concat(user_lname,', ',user_fname) as fullname
									from
										admin_access
									where
										(user_lname like '%$addBkey%' or
										user_fname like '%$addBkey%') and 
										access!='1'");
								
			while($r=mysql_fetch_array($query)) {
				$check_if_membered = mysql_query("select gm_id from group_members where groupID='$id' and userID='$r[userID]'");
				if(mysql_num_rows($check_if_membered)>0) continue;
			
				$row .= '<div style="padding:3px;border-bottom:#C0C0C0 1px dashed;"><img src=\'images/user_add.png\' style=\'cursor:pointer;\' onclick="xajax_saveSelectedUsers(\''.$r[userID].'\',\''.$id.'\');toggleBox(\'demodiv\',1);""> '.$r[fullname].'</div>';
			}
		}
		
		$objResponse->assign("addbookdiv","innerHTML", $row);
		$objResponse->script("toggleBox('demodiv',0)");
		
		return $objResponse;
	}
	
	function saveSelectedUsers($userID, $groupID) {
		$objResponse = new xajaxResponse();
		
		$id = date("Ymd-his");
		
		$q = mysql_query("insert into group_members set
								gm_id='$id',
								userID='$userID',
								groupID='$groupID'");
								
		$objResponse->script("xajax_showSelectedUsers('$groupID')");
		$objResponse->script("toggleBox('demodiv',0)");
		
		return $objResponse;
	}
	
	function showSelectedUsers($groupID) {
		$objResponse = new xajaxResponse();
		
		$query = mysql_query("select
									a.userID,
									concat(a.user_lname,', ',a.user_fname) as fullname,
									gm.gm_id
								from
									admin_access as a,
									group_members as gm
								where
									gm.groupID='$groupID' and
									gm.userID=a.userID");
							
		while($r=mysql_fetch_array($query)) {
			$row .= '<div style="padding:3px;border-bottom:#C0C0C0 1px dashed;"><img src=\'images/trash.gif\' style=\'cursor:pointer;\' onclick="xajax_deleteGM(\''.$r[gm_id].'\',\''.$groupID.'\');toggleBox(\'demodiv\',1);"> '.$r[fullname].'</div>';
		}
		
		$objResponse->script("xajax_showUsersGroup(document.getElementById('addBkey').value,'$groupID')");
		$objResponse->assign("selectedgroupdiv","innerHTML", $row);
		$objResponse->script("toggleBox('demodiv',0)");
		
		return $objResponse;
	}
	
	function deleteGM($gm_id, $groupID) {
		$objResponse = new xajaxResponse();

		$q = mysql_query("delete from group_members where gm_id='$gm_id'");
		
		$objResponse->script("xajax_showSelectedUsers('$groupID')");
		$objResponse->script("toggleBox('demodiv',0)");
		
		return $objResponse;
	}

?>