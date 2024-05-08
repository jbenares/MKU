<?php

	function new_tire_form() {
		$objResponse = new xajaxResponse();
		$options = new options();
		
		
		$newContent = "<br><img src='images/user_orange.png'> NEW TIRE<p>
					<form id=newareaform action=javascript:void(null); onsubmit=xajax_new_tire(xajax.getFormValues('newareaform'));toggleBox('demodiv',1);>
						<table class=form_table>	
						  <tr>
						  	<td>Branding No.: </td>
						  	<td><input type=text name=branding_no class=textbox></td>
						  </tr>
						  <tr>
						  	<td>Tire Type: </td>
						  	<td>".$options->type_options('')."</td>
						  </tr>
						  <tr>
						  	<td>Brand Name: </td>
						  	<td><input type=text name=brand_name class=textbox></td>
						  </tr>
						 
						  <tr>
						  	<td>Date of Purchase: </td>
						  	<td> <input name=purchased_date id=purchased_date class=textbox  type=text  onclick='fPopCalendar(\"purchased_date\")' /></td>
						  </tr>
						  <tr>
						  <tr>
						  	<td>Date Installed: </td>
						  	<td> <input name=installed_date id=installed_date class=textbox  type=text  onclick='fPopCalendar(\"installed_date\")' /></td>
						  </tr>
						  <tr>
						   <tr>
						  	<td>Equipment: </td>
						  	<td>".$options->eq_name('')."</td>
						  </tr>
						  <tr>
						  	<td>Size: </td>
					     	<td>".$options->getSize('')."</td>
					  </tr>
						  <tr>
						  	<td>Remarks: </td>
						  	<td><textarea name='remarks' style='overflow:hidden;width:350px;height:50px;font-size:11px;font-family:Arial;'></textarea></td>
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
	
	function new_tire($form_data) {
		$objResponse = new xajaxResponse();
		
		if(!empty($form_data[branding_no]) && !empty($form_data[equipment]) && !empty($form_data[tire_type])&& !empty($form_data[sizes])) {
		
			$id = date("Ymd-his");
			$date_exec = date("Y-m-d H:i:s");
			
			$now_array = explode("/", $form_data[Ndate]);
			$now = $now_array[2].'-'.$now_array[0].'-'.$now_array[1];
				
			$sql =  "insert into tires set
						branding_no='$form_data[branding_no]',
						type_id='$form_data[tire_type]',
						eqID='$form_data[equipment]',
						brand_name='$form_data[brand_name]',
						purchased_date='$form_data[purchased_date]',
						installed_date='$form_data[installed_date]',
						size_id='$form_data[sizes]',
						remarks='$form_data[remarks]'";
						
                             
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
	
	function edit_tire_form($id) {
		$objResponse = new xajaxResponse();
		$options = new options();
		
		
		//$objResponse->alert($id);
		
		$sql = mysql_query("select
								*
							from
								tires as t,
								tire_type as tt,
								equipment as e,
								tire_size as ts
							where
								tire_id='$id' and
								t.eqID=e.eqID and
								t.size_id=ts.size_id and
								t.type_id=tt.type_id");
						  
		$r = mysql_fetch_array($sql);
		
		if($r[purchased_date]=='0000-00-00') $dop = '';
		else $dop = $r[purchased_date];
		
						  
		$newContent = "<br><img src='images/user_orange.png'> UPDATE TIRE<p>
					<form id=editareaform action=javascript:void(null); onsubmit=xajax_edit_tire(xajax.getFormValues('editareaform'));toggleBox('demodiv',1);>
						<table class=form_table>	
						  <tr>
						  	<td>Branding No.: </td>
							<td>
						  	<input type=text name=branding_no class=textbox value='$r[branding_no]'>
								<input type=hidden name=tire_id class=textbox value='$id'>
								</td>
						  </tr>
						  <tr>
						  	<td>Tire Type: </td>
						  	<td>".$options->type_options($r[type_id])."</td>
						  </tr>
						  <tr>
						  	<td>Brand Name: </td>
						  	<td><input type=text name=brand_name class=textbox value='$r[brand_name]'></td>
						  </tr>
						
						  <tr>
						  	<td>Date of Purchase: </td>
						  	<td> <input name=purchased_date id=purchased_date class=textbox  type=text value= '$r[purchased_date]' onclick='fPopCalendar(\"purchased_date\")' /></td>
						  </tr>
						  <tr>
						  <tr>
						  	<td>Date Installed: </td>
						  	<td> <input name=installed_date id=installed_date class=textbox  type=text value= '$r[installed_date]' onclick='fPopCalendar(\"installed_date\")' /></td>
						  </tr>
						  <tr>
						  <tr>
						  	<td>Equipment: </td>
						  	<td>".$options->eq_name($r[eqID])."</td>
						  </tr>
						  <tr>
						  	<td>Size: </td>
						  	<td>".$options->getSize($r[size_id])."</td>
						  </tr>
						  <tr>
						  	<td>Remarks: </td>
						  	<td><textarea name='remarks' style='overflow:hidden;width:350px;height:50px;font-size:11px;font-family:Arial;'>$r[remarks]</textarea></td>
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
	
	function edit_tire($form_data) {
		$objResponse = new xajaxResponse();
		
		if(!empty($form_data[branding_no]) && !empty($form_data[equipment]) && !empty($form_data[tire_type])&& !empty($form_data[sizes])){
		   
			$sql = "update tires set
						branding_no='$form_data[branding_no]',
						type_id='$form_data[tire_type]',
						eqID='$form_data[equipment]',
						brand_name='$form_data[brand_name]',
						purchased_date='$form_data[purchased_date]',
						installed_date='$form_data[installed_date]',
						size_id='$form_data[sizes]',
						remarks='$form_data[remarks]'
					where
						tire_id='$form_data[tire_id]'";
			
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
	
	function tire_search($keyaccount) {
		$objResponse = new xajaxResponse();	
		$transac = new query();
		
		//$objResponse->alert($keyaccount);	
		
		if(empty($keyaccount)) {
			$objResponse->script("toggleBox('demodiv',0)");
			return $objResponse;
		}
		
		$query_tire = mysql_query("select
											*
										from
											tires as t,
											tire_type as tt,
											equipment as e,
											tire_size as ts
										where
											t.branding_no like '%$keyaccount%' and
											t.eqID=e.eqID and
											t.size_id=ts.size_id and
											t.type_id=tt.type_id
										limit
											0, 20");	
											
		$row = "<table align='center' cellpadding=3 class='search_table' width='100%'>";
											
		while($r=mysql_fetch_array($query_tire)) {		
			$name = $r[branding_no].' ('.$r[brand_name].') '.$r[type_name].' ';
		
			$row .= '<tr bgcolor="'.$transac->row_color($ii++).'" title="Click to Select" onclick="xajax_select_tire(\''.$r[type_id].'\', \''.$name.'\');toggleBox(\'demodiv\',1);">
						<td width=15 bgcolor="#EEEEEE">'.$ii.'</td>
						<td>'.$name.'</td>
						<td>'.$r[branding_no].'</td></tr>';		
 		}
		
		$row .= '</table>';
		
		$objResponse->script("toggleBox('demodiv4',1)");
		$objResponse->assign("eqdiv","innerHTML", $row);
		$objResponse->script("toggleBox('demodiv',0)");
		
		return $objResponse;
	}
	
	function select_tire($tire_id, $branding_no) {
		$objResponse = new xajaxResponse();	
		
		$objResponse->script("document.getElementById(\"branding_no\").value='$branding_no'");
		$objResponse->script("document.getElementById(\"tire_id\").value='$tire_id'");
		$objResponse->script("toggleBox('demodiv4',0)");
		$objResponse->script("toggleBox('demodiv',0)");
		
		return $objResponse;
	}
	
	
?>