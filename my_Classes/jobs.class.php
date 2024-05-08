<?php
	
	function new_jobform() {
		$objResponse = new xajaxResponse();
		$options = new options();
		
		$newContent = "<br><img src='images/wrench.png'> NEW JOB<p>
					<form id=newjobform action=javascript:void(null); onsubmit=xajax_new_job(xajax.getFormValues('newjobform'));toggleBox('demodiv',1);>
						<table class=form_table>	
						  <tr>
						  	<td>Job: </td>
						  	<td><textarea name='job' style='overflow:hidden;width:350px;height:100px;font-size:11px;font-family:Arial;'></textarea></td>
						  </tr>	
						  <tr>
						  	<td>Job Type: </td>
						  	<td>".$options->jobtype_options('')."</td>
						  </tr>
						  <tr>
						  	<td>Standard Time:</td>
							<td><input type=text name=standard_time class=text3 /> <span style='color:#FF0000;'>minutes</span></td>
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
	
	function new_job($form_data) {
		$objResponse = new xajaxResponse();
		
		if(!empty($form_data[job]) && !empty($form_data[job_type]) && $form_data[standard_time]) {
				
			$sql = "insert into dynamic.jobs set
						job='$form_data[job]',
						job_typeID='$form_data[job_type]',
						s_time='$form_data[standard_time]'";
			
			$query = mysql_query($sql);
			
			$objResponse->script("toggleBox('demodiv',0)");
			
			if($query) {
				$objResponse->alert("Query Successful!");
				$objResponse->script("window.location.reload();");
			}					
			else
				$objResponse->alert(mysql_error());
		}
		else {
			$objResponse->script("toggleBox('demodiv',0)");
			$objResponse->alert("Fill in all fields!");
		}
		
		return $objResponse;}
	
	function edit_jobform($id) {
		$objResponse = new xajaxResponse();
		$options = new options();
		
		//$objResponse->alert($id);
		
		$sql = mysql_query("select
								*					
							from
								dynamic.jobs
							where
								job_id=$id");
						  
		$r = mysql_fetch_array($sql);
						  
		$newContent = "<br><img src='images/wrench.png'> UPDATE JOB<p>
					<form id=editjobform action=javascript:void(null); onsubmit=xajax_edit_job(xajax.getFormValues('editjobform'));toggleBox('demodiv',1);>
						<table class=form_table>
						  <tr>
						  	<td>Job: </td>
						  	<td>
								<textarea name='job' style='overflow:hidden;width:350px;height:100px;font-size:11px;font-family:Arial;'>$r[job]</textarea>
								<input type=hidden name=jobID value='$r[job_id]'>
							</td>
						  </tr>	
						  <tr>
						  	<td>Job Type: </td>
						  	<td>".$options->jobtype_options($r[job_typeID])."</td>
						  </tr>
						  <tr>
						  	<td>Standard Time:</td>
							<td><input type=text name=standard_time value='$r[s_time]' class=text3 /> <span style='color:#FF0000;'>minutes</span></td>
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
	
	function edit_job($form_data) {
		$objResponse = new xajaxResponse();
		
		if(!empty($form_data[jobID]) && !empty($form_data[job])) {
				
			$sql = "update dynamic.jobs set
						job='$form_data[job]',
						job_typeID='$form_data[job_type]',
						s_time='$form_data[standard_time]'
					where
						job_id='$form_data[jobID]'";
			
			$query = mysql_query($sql);
			
			$objResponse->script("toggleBox('demodiv',0)");
			
			if($query) {
				$objResponse->alert("Query Successful!");
				$objResponse->script("window.location.reload();");
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
	
	function job_search($keyaccount) {
		$objResponse = new xajaxResponse();	
		$transac = new query();
		
		//$objResponse->alert($keyaccount);	
		
		if(empty($keyaccount)) {
			$objResponse->script("toggleBox('demodiv',0)");
			return $objResponse;
		}
		
		$query_eq = mysql_query("select
											*
										from
											dynamic.jobs as j,
											dynamic.job_types as jt
										where
											j.job like '%$keyaccount%' and
											j.job_typeID=jt.job_typeID
										limit
											0, 20");	
											
		$row = "<table align='center' cellpadding=3 class='search_table' width='100%'>";
											
		while($r=mysql_fetch_array($query_eq)) {		
			$name = $r[job];
		
			$row .= '<tr bgcolor="'.$transac->row_color($ii++).'" title="Click to Select" onclick="xajax_select_job(\''.$r[job_id].'\', \''.$name.'\');toggleBox(\'demodiv\',1);">
						<td width=15 bgcolor="#EEEEEE">'.$ii.'</td>
						<td>'.$name.'</td>
						<td>'.$r[job_type].'</td>
						<td>'.$r[s_time].' mins</td></tr>';		
 		}
		
		$row .= '</table>';
		
		$objResponse->script("toggleBox('demodiv5',1)");
		$objResponse->assign("jobdiv","innerHTML", $row);
		$objResponse->script("toggleBox('demodiv',0)");
		
		return $objResponse;
	}
	
	function select_job($jobID, $jobname) {
		$objResponse = new xajaxResponse();	
		
		$objResponse->script("document.getElementById(\"job_name\").value='$jobname'");
		$objResponse->script("document.getElementById(\"jobID\").value='$jobID'");
		$objResponse->script("toggleBox('demodiv5',0)");
		$objResponse->script("toggleBox('demodiv',0)");
		
		return $objResponse;
	}

?>