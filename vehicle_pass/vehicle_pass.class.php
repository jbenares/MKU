<?php
	
	function new_vehiclepassform() {
		$objResponse = new xajaxResponse();
		$vh_options = new vehicle_pass_options();
		$options = new options();

		$newContent = "<br><img src='images/user_add.png'> NEW VEHICLE PASS<p>
					<form id=newuserform action=javascript:void(null); onsubmit=xajax_new_vehiclepass(xajax.getFormValues('newuserform'));>
						<table class=form_table>
						  <tr>
						  	<td>Date: </td>
						  	<td>
						  		<input type=text id=encoded_date name=vh_date class=textbox onclick='fPopCalendar(\"encoded_date\")'>
						  	</td>
						  </tr>
					       <tr>
						  	<td>P.O. Number: </td>
						  	<td>
						  		<input type=text name=po_number class=textbox>
						  	</td>
						  </tr>
						  <tr>
						  	<td>Driver: </td>
						  	<td>".$vh_options->driver_options('')."</td>
						  </tr>	
						     <td>Time Out: </td>
						     <td>
							".$vh_options->list_hours('start_hour', "").":
							".$vh_options->list_mins('start_min', "")."
						     </td>
						  <tr>						    
						  	<td>Heavy Equipment: </td>
						     <td>".$vh_options->he_options('')."</td>
						  </tr>
					       <tr>						    
						     <td>Purpose: </td>
						     <td>".$vh_options->vh_purpose_options('')."</td>
					       </tr>
					     <tr>						    
						     <td>Remarks: </td>
						     <td><textarea name=remarks rows=10 cols=50></textarea></td>
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
		$objResponse->script("showBox()");
		
		return $objResponse;
	}
	
	function new_vehiclepass($form_data) {
		$objResponse = new xajaxResponse();

		$registered_userID = $_SESSION[userID];		
		
		if(!empty($form_data[vh_date]) && !empty($form_data[start_hour]) && !empty($form_data[start_min])
			&& !empty($form_data[driver]) && !empty($form_data[he]) && !empty($form_data[vh_purpose]) && !empty($form_data[po_number])) {

			//$objResponse->alert($form_data[po_number]);

		       $time_out = $form_data[start_hour].":".$form_data[start_min];
				
			$sql = "insert into vehicle_pass set
					vh_date='$form_data[vh_date]',
					vh_time_out='$time_out',
					driverID='$form_data[driver]',
					stock_id='$form_data[he]',
				      vh_purpose_id='$form_data[vh_purpose]',
				      po_header_id='$form_data[po_number]',
				      userID='$registered_userID',
				    vh_remarks='$form_data[remarks]'";
			
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
		
		return $objResponse;  			   
	}
	
	function edit_vehiclepassform($id) {
		$objResponse = new xajaxResponse();
		$vh_options = new vehicle_pass_options();
		
		//$objResponse->alert($id);

		$getR= mysql_query("select * from vehicle_pass where vh_number='$id'");					  
		$r = mysql_fetch_array($getR);
		
	       $time_part = explode(":",$r[vh_time_out]);
						  
		$newContent = "<br><img src='images/user_add.png'> UPDATE USER<p>
					<form id=edituserform action=javascript:void(null); onsubmit=xajax_edit_vehiclepass(xajax.getFormValues('edituserform'));toggleBox('demodiv',1);>
						<table class=form_table>
						  <tr>
						  	<td>Date: </td>
						  	<td>
						  		<input type=text id=encoded_date name=vh_date class=textbox onclick='fPopCalendar(\"encoded_date\")' value='".$r[vh_date]."'>
								<input type=hidden name=vh_number value=$id>
						  	</td>
						  </tr>
					       <tr>
						  	<td>P.O. Number: </td>
						  	<td>
						  		<input type=text name=po_number class=textbox value='".$r[po_header_id]."'>
						  	</td>
						  </tr>
						  <tr>
						  	<td>Driver: </td>
						  	<td>".$vh_options->driver_options($r[driverID])."</td>
						  </tr>	
						     <td>Time Out: </td>
						     <td>
							".$vh_options->list_hours('start_hour', $time_part[0]).":
							".$vh_options->list_mins('start_min', $time_part[1])."
						     </td>
						  <tr>						    
						  	<td>Heavy Equipment: </td>
						     <td>".$vh_options->he_options($r[stock_id])."</td>
						  </tr>
					       <tr>						    
						     <td>Purpose: </td>
						     <td>".$vh_options->vh_purpose_options($r[vh_purpose_id])."</td>
					       </tr>
					     <tr>						    
						     <td>Remarks: </td>
						     <td><textarea name=remarks rows=10 cols=50>".$r[vh_remarks]."</textarea></td>
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
		$objResponse->script("showBox()");
		
		return $objResponse;	
	}
	
	function edit_vehiclepass($form_data) {
		$objResponse = new xajaxResponse();

	       $time_out = $form_data[start_hour].":".$form_data[start_min];	
		
				
		$sql = "update vehicle_pass set
					vh_date='$form_data[vh_date]',
				      vh_time_out='$time_out',
				      driverID='$form_data[driver]',
				      stock_id='$form_data[he]',
				      vh_purpose_id='$form_data[vh_purpose]',
				      po_header_id='$form_data[po_number]',
				      vh_remarks='$form_data[remarks]'
				where
					vh_number='$form_data[vh_number]'";
			
		$query = mysql_query($sql);
			
		$objResponse->script("toggleBox('demodiv',0)");
			
		if($query) {
			$objResponse->alert("Query Successful!");
			$objResponse->script("window.location.reload();");
		}					
		else
			$objResponse->alert(mysql_error());
		
		return $objResponse;  			   
	}
	
	class vehicle_pass_options {
				
		function driver_options($id) {
			$sql = "select * from drivers where driverID!='$id' order by driver_name";
			$query = mysql_query($sql);		
			
			while($r=mysql_fetch_array($query)) {
				if($id==$r[driverID]) continue;
				
				$string_row.='<option value="'.$r[driverID].'">'.$r[driver_name].'</option>';
			}
			
			if(!empty($id)) {
			  $sql = "select * from drivers where driverID='$id'";	
			  $query = mysql_query($sql);
			  $result = mysql_fetch_array($query);
				
			  return '<select name=driver class=select><option value="'.$result[driverID].'">'.$result[driver_name].'</option>
				<option>= = = = = = = = = = = = = = = = = = = = </option>'.$string_row.'</select>';
		    }
			else
			  return '<select name=driver class=select><option value=0>Please Select Driver...</option>'.$string_row.'</select>';
		}

		function he_options($id) {
			$sql = "select * from productmaster where stock_id!='$id' and categ_id1='25' order by stock";
			$query = mysql_query($sql);		
			
			while($r=mysql_fetch_array($query)) {
				if($id==$r[stock_id]) continue;
				
				$string_row.='<option value="'.$r[stock_id].'">'.$r[stock].'</option>';
			}
			
			if(!empty($id)) {
			  $sql = "select * from productmaster where stock_id='$id'";	
			  $query = mysql_query($sql);
			  $result = mysql_fetch_array($query);
				
			  return '<select name=he class=select><option value="'.$result[stock_id].'">'.$result[stock].'</option>
				<option>= = = = = = = = = = = = = = = = = = = = </option>'.$string_row.'</select>';
		    }
			else
			  return '<select name=he class=select><option value=0>Please Select H.E…</option>'.$string_row.'</select>';
		}

		function vh_purpose_options($id) {
			$sql = "select * from vehicle_pass_purpose where vh_purpose_id!='$id' order by vh_purpose_id";
			$query = mysql_query($sql);		
			
			while($r=mysql_fetch_array($query)) {
				if($id==$r[vh_purpose_id]) continue;
				
				$string_row.='<option value="'.$r[vh_purpose_id].'">'.$r[vh_purpose_description].'</option>';
			}
			
			if(!empty($id)) {
			  $sql = "select * from vehicle_pass_purpose where vh_purpose_id='$id'";	
			  $query = mysql_query($sql);
			  $result = mysql_fetch_array($query);
				
			  return '<select name=vh_purpose class=select><option value="'.$result[vh_purpose_id].'">'.$result[vh_purpose_description].'</option>
				<option>= = = = = = = = = = = = = = = = = = = = </option>'.$string_row.'</select>';
		    }
			else
			  return '<select name=vh_purpose class=select><option value=0>Please Select Purpose...</option>'.$string_row.'</select>';
		}

		function list_hours($name, $value) {
			for($i=0;$i<=23;$i++) {
				$h = str_pad($i, 2, "0", STR_PAD_LEFT);
				
				if($h==$value && $h!=00) continue;
				$hours .= "<option value=$h>$h</option>";
			}		
			
			if(!empty($value)) {
				$row = "<select name=$name>
						<option value=$value>$value</option>
						<option> = = =</option>
						$hours
						</select>";
			}
			else {
				$row = "<select name=$name>
						$hours
						</select>";
			}
					
			return $row;
		}
		
		function list_mins($name, $value) {
			for($i=0;$i<=59;$i++) {
				$h = str_pad($i, 2, "0", STR_PAD_LEFT);
				
				if($h==$value && $h!=00) continue;
				$mins .= "<option value=$h>$h</option>";
			}		
			
			if(!empty($value)) {
				$row = "<select name=$name>
						<option value=$value>$value</option>
						<option> = = =</option>
						$mins
						</select>";
			}
			else {
				$row = "<select name=$name>
						$mins
						</select>";
			}
					
			return $row;
		}
		
	}

?>