<?php
require_once('../my_Classes/options.class.php');
include_once("../conf/ucs.conf.php");

$options=new options();	
$from_date				= $_REQUEST['from_date'];
$to_date				= $_REQUEST['to_date'];
$project_id				= $_REQUEST['project_id'];
$to_project_id			= $_REQUEST['to_project_id'];
$project 				= $options->getAttribute("projects","project_id",$project_id,"project_name");
$to_project 			= $options->getAttribute("projects","project_id",$to_project_id,"project_name");
//$work_category_id		= $_REQUEST['work_category_id'];
//$sub_work_category_id	= $_REQUEST['sub_work_category_id'];

$sql = "select 
h.date,
h.translog_header_id,
p.project_name as from_project,
h.to_project_id,
h.work_category_id,
h.reference,
h.remarks,
d.description,
d.quantity,
d.unit
from 
transfer_log_header as h, 
transfer_log_detail as d,
projects as p
where 
h.project_id = p.project_id
and h.translog_header_id = d.translog_header_id
and h.`status` != 'C'
and h.date between '$from_date' and '$to_date'
";

if(!empty($project_id)){
	$sql.= " and h.project_id = '$project_id'";
}
if(!empty($to_project_id)){
	$sql.= " and h.to_project_id = '$to_project_id'";
}
$sql.= " order by h.translog_header_id asc";

$result = mysql_query($sql) or die (mysql_error());

function getWork($work){
	$sql = mysql_query("Select * from work_category where work_category_id = '$work'") or die (mysql_error());
	$r = mysql_fetch_assoc($sql);
	
	return $r['work'];
}

function getToProject($to_project_id){
	$sql = mysql_query("Select * from projects where project_id = '$to_project_id'") or die (mysql_error());
	$r = mysql_fetch_assoc($sql);
	
	return $r['project_name'];
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>JOB ORDER</title>
<script>
function printPage() { print(); } //Must be present for Iframe printing
</script>
<style type="text/css">
*{ font-family:Arial, Helvetica, sans-serif; font-size:11px; }
thead{ display:table-header-group; }
table{ border-collapse:collapse; width:100%; }
table thead td{
	font-weight:bold;	
	border-top:1px solid #000;
	border-bottom:1px solid #000;
	
}
table td{
	vertical-align:top;
	padding:3px;
}
table td:nth-child(n+10){
	text-align:right;	
}
table td:nth-child(11){
	text-align:left;	
}
.subtotal td{ border-top:1px solid #000; font-weight:bold; }
.grandtotal td{ border-top:1px solid #000; border-bottom:3px double #000; font-weight:bold; }
</style>
</head>
<body>
<div class="container">
	
     <div><!--Start of Form-->
     	<div style="font-weight:bolder;">
        	TRANSMITTAL LOG REPORT <br />
			<?php if(!empty($project)){ ?>
			FROM <?=$project?>
			<?php }else if($project && $to_project){ ?>
			<?=$project?> TO <?=$to_project?><br />
			<?php }else if(!empty($to_project)){ ?>
			TO <?=$to_project?>
			<?php } ?>
			<br />
            From <?=date("m/d/Y",strtotime($from_date))?> to <?=date("m/d/Y",strtotime($to_date))?>
        </div>           
        
        <table width="1100">
        	<thead>
                <tr>
					<td width="50">#</td>
                    <td>DATE</td>
					<td style="text-align:center">TRANSMITTAL LOG #</td>
                    <td width="200">PROJECT</td>
                    <td width="200">TO PROJECT</td>
                    <td width="200">SCOPE OF WORK</td>
                    <td width="200">REFERENCE</td>
                    <td width="200">REMARKS</td>
                    <td width="500">ITEM</td>
                    <td>QTY</td>
                    <td>UNIT</td>
                </tr>
           	</thead>
            <tbody>
			<?php while($r = mysql_fetch_assoc($result)){ 
			if($r['work_category_id'] != 0){
				$work = $r['work_category_id'];
				
				$work_category = getWork($work);
			}else{
				$work_category = " ";
			}
			
			if($r['to_project_id'] != 0){
				$to_project_id = $r['to_project_id'];
				
				$to_project = getToProject($to_project_id);
			}else{
				$to_project = " ";
			}
			
			$i++;
			?>
				<tr>
					<td><?=$i;?></td>
					<td><?=$r['date']?></td>
					<td style="text-align: center; font-weight: bold;"><?=sprintf("%07d", $r['translog_header_id']);?></td>
					<td width="200"><?=$r['from_project']?></td>
					<td width="200"><?=$to_project?></td>
					<td width="200"><?=$work_category?></td>
					<td width="200"><?=$r['reference']?></td>
					<td width="200"><?=$r['remarks']?></td>
					<td><?=wordwrap($r['description'], 20, "\n", true)?></td>
					<td><?=$r['quantity']?></td>
					<td><?=$r['unit']?></td>
				</tr>
			<?php } ?>
            </tbody>
        </table>
    </div><!--End of Form-->
</div>
</body>
</html>