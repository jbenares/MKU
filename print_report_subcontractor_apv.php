<?php
	require_once('my_Classes/options.class.php');
	require_once('my_Classes/numbertowords.class.php');
	require_once('my_Classes/numtowords.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();		
	$sub_apv_header_id = $_REQUEST[id];

	
	$query="
		select
			 *
		 from
			  sub_apv_header as h,
			  projects as p
		 where
			h.project_id = p.project_id
		and
			sub_apv_header_id = '$sub_apv_header_id'
	";
	$result=mysql_query($query);
	$r=mysql_fetch_assoc($result);
	
	
	$sub_apv_header_id		= $r['sub_apv_header_id'];
	$po_header_id			= $r['po_header_id'];
	$date					= $r['date'];
	$po_date				= $r['po_date'];
	$project_id 			= $r['project_id'];
	$work_category_id		= $r['work_category_id'];
	$sub_work_category_id	= $r['sub_work_category_id'];
	$supplier_id			= $r['supplier_id'];
	$terms					= $r['terms'];
	$remarks				= $r['remarks'];
	$discount_amount		= $r['discount_amount'];
	
	$wtax_gchart_id			= $r['wtax_gchart_id'];		
	$wtax					= $r['wtax'];
	$vat_gchart_id			= $r['vat_gchart_id'];
	$vat					= $r['vat'];
	$user_id				= $r['user_id'];
	
	$retention_rate			= $r['retention_rate'];
	$chargable_amount		= $r['chargable_amount'];
	$other_chargable_amount	= $r['other_chargable_amount'];
	
	$po_header_id_pad = str_pad($po_header_id,7,0,STR_PAD_LEFT);
	$supplier		= $options->attr_Supplier($supplier_id,'account');	
	
	$work_category = $options->attr_workcategory($work_category_id,'work');
	$sub_work_category = $options->attr_workcategory($sub_work_category_id,'work');
	
	function sub_apv_details($sub_apv_header_id){
		$result = mysql_query("
            select
                *
            from
                sub_apv_header as h, sub_apv_detail as d
            where
				h.sub_apv_header_id = d.sub_apv_header_id
            and
				h.sub_apv_header_id = '$sub_apv_header_id'
        ") or die(mysql_error());
		$a = array();
		while($r = mysql_fetch_assoc($result)){
			$tmp = array();
			
			$tmp['description'] 	= $r['description'];
			$tmp['sub_description'] = $r['sub_description'];
			$tmp['quantity'] 		= $r['quantity'];
			$tmp['unit']			= $r['unit'];
			$tmp['unit_cost']		= $r['unit_cost'];
			$tmp['amount']			= $r['amount'];
			
			$a[] = $tmp;
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
<link rel="stylesheet" type="text/css" href="css/print_report_spo.css"/>
<style type="text/css">
body{
	font-size:20px;
}
*{
	font-size:20px;	
}
td,th {
	font-family: Arial;
	font-size: 20px;
	text-align:left;
}
.table-content th{
	border-top:1px solid #000;
	border-bottom:1px solid #000;	
}
.table-content td{
	border-left:1px solid #000;
	border-right:1px solid #000;	
}
</style>

</head>
<body>
	<?php 
		$items_per_page = 15;
		$c = 0;
		$page_beak_content = "<div style='page-break-after:always;'></div>";
		$exit = 0;
		$page_break = 0;
		$inner_page_break = 0;
		
		$totalamount=0;
		$total_quantity = 0;
		$total_items = 0;
		
		$d = sub_apv_details($sub_apv_header_id);
		
		/*echo "<pre>";
		print_r($d);
		echo "</pre>";*/

		while(1):
			if($page_break) echo $page_beak_content;
			$page_break = 0;
		
    ?>
		<?php
            require("form_heading.php");
        ?>
          
        <div style="text-align:center; font-weight:bolder;">
          	SUBCON ACCOUNTS PAYABLE VOUCHER
        </div>
        <div class="header clearfix" style="">
            <table style="width:60%; float:left;">
                <tr>
                    <td width="24%">Supplier :</td>
                    <td width="76%" style="border-bottom:1px solid #000;"><?=$supplier?></td>
                </tr>
                <tr>
                    <td width="24%">Project :</td>
                    <td width="76%" style="border-bottom:1px solid #000;"><?=$options->getAttribute('projects','project_id',$project_id,'project_name')?></td>
                </tr>
                <tr>
                    <td>Scope of Work:</td>
                    <td style="border-bottom:1px solid #000;"><?=$scope_of_work." | ".$work_category." | ".$sub_work_category?></td>
                  
                </tr>
            </table>
            
            <table style="width:30%; float:right; margin-right:20px;">
	            <tr>
                    <td width="38%">NO  :</td>
                    <td width="62%" style="border-bottom:1px solid #000;">SUB APV#<?=str_pad($sub_apv_header_id,7,0,STR_PAD_LEFT)?></td>
                </tr>
                <tr>
                    <td width="38%">PO # :</td>
                    <td width="62%" style="border-bottom:1px solid #000;"><?=$po_header_id_pad?></td>
                </tr>
                <tr>                  
                  <td>Date :</td>
                  <td style="border-bottom:1px solid #000;"><?=date("F j, Y",strtotime($date))?></td>
                </tr> 
                <tr>                  
                  <td>Terms :</td>
                  <td style="border-bottom:1px solid #000;"><?=$terms?></td>
                </tr>
            </table>
        </div><!--End of header--><br />
    
        <table cellspacing="0" cellpadding="4" class="table-content">
            <tr>
                <!--<th >DESC.</th> -->
                <th >SUB DESC.</th>
                <th style="width:10%; text-align:right;" >QTY</th>
                <th style="width:10%;">UNIT</th>
                <th style="width:10%; text-align:right;">UNIT COST</th>
                <th style="width:10%; text-align:right;">AMOUNT</th>
            </tr>
            <?php if(empty($d)){ echo "</table>"; break; } ?>
			<?php
           
            $i=0;
            
            for( ; $c < count($d) ; $c++ ){
                $i++;
                $r = $d[$c];
				
                $quantity 		= $r['quantity'];
                $amount			= $r['amount'];
                
                if($i > $items_per_page){
                    echo "<tr><td colspan='5' style='border-bottom:1px solid #000;'></td></tr>
                            </table>";	
                    $page_break = 1;
                    break;
                }
                
                if($c == (count($d) -1 )){
                    $exit = 1;	
                }
                
                $total_quantity += $quantity;
                $total_items++;
                $totalamount += $amount;
                
            ?>
                <tr>
                    <!--<td><?=$r['description']?></td> -->
                    <td><?=$r['sub_description']?></td>
                    <td style="width:5%; text-align:right;"><?=$r['quantity']?></td>
                    <td style="width:5%;"><?=$r['unit']?></td>
                    <td style="width:5%; text-align:right"><?=$r['unit_cost']?></td>
                    <td style="width:5%; text-align:right;"><?=$r['amount']?></td>
                </tr>
            <?php } ?>
           
            <?php
			if(!$exit){
				continue;				
			}
			
            if( !empty( $remarks ) ) :
				$i++;
            ?>
                <tr>
                    <td style="font-weight:bold;  border-left:1px solid #000; border-right:1px solid #000;" colspan="1" ><?=$remarks?></td>
                    <td style=" border-left:1px solid #000; border-right:1px solid #000;">&nbsp;</td>
                    <td style=" border-left:1px solid #000; border-right:1px solid #000;">&nbsp;</td>
                    <td style=" border-left:1px solid #000; border-right:1px solid #000;">&nbsp;</td>
                    <td style=" border-left:1px solid #000; border-right:1px solid #000;">&nbsp;</td>
                </tr>
            <?php
            endif;
            
			if($discount_amount > 0){
			echo '<tr>';
            echo '<td style="border-right: 1px solid #000000; font-weight:bold;" colspan="1">Less : Discount</td>';
            echo '<td style="border-right: 1px solid #000000;" align=right>&nbsp;</td>';
            echo '<td style="border-right: 1px solid #000000;" align=right>&nbsp;</td>';	
            echo '<td style="border-right: 1px solid #000000;" align=right>&nbsp;</td>';	
            echo '<td style="border-right: 1px solid #000000; font-weight:bold; text-align:right;" align=right>'.number_format(0-$discount_amount,4,'.',',').'</td>';	
            echo '</tr>';
			}
			
            echo '<tr>';
            echo '<td style="border-right: 1px solid #000000;" colspan="1">********** Nothing Follows **********</td>';
            echo '<td style="border-right: 1px solid #000000;" align=right>&nbsp;</td>';
            echo '<td style="border-right: 1px solid #000000;" align=right>&nbsp;</td>';	
            echo '<td style="border-right: 1px solid #000000;" align=right>&nbsp;</td>';	
            echo '<td style="border-right: 1px solid #000000;" align=right>&nbsp;</td>';	
            echo '</tr>';
            
            ?>
            
            <?php
			for($newi=$i;$newi<=$items_per_page;$newi++) {
				
				if( $newi == $items_per_page ){
				?>
				<tr>
					<td style="font-weight:bold;  border-left:1px solid #000; border-right:1px solid #000;" colspan="1" > Total Items : <?=$total_items?></td>
					<td style=" border-left:1px solid #000; border-right:1px solid #000;">&nbsp;</td>
					<td style=" border-left:1px solid #000; border-right:1px solid #000;">&nbsp;</td>
					<td style=" border-left:1px solid #000; border-right:1px solid #000;">&nbsp;</td>
					<td style=" border-left:1px solid #000; border-right:1px solid #000;">&nbsp;</td>
				</tr>
				<?php
				} else {
				?>
				<tr>
					<td style="font-weight:bold;  border-left:1px solid #000; border-right:1px solid #000;" colspan="1" >&nbsp;</td>
					<td style=" border-left:1px solid #000; border-right:1px solid #000;">&nbsp;</td>
					<td style=" border-left:1px solid #000; border-right:1px solid #000;">&nbsp;</td>
					<td style=" border-left:1px solid #000; border-right:1px solid #000;">&nbsp;</td>
					<td style=" border-left:1px solid #000; border-right:1px solid #000;">&nbsp;</td>
				</tr>
				<?php	
				}
            }
            ?>
        </table>     
        <?php break; ?>
   	<?php endwhile; ?>                  
    
    <?php
	
		$totalamount -= $discount_amount;
		
		$vatable_amount = (($totalamount) / (1 + ($vat/100)));
		$vat_amount		= $vatable_amount * ($vat/100);
		$wtax_amount 	= $vatable_amount * ($wtax/100);	
		
		$retention_amount = $totalamount * ($retention_rate/100);
		
		$net_amount		= $totalamount - $wtax_amount - $retention_amount - $chargable_amount - $other_chargable_amount;
		
		$convert = new num2words();
		$convert->setNumber($net_amount);
		$words = strtoupper($convert->getCurrency());
    
    ?>
    <table>
        <tr class="summary">
            <td style="border-bottom:1px solid #000; border-left:1px solid #000; border-right:none; font-weight:bold;" colspan="2"><?=$words?></td>
            <td colspan="2" style=" width:20%;  border-top:1px solid #000; border-bottom:1px solid #000; border-right:none; border-left:1px solid #000;" nowrap="nowrap" ><strong>SUBTOTAL :</strong></td>
            <td style="width:10%; border-left:none; border-top:1px solid #000; border-bottom:1px solid #000; text-align:right; "><strong>PESO</strong></td>
            <td class="align-right" style=" width:10%; border:1px solid #000; font-weight:bold;"><?=number_format($totalamount,2,'.',',')?></td>
        </tr>
        
        <tr class="summary">
            <td colspan="2" class="no-border align-right" style="border:none;"></td>
            <td colspan="2" style=" width:20%;  border-top:1px solid #000; border-bottom:1px solid #000; border-right:none; border-left:1px solid #000;" nowrap="nowrap" ><strong>VATABLE :</strong></td>
            <td style="width:10%; border-left:none; border-top:1px solid #000; border-bottom:1px solid #000; text-align:right; "><strong>PESO</strong></td>
            <td class="align-right" style=" width:10%; border:1px solid #000; font-weight:bold;"><?=number_format($vatable_amount,2,'.',',')?></td>
        </tr>
        
        <tr class="summary">
            <td colspan="2" class="no-border align-right" style="border:none;"></td>
            <td colspan="2" style=" width:20%;  border-top:1px solid #000; border-bottom:1px solid #000; border-right:none; border-left:1px solid #000;" nowrap="nowrap" ><strong>VAT :</strong></td>
            <td style="width:10%; border-left:none; border-top:1px solid #000; border-bottom:1px solid #000; text-align:right; "><strong>PESO</strong></td>
            <td class="align-right" style=" width:10%; border:1px solid #000; font-weight:bold;"><?=number_format($vat_amount,2,'.',',')?></td>
        </tr>
        <tr class="summary">
            <td colspan="2"  class="no-border align-right" style="border:none;"></td>
            <td colspan="2" style="border-top:1px solid #000; border-bottom:1px solid #000; border-right:none; border-left:1px solid #000;"><strong>W/TAX :</strong></td>
            <td style="border-left:none; border-top:1px solid #000; border-bottom:1px solid #000; text-align:right; "><strong>PESO</strong></td>
            <td class="align-right" style="border:1px solid #000; font-weight:bold;"><?=number_format($wtax_amount,2,'.',',')?></td>
        </tr>
        <tr class="summary">
            <td colspan="2"  class="no-border align-right" style="border:none;"></td>
            <td colspan="2" style="border-top:1px solid #000; border-bottom:1px solid #000; border-right:none; border-left:1px solid #000;"><strong>RETENTION PAYABLES :</strong></td>
            <td style="border-left:none; border-top:1px solid #000; border-bottom:1px solid #000; text-align:right; "><strong>PESO</strong></td>
            <td class="align-right" style="border:1px solid #000; font-weight:bold;"><?=number_format($retention_amount,2,'.',',')?></td>
        </tr>
        <tr class="summary">
            <td colspan="2"  class="no-border align-right" style="border:none;"></td>
            <td colspan="2" style="border-top:1px solid #000; border-bottom:1px solid #000; border-right:none; border-left:1px solid #000;"><strong>CHARGABLES :</strong></td>
            <td style="border-left:none; border-top:1px solid #000; border-bottom:1px solid #000; text-align:right; "><strong>PESO</strong></td>
            <td class="align-right" style="border:1px solid #000; font-weight:bold;"><?=number_format($chargable_amount,2,'.',',')?></td>
        </tr>
        <tr class="summary">
            <td colspan="2"  class="no-border align-right" style="border:none;"></td>
            <td colspan="2" style="border-top:1px solid #000; border-bottom:1px solid #000; border-right:none; border-left:1px solid #000;"><strong>RMY LENDING :</strong></td>
            <td style="border-left:none; border-top:1px solid #000; border-bottom:1px solid #000; text-align:right; "><strong>PESO</strong></td>
            <td class="align-right" style="border:1px solid #000; font-weight:bold;"><?=number_format($other_chargable_amount,2,'.',',')?></td>
        </tr>
        <tr class="summary">
            <td colspan="2" class="no-border align-right" style="border:none;"></td>
            <td colspan="2" style="border-top:1px solid #000; border-bottom:1px solid #000; border-right:none; border-left:1px solid #000;" nowrap="nowrap"><strong>NET AMOUNT :</strong></td>
            <td style="border-left:none; border-top:1px solid #000; border-bottom:1px solid #000;  text-align:right;"><strong>PESO</strong></td>
            <td class="align-right" style="border:1px solid #000; font-weight:bold;"><?=number_format($net_amount,2,'.',',')?></td>
        </tr>
    </table>
    
    <table cellspacing="0" cellpadding="5" align="center" width="98%" style="border:none; text-align:center; margin-top:10px; "class="summary">
        <tr>
            <td>Prepared By:<p>
                <input type="text" class="line_bottom" /><br><?=$options->getUserName($user_id);?></p></td>
            <td>Checked By:<p>
                <input type="text" class="line_bottom" contenteditable="true" /><br>May Domingo</p></td>
			<td>Recomd'g Aprroval:<p>
                <input type="text" class="line_bottom" contenteditable="true" /><br>Silvestre Lareza</p></td>
            <td>Approved By:<p>
                <input type="text" class="line_bottom" /><br>J.E. Cruz / R. Yanson Jr.</p></td>
        </tr>
    </table>
	<div class="page-break"></div>
</body>
</html>
