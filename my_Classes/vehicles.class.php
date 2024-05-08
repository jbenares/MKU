<?php
	
	function new_vehicleform() {
		$objResponse = new xajaxResponse();
		$options = new options();
		
		$newContent = "<br><img src='images/car.png'> NEW VEHICLE<p>
					<form id=newvehicleform action=javascript:void(null); onsubmit=xajax_new_vehicle(xajax.getFormValues('newvehicleform'));toggleBox('demodiv',1);>
						<table class=form_table>
						  <tr>
						  	<td>IMEI: </td>
						  	<td><input type=text name=IMEI class=textbox></td>
						  </tr>
						  <tr>
						  	<td>Plate # : </td>
						  	<td><input type=text name=plate_num class=textbox></td>
						  </tr>
						  <tr>
						  	<td>Make: </td>
						  	<td><input type=text name=make class=textbox></td>
						  </tr>	
						  <tr>
						  	<td>Mobile#: </td>
						  	<td><input type=text name=callerID class=textbox></td>
						  </tr>
						  <tr>
						  	<td>Description: </td>
						  	<td><textarea name='description' cols='40' rows='5'></textarea></td>
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
	
	function new_vehicle($form_data) {
		$objResponse = new xajaxResponse();
		
		if(!empty($form_data[IMEI]) && !empty($form_data[plate_num]) && !empty($form_data[callerID])) {
				
			$IMEI = strtoupper($form_data[IMEI]);
			$plate_num = strtoupper($form_data[plate_num]);
				
			$sql = "insert into vehicles set
						IMEI='$IMEI',
						plate_num='$plate_num',
						make='$form_data[make]',
						callerID='$form_data[callerID]',
						description='$form_data[description]'";
			
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
	
	function edit_vehicleform($IMEI) {
		$objResponse = new xajaxResponse();
		$options = new options();
		
		//$objResponse->addAlert($id);
		
		$sql = mysql_query("select
							  IMEI,
							  plate_num,
							  make,
							  callerID,
							  description
							from
							  vehicles
							 where
							 	IMEI='$IMEI'");
						  
		$r = mysql_fetch_array($sql);
						  
		$newContent = "<br><img src='images/disconnect.png'> UPDATE SUB OFFICE INFORMATION<p>
					<form id=editvehicleform action=javascript:void(null); onsubmit=xajax_edit_vehicle(xajax.getFormValues('editvehicleform'));toggleBox('demodiv',1);>
						<table class=form_table>
						  <tr>
						  	<td>IMEI: </td>
						  	<td><input type=text name=IMEI class=textbox value='$r[IMEI]'></td>
						  </tr>
						  <tr>
						  	<td>Plate # : </td>
						  	<td><input type=text name=plate_num class=textbox value='$r[plate_num]'></td>
						  </tr>
						  <tr>
						  	<td>Make: </td>
						  	<td><input type=text name=make class=textbox value='$r[make]'></td>
						  </tr>	
						  <tr>
						  	<td>Mobile #: </td>
						  	<td><input type=text name=callerID class=textbox value='$r[callerID]'></td>
						  </tr>
						  <tr>
						  	<td>Description: </td>
						  	<td><textarea name='description' cols='40' rows='5'>". $r[description]."</textarea></td>
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
	
	function edit_vehicle($form_data) {
		$objResponse = new xajaxResponse();
		
		if(!empty($form_data[IMEI]) && !empty($form_data[plate_num]) && !empty($form_data[callerID])) {
				
			$sql = "update vehicles set
						IMEI='$form_data[IMEI]',
						plate_num='$form_data[plate_num]',
						make='$form_data[make]',
						callerID='$form_data[callerID]',
						description='$form_data[description]'
					where
						IMEI='$form_data[IMEI]'";
			
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
	
	function demand_ping($receiver, $sender) {
		include_once("my_Classes/query.class.php");
		
		$objResponse = new xajaxResponse();
		$transac = new query();
		
		$message = '1234 ?data';
		
		$transac->sendSMS($message, $sender, $receiver);
		
		$objResponse->addScript("toggleBox('demodiv',0)");
		$objResponse->addAlert("Command Successfully Sent!");		
		
		return $objResponse->getXML();  			   
	}

?>