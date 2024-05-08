<?php

	function new_locationform() {
		$objResponse = new xajaxResponse();
		$options = new options();
		
		$newContent = "<br><img src='images/user_orange.png'> NEW LOCATION<p>
					<form id=newareaform action=javascript:void(null); onsubmit=xajax_new_location(xajax.getFormValues('newareaform'));toggleBox('demodiv',1);>
						<table class=form_table>	
						  	<td>Location: </td>
						  	<td><textarea name='location' style='overflow:hidden;width:350px;height:50px;font-size:11px;font-family:Arial;'></textarea></td>
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
	
	function new_location($form_data) {
		$objResponse = new xajaxResponse();
		
		if(!empty($form_data[location])) {
				
			$sql = "insert into location set
						location='$form_data[location]'
					";
			
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
	
	function edit_locationform($id) {
		$objResponse = new xajaxResponse();
		$options = new options();
		
		//$objResponse->alert($id);
		
		$sql = mysql_query("select
								*
							from
								location
							where
								locale_id='$id'");
						  
		$r = mysql_fetch_assoc($sql);
						  
		$newContent = "<br><img src='images/user_orange.png'> EDIT LOCATION<p>
					<form id=newareaform action=javascript:void(null); onsubmit=xajax_edit_location(xajax.getFormValues('newareaform'));toggleBox('demodiv',1);>
						<table class=form_table>	
						  	<td>Location: </td>
						  	<td><textarea name='location' style='overflow:hidden;width:350px;height:50px;font-size:11px;font-family:Arial;'>$r[location]</textarea></td>
							<input type=hidden name=locale_id value='$r[locale_id]' >
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
	
	function edit_location($form_data) {
		$objResponse = new xajaxResponse();
		
		if(!empty($form_data[location])) {
		   
			$sql = "update location set
						location='$form_data[location]'
					where locale_id='$form_data[locale_id]'
					";
			
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