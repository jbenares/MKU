<?php
require_once('../my_Classes/options.class.php');
include_once("../conf/ucs.conf.php");

#echo "<B>UNDER CONSTRUCTION</B>";

$options=new options();	

$project_id           = $_REQUEST['id'];
$work_category_id     = $_REQUEST['work_category_id'];
$sub_work_category_id = $_REQUEST['sub_work_category_id'];

function getRTPQty($stock_id,$project_id,$work_category_id,$sub_work_category_id){
	$sql = "
		select
			sum(request_quantity) as rtp_qty,sum(warehouse_quantity) as w_qty
		from
			pr_header as h, pr_detail as d
		where
			h.pr_header_id = d.pr_header_id
		and status != 'C'
		and project_id = '$project_id'
		and h.work_category_id = '$work_category_id'
		and h.sub_work_category_id = '$sub_work_category_id'
		and stock_id = '$stock_id'
		and allowed = '1'
	";
	$result = mysql_query($sql) or die(mysql_error());
	$r = mysql_fetch_assoc( $result );		
	$a  = array();
	$a['rtp_qty'] = $r['rtp_qty'];
	$a['w_qty'] = $r['w_qty'];
	
	return $a;
}

function getPOQty($stock_id,$project_id,$work_category_id,$sub_work_category_id){
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
		and h.work_category_id = '$work_category_id'
		and	h.sub_work_category_id = '$sub_work_category_id'
		and po_type = 'M'
		and ( d.stock_id = '$stock_id' or p.parent_stock_id = '$stock_id' )
	";

	$result = mysql_query($sql) or die(mysql_error());
	$a = array();
	$r = mysql_fetch_assoc( $result );
	$a['po_qty'] = $r['po_qty'];
	
	return $a;
}

function getMRRQtyAmount($stock_id,$project_id,$work_category_id,$sub_work_category_id){
	$sql = "
		select
			sum(quantity) as mrr_qty, sum(amount) as mrr_amount
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

	$result = mysql_query($sql) or die(mysql_error());
	$r = mysql_fetch_assoc( $result );		

	return $r;
}
function getRISQtyAmount($stock_id,$project_id,$work_category_id,$sub_work_category_id){
	$sql = "
		select
			sum(quantity) as ris_qty, sum(amount) as ris_amount
		from
			issuance_header as h, issuance_detail as d, productmaster as p
		where
			h.issuance_header_id = d.issuance_header_id
		and d.stock_id = p.stock_id
		and h.status != 'C'
		and project_id = '$project_id'
		and h.work_category_id = '$work_category_id'
		and h.sub_work_category_id = '$sub_work_category_id'
		and ( d.stock_id = '$stock_id' or p.parent_stock_id = '$stock_id' )
	";
	$result = mysql_query($sql) or die(mysql_error());
	$r = mysql_fetch_assoc( $result );
	

	return $r;
}




function getWorkCategories($project_id,$work_category_id,$sub_work_category_id){

	$sql = "
		select
			h.work_category_id, h.sub_work_category_id, if(isnull(w1.work),'No Work Category',w1.work) as work_category, if(isnull(w2.work),'No Sub Work Category',w2.work) as sub_work_category
		from
			budget_header as h left join work_category as w1 on h.work_category_id = w1.work_category_id
		left join work_category as w2 on h.sub_work_category_id = w2.work_category_id
		where 
			project_id = '$project_id'
		and h.status != 'C'
	";

	if($work_category_id) $sql .= " and h.work_category_id = '$work_category_id'";
	if($sub_work_category_id) $sql .= " and h.sub_work_category_id = '$sub_work_category_id'";

	$sql .= "
		group by h.work_category_id, h.sub_work_category_id
		order by h.work_category_id asc, h.sub_work_category_id asc
	";
	$result = mysql_query($sql) or die(mysql_error());
	$a = array();
	while( $r = mysql_fetch_assoc( $result ) ){
		$a[] = $r;
	}

	return $a;
}

