<?php
/********************************************
Author      : Michael Angelo O. Salvio, CpE, MIT
Description : Job Order
********************************************/
?>
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
<style type="text/css">
.align-right{
	text-align:right;
}
.cp_table{
	width:50%;
	border-collapse:collapse;
}
.cp_table tr th {
	border-top:1px solid #000;
	border-bottom:1px solid #000;
}
</style>
<?php
function finishOrCancelTranasction(){
    $status = ($_REQUEST['b'] == "Finish") ? "F" : "C";
    $sql  = "
        update
            tiretransfer
        set
            status = '$status'
        where
            tiretransfer_header_id = '$_REQUEST[joborder_header_id]'
    ";
    DB::conn()->query($sql);
    $msg = ($_REQUEST['b'] == "Finish") ? "Transaction Finished" : "Transaction Cancelled";
    return $msg;
}

function saveToDB(){

    $_REQUEST['time_completed'] = $_REQUEST['time_completed_hour'] . ":" . $_REQUEST['time_completed_min'];
    $_REQUEST['time_started']   = $_REQUEST['time_started_hour'] . ":" . $_REQUEST['time_started_min'];

    if( !empty($_REQUEST['joborder_header_id']) ){
        $sql = "
            update
                tiretransfer
            set
                date               = '$_REQUEST[date]',
                project_id         = '$_REQUEST[project_id]',
                equipment_id       = '$_REQUEST[equipment_id]',
                driver_id          = '$_REQUEST[driver_id]',
                job_id             = '$_REQUEST[job_id]',
                inspected_by       = '$_REQUEST[inspected_by]',
                estimated_hours    = '$_REQUEST[estimated_hours]',
                details            = '$_REQUEST[details]',
                conducted_by       = '$_REQUEST[conducted_by]',
                date_started       = '$_REQUEST[date_started]',
                time_started       = '$_REQUEST[time_started]',
                date_completed     = '$_REQUEST[date_completed]',
                time_completed     = '$_REQUEST[time_completed]',
                trial_conducted_by = '$_REQUEST[trial_conducted_by]',
                trial_date         = '$_REQUEST[trial_date]',
                results            = '$_REQUEST[results]',
                accepted_by        = '$_REQUEST[accepted_by]',
                accepted_date      = '$_REQUEST[accepted_date]',
                reference          = '$_REQUEST[etc]',
                type               = '$_REQUEST[type]',
                from_project_id    = '$_REQUEST[from_project_id]',
                to_project_id      = '$_REQUEST[to_project_id]',
                from_eqID          = '$_REQUEST[from_eqID]',
                to_eqID            = '$_REQUEST[to_eqID]',
                from_position      = '$_REQUEST[from_position]',
                to_position        = '$_REQUEST[to_position]',
                branding_num       = '$_REQUEST[branding_num]'
            where
                tiretransfer_header_id = '$_REQUEST[joborder_header_id]'
        ";

            DB::conn()->query($sql) or die(DB::conn()->error);
            /*update details*/
            //updateDetails();
            $msg = "Tranasction Updated.";


    } else {
        $sql = "
            insert into
                tiretransfer
            set
                date               = '$_REQUEST[date]',
                project_id         = '$_REQUEST[project_id]',
                equipment_id       = '$_REQUEST[equipment_id]',
                driver_id          = '$_REQUEST[driver_id]',
                job_id             = '$_REQUEST[job_id]',
                inspected_by       = '$_REQUEST[inspected_by]',
                estimated_hours    = '$_REQUEST[estimated_hours]',
                details            = '$_REQUEST[details]',
                conducted_by       = '$_REQUEST[conducted_by]',
                date_started       = '$_REQUEST[date_started]',
                time_started       = '$_REQUEST[time_started]',
                date_completed     = '$_REQUEST[date_completed]',
                time_completed     = '$_REQUEST[time_completed]',
                trial_conducted_by = '$_REQUEST[trial_conducted_by]',
                trial_date         = '$_REQUEST[trial_date]',
                results            = '$_REQUEST[results]',
                accepted_by        = '$_REQUEST[accepted_by]',
                accepted_date      = '$_REQUEST[accepted_date]',
                encoded_datetime   = now(),
                encoded_by         = '$_SESSION[userID]',
                reference          = '$_REQUEST[etc]',
                type               = '$_REQUEST[type]',
                from_project_id    = '$_REQUEST[from_project_id]',
                to_project_id      = '$_REQUEST[to_project_id]',
                from_eqID          = '$_REQUEST[from_eqID]',
                to_eqID            = '$_REQUEST[to_eqID]',
                from_position      = '$_REQUEST[from_position]',
                to_position        = '$_REQUEST[to_position]',
                branding_num       = '$_REQUEST[branding_num]'
        ";

            DB::conn()->query($sql) or die(DB::conn()->error);
            $_REQUEST['joborder_header_id'] = DB::conn()->insert_id;
            $msg = "Tranasction Saved.";


    }

    return $msg;
}
function getTime($name,$end,$selected=NULL){
	$content = "
		<select name='$name' id = '$id'>
	";

	for($x = 0 ; $x <= $end ; $x++){
		$s = "";
		if($x == $selected){
			$s = "selected='selected'";
		}
		$content .="
			<option $s>".str_pad($x,2,0,STR_PAD_LEFT)."</option>
		";
	}

	$content.="</select>";

	return $content;
}

