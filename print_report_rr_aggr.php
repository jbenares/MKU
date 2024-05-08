<?php
require_once('my_Classes/options.class.php');
include_once("conf/ucs.conf.php");

$options=new options();	

$rr_header_id=$_REQUEST[id];

function getRRDetails($rr_header_id){
	$query="
			select
				quantity,
				d.stock_id,
				p.stock,
				p.stockcode,
				unit,
				d.cost,
				d.amount,
				invoice,
				driverID,
				quantity_cum,
				equipment_id,
				_unit
			from
				rr_detail as d, productmaster as p
			where
				d.stock_id = p.stock_id
			and
				rr_header_id='$rr_header_id'
		";
	$result = mysql_query($query) or die(mysql_error());
	$a = array();
	while($r = mysql_fetch_assoc($result)){
		$tmp = array();
		$tmp['quantity'] 		= $r['quantity'];
        $tmp['stock_id']		= $r['stock_id'];
        $tmp['stock'] 			= $r['stock'];
        $tmp['stockcode']		= $r['stockcode'];
        $tmp['unit']			= $r['unit'];
        $tmp['cost']			= $r['cost'];
        $tmp['amount']			= $r['amount'];
        $tmp['invoice']			= $r['invoice'];
		$tmp['driverID'] 		= $r['driverID'];
		$tmp['equipment_id']	= $r['equipment_id'];
		$tmp['quantity_cum'] 	= $r['quantity_cum'];
		$tmp['_unit']			= $r['_unit'];
		$a[] = $tmp;
	}
	return $a;	
}

$rr = getRRDetails($rr_header_id);

$query="
	select
		  *
	 from
		  rr_header as h,
		  projects as p
	 where
		h.project_id = p.project_id
	and
		rr_header_id = '$rr_header_id'
";
$result=mysql_query($query);
$r=mysql_fetch_assoc($result);
$user_id		= $r['user_id'];
$project_id		= $r['project_id'];
$project_name	= $r['project_name'];
$date			= $r['date'];
$po_header_id	= $r['po_header_id'];
$po_header_id_pad	= str_pad($po_header_id,7,0,STR_PAD_LEFT);
$rr_header_id_pad	= str_pad($rr_header_id,7,0,STR_PAD_LEFT);
$supplier_id 	= $r['supplier_id'];
$supplier		= $options->attr_Supplier($supplier_id,'account');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>JOB ORDER</title>
<script>
function printPage() { print(); } //Must be present for Iframe printing
</script>
<link rel="stylesheet" type="text/css" href="css/print_report_rr.css"/>
<style type="text/css">
*{ font-family: "Times New Roman"; }

