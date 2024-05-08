<?php

$b 						= $_REQUEST['b'];
$msg					= $_REQUEST['msg'];
$filter					= $_REQUEST['filter'];
$header_saved			= $_REQUEST['header_saved'];
$employee_keyword		= $_REQUEST['employee_keyword'];
$employee_keyword_car	= $_REQUEST['employee_keyword_car'];
$checkList 				= $_REQUEST['checkList'];	
$tdate					= $_REQUEST['tdate'];
$employeeID_car			= $_REQUEST['employeeID_car'];
$dtrID					= $_REQUEST['dtrID'];

$field_number			= $_REQUEST['field_number'];
$dayStatID				= $_REQUEST['dayStatID'];
$wc						= $_REQUEST['wc'];
$wc_car					= $_REQUEST['wc_car'];
$work_value				= $_REQUEST['work_value'];
$incentives				= $_REQUEST['incentives'];

// verified	
$employeeID				= $_REQUEST['employeeID'];
$empStat				= $_REQUEST['empStat'];

$hrs_ot					= $_REQUEST['hrs_ot'];

$checkIFgiac = mysql_query("select * from employee where employeeID='$employeeID'");
$rfg = mysql_fetch_array($checkIFgiac);

if($b=='Delete Selected') {
  if(!empty($checkList)) {
	foreach($checkList as $ch) {	
		mysql_query("update dtr set dtr_void='1' where dtrID='$ch'");
	}
  }
  
  $b = 'List DTR Entries';
}
else if( $b=='Save & Add Another Entry' ) {
	if( !empty($work_value) && !empty($tdate) && !empty($employeeID) ) {
		
			$employee_statusID 	= $options->getAttribute('employee','employeeID',$employeeID,'employee_statusID');
			$no_of_days			= $options->getAttribute('employee_status','employee_statusID',$employee_statusID,'no_of_days');
			
			#PLACE PROVISION FOR AMOUNT OF WORK DONE LATER
			$saved_rate = $options->getAttribute('employee','employeeID',$employeeID,'base_rate');
			$saved_hourly_rate = ($saved_rate / $no_of_days) / 8;
			
			if($work_value >= 8){		
				$actual_rate = $saved_hourly_rate * 8 ;
			}else{
				$actual_rate = $saved_hourly_rate * $work_value;
			}
			
			$saved_allowance_rate	= $options->getAttribute('employee','employeeID',$employeeID,'allowance');
		
			mysql_query("
				insert into 
					dtr 
				set
					employeeID='$employeeID',
					dtr_date='$tdate',
					employee_statusID='$employee_statusID',
					saved_rate='$saved_rate',
					work_value='$work_value',											
					userID='$registered_userID',
					saved_allowance_rate = '$saved_allowance_rate',
					actual_rate = '$actual_rate',
					hrs_ot = '$hrs_ot'
			") or die(mysql_error());
				
			header("location: admin.php?view=$view&b=Add Details&employeeID=$employeeID&employee_keyword=$employee_keyword&msg=DTR Added.");
	}
	else {
		$msg = "Fill in required fields!";
		$b = "Add Details";
	}
}
else if($b=='Update DTR') {		
	$employee_statusID = $options->getAttribute('employee','employeeID',$employeeID,'employee_statusID');
	
	#PLACE PROVISION FOR AMOUNT OF WORK DONE LATER
	if($employee_statusID==1) { // Daily					
		$saved_rate = $options->getAttribute('employee','employeeID',$employeeID,'base_rate');
	}
	else if( $employee_statusID ==2 ) { // Monthly
		$saved_rate = $options->getAttribute('employee','employeeID',$employeeID,'base_rate');
	}
	
	$saved_rate = $options->getAttribute('dtr','dtrID',$dtrID,'saved_rate');
	
	if($work_value >= 8){		
		$actual_rate = $saved_rate * (8 / 8);
	}else{
		$actual_rate = $saved_rate * ($work_value / 8);
	}
	
	$query = mysql_query("update dtr set
								dtr_date='$tdate',
								work_value='$work_value',
								actual_rate = '$actual_rate',
								hrs_ot = '$hrs_ot'
							where
								dtrID='$dtrID'") or die(mysql_error());		
								
	$header_saved = true;
}
else if($b=='Add Details') {
	unset($tdate, $field_number, $dayStatID, $wc, $work_value, $pacquiao_unit, $employeeID_car, $employee_keyword_car);
}
else if($b=='New') {
	header("location: admin.php?view=$view");
}

if($header_saved) {
	$get_rr = mysql_query("select
									*
								from
									dtr
								where
									dtrID='$dtrID'");	
									
	$r_rr = mysql_fetch_array($get_rr);
	
	}
	
?>
<form name="_form" action="admin.php?view=<?=$view;?>" method="post">
<div class=form_layout>
	<div class="module_title"><img src='images/table.png'><?=$transac->getMname($view);?></div>
    <div class="module_actions" style="background:#FFFFCC;">
    		<img src="images/user_orange.png" />
            <input type="text" autocomplete="off" id="employee_keyword" name="employee_keyword" class="textbox" onclick="this.select();" onkeyup="xajax_show_employees(document.getElementById('employee_keyword').value);toggleBox('demodiv',1);" onmouseover="Tip('Type a keyword to search employee.');" value="<?=$employee_keyword;?>" style="color:#0000CC" />
            <div id='demodiv3' class='demo3'><a style='cursor: pointer' onclick="toggleBox('demodiv3',0);">
            <img src='images/close.gif' style='position:absolute;right:-4px;top:-4px;'></a><br />
            <div id='employeediv' style='overflow-y:scroll;overflow-x:hidden;height:250px;border-top:1px #C0C0C0 dashed;'></div></div> 
            <input type="hidden" name="employeeID" id="employeeID" value="<?=$employeeID;?>" />
            <input type="submit" name="b" value="Add Details" class="buttons" />
            <input type="submit" name="b" class="buttons" value="List DTR Entries">            
            <input type="submit" name="b" value="Delete Selected" onclick="return approve_confirm();" class="buttons" />
            <input type="submit" name="b" value="New" class="buttons" />            
    </div>
    <?php if(!empty($msg)) echo '<div id="status_update" class="ui-state-highlight ui-corner-all" style="padding: 0px 0.7em; text-align:left;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right:.3em;"></span> '.$msg.'</p></div>'; ?>
    <?php if($b=='List DTR Entries') { ?>
    <div class="module_actions">
	<?php
        $sql = mysql_query("select
                                *				
                            from
                                employee as e
                            where
                                e.employeeID='$employeeID'");
								
        
        $rpc = mysql_fetch_array($sql);
        
        echo '<div style="background:#FFFFCC;padding:3px;font-weight:bold;border:1px #C0C0C0 solid;">PAYROLL CENTER : <u><span style="color:#00CC00;">'.strtoupper($rpc[payrollcenter]).'</span></u></b></div>';
    ?>
    </div>
    	<?php
		$page = $_REQUEST['page'];
		if(empty($page)) $page = 1;
		 
		$limitvalue = $page * $limit - ($limit);

		$sql = "select
					*
				from
					dtr as d, employee as e
				where
					d.employeeID = e.employeeID
				and
					e.employee_void = '0'
				and
					d.dtr_void='0'

		";
		
		if(!empty($employeeID)){
		$sql .="
				and
					d.employeeID = '$employeeID'
		";
		}
		
		$sql.="
				order by
					dtr_date desc";
					
		$pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
				
		$i=$limitvalue;
		$rs = $pager->paginate();
		?>
        <div class="pagination">
        	<?=$pager->renderFullNav("$view&b=$b&employeeID=$employeeID")?>
        </div>
        <table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table">            
            <tr bgcolor="#C0C0C0">				
              <td width="20"><b>#</b></td>
              <td width="20" align="center"><input type="checkbox"  name="checkAll" value="Comfortable with" onclick="javascript:check_all('_form', this)" title="Check/Uncheck All" /></td>
              <td width="15"></td>
              <td><b>EMPLOYEE</b></td>
              <td><b>DATE</b></td>
              <td style="text-align:right;"><b>WORK VALUE</b></td>
              <td style="text-align:right;"><b>HRS OT</b></td>
              <td style="text-align:right;"><b>BASE RATE</b></td>
              <td style="text-align:right;"><b>ACTUAL RATE</b></td>
              <td><b>STATUS</b></td>
            </tr>
            <?php  
			while($r=mysql_fetch_assoc($rs)) { 
				echo '<tr>';
				echo '<td width="20">'.++$i.'</td>';
				echo '<td width="15"><a href="admin.php?view='.$view.'&dtrID='.$r['dtrID'].'&header_saved=1" title="Show Details"><img src="images/edit.gif" border="0"></a></td>';
				echo '<td><input type="checkbox" name="checkList[]" value="'.$r['dtrID'].'" onclick="document._form.checkAll.checked=false"></td>';
				echo '<td>'."$r[employee_lname], $r[employee_fname]".'</td>';	
				echo '<td>'.$r['dtr_date'].'</td>';	
				echo '<td style="text-align:right;">'.$r['work_value'].'</td>';	
				echo '<td style="text-align:right;">'.$r['hrs_ot'].'</td>';	
				echo '<td style="text-align:right;">'.$r['saved_rate'].'</td>';	
				echo '<td style="text-align:right;">'.$r['actual_rate'].'</td>';	
				if($r['closed']==0) $status = '<span style="color:#00CC00;">Open</span>';
					else $status = '<span style="color:#FF0000;">Closed</span>';
				echo '<td>'.$status.'</td>';	
				echo '</tr>';	
        	} 
			?>
        </table>
        <div class="pagination">
        	<?=$pager->renderFullNav("$view&b=$b&employeeID=$employeeID")?>
        </div>
    <?php }
	   else {
	   		if(($b=="Add Details" && !empty($employeeID)) || !empty($dtrID)) { // Add Details
	?>
	
	
    <div class="module_actions">
    	<?php
		if(empty($dtrID)){
		$sql = mysql_query("select
								*				
							from
								employee as e,
								projects as p
							where
								e.employeeID = '$employeeID' and
								e.projectsID = p.project_id");
			$rpc = mysql_fetch_array($sql);
		}else{
			$sql = mysql_query("
						select
							*				
						from
							dtr as d,
							employee as e,
							projects as p
						where
							d.employeeID = e.employeeID 
						and e.projectsID = p.project_id
						and dtrID = '$dtrID' 
						");
				$rpc = mysql_fetch_array($sql);
				$tdate 		= $rpc['dtr_date'];
				$employeeID = $rpc['employeeID'];
				$work_value = $rpc['work_value'];
				$hrs_ot		= $rpc['hrs_ot'];
		}
							
		
		
		echo '<div style="background:#FFFFCC;padding:3px;font-weight:bold;border:1px #C0C0C0 solid;">PROJECTS : <u><span style="color:#00CC00;">'.strtoupper($rpc[project_name]).'</span></u></b></div>';
		?>
    	<div style="background:#000000;color:#FFFFFF;padding:3px;"><img src="images/table_add.png" /> <b>DTR DETAILS</b></div>
    	<table>
            <tr>               
                <td>Date : <br />
                  <input type="text" name="tdate" id="tdate" class="textbox datepicker" value="<?=$tdate;?>" readonly="readonly" />
                  <input type="hidden" name="dtrID" value="<?=$dtrID;?>" />
                </td>
             </tr>
         	<tr>              
                <td>
                	Employee Status : (Default is shown)<br />
                    <?php 
					$empStat = $options->getAttribute('employee','employeeID',$employeeID,'employee_statusID');
					$employee_status  = $options->getAttribute('employee_status','employee_statusID',$empStat,'employee_status');
					?>
                    <input type="text" class="textbox" value="<?=$employee_status?>"  readonly="readonly" />
                	
                </td>          
           	</tr>
            <tr>
            	<td>
                	Work Value : (in Hours or Paquiao Unit) <br />
                    <input type="text" name="work_value" class="textbox" value="<?=$work_value;?>" />
                </td>
            </tr>
            <tr>
            	<td>
                	Hrs OT: <br />
                    <input type="text" name="hrs_ot" class="textbox" value="<?=$hrs_ot?>" />
                </td>
            </tr>
        </table>
        <table>
        	<tr>
            	<td>
                	<?php
						if($header_saved)
							echo '<input type="submit" name="b" value="Update DTR" class="buttons" />';
						else
							echo '<input type="submit" name="b" value="Save & Add Another Entry" class="buttons" />';
					?>                	
                </td>
            </tr>
        </table>
 </div>
    <?php
			}
    	}
	?>  
</div>
</form>