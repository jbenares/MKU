<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
	$from_date		= $_REQUEST['from_date'];
	$to_date		= $_REQUEST['to_date'];
	$categ_id		= $_REQUEST['categ_id'];
	$supplier 		= $_REQUEST['supplier'];
	$po_header_id	= $_REQUEST['po_header_id'];
	$stock_id		= $_REQUEST['stock_id'];
	$allowed		= $_REQUEST['allowed'];
	$work_category_id	= $_REQUEST['work_category_id'];
	$sub_work_category_id = $_REQUEST['sub_work_category_id'];
	
	function getPOQty($pr_header_id,$from_date,$to_date,$project_id,$work_category_id,$sub_work_category_id,$stock_id){
		$sql = "
			select 
				sum(quantity) as quantity
			from
				po_header as h, po_detail as d
			where
				h.po_header_id = d.po_header_id
			and
				pr_header_id = '$pr_header_id'
			and
			(
				stock_id = '$stock_id' or stock_id in (select stock_id from productmaster where parent_stock_id = '$stock_id')
			)
			and
				status != 'C'
			-- and h.approval_status = 'A' 
		";
		if($from_date && $to_date){
		$sql .= "
			and date between '$from_date' and '$to_date'
		";	
		}
		
		if($project_id){
		$sql .= "
			and project_id = '$project_id'
		";	
		}
		
		if($work_category_id){
		$sql .= "
			and work_category_id = '$work_category_id'
		";	
		}
		
		if($sub_work_category_id){
		$sql .= "
			and sub_work_category_id = '$sub_work_category_id'
		";	
		}
		//echo $sql. "<br>";
		
		$result =  mysql_query($sql) or die(mysql_error());	
		$r = mysql_fetch_assoc($result);	
		return $r['quantity'];
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
<link rel="stylesheet" type="text/css" href="css/print.css"/>
</head>
<body>
<div class="container">
	
     <div><!--Start of Form-->
     	
     	<div style="font-weight:bolder;">
        	RTP HISTORY REPORT <br />
            <?=$project?> <br />
            From <?=date("m/d/Y",strtotime($from_date))?> to <?=date("m/d/Y",strtotime($to_date))?>
        </div>           
        <div class="content" style="">
        	<table cellpadding="3">
            	<tr>
                	<th style="text-align:left;">DATE</th>
                    <th style="text-align:left;">PROJECT</th>
                    <th style="text-align:left;">RTP#</th>
                    <th style="text-align:left;">ITEM</th>
                    <th style="text-align:right;">QTY</th>
                    <th style="text-align:left;">UNIT</th>
                    <th style="text-align:left;">PO QTY</th>
                </tr>	
                
             	<?php
					$query="
						select
							h.project_id,
							h.date,
							h.pr_header_id,
							stock,
							sum(d.quantity) as quantity,
							p.unit,
							d.stock_id
						from
							pr_header as h, pr_detail as d, productmaster as p
						where
							h.pr_header_id = d.pr_header_id
						and
							d.stock_id = p.stock_id
						and
							h.status != 'C'
						and
							h.date between '$from_date' and '$to_date'
					";
					
					$query.= ($allowed) ? "and allowed = '1'" : "and allowed = '0'";
					
					if($categ_id) $query.=" and p.categ_id1 = '$categ_id' ";
					
					if($stock_id) $query.=" and d.stock_id= '$stock_id'";	
					
					if($work_category_id) $query.=" and work_category_id = '$work_category_id' ";	
					
					
					if($sub_work_category_id) $query.=" and sub_work_category_id = '$sub_work_category_id' ";	
					
					$query.=" group by h.pr_header_id, d.stock_id order by h.date asc, h.pr_header_id asc ";
					
					$result=mysql_query($query) or die(mysql_error());
					$total_quantity = 0;
					$total_po_qty = 0;
					while($r=mysql_fetch_assoc($result)){
						$total_quantity += $r['quantity'];
						$po_qty = getPOQty($r['pr_header_id'],$from_date,$to_date,$r['project_id'],$work_category_id,$sub_work_category_id,$r['stock_id']);
						$total_po_qty += $po_qty;
				?>	
                    <tr>
                        <td><?=date("m/d/Y",strtotime($r['date']))?></td>
                        <td><?=$options->getAttribute('projects','project_id',$r['project_id'],'project_name');?></td>                       
                        <td><?=str_pad($r['pr_header_id'],7,0,STR_PAD_LEFT)?></td>                       
                        <td><?=htmlentities($r['stock'])?></td>                       
                        <td style="text-align:right;"><?=number_format($r['quantity'],4,'.',',')?></td>                       
                        <td><?=$r['unit']?></td>             
                        <td style="text-align:right;"><?=number_format($po_qty,4,'.',',')?></td>                       
                    </tr>
				<?php } ?>
                <tr>
                	<td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td style="text-align:right; font-weight:bold; border-bottom:1px solid #000;"><?=number_format($total_quantity,4,'.',',')?></td>
                    <td>&nbsp;</td>
                    <td style="text-align:right; font-weight:bold; border-bottom:1px solid #000;"><?=number_format($total_po_qty,4,'.',',')?></td>
                </tr>
            </table>
        
        </div><!--End of content-->
    </div><!--End of Form-->
</div>
</body>
</html>