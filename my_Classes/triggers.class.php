<?php

	function new_triggerform() {
		$objResponse = new xajaxResponse();
		$options = new options();
		
		$newContent = "<br><img src='images/wrench.png'> NEW TRIGGER<p>
					<form id=newtriggerform action=javascript:void(null); onsubmit=xajax_new_trigger(xajax.getFormValues('newtriggerform'));toggleBox('demodiv',1);>
						<table class=form_table>
						  <tr>
						  	<td>Mobile #: </td>
						  	<td><input type=text name=mobileno class=textbox value='+63'></td>
						  </tr>	
						  <tr>						    
						  	<td>Password: </td>
						  	<td><input type=text name=password class=textbox></td>
						  </tr>		
						  <tr>
						  	<td>Notes: </td>
						  	<td><textarea name='notes' cols='40' rows='5'></textarea></td>
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
	
	function new_trigger($form_data) {
		$objResponse = new xajaxResponse();
		
		if(!empty($form_data[mobileno]) && !empty($form_data[password]) && !empty($form_data[notes])) {
				
			$sql = "insert into triggers set
					mobileno='$form_data[mobileno]',
					password='$form_data[password]',
					notes='$form_data[notes]'";
			
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
	
	function edit_triggerform($id) {
		$objResponse = new xajaxResponse();
		$options = new options();
		
		//$objResponse->addAlert($id);
		
		$sql = mysql_query("select
						  id,
						  mobileno,
						  password,
						  notes
						from
						  triggers");
						  
		$r = mysql_fetch_array($sql);
						  
		$newContent = "<br><img src='images/wrench.png'> UPDATE TRIGGER<p>
					<form id=edittriggerform action=javascript:void(null); onsubmit=xajax_edit_trigger(xajax.getFormValues('edittriggerform'));toggleBox('demodiv',1);>
						<table class=form_table>
						  <tr>
						  	<td>Mobile #: </td>
						  	<td>
								<input type=hidden name=id value='".$r[id]."'>
								<input type=text name=mobileno class=textbox value='".$r[mobileno]."'>
							</td>
						  </tr>	
						  <tr>						    
						  	<td>Password: </td>
						  	<td><input type=text name=password class=textbox value='".$r[password]."'></td>
						  </tr>		
						  <tr>
						  	<td>Notes: </td>
						  	<td><textarea name='notes' cols='40' rows='5'>".$r[notes]."</textarea></td>
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
	
	function edit_trigger($form_data) {
		$objResponse = new xajaxResponse();
		
		if(!empty($form_data[mobileno]) && !empty($form_data[password]) && !empty($form_data[notes])) {
				
			$sql = "update triggers set
						mobileno='$form_data[mobileno]',
						password='$form_data[password]',
						notes='$form_data[notes]'
					where
						id='$form_data[id]'";
			
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

	function command_form($id) {
		$objResponse = new xajaxResponse();
		
		$query = 	mysql_query("select
							  id,
							  mobileno,
							  password,
							  notes
							from
							  triggers
							where
							  id='$id'");
							  
		$r = mysql_fetch_array($query);
		
		$newContent = "<br><img src='images/phone.png'> SEND TRIGGER COMMAND<p>
						<table cellpadding=3 class=Rtable>						 
						  <tr>
						  	<td>Mobile # : </td>
						  	<td>".$r[mobileno]."</td>
						  </tr>
						  <tr>
						  	<td>Password : </td>
						  	<td>".$r[password]."</td>
						  </tr>
						  <tr>
						  	<td>Notes : </td>
						  	<td>".$r[notes]."</td>
						  </tr>
						  <tr>
						  	<td>Output : </td>
						  	<td>
								<select id='output' class=select>
									<option value=0>Please Select Output . . .</option>
									<option value=1>Output 1</option>
									<option value=2>Output 2</option>
									<option value=3>Output 3</option>
									<option value=4>Output 4</option>
								</select>
							</td>
						  </tr>
						</table>						
						</p><hr>
						<p align=right>						
						<input type=button name='b' value='Set To Off' onclick=\"return send_confirm('$r[password]', document.getElementById('output').value, 'off', '$r[mobileno]', '$id')\" class='buttons'>
						<input type=button name='b' value='Set To On' onclick=\"return send_confirm('$r[password]', document.getElementById('output').value, 'on', '$r[mobileno]', '$id')\" class='buttons'><p>";							   
		
		$objResponse->addAssign("Rdiv","innerHTML", $newContent);
		$objResponse->addScript("toggleBox('demodiv',0)");
		$objResponse->addScript("showBox()");
		
		return $objResponse->getXML();	
	}
	
	function command_sensor($password, $output, $command, $receiver, $sender, $trigger_id) {	
		include_once("my_Classes/query.class.php");
		
		$objResponse = new xajaxResponse();
		$transac = new query();
		
		$Tid = $transac->GUID();
		
		$saveLogs = mysql_query("insert into trigger_logs set
									Tid='$Tid',
									trigger_id='$trigger_id',
									output='$output',
									command='$command',
									triggerDate=SYSDATE()");
		
		if($output!=0) {
			$message = $password." output".$output." ".$command;
			$transac->sendSMS($message, $sender, $receiver);
			
			$objResponse->addScript("toggleBox('demodiv',0)");
			$objResponse->addAlert("Command Successfully Sent!");		 		
		}
		else {
			$objResponse->addAlert("Please select a trigger output port!");
		}	   
		
		return $objResponse->getXML();
	}

?>