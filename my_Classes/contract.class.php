<?php
	
	function new_contract($employeeID, $employeeName) {
		$objResponse = new xajaxResponse();
		$options = new options();
		$randy_options = new randy_options();
		$options       = new options();
		
		//$objResponse->alert($PCode);
		
		$newContent = "<br><img src='images/user_add.png'> ADD CONTRACT(".strtoupper($employeeName).")<p>
					<form id=edit_function action=javascript:void(null); onsubmit=xajax_save_contract(xajax.getFormValues('edit_function'));toggleBox('demodiv',1);>
						<table class=form_table border=0>
							<input type='hidden' name='employeeID' value='$employeeID'>
						  <tr>
						  	<td>Contract #: </td>
						  	<td>
								<input type=text name=contract_num class=textbox3>
							</td>
						  </tr>
						  <tr>
							<td>Effectivity Date: </td>
						  	<td >
								<input type=text name=date_hired class='textbox3 datepicker'>
							</td>
							<td style='text-align:right;'>Position: </td>
						  	<td >
								<input type=text name=pos class='textbox3'>
							</td>
						  </tr>
						  <tr>
						  	<td>Separation Date: </td>
						  	<td>
								<input type=text name=separation_date class='textbox3 datepicker'>
							</td>
							</td>
							<td style='text-align:right;'>Status: </td>
						  	<td >
								<input type=text name=status class='textbox3'>
							</td>
						  </tr>
						  <tr>
							<td colspan=3>
								<div style='display:inline-block;'>
										Company: <br>
									".$options->getTableAssoc($r['companyID'],'companyID','Select Company',"select * from companies where company_void = '0' order by company_name asc",'companyID','company_abbrevation')."
									</div>
									<div style='display:inline-block'>
										Project: <br>
										".$randy_options->projects_options($r[projectsID])."											
								</div>
							</td>
						  </tr>
						  <tr>
						  	<td></td>
							<td></td>
						  	<td>						  	  
						  	  <input type=submit name=b value='Submit' class=buttons>
						  	  <input type=reset value='Clear Form' class=buttons>
						  	</td>
						</table>
					   </form><br>CONTRACTS<hr><div id='funclistdiv' style='padding:5px;color:#5e6977;background:#EEEEEE;height:150px;overflow-y:scroll;overflow-x:hidden;'></div>";	
		$newContent.="
					<style>
						#box,.form_table,#funclistdiv{
							width:700px;
						}
					</style>
					";
		
		$objResponse->assign("Rdiv","innerHTML", $newContent);
		$objResponse->script("xajax_show_contracts('$employeeID')");
		$objResponse->includeScript("scripts/cwcalendar/calendar.js", "text/javascript", $sId = null);
		$objResponse->script("j(\".datepicker\").each(function(){j(this).datepicker({ dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true });});");
		$objResponse->includeCSS("scripts/cwcalendar/cwcalendar.css", "screen");
		$objResponse->script("toggleBox('demodiv',0)");
		$objResponse->script("showBox()");
		
		return $objResponse;	
	}
	
	function save_contract($form_data) {
		$objResponse = new xajaxResponse();
		
		if(!empty($form_data[contract_num])) {
			
			$date_hired = $form_data[date_hired];
				
			$sql = "insert into employee_contracts set
					contract_num='$form_data[contract_num]',
					employeeID='$form_data[employeeID]',
					effectivity_date='$date_hired',
					position='$form_data[pos]',
					status='$form_data[status]',
					date_added = NOW(),
					companyID = '$form_data[companyID]',
					projectsID = '$form_data[projects]'
					";
			
			$query = mysql_query($sql);
			
			$objResponse->script("toggleBox('demodiv',0)");
			
			if($query) {
				$objResponse->script("xajax_show_contracts('$form_data[employeeID]')");
			}					
			else
				$objResponse->alert(mysql_error());
		}
		else {
			$objResponse->script("toggleBox('demodiv',0)");
			$objResponse->alert("Fill in all fields!");
		}
		
		return $objResponse;  			   
	}
	
	function deleteContract($FID, $PCode) {
		$objResponse = new xajaxResponse();
		
		$removeF = mysql_query("delete from employee_contracts where contract_id='$FID'");
											
		$objResponse->script("xajax_show_contracts('$PCode')");
		$objResponse->script("toggleBox('demodiv',0)");
				
		return $objResponse;  			   
	}
	
	function show_contracts($PCode) {
		$objResponse = new xajaxResponse();
		$options = new options();
		
		$get_functions = mysql_query("select
											*
										from
											employee_contracts
										where
											employeeID='$PCode'
										order by
											contract_id desc");
			
		$separation_date = "";	
		while($rFunctions=mysql_fetch_array($get_functions)) {
			if($rFunctions[separation_date] == "0000-00-00 00:00:00"){
				$separation_date = "None";
			}else{
				$separation_date = $rFunctions[separation_date];
			}
			$row.='<div style="padding:3px;border-bottom:#C0C0C0 1px dashed;">
					<img src=\'images/edit.gif\' style=\'cursor:pointer;\' onclick="xajax_EditContract(\''.$rFunctions[contract_id].'\',\''.$PCode.'\');toggleBox(\'demodiv\',1);" title="Edit Contract">
				   <img src=\'images/trash.gif\' style=\'cursor:pointer;\' onclick="xajax_deleteContract(\''.$rFunctions[contract_id].'\',\''.$PCode.'\');toggleBox(\'demodiv\',1);" title="Remove"> 
				   <b><u>'.$options->getAttribute("companies","companyID",$rFunctions[companyID],"company_abbrevation").' - 
				   '.$options->getAttribute("projects","project_id",$rFunctions[projectsID],"project_name").' - '.$rFunctions[position].' - '.$rFunctions[status].' </u></b>
				   <br/>
				   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				   <b>Contract:</b> '.$rFunctions[contract_num].' - 
				   <b>Effictivity Date:</b> '.$rFunctions[effectivity_date].' 	- 
				   <b>Separation Date:</b> '.$separation_date.'
				   </div>';
		}
		
		$newContent = $row;
		
		$objResponse->assign("funclistdiv","innerHTML", $newContent);
		$objResponse->script("toggleBox('demodiv',0)");
				
		return $objResponse;  			   
	}
	
	function EditContract($contract_id,$employeeID){
		$objResponse = new xajaxResponse();
		$options = new options();
		$randy_options = new randy_options();
		$employeeName = $options->getAttribute("employee","employeeID",$employeeID,"employee_lname").', '.$options->getAttribute("employee","employeeID",$employeeID,"employee_fname").' '.$options->getAttribute("employee","employeeID",$employeeID,"employee_mname");
		
		$sql="select * from employee_contracts where contract_id = '$contract_id'";
		$r=mysql_fetch_assoc(mysql_query($sql));
		
		//$objResponse->alert($PCode);
		
		$newContent = "<br><img src='images/user_add.png'> EDIT CONTRACT(".strtoupper($employeeName).")<p>
					  <form id=save_function action=javascript:void(null); onsubmit=xajax_edit_contract(xajax.getFormValues('save_function'));toggleBox('demodiv',1);>
						<table class=form_table>
							<input type='hidden' name='employeeID' value='$employeeID'>
							<input type='hidden' name='contract_id' value='$contract_id'>
							<tr>
								<td>Contract #: </td>
								<td>
									<input type=text name=contract_num class=textbox value='$r[contract_num]'>
								</td>
							 </tr>
							  <tr>
								<td>Effectivity Date: </td>
								<td>
									<input type=text name=date_hired class='textbox3 datepicker' value='$r[effectivity_date]'>
								</td>
								<td style='text-align:right;'>Position: </td>
							  	<td >
									<input type=text name=pos class='textbox3' value='$r[position]'>
								</td>
							  </tr>
							  <tr>
								<td>Separation Date: </td>
								<td align=left>
									<input type=text name=separation_date class='textbox3 datepicker' value='$r[separation_date]'>
								</td>
								</td>
							<td style='text-align:right;'>Status: </td>
						  	<td >
								<input type=text name=status class='textbox3' value='$r[status]'>
							  </tr>
							  <tr>
								<td colspan=3>
									<div style='display:inline-block;'>
											Company: <br>
										".$options->getTableAssoc($r['companyID'],'companyID','Select Company',"select * from companies where company_void = '0' order by company_name asc",'companyID','company_abbrevation')."
										</div>
										<div style='display:inline-block'>
											Project: <br>
											".$randy_options->projects_options($r[projectsID])."											
									</div>
								</td>
							  </tr>
							  
						  <tr>
						  	<td></td>
						  	<td>						  	  
						  	  <input type=submit name=b value='Submit' class=buttons>
						  	  <input type=reset value='Clear Form' class=buttons>
						  	</td>
						  </tr>
						</table>
					   </form><br>CONTRACTS<hr><div id='funclistdiv' style='padding:5px;color:#5e6977;background:#EEEEEE;height:150px;overflow-y:scroll;overflow-x:hidden;'></div>";	
		$newContent.="
					<style>
						#box,.form_table,#funclistdiv{
							width:700px;
						}
					</style>
					";
		
		$objResponse->assign("Rdiv","innerHTML", $newContent);
		$objResponse->script("xajax_show_contracts('$employeeID')");
		$objResponse->includeScript("scripts/cwcalendar/calendar.js", "text/javascript", $sId = null);
		$objResponse->script("j(\".datepicker\").each(function(){j(this).datepicker({ dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true });});");
		$objResponse->includeCSS("scripts/cwcalendar/cwcalendar.css", "screen");
		$objResponse->script("toggleBox('demodiv',0)");
		$objResponse->script("showBox()");
		
		return $objResponse;
	}
	
	function edit_contract($form_data) {
		$objResponse = new xajaxResponse();
		
		if(!empty($form_data[separation_date])) {
				
			$sql = "update employee_contracts set
					separation_date = '$form_data[separation_date]',
					contract_num='$form_data[contract_num]',
					effectivity_date='$form_data[date_hired]',
					position='$form_data[pos]',
					status='$form_data[status]',
					companyID = '$form_data[companyID]',
					projectsID = '$form_data[projects]'
					where
						contract_id = '$form_data[contract_id]'
					";
			
			$query = mysql_query($sql);
			
			$objResponse->script("toggleBox('demodiv',0)");
			
			if($query) {
				$objResponse->script("xajax_show_contracts('$form_data[employeeID]')");
			}					
			else
				$objResponse->alert(mysql_error());
		}
		else {
			$objResponse->script("toggleBox('demodiv',0)");
			$objResponse->alert("Fill in all fields!");
		}
		
		return $objResponse;  			   
	}
	
?>