if( $_REQUEST['b'] == "Submit" ){
    $msg = saveToDB();
} else if( $_REQUEST['b'] == "Add" ){
    $msg = insertDetail();
} else if( $_REQUEST['b'] == "d" ){
    $msg = deleteDetail();
} else if( $_REQUEST['b'] == "Finish" || $_REQUEST['b'] == "Cancel" ){
    $msg = finishOrCancelTranasction();
}


if( $_REQUEST['joborder_header_id'] ){

	$query="
		select
			h.*, concat(e1.employee_fname,' ',e1.employee_lname) as driver_name,
            concat(e2.employee_fname,' ',e2.employee_lname) as inspected_by_name,
            concat(e3.employee_fname,' ',e3.employee_lname) as conductd_by_name,
            concat(e4.employee_fname,' ',e4.employee_lname) as trial_conductd_by_name,
            concat(e5.employee_fname,' ',e5.employee_lname) as accepted_by_name,
            concat(user_fname,' ',user_lname) as encoded_by_name
		from
			tiretransfer as h
            left join employee as e1 on h.driver_id = e1.employeeID
            left join employee as e2 on h.inspected_by = e2.employeeID
            left join employee as e3 on h.conducted_by = e3.employeeID
            left join employee as e4 on h.trial_conducted_by = e4.employeeID
            left join employee as e5 on h.accepted_by = e5.employeeID
            left join admin_access as admin on h.encoded_by = admin.userID
		where
			tiretransfer_header_id = '$_REQUEST[joborder_header_id]'
	";

	$aVal = DB::conn()->query($query)->fetch_assoc() or die(DB::conn()->error);

} else {
	$aVal = $_REQUEST;
}


?>

<form name="header_form" id="header_form" action="" method="post">
<div class="module_actions">
    <div class='inline'>
        Transfer # : <br />
        <input type="text" class="textbox"  name="search_joborder_header_id" value="<?=$_REQUEST['search_joborder_header_id']?>"  onclick="this.select();"  autocomplete="off" />
    </div>
    <input type="submit" name="b" value="Search" />
    <a href="admin.php?view=<?=$view?>"><input type="button" value="New" /></a>
</div>

