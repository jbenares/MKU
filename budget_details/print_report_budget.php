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
		
			$query = "
						select
							*
						from
							budget_detail b, productmaster m
						where
							b.budget_header_id = '$headerId' AND m.stock_id = b.stock_id
						order by m.stock asc											
					";
			$result=mysql_query($query) or die(mysql_error());
	
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
                    	
                        BUDGET LIST REPORT<br />
						<?php echo $project_name; ?><br />
						<?php echo $work; ?>
                        
                    </div>
                </td>
            </tr>
            <tr>
				<td><b>#</b></td>
				<td><b>Material</b></td>
				<td><b>Left</b></td>	
				<td><b>Unit</b></td>			
            </tr>
        </thead>
        <tbody>
	<?php
		$i=1;
		while($r=mysql_fetch_assoc($result)){
				$stock_id	= $r['stock_id'];
				$stock = htmlentities($r['stock']);
				$quantity = $r['quantity'];
				$unit = $r['unit'];
					
					$ch = "SELECT *, sum(s.qty_used) as t_qty_u FROM budget_section_detail s, budget_detail b
								WHERE s.budget_header_id = '$headerId'
									AND s.stock_id = b.stock_id AND s.stock_id = '$stock_id' AND s.budget_detail_id = b.budget_detail_id
										";
					$rss = mysql_query($ch);
					$numrow = mysql_num_rows($rss);
					if($numrow > 0)
					{
						while($rw = mysql_fetch_assoc($rss))
						{
							$total_qty_used = $rw['t_qty_u'];
							$qty_left = $quantity - $total_qty_used;
						}
					}else{ 
						$qty_left = $quantity; 
					}
					
					echo '<tr>';
					echo '<td>'.$i.'.</td>';
					echo '<td>'.$stock.'</td>';
					echo '<td>'.$qty_left.'</td>';						
					echo '<td>'.$unit.'</td>';		
					echo '</tr>';
					
			$i++;
		}		
	?>
        </tbody>
    </table>
</body>
</html>
