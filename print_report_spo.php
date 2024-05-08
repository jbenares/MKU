<?php
	require_once('my_Classes/options.class.php');
	require_once('my_Classes/numbertowords.class.php');
	require_once('my_Classes/numtowords.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();		
	$po_header_id=$_REQUEST[id];

	
	$query="
		select
			 *
		 from
			  po_header as h,
			  projects as p
		 where
			h.project_id = p.project_id
		and
			po_header_id = '$po_header_id'
	";
	$result=mysql_query($query);
	$r=mysql_fetch_assoc($result);
	
	$po_header_id_pad = str_pad($po_header_id,7,0,STR_PAD_LEFT);
	$pr_header_id	= $r['pr_header_id'];
	$project_id		= $r['project_id'];
	$project_name	= $r['project_name'];
	$description	= $r['description'];
	$status			= $r['status'];
	$date			= $r['date'];
	$user_id		= $r['user_id'];
	$supplier_id	= $r['supplier_id'];
	$supplier		= $options->attr_Supplier($supplier_id,'account');
	$terms			= $r['terms'];
	$remarks		= $r['remarks'];
	$discount_amount = $r['discount_amount'];
	
	$pr_header_id_pad = str_pad($pr_header_id,7,0,STR_PAD_LEFT);
	
	$work_category_id 	= $r['work_category_id'];
	$sub_work_category_id = $r['sub_work_category_id'];
	$scope_of_work = $r['scope_of_work'];
	
	$work_category = $options->attr_workcategory($work_category_id,'work');
	$sub_work_category = $options->attr_workcategory($sub_work_category_id,'work');
	
	function po_details($po_header_id){
		$result = mysql_query("
            select
                *
            from
                po_header as h, spo_detail as d
            where
                h.po_header_id = d.po_header_id
            and
                h.po_header_id = '$po_header_id'		
        ") or die(mysql_error());
		$a = array();
		while($r = mysql_fetch_assoc($result)){
			$tmp = array();
			$tmp['spo_detail_id'] = $r['spo_detail_id'];
			$tmp['description'] = $r['description'];
			$a[] = $tmp;
		}
		
		return $a;
	}
	
	function po_sub_details($spo_detail_id){
		$result = mysql_query("
            select * from spo_detail as h, sub_spo_detail as d where h.spo_detail_id = d.spo_detail_id and h.spo_detail_id = '$spo_detail_id'	
        ") or die(mysql_error());
		$a = array();
		while($r = mysql_fetch_assoc($result)){
			$tmp = array();
			$tmp['sub_description'] = $r['sub_description'];
			$tmp['quantity'] = $r['quantity'];
			$tmp['unit'] = $r['unit'];
			$tmp['unit_cost'] = $r['unit_cost'];
			$tmp['amount'] = $r['amount'];
			$tmp['chargables'] = $r['chargables'];
			$tmp['person'] = $r['person'];
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
</style>

</head>
<body>
	<?php 
		$items_per_page = 20;
		$c = 0;
		$page_beak_content = "<div style='page-break-after:always;'></div>";
		$exit = 0;
		$page_break = 0;
		$inner_page_break = 0;
		
		$totalamount=0;
		$total_quantity = 0;
		$total_items = 0;
		
		$d = po_details($po_header_id);
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
            PURCHASE ORDER
        </div>
        <div class="header clearfix" style="">
            <table style="width:60%; float:left;">
                <tr>
                    <td width="24%">Supplier :</td>
                    <td width="76%" style="border-bottom:1px solid #000;"><?=$supplier?></td>
                </tr>
                <tr>
                    <td width="24%">Project :</td>
                    <td width="76%" style="border-bottom:1px solid #000;"><?=$project_name?></td>
                </tr>
                <tr>
                    <td>Scope of Work:</td>
                    <td style="border-bottom:1px solid #000;"><?=$scope_of_work." | ".$work_category." | ".$sub_work_category?></td>
                  
                </tr>
            </table>
            
            <table style="width:30%; float:right; margin-right:20px;">
                <tr>
                    
                    <td width="38%">PO # :</td>
                    <td width="62%" style="border-bottom:1px solid #000;"><?=$po_header_id_pad?></td>
                </tr>
                <tr>                  
                  <td>Date :</td>
                  <td style="border-bottom:1px solid #000;"><?=date("F j, Y",strtotime($date))?></td>
                </tr>
                
                <!--<tr>                  
                  <td>RTP # :</td>
                  <td width="62%" style="border-bottom:1px solid #000;"><?=$pr_header_id_pad?></td>
                </tr> -->
                
                <tr>                  
                  <td>Terms :</td>
                  <td style="border-bottom:1px solid #000;"><?=$terms?></td>
                </tr>
            </table>
        </div><!--End of header--><br />
    
        <table cellspacing="0">
            <tr>
                <th style="width:5%;"></th>
                <th colspan="2">DESCRIPTION</th>
                <th style="width:10%;">EQUIPT</th>
                <th style="width:10%;">PERSON</th>
                <th style="width:10%;">QTY</th>
                <th style="width:10%;">UNIT</th>
                <th style="width:10%;">UNIT COST</th>
                <th style="width:10%;">AMOUNT</th>
            </tr>
            <?php
            $i=0;
			for(;$c < count($d) ; $c++ ){
				$r = $d[$c];
				
				/*echo "<pre>";
				print_r($r);
				echo "</pre>";*/
		
                $spo_detail_id 	= $r['spo_detail_id'];
                $description	= $r['description'];
                $i++;
				
				if(!$inner_page_break){
				$rs = mysql_query("
                    select * from spo_detail as h, sub_spo_detail as d where h.spo_detail_id = d.spo_detail_id and h.spo_detail_id = '$spo_detail_id'
                ") or die(mysql_error());				
				$max_rows = mysql_num_rows($rs);
				$counter = 0;
				$po = po_sub_details($spo_detail_id);
				}
				
				if( $i > $items_per_page){
					echo "<tr><td colspan='9' style='border-bottom:1px solid #000;'></td></tr>
							</table>";	
					$page_break = 1;
					break;
				}
				
				if($c == (count($d) -1 )){
					$exit = 1;	
				}
            ?>
                <tr>
                    <td style="border-left:1px solid #000; border-right:1px solid #000;">&nbsp;</td>
                    <td style="font-weight:bold;  border-left:1px solid #000; border-right:1px solid #000;" colspan="2" ><?=$description?></td>
                    <td style=" border-left:1px solid #000; border-right:1px solid #000;">&nbsp;</td>
                    <td style=" border-left:1px solid #000; border-right:1px solid #000;">&nbsp;</td>
                    <td style=" border-left:1px solid #000; border-right:1px solid #000;">&nbsp;</td>
                    <td style=" border-left:1px solid #000; border-right:1px solid #000;">&nbsp;</td>
                    <td style=" border-left:1px solid #000; border-right:1px solid #000;">&nbsp;</td>
                    <td style=" border-left:1px solid #000; border-right:1px solid #000;">&nbsp;</td>
                </tr>
                <?php
                #while($s = mysql_fetch_assoc($rs)){
				for(;$counter < count($po) ; $counter++){
					$s = $po[$counter];
					#$counter ++;
                    $i++;
					
					if( ($i) > $items_per_page){
						echo "<tr><td colspan='9' style='border-bottom:1px solid #000;'></td></tr>
								</table>";	
						$page_break = 1;
						$inner_page_break = 1;
						break;
					}
					
					if($counter == (count($po) -1 )){
						$inner_page_break = 0;
					}
					
					if($counter == $max_rows ){
						$inner_page_break = 0;	
					}
					
					$totalamount += $s['amount'];
                    $total_items += 1;
                ?>
                <tr>
                    <td style="border-left:1px solid #000; border-right:1px solid #000;">&nbsp;</td>
                    <td style="">&nbsp;</td>
                    <td><?=$s['sub_description']?></td>
                    <td style="border-left:1px solid #000; border-right:1px solid #000;"><?=$s['chargables']?></td>
                    <td style="border-left:1px solid #000; border-right:1px solid #000;"><?php
					if($s[unit]!="Lot"){
							echo $s['person'];
						}
					?></td>
                    <td style="text-align:right;  border-left:1px solid #000; border-right:1px solid #000;"><?=$s['quantity']?></td>
                    <td style="border-left:1px solid #000; border-right:1px solid #000;"><?=$s['unit']?></td>
                    <td style="text-align:right;  border-left:1px solid #000; border-right:1px solid #000;"><?=number_format($s['unit_cost'],4,'.',',')?></td>
                    <td style="text-align:right;  border-left:1px solid #000; border-right:1px solid #000;"><?=number_format($s['amount'],4,'.',',')?></td>
                </tr>
                <?php 
				} 
				?>
                <?php
				if($inner_page_break){
					break;	
				}
                ?>
            <?php
            }#END OF FOR LOOP
			
			if(!$exit || $inner_page_break){
				continue;				
			}
			
            if( !empty( $remarks ) ) :
				$i++;
            ?>
                <tr>
                    <td style="border-left:1px solid #000; border-right:1px solid #000;">&nbsp;</td>
                    <td style="font-weight:bold;  border-left:1px solid #000; border-right:1px solid #000;" colspan="2" ><?=$remarks?></td>
                    <td style=" border-left:1px solid #000; border-right:1px solid #000;">&nbsp;</td>
                    <td style=" border-left:1px solid #000; border-right:1px solid #000;">&nbsp;</td>
                    <td style=" border-left:1px solid #000; border-right:1px solid #000;">&nbsp;</td>
                    <td style=" border-left:1px solid #000; border-right:1px solid #000;">&nbsp;</td>
                    <td style=" border-left:1px solid #000; border-right:1px solid #000;">&nbsp;</td>
                    <td style=" border-left:1px solid #000; border-right:1px solid #000;">&nbsp;</td>
                </tr>
            <?php
            endif;
            
			if($discount_amount > 0){
			echo '<tr>';
            echo '<td style="border-right:1px solid #000; border-left:1px solid #000;">&nbsp;</td>';
            echo '<td style="border-right: 1px solid #000000; font-weight:bold;" colspan="2">Less : Discount</td>';
            echo '<td style="border-right: 1px solid #000000;" align=right>&nbsp;</td>';
            echo '<td style="border-right: 1px solid #000000;" align=right>&nbsp;</td>';	
            echo '<td style="border-right: 1px solid #000000;" align=right>&nbsp;</td>';	
			echo '<td style="border-right: 1px solid #000000;" align=right>&nbsp;</td>';	
            echo '<td style="border-right: 1px solid #000000;" align=right>&nbsp;</td>';	
            echo '<td style="border-right: 1px solid #000000; font-weight:bold; text-align:right;" align=right>'.number_format(0-$discount_amount,4,'.',',').'</td>';	
            echo '</tr>';
			}
			
            echo '<tr>';
            echo '<td style="border-right:1px solid #000; border-left:1px solid #000;">&nbsp;</td>';
            echo '<td style="border-right: 1px solid #000000;" colspan="2">********** Nothing Follows **********</td>';
            echo '<td style="border-right: 1px solid #000000;" align=right>&nbsp;</td>';
			echo '<td style="border-right: 1px solid #000000;" align=right>&nbsp;</td>';
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
					<td style="width:5%; border-left:1px solid #000; border-right:1px solid #000;">&nbsp;</td>
					<td style="font-weight:bold;  border-left:1px solid #000; border-right:1px solid #000;" colspan="2" > Total Items : <?=$total_items?></td>
					<td style=" border-left:1px solid #000; border-right:1px solid #000;">&nbsp;</td>
					<td style=" border-left:1px solid #000; border-right:1px solid #000;">&nbsp;</td>
					<td style=" border-left:1px solid #000; border-right:1px solid #000;">&nbsp;</td>
                    <td style=" border-left:1px solid #000; border-right:1px solid #000;">&nbsp;</td>
					<td style=" border-left:1px solid #000; border-right:1px solid #000;">&nbsp;</td>
					<td style=" border-left:1px solid #000; border-right:1px solid #000;">&nbsp;</td>
				</tr>
				<?php
				} else {
				?>
				<tr>
					<td style="width:5%; border-left:1px solid #000; border-right:1px solid #000;">&nbsp;</td>
					<td style="font-weight:bold;  border-left:1px solid #000; border-right:1px solid #000;" colspan="2" >&nbsp;</td>
					<td style=" border-left:1px solid #000; border-right:1px solid #000;">&nbsp;</td>
					<td style=" border-left:1px solid #000; border-right:1px solid #000;">&nbsp;</td>
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
		
		$convert = new num2words();
		$convert->setNumber($totalamount);
		$words = strtoupper($convert->getCurrency());
		
		//$c=new NumToWords();
        //$c->setNumber($totalamount);
        //$words =  $c->num_words().$c->appendDecimal();
		
        $vat = $totalamount - ($totalamount / 1.12)
    
    ?>
    <table>
        <tr class="summary">
            <td style="border-bottom:1px solid #000; border-left:1px solid #000; border-right:none; font-weight:bold;" colspan="2"><?=$words?></td>
            <td colspan="4" style=" width:20%;  border-top:1px solid #000; border-bottom:1px solid #000; border-right:none; border-left:1px solid #000;" nowrap="nowrap" ><strong>SUBTOTAL :</strong></td>
            <td style="width:10%; border-left:none; border-top:1px solid #000; border-bottom:1px solid #000; text-align:right; "><strong>PESO</strong></td>
            <td class="align-right" style=" width:10%; border:1px solid #000; font-weight:bold;"><?=number_format($totalamount,2,'.',',')?></td>
        </tr>
        <tr class="summary">
            <td colspan="2"  class="no-border align-right" style="border:none;"></td>
            <td colspan="4" style="border-top:1px solid #000; border-bottom:1px solid #000; border-right:none; border-left:1px solid #000;"><strong>VAT 12%(INCLU) :</strong></td>
            <td style="border-left:none; border-top:1px solid #000; border-bottom:1px solid #000; text-align:right; "><strong>PESO</strong></td>
            <td class="align-right" style="border:1px solid #000; font-weight:bold;"><?=number_format($vat,2,'.',',')?></td>
        </tr>
        <tr class="summary">
            <td colspan="2" class="no-border align-right" style="border:none;"></td>
            <td colspan="4" style="border-top:1px solid #000; border-bottom:1px solid #000; border-right:none; border-left:1px solid #000;" nowrap="nowrap"><strong>NET AMOUNT :</strong></td>
            <td style="border-left:none; border-top:1px solid #000; border-bottom:1px solid #000;  text-align:right;"><strong>PESO</strong></td>
            <td class="align-right" style="border:1px solid #000; font-weight:bold;"><?=number_format($totalamount,2,'.',',')?></td>
        </tr>
    </table>
    
    <table cellspacing="0" cellpadding="5" align="center" width="98%" style="border:none; text-align:center; margin-top:10px;" class="summary">
        <tr>
            <td>Prepared By:<p>
                <input type="text" class="line_bottom" value="<?=$options->getUserName($user_id);?>" /><br>Purchasing Staff</p></td>
            <td>Checked By:<p contenteditable="true">
                <input type="text" class="line_bottom" contenteditable="true" /><br>Purchasing Manager</div></p></td>
            <td>Approved By:<p>
                <input type="text" class="line_bottom" /><br>GM / President</p></td>
        </tr>
    </table>
	<div class="divFooter">
        F-PUR-004a<br>
        Rev.0 2/29/16
    </div>
	<div class="page-break"></div>
</body>
</html>
