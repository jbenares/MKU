<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");

	$options=new options();

	$project_id=$_REQUEST[id];


	function deductedFromBudget($stock_id,$project_id,$work_category_id,$sub_work_category_id){
		$result = mysql_query("
			select
				sum(d.quantity) as quantity,
				sum(d.amount) as amount
			from
				rr_header as h, rr_detail as d, budget_deduction as b
			where
				h.rr_header_id = d.rr_header_id
			and
				d.rr_detail_id = b.rr_detail_id
			and
				h.status != 'C'
			and
				h.project_id = '$project_id'
			and
				b.stock_id = '$stock_id'
			and
				b.work_category_id = '$work_category_id'
			and
				b.sub_work_category_id = '$sub_work_category_id'
		") or die(mysql_error());
		$r = mysql_fetch_assoc($result);
		$array = array();
		$array['quantity'] 	= $r['quantity'];
		$array['amount'] 	= $r['amount'];

		return $array;
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

@media print and (width: 8.5in) and (height: 14in) {
	@page {
		margin: 1in;
	}
	.page-break{
		page-break-before:always;
	}
}



body
{
	size: legal portrait;
	padding:0px;
	font-family:Arial, Helvetica, sans-serif;
	font-size:12px;
	letter-spacing:2px;
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
	width:100%;
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
        <table style="width:100%;">
            <tr>
                <td width="14%">Project</td>
                <td width="55%">: <?=$project_name?></td>
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
                  projects as p
             where
                h.project_id = p.project_id
            and
                budget_header_id = '$budget_header_id'
        ";
        $result=mysql_query($query);
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
        	<strong>Material Cost</strong>
        	<table cellspacing="0">
            	<tr>
                	<th>Description</th>
                    <th width="100">Budget Quantity</th>
                    <th width="100">Budget Amount</th>
                    <th width="100">RTP Qty</th>
                    <th width="100">MRR Qty</th>
                    <th width="100">MRR Amount</th>
                    <th width="100">Budget Qty</th>
                    <th width="100">DEDUCTED FROM BUDGET QTY</th>
                    <th width="100">DEDUCTED FROM BUDGET AMOUNT</th>
                </tr>

           		<?php
				$total_amount=0;
				while($r=mysql_fetch_assoc($result)):

					$stock_id		= $r['stock_id'];
					$stock 			= $r['stock'];
					$stockcode		= $r['stockcode'];
					$unit			= $r['unit'];

					$quantity 		= $r['quantity'];
					$cost			= $r['cost'];
					$amount			= $r['amount'];

					$total_amount += $amount;

					$mrr = $options->totalMRR($stock_id,$project_id,$work_category_id,$sub_work_category_id);
					$mrr_quantity = $mrr['quantity'];
					$mrr_amount = $mrr['amount'];

					$pr_quantity = $options->totalPR($stock_id,$project_id,$work_category_id,$sub_work_category_id);

					$budget_qty = $quantity - $mrr_quantity;

					$budget_deduction = deductedFromBudget($stock_id,$project_id,$work_category_id,$sub_work_category_id);
					$qty_deduction 		= $budget_deduction['quantity'];
					$amount_deduction 	= $budget_deduction['amount'];

				?>
                    <tr>
                        <td><?=htmlentities($stock)?></td>
                        <td class="align-right" ><?=number_format($quantity,2,'.',',')?></td>
                        <td class="align-right"><?=number_format($amount,2,'.',',')?></td>
                        <td class="align-right"><?=number_format($pr_quantity,2,'.',',')?></td>
                        <td class="align-right"><?=number_format($mrr_quantity,2,'.',',')?></td>
                        <td class="align-right"><?=number_format($mrr_amount,2,'.',',')?></td>
                        <td class="align-right"><?=number_format($budget_qty,2,'.',',')?></td>
                        <td class="align-right"><?=number_format($qty_deduction,2,'.',',')?></td>
                        <td class="align-right"><?=number_format($amount_deduction,2,'.',',')?></td>
                    </tr>
                <?php
				endwhile;
				?>
            </table>
        <?php
		endif;
        ?>
        </div><!--End of content-->
    <?php
    endforeach;
    ?>

</div>
</body>
</html>
