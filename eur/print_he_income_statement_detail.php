<?php
require_once('../my_Classes/options.class.php');
include_once("../conf/ucs.conf.php");

$options=new options();	

$from_date		= $_REQUEST['from_date'];
$to_date		= $_REQUEST['to_date'];

function numform($num) {
	if($num==0) $num = "&nbsp;";
	else if($num < 0 ) $num = "( ".number_format(abs($num),2)." )";
	else $num = number_format($num, 2);
	
	return $num;
}

function getIncomeStatement($from_date,$to_date){
	$aIncome = array();
	/*$result = mysql_query("
		select 
			sum(h.rate_per_hour * computed_time) as income
		from
			eur_header as h, eur_detail as d
		where
			h.eur_header_id = d.eur_header_id
		and h.status != 'C'
		and d.released_date between '$from_date' and '$to_date'
	") or die(mysql_error());*/
	
	$result = mysql_query("
		select 
			p.stock , sum(h.rate_per_hour * computed_time) as income
		from
			eur_header as h, eur_detail as d, productmaster as p, equipment_categories as eq
		where
			h.eur_header_id = d.eur_header_id
		and h.stock_id = p.stock_id
		and p.eq_catID = eq.eq_catID
		and h.`status` != 'C'
		and d.released_date between '$from_date' and '$to_date'
		group by h.stock_id
		order by p.stock asc
	") or die(mysql_error());
	$t = array();
	while($r = mysql_fetch_assoc($result)){
		$t['equipment'] = $r['stock'];
		$t['amount'] = $r['income'];
		$aIncome['income']['details'][] = $t;
	}
	
	
	
	#8 - HE
	#solve for repair and maintenance
	/*$result = mysql_query("
		select 
			sum(amount) as amount
		from
			issuance_header as h, issuance_detail as d
		where
		 	h.issuance_header_id = d.issuance_header_id
		and status != 'C'
		and h.project_id = '8'
		and date between '$from_date' and '$to_date'
		and d.stock_id != '669'
	") or die(mysql_error());
	$r = mysql_fetch_assoc($result);
	$repair_amount = $r['amount'];*/
	
	$result = mysql_query("
		select 
			sum(amount) as amount, eq_name
		from
			issuance_header as h, issuance_detail as d, equipment as p, equipment_categories as eq
		where
			h.issuance_header_id = d.issuance_header_id
		and d.equipment_id = p.eqID
		and p.eq_catID = eq.eq_catID
		and h.status != 'C'
		and h.project_id = '8'
		and date between '$from_date' and '$to_date'
		and d.stock_id != '669'
		group by p.eqID
		order by eq_name asc
	") or die(mysql_error());
	$t = array();
	while($r = mysql_fetch_assoc($result)){
		$t['equipment'] = $r['eq_name'];
		$t['amount'] = $r['amount'];
		$aIncome['repair']['details'][] = $t;
	}
	
	
	#solve for diesel - 669 stock_id
	/*$result = mysql_query("
		select 
			sum(amount) as amount
		from
			issuance_header as h, issuance_detail as d
		where
		 	h.issuance_header_id = d.issuance_header_id
		and status != 'C'
		and h.project_id = '8'
		and date between '$from_date' and '$to_date'
		and d.stock_id = '669'
	") or die(mysql_error());
	$r = mysql_fetch_assoc($result);
	$diesel_amount = $r['amount'];*/
	
	$result = mysql_query("
		select 
			sum(amount) as amount, eq_name
		from
			issuance_header as h, issuance_detail as d, equipment as p, equipment_categories as eq
		where
			h.issuance_header_id = d.issuance_header_id
		and d.equipment_id = p.eqID
		and p.eq_catID = eq.eq_catID
		and h.status != 'C'
		and h.project_id = '8'
		and date between '$from_date' and '$to_date'
		and d.stock_id = '669'
		group by p.eqID
		order by eq_name asc
	") or die(mysql_error());
	$t = array();
	while($r = mysql_fetch_assoc($result)){
		$t['equipment'] = $r['eq_name'];
		$t['amount'] = $r['amount'];
		$aIncome['fuel']['details'][] = $t;
	}
	
	
	#FUEL-DIESEL - 15
	
	$result = mysql_query("
		select * from gchart where gchart_id = '15'
	") or die(mysql_error());
	$aIncome['fuel']['account'] = mysql_fetch_assoc($result);
	
	#HEAVY EQUIPMENT RENTAL - 3
	$result = mysql_query("
		select * from gchart where gchart_id = '3'
	") or die(mysql_error());
	$aIncome['income']['account'] = mysql_fetch_assoc($result);
	
	#REPAIR AND MAINTENANCE - 40
	$result = mysql_query("
		select * from gchart where gchart_id = '40'
	") or die(mysql_error());
	$aIncome['repair']['account'] = mysql_fetch_assoc($result);
	
	
	return $aIncome;
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
*{
	font-size:11px;	
	font-family:Arial, Helvetica, sans-serif;
}
table{ border-collapse:collapse; }
table tr td:nth-child(2){ text-align:right; }
td{ vertical-align:top; padding:3px; }	
.subtotal td{ border-top:1px solid #000; font-weight:bold;}

.grandtotal td{ border-bottom:3px double #000; font-weight:bold;}

</style>
</head>
<body>
<div class="container">
	
     <div><!--Start of Form-->
     	<div style="font-weight:bolder;">
        	<?=$title?><br />
            <?=$company_address?> <br />
            INCOME STATEMENT <br />
			<?php echo date("m/d/Y",strtotime($from_date))." - ".date("m/d/Y",strtotime($to_date)); ?>
            <br />
			<br />
        </div>          
        <?php
		$aInc = getIncomeStatement($from_date,$to_date);
		#echo "<pre>";
		#print_r($aInc);
		#echo "</pre>";
        ?>
        <table>
        	<tr>
            	<td style="font-weight:bold;">REVENUE</td>
                <td></td>
            </tr>
            <tr>
            	<td style="font-style:italic;"><?=$aInc['income']['account']['gchart']?></td>
                <td></td>
            </tr>
            <?php
			$t_revenue = 0;
			foreach($aInc['income']['details'] as $r){
				$t_revenue += $r['amount'];
				echo "
					<tr>
						<td>$r[equipment]</td>
						<td>".numform($r['amount'])."</td>
					</tr>
				";	
			}
            ?>
            <tr class="subtotal">
            	<td>TOTAL REVENUE</td>
                <td><?=numform($t_revenue)?></td>
            </tr>
            <tr>
            	<td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
            	<td style="font-weight:bold;">LESS: EXPENSES</td>
                <td></td>
            </tr>
            <tr>
            	<td style="font-style:italic;"><?=$aInc['fuel']['account']['gchart']?></td>
                <td></td>
            </tr>
            <?php
			$t_fuel = 0;
			foreach($aInc['fuel']['details'] as $r){
				$t_fuel += $r['amount'];
				echo "
					<tr>
						<td>$r[equipment]</td>
						<td>".numform($r['amount'])."</td>
					</tr>
				";	
			}
            ?>
            <!--<tr class="subtotal">
            	<td></td>
                <td><?=numform($t_fuel)?></td>
            </tr> -->
            <tr>
            	<td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
            	<td style="font-style:italic;"><?=$aInc['repair']['account']['gchart']?></td>
                <td></td>
            </tr>
            <?php
			$t_repair = 0;
			foreach($aInc['repair']['details'] as $r){
				$t_repair += $r['amount'];
				echo "
					<tr>
						<td>$r[equipment]</td>
						<td>".numform($r['amount'])."</td>
					</tr>
				";	
			}
            ?>
            <tr class="subtotal">
            	<td>TOTAL EXPENSES</td>
                <td><?=numform($t_repair + $t_fuel)?></td>
            </tr>
            <tr>
            	<td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr class="grandtotal">
            	<td>TOTAL NET INCOME/(LOSSES)</td>
                <td><?=numform($t_revenue - $t_fuel - $t_repair)?></td>
            </tr>
        </table>
         
        <div class="content" style="">
        </div><!--End of content-->
    </div><!--End of Form-->
</div>
</body>
</html>