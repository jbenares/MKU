<style type="text/css">
.table-contents tr td:nth-child(odd) {
	text-align:right;
	font-weight:bold;
}

#raw{
	text-align: center;
}

</style>
<script type="text/javascript">
function printIframe(id)
{
    var iframe = document.frames ? document.frames[id] : document.getElementById(id);
    var ifWin = iframe.contentWindow || iframe;
    iframe.focus();
    ifWin.printPage();
    return false;
}
</script>
<?php
	$b					= $_REQUEST['b'];
	$dtr_header_id		= $_REQUEST['dtr_header_id'];
	$dtr_header_id_pad	= $_REQUEST['dtr_header_id_pad'];
	
	$from_date			= $_REQUEST['from_date'];
	$to_date			= $_REQUEST['to_date'];
	$payroll_date			= $_REQUEST['payroll_date'];
	$paytype			= $_REQUEST['paytype'];
	$remarks			= $_REQUEST['remarks'];

	$user_id			= $_SESSION['userID'];
	
	//detail request
	$sss				= $_SESSION['sss'];
	$phic				= $_SESSION['phic'];
	$hdmf				= $_SESSION['hdmf'];
	$ca					= $_SESSION['ca'];
	
	$checkList	= $_REQUEST['checkList'];
	
	function getEmpDays($employeeID,$from_date,$to_date){
		$days = 0;
		$q = mysql_query("select sum(`day`) as days
							from dtr where
							employeeID = '$employeeID' and
							dtr_date between '$from_date' and '$to_date'") or die(mysql_error());
							
		$r = mysql_fetch_assoc($q);

		$days = $r['days'];
		
		return $days;
		
	}

	if($b == "Unfinish"){
		mysql_query("
			update dtr_header set status = 'S' where dtr_header_id = '$dtr_header_id'
		") or die(mysql_error());

		$msg = "DTR Summary Unfinished";
	}


	if($b=="Submit"){
		
		$q = mysql_query("select * from dtr_header as h
							where
							h.from_date = '$from_date' and h.to_date = '$to_date' and status != 'C'") or die(mysql_error());
		
		$cnt = mysql_num_rows($q);
		
		
		if($cnt > 0){
			$msg="<span style='color: red; font-size: 15px;'>Payroll Period Already Exist! Delete the existing period and try again.</span>";			
		}else{
			$query="
				insert into
					dtr_header
				set
					status='S',
					from_date='$from_date',
					to_date='$to_date',
					payroll_date='$payroll_date',
					remarks='$remarks',
					date_added = now()
			";

			mysql_query($query) or die(mysql_error());

			$dtr_header_id = mysql_insert_id();
			$options->insertAudit($dtr_header_id,'dtr_header_id','I');
			
			//Details
			$q = mysql_query("select
								*
								from
								dtr as d,
								employee as e
								where
								d.employeeID = e.employeeID and
								e.inactive = '0' and
								e.employee_void = '0' and
								d.period_from = '$from_date' and 
								d.period_to = '$to_date'") or die(mysql_error());
			while($r = mysql_fetch_assoc($q)){
				
				$employeeID 		= $r['employeeID'];
				$employee_type_id 	= $r['employee_type_id'];
				$day			 	= $r['day'];
				$overtime			 = $r['overtime_hr'];
				
				
				mysql_query("Insert into dtr_detail set 
					employeeID = '$employeeID',
					employee_type_id = '$employee_type_id',
					dtr_header_id = '$dtr_header_id',
					overtime = '$overtime',
					day = '$day'
				") or die(mysql_error());
			}

			$msg="Transaction Saved";
		
		}

	}else if($b=="Update"){
		
		
		$query="
			update
				dtr_header
			set
				remarks='$remarks',
				from_date='$from_date',
				to_date='$to_date',
				payroll_date='$payroll_date'
			where
				dtr_header_id='$dtr_header_id'
		";

		mysql_query($query) or die(mysql_error());
		$options->insertAudit($dtr_header_id,'dtr_header_id','U');

		$msg = "Transaction Updated";


	}else if($b=="Cancel"){
		
		
		$query="
			update
				dtr_header
			set
				status='C'
			where
				dtr_header_id='$dtr_header_id'
		";
		mysql_query($query) or die(mysql_error());
		
		$options->insertAudit($dtr_header_id,'dtr_header_id','C');

		$msg = "DTR Transaction Cancelled";
			
	}else if($b=="Finish"){
		$query="
			update
				dtr_header
			set
				status='F'
			where
				dtr_header_id='$dtr_header_id'
		";
		mysql_query($query) or die(mysql_error());
		$options->insertAudit($dtr_header_id,'dtr_header_id','F');

	
	}else if($b=="New"){
		header("Location: admin.php?view=$view");
	}


	$query="
		select * from dtr_header where dtr_header_id = '$dtr_header_id'
	";

	$result=mysql_query($query);
	$r = $aVal = mysql_fetch_assoc($result);


	$dtr_header_id		= $r['dtr_header_id'];
	$dtr_header_id_pad	= (!empty($dtr_header_id))?str_pad($dtr_header_id,7,0,STR_PAD_LEFT):"";

	$remarks			= $r['remarks'];
	$date_added			= $r['date_added'];
	$from_date			= $r['from_date'];
	$to_date			= $r['to_date'];
	$payroll_date			= $r['payroll_date'];
	$status				= $r['status'];

?>
<form name="header_form" id="header_form" action="" method="post">
<div class=form_layout>
	<?php if(!empty($msg)) echo '<div id="status_update" class="ui-state-highlight ui-corner-all" style="padding: 0px 0.7em; text-align:left;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right:.3em;"></span> '.$msg.'</p></div>'; ?>
	<div class="module_title"><img src='images/user_orange.png'>Daily Time Record Summary</div>

    <div style="width:100%;">
        <div class="module_actions">
            <input type="hidden" name="rr_header_id" id="rr_header_id" value="<?=$rr_header_id?>" />
            <input type="hidden" name="view" value="<?=$view?>" />
            <div id="messageError">
                <ul>
                </ul>
            </div>

            <table style="display:inline-table;" class="table-contents" >
				<tr>
					<td>
					
					</td>
					<td>
					Work Period
					</td>
				</tr>
            	<tr>
                	<td>From : </td>
                    <td><input type="text" class="datepicker required textbox3" title="Please enter date"  name="from_date" readonly='readonly'  value="<?=$from_date?>"></td>
                </tr>
				<tr>
                	<td>To: </td>
                    <td><input type="text" class="datepicker required textbox3" title="Please enter date"  name="to_date" readonly='readonly'  value="<?=$to_date?>"></td>
                </tr>
				<tr>
                	<td>Payroll Date: </td>
                    <td><input type="text" class="datepicker required textbox3" title="Please enter date"  name="payroll_date" readonly='readonly'  value="<?=$payroll_date?>"></td>
                </tr>
				<tr>
					<td>Remarks : </td>
					<td>
						<div>
						<textarea class="textarea_small" name='remarks'><?=$remarks?></textarea>
						</div> 
					</td>
				</tr>
       		</table>
     	</div>
        <div style="background-color:#CCC; padding:5PX;">
            <?php if(!empty($status)){ ?>
            <div class="inline" style="vertical-align:top;">
                DTR Summary # : <br />
                <input type="text" class="textbox3" name="dtr_header_id_pad" id="dtr_header_id_pad" value="<?=$dtr_header_id_pad?>" readonly="readonly"/>
                <input type="hidden" class="textbox3" name="dtr_header_id" id="dtr_header_id" value="<?=$dtr_header_id?>" readonly="readonly"/>
            </div>

            <div class='inline' style="vertical-align:top;">
                <div>Status : </div>
                <div>
                    <input type="text" class="textbox3" name="status" id="status" value="<?=$options->getTransactionStatusName($status)?>" readonly="readonly"/>
                </div>
            </div>

            <div class='inline'>
                <div>Encoded by : </div>
                <div>
                    <input type='text' class="textbox" value="<?=$options->getUserName($user_id);?>" readonly="readonly" />
                    <?php
                    if( !empty($aVal['encoded_datetime']) ){
                    	echo "<br>".$aVal['encoded_datetime'];
                    }
                    ?>
                </div>
            </div>
            <?php
            }
            ?>
        </div>
        <div class="module_actions">
            <input type="submit" name="b" value="New" />
            <?php
            if($status=="S"){
            ?>
            <input type="submit" name="b" id="b" value="Update" />
            <input type="submit" name="b" id="b" value="Finish" />

            <?php
            }else if($status!="F" && $status!="C"){
            ?>
            <input type="submit" name="b" id="b" value="Submit" />
            <?php } ?>
	
			
            <?php if($b!="Print Preview" && $status == 'F'){ ?>
                <input type="submit" name="b" id="b" value="Print Preview" />
            <?php } ?>

            <?php if($b=="Print Preview" && !empty($status)){ ?>
                <input type="button" value="Print" onclick="printIframe('JOframe');" />
            <?php } ?>

            <?php if($status == "F"){ 
					//if($registered_access == '' or $registered_access == ''){
			?>
            <input type="submit" name="b" value="Unfinish" />
            <?php 
					//}
			} 
			?>

            <?php if($status!="C" && !empty($status)){ ?>
            <input type="submit" name="b" id="b" value="Cancel" onClick="return confirm('Cancel DTR Summary?')" />
            <?php
            }
            ?>			
        </div>
    </div>
	<?php if($dtr_header_id){ ?>
    <div style="width:100%;">
        <div class="module_title"><img src='images/book_open.png'>Employees</div>
        <table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table" id="search_table">
            <tr bgcolor="#C0C0C0">
                <th width="20"><b>#</b></th>
                <th width="20"><b></b></th>
                <th>Employee</th>
                <th>Occupation</th>
                <th>Daily Rate</th>
                <th>Hourly Rate</th>
                <th>Days</th>
                <th>OT Hours</th>
                <th>Gross Pay</th>
                <th>SSS</th>
                <th>PHIC</th>
                <th>HDMF</th>
                <th>C/A</th>
                <th>NET PAY</th>
            </tr>
            <?php
            $result=mysql_query("
            select
				d.dtr_detail_id,
				e.employeeID,
				e.employee_lname,
				e.employee_fname,
				e.employee_mname,
				et.employee_type,
				e.base_rate,
				e.daily_rate,
				d.overtime,
				d.day,
				d.sss,
				d.phic,
				d.hdmf,
				d.ca,
				d.net
				from
				dtr_detail as d,
				employee as e,
				employee_type as et
				where
				d.employeeID = e.employeeID and
				e.employee_type_id = et.employee_type_id and
				d.dtr_header_id = '$dtr_header_id'     
            ") or die(mysql_error());

            $i=1;
            $netamount = 0;
            while($r=mysql_fetch_assoc($result)){
			
			$hourly = $r['daily_rate']/8;
			
			//$days = getEmpDays($r['employeeID'],$from_date,$to_date);
			$gross = $r['day'] * $r['daily_rate'] + ($r['overtime'] * $hourly);
			$net = $gross -($r['sss'] + $r['phic'] + $r['hdmf'] + $r['ca']);
            ?>
            <tr>
                <td><?=$i++?></td>
                <?php echo '<td width="15"><a href="#" onclick="xajax_edit_dtr_detail_form(\''.$r['dtr_detail_id'].'\');" title="Edit Entry"><img src="images/edit.gif" border="0"></a></td>'; ?>
                <td><?=$r['employee_lname'],', ',$r['employee_fname'],' ',$r['employee_mname']?></td>
                <td><?=$r['employee_type']?></td>
                <td><?=$r['daily_rate']?></td>                
                <td><?=$hourly?></td>
				<td><?=$r['day']?></td>				
                <td><?=$r['overtime']?></td>                                                             
                <td><?=number_format($gross,2)?></td>                
                <td><?=$r['sss']?></td>                
                <td><?=$r['phic']?></td>                                           
                <td><?=$r['hdmf']?></td>                                                          
                <td><?=$r['ca']?></td>                                                                          
                <td><?=number_format($net,2)?></td>                                               
            </tr>
            <?php
            }
            ?>
        </table>
   	</div>
	<?php } ?>
    <div style="clear:both;">
		<?php
        if($b == "Print Preview" && $dtr_header_id){

            echo "	<iframe id='JOframe' name='JOframe' frameborder='0' src='print_dtr_summary.php?dtr_header_id=$dtr_header_id' width='100%' height='500'>
                    </iframe>";
        }
        ?>


   	</div>


</div>
</form>