<?php
	// Update dependents data - dependents.php
	function edit_dependentsform($id) {
		$objResponse = new xajaxResponse();
		$randy_options = new randy_options();		
		//$objResponse->alert($id);
		
		$sql = mysql_query("select
								*				
							from
								dependents
							where
								dependentsID='$id'");
						  
		$r = mysql_fetch_array($sql);
		
		$newContent = "<br><img src='images/user_orange.png'> UPDATE DEPENDENTS' DATA<p>
					<form id=editdependentsform action=javascript:void(null); onsubmit=xajax_edit_dependents(xajax.getFormValues('editdependentsform'));toggleBox('demodiv',1);>
						<table class=form_table >
						  <tr>
						  	<td>Dependent's Last Name: </td>
						  	<td><input type=text name=dep_lname class=textbox value='$r[dep_lname]'></td>
								<input type=hidden name=id class=textbox value='$id'>
						  <tr>
						  </tr>	
						  	<td>Dependent's First Name: </tdr>
						  	<td><input type=text name=dep_fname class=textbox value='$r[dep_fname]'></td>
						  <tr>
						  </tr>	
						  	<td>Dependent's Middle Name: </td>
						  	<td><input type=text name=dep_mname class=textbox value='$r[dep_mname]'></td>
						  <tr>
						  </tr>	
							<td>Date of Birth: </td>
							<td><input type=\"text\" name=\"tdate\" id=\"tdate\" class=\"textbox\" value='$r[dob]' onmouseover=\"Tip('Choose a date.');\" onclick=\"fPopCalendar('tdate')\" autocomplete=off /></td>		  	
						  </tr>
						  <tr>
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
	
	function edit_dependents($form_data) {
		$objResponse = new xajaxResponse();
		
		if(!empty($form_data[dep_lname]) && !empty($form_data[dep_fname])
			&& !empty($form_data[tdate]) && !empty($form_data[dep_mname]))
			{
		   			$sql = "update dependents set
							dep_lname='$form_data[dep_lname]',
							dep_fname='$form_data[dep_fname]',
							dep_mname='$form_data[dep_mname]',
							dob='$form_data[tdate]'
						where
							dependentsID='$form_data[id]'";
			
					$query = mysql_query($sql);
			
					if($query) 
						{
							$objResponse->alert("Query Successful!");
							$objResponse->script("window.location.reload();");
						}					
					else 

							$objResponse->alert(mysql_error());
					
			}
		else 
		{
			$objResponse->alert("Fill in all fields!");
		}
		
		$objResponse->script("toggleBox('demodiv',0)");
		return $objResponse;  			   
	}
	
	
?>