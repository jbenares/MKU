<?php
	
	function new_areaform() {
		$objResponse = new xajaxResponse();
		$options = new options();
		
		$newContent = "<br><img src='images/transmit.png'> NEW SENSOR LOCATION<p>
					<form id=newareaform action=javascript:void(null); onsubmit=xajax_new_area(xajax.getFormValues('newareaform'));toggleBox('demodiv',1);>
						<table class=form_table>
						  <tr>
						  	<td>Feeder #: </td>
						  	<td><input type=text name=area class=textbox></td>
						  </tr>
						  <tr>
						  	<td>Mobile #: </td>
						  	<td><input type=text name=mobileno class=textbox value='+63'></td>
						  </tr>
						  <tr>
						  	<td>Sub Office: </td>
						  	<td>".$options->substation_options('')."</td>
						  </tr>	
						  <tr>						    
						  	<td>Latitude: </td>
						  	<td><input type=text name=latitude class=textbox></td>
						  </tr>	
						  <tr>						    
						  	<td>Longitude: </td>
						  	<td><input type=text name=longitude class=textbox></td>
						  </tr>		
						  <tr>
						  	<td>Notes: </td>
						  	<td><textarea name='notes' style='overflow:hidden;width:350px;height:100px;font-size:11px;font-family:Arial;'></textarea></td>
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
	
	function new_area($form_data) {
		$objResponse = new xajaxResponse();
		
		if(!empty($form_data[area]) && !empty($form_data[mobileno]) &&
			!empty($form_data[substation]) && !empty($form_data[latitude]) && !empty($form_data[longitude])) {
				
			$sql = "insert into stations set
					station_name='$form_data[area]',
					station_code='$form_data[mobileno]',
					latitude='$form_data[latitude]',
					longitude='$form_data[longitude]',
					notes='$form_data[notes]',
					substation_id='$form_data[substation]'";
			
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
	
	function edit_areaform($id) {
		$objResponse = new xajaxResponse();
		$options = new options();
		
		//$objResponse->addAlert($id);
		
		$sql = mysql_query("select
					  id,
					  station_name,
					  station_code,
					  latitude,
					  longitude,
					  notes,
					  substation_id
					from
					  stations
					where
					  id=$id");
						  
		$r = mysql_fetch_array($sql);
						  
		$newContent = "<br><img src='images/transmit.png'> UPDATE SENSOR LOCATION<p>
					<form id=editareaform action=javascript:void(null); onsubmit=xajax_edit_area(xajax.getFormValues('editareaform'));toggleBox('demodiv',1);>
						<table class=form_table>
						  <tr>
						  	<td>Feeder: </td>
						  	<td>
								<input type=hidden name=stationID value='$id'>
								<input type=text name=area class=textbox value='$r[station_name]'>
							</td>
						  </tr>
						  <tr>
						  	<td>Mobile #: </td>
						  	<td><input type=text name=mobileno class=textbox value='$r[station_code]'></td>
						  </tr>
						  <tr>
						  	<td>Sub Office: </td>
						  	<td>".$options->substation_options($r[substation_id])."</td>
						  </tr>	
						  <tr>						    
						  	<td>Latitude: </td>
						  	<td><input type=text name=latitude class=textbox value='$r[latitude]'></td>
						  </tr>	
						  <tr>						    
						  	<td>Longitude: </td>
						  	<td><input type=text name=longitude class=textbox value='$r[longitude]'></td>
						  </tr>
						  <tr>
						  	<td>Notes: </td>
						  	<td><textarea name='notes' cols='40' rows='5'>$r[notes]</textarea></td>
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
	
	function edit_area($form_data) {
		$objResponse = new xajaxResponse();
		
		if(!empty($form_data[stationID]) && !empty($form_data[area]) && !empty($form_data[mobileno]) &&
			!empty($form_data[substation]) && !empty($form_data[latitude]) && !empty($form_data[longitude])) {
				
			$sql = "update stations set
						station_name='$form_data[area]',
						station_code='$form_data[mobileno]',
						latitude='$form_data[latitude]',
						longitude='$form_data[longitude]',
						notes='$form_data[notes]',
						substation_id='$form_data[substation]'
					where
						id='$form_data[stationID]'";
			
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