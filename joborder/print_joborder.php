<?php
require_once(dirname(__FILE__).'/../conf/ucs.conf.php');
require_once(dirname(__FILE__).'/../library/lib.php');
require_once(dirname(__FILE__).'/../library/DB.php');

function getRISReference($joborder_header_id,$stock_id){
    $sql  = "
        select
            h.issuance_header_id
        from
            issuance_header as h
            inner join issuance_detail as d on h.issuance_header_id = d.issuance_header_id
        where
            h.status != 'C'
        and d.joborder_header_id = '$joborder_header_id'
        and d.stock_id = '$stock_id'
    ";
    $mysqli = DB::conn();

    $issuance_header_id =  $mysqli->query($sql)->fetch_object()->issuance_header_id;
    if($mysqli->error) echo $mysqli->error;
    return $issuance_header_id;

}

function displayDetails($joborder_header_id){
	$sql = "
	    select
	        d.*,stock, d.stock_id
	    from
	        joborder_detail as d
	    inner join productmaster as p on d.stock_id = p.stock_id
	    and d.joborder_detail_void = '0'
	    and d.joborder_header_id = '$joborder_header_id'
	";
	$result  = DB::conn()->query($sql) or die(DB::conn()->error);
	while( $r = $result->fetch_assoc() ){
        $ris_reference = getRISReference($joborder_header_id,$r['stock_id']);
	    echo "
	        <tr>
	            <td>
	                ".htmlentities($r['stock'])."
	            </td>
	            <td style='text-align:right;'>
	                ".number_format($r['quantity'],2)."
	            </td>
	            <td style='text-align:center;'>
	                $r[ref_no] $ris_reference
	            </td>
	        </tr>
	    ";
	}
}

$joborder_header_id = $_REQUEST['joborder_header_id'];
$query="
	select
		h.*, concat(e1.employee_fname,' ',e1.employee_lname) as driver_name,
        concat(e2.employee_fname,' ',e2.employee_lname) as inspected_by_name,
        concat(e3.employee_fname,' ',e3.employee_lname) as conductd_by_name,
        concat(e4.employee_fname,' ',e4.employee_lname) as trial_conductd_by_name,
        concat(e5.employee_fname,' ',e5.employee_lname) as accepted_by_name,
        concat(user_fname,' ',user_lname) as encoded_by_name,
        stock as equipment_name,
        project_name
	from
		joborder_header as h
        left join employee as e1 on h.driver_id = e1.employeeID
        left join employee as e2 on h.inspected_by = e2.employeeID
        left join employee as e3 on h.conducted_by = e3.employeeID
        left join employee as e4 on h.trial_conducted_by = e4.employeeID
        left join employee as e5 on h.accepted_by = e5.employeeID
        left join admin_access as admin on h.encoded_by = admin.userID
        left join productmaster as p on p.stock_id = h.equipment_id
        left join projects on h.project_id = projects.project_id
	where
		joborder_header_id = '$joborder_header_id'
";

$aVal = DB::conn()->query($query)->fetch_assoc();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>REPORT</title>
<script>
function printPage() { print(); } //Must be present for Iframe printing
</script>
<style type="text/css">
body,pre
{
	font-family:"Times New Roman";
	font-size:12px;
}
.container{
	margin:0px auto;
	padding:0.1in;
}

.clearfix:after {
	content: ".";
	display: block;
	clear: both;
	visibility: hidden;
	line-height: 0;
	height: 0;
}

.clearfix {
	display: inline-block;
}

html[xmlns] .clearfix {
	display: block;
}

* html .clearfix {
	height: 1%;
}

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
	text-align: left;
}
.jo-detail{
	width:100%;
	border-collapse: collapse;
}
.jo-detail tbody td{
	border:1px solid #000;
	padding:3px;
}
.jo-detail tbody td:nth-child(2),.jo-detail tbody td:nth-child(3){
	width:20%;

}
.line_bottom {
	border-bottom-width: 1px;
	border-bottom-style: solid;
	border-bottom-color: #000000;
	border-left: 0px;
	border-right: 0px;
	border-top: 0px;
	width:120px;
}

@media screen {
    div.divFooter {
        display: none;
    }
}
@media print {
    div.divFooter {
        position: fixed;
        bottom: 0;

        font-family: "Times New Roman";
        font-size: 11px;
    }
}

