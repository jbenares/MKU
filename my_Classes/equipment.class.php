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
						  	<td>Equipment Category: </td>
						  	<td>".$options->eqcat_options('')."</td>
						  </tr>
						  <tr>
						  	<td>Equipment Brand/Model: </td>
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
						  	<td>
								<input type=text name=rateperhour class=textbox><br>
								<span style='color:#FF0000;font-size:11px;'><i>Rate per hour is automatically reflected on Purchase Order Requests</i>.</span>
							</td>
						  </tr>
						  <tr>
						  	<td>Minimum Time (In Hrs): </td>
						  	<td>
								<input type=text name=mintime class=textbox>
							</td>
						  </tr>
						  <tr>
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
		
		if(!empty($form_data[eq_name]) && !empty($form_data[rateperhour]) && !empty($form_data[eq_cat])) {
		
			$id = date("Ymd-his");
			$date_exec = date("Y-m-d H:i:s");
			
			$now_array = explode("/", $form_data[Ndate]);
			$now = $now_array[2].'-'.$now_array[0].'-'.$now_array[1];
				
			$sql = "insert into equipment set
						eq_name='$form_data[eq_name]',
						eq_catID='$form_data[eq_cat]',
						eqModel='$form_data[eqModel]',
						plateNumber='$form_data[plateNumber]',
						date_of_purchase='$form_data[date_of_purchase]',
						rateperhour='$form_data[rateperhour]',
						minimum_time='$form_data[mintime]',
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
								equipment as e,
								equipment_categories as ec
							where
								eqID='$id' and
								e.eq_catID=ec.eq_catID");
						  
		$r = mysql_fetch_array($sql);
		
		if($r[date_of_purchase]=='0000-00-00') $dop = '';
		else $dop = $r[date_of_purchase];
						  
		$newContent = "<br><img src='images/user_orange.png'> UPDATE EQUIPMENT<p>
					<form id=editareaform action=javascript:void(null); onsubmit=xajax_edit_equipment(xajax.getFormValues('editareaform'));toggleBox('demodiv',1);>
						<table class=form_table>
						  <tr>
						  	<td>Equipment Name: </td>
						  	<td>
								<input type=text name=eq_name class=textbox value='$r[eq_name]'>
								<input type=hidden name=eqID class=textbox value='$id'>
							</td>
						  <tr>
						  	<td>Equipment Category: </td>
						  	<td>".$options->eqcat_options($r[eq_catID])."</td>
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
						  	<td><input type=text name=date_of_purchase  id=date_of_purchase class=textbox value='$dop' onclick='fPopCalendar(\"date_of_purchase\")'></td>
						  </tr>
						  <tr>
						  	<td>Rate Per hour: </td>
						  	<td><input type=text name=rateperhour class=textbox value='$r[rateperhour]'><br>
								<span style='color:#FF0000;font-size:11px;'><i>Rate per hour is automatically reflected on Purchase Order Requests</i>.</span>
							</td>
						  </tr>
						  <tr>
						  	<td>Minimum Time (In Hrs): </td>
						  	<td>
								<input type=text name=mintime class=textbox value='$r[minimum_time]'>
							</td>
						  </tr>
						  <tr>
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
		
		if(!empty($form_data[eq_name]) && !empty($form_data[rateperhour]) && !empty($form_data[eq_cat])){
		   
			$sql = "update equipment set
						eq_name='$form_data[eq_name]',
						eq_catID='$form_data[eq_cat]',
						eqModel='$form_data[eqModel]',
						plateNumber='$form_data[plateNumber]',
						date_of_purchase='$form_data[date_of_purchase]',
						rateperhour='$form_data[rateperhour]',
						minimum_time='$form_data[mintime]',
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
	
	function eq_search($keyaccount) {
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
											equipment as e,
											equipment_categories as ec
										where
											e.eq_name like '%$keyaccount%' and
											e.eq_catID=ec.eq_catID
										limit
											0, 20");	
											
		$row = "<table align='center' cellpadding=3 class='search_table' width='100%'>";
											
		while($r=mysql_fetch_array($query_eq)) {		
			$name = $r[eq_name].' ('.$r[eqModel].') '.$r[plateNumber].' ';
		
			$row .= '<tr bgcolor="'.$transac->row_color($ii++).'" title="Click to Select" onclick="xajax_select_eq(\''.$r[eqID].'\', \''.$name.'\');toggleBox(\'demodiv\',1);">
						<td width=15 bgcolor="#EEEEEE">'.$ii.'</td>
						<td>'.$name.'</td>
						<td>'.$r[eq_cat_name].'</td></tr>';		
 		}
		
		$row .= '</table>';
		
		$objResponse->script("toggleBox('demodiv4',1)");
		$objResponse->assign("eqdiv","innerHTML", $row);
		$objResponse->script("toggleBox('demodiv',0)");
		
		return $objResponse;
	}
	
	function select_eq($eqID, $eqname) {
		$objResponse = new xajaxResponse();	
		
		$objResponse->script("document.getElementById(\"eqname\").value='$eqname'");
		$objResponse->script("document.getElementById(\"eqID\").value='$eqID'");
		$objResponse->script("toggleBox('demodiv4',0)");
		$objResponse->script("toggleBox('demodiv',0)");
		
		return $objResponse;
	}
	
	function show_change_oil_schedules($eqID) {
		$objResponse = new xajaxResponse();	
		$options = new options();
		
		//$objResponse->alert("mike");
		
		$query_eq = mysql_query("select
										*
									from
										equipment as e,
										equipment_categories as ec
									where
										e.eqID='$eqID' and
										e.eq_catID=ec.eq_catID");	
											
		$r = mysql_fetch_array($query_eq);
		$name = $r[eq_name].' ('.$r[eqModel].') '.$r[plateNumber].' ';
											
		$row = "<br><img src='images/cog.png'> ".strtoupper($name)."<p>";
		
		if($r[change_oil_start]!='0000-00-00') {
			$get_eur = mysql_query("select
										*
									from
										eurs as e,
										purchase_order_details as pod
									where
										e.podetailID=pod.podetailID and
										e.released_datetime>'$r[change_oil_start]' and
										pod.eqID='$eqID'");
					
			$actual_total = 0;					
			while($rg = mysql_fetch_array($get_eur)) {
				$actual_ = number_format(($options->convert_datetime($rg[returned_datetime])-$options->convert_datetime($rg[released_datetime]))/3600,2);
					
				$actual_total += $actual_;
			}
		}
		
		if($actual_total>=$r[hours_limit]) 
			$show = '<span style="color:#FF0000">'.number_format($actual_total,2).' Hrs</span>';
		else
			$show = '<span style="color:#0000FF">'.number_format($actual_total,2).' Hrs</span>';
		
		$row .= '<table class=form_table>	
				  <tr>
					<td>Current operational hours based from last change oil : <p><b>'.$show.'</b></p><hr></td>
				  </tr>
				 </table>';
		$row .= "</p>";
		
		$objResponse->assign("Rdiv","innerHTML", $row);
		$objResponse->script("toggleBox('demodiv',0)");
		$objResponse->script("showBox()");
		
		return $objResponse;
	}
	
	function redirect_changeOil_jo($eqID, $name) {
		$objResponse = new xajaxResponse();	
		
		$query_job = mysql_query("select * from jobs where job='Change Oil'");
		$r_job = mysql_fetch_array($query_job);
		
		$query_jo_link = mysql_query("select view_keyword from programs where Pfilename='job_order_request.php'");
		$r_jo_link = mysql_fetch_array($query_jo_link);
		
		$objResponse->redirect("admin.php?view=$r_jo_link[view_keyword]&eqID=$eqID&equipment=$name&jobID=$r_job[job_id]&job=$r_job[job]");
		
		return $objResponse;
	}
	
	function SchedVisit($Vdate, $i) {
		$date_visit = date("Y-m-d D", strtotime($Vdate . " +$i months"));
		$dateVarray = explode(" ", $date_visit);
		$dateV = $dateVarray[0];
		$dayV = $dateVarray[1];
		
		if($dayV=='Sun') {
			$dateV = date("Y-m-d", strtotime($dateV . " +1 day"));
		}			
		else if($dayV=='Sat') {
			$dateV = date("Y-m-d", strtotime($dateV . " +2 days"));
		}
		
		return $dateV;
	}
	
?>