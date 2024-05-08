<?php
	
	function new_privilegesform() {
		$objResponse = new xajaxResponse();
		$options = new options();
		
		$newContent = "<br><img src='images/key_add.png'> NEW ACCESS GROUP<p>
					<form id=newprivilegesform action=javascript:void(null); onsubmit=xajax_new_privileges(xajax.getFormValues('newprivilegesform'));toggleBox('demodiv',1);>
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
	
	function new_privileges($form_data) {
		$objResponse = new xajaxResponse();
		
		if(!empty($form_data[gname]) && !empty($form_data[description])) {
				
			$sql = "insert into access_type set
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
	
	function edit_privilegesform($id) {
		$objResponse = new xajaxResponse();
		$options = new options();
		
		//$objResponse->alert($id);
		
		$sql = mysql_query("select
								id,
								name,
								description
							from
								access_type
							where
								id='$id'");
						  
		$r = mysql_fetch_array($sql);
						  
		$newContent = "<br><img src='images/group_add.png'> NEW MESSAGING GROUP<p>
					<form id=editgroupform action=javascript:void(null); onsubmit=xajax_edit_privileges(xajax.getFormValues('editgroupform'));toggleBox('demodiv',1);>
						<table class=form_table>
						  <tr>
						  	<td>Group Name: </td>
						  	<td>
								<input type=text name=gname value='$r[name]' class=textbox>
								<input type=hidden name=id value='$r[id]' class=textbox>
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
	
	function edit_privileges($form_data) {
		$objResponse = new xajaxResponse();
		
		if(!empty($form_data[gname]) && !empty($form_data[description])) {
				
			$sql = "update access_type set
						name='$form_data[gname]',
						description='$form_data[description]'
					where
						id='$form_data[id]'";
			
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
	
	function addPToGroup($id) {
		$objResponse = new xajaxResponse();	
		$option = new options();
		
		$q = mysql_query("select name from access_type where id='$id'");
		$r = mysql_fetch_array($q);
		
		$newContent = "<br><img src='images/key_add.png'> <b>GRANT MODULES TO ".strtoupper($r[name])." GROUP</b>
					   <p><input type='text' id=addBkey class=textbox onkeyup=\"xajax_showFiles(document.getElementById('addBkey').value,'$id');toggleBox('demodiv',1);\" /><div id='addbookdiv' style='overflow-y:scroll;overflow-x:hidden;width:400px;height:100px;padding:5px;color:#5e6977;background:#EEEEEE;'></div><br><img src='images/key.png'> <b>ALLOWED MODULES</b><div id='selectedfilesdiv' style='overflow-y:scroll;overflow-x:hidden;width:400px;height:100px;padding:5px;color:#5e6977;background:#EEEEEE;'></div></p>";
		
		$objResponse->assign("Rdiv","innerHTML", $newContent);		
		$objResponse->script("toggleBox('demodiv',0)");
		$objResponse->script("showBox()");
		$objResponse->script("xajax_showSelectedFiles('$id')");
		$objResponse->script("document.getElementById('addBkey').focus();");
		
		return $objResponse;
	}
	
	function showFiles($addBkey, $id) {
		$objResponse = new xajaxResponse();
		
		if(!empty($addBkey)) {
			$query = mysql_query("select
										PCode,
										Pfilename,
										Fdescription
									from
										programs
									where
										(Fdescription like '%$addBkey%' or
										Pfilename like '%$addBkey%') and
										enabled='1' and
										protect='0'");
								
			while($r=mysql_fetch_array($query)) {
				$chk_if_priv = mysql_query("select id from my_privileges where PCode='$r[PCode]' and access_type_ID='$id'");
				if(mysql_num_rows($chk_if_priv)>0) continue;
			
				$row .= '<div style="padding:3px;border-bottom:#C0C0C0 1px dashed;"><img src=\'images/note.png\' style=\'cursor:pointer;\' onclick="xajax_saveSelectedFiles(\''.$r[PCode].'\',\''.$id.'\');toggleBox(\'demodiv\',1);" title="Add"> '.$r[Fdescription].' ('.$r[Pfilename].')</div>';
			}
		}
		
		$objResponse->assign("addbookdiv","innerHTML", $row);
		$objResponse->script("toggleBox('demodiv',0)");
		
		return $objResponse;
	}
	
	function saveSelectedFiles($PCode, $access_type) {
		$objResponse = new xajaxResponse();
		
		$id = date("Ymd-his");
		
		$q = mysql_query("insert into my_privileges set
								id='$id',
								PCode='$PCode',
								access_type_ID='$access_type'");
		
		$objResponse->script("xajax_showSelectedFiles('$access_type')");
		$objResponse->script("toggleBox('demodiv',0)");
		
		return $objResponse;
	}
	
	function showSelectedFiles($access_type) {
		$objResponse = new xajaxResponse();
		
		$query = mysql_query("select
									p.PCode,
									p.Pfilename,
									p.Fdescription,
									pp.id as ppid
								from
									programs as p,
									my_privileges as pp
								where
									pp.access_type_ID='$access_type' and
									p.PCode=pp.PCode
								order by
									p.Fdescription");
							
		while($r=mysql_fetch_array($query)) {
			$row .= '<div style="padding:3px;border-bottom:#C0C0C0 1px dashed;"><img src=\'images/trash.gif\' style=\'cursor:pointer;\' onclick="xajax_deleteFile(\''.$r[ppid].'\',\''.$access_type.'\');toggleBox(\'demodiv\',1);" title="Remove"> '.$r[Fdescription].' ('.$r[Pfilename].')</div>';
		}
		
		$objResponse->script("xajax_showFiles(document.getElementById('addBkey').value,'$access_type')");
		$objResponse->assign("selectedfilesdiv","innerHTML", $row);
		$objResponse->script("toggleBox('demodiv',0)");
		
		return $objResponse;
	}
	
	function deleteFile($ppid, $access_type) {
		$objResponse = new xajaxResponse();

		$q = mysql_query("delete from my_privileges where id='$ppid'");
		
		$objResponse->script("xajax_showSelectedFiles('$access_type')");
		$objResponse->script("toggleBox('demodiv',0)");
		
		return $objResponse;
	}

?>