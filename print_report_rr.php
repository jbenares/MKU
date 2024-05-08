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
					account_id,
                    d.discount
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
            $tmp['discount']         = $r['discount'];
			$tmp['account_id']		= $r['account_id'];
			$a[] = $tmp;
		}
		return $a;
	}
	
	//evalutation
	$count_eva = 0;
	$sql_eva = mysql_query("Select * from rr_evaluation where rr_header_id = '$rr_header_id'") or die (mysql_error());
	$count_eva = mysql_num_rows($sql_eva);
	
	if($count_eva == 1){
		$r_eva = mysql_fetch_assoc($sql_eva);
		
		$eva_ps = $r_eva['eva_ps'];
		$eva_d = $r_eva['eva_d'];
		$eva_cr = $r_eva['eva_cr'];
		$eva_sf = $r_eva['eva_sf'];
		$eva_p = $r_eva['eva_p'];
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
	$discount_amount = $r['discount_amount'];
	$advance_payment_amount = $r['advance_payment_amount'];

	$terms = $options->getAttribute('po_header','po_header_id',$po_header_id,'terms');

	$due_date = date("F j, Y",strtotime("+$terms days",strtotime($date)));
	if(strtotime($date) > strtotime($due_date)){
		$due_date = "";
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
<link rel="stylesheet" type="text/css" href="css/print_report_rr.css"/>
<style type="text/css">
	*{
		font-family: "Times New Roman";
	}
	@media screen {
	    div.divFooter {
	        display: none;
	    }
	}
	@media print {
	    div.divFooter {
	        position: fixed;
	        bottom: 0;

	        font-family: "Times New Roman";
	        font-size: 11px;
	    }
	}
	.square {
		width: 13px;
		height: 13px;
		background: #FFF;
		border:1px solid #000;
		display:inline-block;
	}
	.eva_td{
		text-align: center;
	}
</style>
</head>
<body>
<div class="container">


    <?php
	$c = 0;
	//$page_beak_content = "<div style='page-break-after:always;'></div>";
	$exit = 0;
	//$page_break = 0;
	$items = 10;
	$totalamount=0;
	$total_quantity = 0;
	$total_items = 0;
	//while(1){
	//	if($page_break) echo $page_beak_content;
	//	$page_break = 0;
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

            <tr>
            	<td>Terms</td>
                <td style="border-bottom:1px solid #000;"><?=$terms?></td>

                <td>Due Date</td>
                <td style="border-bottom:1px solid #000;"><?=$due_date?></td>
            </tr>

        </table>
    </div><!--End of header-->
    <table cellspacing="3" class="content">
        <tr>
            <th  rowspan='2' style="width:10%;">Qty</th>
            <th  rowspan='2' style="width:10%;">Unit</th>
            <th rowspan='2' >Item Description </th>
            <th  rowspan='2' style="width:10%;">Inv./DR #</th>
            <th  rowspan='2' style="width:10%;">U.Price</th>
            <th  rowspan='2' style="width:10%;">Discount</th>
            <th  rowspan='2' style="width:10%;">Amount</th>
            <th colspan='5' style='text-align:center;'>*Issues in Delivery</th>
        </tr>
        <tr>
        	<th style="width:3% text-align:center;">P/S</th>
        	<th style="width:3% text-align:center;">D</th>
        	<th style="width:3% text-align:center;">C</th>
        	<th style="width:3% text-align:center;">SF</th>
        	<th style="width:3% text-align:center;">P</th>
        </tr>
        <?php //if(empty($rr)){ echo "</table>"; break; } ?>
        <?php
        $i=0;
        for( ; $c < count($rr) ; $c++ ){
            $i++;
			$r = $rr[$c];
            $quantity	= $r['quantity'];
            $unit		= $r['unit'];
            $stock		= $r['stock'];
            $cost		= $r['cost'];
            $discount   = $r['discount'];
            $invoice	= $r['invoice'];
            $amount		= $r['amount'];


			/*if($i > $items){
				echo "<tr><td colspan='6' style='border-bottom:1px solid #000;'></td></tr>
						</table>";
				$page_break = 1;
				break;
			}

			if($c == (count($rr) -1 )){
				$exit = 1;
			}*/

		   	  $totalamount += $r['amount'];
              $discount_amount +=$discount * $quantity;
			  

        ?>
            <tr>
                <td><div align="right"><?=$r[quantity]?></div></td>
                <td><?=$unit?></td>
                <td><?=$stock?> <?=(!empty($r['account_id'])) ? "(".$options->getAttribute('account','account_id',$r['account_id'],'account').")" : ""?></td>
                <td><?=$invoice?></td>
                <td><div align="right"><?=number_format($cost,2,'.',',')?></div></td>
                <td><div align="right"><?=number_format($discount * $quantity,2,'.',',')?></div></td>
                <td><div align="right"><?=number_format($amount,2,'.',',')?></div></td>
				<?php if($count_eva == 1){ ?>
                <td class="eva_td"><?=$eva_ps?></td>
                <td class="eva_td"><?=$eva_d?></td>
                <td class="eva_td"><?=$eva_cr?></td>
                <td class="eva_td"><?=$eva_sf?></td>
                <td class="eva_td"><?=$eva_p?></td>				
				<?php }else{ ?>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
				<?php } ?>
            </tr>
        <?php
		}

		function getVat($po_header_id){
			$sql = mysql_query("Select vat from po_header where po_header_id = '$po_header_id'") or die (mysql_error());
			$r = mysql_fetch_assoc($sql);
			$vat_precentage = $r['vat'];
			
			return $vat_precentage;
		}
		
		function getWtax($po_header_id){
			$sql = mysql_query("Select wtax from po_header where po_header_id = '$po_header_id'") or die (mysql_error());
			$r = mysql_fetch_assoc($sql);
			$wtax_percentage = $r['wtax'];
			
			return $wtax_percentage;
		}
		
		//formulas
		$allow_vat = getVat($po_header_id,$vat_precentage);
		if($allow_vat == 0){
			$vatable = $totalamount;
		}else{
			$vatable = $totalamount / 1.12;
		}
		$vat = $vatable * (getVat($po_header_id,$vat_precentage)/100);
		$wtax = $vatable * (getWtax($po_header_id,$wtax_percentage)/100);
		$net_amount = $totalamount - $wtax;
		$balance = $net_amount - $advance_payment_amount;
		
		//if(!$exit){
		//	continue;
		//}
		?>

        <?php
        echo '<tr>';
        echo '<td>&nbsp;</td>';
        echo '<td>&nbsp;</td>';
        echo '<td>********** Nothing Follows **********</td>';
        echo '<td>&nbsp;</td>';
        echo '<td>&nbsp;</td>';
        echo '<td>&nbsp;</td>';

        echo '<td>&nbsp;</td>';
        echo '<td>&nbsp;</td>';
        echo '<td>&nbsp;</td>';
        echo '<td>&nbsp;</td>';
        echo '<td>&nbsp;</td>';
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
			echo '<td>&nbsp;</td>';
			echo '</tr>';
		}
        ?>
   	</table>
    <?php // break; }  ?>
    <table class="content">
        <tr>
            <td style="text-align:right; border:1px solid #000; font-weight:bold; ">
                Gross Amount
            </td>
            <td style="width:10%; border:1px solid #000; font-weight:bold; text-align:right;">P<?=number_format($totalamount,2,'.',',')?></td>
        </tr>
        <tr>
            <td style="text-align:right; border:1px solid #000; font-weight:bold;">
                Discount Amount
            </td>
            <td style="width:10%; border:1px solid #000; font-weight:bold; text-align:right;">P<?=number_format($discount_amount,2,'.',',')?></td>
        </tr>
		<!--
        <tr>
            <td style="text-align:right; border:1px solid #000; font-weight:bold;">
                Vatable
            </td>
            <td style="width:10%; border:1px solid #000; font-weight:bold; text-align:right;">P<?=number_format($vatable,2,'.',',')?></td>
        </tr>
        <tr>
            <td style="text-align:right; border:1px solid #000; font-weight:bold;">
                Vat
            </td>
            <td style="width:10%; border:1px solid #000; font-weight:bold; text-align:right;">P<?=number_format($vat,2,'.',',')?></td>
        </tr>
        <tr>
            <td style="text-align:right; border:1px solid #000; font-weight:bold;">
                W/ Tax
            </td>
            <td style="width:10%; border:1px solid #000; font-weight:bold; text-align:right;">P<?=number_format($wtax,2,'.',',')?></td>
        </tr>
        <tr>
            <td style="text-align:right; border:1px solid #000; font-weight:bold;">
                Net Amount
            </td>
            <td style="width:10%; border:1px solid #000; font-weight:bold; text-align:right;">P<?=number_format($net_amount,2,'.',',')?></td>
        </tr>
		<tr>
            <td style="text-align:right; border:1px solid #000; font-weight:bold;">
                Advance Payment
            </td>
            <td style="width:10%; border:1px solid #000; font-weight:bold; text-align:right;">P<?=number_format($advance_payment_amount,2,'.',',')?></td>
        </tr>-->
        <tr>
            <td style="text-align:right; border:1px solid #000; font-weight:bold;">
                Balance
            </td>
            <td style="width:10%; border:1px solid #000; font-weight:bold; text-align:right;">P<?=number_format($balance,2,'.',',')?></td>
        </tr>     
    </table>
    <p style="font-size:10px; font-style:italic;">
    	Legend: P/S - Products/Services, D - Delivery, C - Customer Relations, SF - Supplier Functions, P - Price
    </p>
    <p>
    	ACTION TAKEN IF REJECT/PARTIAL ACCEPTANCE: <div class="square"></div> Return to Supplier __________________  <div class="square"></div> Items Replaced by Suppler __________________ <div class="square"></div> Others __________________
    </p>
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
<!--<div class="divFooter">
    F-WHS-001<br>
    Rev. 0 10/07/13
</div>-->
</body>
</html>
