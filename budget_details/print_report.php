<?php
	ob_start();
	session_start();

	include_once("../conf/ucs.conf.php");
	include_once("../library/lib.php");

	$project_id		= $_REQUEST['project_id'];
	$work_category_id		= $_REQUEST['work_category_id'];
	$section		= $_REQUEST['section'];
	$sub_work_category_id = $_REQUEST['sub_work_category_id'];
	
		$hd = "SELECT * FROM budget_header b, projects p, work_category w
					WHERE b.project_id = '$project_id' AND b.work_category_id = '$work_category_id' AND b.sub_work_category_id = '$sub_work_category_id'
						AND b.project_id = p.project_id AND b.work_category_id = w.work_category_id";
		$rs_hd = mysql_query($hd);
		$rw_hd = mysql_fetch_assoc($rs_hd);
		$headerId = $rw_hd['budget_header_id'];
		$project_name = $rw_hd['project_name'];
		$work = $rw_hd['work'];
		
		if($section == 'a')
		{
			$section_statement = "";
			
		}else{
		
			$section_statement = "AND d.section_id = '$section'";
			
		}
		
		$det = "SELECT * FROM budget_section_detail d, sections s, productmaster m
					WHERE d.budget_header_id = '$headerId' $section_statement AND d.section_id = s.section_id AND d.stock_id = m.stock_id
						ORDER BY s.section_id, m.stock_id, d.date_added";
		$rs_det = mysql_query($det);
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>BUDGET DETAILS</title>
<script>
function printPage() { print(); } //Must be present for Iframe printing
</script>
<style type="text/css">

body
{
	size: legal portrait;
	padding:0px;
	font-family:Arial, Helvetica, sans-serif;
	font-size:11px;
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

table{ border-collapse:collapse; width:100%; }
table td{ padding:3px; }
table thead td{ font-weight:bold;  border-top:1px solid #000; border-bottom:1px solid #000; }
table tfoot td{
	border-top:1px solid #000;
	font-weight:bold;	
}
@media print{
	table { page-break-inside:auto }
	tr{ page-break-inside:avoid; page-break-after:auto }
	thead { display:table-header-group }
	tfoot { display:table-footer-group }
	.pb { page-break-after:always }
}

</style>

</head>
<body>
	<table>
    	<thead>
        	<tr>
            	<td colspan="7">
                	<div>
                    	
                        BUDGET DETAILS REPORT<br />
						<?php echo $project_name; ?><br />
						<?php echo $work; ?>
                        
                    </div>
                </td>
            </tr>
            <tr>
				<td><b>#</b></td>
				<td><b>Section</b></td>
				<td><b>Material</b></td>
				<td><b>Qty Used</b></td>				
            </tr>
        </thead>
        <tbody>
	<?php

		
		$i=1;
		while($r=mysql_fetch_array($rs_det)) {
			//$dt_da = date("M d, Y",strtotime($r['date_requested']));			
			echo '<tr>';
			echo '<td>'.$i.'.</td>';
			echo '<td>'.$r[section_name].'</td>';						
			echo '<td>'.$r[stock].'</td>';
			echo '<td>'.$r[qty_used].'&nbsp;'.$r[unit].'</td>';			
			echo '</tr>';
			
			$i++;
		}		
	?>
        </tbody>
    </table>
</body>
</html>
