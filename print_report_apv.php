<?php
	require_once('my_Classes/options.class.php');
	require_once('my_Classes/numbertowords.class.php');
	include_once("conf/ucs.conf.php");
	
	function getLatestMRRDate($apv_header_id){
		$result = mysql_query("
		select 
			h.date					
		from
			apv_detail as d, rr_header as h
		where
			d.rr_id = h.rr_header_id
		and
			h.status != 'C'
		and
			apv_header_id = '$apv_header_id'
		order by date desc
		") or die(mysql_error());
		$r = mysql_fetch_assoc($result);
		return $r['date'];
	}
	
	function getInvoice($rr_header_id,$stock_id,$cost,$quantity){
		$result = mysql_query("select * from rr_detail where rr_header_id = '$rr_header_id' and stock_id = '$stock_id' and cost = '$cost' and quantity = '$quantity'") or die(mysql_error());
		$r = mysql_fetch_assoc($result);
		
		return $r['invoice'];
	}

	$options=new options();	
	$c=new NumToWords();
	
	$apv_header_id=$_REQUEST[id];

	
	$query="
		select
			 *
		 from
			  apv_header
		 where
			apv_header_id = '$apv_header_id'
	";
	$result=mysql_query($query);
	$r=mysql_fetch_assoc($result);
	
	$apv_header_id			= $r['apv_header_id'];
	$apv_header_id_pad		= str_pad($apv_header_id,7,0,STR_PAD_LEFT);
	$po_header_id			= $r['po_header_id'];
	$po_header_id_pad		= str_pad($po_header_id,7,0,STR_PAD_LEFT);
	$date 					= $r['date'];
	$po_date				= $r['po_date'];
	$project_id 			= $r['project_id'];
	$project				= $options->getAttribute('projects','project_id',$project_id,'project_name');
	$work_category_id 		= $r['work_category_id'];
	$work_category			= $options->getAttribute('work_category','work_category_id',$work_category_id,'work');
	$sub_work_category_id 	= $r['sub_work_category_id'];
	$sub_work_category		= $options->getAttribute('work_category','work_category_id',$sub_work_category_id,'work');
	$supplier_id 			= $r['supplier_id'];
	$supplier				= $options->getAttribute('supplier','account_id',$supplier_id,'account');
	$terms 					= $r['terms'];
	$status					= $r['status'];
	$user_id				= $r['user_id'];
	$discount_amount		= $r['discount_amount'];
	$advance_payment_amount	= $r['advance_payment_amount'];
	$remarks				= $r['remarks'];
	
	$tax_gchart_id			= $r['tax_gchart_id'];
	$vat_gchart_id			= $r['vat_gchart_id'];
	$vatable				= $r['vatable'];
	$w_tax					= $r['w_tax'];
	
	$rr_date = getLatestMRRDate($apv_header_id);
	if(empty($rr_date)){
		$due_date	= date("m/d/Y",strtotime("+$terms days",strtotime($po_date)));
	}else{
		$due_date	= date("m/d/Y",strtotime("+$terms days",strtotime(getLatestMRRDate($apv_header_id))));
	}

	/*$result1 = mysql_query("select * from rr_header where status != 'C' and po_header_id = '$po_header_id'") or die(mysql_error());
	while($r1 = mysql_fetch_assoc($result1)){
		$advance_payment_amount += $r1['advance_payment_amount'];
	}
	if($advance_payment_amount > 0){
		$advances = $advance_payment_amount;
	}else if($discount_amount > 0){
		$advances = $discount_amount;
	}*/
	
		//Remaining Advance Payment
		$sqlrap = mysql_query("Select apv_header_id from apv_header where po_header_id = '$po_header_id' and status != 'C'") or die (mysql_error());
		while($mapv = mysql_fetch_assoc($sqlrap)){
			$apv_header_r = $mapv['apv_header_id'];
				$rapv = mysql_query("Select amount from apv_detail where apv_header_id = '$apv_header_r'") or die (mysql_error());
				while($rtapv = mysql_fetch_assoc($rapv)){
					$total_remaining_ap += $rtapv['amount'];
				}
		}
			$remaining_ap = $advance_payment_amount - $total_remaining_ap;

	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>ORDER SHEET</title>
<script>
function printPage() { print(); } //Must be present for Iframe printing
</script>
<link rel="stylesheet" type="text/css" href="css/printable.css" />
<style type="text/css">
*{
  letter-spacing:5px;
  font-size:12px;
}

.header_table,.content_table{
	width:100%;
}
.content_table th{
	border-top:1px solid #000;
	border-bottom:1px solid #000;	
}
</style>
</head>
<body>
<div class="container">	
    <div><!--Start of Form-->
    <?php require("form_heading.php"); ?>
      
    <div style="text-align:center; font-size:14px;">
    	ACCOUNTS PAYABLE VOUCHER
    </div>
        <div class="header clearfix" style="">
        	<table class="header_table">
            	<tr>
                	<td>Supplier:</td>
                	<td><?=$supplier?></td>
                    
                    <td>No. : </td>
                    <td>APV#<?=$apv_header_id_pad?></td>
                </tr>
                <tr>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                    
                    <td>Date : </td>
                    <td><?=date("m/d/Y", strtotime($date))?></td>
                </tr>
                <tr>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                    
                    <td>PO # : </td>
                    <td>PO#<?=$po_header_id_pad?></td>
                </tr>
                <tr>
                	<td>Project : </td>
                	<td><?=$project?></td>
                    
                    <td>Terms : </td>
                    <td><?=$terms?> DAYS</td>
                </tr>
                <tr>
                	<td>Scope of Work : </td>
                    <td><?=$work_category?> <?=$sub_work_category?></td>
                    
                    <td>Due Date: </td>
                    <td><?=$due_date?></td>
                </tr>
                
            </table>
     	</div><!--End of header--><br />

        <div class="content" >
        	<?php
			$result=mysql_query("
			select
				distinct(rr_id) as rr_id
				from
				apv_detail as d,
				apv_header as h
				where
				d.apv_header_id = '$apv_header_id' and
				h.status != 'C' and
				h.apv_header_id = d.apv_header_id
			") or die(mysql_error());	
			?>
        
        	<table cellspacing="0" cellpadding="3" class="content_table">
            	<tr>
                    <th>DESCRIPTION</th>
                    <th style="width:10%">MRR #</th>
                    <th style="width:10%">INVOICE</th>
                    <th style="width:10%">QTY</th>
                    <th style="width:10%">UOM</th>
                    <th style="width:10%">U.PRICE</th>
                    <th style="width:10%">AMOUNT</th>
                </tr>
           		<?php
				$totalamount=0;
				$total_quantity = 0;
				$i = 1;
				while($r=mysql_fetch_assoc($result)):
					$rr_id = $r['rr_id'];
					
					$newq = mysql_query("select 
										p.stock, d.rr_header_id, d.invoice, d.quantity, p.unit, d.cost, d.amount 
										from 
										rr_detail as d,
										productmaster as p
										where 
										d.rr_header_id = '$rr_id' and
										p.stock_id = d.stock_id") or die (mysql_error());
					while($ret = mysql_fetch_assoc($newq)){
				
					$totalamount += $ret['amount'];
					$total_quantity += $ret['quantity'];
				?>
                    <tr>
                        <td style="vertical-align:top; text-align:left; border-left: 1px solid black; border-right: 1px solid black;"><?=$ret['stock']?></td>
                        <td  style="text-align:center; vertical-align:top; border-right: 1px solid black;"><?=$ret['rr_header_id']?></td>
                        <!--<td  style="text-align:center; vertical-align:top;"><?=getInvoice($r['rr_id'],$r['stock_id'],$r['price'],$r['quantity'])?></td> -->
                        <td style="vertical-align:top; border-right: 1px solid black; text-align:left; vertical-align:top;"><?=$ret['invoice']?></td>
                        <td style="vertical-align:top; border-right: 1px solid black; " class="align-right;"><?=number_format($ret['quantity'],2,'.',',')?></td>
                        <td style="text-align:center; vertical-align:top; border-right: 1px solid black;"><?=$ret['unit']?></td>
                        <td style="vertical-align:top; border-right: 1px solid black; " class="align-right;"><?=number_format($ret['cost'],2,'.',',')?></td>
                        <td style="vertical-align:top; border-right: 1px solid black;" class="align-right;"><?=number_format($ret['amount'],2,'.',',')?></td>
                    </tr>
                <?php
				
					}
					
				endwhile;
				
				echo '<tr>';
				echo '<td style="border-left: 1px solid black; border-right: 1px solid black;">**** Nothing Follows ****</td>';
				echo '<td style="border-right: 1px solid black;">&nbsp;</td>';
				echo '<td style="border-right: 1px solid black;">&nbsp;</td>';	
				echo '<td style="border-right: 1px solid black;">&nbsp;</td>';	
				echo '<td style="border-right: 1px solid black;">&nbsp;</td>';	
				echo '<td style="border-right: 1px solid black;">&nbsp;</td>';
				echo '<td style="border-right: 1px solid black;">&nbsp;</td>';	
				echo '</tr>';
				
				if( !empty( $remarks ) ) :
				echo '<tr>';
				echo '<td style="border-left: 1px solid black;  border-right: 1px solid black;">'.$remarks.'</td>';
				echo '<td style="border-right: 1px solid black;">&nbsp;</td>';
				echo '<td style="border-right: 1px solid black;">&nbsp;</td>';	
				echo '<td style="border-right: 1px solid black;">&nbsp;</td>';	
				echo '<td style="border-right: 1px solid black;">&nbsp;</td>';	
				echo '<td style="border-right: 1px solid black;">&nbsp;</td>';
				echo '<td style="border-right: 1px solid black;">&nbsp;</td>';	
				echo '</tr>';
				endif;
				
				if($i<5) {
					for($newi=$i;$newi<=5;$newi++) {
						echo '<tr>';
						echo '<td style="border-left: 1px solid black;  border-right: 1px solid black;">&nbsp;</td>';
						echo '<td style="border-right: 1px solid black;" >&nbsp;</td>';
						echo '<td style="border-right: 1px solid black;" >&nbsp;</td>';	
						echo '<td style="border-right: 1px solid black;">&nbsp;</td>';	
						echo '<td style="border-right: 1px solid black;">&nbsp;</td>';
						echo '<td style="border-right: 1px solid black;">&nbsp;</td>';
						echo '<td style="border-right: 1px solid black;">&nbsp;</td>';	
						echo '</tr>';	
					}
				}	
				
				?>
                <?php
					$gross_amount = $totalamount;
					$totalamount -= $discount_amount;
					
                	$vat = $totalamount - ($totalamount / 1.12)
				
                ?>
					 <?php
                     #$vatable_amount = ($vatable) ? $totalamount / 1.12 : 0;
                     $vatable_amount = ($vatable) ? $gross_amount/1.12 : 0;
                     $vat = $vatable_amount * 0.12;
                
                if($vatable_amount){
                    $witholding_tax_amount = $vatable_amount * ($w_tax / 100);
                }else{
                    $witholding_tax_amount = $gross_amount * ($w_tax / 100);
                }
					//echo doubleval($witholding_tax_amount);
                    $net_amount = $gross_amount- ($witholding_tax_amount);
                    $c->setNumber ($net_amount,2);
					//$words =  $c->num_words().$c->appendDecimal();
					$words =  $c->num_words();
					$balance = $net_amount - $discount_amount;
					
					$n = $balance;
					$whole = floor($balance);      // 1
					$fraction = (round($n - $whole, 2)) * 100;
					
                   
                ?>
                <tr>
                	<td colspan="7"  style="border-bottom:1px solid #000;  border-right:none; font-weight:bold;"><?=$words?> & <?=$fraction?>/100</td>
                </tr>
            </table>
           
            
            <table style="float:right; font-weight:bolder;">
            	<tr>
                	<td>TOTAL:</td>
                    <td style="padding:0px 20px; border-bottom:5px double #000; text-align:right;"><?=number_format($total_quantity,2,'.',',')?></td>
                    <td>TOTAL AMOUNT</td>
                    <td style="padding:0px 20px;">PESO</td>
                    <td style="padding-left:20px; text-align:right;"><?=number_format($gross_amount,2,'.',',')?></td>
                </tr>
				<tr>
                	<td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>VATABLE</td>
                    <td style="padding:0px 20px;">PESO</td>
                    <td style="padding-left:0px 20px; text-align:right;"><?=number_format($vatable_amount,2,'.',',')?></td>
                </tr>                


                <tr>
                	<td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>VAT</td>
                    <td style="padding:0px 20px;">PESO</td>
                    <td style="padding-left:0px 20px; text-align:right;"><?=number_format($vat,2,'.',',')?></td>
                </tr>
                
                <tr>
                	<td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>W/ TAX</td>
                    <td style="padding:0px 20px;">PESO</td>
                    <td style="padding-left:0px 20px; text-align:right;"><?=number_format($witholding_tax_amount,2,'.',',')?></td>
                </tr>
                <tr>
                	<td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>NET AMOUNT</td>
                    <td style="padding:0px 20px;">PESO</td>
                    <td style="padding-left:0px 20px; text-align:right; "><?=number_format($net_amount,2,'.',',')?></td>
                </tr>
                <tr>
                	<td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>ADVANCE PAYMENT</td>
                    <td style="padding:0px 20px;">PESO</td>				
					<?php
						if($advance_payment_amount > $gross_amount){
							$discount_display = 0;
						}else{
							$discount_display = $advance_payment_amount;
						}
					?>
                    <td style="padding-left:0px 20px; text-align:right;"><?=number_format($advance_payment_amount,2,'.',',')?></td>
                </tr>
                  <tr>
                	<td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>BALANCE</td>
                    <td style="padding:0px 20px;">PESO</td>
					<?php
						if($advance_payment_amount > $gross_amount){
							$g_display = 0;
						}else{
							$g_display = $gross_amount-$advance_payment_amount;
						}
					?>
                    <td style="padding-left:0px 20px; text-align:right;border-bottom:5px double #000; border-top:1px solid #000;"><?=number_format($g_display,2)?></td>
                </tr>
            </table>
			            <br />   
            <div style="clear:both;"></div>
       
          	<table cellspacing="0" cellpadding="2" align="center" width="98%" style="border:none; text-align:center; margin-top:20px;" class="">
                <tr>
                    <td>Prepared By: <br /><br />
						<p>
							<br />
							<div style="width: 150px; border-bottom: 1px solid black; margin: 0 auto;"></div>
							<br />
							<br />
							<?=$options->getUserName($user_id);?>
						</p>
					</td>
					<td>Checked By: <br /><br /><br /><p>
                        <div style="width: 150px; border-bottom: 1px solid black; margin: 0 auto;"></div></p>
						<br />
						<br />
						<br />
						Kate Dequena
						</td>	
                    <td>Noted By: <br /><br /><br /><p>
                        <div style="width: 150px; border-bottom: 1px solid black; margin: 0 auto;"></div></p>
						<br />
						<br />
						Marian Joyce Camille Ku
						</td>					
					<td>Approved By: <br /><br /><br /><p>
                        <div style="width: 150px; border-bottom: 1px solid black; margin: 0 auto;"></div></p>
						<br />
						<br />
						Michael John S. Ku
						</td>	
					              
                </tr>
            </table>
            
        </div><!--End of content-->
    </div><!--End of Form-->
</div>
<div class="page-break"></div>
</body>
</html>
