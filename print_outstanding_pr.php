<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	require_once(dirname(__FILE__).'/library/lib.php');
	
	$options=new options();	
	$from_date		= $_REQUEST['from_date'];
	$to_date		= $_REQUEST['to_date'];
	$project_id		= $_REQUEST['project_id'];
	$project 		= $options->getAttribute("projects","project_id",$project_id,"project_name");
	$stock_id		= $_REQUEST['stock_id'];
	$sortby			= $_REQUEST['sortby'];
	
	
	$que = "
		select
		h.pr_header_id,
		h.date,
		pr.project_name,
		d.quantity,
		d.request_quantity,
		p.stock,
		p.stock_id,
		p.unit
		from pr_header as h,
		pr_detail as d,
		productmaster as p,
		projects as pr
		where
		h.pr_header_id = d.pr_header_id and
		h.date between '$from_date' and '$to_date' and
		d.stock_id = p.stock_id and
		h.project_id = pr.project_id and
		h.approval_status = 'A'
	";
	
	if(!empty($project_id)){
	$que .= " and h.project_id = '$project_id'";	
		
	}
	
	if(!empty($stock_id)){
	$que .= " and p.stock_id = '$stock_id'";
	}	
	

	$que .= " 	group by d.pr_detail_id
				order by
				$sortby asc";

	

				
	$result = mysql_query($que) or die (mysql_error());

	
	
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>JOB ORDER</title>
<script>
function printPage() { print(); } //Must be present for Iframe printing
</script>
<link rel="stylesheet" type="text/css" href="css/print.css"/>
<style type="text/css">
.gone td{
	/*color:#F00;*/
	display:none;
}

.mini-table{
	border-collapse: collapse;
}
.mini-table tfoot td{
	border-top: 1px solid #000;
	font-weight: bold;
}

th{
	text-align: left;
}
</style>
</head>
<body>
<div class="container">
	
     <div><!--Start of Form-->
     	
     	<div style="font-weight:bolder;">
        	OUTSTANDING PR REPORT (APPROVED)<br />
            <?=$project?> <br />
            From <?=date("m/d/Y",strtotime($from_date))?> to <?=date("m/d/Y",strtotime($to_date))?>
        </div>           
        <div class="content" style="">
        	<table cellpadding="6">
            	<tr>
                    <th>PR Reference #</th>
                    <th>PR Date</th>
                    <th>Project</th>               
                    <th>Item</th>
                    <th>Unit</th>
					<th>PR Qty</th>
					<th>PO Reference #</th>
					<th>PO Date</th>
					<th>Supplier</th>
					<th>PO Qty</th>
					<th>Balance</th>
                </tr>	
                <?php while($res = mysql_fetch_assoc($result)){ 
				
				 $pr = $res['pr_header_id'];				 
				 $stock_id = $res['stock_id'];
				
				$q1 = mysql_query("
								select 
								h.po_header_id,
								h.date,
								d.quantity,
								s.account,
								d.po_detail_id
								from
								po_header as h,
								po_detail as d,
								supplier as s
								where
								h.po_header_id = d.po_header_id and
								h.supplier_id = s.account_id and
								h.`status` != 'C' and
								h.pr_header_id = '$pr' and
								d.stock_id = '$stock_id'
				") or die (mysql_error());
				
				$r1 = mysql_fetch_assoc($q1);
				$balance = 0;
				$balance = $res['quantity']-$r1['quantity'];
				
				$total_balance += $balance;
				$total_pr_qty += $res['quantity'];
				$total_po_qty += $r1['quantity'];
				
				
?>
				<tr>	
					<td><?=str_pad($res['pr_header_id'],7,0,STR_PAD_LEFT)?></td>
					<td><?=$res['date']?></td>
					<td><?=$res['project_name']?></td>
					<td><?=$res['stock']?></td>
					<td><?=$res['unit']?></td>
					<td style="color: red; text-align: right;"><?=number_format($res['quantity'],2)?></td>			
					<td><?=$r1['po_header_id']?></td>			
					<td><?=$r1['date']?></td>			
					<td><?=$r1['account']?></td>			
					<td style="color: red; text-align: right;"><?=number_format($r1['quantity'],2)?></td>			
					<td style="text-align: right;"><?=number_format($balance,2)?></td>			
					<td></td>			
				</tr>
             	<?php 
				}
				?>
				<tr>
					<th colspan="5"></th>
					<th><?=number_format($total_pr_qty,2)?></th>
					<th colspan="3"></th>
					<th><?=number_format($total_po_qty,2)?></th>
					<th><?=number_format($total_balance,2)?></th>
				</tr>
            </table>
        
        </div><!--End of content-->
    </div><!--End of Form-->
</div>
</body>
</html>