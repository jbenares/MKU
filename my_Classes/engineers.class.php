<?php
	
	function new_engineerform() {
		$objResponse = new xajaxResponse();
		$options = new options();
		
		$newContent = "<br><img src='images/user_orange.png'> NEW ENGINEER<p>
					<form id=newengineerform action=javascript:void(null); onsubmit=xajax_new_engineer(xajax.getFormValues('newengineerform'));toggleBox('demodiv',1);>
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
						  	<td>Sub Office: </td>
						  	<td>".$options->substation_options('')."</td>
						  </tr>	
						  <tr>						    
						  	<td>Mobile #: </td>
						  	<td><input type=text name=mobile_num class=textbox value='+63'></td>
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
	
	function new_engineer($form_data) {
		$objResponse = new xajaxResponse();
		
		if(!empty($form_data[lname]) && !empty($form_data[fname]) &&
			!empty($form_data[mobile_num]) && !empty($form_data[substation])) {
				
			$sql = "insert into engineers set
					lname='$form_data[lname]',
					fname='$form_data[fname]',
					mobile_num='$form_data[mobile_num]',
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
	
	function edit_engineerform($id) {
		$objResponse = new xajaxResponse();
		$options = new options();
		
		//$objResponse->addAlert($id);
		
		$getUser= mysql_query("select
						e.id,
						e.lname,
						e.fname,
						e.mobile_num,
						sb.id
					from
					  	engineers as e,
					  	substations as sb
					where
					  	e.substation_id=sb.id and
						e.id='$id'");
						  
		$rUser = mysql_fetch_array($getUser);
						  
		$newContent = "<br><img src='images/user_orange.png'> UPDATE ENGINEER<p>
					<form id=edit_engineerform action=javascript:void(null); onsubmit=xajax_edit_engineer(xajax.getFormValues('edit_engineerform'));toggleBox('demodiv',1);>
						<table class=form_table>
						  <tr>
						  	<td>Last Name: </td>
						  	<td>
								<input type=hidden name=Eid class=textbox value='$id'>
								<input type=text name=lname class=textbox value='$rUser[lname]'>
							</td>
						  </tr>
						  <tr>
						  	<td>First Name: </td>
						  	<td><input type=text name=fname class=textbox value='$rUser[fname]'></td>
						  </tr>
						  <tr>
						  	<td>Sub Office: </td>
						  	<td>".$options->substation_options($rUser[id])."</td>
						  </tr>	
						  <tr>						    
						  	<td>Username: </td>
						  	<td><input type=text name=mobile_num class=textbox value='$rUser[mobile_num]'></td>
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
	
	function edit_engineer($form_data) {
		$objResponse = new xajaxResponse();
		
		if(!empty($form_data[lname]) && !empty($form_data[Eid]) && !empty($form_data[fname]) &&
			!empty($form_data[mobile_num]) && !empty($form_data[substation])) {
				
			$sql = "update engineers set
						lname='$form_data[lname]',
						fname='$form_data[fname]',
						mobile_num='$form_data[mobile_num]',
						substation_id='$form_data[substation]'
					where
						id='$form_data[Eid]'";
			
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