function getBudgetFromWorkCategory($project_id,$work_category_id,$sub_work_category_id,$budget_category){
	#M, L, E, F
	$sql = "
		select
			d.stock_id,p.stock,p.unit,d.quantity,d.cost,d.amount
		from
			budget_header as h inner join budget_detail as d on h.budget_header_id = d.budget_header_id
		inner join productmaster as p on d.stock_id = p.stock_id
		where
			h.status != 'C'
		and h.project_id = '$project_id'
		and h.work_category_id = '$work_category_id'
		and h.sub_work_category_id = '$sub_work_category_id'
		and p.budget_category = '$budget_category'
		order by stock asc
	";

	$result = mysql_query($sql) or die(mysql_error());
	$a = array();
	while( $r = mysql_fetch_assoc( $result ) ){

		$aRTP                    = getRTPQty($r['stock_id'],$project_id,$work_category_id,$sub_work_category_id);
		$r['rtp_qty']            = $aRTP['rtp_qty'];
		$r['w_qty']              = $aRTP['w_qty'];
		
		$aPO                     = getPOQty($r['stock_id'],$project_id,$work_category_id,$sub_work_category_id);
		$r['po_qty']             = $aPO['po_qty'];
		
		$aMRR                    = getMRRQtyAmount($r['stock_id'],$project_id,$work_category_id,$sub_work_category_id);
		$r['mrr_qty']            = $aMRR['mrr_qty'];
		$r['mrr_amount']         = $aMRR['mrr_amount'];
		
		$aRIS                    = getRISQtyAmount($r['stock_id'],$project_id,$work_category_id,$sub_work_category_id);
		$r['ris_qty']            = $aRIS['ris_qty'];
		$r['ris_amount']         = $aRIS['ris_amount'];
		
		$r['mrr_balance_qty']    = $r['quantity'] - $r['mrr_qty'];
		$r['mrr_balance_amount'] = $r['amount'] - $r['mrr_amount'];
		
		$r['ris_balance_qty']    = $r['quantity'] -  $r['ris_qty'];
		$r['ris_balance_amount'] = $r['amount']  - $r['ris_amount'];

		$a[] = $r;	
		set_time_limit(20);
	}

	return $a;
}

function getBudget($project_id , $work_category_id, $sub_work_category_id){


	$aWC = getWorkCategories($project_id,$work_category_id,$sub_work_category_id); #get work categories
	$a = array();
	
	if($aWC){
		foreach($aWC as $wc){
			#get material, labor, equipment, fuel budget	
			#material
			$t = array();
			$t['work_categories']  = $wc;
			$t['budget_materials'] = getBudgetFromWorkCategory($project_id,$wc['work_category_id'],$wc['sub_work_category_id'],'M');
			$t['budget_labor']     = getBudgetFromWorkCategory($project_id,$wc['work_category_id'],$wc['sub_work_category_id'],'L');
			$t['budget_equipment'] = getBudgetFromWorkCategory($project_id,$wc['work_category_id'],$wc['sub_work_category_id'],'E');
			$t['budget_fuel']      = getBudgetFromWorkCategory($project_id,$wc['work_category_id'],$wc['sub_work_category_id'],'F');

			$a[] = $t;
		}
	}
	return $a;	
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
	padding:0px;
	font-family:Arial, Helvetica, sans-serif;
	font-size:12px;
}
table{ border-collapse: collapse; }
.table td:nth-child(1){
}
.table td:nth-child(n+2){
	width:10%;	
	text-align: right;
}

