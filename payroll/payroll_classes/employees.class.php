<?php
require_once(dirname(__FILE__).'/../../library/lib.php');
// Add New Employee - employees.php
	function new_employeesform() {
		$objResponse   = new xajaxResponse();
		$randy_options = new randy_options();
		$options       = new options();
		
		$status_select = "
				<select name='inactive'>
					<option value=''>Select Status:</option>
					<option value='0'>Active</option>
					<option value='1'>Inactive</option>
				</select>
			";
		
		$newContent = "<br><img src='images/user_orange.png'> ADD EMPLOYEE<p>
					<form id=newemployeeform action=javascript:void(null); onsubmit=xajax_new_employees(xajax.getFormValues('newemployeeform'));toggleBox('demodiv',1);>
						<div style=\"height:500px;overflow-y:scroll;\">
						<table class=form_table width=710px>	
						  <tr>
						  	<td>
								<div style='display:inline-block;'>
									Employee No.: <br>
									<input type=text name=employeeNUM class=textbox value='".str_pad($id,7,0,STR_PAD_LEFT)."' readonly='readonly'>
									<input type=hidden name=employeeID class=textbox value='$id'>
								</div>
								<div style='display:inline-block;'>
									Date Hired: <br><input type=\"text\" name=\"hdate\" id=\"hdate\" class=\"textbox\" value='$r[datehired]' onmouseover=\"Tip('Choose a date.');\" onclick=\"fPopCalendar('hdate')\" autocomplete=off />
								</div>
								<div style='display:inline-block;'>
									Employee Type: <br>
									".$options->getTableAssoc($r['employee_type_id'],'employee_type_id','Select Employee Type',"select * from employee_type where employee_type_void = '0' order by employee_type asc",'employee_type_id','employee_type')."
								</div>
								<!--<div style='display:inline-block;'>
									Position: <br>
									<input type='text' class='textbox' name='position' value='$r[position]'>-->
								</div>
								<div style='display:inline-block;'>
									Date Enrolled: <br><input type=\"text\" name=\"date_enrolled\" id=\"date_enrolled\" class=\"textbox\" value='$r[date_enrolled]' onmouseover=\"Tip('Choose a date.');\" onclick=\"fPopCalendar('date_enrolled')\" autocomplete=off />
								</div>

								<div style='display:inline-block;'>
									Contact No: <br><input type=\"text\" name=\"contact_no\" id=\"contact_no\" class=\"textbox\"    autocomplete=off />
								</div>
							</td>
						  </tr>		
						  <tr>
							<td>
								<!--<div style='display:inline-block;'>
									Company: <br>
								".$options->getTableAssoc($r['companyID'],'companyID','Select Company',"select * from companies where company_void = '0' order by company_name asc",'companyID','company_abbrevation')."
								</div>
								<div style='display:inline-block;'>
									Division: <br>
									".$options->getTableAssoc($r['divisionID'],'divisionID','Select Division',"select * from division where division_void = '0' order by division_name asc",'divisionID','division_name')."
								</div>
								<div style='display:inline-block'>
									Project: <br>
									".$randy_options->projects_options($r[projectsID])."											
								</div>-->
								<div style='display:inline-block;'>
									Work Category :<br>
									".lib::getTableAssoc(NULL,'work_category_id',"Select Work Category","select * from work_category order by work asc",'work_category_id','work')."
								</div>
							</td>
						  </tr>	
						  <tr>
						  	<td>
						  		<div style='display:inline-block;'>
						  			Employee Bank: <br>
						  			<input type='text' name='emp_bank' class='textbox' value=''>
						  		</div>
						  		<div style='display:inline-block;'>
						  			Employee Account No.: <br>
						  			<input type='text' name='emp_account_no' class='textbox' value=''>
						  		</div>
						  	</td>
						  </tr>
						</table>
						<table class=form_table width=710px>
						  <tr>
						  	<td>First Name: <br><input type=text name=employee_fname class=textbox></td>
						  	<td>Middle Name: <br><input type=text name=employee_mname class=textbox></td>
						  	<td>Last Name: <br><input type=text name=employee_lname class=textbox></td>
						  </tr>
						  <tr>						  	
						  	<td width=220px>Gender: <br>
								<input type=radio name=sex value=M> Male
								<input type=radio name=sex value=F> Female
							</td>
							<td>Date of Birth: <br><input type=\"text\" name=\"tdate\" id=\"tdate\" class=\"textbox\" onmouseover=\"Tip('Choose a date.');\" onclick=\"fPopCalendar('tdate')\" autocomplete=off /></td>		  	
						  	<td>
						  		Number of Dependents
						  		<br />
						  		<select name=child style=width:208px>
								  <option>SELECT</option>
								  <option value=0>None</option>
								  <option value=1>1</option>
								  <option value=2>2</option>
								  <option value=3>3</option>
								  <option value=4>4 or more</option>
								</select> 
						  	</td>
						  </tr>
						  <tr>
						  	<td colspan='3'>Address: <br><input type=text name=address class=textbox5></td>
						  	<!--<td>Middle Name: <br><input type=text name=employee_mname class=textbox></td>
						  	<td>Last Name: <br><input type=text name=employee_lname class=textbox></td>-->
						  </tr>
						  
						</table>
						</table>
						<!--<table class=form_table width=710px>
						  <tr>
						  	<td><b>
						  		** Leave blank if Not Applicable
						  		<br />
								** Maiden Name if Female Spouse
						  	</b></td>
						  </tr>
						  <tr>
						  	<td>Spouse's First Name: <br><input type=text name=spouse_fname class=textbox></td>
						  	<td>Spouse's Middle Name: <br><input type=text name=spouse_mname class=textbox></td>
						  	<td>Spouse's Last Name: <br><input type=text name=spouse_maiden class=textbox></td>
						  </tr>
						</table>-->
						<table class=form_table width=710px>
						  <tr>
						  	<td>SSS No.: <br><input type=text name=sss class=textbox></td>
						  	<td>PhilHealth No.: <br><input type=text name=philhealth class=textbox></td>
						  	<td>HDMF No.: <br><input type=text name=hdmf class=textbox></td>
						  </tr>
						  <tr>
						  	<td>
								T.I.N.: 
								<br/>
								<input type=text name=tin class=textbox>
						  	</td>
							<td>
								Base Rate : <br><input type=text name=baserate class=textbox><br>
							</td>
						  	<td>
						  		Employee Status: 
						  		<br>
						  		".lib::getTableAssoc(null,'empStat',"Select Employee Status","select * from employee_status order by employee_status asc",'employee_statusID','employee_status')."								
						  	</td>
						  </tr>						  
						  <tr>
							<td></td>
							<td>
								Daily Rate : <br><input type=text name=dailyrate class=textbox><br>
							</td>
							<td></td>
						  </tr>
						  <!--<tr>
							<td>Allowance: <br><input type='text' name='allowance' class=textbox value='$r[allowance]'></td>	
							
							<td>Apply Tax: <br><input type='checkbox' name='apply_tax' value='1'></td>	
							
							<td>
								Fixed Overtime: <br>
								<input type='text' name='fixed_ot' class='textbox' value='$r[fixed_ot]'> hrs
							</td>
						  </tr>
						  <tr>
						  	<td>Apply SSS: 			<br><input type='checkbox' name='apply_sss' value='1'></td>	
							<td>Apply Philhealth: 	<br><input type='checkbox' name='apply_philhealth' value='1'></td>	
							<td>Apply HDMF: 		<br><input type='checkbox' name='apply_hdmf' value='1'></td>	
						  </tr>
						  -->
						  <tr>
						  	<td style='vertical-align:top;'>
								Status: <br>
								$status_select
							</td>
							<td colspan='2'>
								Remarks <br>
								<textarea name='employee_remarks' style='width:100%; border:1px solid #c0c0c0; font-size:11px; font-family:arial;'></textarea>
							</td>
						  </tr>
						  <tr>
						  	<td>
						  		Time-in <br>
						  		".lib::getTime('emp_time_in_hour',23,NULL)."
						  		".lib::getTime('emp_time_in_min',59,NULL)."
						  	</td>
						  	<td>	
						  		Release type <br>
						  		".lib::getTableAssoc(NULL,'release_type_id',"Select Release Type","select * from release_type order by release_type asc",'release_type_id','release_type')."
						  	</td>
						  </tr>
						</table>
						<table class=form_table width=710px>
						  <tr>
						  	<td>						  	  
						  	  <input type=submit name=b value='Submit' class=buttons>
						  	  <input type=reset value='Clear Form' class=buttons>
						  	</td>
						  </tr>
						</table>
						</div>
					</form>";							   
		
		$objResponse->assign("Rdiv","innerHTML", $newContent);
		$objResponse->script("toggleBox('demodiv',0)");
		$objResponse->script("showBox()");
		
		return $objResponse;
	}
	
	function new_employees($form_data) {
		$objResponse = new xajaxResponse();

		$form_data['emp_time_in'] = $form_data['emp_time_in_hour'] . ":" . $form_data['emp_time_in_min'];
		
		if(!empty($form_data[employee_lname])){
			
			$sql_sss = mysql_query("select 
											* 
										from 
											employee 
										where 
											sss = '$form_data[sss]' and 
											employee_void = '0'
									");
			$num_row = mysql_num_rows($sql_sss);
			
			if ($num_row == '0')
			{
			
			$inactive = ($form_data['invactive']) ? 1 : 0;
			$apply_tax = ($form_data['apply_tax']) ? 1 : 0;
			
			$apply_sss 			= ($form_data['apply_sss']) ? 1 : 0;
			$apply_philhealth 	= ($form_data['apply_philhealth']) ? 1 : 0;
			$apply_hdmf 		= ($form_data['apply_hdmf']) ? 1 : 0;
			$sql = "insert into employee set
						employeeNUM       ='$form_data[employeeNUM]',
						employee_fname    ='$form_data[employee_fname]',
						employee_mname    ='$form_data[employee_mname]',
						employee_lname    ='$form_data[employee_lname]',
						spouse_fname      ='$form_data[spouse_fname]',
						spouse_mname      ='$form_data[spouse_mname]',
						spouse_maiden     ='$form_data[spouse_maiden]',
						sex               ='$form_data[sex]',
						dateofbirth       ='$form_data[tdate]',
						datehired         ='$form_data[hdate]',
						date_enrolled     ='$form_data[date_enrolled]',
						address           ='$form_data[address]',
						tin               ='$form_data[tin]',
						sss               ='$form_data[sss]',
						philhealth        ='$form_data[philhealth]',
						hdmf              ='$form_data[hdmf]',
						dependents        ='$form_data[child]',
						base_rate         ='$form_data[baserate]',
						daily_rate        ='$form_data[dailyrate]',
						employee_statusID ='$form_data[empStat]',
						projectsID        ='$form_data[projects]',
						companyID         = '$form_data[companyID]',
						divisionID        = '$form_data[divisionID]',
						employee_type_id  = '$form_data[employee_type_id]',
						allowance         = '$form_data[allowance]',
						apply_tax         = '$apply_tax',
						fixed_ot          = '$form_data[fixed_ot]',
						inactive          = '$inactive',
						apply_sss         = '$apply_sss',
						apply_philhealth  = '$apply_philhealth',
						apply_hdmf        = '$apply_hdmf',
						position          = '$form_data[position]',
						separation_date   = '$form_data[separation_date]',
						contact_no        = '$form_data[contact_no]',
						employee_remarks  = '".addslashes($form_data['employee_remarks'])."',
						emp_bank          = '$form_data[emp_bank]',
						emp_account_no    = '$form_data[emp_account_no]',
						emp_time_in       = '$form_data[emp_time_in]',
						work_category_id  = '$form_data[work_category_id]',
						release_type_id   = '$form_data[release_type_id]'
					";
			
			$query = mysql_query($sql);	
			
			
				if(!mysql_error()) 
				{
					$objResponse->alert("Query Successful!");
					$objResponse->script("window.location.reload();");
				}					
				else
					$objResponse->alert(mysql_error());
				}
				else if ($num_row > '0')
				{
				$objResponse->alert("SSS Number alrady exists!!! Please Verify!");
				}
			
		}
		else {
			$objResponse->alert("Fill in all fields!");
		}
		
		$objResponse->script("toggleBox('demodiv',0)");
		return $objResponse;  			   
	}
	// Update employee data - employees.php
	function edit_employeesform($id) {
		$objResponse = new xajaxResponse();
		$randy_options = new randy_options();		
		$options = new options();
		//$objResponse->alert($id);
		
		$sql = mysql_query("select
								*				
							from
								employee
							where
								employeeID='$id'");
						  
		$r = mysql_fetch_array($sql);
		
		if($r[sex]=="M") $male = "checked";
		else if ($r[sex]=="F") $female = "checked";
		else {
			$male = "";
			$female = "";
		}
		
		if($r[dependents]=="0") $dependents = "None";
		else if($r[dependents]=="4") $dependents = "4 or More";
		else if($r[dependents]=="1" or "2" or "3") $dependents = $r[dependents];
		
		$status_select = "
				<select name='inactive'>
					<option value=''>Select Status:</option>
					<option value='0' ".(($r['inactive'] == 0) ? "selected='selected'": "").">Active</option>
					<option value='1' ".(($r['inactive'] == 1) ? "selected='selected'": "").">Inactive</option>
				</select>
			";

		$time_in      = $r['emp_time_in'];
		$aTimeIn      = explode(":",$time_in);
		$time_in_hour = $aTimeIn[0];
		$time_in_min  = $aTimeIn[1];

						  
		$newContent = "<br><img src='images/user_orange.png'> UPDATE EMPLOYEE<p>
					<form id=editareaform action=javascript:void(null); onsubmit=xajax_edit_employees(xajax.getFormValues('editareaform'));toggleBox('demodiv',1);>
						<div style=\"height:500px;overflow-y:scroll;\">
						<table class=form_table width=710px>
						  <tr>
						  	<td>
								<div style='display:inline-block;'>
									Employee No.: <br>
									<input type=text name=employeeNUM class=textbox value='".str_pad($id,7,0,STR_PAD_LEFT)."' readonly='readonly'>
									<input type=hidden name=employeeID class=textbox value='$id'>
								</div>
								<div style='display:inline-block;'>
									Date Hired: <br><input type=\"text\" name=\"hdate\" id=\"hdate\" class=\"textbox\" value='$r[datehired]' onmouseover=\"Tip('Choose a date.');\" onclick=\"fPopCalendar('hdate')\" autocomplete=off />
								</div>
								<div style='display:inline-block;'>
									Employee Type: <br>
									".$options->getTableAssoc($r['employee_type_id'],'employee_type_id','Select Employee Type',"select * from employee_type where employee_type_void = '0' order by employee_type asc",'employee_type_id','employee_type')."
								</div>
								<!--<div style='display:inline-block;'>
									Position: <br>
									<input type='text' class='textbox' name='position' value='$r[position]'>
								</div>-->
								<div style='display:inline-block;'>
									Date Enrolled: <br><input type=\"text\" name=\"date_enrolled\" id=\"date_enrolled\" class=\"textbox datepicker\"  onmouseover=\"Tip('Choose a date.');\" onclick=\"fPopCalendar('date_enrolled')\" autocomplete=off value='".((empty($r['date_enrolled'])) ? "" : $r['date_enrolled'])."' />
								</div>
							
								<div style='display:inline-block;'>
									Contact No: <br><input type=\"text\" name=\"contact_no\" id=\"contact_no\" class=\"textbox\"  value='$r[contact_no]'   autocomplete=off />
								</div>
							</td>
						  </tr>		
						  <tr>
							<td>
								<!--<div style='display:inline-block;'>
									Company: <br>
								".$options->getTableAssoc($r['companyID'],'companyID','Select Company',"select * from companies where company_void = '0' order by company_name asc",'companyID','company_abbrevation')."
								</div>
								<div style='display:inline-block;'>
									Division: <br>
									".$options->getTableAssoc($r['divisionID'],'divisionID','Select Division',"select * from division where division_void = '0' order by division_name asc",'divisionID','division_name')."
								</div>
								<div style='display:inline-block'>
									Project: <br>
									".$randy_options->projects_options($r[projectsID])."											
								</div>-->
								<div style='display:inline-block;'>
									Work Category :<br>
									".lib::getTableAssoc($r['work_category_id'],'work_category_id',"Select Work Category","select * from work_category order by work asc",'work_category_id','work')."
								</div>
							</td>
						  	</tr>	
						  	<tr>
						  		<td>
							  		<div style='display:inline-block;'>
							  			Employee Bank: <br>
							  			<input type='text' name='emp_bank' class='textbox' value='$r[emp_bank]'>
							  		</div>
							  		<div style='display:inline-block;'>
							  			Employee Account No.: <br>
							  			<input type='text' name='emp_account_no' class='textbox' value='$r[emp_account_no]'>
							  		</div>
						  		</td>
						  	</tr>
						</table>
						<table class=form_table width=710px>
						  <tr>
						  	<td>First Name: <br><input type=text name=employee_fname class=textbox value='$r[employee_fname]'></td>
						  	<td>Middle Name: <br><input type=text name=employee_mname class=textbox value='$r[employee_mname]'></td>
						  	<td>Last Name: <br><input type=text name=employee_lname class=textbox value='$r[employee_lname]'></td>
						  </tr>
						  <tr>						  	
						  	<td width=220px>Gender: <br>
								<input type=radio name=sex value=M $male> Male
								<input type=radio name=sex value=F $female> Female
							</td>
							<td>Date of Birth: <br><input type=\"text\" name=\"tdate\" id=\"tdate\" class=\"textbox\" value='".(($r['dateofbirth'] == "0000-00-00") ? "" : $r['dateofbirth'])."' onmouseover=\"Tip('Choose a date.');\" onclick=\"fPopCalendar('tdate')\" autocomplete=off /></td>		  	
						    <td>
						  		Number of dependents:
						  		<br />
						  		<select name=child>
								  <option value='$r[dependents]'>$dependents</option>
								  <option value>=================</option>
								  <option value=0>None</option>
								  <option value=1>1</option>
								  <option value=2>2</option>
								  <option value=3>3</option>
								  <option value=4>4 or more</option>
								</select> 
						    </td>
						  </tr>
						  <tr>
						  	<td colspan='3'>Address: <br><input type=text name=address value='$r[address]' class=textbox5></td>
						  	<!--<td>Middle Name: <br><input type=text name=employee_mname class=textbox></td>
						  	<td>Last Name: <br><input type=text name=employee_lname class=textbox></td>-->
						  </tr>
						</table>
						<!--<table class=form_table width=710px>
						  <tr>
						  	<td><b>
						  		** Leave blank if Not Applicable
						  		<br />
								** Maiden Name if Female Spouse
						  	</b></td>
						  </tr>
						  <tr>
						  	<td>Spouse's First Name: <br><input type=text name=spouse_fname class=textbox value='$r[spouse_fname]'></td>
						  	<td>Spouse's Middle Name: <br><input type=text name=spouse_mname class=textbox value='$r[spouse_mname]'></td>
						  	<td>Spouse's Last Name: <br><input type=text name=spouse_maiden class=textbox value='$r[spouse_maiden]'></td>
						  </tr>
						</table>-->
						<table class=form_table width=710px>
						  <tr>
						  	<td>SSS No.: <br><input type=text name=sss class=textbox value='$r[sss]'></td>
						  	<td>PhilHealth No.: <br><input type=text name=philhealth class=textbox value='$r[philhealth]'></td>
						  	<td>HDMF No.: <br><input type=text name=hdmf class=textbox value='$r[hdmf]'></td>
						  </tr>
						  <tr>
							<td>T.I.N.: <br><input type=text name=tin class=textbox value='$r[tin]'></td>	
						  
							<td>
								Base Rate : <br><input type=text name=baserate class=textbox value='$r[base_rate]'><br>
							</td>
						  	<td>
						  		Employee Status: 
						  		<br/>						  		
								".lib::getTableAssoc($r['employee_statusID'],'empStat',"Select Employee Status","select * from employee_status order by employee_status asc",'employee_statusID','employee_status')."								
							</td>													
						  </tr>
						  <tr>
							<td></td>
							<td>
								Daily Rate : <br><input type=text name=dailyrate class=textbox value='$r[daily_rate]'><br>
							</td>
							<td></td>
						  </tr>	  
						  <!--<tr>
							<td>Allowance: <br><input type='text' name='allowance' class=textbox value='$r[allowance]'></td>	
							
							<td>Apply Tax: <br><input type='checkbox' name='apply_tax' ".(($r['apply_tax']) ? "checked='checked'" : "")." value='1'></td>	
							
							<td>
								Fixed Overtime: <br>
								<input type='text' name='fixed_ot' class='textbox' value='$r[fixed_ot]'> hrs
							</td>
						  </tr>
						  <tr>
						  	<td>Apply SSS: 			<br><input type='checkbox' name='apply_sss' value='1' ".(($r['apply_sss']) ? "checked='checked'" : "")."></td>	
							<td>Apply Philhealth: 	<br><input type='checkbox' name='apply_philhealth' value='1' ".(($r['apply_philhealth']) ? "checked='checked'" : "")."></td>	
							<td>Apply HDMF: 		<br><input type='checkbox' name='apply_hdmf' value='1' ".(($r['apply_hdmf']) ? "checked='checked'" : "")."></td>	
						  </tr>-->
						  <tr>
						  	<td style='vertical-align:top;'>
								Status: <br>
								$status_select
							</td>
							<td colspan='2'>
								Remarks <br>
								<textarea name='employee_remarks' style='width:100%; border:1px solid #c0c0c0; font-size:11px; font-family:arial;'>$r[employee_remarks]</textarea>
							</td>
						  </tr>
						  <tr>
						  	<td>
						  		Time-in <br>
						  		".lib::getTime('emp_time_in_hour',23,$time_in_hour)."
						  		".lib::getTime('emp_time_in_min',59,$time_in_min)."
						  	</td>
						  	<td>	
						  		Release type <br>
						  		".lib::getTableAssoc($r['release_type_id'],'release_type_id',"Select Release Type","select * from release_type order by release_type asc",'release_type_id','release_type')."
						  	</td>
						  </tr>
						</table>
						<table class=form_table width=710px>
						  <tr>
						  	<td>						  	  
						  	  <input type=submit name=b value='Submit' class=buttons>
						  	  <input type=reset value='Clear Form' class=buttons>
						  	</td>
						  </tr>
						</table>
						</div>
					   </form>";					   
		
		$objResponse->assign("Rdiv","innerHTML", $newContent);
		$objResponse->script("toggleBox('demodiv',0)");
		$objResponse->script("showBox()");
		
		return $objResponse;}
	
	function edit_employees($form_data) {
		$objResponse = new xajaxResponse();
		

		$form_data['emp_time_in'] = $form_data['emp_time_in_hour'] . ":" . $form_data['emp_time_in_min'];

		if(!empty($form_data[employee_lname]) && !empty($form_data[employee_fname])){
		   
			$sql_sss = mysql_query("select * from employee where sss = '$form_data[sss]' and employee_void='0'");

			$num_row = mysql_num_rows($sql_sss);
			
					if ($num_row <= '1')
					{
					
					$apply_tax = ($form_data['apply_tax']) ? 1 : 0;
					$inactive = ($form_data['inactive']) ? 1 : 0;
					$apply_sss 			= ($form_data['apply_sss']) ? 1 : 0;
					$apply_philhealth 	= ($form_data['apply_philhealth']) ? 1 : 0;
					$apply_hdmf 		= ($form_data['apply_hdmf']) ? 1 : 0;
					$sql = "update employee set
								employeeNUM       ='$form_data[employeeNUM]',
								employee_fname    ='$form_data[employee_fname]',
								employee_mname    ='$form_data[employee_mname]',
								employee_lname    ='$form_data[employee_lname]',
								spouse_fname      ='$form_data[spouse_fname]',
								spouse_mname      ='$form_data[spouse_mname]',
								spouse_maiden     ='$form_data[spouse_maiden]',
								sex               ='$form_data[sex]',
								dateofbirth       ='$form_data[tdate]',
								datehired         ='$form_data[hdate]',
								date_enrolled     ='$form_data[date_enrolled]',
						        address           ='$form_data[address]',
								tin               ='$form_data[tin]',
								sss               ='$form_data[sss]',
								philhealth        ='$form_data[philhealth]',
								hdmf              ='$form_data[hdmf]',
								hdmf              ='$form_data[hdmf]',
								dependents        ='$form_data[child]',
								base_rate         ='$form_data[baserate]',
								daily_rate        ='$form_data[dailyrate]',
								employee_statusID ='$form_data[empStat]',
								/*projectsID      ='$form_data[projects]',
								companyID         = '$form_data[companyID]',*/
								divisionID        = '$form_data[divisionID]',
								employee_type_id  = '$form_data[employee_type_id]',
								allowance         = '$form_data[allowance]',
								apply_tax         = '$apply_tax',
								fixed_ot          = '$form_data[fixed_ot]',
								inactive          = '$inactive',
								apply_sss         = '$apply_sss',
								apply_philhealth  = '$apply_philhealth',
								apply_hdmf        = '$apply_hdmf',
								/*position          = '$form_data[position]',*/
								separation_date   = '$form_data[separation_date]',
								contact_no        = '$form_data[contact_no]',
								employee_remarks  = '".addslashes($form_data['employee_remarks'])."',
								emp_bank          = '$form_data[emp_bank]',
								emp_account_no    = '$form_data[emp_account_no]',
								emp_time_in       = '$form_data[emp_time_in]',							
								work_category_id  = '$form_data[work_category_id]',
								release_type_id = '$form_data[release_type_id]'
							where
							employeeID='$form_data[employeeID]'";
			
					$query = mysql_query($sql);
			
					if($query) 
						{
							$objResponse->alert("Query Successful!");
							$objResponse->script("window.location.reload();");
						}					
					else 

							$objResponse->alert(mysql_error());
					}
				else if ($num_row >= '1' && $sql_sss[employeeID] !== '$form_data[employeeID]')
					{
							$objResponse->alert("SSS Number already exists!!! Please Verify!");
					}
			}
		else 
		{
			$objResponse->alert("Fill in all fields!");
		}
		
		$objResponse->script("toggleBox('demodiv',0)");
		return $objResponse;  			   
	}
	// search employee record - dtr.php
	function show_employees($keyWord) {
		$objResponse = new xajaxResponse();
		$transac = new query();
		
		if(!empty($keyWord)) {		
			$objResponse->script("document.getElementById('employeeID').value='';");
		
			$queryC = mysql_query("select
										*
									from
										employee
									where
										employee_lname like '$keyWord%' and
										employee_void='0'
									limit
										0, 20");
			
			$row = '<table width="100%">';		
								
			while($rC=mysql_fetch_array($queryC)) {
				if(empty($rC[projectsID])) continue;
				
				$getEmpStat = mysql_query("select * from employee_status where employee_statusID='$rC[employee_statusID]'");
				$rES = mysql_fetch_array($getEmpStat);
				
				$getProject = mysql_query("select * from projects where project_id='$rC[projectsID]'");
				$rP = mysql_fetch_array($getProject);
			
				$row .= '<tr bgcolor="'.$transac->row_color($i++).'">
							<td width=20 align=center><img src="images/user_orange.png"></td>
						 	<td style="border-bottom:#C0C0C0 1px dashed;">
						   	<div style="cursor:pointer;padding:3px;" onmouseover="Tip(\'Click to select.\');" onclick="document.getElementById(\'employee_keyword\').value=\''.htmlentities($rC[employee_lname]).', '.$rC[employee_fname].' '.$rC[employee_mname][0].'\';document.getElementById(\'employeeID\').value=\''.$rC[employeeID].'\';toggleBox(\'demodiv3\',0);"><b>'.htmlentities($rC[employee_lname]).', '.$rC[employee_fname].' '.$rC[employee_mname][0].'.</b><br>('.$rC[employeeID].')<br>'.$rPC[project_name].' ('.$rES[employee_status].')
						   	</div>
						 	</td>
						 </tr>';
			}	
			
			$row .= '</table>';
			
			$objResponse->script("toggleBox('demodiv3',1)");
			$objResponse->assign("employeediv","innerHTML", $row);
		}
				
		$objResponse->script("toggleBox('demodiv',0)");
		
		return $objResponse; 
	}
	
	function show_employees_car($keyWord) {
		$objResponse = new xajaxResponse();
		$transac = new query();
		
		//$objResponse->alert("mike");
		
		if(!empty($keyWord)) {		
			$queryC = mysql_query("select
										*
									from
										employees
									where
										employee_lname like '$keyWord%' and
										emp_void='0'
									limit
										0, 20");
			
			$row = '<table width="100%">';		
								
			while($rC=mysql_fetch_array($queryC)) {
				$getEmpStat = mysql_query("select * from employee_status where empStatID='$rC[empStatID]'");
				$rES = mysql_fetch_array($getEmpStat);
				
				$getPayCenter = mysql_query("select * from payroll_center where payrollcenterID='$rC[payrollcenterID]'");
				$rPC = mysql_fetch_array($getPayCenter);
			
				$row .= '<tr bgcolor="'.$transac->row_color($i++).'">
							<td width=20 align=center><img src="images/user_orange.png"></td>
						 	<td style="border-bottom:#C0C0C0 1px dashed;">
						   	<div style="cursor:pointer;padding:3px;" onmouseover="Tip(\'Click to select.\');" onclick="document.getElementById(\'employee_keyword_car\').value=\''.htmlentities($rC[employee_lname]).', '.$rC[employee_fname].' '.$rC[employee_mname][0].'\';document.getElementById(\'empID_car\').value=\''.$rC[empID].'\';toggleBox(\'demodiv4\',0);"><b>'.htmlentities($rC[employee_lname]).', '.$rC[employee_fname].' '.$rC[employee_mname][0].'('.$rC[employeeID].')</b><br>'.$rPC[payrollcenter].' ('.$rES[empStat].')
						   	</div>
						 	</td>
						 </tr>';
			}	
			
			$row .= '</table>';
			
			$objResponse->script("toggleBox('demodiv4',1)");
			$objResponse->assign("employeediv_car","innerHTML", $row);
		}
				
		$objResponse->script("toggleBox('demodiv',0)");
		
		return $objResponse; 
	}
	
	function put_thesameEMP($ifchecked, $empkey, $empID) {
		$objResponse = new xajaxResponse();
		
		if($ifchecked) {
			$objResponse->script("document.getElementById('employee_keyword_car').value='$empkey';
								  document.getElementById('empID_car').value='$empID';");
		}
		else {
			$objResponse->script("document.getElementById('employee_keyword_car').value='';
								  document.getElementById('empID_car').value='';");
		}
		
		$objResponse->script("toggleBox('demodiv',0)");
		
		return $objResponse;
	}
	
	function view_ca_ledger($empID, $fdate, $tdate) {
		$objResponse = new xajaxResponse();
		$transac = new query();
		
		//$objResponse->alert($fdate.' * '.$tdate.' *'.$empID);
		
		$get_emp = mysql_query("select
									  *
								 from
									  employees as e,
									  payroll_center as p
								 where
									  e.empID='$empID' and
									  e.payrollcenterID=p.payrollcenterID and
									  e.emp_void='0'");
									  
		$r = mysql_fetch_array($get_emp);

		$get_cash_advances = mysql_query("select
												*
											from
												cash_advances
											where
												empID='$r[empID]' and
												ca_date between '$fdate' and '$tdate' and
												ca_void='0'
											order by
												ca_date asc,
												caID asc
											limit
												0,100");
											
		while($rca=mysql_fetch_array($get_cash_advances)) {
			if($rca[ca_period_from]=='0000-00-00' && $rca[ca_period_to]=='0000-00-00') $remarks = "";
			else $remarks = date("M j, Y", strtotime($rca[ca_period_from])).' - '.date("M j, Y", strtotime($rca[ca_period_to]));
			
			if(empty($rca[debit])) $debit = "";
			else $debit = number_format($rca[debit],2);
			
			if(empty($rca[credit])) $credit = "";
			else $credit = number_format($rca[credit],2);
		
			$solve_balance = mysql_query("select
												sum(debit) as sub_debit,
												sum(credit) as sub_credit
											from
												cash_advances
											where
												caID<='$rca[caID]' and
												empID='$r[empID]' and
												ca_void='0'
											group by
												empID");
												
			$rsb = mysql_fetch_array($solve_balance);
		
			$balance = $rsb[sub_debit] - $rsb[sub_credit];
		
			$row .= '<tr bgcolor="'.$transac->row_color($i++).'">
						<td style="border-right:1px dashed #C0C0C0;border-bottom: 1px dashed #C0C0C0;">'.$i.'.</td>
						<td style="border-right:1px dashed #C0C0C0;border-bottom: 1px dashed #C0C0C0;">'.date("M j, Y", strtotime($rca[ca_date])).'</td>
						<td style="border-right:1px dashed #C0C0C0;border-bottom: 1px dashed #C0C0C0;">'.$debit.'</td>
						<td style="border-right:1px dashed #C0C0C0;border-bottom: 1px dashed #C0C0C0;">'.$credit.'</td>
						<td style="border-right:1px dashed #C0C0C0;border-bottom: 1px dashed #C0C0C0;">'.number_format($balance, 2).'</td>
						<td style="border-right:1px dashed #C0C0C0;border-bottom: 1px dashed #C0C0C0;">'.$remarks.'</td>
					 </tr>';
		}

		$newContent = "<br><img src='images/table.png'> CASH ADVANCE LEDGER						
						<table class=form_table width=700px>
						<tr>
							<td style='border-right:1px dashed #C0C0C0;'>Employee : <span style='color:#FF0000;'>$r[employee_lname], $r[employee_fname] $r[employee_mname]</span></td>
							<td style='border-right:1px dashed #C0C0C0;'>Status : <span style='color:#FF0000;'>$r[empStat]</span></td>	
							<td style='border-right:1px dashed #C0C0C0;'>Payroll Center : <span style='color:#FF0000;'>$r[payrollcenter]</span></td>						
						</tr>
						</table>
						<div style='border-bottom:2px #C0C0C0 solid;height:300px;overflow-y:scroll;overflow-x:hidden;background:#EEEEEE;color:#000000;padding:5px;'>
						<table class=form_table width=672px>
						<tr>
							<td style='border-right:1px dashed #C0C0C0;border-bottom: 1px dashed #C0C0C0;font-weight:bold;background:#C0C0C0;' width=20>#</td>
							<td style='border-right:1px dashed #C0C0C0;border-bottom: 1px dashed #C0C0C0;font-weight:bold;background:#C0C0C0;'>DATE</td>
							<td style='border-right:1px dashed #C0C0C0;border-bottom: 1px dashed #C0C0C0;font-weight:bold;background:#C0C0C0;' width=100>DEBIT</td>
							<td style='border-right:1px dashed #C0C0C0;border-bottom: 1px dashed #C0C0C0;font-weight:bold;background:#C0C0C0;' width=100>CREDIT</td>
							<td style='border-right:1px dashed #C0C0C0;border-bottom: 1px dashed #C0C0C0;font-weight:bold;background:#C0C0C0;' width=100>BALANCE</td>	
							<td style='border-right:1px dashed #C0C0C0;border-bottom: 1px dashed #C0C0C0;font-weight:bold;background:#C0C0C0;'>REMARKS</td>	
						</tr>
						".$row."
						</table>
						</div>";
		
		$objResponse->assign("Rdiv","innerHTML", $newContent);
		$objResponse->script("toggleBox('demodiv',0)");
		$objResponse->script("showBox()");
		
		return $objResponse; 
	}
	
?>