<?php
if($_REQUEST['b'] == "Search"){
?>
<?php
    $page = $_REQUEST['page'];
    if(empty($page)) $page = 1;

    $limitvalue = $page * $limit - ($limit);

    $sql = "
		select
        	h.*, project_name, job, concat(user_lname,', ',user_fname) as encoded_by_name, stock as equipment_name
        from
			  tiretransfer as h
        left join projects as p on h.project_id = p.project_id
        left join productmaster as pr on h.equipment_id = pr.stock_id
        left join ".DB_HE.".jobs on h.job_id = jobs.job_id
        left join admin_access as a on h.encoded_by = a.userID
        where
            1=1
    ";

    if(!empty($_REQUEST['search_joborder_header_id'])) $sql.=" and tiretransfer_header_id like '%$_REQUEST[search_joborder_header_id]%' ";


	$sql.=" order by date desc ";


    /*echo "<pre>";
    echo $sql;
    echo "</pre>";*/

    $pager = new PS_Pagination($conn,$sql,$limit,$link_limit);

    $i=$limitvalue;
    $rs = $pager->paginate();

	$pagination	= $pager->renderFullNav("$view&b=Search&search_eur_no=$search_eur_no");
    ?>
    <div class="pagination">
        <?=$pagination?>
    </div>
    <table id="my_table" cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table">
    <tr>

        <th width="20">#</th>
        <th width="20"></th>
        <th style="width:5%;">TRANSFER #</th>
        <th>DATE</th>
        <th style="text-align:left;">FROM PROJECT</th>
        <th style="text-align:left;">FROM EQUIPMENT</th>
        <th style="text-align:left;">FROM POSITION</th>
        <th style="text-align:left;">TO PROJECT</th>
        <th style="text-align:left;">TO EQUIPMENT</th>
        <th style="text-align:left;">TO POSITION</th>
        <th style="width:20%;">ENCODED BY</th>
        <th style="width:10%;">STATUS</th>
    </tr>
    <?php
    while($r=mysql_fetch_assoc($rs)) {

        echo '<tr>';
        echo '<td width="20">'.++$i.'</td>';
        echo '<td width="15"><a href="admin.php?view='.$view.'&joborder_header_id='.$r['tiretransfer_header_id'].'" title="Show Details"><img src="images/edit.gif" border="0"></a></td>';
    		echo '<td>'.str_pad($r['tiretransfer_header_id'],7,0,STR_PAD_LEFT).'</td>';
    		echo '<td>'.lib::ymd2mdy($r['date']).'</td>';
        echo '<td>'.$options->getAttribute("projects","project_id",$r[from_project_id],"project_name").'</td>';
        echo '<td>'.$options->getAttribute("productmaster","stock_id",$r[from_eqID],"stock").'</td>';
        echo '<td>'.$options->getTirePositionValue($r[from_position]).'</td>';
        echo '<td>'.$options->getAttribute("projects","project_id",$r[to_project_id],"project_name").'</td>';
        echo '<td>'.$options->getAttribute("productmaster","stock_id",$r[to_eqID],"stock").'</td>';
        echo '<td>'.$options->getTirePositionValue($r[to_position]).'</td>';
        echo '<td>'.$r['encoded_by_name'].'</td>';
        echo '<td>'.$GLOBALS['aStatus'][$r['status']].'</td>';
        echo '</tr>';

    }
    ?>
    </table>
    <div class="pagination">
	   	 <?=$pagination?>
    </div>
<?php
} else {
?>
	<style type="text/css">
		.jo-header{
			width:60%;
			border-collapse: collapse;
		}
		.jo-header tbody td{
			padding:3px 5px 3px 3px;
		}
		.jo-header tbody td:nth-child(even){
			padding-right:20px;
		}
		.jo-header tbody td:nth-child(odd){
			text-align: right;
		}
		.jo-detail{
			width:100%;
			border-collapse: collapse;
		}
		.jo-detail tbody td{
			border:1px solid #c0c0c0;
			padding:3px;
		}
		.jo-detail tbody td:nth-child(2),.jo-detail tbody td:nth-child(3){
			width:20%;
		}

	</style>

    <div class=form_layout>
        <?php if(!empty($msg)) echo '<div id="status_update" class="ui-state-highlight ui-corner-all" style="padding: 0px 0.7em; text-align:left;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right:.3em;"></span> '.$msg.'</p></div>'; ?>
        <div class="module_title"><img src='images/user_orange.png'>TIRE TRANSFER</div>
        <div class="module_actions">
            <table class="jo-header">
            	<tbody>
            		<tr>
            			<td>Transfer No.</td>
            			<td>
            				<input type="text" class="textbox" disabled='disabled' value='<?=$aVal['tiretransfer_header_id']?>'>
            				<input type="hidden" name="joborder_header_id" value="<?=$aVal['tiretransfer_header_id']?>" />
            			</td>

            			<td>Date</td>
            			<td><input type="text" name="date" class="textbox datepicker" value="<?=$aVal['date']?>" ></td>
                  <!--<td style="width:500px;">Type</td>
            			<td><?=$options->getTypeJob($aVal['type'])?></td>-->
            		</tr>
            		<tr>
            			<td>Department Name</td>
            			<td>
            				<input type="text" class="textbox project" value="<?=lib::getAttribute('projects','project_id',$aVal['project_id'],'project_name')?>">
            				<input type="hidden" name="project_id" value="<?=$aVal['project_id']?>" >
            			</td>
            		</tr>
            	</tbody>
            </table>
            <table class='jo-detail'>
            	<tbody>
                    <tr id="transfer_header">
                                    <td colspan="3">
                                         <div style='font-weight:bold; color:#FFF; background:#000; padding:3px;'>
                                         Transfer Details
                                        </div>
                                    </td>
                                </tr>

                                <tr id="transfer_details">
                                    <td>
                                        From Project<br/>
                                        <?=$options->getTableAssoc($aVal['from_project_id'],'from_project_id','Select Project','select * from projects order by project_name asc','project_id','project_name')?>
                                        <br/>
                                        From Equipment<br/>
                                        <?=$options->getTableAssoc($aVal['from_eqID'],'from_eqID',"Select Equipment","select * from productmaster where categ_id1=25 AND status = 'S' ORDER by stock ASC",'stock_id','stock')?>
                                        <br/>
                                        From Position<br/>
                                        <?=$options->getTirePosition($aVal['from_position'],"from_position")?>
                                        <br/>
                                        Branding Number<br/>
                                        <input type="text" name="branding_num" id="branding_num" value="<?=$aVal['branding_num']?>" class="textbox">
                                    </td>
                                    <td valign="top">
                                         To Project<br/>
                                        <?=$options->getTableAssoc($aVal['to_project_id'],'to_project_id','Select Project','select * from projects order by project_name asc','project_id','project_name')?>
                                        <br/>
                                         To Equipment<br/>
                                        <?=$options->getTableAssoc($aVal['to_eqID'],'to_eqID',"Select Equipment","select * from productmaster where categ_id1=25 AND status = 'S' ORDER by stock ASC",'stock_id','stock')?>
                                        <br/>
                                        To Position<br/>
                                        <?=$options->getTirePosition($aVal['to_position'],"to_position")?>
                                    </td>
                                </tr>
            		<tr>
            			<!--<td>
            				Inspected by <br>
            				<input type="text" class="textbox ac-employee" value="<?=$aVal['inspected_by_name']?>">
            				<input type="hidden" name="inspected_by" value="<?=$aVal['inspected_by']?>" >
            			</td>-->
            			<td colspan='2'>
            				<u>Estimated Hours/Day(s) for Transfer</u><br>
            				<input type="text" class="textbox" name="estimated_hours" value="<?=$aVal['estimated_hours']?>">
            			</td>
            		</tr>
            		<tr>
            			<td colspan='3'>
	        				<div>
	        					Details of Work to be Done <br>
	        					<textarea style="width:98%; border:1px solid #c0c0c0; height:60px; font-size:11px; font-family:arial;" name="details" ><?=$aVal['details']?></textarea>
	        				</div>
	        			</td>
            		</tr>
            		<tr id="materials_entry">
            			<td rowspan='2' style='vertical-align:top;'>
            				Conducted by <br>
            				<input type="text" class="textbox ac-employee" value='<?=$aVal['conductd_by_name']?>'>
            				<input type="hidden" name="conducted_by" value="<?=$aVal['conducted_by']?>" >
            			</td>

            			<td>
            				<u>Date Started</u> <br>
            				<input type="text" class="textbox datepicker" name='date_started' value='<?=$aVal['date_started']?>'>
            			</td>
            			<td>
            				<u>Date Completed</u> <br>
            				<input type="text" class="textbox datepicker" name='date_completed' value='<?=$aVal['date_completed']?>'>
            			</td>
            		</tr>
            		<tr>
            			<?php
            			$start_time			= $aVal['time_started'];
      						$aStartTime			= explode(":",$start_time);
      						$start_time_hour 	= $aStartTime[0];
      						$start_time_min 	= $aStartTime[1];

      						$end_time			= $aVal['time_completed'];
      						$aEndTime			= explode(":",$end_time);
      						$end_time_hour	 	= $aEndTime[0];
      						$end_time_min	 	= $aEndTime[1];
            			?>
            			<td>
            				<u>Time Started <em>(24 hour format)</em></u> <br>
            				<?=getTime('time_started_hour',24,$start_time_hour)?>:
                                <?=getTime('time_started_min',59,$start_time_min)?>
            			</td>
            			<td>
            				<u>Time Completed <em>(24 hour format)</em></u> <br>
            				<?=getTime('time_completed_hour',24,$end_time_hour)?>:
                                <?=getTime('time_completed_min',59,$end_time_min)?>
            			</td>
            		</tr>
            		<tr>
            			<td style='vertical-align:top;'>
            				Accepted by <br>
            				<input type="text" class="textbox ac-employee" value='<?=$aVal['accepted_by_name']?>' >
            				<input type='hidden' name='accepted_by' value='<?=$aVal['accepted_by']?>' >
            			</td>

            			<td colspan='2'>
            				Date <br>
            				<input type="text" class="textbox datepicker" name="accepted_date" value='<?=$aVal['accepted_date']?>'>
            			</td>
            		</tr>
            	</tbody>
            </table>

        </div>
        <?php if(!empty($aVal['status'])){ ?>
        <div class="module_actions">
        	<div style="display:inline-block; margin-right:10px; vertical-align:top;">
            	Status:<br />
                <span style="font-size:15px; font-weight:bold;"><?=$GLOBALS['aStatus'][$aVal['status']]?></span>
            </div>
            <div style="display:inline-block;">
            	Encoded by:<br />
                <span style="font-size:15px; font-weight:bold;"><?=$aVal['encoded_by_name']?></span><br>
                <?=$aVal['encoded_datetime']?>
            </div>
        </div>
        <?php } ?>
        <div class="module_actions">
            <?php if( $aVal['status'] == "S" || empty($aVal['status']) ){ ?>
            <input type="submit" name="b"  value="Submit" />
            <?php } ?>
            <?php if( $aVal['status'] == "S" ){ ?>
            <input type="submit" name="b"  value="Finish" onclick='return approve_confirm();' />
            <?php } ?>
            <?php if( $aVal['status'] == "F" || $aVal['status'] == "S" ){ ?>
            <input type="submit" name="b"  value="Cancel"  onclick='return approve_confirm();'/>
            <?php } ?>
            <?php if( !empty($aVal['status']) && $_REQUEST['b'] != "Print Preview"){ ?>
            <input type="submit" name="b"  value="Print Preview" />
            <?php } ?>
            <?php if( $_REQUEST['b'] == "Print Preview" ){ ?>
            <input type="button" value="Print" onclick="printIframe('JOframe');" />
            <?php } ?>
            <a href="admin.php?view=<?=$view?>"><input type="button" value="New" /></a>
        </div>
    </div>

<?php } ?>
<?php
if($_REQUEST['b'] == "Print Preview"){
	/*echo "	<iframe id='JOframe' name='JOframe' frameborder='0' src='joborder/print_joborder.php?joborder_header_id=$aVal[joborder_header_id]' width='100%' height='500'>
			</iframe>";*/
}

