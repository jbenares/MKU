<?php

	function new_equipmentform() {
		$objResponse = new xajaxResponse();
		$options = new options();
		
		$newContent = "<br><img src='images/user_orange.png'> NEW EQUIPMENT<p>
					<form id=newareaform action=javascript:void(null); onsubmit=xajax_new_equipment(xajax.getFormValues('newareaform'));toggleBox('demodiv',1);>
						<table class=form_table>	
						  <tr>
						  	<td>Equipment Name: </td>
						  	<td><input type=text name=eq_name class=textbox></td>
						  </tr>
						  <tr>
						  	<td>Equipment Type: </td>
						  	<td><input type=text name=eqType class=textbox></td>
						  </tr>
						  <tr>
						  	<td>Equipment Model: </td>
						  	<td><input type=text name=eqModel class=textbox></td>
						  </tr>
						  <tr>
						  	<td>Plate #: </td>
						  	<td><input type=text name=plateNumber class=textbox></td>
						  </tr>
						  <tr>
						  	<td>Date of Purchase: </td>
						  	<td> <input name=date_of_purchase id=date_of_purchase class=textbox  type=text  onclick='fPopCalendar(\"date_of_purchase\")' /></td>
						  </tr>
						  <tr>
						  	<td>Rate Per Hour: </td>
						  	<td><input type=text name=rateperhour class=textbox></td>
						  </tr>
						  	<td>Notes: </td>
						  	<td><textarea name='eq_notes' style='overflow:hidden;width:350px;height:50px;font-size:11px;font-family:Arial;'></textarea></td>
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
	
	function new_equipment($form_data) {
		$objResponse = new xajaxResponse();
		
		if(!empty($form_data[eq_name]) && !empty($form_data[date_of_purchase]) && !empty($form_data[rateperhour]) && !empty($form_data[plateNumber]) && !empty($form_data[eqType]) && !empty($form_data[eqModel])) {
		
			$id = date("Ymd-his");
			$date_exec = date("Y-m-d H:i:s");
			
			$now_array = explode("/", $form_data[Ndate]);
			$now = $now_array[2].'-'.$now_array[0].'-'.$now_array[1];
				
			$sql = "insert into equipment set
						eq_name='$form_data[eq_name]',
						eqType='$form_data[eqType]',
						eqModel='$form_data[eqModel]',
						plateNumber='$form_data[plateNumber]',
						date_of_purchase='$form_data[date_of_purchase]',
						rateperhour='$form_data[rateperhour]',
						eq_notes='$form_data[eq_notes]'";
			
			$query = mysql_query($sql);		
			
			if(!mysql_error()) {
				$objResponse->alert("Query Successful!");
				$objResponse->script("window.location.reload();");
			}					
			else
				$objResponse->alert(mysql_error());
		}
		else {
			$objResponse->alert("Fill in all fields!");
		}
		
		$objResponse->script("toggleBox('demodiv',0)");
		return $objResponse;  			   
	}
	
	function edit_equipmentform($id) {
		$objResponse = new xajaxResponse();
		$options = new options();
		
		//$objResponse->alert($id);
		
		$sql = mysql_query("select
								*
							from
								equipment
							where
								eqID='$id'");
						  
		$r = mysql_fetch_array($sql);
						  
		$newContent = "<br><img src='images/user_orange.png'> UPDATE EQUIPMENT<p>
					<form id=editareaform action=javascript:void(null); onsubmit=xajax_edit_equipment(xajax.getFormValues('editareaform'));toggleBox('demodiv',1);>
						<table class=form_table>
						  <tr>
						  	<td>Equipment Name: </td>
						  	<td>
								<input type=text name=eq_name class=textbox value='$r[eq_name]'>
								<input type=hidden name=eqID class=textbox value='$id'>
							</td>
						  </tr>
						  <td>Equipment Type: </td>
						  	<td><input type=text name=eqType class=textbox value='$r[eqType]' ></td>
						  </tr>
						  <tr>
						  	<td>Equipment Model: </td>
						  	<td><input type=text name=eqModel class=textbox value='$r[eqModel]' ></td>
						  </tr>
						  <tr>
						  	<td>Plate #: </td>
						  	<td><input type=text name=plateNumber class=textbox value='$r[plateNumber]' ></td>
						  </tr>
						  <tr>
						  	<td>Date of Purchase: </td>
						  	<td><input type=text name=date_of_purchase  id=date_of_purchase class=textbox value='$r[date_of_purchase]' onclick='fPopCalendar(\"date_of_purchase\")'></td>
						  </tr>
						  <tr>
						  	<td>Rate Per hour: </td>
						  	<td><input type=text name=rateperhour class=textbox value='$r[rateperhour]'></td>
						  </tr>
						  	<td>Notes: </td>
						  	<td><textarea name='eq_notes' style='overflow:hidden;width:350px;height:50px;font-size:11px;font-family:Arial;'>$r[eq_notes]</textarea></td>
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
		
		return $objResponse;}
	
	function edit_equipment($form_data) {
		$objResponse = new xajaxResponse();
		
		if(!empty($form_data[eq_name]) && !empty($form_data[date_of_purchase]) && !empty($form_data[rateperhour])){
		   
			$sql = "update equipment set
						eq_name='$form_data[eq_name]',
						eqType='$form_data[eqType]',
						eqModel='$form_data[eqModel]',
						plateNumber='$form_data[plateNumber]',
						date_of_purchase='$form_data[date_of_purchase]',
						rateperhour='$form_data[rateperhour]',
						eq_notes='$form_data[eq_notes]'
					where
						eqID='$form_data[eqID]'";
			
			$query = mysql_query($sql);
			
			
			
			if($query) {
				$objResponse->alert("Query Successful!");
				$objResponse->script("window.location.reload();");
			}					
			else
				$objResponse->alert(mysql_error());
		}
		else {
			$objResponse->alert("Fill in all fields!");
		}
		
		$objResponse->script("toggleBox('demodiv',0)");
		return $objResponse;  			   
	}
	
?>