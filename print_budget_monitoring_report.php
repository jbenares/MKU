<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	#echo "<B>UNDER CONSTRUCTION</B>";
	
	$options=new options();	
	
	$project_id=$_REQUEST[id];
	$work_category_id		= $_REQUEST['work_category_id'];
	$sub_work_category_id	= $_REQUEST['sub_work_category_id'];
	
	
	function budget_monitoring($stock_id,$project_id,$work_category_id,$sub_work_category_id){
		#get po quantity
		$result = mysql_query("
			select
				sum(quantity) as pr_qty
			from
				pr_header as h, pr_detail as d
			where
				h.pr_header_id = d.pr_header_id
			and status != 'C'
			and project_id = '$project_id'
			and work_category_id = '$work_category_id'
			and sub_work_category_id = '$sub_work_category_id'
			and stock_id = '$stock_id'
			and allowed = '1'
		") or die(mysql_error());
		$r = mysql_fetch_assoc($result);
		$pr_qty = $r['pr_qty'];
		
		#get pr reference
		$result = mysql_query("
			select
				h.pr_header_id
			from
				pr_header as h, pr_detail as d
			where
				h.pr_header_id = d.pr_header_id
			and status != 'C'
			and project_id = '$project_id'
			and work_category_id = '$work_category_id'
			and sub_work_category_id = '$sub_work_category_id'
			and stock_id = '$stock_id'
			and allowed = '1'
		") or die(mysql_error());
		$aPRID = array();
		while($r = mysql_fetch_assoc($result)){
			$aPRID[] = $r['pr_header_id'];	
		}
		#end of get pr reference
		
		
		$sql = "
			select
				sum(quantity) as po_qty
			from	
				po_header as h, po_detail as d, productmaster as p
			where
				h.po_header_id = d.po_header_id
			and d.stock_id = p.stock_id
			and h.status != 'C'
			and project_id = '$project_id'
			and work_category_id = '$work_category_id'
			and	 sub_work_category_id = '$sub_work_category_id'
			and po_type = 'M'
			and ( d.stock_id = '$stock_id' or p.parent_stock_id = '$stock_id' )
		";
		if($aPRID) $sql .= "and h.pr_header_id in (".implode(',',$aPRID).")";
		
		$result = mysql_query($sql) or die(mysql_error());
		$r = mysql_fetch_assoc($result);
		$po_qty = $r['po_qty'];
		
		#get po reference
		
		$sql = "
			select
				h.po_header_id
			from	
				po_header as h, po_detail as d, productmaster as p
			where
				h.po_header_id = d.po_header_id
			and d.stock_id = p.stock_id
			and h.status != 'C'
			and project_id = '$project_id'
			and work_category_id = '$work_category_id'
			and	 sub_work_category_id = '$sub_work_category_id'
			and po_type = 'M'
			and ( d.stock_id = '$stock_id' or p.parent_stock_id = '$stock_id' )
		";
		
		if($aPRID) $sql .= "and h.pr_header_id in (".implode(',',$aPRID).")";
		
		$aPOID = array();
		$result = mysql_query($sql) or die(mysql_error());
		while($r = mysql_fetch_assoc($result)){
			$aPOID[] = $r['po_header_id'];
		}
		#end of get po reference
		
		$sql = "
			select
				sum(quantity) as rr_qty, sum(amount) as amount
			from	
				rr_header as h, rr_detail as d, po_header as po, productmaster as p
			where
				h.rr_header_id = d.rr_header_id
			and h.po_header_id = po.po_header_id
			and d.stock_id = p.stock_id
			and h.status != 'C'
			and h.project_id = '$project_id'
			and po.work_category_id = '$work_category_id'
			and po.sub_work_category_id = '$sub_work_category_id'
			and ( d.stock_id = '$stock_id' or p.parent_stock_id = '$stock_id' )
		";
		
		if($aPOID) $sql .= "and h.po_header_id in (".implode(',',$aPOID).")";
		
		$result = mysql_query($sql) or die(mysql_error());
		$r = mysql_fetch_assoc($result);
		$rr_qty = $r['rr_qty'];
		$rr_amount = $r['amount'];
		
		$result = mysql_query("
			select
				sum(quantity) as ris_qty, sum(amount) as amount
			from
				issuance_header as h, issuance_detail as d, productmaster as p
			where
				h.issuance_header_id = d.issuance_header_id
			and d.stock_id = p.stock_id
			and h.status != 'C'
			and project_id = '$project_id'
			and work_category_id = '$work_category_id'
			and sub_work_category_id = '$sub_work_category_id'
			and ( d.stock_id = '$stock_id' or p.parent_stock_id = '$stock_id' )
		") or die(mysql_error());
		
		$r = mysql_fetch_assoc($result);
		$ris_qty = $r['ris_qty'];
		$ris_amount = $r['amount'];
		
		$aList = array();
		$aList['pr_qty'] = $pr_qty;
		$aList['po_qty'] = $po_qty;
		$aList['rr_qty'] = $rr_qty;
		$aList['ris_qty'] = $ris_qty;
		
		$aList['rr_amount'] = $rr_amount;
		$aList['ris_amount'] = $ris_amount;
		
		return $aList;
	}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>ORDER SHEET</title>
<script>
function printPage() { print(); } //Must be present for Iframe printing
</script>
<style type="text/css">
	
body
{
	size: legal portrait;		
	padding:0px;
	font-family:Arial, Helvetica, sans-serif;
	font-size:12px;
}
.container{
	width:100%;
}

.header
{
	text-align:center;	
	margin-top:20px;
}

.header table, .content table
{
	
	text-align:left;
	

}
.header table td, .content table td
{
	padding:3px;
	
}

.content table{
	margin-top:10px;
	margin-left:30px;
	width:80%;
	border-collapse:collapse;
}
.content table td,.content table th{
	padding:5px;
}
.content table tr th{
	border-top:1px solid #000;
	border-bottom:1px solid #000;
	text-align:center;
}

.content table tr:last-child td{
	border-top:1px solid #000;
}
hr
{
	margin:40px 0px;	
	border:1px dashed #999;

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

.footer td{
	border:none;
}
.align-right{
	text-align:right;	
}
.align-center{
	text-align:center;	
}

.costing-header{
	margin-left:10px;	
}

.last-content{
	page-break-after:always;  
} 


</style>
</head>
<body>
<div class="container">
	<?php
		require("form_heading.php");
	?>
	
	<?php
	$query="
		select
			 *		  
		 from
			  projects
		 where
			project_id = '$project_id'

	";
	$result=mysql_query($query) or die(mysql_error());
	$r=mysql_fetch_assoc($result);
	
	$project_id		= $r['project_id'];
	$project_name	= $r['project_name'];
	$owner			= $r['owner'];
	$location		= $r['location'];
	?>

	<div class="header" style="font-weight:bold; margin-bottom:20px;">
        <table>
            <tr>
                <td>Project</td>
                <td>: <?=$project_name?></td>
            </tr>
            <tr>
              <td>Location</td>
              <td >: <?=$location?></td>
            </tr>
            <tr>
              <td>Owner</td>
              <td >: <?=$owner?></td>
            </tr>
        </table>
    </div><!--End of header-->
	
    
    <?php
    $sql = "select
			  budget_header_id
		 from
			  budget_header as h,
			  projects as p
		 where
			h.project_id = p.project_id
		and
			  h.project_id ='$project_id'
	";
	
	if($work_category_id){
	$sql.=" and work_category_id = '$work_category_id' ";	
	}
	
	if($sub_work_category_id){
	$sql.=" and sub_work_category_id = '$sub_work_category_id' ";	
	}
	
	$sql .= "
		order by
			work_category_id asc, 
			sub_work_category_id asc
	";	
	$result = mysql_query($sql) or die(mysql_error());
	$list = array();
	while($r=mysql_fetch_assoc($result)){
		$budget_header_id = $r['budget_header_id'];
		array_push($list,$budget_header_id);
	}
	?>
    
    
    
	<?php
	$total_material_cost 	= 0;
	$total_labor_cost		= 0;
	$total_equipment_cost	= 0;
	$total_fuel_cost		= 0;
    foreach($list as $budget_header_id):
    ?>    
		<?php
        $query="
            select
                 *		  
             from
                  budget_header as h,
                  projects as p,
				  budget_detail as d
             where
                h.project_id = p.project_id
            and
				h.budget_header_id = d.budget_header_id
			and
                h.budget_header_id = '$budget_header_id'
        ";
        $result=mysql_query($query);
		if(mysql_num_rows($result) <= 0){
			continue;
		}
		
        $r=mysql_fetch_assoc($result);
        
        $project_id		= $r['project_id'];
        $project_name	= $r['project_name'];
        $owner			= $r['owner'];
        $location		= $r['location'];
        $work_category_id		= $r['work_category_id'];
        $sub_work_category_id	= $r['sub_work_category_id'];
        
        $work_category		= $options->attr_workcategory($work_category_id,'work');
        $sub_work_category	= $options->attr_workcategory($sub_work_category_id,'work');
            
        $status			= $r['status'];
        ?>
        
        <div class="header" style="font-weight:bold; margin-bottom:20px;">
            <table style="width:100%;">
                <tr>
                  <td width="80">Item</td>
                  <td >: <?=$work_category?></td>
                </tr>
                <tr>
                  <td width="80">Sub Item</td>
                  <td >: <?=$sub_work_category?></td>
                </tr>
               
            </table>
        </div><!--End of header-->

		<div class="content" >
        <?php
		$query="
			select
				budget_detail_id,
				d.stock_id,
				p.stockcode,
				p.stock,
				p.unit,
				d.quantity,
				d.cost,
				d.amount
			from
				budget_detail as d, productmaster as p
			where
				d.stock_id = p.stock_id
			and
				budget_header_id='$budget_header_id'
			order by stock asc
		";
		
		$result=mysql_query($query) or die(mysql_error());		
		$rows = mysql_num_rows($result);
		if($rows > 0):
		?>
        	<table cellspacing="0">
            	<tr>
                	<th>DESCRIPTION</th>
                    <th style="width:10%; text-align:right;">BUDGET</th>
                    <th style="width:10%; text-align:right;">COST</th>
                    <th style="width:10%; text-align:right;">BUDGET AMOUNT</th>
                    <th style="width:10%; text-align:right;">RTP</th>
                    <th style="width:10%; text-align:right;">PO</th>
                    <th style="width:10%; text-align:right;">MRR</th>
                    <th style="width:10%; text-align:right;">RIS</th>
                    <th style="width:10%; text-align:right;">BALANCE<br />(MRR)</th>
                    <th style="width:10%; text-align:right;">BALANCE<br />(MRR) AMOUNT</th>
                    <th style="width:10%; text-align:right;">BALANCE<br />(RIS)</th>
                    <th style="width:10%; text-align:right;">BALANCE<br />(RIS) AMOUNT</th>
                </tr>
           		<?php
				$total_amount= $total_budget_amount = $total_mrr_budget_amount = $total_ris_budget_amount = 0;
				while($r=mysql_fetch_assoc($result)):
					
					$stock_id		= $r['stock_id'];
					$stock 			= $r['stock'];
					$stockcode		= $r['stockcode'];
					$unit			= $r['unit'];
					
					$quantity 		= $r['quantity'];
					$cost			= $r['cost'];
					$amount			= $r['amount'];
					
					$total_amount += $amount;
					
					$budget = budget_monitoring($stock_id,$project_id,$work_category_id,$sub_work_category_id);
					$budget_amount = $quantity * $cost;
					
					$mrr_budget_quantity = $quantity - $budget['rr_qty'];
					$ris_budget_quantity = $quantity - $budget['ris_qty'];
					
					//$mrr_budget_amount = $mrr_budget_quantity * $cost;
					//$ris_budget_amount = $ris_budget_quantity * $cost;
					$mrr_budget_amount = ($quantity * $cost) - $budget['rr_amount'];
					$ris_budget_amount = ($quantity * $cost) - $budget['ris_amount'];
					
					$total_budget_amount += $budget_amount;
					$total_mrr_budget_amount += $mrr_budget_amount;
					$total_ris_budget_amount += $ris_budget_amount;
				?>
                    <tr>
                        <td><?=htmlentities($stock)?></td>
                        <td style="text-align:right;"><?=number_format($quantity,2,'.',',')?></td>
                        <td style="text-align:right;"><?=number_format($cost,2,'.',',')?></td>
                        <td style="text-align:right;"><?=number_format($budget_amount,2,'.',',')?></td>
                        <td style="text-align:right;"><?=number_format($budget['pr_qty'],2,'.',',')?></td>
                        <td style="text-align:right;"><?=number_format($budget['po_qty'],2,'.',',')?></td>
                        <td style="text-align:right;"><?=number_format($budget['rr_qty'],2,'.',',')?></td>
                        <td style="text-align:right;"><?=number_format($budget['ris_qty'],2,'.',',')?></td>
                        <td style="text-align:right;"><?=number_format($mrr_budget_quantity,2,'.',',')?></td>
                        <td style="text-align:right;"><?=number_format($mrr_budget_amount,2,'.',',')?></td>
                        <td style="text-align:right;"><?=number_format($ris_budget_quantity,2,'.',',')?></td>
                        <td style="text-align:right;"><?=number_format($ris_budget_amount,2,'.',',')?></td>
                    </tr>
                <?php endwhile; ?>
               		<tr>
                        <td></td>
                        <td style="text-align:right;"></td>
                        <td style="text-align:right;"></td>
                        <td style="text-align:right;"><?=number_format($total_budget_amount,2,'.',',')?></td>
                        <td style="text-align:right;"></td>
                        <td style="text-align:right;"></td>
                        <td style="text-align:right;"></td>
                        <td style="text-align:right;"></td>
                        <td style="text-align:right;"></td>
                        <td style="text-align:right;"><?=number_format($total_mrr_budget_amount,2,'.',',')?></td>
                        <td style="text-align:right;"></td>
                        <td style="text-align:right;"><?=number_format($total_ris_budget_amount,2,'.',',')?></td>
                    </tr>
                
               
            </table>
        <?php
		endif;
        ?>
        </div><!--End of content-->
                
    <?php
    endforeach;
    ?>   
    
    </div>
</div>
</body>
</html>

