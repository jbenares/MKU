<?php
	
	function new_substationform() {
		$objResponse = new xajaxResponse();
		$options = new options();
		
		$newContent = "<br><img src='images/connect.png'> NEW SUBSTATION<p>
					<form id=newsubstationform action=javascript:void(null); onsubmit=xajax_new_substation(xajax.getFormValues('newsubstationform'));toggleBox('demodiv',1);>
						<table class=form_table>
						  <tr>
						  	<td>Substation: </td>
						  	<td><input type=text name=substation class=textbox></td>
						  </tr>	
						  <tr>
						  	<td>Address: </td>
						  	<td><textarea name='address' style='overflow:hidden;width:350px;height:100px;font-size:11px;font-family:Arial;'></textarea></td>
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
	
	function new_substation($form_data) {
		$objResponse = new xajaxResponse();
		
		if(!empty($form_data[substation]) && !empty($form_data[address])) {
				
			$sql = "insert into substations set
						name='$form_data[substation]',
						address='$form_data[address]'";
			
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
	
	function edit_substationform($id) {
		$objResponse = new xajaxResponse();
		$options = new options();
		
		//$objResponse->addAlert($id);
		
		$sql = mysql_query("select
					  id,
					  name,
					  address
					from
					  substations
					where
					  id=$id");
						  
		$r = mysql_fetch_array($sql);
						  
		$newContent = "<br><img src='images/connect.png'> UPDATE SUBSTATION INFORMATION<p>
					<form id=editsubstationform action=javascript:void(null); onsubmit=xajax_edit_substation(xajax.getFormValues('editsubstationform'));toggleBox('demodiv',1);>
						<table class=form_table>
						  <tr>
						  	<td>Substation: </td>
						  	<td>
								<input type=hidden name=substationID value='$id'>
								<input type=text name=substation class=textbox value='$r[name]'>
							</td>
						  </tr>					
						  <tr>
						  	<td>Address: </td>
						  	<td><textarea name='address' cols='40'>".$r[address]."</textarea></td>
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
	
	function edit_substation($form_data) {
		$objResponse = new xajaxResponse();
		
		if(!empty($form_data[substationID]) && !empty($form_data[substation])) {
				
			$sql = "update substations set
						name='$form_data[substation]',
						address='$form_data[address]'
					where
						id='$form_data[substationID]'";
			
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
	
	function new_subofficeform() {
		$objResponse = new xajaxResponse();
		$options = new options();
		
		$newContent = "<br><img src='images/connect.png'> NEW SUB OFFICE<p>
					<form id=newsubofficeform action=javascript:void(null); onsubmit=xajax_new_suboffice(xajax.getFormValues('newsubofficeform'));toggleBox('demodiv',1);>
						<table class=form_table>
						  <tr>
						  	<td>Sub Office: </td>
						  	<td><input type=text name=suboffice class=textbox></td>
						  </tr>	
						  <tr>
						  	<td>Address: </td>
						  	<td><textarea name='address' style='overflow:hidden;width:350px;height:100px;font-size:11px;font-family:Arial;'></textarea></td>
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
	
	function new_suboffice($form_data) {
		$objResponse = new xajaxResponse();
		
		if(!empty($form_data[suboffice]) && !empty($form_data[address])) {
				
			$sql = "insert into suboffices set
						name='$form_data[suboffice]',
						address='$form_data[address]'";
			
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
	
	function edit_subofficeform($id) {
		$objResponse = new xajaxResponse();
		$options = new options();
		
		//$objResponse->addAlert($id);
		
		$sql = mysql_query("select
					  id,
					  name,
					  address
					from
					  suboffices
					where
					  id=$id");
						  
		$r = mysql_fetch_array($sql);
						  
		$newContent = "<br><img src='images/connect.png'> UPDATE SUB OFFICE INFORMATION<p>
					<form id=editofficeform action=javascript:void(null); onsubmit=xajax_edit_suboffice(xajax.getFormValues('editofficeform'));toggleBox('demodiv',1);>
						<table class=form_table>
						  <tr>
						  	<td>Substation: </td>
						  	<td>
								<input type=hidden name=substationID value='$id'>
								<input type=text name=substation class=textbox value='$r[name]'>
							</td>
						  </tr>					
						  <tr>
						  	<td>Address: </td>
						  	<td><textarea name='address' style='overflow:hidden;width:350px;height:100px;font-size:11px;font-family:Arial;'>".$r[address]."</textarea></td>
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
	
	function edit_suboffice($form_data) {
		$objResponse = new xajaxResponse();
		
		if(!empty($form_data[substationID]) && !empty($form_data[substation])) {
				
			$sql = "update suboffices set
						name='$form_data[substation]',
						address='$form_data[address]'
					where
						id='$form_data[substationID]'";
			
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