?>
</form>
<script type="text/javascript">
<?php if( $_REQUEST['b'] == "Add" || $_REQUEST['b'] == 'd' ) echo "window.location.hash='materials_entry';" ?>
<?php if( $aVal['status'] == "F" || $aVal['status'] == 'C' ) echo " jQuery('.trash').remove(); "; ?>
jQuery(function(){
	jQuery(".ac-employee").autocomplete({
		source: "autocomplete/employees.php",
		minLength: 2,
		select: function(event, ui) {
			jQuery(this).val(ui.item.value);
			jQuery(this).next().val(ui.item.id);
		}
	});
    /*var job = document.getElementById('job_id');
    //alert(job.value);
    if(job.value == 6903){
         jQuery("#transfer_header").css({"display":"block"});
         jQuery("#transfer_details").css({"display":"block"});
    }else{
         jQuery("#transfer_header").css({"display":"none"});
         jQuery("#transfer_details").css({"display":"none"});
    }
    //alert('ok');
    jQuery('#job_id').change(function(){
       var job_id = this.value;

       var transfer_header  = document.getElementById('transfer_header');
       var transfer_details  = document.getElementById('transfer_details');

       if(job_id == 6903){
             //alert('okay');
             jQuery("#transfer_header").css({"display":"block"});
             jQuery("#transfer_details").css({"display":"block"});
             //alert('okay');
       }else{
             jQuery("#transfer_header").css({"display":"none"});
             jQuery("#transfer_details").css({"display":"none"});

       }
    });*/
});

</script>
