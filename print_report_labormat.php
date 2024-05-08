<?php
	require_once('my_Classes/options.class.php');
	require_once('my_Classes/numbertowords.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
	
	$n =new NumToWords();
	
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
	$vat			= $r['vat'];
	$wtax			= $r['wtax'];
	$note			= $r['note'];
	
	$pr_header_id_pad = str_pad($pr_header_id,7,0,STR_PAD_LEFT);
	
	$work_category_id 	= $r['work_category_id'];
	$sub_work_category_id = $r['sub_work_category_id'];
	$scope_of_work = $r['scope_of_work'];
	
	$work_category = $options->attr_workcategory($work_category_id,'work');
	$sub_work_category = $options->attr_workcategory($sub_work_category_id,'work');

	function getPODetails($po_header_id){
		$options=new options();	
		$query="
			select
				*
			from
				po_detail as d
			where
				po_header_id='$po_header_id'";
		$result = mysql_query($query) or die(mysql_error());
		$a = array();
		while($r = mysql_fetch_assoc($result)){
			$tmp = array();
			$tmp['quantity'] 		= $r['quantity'];
            $tmp['stock_id']		= $r['stock_id'];
            $tmp['stock'] 			= $options->getAttribute('productmaster','stock_id',$r['stock_id'],'stock');
            $tmp['stockcode']		= $options->getAttribute('productmaster','stock_id',$r['stock_id'],'stockcode');
            $tmp['unit']			= $options->getAttribute('productmaster','stock_id',$r['stock_id'],'unit');
            $tmp['cost']			= $r['cost'];
            $tmp['amount']			= $r['amount'];
            $tmp['details']			= $r['details'];
			$tmp['_type']			= $r['_type'];
			$tmp['_unit']			= $r['_unit'];
			$a[] = $tmp;
		}
		return $a;
	}
	
	$po = getPODetails($po_header_id);


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>ORDER SHEET</title>
<script>
function printPage() { print(); } //Must be present for Iframe printing
</script>
<link rel="stylesheet" type="text/css" href="css/print_po.css"/>
<style type="text/css">
	body{
		font-size:15px;
	}
	*{
		font-size:15px;	
	}
	td,th {
		font-family: Arial;
		font-size: 15px;
		text-align:left;
	}
</style>

</head>
<body>
		
    
    <?php 
	$c = 0;
	$page_beak_content = "<div style='page-break-after:always;'></div>";
	$exit = 0;
	$page_break = 0;
	while(1){ 
		if($page_break) echo $page_beak_content;
		$page_break = 0;
	?>
    <?php
		require("form_heading.php");
    ?>
      
    <div style="text-align:center;">
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
            <tr>
                <td>VAT:</td>
                <td style="border-bottom:1px solid #000;"><?=$vat?> %</td>
            </tr>
            <tr>
                <td>WTAX:</td>
                <td style="border-bottom:1px solid #000;"><?=$wtax?>%</td>
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
            
            <tr>                  
              <td>RTP # :</td>
              <td width="62%" style="border-bottom:1px solid #000;"><?=$pr_header_id_pad?></td>
            </tr>
            
            <tr>                  
              <td>Terms :</td>
              <td style="border-bottom:1px solid #000;"><?=$terms?></td>
            </tr>
        </table>
    </div><!--End of header--><br />
        	
    <table cellspacing="0" class="content">
        <tr>
            <th>DESCRIPTION</th>
            <th style="width:10%;">QTY</th>
            <th style="width:10%;">UOM</th>
            <th style="width:10%;">U.PRICE</th>
            <th style="width:10%;">AMOUNT</th>
        </tr>
        <?php if(empty($po)){ echo "</table>"; break; } ?>
        <?php
        $totalamount=0;
        $total_quantity = 0;
        $total_items = 0;
        $i=0;
		/*echo "<pre>";
		print_r($po);
		echo "</pre>";*/
		
       	for( ; $c < count($po) ; $c++ ){
			$i++;
			
			$r = $po[$c];
            $quantity 		= $r['quantity'];
            $stock_id		= $r['stock_id'];
            $stock 			= $r['stock'];
            $stockcode		= $r['stockcode'];
            $unit			= ($r['_type'] == "M")?$r['unit']:$r['_unit'];
            $cost			= $r['cost'];
            $amount			= $r['amount'];
            $details		= $r['details'];
            
            
			
			if($i > 26){
				echo "<tr><td colspan='5' style='border-bottom:1px solid #000;'></td></tr>
						</table>";	
				$page_break = 1;
				break;
			}
			
			if($c == (count($po) -1 )){
				$exit = 1;	
			}
			
			$total_quantity += $quantity;
            $total_items++;
            $totalamount += $amount;
            
        ?>
            <tr>
                <td><?=$stock?> <?php if($details){ echo "($details)"; }?> </td>
                <td class="align-right"><?=$quantity?></td>
                <td><?=$unit?></td>
                <td class="align-right"><?=number_format($cost,4,'.',',')?></td>
                <td class="align-right"><?=number_format($amount,4,'.',',')?></td>
            </tr>
        <?php
		}
		if(!$exit){
			continue;				
		}
        ?>
        
        <?php
        if( !empty( $remarks ) ) :
        echo '<tr>';
        echo '<td style="border-right: 1px solid #000000;">'.$remarks.'</td>';
        echo '<td style="border-right: 1px solid #000000;" align=right>&nbsp;</td>';
        echo '<td style="border-right: 1px solid #000000;" align=right>&nbsp;</td>';	
        echo '<td style="border-right: 1px solid #000000;" align=right>&nbsp;</td>';	
        echo '<td style="border-right: 1px solid #000000;" align=right>&nbsp;</td>';	
        echo '</tr>';
        endif;
        
        echo '<tr>';
        echo '<td style="border-right: 1px solid #000000;">********** Nothing Follows **********</td>';
        echo '<td style="border-right: 1px solid #000000;" align=right>&nbsp;</td>';
        echo '<td style="border-right: 1px solid #000000;" align=right>&nbsp;</td>';	
        echo '<td style="border-right: 1px solid #000000;" align=right>&nbsp;</td>';	
        echo '<td style="border-right: 1px solid #000000;" align=right>&nbsp;</td>';	
        echo '</tr>';
        
        ?>
                                       
        <?php
        for($newi=$i;$newi<=26;$newi++) {
            
            if( $newi == 26 ){
                echo '<tr>';
                echo '<td style="border-right: 1px solid #000000;">Total Items : <b>'.$total_items.'</b></td>';
                echo '<td style="border-right: 1px solid #000000;" align=right>&nbsp;</td>';
                echo '<td style="border-right: 1px solid #000000;" align=right>&nbsp;</td>';	
                echo '<td style="border-right: 1px solid #000000;" align=right>&nbsp;</td>';	
                echo '<td style="border-right: 1px solid #000000;" align=right>&nbsp;</td>';	
                echo '</tr>';
            } else {
                echo '<tr>';
                echo '<td style="border-right: 1px solid #000000;">&nbsp;</td>';
                echo '<td style="border-right: 1px solid #000000;" align=right>&nbsp;</td>';
                echo '<td style="border-right: 1px solid #000000;" align=right>&nbsp;</td>';	
                echo '<td style="border-right: 1px solid #000000;" align=right>&nbsp;</td>';	
                echo '<td style="border-right: 1px solid #000000;" align=right>&nbsp;</td>';	
                echo '</tr>';
            }
        }
        ?>
        </table>
		<?php break; }#END OF WHILE ?>      
        <table class="content">
        <?php
            $vatable_amount = $totalamount / ( 1 + ($vat / 100));
            $vatable_display = ($vat > 0)?$vatable_amount:0;
            $vat_amount = $vatable_amount * ( $vat / 100 );
            $wtax_amount = $vatable_amount * ($wtax / 100);
            $total_net_amount = ($vatable_amount + $vat_amount) - $wtax_amount;
        
            $n->setNumber($totalamount);
            $words =  $n->num_words().$n->appendDecimal();
            $vat = $totalamount - ($totalamount / 1.12)
        ?>
        <tr class="summary">
            <td style="border-bottom:1px solid #000;  border-right:none; font-weight:bold;"><?=$words?></td>
            <td colspan="2" style=" width:20%; border-top:1px solid #000; border-bottom:1px solid #000; border-right:none;"><strong>SUBTOTAL :</strong></td>
            <td style="width:10%; border-left:none; border-top:1px solid #000; border-bottom:1px solid #000; text-align:right; "><strong>PESO</strong></td>
            <td class="align-right" style="width:10%; border:1px solid #000; font-weight:bold;"><?=number_format($totalamount,2,'.',',')?></td>
        </tr>
        <tr class="summary">
            <td class="no-border align-right" style="border:none;"></td>
            <td colspan="2" style="border-top:1px solid #000; border-bottom:1px solid #000; border-right:none;"><strong>VATABLE:</strong></td>
            <td style="border-left:none; border-top:1px solid #000; border-bottom:1px solid #000; text-align:right; "><strong>PESO</strong></td>
            <td class="align-right" style="border:1px solid #000; font-weight:bold;"><?=number_format($vatable_display,2,'.',',')?></td>
        </tr>
        <tr class="summary">
            <td class="no-border align-right" style="border:none;"></td>
            <td colspan="2" style="border-top:1px solid #000; border-bottom:1px solid #000; border-right:none;"><strong>VAT:</strong></td>
            <td style="border-left:none; border-top:1px solid #000; border-bottom:1px solid #000; text-align:right; "><strong>PESO</strong></td>
            <td class="align-right" style="border:1px solid #000; font-weight:bold;"><?=number_format($vat_amount,2,'.',',')?></td>
        </tr>
        <tr class="summary">
            <td class="no-border align-right" style="border:none;"></td>
            <td colspan="2" style="border-top:1px solid #000; border-bottom:1px solid #000; border-right:none;"><strong>WTAX :</strong></td>
            <td style="border-left:none; border-top:1px solid #000; border-bottom:1px solid #000; text-align:right; "><strong>PESO</strong></td>
            <td class="align-right" style="border:1px solid #000; font-weight:bold;"><?=number_format($wtax_amount,2,'.',',')?></td>
        </tr>
        <tr class="summary">
            <td class="no-border align-right" style="border:none;"></td>
            <td colspan="2" style="border-top:1px solid #000; border-bottom:1px solid #000; border-right:none;"><strong>NET AMOUNT :</strong></td>
            <td style="border-left:none; border-top:1px solid #000; border-bottom:1px solid #000;  text-align:right;"><strong>PESO</strong></td>
            <td class="align-right" style="border:1px solid #000; font-weight:bold;"><?=number_format($total_net_amount,2,'.',',')?></td>
        </tr>
    </table>
    
    <table cellspacing="0" cellpadding="5" align="center" width="98%" style="border:none; text-align:center; margin-top:10px;" class="summary">
        <tr>
            <td>Prepared By:<p>
                <input type="text" class="line_bottom" /><br><?=$options->getUserName($user_id);?></p></td>
            <td>Checked By:<p>
                <input type="text" class="line_bottom" /><br>Liza Marie Garcia</p></td>
            <td>Approved By:<p>
                <input type="text" class="line_bottom" /><br>R. Yanson, Jr./M. Yanson</p></td>
        </tr>
    </table>
    
    <?php if(!empty($note)){ ?>
    <div>
    	Note: <?=$note?>
    </div>
    <?php } ?>
    

<div class="page-break"></div>
</body>
</html>
