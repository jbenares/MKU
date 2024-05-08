<?php
	
	function violations($employeeID, $employeeName) {
		$objResponse = new xajaxResponse();
		$options = new options();
		$randy_options = new randy_options();
		$options       = new options();
		
		//$objResponse->alert($PCode);
		
		$newContent = "<br><img src='images/user_add.png'> ADD  VIOLATIONS (".strtoupper($employeeName).")<p>
					<form id=edit_function action=javascript:void(null); onsubmit=xajax_save_violations(xajax.getFormValues('edit_function'));toggleBox('demodiv',1);>
						<table class=form_table border=0>
							<input type='hidden' name='employeeID' value='$employeeID'>
						  <tr>
						  	<td>Memo Number </td>
						  	<td>
								<input type=text name=memo_num class=textbox3>
							</td>
							<td>AWOL Date </td>
						  	<td >
								<input type=text name=date_awol class='textbox3 datepicker'>
							</td>
						  </tr>
						  <td>Violation </td>
						  	<td>
								<input type=text name=violation1 class=textbox3>
							</td>
							<td>Termination Date </td>
						  	<td >
								<input type=text name=date_termination class='textbox3 datepicker'>
							</td>
						  <tr>
						   </tr>
						   <tr>
						  <td>Action Taken </td>
						  	<td>
								".$options->getTableAssoc($r['actionID'],'actionID','Select Action',"select * from disc_action where 1 = 1 order by ac_desc asc",'actionID','ac_desc')."
							</td>
							<td>Date Resigned </td>
						  	<td >
								<input type=text name=date_resigned class='textbox3 datepicker'>
							</td>
						  </tr>
						  <tr>
						  	<td colspan=4>
								From:&nbsp;&nbsp;<input type=text name=suspension_from class='textbox3 datepicker'>
								To:&nbsp;&nbsp;<input type=text name=suspension_to class='textbox3 datepicker'>
						
							</td>
						  </tr>
						  <tr>
						  <td>Poject / Department </td>
						  	<td>
								".$randy_options->projects_options($r[projectsID])."	
							</td>
						  </tr>
						   
						 <!-- <tr>
							<td>Effectivity Date: </td>
						  	<td >
								<input type=text name=date_hired class='textbox3 datepicker'>
							</td>
							<td style='text-align:right;'>Position: </td>
						  	<td >
								<input type=text name=pos class='textbox3'>
							</td>
						  </tr>-->
						  <!--<tr>
						  	<td>Separation Date: </td>
						  	<td>
								<input type=text name=separation_date class='textbox3 datepicker'>
							</td>
							</td>
							<td style='text-align:right;'>Status: </td>
						  	<td >
								<input type=text name=status class='textbox3'>
							</td>
						  </tr>-->
						  <!--<tr>
							<td colspan=3>
								<div style='display:inline-block;'>
										Action: <br>
									".$options->getTableAssoc($r['actionID'],'actionID','Select Action',"select * from disc_action where 1 = 1 order by ac_desc asc",'actionID','ac_desc')."
									</div>
									<div style='display:inline-block'>
										Project: <br>
										".$randy_options->projects_options($r[projectsID])."											
								</div>
							</td>
						  </tr>-->
						  <tr>	
							<td>					  	  
						  	  <input type=submit name=b value='Submit' class=buttons>
						  	  <input type=reset value='Clear Form' class=buttons>
						  	</td>
						</table>
					   </form><br>VIOLATIONS<hr><div id='funclistdiv' style='padding:5px;color:#5e6977;background:#EEEEEE;height:150px;overflow-y:scroll;overflow-x:hidden;'></div>";	
		$newContent.="
					<style>
						#box,.form_table,#funclistdiv{
							width:700px;
						}
					</style>
					";
		
		$objResponse->assign("Rdiv","innerHTML", $newContent);
		$objResponse->script("xajax_show_violations('$employeeID')");
		$objResponse->includeScript("scripts/cwcalendar/calendar.js", "text/javascript", $sId = null);
		$objResponse->script("j(\".datepicker\").each(function(){j(this).datepicker({ dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true });});");
		$objResponse->includeCSS("scripts/cwcalendar/cwcalendar.css", "screen");
		$objResponse->script("toggleBox('demodiv',0)");
		$objResponse->script("showBox()");
		
		return $objResponse;	
	}
	
	function save_violations($form_data) {
		$objResponse = new xajaxResponse();
		
		if(!empty($form_data[memo_num])) {
			
			#$date_hired = $form_data[date_hired];
				
			$sql = "insert into emp_violations set
					memo_num='$form_data[memo_num]',
					employeeID='$form_data[employeeID]',
					actionID='$form_data[actionID]',
					violation1 = '$form_data[violation1]',
					date_added = NOW(),
					projectsID = '$form_data[projects]',
					date_awol = '$form_data[date_awol]',
					date_termination = '$form_data[date_termination]',
					date_resigned = '$form_data[date_resigned]',
					suspension_from = '$form_data[suspension_from]',
					suspension_to = '$form_data[suspension_to]',
					violation2 = '$form_data[violation2]'
					";
			
			$query = mysql_query($sql);
			
			$objResponse->script("toggleBox('demodiv',0)");
			
			if($query) {
				$objResponse->script("xajax_show_violations('$form_data[employeeID]')");
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
	
	function deleteViolations($FID, $PCode) {
		$objResponse = new xajaxResponse();
		
		$removeF = mysql_query("delete from emp_violations where violation_id='$FID'");
											
		$objResponse->script("xajax_show_violations('$PCode')");
		$objResponse->script("toggleBox('demodiv',0)");
				
				
		return $objResponse;  			   
	}
	
	function show_violations($PCode) {
		$objResponse = new xajaxResponse();
		$options = new options();
		
		$get_functions = mysql_query("select
											*
										from
											emp_violations
										where
											employeeID='$PCode'
										order by
											violation_id desc");
			
		//$separation_date = "";	
		while($rFunctions=mysql_fetch_array($get_functions)) {
			/*if($rFunctions[separation_date] == "0000-00-00 00:00:00"){
				$separation_date = "None";
			}else{
				$separation_date = $rFunctions[separation_date];
			}*/
			$row.='<div style="padding:3px;border-bottom:#C0C0C0 1px dashed;">
					<img src=\'images/edit.gif\' style=\'cursor:pointer;\' onclick="xajax_EditViolations(\''.$rFunctions[violation_id].'\',\''.$PCode.'\');toggleBox(\'demodiv\',1);" title="Edit Violation">
				   <img src=\'images/trash.gif\' style=\'cursor:pointer;\' onclick="xajax_deleteViolations(\''.$rFunctions[violation_id].'\',\''.$PCode.'\');toggleBox(\'demodiv\',1);" title="Remove"> 
				   <b><u>'.$options->getAttribute("projects","project_id",$rFunctions[projectsID],"project_name").'</u> </b> -
				   <b>'.$rFunctions[memo_num].' </b> - 
				   <b>Violation:</b> '.$rFunctions[violation1].' - 
				  <b>Action Taken:</b> '.$options->getAttribute("disc_action","actionID",$rFunctions[actionID],"ac_desc").'-
				  <b>From:</b> '.$rFunctions[suspension_from].' - <b>To:</b> '.$rFunctions[suspension_to].'<br>
				  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				  <b>Awol:</b> '.$rFunctions[date_awol].' - <b>Termination:</b> '.$rFunctions[date_termination].' - <b>Resign:</b> '.$rFunctions[date_resigned].'
				   </div>';
		}
		
		$newContent = $row;
		
		$objResponse->assign("funclistdiv","innerHTML", $newContent);
		$objResponse->script("toggleBox('demodiv',0)");
				
		return $objResponse;  			   
	}
	
	function EditViolations($violation_id,$employeeID){
		$objResponse = new xajaxResponse();
		$options = new options();
		$randy_options = new randy_options();
		$employeeName = $options->getAttribute("employee","employeeID",$employeeID,"employee_lname").', '.$options->getAttribute("employee","employeeID",$employeeID,"employee_fname").' '.$options->getAttribute("employee","employeeID",$employeeID,"employee_mname");
		
		$sql="select * from emp_violations where violation_id = '$violation_id'";
		$r=mysql_fetch_assoc(mysql_query($sql));
		
		//$objResponse->alert($PCode);
		
		$newContent = "<br><img src='images/user_add.png'> EDIT VIOLATIONS (".strtoupper($employeeName).")<p>
					  <form id=save_function action=javascript:void(null); onsubmit=xajax_edit_violations(xajax.getFormValues('save_function'));toggleBox('demodiv',1);>
						<table class=form_table>
							<input type='hidden' name='employeeID' value='$employeeID'>
							<input type='hidden' name='violation_id' value='$violation_id'>
							<tr>
						  	<td>Memo Number </td>
						  	<td>
								<input type=text name=memo_num class=textbox3 value='$r[memo_num]'>
							</td>
							<td>AWOL Date </td>
						  	<td >
								<input type=text name=date_awol class='textbox3 datepicker' value='$r[date_awol]'>
							</td>
						  </tr>
						  <td>Violation </td>
						  	<td>
								<input type=text name=violation1 class=textbox3 value='$r[violation1]'>
							</td>
							<td>Termination Date </td>
						  	<td >
								<input type=text name=date_termination class='textbox3 datepicker' value='$r[date_termination]'>
							</td>
						  <tr>
						   </tr>
						   <tr>
						  <td>Action Taken </td>
						  	<td>
								".$options->getTableAssoc($r['actionID'],'actionID','Select Action',"select * from disc_action order by ac_desc asc",'actionID','ac_desc')."
							</td>
							<td>Date Resigned </td>
						  	<td >
								<input type=text name=date_resigned class='textbox3 datepicker' value='$r[date_resigned]'>
							</td>
						  </tr>
						  <tr>
						  	<td colspan=4>
								From:&nbsp;&nbsp;<input type=text name=suspension_from class='textbox3 datepicker' value='$r[suspension_from]'>
								To:&nbsp;&nbsp;<input type=text name=suspension_to class='textbox3 datepicker' value = '$r[suspension_to]'>
						
							</td>
						  </tr>
						  <tr>
						  <td>Poject / Department </td>
						  	<td>
								".$randy_options->projects_options($r[projectsID])."	
							</td>
						  </tr>
							  <!--<tr>
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
							  </tr>-->
							  
						  <tr>
						  	<td></td>
						  	<td>						  	  
						  	  <input type=submit name=b value='Submit' class=buttons>
						  	  <input type=reset value='Clear Form' class=buttons>
						  	</td>
						  </tr>
						</table>
					   </form><br>VIOLATIONS<hr><div id='funclistdiv' style='padding:5px;color:#5e6977;background:#EEEEEE;height:150px;overflow-y:scroll;overflow-x:hidden;'></div>";	
		$newContent.="
					<style>
						#box,.form_table,#funclistdiv{
							width:700px;
						}
					</style>
					";
		
		$objResponse->assign("Rdiv","innerHTML", $newContent);
		$objResponse->script("xajax_show_violations('$employeeID')");
		$objResponse->includeScript("scripts/cwcalendar/calendar.js", "text/javascript", $sId = null);
		$objResponse->script("j(\".datepicker\").each(function(){j(this).datepicker({ dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true });});");
		$objResponse->includeCSS("scripts/cwcalendar/cwcalendar.css", "screen");
		$objResponse->script("toggleBox('demodiv',0)");
		$objResponse->script("showBox()");
		
		return $objResponse;
	}
	
	function edit_violations($form_data) {
		$objResponse = new xajaxResponse();
		
		
				
			$sql = "update emp_violations set
					memo_num='$form_data[memo_num]',
					employeeID='$form_data[employeeID]',
					actionID='$form_data[actionID]',
					violation1 = '$form_data[violation1]',
					projectsID = '$form_data[projects]',
					date_awol = '$form_data[date_awol]',
					date_termination = '$form_data[date_termination]',
					date_resigned = '$form_data[date_resigned]',
					suspension_from = '$form_data[suspension_from]',
					suspension_to = '$form_data[suspension_to]',
					violation2 = '$form_data[violation2]'
					where
					violation_id = '$form_data[violation_id]'
					";
			
			$query = mysql_query($sql);
			
			$objResponse->script("toggleBox('demodiv',0)");
			
			if($query) {
				$objResponse->script("xajax_show_violations('$form_data[employeeID]')");
			}					
			else
				$objResponse->alert(mysql_error());
		
		return $objResponse;  			   
	}
	
?>