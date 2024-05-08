<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
	
	$project_id=$_REQUEST['id'];
	
	function getLabor($project_id){
		$sql = "
			select
				sub_description,
				sum(quantity) as quantity,
				sum(amount) as amount
			FROM
				po_header as h, spo_detail as d, sub_spo_detail as s
			where 
				h.po_header_id = d.po_header_id
			and d.spo_detail_id = s.spo_detail_id
			and h.`status` != 'C'
			and h.project_id = '$project_id'
			group by sub_description
			order by sub_description asc

		";
		$result = mysql_query($sql) or die(mysql_error());
		$a = array();
		while( $r = mysql_fetch_assoc($result)  ){
			$a[] = $r;
		}
		
		return $a;
	}
	
	
	function getEUR($project_id){
		$sql = "
			select
				p.stock,
				sum( computed_time ) as total_actual_hrs,
				sum( IF( ed.eur_charge_type_id = 1 , ed.computed_time , IF(ed.eur_charge_type_id = 3, 0 , IF(ed.computed_time >= 4, 4 , ed.computed_time) ) ) ) as total_charged_hrs,
				eh.rate_per_hour,
				p.stock_id
			from
				po_detail as po,
				po_header as po_h,
				eur_header as eh, 
				eur_detail as ed, 			
				productmaster as p
			where
				eh.eur_header_id = ed.eur_header_id
			and ed.po_detail_id = po.po_detail_id
			and	po.po_header_id = po_h.po_header_id
			and eh.stock_id = p.stock_id
			and	ed.eur_void = '0'
			and po_h.status != 'C'
			and po_h.project_id = '$project_id'
			group by p.stock_id
			order by stock asc
		";
		$result = mysql_query($sql) or die(mysql_error());
		$a = array();
		while( $r = mysql_fetch_assoc($result)  ){
			$arr = getBudgetOfItem($r['stock_id'],$project_id);

			$r['amount'] = $r['total_charged_hrs'] * $r['rate_per_hour'];
			$r['budget_quantity'] = $arr['budget_quantity'];
			$r['budget_amount'] = $arr['budget_amount'];

			$a[] = $r;
		}
		
		return $a;
	}
	
	function getBudgetOfItem($stock_id,$project_id){
		$sql = "
			select
				sum(quantity) as budget_quantity, sum(amount) as budget_amount
			from
				budget_header as h, budget_detail as d
			where
				h.budget_header_id = d.budget_header_id
			and h.status != 'C'
			and h.project_id = '$project_id'
			and d.stock_id = '$stock_id'
		";
		$result = mysql_query($sql) or die(mysql_error());
		$r = mysql_fetch_assoc($result);
		
		return $r;
	}
	
	function getFuelConsumption($project_id){
		$result = mysql_query("
			select 
				stock, sum(amount) as amount, sum(quantity) as quantity, d.stock_id
			from
				issuance_header as h, issuance_detail as d, productmaster as p
			where
				h.issuance_header_id = d.issuance_header_id
			and d.stock_id = p.stock_id
			and h.status != 'C'
			and ( categ_id1 = '6' or categ_id2 = '6' or categ_id3 = '6' or categ_id4 = '6' )
			and h.project_id = '$project_id'
			group by d.stock_id
			
		") or die(mysql_error);
		$a = array();
		while( $r = mysql_fetch_assoc($result) ){
			$aBudget = getBudgetOfItem($r['stock_id'],$project_id);
			$r['budget_quantity'] 	= $aBudget['budget_quantity'];
			$r['budget_amount'] 	= $aBudget['budget_amount'];
			$a[] = $r;	
		}
		
		return $a;
		
	}
	
	function budget_monitoring($stock_id,$project_id){
		$result = mysql_query("
			select
				sum(request_quantity) as pr_qty
			from
				pr_header as h, pr_detail as d
			where
				h.pr_header_id = d.pr_header_id
			and
				status != 'C'
			and
				project_id = '$project_id'
			and
				stock_id = '$stock_id'
			and
				allowed = '1'
		") or die(mysql_error());
		$r = mysql_fetch_assoc($result);
		$pr_qty = $r['pr_qty'];
		
		$result = mysql_query("
			select
				sum(quantity) as po_qty
			from	
				po_header as h, po_detail as d
			where
				h.po_header_id = d.po_header_id
			and
				status != 'C'
			and
				project_id = '$project_id'
			and
				stock_id = '$stock_id'
			and
				po_type = 'M'
		") or die(mysql_error());
		$r = mysql_fetch_assoc($result);
		$po_qty = $r['po_qty'];
		
		$result = mysql_query("
			select
				sum(quantity) as rr_qty, sum(amount) as amount
			from	
				rr_header as h, rr_detail as d, po_header as po
			where
				h.rr_header_id = d.rr_header_id
			and
				h.po_header_id = po.po_header_id
			and
				h.status != 'C'
			and
				h.project_id = '$project_id'
			and
				stock_id = '$stock_id'
		") or die(mysql_error());
		$r = mysql_fetch_assoc($result);
		$rr_qty = $r['rr_qty'];
		$rr_amount = $r['amount'];
		
		$result = mysql_query("
			select
				sum(quantity) as ris_qty, sum(amount) as amount
			from
				issuance_header as h, issuance_detail as d
			where
				h.issuance_header_id = d.issuance_header_id
			and
				status != 'C'
			and
				project_id = '$project_id'
			and
				stock_id = '$stock_id'
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
	width:90%;
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
    </div>
	

    <div class="content" >
    <?php
    $query="
        select
            budget_detail_id,
            d.stock_id,
            p.stockcode,
            p.stock,
            p.unit,
            sum(d.quantity) as quantity,
			d.cost
        from
			budget_header as h,
            budget_detail as d,
			productmaster as p
        where
			h.budget_header_id = d.budget_header_id
		and
            d.stock_id = p.stock_id
        and
			project_id = '$project_id'
		and
			h.status != 'C'
		group by d.stock_id
        order by stock asc
    ";
    
    $result=mysql_query($query) or die(mysql_error());		
    $rows = mysql_num_rows($result);
    if($rows > 0):
    ?>
        <table cellspacing="0">
            <tr>
                <th>DESCRIPTION</th>
                <th style="width:10%; text-align:right;">COST</th>
                <th style="width:10%; text-align:right;">BUDGET</th>
                <th style="width:10%; text-align:right;">BUDGET<br />AMOUNT</th>
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
            $total_quantity = $total_budget_amount  = $total_mrr_budget_amount = $total_ris_budget_amount = 0;
            while($r=mysql_fetch_assoc($result)):
                
                $stock_id		= $r['stock_id'];
                $stock 			= $r['stock'];
                $stockcode		= $r['stockcode'];
                $unit			= $r['unit'];
                $cost			= $r['cost'];
                $quantity 		= $r['quantity'];
                
                $total_quantity += $quantity;
				$budget = budget_monitoring($stock_id,$project_id,$work_category_id,$sub_work_category_id);
				
				$budget_amount = $quantity * $cost;
				#$mrr_budget_amount = ($quantity - $budget['rr_qty']) * $cost;
                #$ris_budget_amount = ($quantity - $budget['ris_qty']) * $cost;
				$mrr_budget_amount = ($quantity * $cost) - $budget['rr_amount'];
				$ris_budget_amount = ($quantity * $cost) - $budget['ris_amount'];
				
				$total_budget_amount += $budget_amount;
				$total_mrr_budget_amount += $mrr_budget_amount;
				$total_ris_budget_amount += $ris_budget_amount;
            ?>
                <tr>
                    <td><?=htmlentities($stock)?></td>
                    <td style="text-align:right;"><?=number_format($cost,2,'.',',')?></td>
                    <td style="text-align:right;"><?=number_format($quantity,2,'.',',')?></td>
                    <td style="text-align:right;"><?=number_format($budget_amount,2,'.',',')?></td>
                    <td style="text-align:right;"><?=number_format($budget['pr_qty'],2,'.',',')?></td>
                    <td style="text-align:right;"><?=number_format($budget['po_qty'],2,'.',',')?></td>
                    <td style="text-align:right;"><?=number_format($budget['rr_qty'],2,'.',',')?></td>
                    <td style="text-align:right;"><?=number_format($budget['ris_qty'],2,'.',',')?></td>
                    <td style="text-align:right;"><?=number_format($quantity - $budget['rr_qty'],2,'.',',')?></td>
                    <td style="text-align:right;"><?=number_format($mrr_budget_amount,2,'.',',')?></td>
                    <td style="text-align:right;"><?=number_format($quantity - $budget['ris_qty'],2,'.',',')?></td>
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
    <?php endif; ?>
    
    </div><!--End of content-->
    <div class="content">
    	<table cellspacing="0">
        	<caption style="text-align:left; font-weight:bold;">FUEL CONSUMPTION</caption>
            <tr>
            	<th style="text-align:left;">DESCRIPTION</th>
                <th style="width:10%; text-align:right;">BUDGET QUANTITY</th>
                <th style="width:10%; text-align:right;">BUDGET AMOUNT</th>
                <th style="width:10%; text-align:right;">TOTAL ISSUED QUANTITY</th>
                <th style="width:10%; text-align:right;">TOTAL ISSUED AMOUNT</th>
                
            </tr>
            <?php
			$aFuelCost = getFuelConsumption($project_id);
			if($aFuelCost):
				$t_quantity = $t_amount = $t_budget_quantity = $t_budget_amount = 0;
				foreach( $aFuelCost as $r ):
					$t_quantity += $r['quantity'];
					$t_amount 	+= $r['amount'];
					$t_budget_quantity += $r['budget_quantity'];
					$t_budget_amount	+= $r['budget_amount'];
					echo "
						<tr>
							<td>$r[stock]</td>
							<td style='text-align:right;'>".number_format($r['budget_quantity'],2)."</td>
							<td style='text-align:right;'>".number_format($r['budget_amount'],2)."</td>
							<td style='text-align:right;'>".number_format($r['quantity'],2)."</td>
							<td style='text-align:right;'>".number_format($r['amount'],2)."</td>
						</tr>
					";
				endforeach;
				echo "
					<tr>
						<td></td>
						<td style='text-align:right;'>".number_format($t_budget_quantity,2)."</td>
						<td style='text-align:right;'>".number_format($t_budget_amount,2)."</td>
						<td style='text-align:right;'>".number_format($t_quantity,2)."</td>
						<td style='text-align:right;'>".number_format($t_amount,2)."</td>
					</tr>
				";
			endif;
            ?>
            
        
        </table>
    </div>
    
    <div class="content">
    	<table cellspacing="0">
        	<caption style="text-align:left; font-weight:bold;">EQUIPMENT RENTAL</caption>
            <tr>
            	<th style="text-align:left;">DESCRIPTION</th>
            	<th style="width:10%; text-align:right;">BUDGET QTY</th>
            	<th style="width:10%; text-align:right;">BUDGET AMOUNT</th>
                <th style="width:10%; text-align:right;">TOTAL ACTUAL HRS</th>
                <th style="width:10%; text-align:right;">TOTAL CHARGED HRS</th>
                <th style="width:10%; text-align:right;">RATE/HR</th>
                <th style="width:10%; text-align:right;">AMOUNT</th>

                <th style="width:10%; text-align:right;">BUDGET BALANCE QTY</th>
                <th style="width:10%; text-align:right;">BUDGET BALANCE AMOUNT</th>
                
            </tr>
            <?php
			$aEUR = getEUR($project_id);
			if($aEUR):
				$t_actual = $t_charged = $t_amount = 0;
				$t_budget_quantity = $t_budget_amount = 0;

				$t_budget_balance_quantity  = $t_budget_balance_amount = 0;
				foreach( $aEUR as $r ):
					$t_actual                  += $r['total_actual_hrs'];
					$t_charged                 += $r['total_charged_hrs'];
					$t_amount                  += $r['amount'];
					
					$budget_balance_quantity   += $r['budget_quantity'] - $r['total_charged_hrs'];
					$budget_balance_amount     += $r['budget_amount'] - $r['amount'];
					
					$t_budget_balance_quantity += $budget_balance_quantity;
					$t_budget_balance_amount   += $budget_balance_amount;


					echo "
						<tr>
							<td>$r[stock]</td>
							<td style='text-align:right;'>".number_format($r['budget_quantity'],2)."</td>
							<td style='text-align:right;'>".number_format($r['budget_amount'],2)."</td>
							<td style='text-align:right;'>".number_format($r['total_actual_hrs'],2)."</td>
							<td style='text-align:right;'>".number_format($r['total_charged_hrs'],2)."</td>
							<td style='text-align:right;'>".number_format($r['rate_per_hour'],2)."</td>
							<td style='text-align:right;'>".number_format($r['amount'],2)."</td>

							<td style='text-align:right;'>".number_format($budget_balance_quantity,2)."</td>
							<td style='text-align:right;'>".number_format($budget_balance_amount,2)."</td>
						</tr>
					";
				endforeach;
				echo "
					<tr>
						<td></td>
						<td style='text-align:right;'>".number_format($t_budget_quantity,2)."</td>
						<td style='text-align:right;'>".number_format($t_budget_amount,2)."</td>
						<td style='text-align:right;'>".number_format($t_actual,2)."</td>
						<td style='text-align:right;'>".number_format($t_charged,2)."</td>
						<td style='text-align:right;'></td>
						<td style='text-align:right;'>".number_format($t_amount,2)."</td>
						
						<td style='text-align:right;'>".number_format($t_budget_balance_quantity,2)."</td>
						<td style='text-align:right;'>".number_format($t_budget_balance_amount,2)."</td>
					</tr>
				";
			endif;
            ?>
            
        
        </table>
    </div>
    
    <div class="content">
    	<table cellspacing="0">
        	<caption style="text-align:left; font-weight:bold;">LABOR</caption>
            <tr>
            	<th style="text-align:left;">DESCRIPTION</th>
                <th style="width:10%; text-align:right;">TOTAL QUANTITY</th>
                <th style="width:10%; text-align:right;">TOTAL AMOUNT</th>                
            </tr>
            <?php
			$aLabor = getLabor($project_id);
			if($aLabor):
				$t_quantity = $t_amount = 0;
				foreach( $aLabor as $r ):
					
					$t_quantity += $r['quantity'];
					$t_amount 	+= $r['amount'];
					echo "
						<tr>
							<td>$r[sub_description]</td>
							<td style='text-align:right;'>".number_format($r['quantity'],2)."</td>
							<td style='text-align:right;'>".number_format($r['amount'],2)."</td>
						</tr>
					";
				endforeach;
				echo "
					<tr>
						<td></td>
						<td style='text-align:right;'>".number_format($t_quantity,2)."</td>
						<td style='text-align:right;'>".number_format($t_amount,2)."</td>
					</tr>
				";
			endif;
            ?>
            
        
        </table>
    </div>
                
   
    
    </div>
</div>
</body>
</html>

