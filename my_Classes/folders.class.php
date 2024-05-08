<?php
	
	function new_folderform() {
		$objResponse = new xajaxResponse();
		
		$newContent = "<br><img src='images/folder_new.gif'> NEW FOLDER<p>
					<form id=newfolderform action=javascript:void(null); onsubmit=xajax_new_folder(xajax.getFormValues('newfolderform'));toggleBox('demodiv',1);>
						<table class=form_table>
						  <tr>
						  	<td>Folder Name: </td>
						  	<td><input type=text name=fname class=textbox></td>
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
		
		$objResponse->addAssign("Rdiv","innerHTML", $newContent);
		$objResponse->addScript("toggleBox('demodiv',0)");
		$objResponse->addScript("showBox()");
		
		return $objResponse->getXML();	
	}
	
	function new_folder($form_data) {
		$objResponse = new xajaxResponse();
		
		if(!empty($form_data[fname]) && !empty($form_data[description])) {
				
			$sql = "insert into folders set
					folderName='$form_data[fname]',
					folderdescription='$form_data[description]'";
			
			$query = mysql_query($sql);
			
			$objResponse->addScript("toggleBox('demodiv',0)");
			
			if($query) {
				$objResponse->addAlert("Query Successful!");
				$objResponse->addScript("window.location.reload();");
			}					
			else
				$objResponse->addAlert(mysql_error());
		}
		else {
			$objResponse->addScript("toggleBox('demodiv',0)");
			$objResponse->addAlert("Fill in all fields!");
		}
		
		return $objResponse->getXML();  			   
	}
	
	function edit_folderform($id) {
		$objResponse = new xajaxResponse();
		
		//$objResponse->addAlert($id);
		
		$sql = mysql_query("select
								folderID,
								folderName,
								folderdescription
							from
								folders
							where
								folderID='$id'");
						  
		$r = mysql_fetch_array($sql);
						  
		$newContent = "<br><img src='images/folder_new.gif'> UPDATE FOLDER<p>
					<form id=editfolderform action=javascript:void(null); onsubmit=xajax_edit_folder(xajax.getFormValues('editfolderform'));toggleBox('demodiv',1);>
						<table class=form_table>
						  <tr>
						  	<td>Folder Name: </td>
						  	<td>
								<input type=text name=fname value='$r[folderName]' class=textbox>
								<input type=hidden name=folderID value='$r[folderID]' class=textbox>
							</td>
						  </tr>
						  <tr>
						  	<td>Description: </td>
						  	<td><textarea name='description' style='overflow:hidden;width:300px;height:100px;font-size:11px;font-family:Arial;'>$r[folderdescription]</textarea></td>
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
		
		$objResponse->addAssign("Rdiv","innerHTML", $newContent);
		$objResponse->addScript("toggleBox('demodiv',0)");
		$objResponse->addScript("showBox()");
		
		return $objResponse->getXML();	
	}
	
	function edit_folder($form_data) {
		$objResponse = new xajaxResponse();
		
		if(!empty($form_data[fname]) && !empty($form_data[description])) {
				
			$sql = "update folders set
						folderName='$form_data[fname]',
						folderdescription='$form_data[description]'
					where
						folderID='$form_data[folderID]'";
			
			$query = mysql_query($sql);
			
			$objResponse->addScript("toggleBox('demodiv',0)");
			
			if($query) {
				$objResponse->addAlert("Query Successful!");
				$objResponse->addScript("window.location.reload();");
			}					
			else
				$objResponse->addAlert(mysql_error());
		}
		else {
			$objResponse->addScript("toggleBox('demodiv',0)");
			$objResponse->addAlert("Fill in all fields!");
		}
		
		return $objResponse->getXML();  			   
	}

?>