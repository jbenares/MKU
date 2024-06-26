<?php
require_once('../my_Classes/options.class.php');
include_once("../conf/ucs.conf.php");

$options=new options();	
$from_date				= $_REQUEST['from_date'];
$to_date				= $_REQUEST['to_date'];
$project_id				= $_REQUEST['project_id'];
$project 				= $options->getAttribute("projects","project_id",$project_id,"project_name");
$work_category_id		= $_REQUEST['work_category_id'];
$sub_work_category_id	= $_REQUEST['sub_work_category_id'];


function getProjects($from_date,$to_date,$project_id,$work_category_id,$sub_work_category_id){
	$a = array();
	$sql = "
		select distinct(h.project_id) from rtp_header as h, projects as p where h.project_id = p.project_id 
	";
	if(!empty($from_date)) $sql.= " and date >= '$from_date'";
	if(!empty($to_date)) $sql.= " and date <= '$to_date'";
	if(!empty($work_category_id)) $sql.= " and work_category_id <= '$work_category_id'";
	if(!empty($project_id)) $sql.= " and h.project_id = '$project_id'";
	if(!empty($sub_work_category_id)) $sql.= " and sub_work_category_id <= '$sub_work_category_id'";
	$sql .= "order by project_name asc";
	
	$result = mysql_query($sql) or die(mysql_error());	
	while($r = mysql_fetch_assoc($result)){
		$a[] = $r;	
	}
	return $a;
}

function getData($from_date,$to_date,$project_id,$work_category_id,$sub_work_category_id){
	$sql = "
		select 
			*
		from
			rtp_header as h, rtp_detail as d
		where
			h.rtp_header_id = d.rtp_header_id
		and rtp_void = '0'
		and project_id = '$project_id'
	";
	if(!empty($from_date)) $sql.= " and date >= '$from_date'";
	if(!empty($to_date)) $sql.= " and date <= '$to_date'";
	#if(!empty($project_id)) $sql.= " and project_id = '$project_id'";
	if(!empty($work_category_id)) $sql.= " and work_category_id <= '$work_category_id'";
	if(!empty($sub_work_category_id)) $sql.= " and sub_work_category_id <= '$sub_work_category_id'";
	
	$sql .= " order by date asc";
	
	$result  = mysql_query($sql) or die(mysql_error());
	$aData = array();
	while($r = mysql_fetch_assoc($result)){
		$aData[] = $r;	
	}
	
	return $aData;
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
        	RTP MONITORING LOG REPORT <br />
        </div>           
        
        <table>
        	<thead>
                <tr>
                	<td>DATE REQUESTED</td>
                    <td>DATE NEEDED</td>
                    <td>DATE RECEIVED</td>
					<td>DATE ENCODED</td>
                    <td>PROJECT</td>
                    <td>WORK CATEG</td>
                    <td>SUBWORK CATEG</td>
                    <td>REFERENCE</td>
                    <td>REMARKS</td>
                    
                    <td>ITEM</td>
                    <td>QTY</td>
                    <td>UNIT</td>
                    <td>C/O MCD</td>
                    <td>IN-HOUSE BUDGET</td>
                    <td>ACTUAL RECEIVED</td>
                    <td>BALANCE</td>
                </tr>
           	</thead>
            <tbody>
            	<?php
				$g_quantity = $g_mcd_qty = $g_budget_qty = $g_actual_qty = $g_balance_qty = 0;
				foreach(getProjects($from_date,$to_date,$project_id,$work_category_id,$sub_work_category_id) as $aProjects):
					echo "
						<tr>
							<td colspan='10' style='font-weight:bold;'>".$options->getAttribute('projects','project_id',$aProjects['project_id'],'project_name')."</td>
						</tr>
					";
				
					$t_quantity = $t_mcd_qty = $t_budget_qty = $t_actual_qty = $t_balance_qty = 0;
					foreach(getData($from_date,$to_date,$aProjects['project_id'],$work_category_id,$sub_work_category_id) as $r):
						$t_quantity 	+= $r['quantity'];
						$t_mcd_qty		+= $r['mcd_qty'];
						$t_budget_qty	+= $r['budget_qty'];
						$t_actual_qty	+= $r['actual_qty'];
						$t_balance_qty	+= $r['balance_qty'];
						echo "
							<tr>
								<td>".date("m/d/Y",strtotime($r['date']))."</td>
								<td>".date("m/d/Y",strtotime($r['date_needed']))."</td>
								<td>".date("m/d/Y",strtotime($r['date_received']))."</td>
								<td>".date("m/d/Y h:i:s",strtotime($r['datetime_encoded']))."</td>
								<td>".$options->getAttribute('projects','project_id',$r['project_id'],'project_name')."</td>
								<td>".$options->getAttribute('work_category','work_category_id',$r['work_category_id'],'work')."</td>
								<td>".$options->getAttribute('work_category','work_category_id',$r['sub_work_category_id'],'work')."</td>
								<td>$r[reference]</td>
								<td>$r[remarks]</td>
								
								<td>".htmlentities($r['description'])."</td>
								<td>".number_format($r['quantity'],2)."</td>
								<td>".$r['unit']."</td>
								<td>".number_format($r['mcd_qty'],2)."</td>
								<td>".number_format($r['budget_qty'],2)."</td>
								<td>".number_format($r['actual_qty'],2)."</td>
								<td>".number_format($r['balance_qty'],2)."</td>
							</tr>
						";
					
					endforeach;
					
					$g_quantity 	+= $t_quantity;
					$g_mcd_qty		+= $t_mcd_qty;
					$g_budget_qty	+= $t_budget_qty;
					$g_actual_qty	+= $t_actual_qty;
					$g_balance_qty	+= $t_balance_qty;
					
					echo "
						<tr class=\"subtotal\">
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							
							<td></td>
							<td>".number_format($t_quantity,2)."</td>
							<td></td>
							<td>".number_format($t_mcd_qty,2)."</td>
							<td>".number_format($t_budget_qty,2)."</td>
							<td>".number_format($t_actual_qty,2)."</td>
							<td>".number_format($t_balance_qty,2)."</td>
						</tr>
					";
				endforeach;
				
				echo "
					<tr class=\"grandtotal\">
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						
						<td></td>
						<td>".number_format($g_quantity,2)."</td>
						<td></td>
						<td>".number_format($g_mcd_qty,2)."</td>
						<td>".number_format($g_budget_qty,2)."</td>
						<td>".number_format($g_actual_qty,2)."</td>
						<td>".number_format($g_balance_qty,2)."</td>
					</tr>
				";
                ?>
            </tbody>
        </table>
    </div><!--End of Form-->
</div>
</body>
</html>