</style>
</head>
<body>
<div class="container">
	<?php
	require("../transactions/form_heading.php");
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
	
    <div>
	    <?php
	    $a = getBudget($project_id , $work_category_id, $sub_work_category_id);

	    /*echo "<pre>";
	    print_r($a);
	    echo "</pre>";*/

		$aBudgetCategory = array('budget_materials' => 'MATERIALS', 'budget_labor' => 'LABOR', 'budget_equipment' => 'EQUIPMENT' , 'budget_fuel' => 'FUEL');
		$aTotalKey       = array('quantity','cost','amount','rtp_qty','po_qty','mrr_qty','mrr_amount','w_qty','ris_qty','ris_amount','mrr_balance_qty','mrr_balance_amount',
		'ris_balance_qty','ris_balance_amount');
		$aGrandTotal     = array();

	    echo "<table class='table'>";
	    if($a){
	    	foreach($a as $r){

	    		$i = 0;
	    		foreach( $aBudgetCategory as $key => $value ){
	    			$i += count($r[$key]);
	    		}

	    		if($i > 0){

		    		echo "
	    				<tr>
	    					<td style='font-weight:bold;' colspan='3' >Work Category :
	    					".$r['work_categories']['work_category']."</td>

	    					
	    				</tr>
	    				<tr>
	    					<td style='font-weight:bold;' colspan='3'>Sub Work Category :
	    					".$r['work_categories']['sub_work_category']."</td>

		    			</tr>	 
		    			<tr><td>&nbsp</td></tr>   		
		    		";

		    		foreach ($aBudgetCategory as $key => $value) {
		    			if($r[$key]){
							#if(!isset($_REQUEST[subtotal])){
								echo "<tr><td style='padding-left:5px; font-weight:bold;' colspan='2'>$value</td></tr>";
							#}
			    			    			
			    			echo "
			    				<tr>";
								if($_REQUEST[subtotal] == ""){
									echo "<td style='font-weight:bold; padding-left:10px;'>ITEM</td>";
								}else{
									echo "<td>&nbsp;</td>";	
								}
			    					
			    			echo "<td style='font-weight:bold;'>BUDGET QTY</td>
			    					<td style='font-weight:bold;'>COST</td>
			    					<td style='font-weight:bold;'>BUDGET AMOUNT</td>

			    					<td style='font-weight:bold;'>RTP QTY</td>
			    					<td style='font-weight:bold;'>PO QTY</td>

			    					<td style='font-weight:bold;'>MRR QTY</td>
			    					<td style='font-weight:bold;'>MRR AMOUNT</td>
									<td style='font-weight:bold;'>MCD</td>
									
			    					<td style='font-weight:bold;'>RIS QTY</td>
			    					<td style='font-weight:bold;'>RIS AMOUNT</td>

			    					<td style='font-weight:bold;'>MRR BALANCE QTY</td>
			    					<td style='font-weight:bold;'>MRR BALANCE AMOUNT</td>
			    					
			    					<td style='font-weight:bold;'>RIS BALANCE QTY</td>
			    					<td style='font-weight:bold;'>RIS BALANCE AMOUNT</td>			    					
			    				</tr>
			    			";

			    			$aTotal = array();
							$co="";
			    			foreach($r[$key] as $p){
			    				if($p['w_qty'] > 0){
									$co="c/o";
								}else{
									$co="";	
								}
								if($_REQUEST[subtotal] == ""){
									echo "
			    					<tr>
			    						<td style='padding-left:10px;'>".htmlentities($p['stock'])."</td>
			    						<td>".number_format($p['quantity'],2)."</td>			    					
			    						<td>".number_format($p['cost'],2)."</td>
			    						<td>".number_format($p['amount'],2)."</td>

			    						<td>".number_format($p['rtp_qty'],2)."</td>
			    						<td>".number_format($p['po_qty'],2)."</td>

			    						<td>".number_format($p['mrr_qty'],2)."</td>
			    						<td>".number_format($p['mrr_amount'],2)."</td>
										<td>".$co."</td>

			    						<td>".number_format($p['ris_qty'],2)."</td>
			    						<td>".number_format($p['ris_amount'],2)."</td>

			    						<td>".number_format($p['mrr_balance_qty'],2)."</td>
			    						<td>".number_format($p['mrr_balance_amount'],2)."</td>

			    						<td>".number_format($p['ris_balance_qty'],2)."</td>
			    						<td>".number_format($p['ris_balance_amount'],2)."</td>

			    					</tr>
			    					";
								}
								

			    				foreach( $aTotalKey as $k ){
			    					$aTotal[$k] += $p[$k];
			    					$aGrandTotal[$k] += $p[$k];
			    				}
			    			}

			    			echo "
			    				<tr>
			    					<td style='border-top:1px solid #000;'></td>
			    			";

			    			foreach( $aTotalKey as $k ){
								//echo $k;
			    				if ($k == "cost" || $k=="w_qty") {
									echo "<td style='border-top:1px solid #000;'></td>";
			    				} else {
			    					echo "<td style='border-top:1px solid #000; font-weight:bold;'>".number_format($aTotal[$k],2)."</td>";	
			    				}
			    				
			    			}

			    			echo "</tr>";
			    			echo "<tr><td>&nbsp</td></tr>";	    			
			    		}
			    		
		    		}
	    		}

	    	}
	    }
	    echo "</table>";
	    ?>
    
    </div>
</div>
</body>
</html>

