<?php

	function read_report($Rid, $userID, $keyword) {
		$objResponse = new xajaxResponse();
		$options = new options();
		
		$getR = mysql_query("select
								r.id,
								r.accNum,
								r.name,
								r.Rmessage,
								r.telno,
								r.mobileno,
								r.address,
								r.channel,
								r.receivedtime,
								r.user_id,
								r.Rstatus,
								d.Dname,
								concat(a.user_fname,' ',a.user_lname) as EncodedBy
							from								
								reports as r,
								admin_access as a,
								departments as d
							where
								id='$Rid' and
								r.user_id=a.userID and
								r.department_id=d.Did");
								
		$rR = mysql_fetch_array($getR);
		
		$getTA = mysql_query("select
										concat(a.user_fname,' ',a.user_lname) as TA
									from
										job_orders as j,
										admin_access as a
									where
										j.report_id='$Rid' and
										a.userID=j.tagTakeActionBy");
										
		$TA = mysql_fetch_array($getTA);
		
		$getC = mysql_query("select
										concat(a.user_fname,' ',a.user_lname) as C
									from
										job_orders as j,
										admin_access as a
									where
										j.report_id='$Rid' and
										a.userID=j.tagCompletedBy");
										
		$C = mysql_fetch_array($getC);
		
		if($rR[Rstatus]=='Pending') {
			$actionButton = "Team Supervisor : 
							 <input type='text' id='supervisor' class='textbox'>
							 <input type='button' value='Take Action' onclick=\"take_action_confirm('$Rid','$userID',document.getElementById('supervisor').value);\" class='buttons'>";
		}
		else if($rR[Rstatus]=='In Progress') {
			$actionButton = "<input type='button' onclick=\"window.open('generate_pdf.php?report_id=$rR[id]')\" value='Print This Report' class='buttons'>
							 <input type='button' value='Tag as Completed' onclick=\"completed_confirm('$Rid','$userID','$keyword');\" class='buttons'>";
		}
		else {
			$Celapsed = mysql_query("select
										unix_timestamp(j.dateCompleted)-unix_timestamp(r.receivedtime) as elapsed,
										j.dateCompleted
									from
										reports as r,
										job_orders as j
									where
										r.id=j.report_id and
										r.id='$Rid'");
										
			$R = mysql_fetch_array($Celapsed);
			$Elapsed = $options->strTime($R[elapsed]);
		
			$actionButton = "Completed in $Elapsed";
		}
		
		$newContent = '<br>REPORT<p>
					   <table class="Rtable" cellpadding="3px">
					   		<tr>
								<td>Account # </td>
								<td> : '.$rR[accNum].'</td>
							</tr>
							<tr>
								<td>Encoded By </td>
								<td> : '.$rR[EncodedBy].'</td>
							</tr>
							<tr>
								<td>Made Action By </td>
								<td> : '.$TA[TA].'</td>
							</tr>
							<tr>
								<td>Tag Completed By </td>
								<td> : '.$C[C].'</td>
							</tr>
							<tr>
								<td>Name </td>
								<td> : '.$rR[name].'</td>
							</tr>
							<tr>
								<td>Address </td>
								<td> : '.$rR[address].'</td>
							</tr>
							<tr>
								<td>Mobile # </td>
								<td> : '.$rR[mobileno].'</td>
							</tr>
							<tr>
								<td>Tel. No. </td>
								<td> : '.$rR[telno].'</td>
							</tr>
							<tr>
								<td>Addressed To </td>
								<td> : '.$rR[Dname].'</td>
							</tr>							
							<tr>
								<td>Date / Time </td>
								<td> : '.$options->convert_sysdate($rR[receivedtime]).'</td>
							</tr>
						</table>	
						<p>Message : <hr />'.$rR[Rmessage].'<hr /></p>					
						<p align="right">'.$actionButton.'</p>';
		
		$objResponse->addAssign("Rdiv","innerHTML", $newContent);
		$objResponse->addScript("toggleBox('demodiv',0)");
		$objResponse->addScript("showBox()");
			
		return $objResponse->getXML();
	}
	
	function update_bb($access_id, $userID) {
		$objResponse = new xajaxResponse();
		
		$options = new options();			
		$transac = new query();
		
		if($access_id==3) {
			$getRF = mysql_query("select substation_id from substation_assignments where userID='$userID'");
			$rRF = mysql_fetch_array($getRF);
		
			$getR = mysql_query("select
						id,
						name,
						receivedtime,
						channel,
						priority,
						read_,
						Rstatus
					from
						reports
					where
						read_='0' and
						substation_id='$rRF[substation_id]'
					order by
						receivedtime desc
					limit 0,50");
		}
		else {
			$getR = mysql_query("select
						id,
						name,
						Rmessage,
						Rstatus,
						receivedtime,
						channel,
						priority,
						read_,
						Rstatus
					from
						reports
					where
						read_='0'
					order by
						receivedtime desc
					limit 0,50");
		}
		
		$row = '<table cellspacing="1" cellpadding="5" width="98%" align="center" class="search_table">';
		
		while($r=mysql_fetch_array($getR)) {
			$row .= '<tr bgcolor="'.$transac->row_color($i++).'">';
			
			$row .= '<td width="15">'.$i.'</td>';
			$row .= '<td width="250"><b>'.$r[name].'</b> <img src="images/new.png"><br>Via : '.$r[channel].'</td>';
			$row .= '<td width="100">'.$options->show_priority($r[priority]).'</td>';
			$row .= '<td width="150">'.$options->convert_sysdate($r[receivedtime]).'</td>';
			$row .= '<td>'.$r[Rstatus].'</td>';
			$row .= '<td width="20" title="Read Entry"><a href="javascript:void(0);" style="cursor:pointer;" onclick="xajax_read_report(\''.$r[id].'\',\''.$userID.'\',\'\');"><img src="images/page_text.gif" border="0"></a></td>';
			$row .= '<td width="20" title="Print Entry"><a href="generate_pdf.php?report_id='.$r[id].'" target="_blank"><img src="images/action_print.gif" border="0"></a></td>';
							
			$row .= '</tr>';
		}
		
		$row .= '</table>';
		
		$objResponse->addAssign("bb_div","innerHTML", $row);
		$objResponse->addScript("setTimeout(\"xajax_update_bb('".$access_id."', '".$userID."')\",1000)");
			
		return $objResponse->getXML();
	}
	
	function search_reports($keyword, $userID) {
		$objResponse = new xajaxResponse();
		
		$options = new options();			
		$transac = new query();
		
		$query = mysql_query("select
						id,
						name,
						Rmessage,
						receivedtime,
						channel,
						priority,
						read_,
						Rstatus
					from
						reports
					where
						read_='1' and
						(name like '%$keyword%'  or
						 channel like '%$keyword%' or
						 id like '%$keyword%' or
						 receivedtime like '%$keyword%')
					order by
						receivedtime desc
					limit 0,50");
						
		$row = '<table cellspacing="1" cellpadding="5" width="98%" align="center" class="search_table">';
						
		while($r=mysql_fetch_array($query)) {
			$row .= '<tr bgcolor="'.$transac->row_color($i++).'">';
			
			$row .= '<td width="15">'.$i.'</td>';
			$row .= '<td width="250"><b>'.$r[name].'</b><br>Via : '.$r[channel].' | Priority : '.$options->show_priority($r[priority]).'</td>';			
			$row .= '<td width="150">'.$options->convert_sysdate($r[receivedtime]).'</td>';
			
			if($r[Rstatus]=='Completed') {				
				$Celapsed = mysql_query("select
										unix_timestamp(j.dateCompleted)-unix_timestamp(r.receivedtime) as elapsed,
										j.dateCompleted
									from
										reports as r,
										job_orders as j
									where
										r.id=j.report_id and
										r.id='$r[id]'");
										
				$R = mysql_fetch_array($Celapsed);
				$Elapsed = $options->strTime($R[elapsed]);
				
				$statUs = $r[Rstatus].' in '.$Elapsed;
				
				$row .= '<td onmouseover="Tip(\'Date Completed : <p>'.$options->convert_sysdate($R[dateCompleted]).'</p>\')">'.$statUs.'</td>';
			}
			else {
				$statUs = $r[Rstatus];
				$row .= '<td>'.$statUs.'</td>';
			}							
				
			$row .= '<td width="20" title="Read Entry"><a href="javascript:void(0);" style="cursor:pointer;" onclick="xajax_read_report(\''.$r[id].'\',\''.$userID.'\',\''.$keyword.'\');toggleBox(\'demodiv\',1);"><img src="images/page_text.gif" border="0"></a></td>';
			$row .= '<td width="20" title="Print Entry"><a href="generate_pdf.php?report_id='.$r[id].'" target="_blank"><img src="images/action_print.gif" border="0"></a></td>';
							
			$row .= '</tr>';
		}
		
		$row .= '</table>';
		
		$objResponse->addAssign("listRdiv","innerHTML", $row);
		$objResponse->addScript("toggleBox('demodiv',0)");
		
		return $objResponse->getXML();
	}
	
	function take_action($Rid, $userID, $supervisor) {	
		$objResponse = new xajaxResponse();			
			
		if(empty($supervisor)) {
			$objResponse->addAlert("Please key in the name of the team supervisor.");
			$objResponse->addScript("toggleBox('demodiv',0)");
			
			return $objResponse->getXML();  
		}	
			
		$id = date("Ymdhis");
		
		$updateRstatus = mysql_query("update reports set
											read_='1',
											Rstatus='In Progress'
										where
											id='$Rid'");
											
		$logJob = mysql_query("insert into job_orders set
									id='$id',
									dateStarted=SYSDATE(),
									report_id='$Rid',
									tagTakeActionBy='$userID',
									supervisor='$supervisor'");
				
		$objResponse->addScript("xajax_read_report('$Rid','$userID','')");
		
		return $objResponse->getXML();  			   
	}
	
	function completed($Rid, $userID, $keyword) {	
		$objResponse = new xajaxResponse();			
		
		$updateRstatus = mysql_query("update reports set
											Rstatus='Completed'
										where
											id='$Rid'");
											
		$logJob = mysql_query("update job_orders set
									dateCompleted=SYSDATE(),
									tagCompletedBy='$userID'
								where
									report_id='$Rid'");
				
		$objResponse->addScript("xajax_read_report('$Rid','$userID','$keyword')");
		$objResponse->addScript("xajax_search_reports('$keyword', $userID)");
		
		return $objResponse->getXML();  			   
	}

?>