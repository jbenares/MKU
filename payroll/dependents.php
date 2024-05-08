<?php

	$b 						= $_REQUEST['b'];
	$filter					= $_REQUEST['filter'];
	$header_saved			= $_REQUEST['header_saved'];
	$employee_keyword		= $_REQUEST['employee_keyword'];
	$employee_keyword_car	= $_REQUEST['employee_keyword_car'];
	$checkList 				= $_REQUEST['checkList'];	
	$tdate					= $_REQUEST['tdate'];
	$empID_car				= $_REQUEST['empID_car'];
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
	
	$bdate					= $_REQUEST['bdate'];
	$emplname				= $_REQUEST['emplname'];
	$empmname				= $_REQUEST['empmname'];
	$empfname				= $_REQUEST['empfname'];
	$splname				= $_REQUEST['splname'];
	$spmname				= $_REQUEST['spmname'];
	$spfname				= $_REQUEST['spfname'];
	$deplname				= $_REQUEST['deplname'];
	$depmname				= $_REQUEST['depmname'];
	$depfname				= $_REQUEST['depfname'];
	
	
	$checkIFgiac = mysql_query("select * from employee where employeeID='$employeeID'");
	$rfg = mysql_fetch_array($checkIFgiac);

	if($b=='Delete Details') {
	  if(!empty($checkList)) {
		foreach($checkList as $ch) {	
			mysql_query("update dependents set dependents_void='1' where dependentsID='$ch'");
		}
	  }
	  
	  $b = 'Display Details';
	}
	else if($b=='Save Details' && !empty($bdate)) {
		
		$query = mysql_query("insert into dependents
										(
										employeeID,
										dep_lname,
										dep_mname,
										dep_fname,
										dob
										)
										values
										(
										'$employeeID',
										'$deplname',
										'$depmname',
										'$depfname',
										'$bdate'
										)
										");
											
		unset($deplname, $depmname, $depfname, $bdate); 

		$b='Display Details';	
		
	}
	else if($b=='Display Details') {

	$header_saved = true;
	
	}
	else if($b=='New') {
		header("location: admin.php?view=$view");
	}
	
	if($header_saved) {
		$get_rr = mysql_query("select
										*
									from
										employee
									where
										employeeID='$employeeID'");	
										
		$r_rr = mysql_fetch_array($get_rr);
		
		$emplname			 = $r_rr[employee_lname];
		$empmname			 = $r_rr[employee_mname];
		$empfname			 = $r_rr[employee_fname];
		$splname			 = $r_rr[spouse_maiden];
		$spmname			 = $r_rr[spouse_mname];
		$spfname			 = $r_rr[spouse_fname];
		$empnum				 = $r_rr[employeeNUM];
		
/*		$get_cs = mysql_query("select
									*
								from
									carabao_shares as cs,
									employees as e
								where
									cs.dtrID='$dtrID' and
									cs.empID=e.empID");
									
		$rcs = mysql_fetch_array($get_cs);
		
		$empID_car 				= $rcs[empID];
		$employee_keyword_car	= $rcs[employee_lname].', '.$rcs[employee_fname].' '.$rcs[employee_mname][0];
		$wc_car					= $rcs[workID];
		
		if($empID_car==$empID) $check_thesame = 'checked';
		else $check_thesame = '';
*/	}
	
?>

<?php
//	echo $employeeID."<br />".$deplname.", ".$depfname;
?>
<script language="JavaScript" src="scripts/cwcalendar/calendar.js"></script>
<link rel="stylesheet" href="scripts/cwcalendar/cwcalendar.css"></link>
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
            <input type="submit" name="b" value="Display Details" class="buttons" />
            <input type="submit" name="b" value="New" class="buttons" />            
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <?php if($b=="Display Details" && !empty($employeeID)) { ?>
	
    <div class="module_actions">
    	<?php
        	$sql = mysql_query("select
									*				
								from
									employee 
								where
									employeeID = '$employeeID'
									");
								
			
			$rpc = mysql_fetch_array($sql);
		?>
    	<div style="background:#000000;color:#FFFFFF;padding:3px;"><img src="images/table_add.png" /> <b>EMPLOYEE'S RECORD</b></div>
    	<table>
            <tr>               
                <td>Employee Number : <br />
					<input type=text name="empnum" class="textbox" value="<?=$empnum;?>" id="empnum" style="background:#EEEEEE;font-weight:bold;" readonly="readonly">
                  <input type="hidden" name="dtrID" value="<?=$dtrID;?>" />
                </td>
             </tr>
			<tr>
				<td>
					<b>Employees's Data</b>
				</td>
			</tr>
            <tr>
           		<td>
					Last Name: <br />
					<input type=text name="emplname" class="textbox" value="<?=$emplname;?>" id="emplname" style="background:#EEEEEE;font-weight:bold;" readonly="readonly">
				</td>
				<td>First Name: <br />
					<input type=text name="empfname" class="textbox" value="<?=$empfname;?>" id="empfname" style="background:#EEEEEE;font-weight:bold;" readonly="readonly">
				</td>
				<td>Middle Name: <br />
					<input type=text name="emplmname" class="textbox" value="<?=$empmname;?>" id="empmname" style="background:#EEEEEE;font-weight:bold;" readonly="readonly">
				</td>
			</tr>
			<tr>
				<td>
					<b>Spouse's Data</b>
				</td>
			</tr>
            <tr>
           		<td>
					Last Name: <br />
					<input type=text name="splname" class="textbox" value="<?=$splname;?>" id="splname" style="background:#EEEEEE;font-weight:bold;" readonly="readonly">
				</td>
				<td>First Name: <br />
					<input type=text name="spfname" class="textbox" value="<?=$spfname;?>" id="spfname" style="background:#EEEEEE;font-weight:bold;" readonly="readonly">
				</td>
				<td>Middle Name: <br />
					<input type=text name="spmname" class="textbox" value="<?=$spmname;?>" id="spmname" style="background:#EEEEEE;font-weight:bold;" readonly="readonly">
				</td>
			</tr>
        </table>
    </div>
    <div class="module_actions">
		<div style="background:#000000;color:#FFFFFF;padding:3px;"><img src="images/money_add.png" /> <b>DEPENDENTS' DETAILS</b></div>
    	<table>
            <tr>
           		<td>
					Dependent's Last Name: <br />
					<input type=text name="deplname" class="textbox" value="<?=$deplname;?>" id="deplname" style="text-align:left;" >
				</td>
				<td>Dependent's First Name: <br />
					<input type=text name="depfname" class="textbox" value="<?=$depfname;?>" id="depfname" style="text-align:left;" >
				</td>
				<td>Dependent's Middle Name: <br />
					<input type=text name="depmname" class="textbox" value="<?=$depmname;?>" id="depmname" style="text-align:left;" >
				</td>
				<td>Date of Birth: <br />
                    <input type="text" name="bdate" id="bdate" class="textbox" onmouseover="Tip('Choose a date.');" value="<?=$bdate;?>" onclick="fPopCalendar('bdate')" readonly="readonly" />
				</td>
			</tr>
			<tr>
            	<td>
					<input type="submit" name="b" value="Save Details" class="buttons" />
           			<input type="submit" name="b" value="Delete Details" onclick="return approve_confirm();" class="buttons" />
                </td>
            </tr>
    	</table>
    </div>
	<div style="padding:3px; text-align:center;">
            <table cellspacing="2" cellpadding="5" width="100%" align="center" class="search_table">
                <?php
                    $page = $_REQUEST['page'];
                    if(empty($page)) $page = 1;
                     
                    $limitvalue = $page * $limit - ($limit);
        
                    $sql = "select
                                *
                            from
                                dependents
                            where
                                employeeID='$employeeID' and
                                dependents_void='0'";
                    
                    $pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
                            
                    $i=$limitvalue;
                    $rs = $pager->paginate();
                ?>
                <tr>
                    <td colspan="7" align="left">
                        <?php
                            echo $pager->renderFullNav("$view&header_saved=true&jo_number=$jo_number");
                        ?>                
                    </td>
                </tr>
                <tr bgcolor="#C0C0C0">				
                  <td width="20"><b>#</b></td>
                  <td width="20" align="center"><input type="checkbox"  name="checkAll" value="Comfortable with" onclick="javascript:check_all('_form', this)" title="Check/Uncheck All" /></td>
          	      <td width="15"></td>
                  <td><b>Dependents Name</b></td>                                      
                  <td><b>Date of Birth</b></td>
                  <td><b>Age</b></td>
                </tr>               
                <?php			
                    while($r=mysql_fetch_assoc($rs)) {				
						$now = strtotime($today);
						$dob = strtotime($r[dob]);
						
						$raw_age = ($now - $dob);
						
						$age = $raw_age / 31556926 % 100;
						
						if($dob > '0') 
							{
								$bday = date("F j, Y", $dob);
								$age = $raw_age / 31556926 % 100;
							}
						else if ($dob == '0') 
							{
								$bday = "  ";
								$age = "  ";
							}
						
                        echo '<tr bgcolor="'.$transac->row_color($i++).'">';
                        echo '<td width="20">'.$i.'.</td>';
                        echo '<td><input type="checkbox" name="checkList[]" value="'.$r[dependentsID].'" onclick="document._form.checkAll.checked=false"></td>';	
						echo '<td width="15"><a style="cursor:pointer;" onclick="xajax_edit_dependentsform(\''.$r[dependentsID].'\');" title="Edit Entry"><img src="images/edit.gif" border="0"></a></td>';
                        echo '<td style="text-align:left;">'.$r[dep_lname].', '.$r[dep_fname].' '.$r[dep_mname].'</td>';                        
                        echo '<td style="text-align:left;">'.$bday.'</td>';                        
                        echo '<td style="text-align:left;">'.$age.'</td>';                        
						
                    }
                ?>
          </table>
        </div>
   		<?php	 
    	}
	?>  
</div>
</form>