@media screen {
    div.divFooter { display: none;}
}
@media print {
	.kurit td{
		border-top:1px solid #000;
		border-bottom:1px solid #000;	
	}

	*{
		font-family: "Times New Roman";
	}
    div.divFooter {
        position: fixed;
        bottom: 0;

        font-family: "Times New Roman";
        font-size: 11px;
    }
}
</style>
</head>
<body>
<div class="container">
	<?php 
	$c = 0;
	$page_beak_content = "<div style='page-break-after:always;'></div>";
	$exit = 0;
	$page_break = 0;
	$items = 7;
	$totalamount=0;
	$total_quantity = 0;
	$total_items = 0;
	while(1){ 
		if($page_break) echo $page_beak_content;
		$page_break = 0;
	?>
	<?php
        require("form_heading_ieee.php");
    ?>
   	
    <div style="text-align:right; font-weight:bolder;">
        M.R.R #. : <?=str_pad($rr_header_id,7,0,STR_PAD_LEFT)?><br />
    </div>   
    <div style="text-align:center; font-size:14px;">
        Material Receiving Report
    </div>
    <div class="header" style="margin-bottom:10px;">
        <table style="width:100%;">
            <tr>
                <td width="19%">Supplier:</td>
                <td width="47%" style="border-bottom:1px solid #000;"><?=$supplier?></td>
                
                <td width="7%">Date:</td>
                <td width="27%" style="border-bottom:1px solid #000;"><?=date("F j, Y",strtotime($date))?></td>
            </tr>
            <tr>
              <td>Project / Section:</td>
              <td style="border-bottom:1px solid #000;"><?=$project_name?></td>
              
              <td>PO #:</td>
              <td style="border-bottom:1px solid #000;"><?=$po_header_id_pad?></td>
            </tr>
           
        </table>
    </div><!--End of header-->
    <table cellspacing="0" cellpadding="3" class="content">
        <tr>
            <th style="width:10%;">Driver</th>
            <th style="width:10%;">EQ</th>
            <th style="width:5%;">Qty</th>
            <th style="width:5%;">Unit</th>
            <th style="width:5%;">(Qty)</th>
            <th style="width:5%;">(Unit)</th>
            <th>Item Description </th>
            <th style="width:10%;">Inv./DR #</th>
            <th style="width:10%;">U.Price</th>
            <th style="width:10%;">Amount</th>
            
        </tr>
       	<?php if(empty($rr)){ echo "</table>"; break; } ?>
        <?php
        $i=0;
        for( ; $c < count($rr) ; $c++ ){
            $i++;
			$r = $rr[$c];
            $quantity	= $r['quantity'];
            $unit		= $r['unit'];
            $stock		= $r['stock'];
            $cost		= $r['cost'];
            $invoice	= $r['invoice'];
            $amount		= $r['amount'];
            $driverID	= $r['driverID'];
            $quantity_cum = $r['quantity_cum'];
			$unit		= $r['_unit'];
            $_unit		= $r['_unit'];
            $equipment_id = $r['equipment_id'];			
            
			if($i > $items){
				echo "<tr><td colspan='6' style='border-bottom:1px solid #000;'></td></tr>
						</table>";	
				$page_break = 1;
				break;
			}
			
			if($c == (count($rr) -1 )){
				$exit = 1;	
			}	
			
			$totalamount += $r[amount];		
            $total_qty += $r['quantity'];
            $total_qty_aggr += $r['quantity_cum'];	
        ?>
        <tr>
            <td><?=$options->getAttribute('drivers','driverID',$driverID,'driver_name')?></td>
            <td><?=$options->getAttribute('equipment','eqID',$equipment_id,'eq_name')?>(<?=$options->getAttribute('equipment','eqID',$equipment_id,'eqModel')?>)</td>
            <td><div align="right"><?=number_format($r[quantity],4,'.',',')?></div></td>
            <td><?=$unit?></td>
            <td><div align="right"><?=number_format($r[quantity_cum],4,'.',',')?></div></td>
            <td><?=$_unit?></td>
            <td><?=$stock?></td>
            <td><?=$invoice?></td>
            <td nowrap="nowrap" style="text-align:right;">P <?=number_format($cost,2,'.',',')?></td>
            <td nowrap="nowrap" style="text-align:right;">P <?=number_format($amount,2,'.',',')?></td>
        </tr>
        <?php
		}
		if(!$exit){
			continue;				
		}
        ?>   
        <?php
        echo '<tr>';
        /*echo '<td>&nbsp;</td>';
        echo '<td>&nbsp;</td>';
        echo '<td>&nbsp;</td>';
        echo '<td>&nbsp;</td>';
        echo '<td>&nbsp;</td>';*/
        echo '<td colspan="10" style="text-align:center; font-weight:bold;">********** Nothing Follows **********</td>';
        /*echo '<td>&nbsp;</td>';	
        echo '<td>&nbsp;</td>';	
        echo '<td>&nbsp;</td>';	*/
        echo '</tr>';
        ?>
        <?php
		for($newi=$i;$newi<=$items;$newi++) {
			echo '<tr>';
			echo '<td>&nbsp;</td>';
			echo '<td>&nbsp;</td>';
			echo '<td>&nbsp;</td>';
			echo '<td>&nbsp;</td>';
			echo '<td>&nbsp;</td>';
			echo '<td>&nbsp;</td>';
			echo '<td>&nbsp;</td>';
			echo '<td>&nbsp;</td>';	
			echo '<td>&nbsp;</td>';	
			echo '<td>&nbsp;</td>';	
			echo '</tr>';
		}
		?>
       	<tr class="kurit">
            <td style="width:10%;">&nbsp;</td>
            <td style="width:10%;">&nbsp;</td>
            <td style="width:5%; text-align:right; font-weight:bold;"><?=number_format($total_qty,4,'.',',')?></td>
            <td style="width:5%;">&nbsp;</td>
            <td style="width:5%; text-align:right; font-weight:bold;"><?=number_format($total_qty_aggr,4,'.',',')?></td>
            <td style="width:5%;"></td>
            <td></td>
            <td style="width:10%;"></td>
            <td nowrap="nowrap" style="text-align:right; width:10%;">Total</td>
            <td nowrap="nowrap" style="text-align:right; font-weight:bold; width:10%;">P&nbsp;<?=number_format($totalamount,2,'.',',')?></td>
        </tr>
    </table>
    <?php  break; }  ?>
   
    
    <table cellspacing="0" cellpadding="5" align="center" width="98%" style="border:none; text-align:center; margin-top:10px;" class="summary">
        <tr>
            <td>Received & Checked by:<p>
                <input type="text" class="line_bottom" /><br>Warehouseman</p></td>
            <td>Noted by:<p>
                <input type="text" class="line_bottom" /><br>P.I.C / MCD Head / Finance</p></td>
            <td>Encoded by:<p>
                <input type="text" class="line_bottom" /><br><?=$options->getUserName($user_id);?></p></td>
        </tr>
    </table>
</div>
<div class="divFooter">
    F-WHS-001<br>
    Rev. 0 10/07/13
</div>
</body>
</html>