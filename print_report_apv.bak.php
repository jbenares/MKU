<?php
	require_once('my_Classes/options.class.php');
	require_once('my_Classes/numbertowords.class.php');
	include_once("conf/ucs.conf.php");
	
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
	
	
	$tax_gchart_id			= $r['tax_gchart_id'];
	$vat_gchart_id			= $r['vat_gchart_id'];
	$vatable				= $r['vatable'];
	$w_tax					= $r['w_tax'];
	
	

	$due_date	= date("m/d/Y",strtotime("+$terms days",strtotime($po_date)));

	
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
					h.rr_header_id,p.stock, p.unit,d.quantity,d.cost,d.amount
				FROM
					rr_header as h, rr_detail as d,productmaster as p
				where
					h.rr_header_id = d.rr_header_id
				AND	
					d.stock_id = p.stock_id
				and
					h.rr_header_id in
				(
					select rr_header_id from apv_detail as d, apv_mrr_detail as mrr
					where
						d.apv_detail_id = mrr.apv_detail_id
					AND
						apv_header_id = '$apv_header_id'
				)
				and
					d.stock_id IN
				(
					select d.stock_id from apv_detail as d where apv_header_id = '$apv_header_id'	
				)
			") or die(mysql_error());	
			?>
        
        	<table cellspacing="0" class="content_table">
            	<tr>
                    <th>DESCRIPTION</th>
                    <th style="width:5%">MRR #</th>
                    <th style="width:5%">QTY</th>
                    <th style="width:5%">UOM</th>
                    <th style="width:5%">U.PRICE</th>
                    <th style="width:5%">AMOUNT</th>
                </tr>
           		<?php
				$totalamount=0;
				$total_quantity = 0;
				$i = 1;
				while($r=mysql_fetch_assoc($result)):
					$totalamount += $r['amount'];
					$total_quantity += $r['quantity'];
				?>
                    <tr>
                        <td style="vertical-align:top; text-align:left; "><?=$r['stock']?></td>
                        <td  style="text-align:center; vertical-align:top;"><?=$r['rr_header_id']?></td>
                        <td style="vertical-align:top; " class="align-right"><?=number_format($r['quantity'],2,'.',',')?></td>
                        <td style="text-align:center; vertical-align:top;"><?=$r['unit']?></td>
                        <td style="vertical-align:top; " class="align-right"><?=number_format($r['cost'],2,'.',',')?></td>
                        <td style="vertical-align:top; " class="align-right"><?=number_format($r['amount'],2,'.',',')?></td>
                    </tr>
                <?php
				endwhile;
				
				echo '<tr>';
				echo '<td>**** Nothing Follows ****</td>';
				echo '<td>&nbsp;</td>';
				echo '<td>&nbsp;</td>';	
				echo '<td>&nbsp;</td>';	
				echo '<td>&nbsp;</td>';
				echo '<td>&nbsp;</td>';	
				echo '</tr>';
				
				if($i<20) {
					for($newi=$i;$newi<=26;$newi++) {
						echo '<tr>';
						echo '<td>&nbsp;</td>';
						echo '<td>&nbsp;</td>';
						echo '<td>&nbsp;</td>';	
						echo '<td>&nbsp;</td>';	
						echo '<td>&nbsp;</td>';
						echo '<td>&nbsp;</td>';	
						echo '</tr>';
						echo '</tr>';						
					}
				}	
				
				?>
                <?php
					$c->setNumber($totalamount);
					$words =  $c->num_words().$c->appendDecimal();
                	$vat = $totalamount - ($totalamount / 1.12)
				
				?>
                <tr>
                	<td colspan="6"  style="border-bottom:1px solid #000;  border-right:none; font-weight:bold;"><?=$words?></td>
                </tr>
            </table>
            <?php
			$vatable_amount = ($vatable) ? $totalamount / 1.12 : 0;
			$vat = $vatable_amount * 0.12;
			
			if($vatable_amount){
				$witholding_tax_amount = $vatable_amount * ($w_tax / 100);
			}else{
				$witholding_tax_amount = $totalamount * ($w_tax / 100);
			}
			$net_amount = $totalamount - $witholding_tax_amount;
            			
			?>
            
            <table style="float:right; font-weight:bolder;">
            	<tr>
                	<td>TOTAL:</td>
                    <td style="padding:0px 20px; border-bottom:5px double #000; text-align:right;"><?=number_format($total_quantity,2,'.',',')?></td>
                    <td>TOTAL AMOUNT</td>
                    <td style="padding:0px 20px;">PESO</td>
                    <td style="padding-left:20px; text-align:right;"><?=number_format($totalamount,2,'.',',')?></td>
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
                    <td style="padding-left:0px 20px; text-align:right; border-bottom:5px double #000; border-top:1px solid #000;"><?=number_format($net_amount,2,'.',',')?></td>
                </tr>
            </table>
            <div style="clear:both;"></div>
                       
          	<table cellspacing="0" cellpadding="5" align="center" width="98%" style="border:none; text-align:center; margin-top:10px;" class="summary">
                <tr>
                    <td>Prepared By:<p>
                        <input type="text" class="line_bottom" /><br><?=$options->getUserName($user_id);?></p></td>
                    <td>Checked By:<p>
                        <input type="text" class="line_bottom" /><br>Rommel Armenion</p></td>
                    <td>Approved By:<p>
                        <input type="text" class="line_bottom" /><br>R. Yanson, Jr./M. Yanson</p></td>
                </tr>
            </table>
            
        </div><!--End of content-->
    </div><!--End of Form-->
</div>
<div class="page-break"></div>
</body>
</html>