</style>
</head>
<body>
<div class="container">
	<?php require_once('../transactions/form_heading.php') ?>
	<p style="font-weight:bold; font-size:15px; text-align:center;">
		JOBORDER
	</p>
	<table class="jo-header">
    	<tbody>
    		<tr>
    			<td>Joborder No.</td>
    			<td>
					<?=$aVal['joborder_header_id']?>
    			</td>

    			<td>Date</td>
    			<td><?=lib::ymd2mdy($aVal['date'])?></td>
    		</tr>
    		<tr>
    			<td>Department Name</td>
    			<td>
    				<?=$aVal['project_name']?>
    			</td>

    			<td>Equipment</td>
    			<td>
    				<?=$aVal['equipment_name']?>
    			</td>
    		</tr>
    		<tr>
    			<td>Driver's Name</td>
    			<td>
    				<?=$aVal['driver_name']?>
    			</td>
          <td>Job Order Type</td>
          <td>
            <?php
              if($aVal['type']=="A"){
                echo 'Accident';
              }else if($aVal['type']=="M"){
                echo 'Maintenance';
              }
            ?>
          </td>
    		</tr>
    	</tbody>
    </table>
    <table class='jo-detail'>
    	<tbody>
    		<tr>
    			<td colspan='3'>
    				<div>
    					Problem Encountered <br>
                         <?=lib::getAttribute(DB_HE.".jobs",'job_id',$aVal['job_id'],'job')?>
                    </div>
    			</td>
    		</tr>

    		<tr>
    			<td>
    				Inspected by <br>
    				<?=$aVal['inspected_by_name']?>
    			</td>
    			<td colspan='2'>
    				<u>Estimated Hours/Day(s) for Repair</u><br>
    				<?=$aVal['estimated_hours']?>
    			</td>
    		</tr>
    		<tr>
    			<td colspan='3'>
    				<div>
    					Details of Work to be Done <br>
    					<pre><?=$aVal['details']?></pre>
    				</div>
    			</td>
    		</tr>
    		<tr id="materials_entry">
    			<td rowspan='2' style='vertical-align:top;'>
    				Conducted by <br>
    				<?=$aVal['conductd_by_name']?>
    			</td>

    			<td>
    				<u>Date Started</u> <br>
    				<?=( ($aVal['date_started'] == "0000-00-00") ? "" : lib::ymd2mdy($aVal['date_started']) )?>
    			</td>
    			<td>
    				<u>Date Completed</u> <br>
    				<?=( ($aVal['date_completed'] == "0000-00-00") ? "" : lib::ymd2mdy($aVal['date_completed']) )?>
    			</td>
    		</tr>
    		<tr>
    			<td>
    				<u>Time Started <em>(24 hour format)</em></u> <br>
    				<?=( ($aVal['time_started'] == "00:00:00") ? "" : $aVal['time_started'] )?>
    			</td>
    			<td>
    				<u>Time Completed <em>(24 hour format)</em></u> <br>
    				<?=( ($aVal['time_completed'] == "00:00:00") ? "" : $aVal['time_completed'] )?>
    			</td>
    		</tr>
    		<tr>
    			<td colspan='3' style='text-align:center; font-size:14px; font-weight:bold;'>Material/Spare Part Requirements:</td>
    		</tr>
    		<tr>
    			<td style="text-align:center;">Material</td>
    			<td style="text-align:center;">Qty</td>
    			<td style="text-align:center;">RTP No / IS No</td>
    		</tr>
            <?php
            displayDetails($aVal['joborder_header_id']);
            ?>
    		<!-- end of details here -->
    		<tr>
    			<td colspan='3' style='text-align:center; font-size:14px; font-weight:bold;'>Trial Run/Turn-over</td>
    		</tr>
    		<tr>
    			<td style='vertical-align:top;'>
    				Conducted by <br>
    				<?=$aVal['trial_conductd_by_name']?>
            	</td>

    			<td colspan='2'>
    				Date <br>
    				<?=lib::ymd2mdy($aVal['trial_date'])?>
    			</td>
    		</tr>
    		<tr>
    			<td colspan='3'>
    				<div>
    					Results <br>
    					<pre><?=$aVal['results']?></pre>
    				</div>
    			</td>
    		</tr>
    		<tr>
    			<td style='vertical-align:top;'>
    				Accepted by <br>
    				<?=$aVal['accepted_by_name']?>
    			</td>

    			<td colspan='2'>
    				Date <br>
    				<?=lib::ymd2mdy($aVal['accepted_date'])?>
    			</td>
    		</tr>
    	</tbody>
    </table>
    <p></p>
    <table cellspacing="0" cellpadding="5" align="center" width="100%" style="border:none; text-align:center;font-size:13px; margin-top:50px;">
        <tr>
            <!-- <td>Requested By:<p>
                <input type="text" class="line_bottom" style="width:200px;" value="" /><br>End User</p></td> -->
            <td>Requested By:<p>
                <input type="text" class="line_bottom" value=""/><br>Name & Singnature<br><?=$aVal['encoded_datetime']?></td>
          	<td>Noted By:<p>
                <input type="text" class="line_bottom" /><br>P.I.C. / Dept.Head</p></td>
            <td>Approved By:<p>
                <input type="text" class="line_bottom" /><br>G.M / President</p></td>
        </tr>
    </table>


    <div class="divFooter">
        F-EMN-003<br>
        Rev. 1 03/09/15
    </div>
</div>
</body>
</html>
