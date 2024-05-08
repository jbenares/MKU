<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
	
	$invadjust_header_id=$_REQUEST[id];
	
	function getDetails($invadjust_header_id){
		$query="
				select
					quantity,
					d.stock_id,
					p.stock,
					p.stockcode,
					unit,
					d.cost,
					d.amount,
					p.kg
				from
					invadjust_detail as d, productmaster as p
				where
					d.stock_id = p.stock_id
				and
					invadjust_header_id='$invadjust_header_id'
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
			$tmp['kg']				= $r['kg'];
			$a[] = $tmp;
		}
		return $a;	
	}
	
	$rr = getDetails($invadjust_header_id);
	
	
	$query="
		select
			  *
		 from
			  invadjust_header as h,
			  projects as p
		 where
			h.project_id = p.project_id
		and
			invadjust_header_id = '$invadjust_header_id'
	";
	$result=mysql_query($query);
	$r=mysql_fetch_assoc($result);
	$user_id		= $r['user_id'];
	$invadjust_header_id_pad	= str_pad($invadjust_header_id,7,0,STR_PAD_LEFT);
	
	$project_id		= $r['project_id'];
	$project_name	= $r['project_name'];
	$date			= $r['date'];
	
	$work_category_id 		= $r['work_category_id'];
	$sub_work_category_id 	= $r['sub_work_category_id'];
	
	$remarks = $r['remarks'];
	
	$work_category = $options->attr_workcategory($work_category_id,'work');
	$sub_work_category = $options->attr_workcategory($sub_work_category_id,'work');



	

	
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
</head>
<body>
<div class="container">
	
    
    <?php 
	$c = 0;
	$page_beak_content = "<div style='page-break-after:always;'></div>";
	$exit = 0;
	$page_break = 0;
	$items = 24;
	while(1){ 
		if($page_break) echo $page_beak_content;
		$page_break = 0;
	?>
	<?php
        require("form_heading.php");
    ?>

    <div style="text-align:right; font-weight:bolder;">
        INV ADJ #. : <?=str_pad($invadjust_header_id,7,0,STR_PAD_LEFT)?><br />
    </div>   
    <div style="text-align:center; font-size:14px;">
        INVENTORY ADJUSTMENT REPORT
    </div>
    <div class="header" style="margin-bottom:10px;">
        <table style="width:100%;">
            <tr>
                <td width="11%">Date:</td>
                <td width="42%" style="border-bottom:1px solid #000;"><?=date("F j, Y",strtotime($date))?></td>
                
                <td width="9%">Remarks:</td>
                <td width="38%" style="border-bottom:1px solid #000;"><?=$remarks?></td>
            </tr>
            <tr>
              <td>Project / Section:</td>
              <td style="border-bottom:1px solid #000;"><?=$project_name?></td>
              
              <td>Scope of Work:</td>
              <td style="border-bottom:1px solid #000;"><?=$scope_of_work." | ".$work_category." | ".$sub_work_category?></td>
            </tr>
           
        </table>
    </div><!--End of header-->
    <table cellspacing="3" class="content">
        <tr>
            <th style="width:5%;">Qty</th>
            <th style="width:5%;">Unit</th>
            
            <th style="width:5%;">kg</th>
            <th style="width:5%;">kg/pc</th>
            
            <th>Item Description </th>
            <th style="width:8%;">Inv./DR #</th>
            <th style="width:5%;">U.Price</th>
            <th style="width:5%;">Amount</th>
            
        </tr>
        <?php if(empty($rr)){ echo "</table>"; break; } ?>
        <?php
        $totalamount=0;
        $total_quantity = 0;
        $total_items = 0;
		
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
            $totalamount += $r['amount'];	
			
			if($i > $items){
				echo "<tr><td colspan='6' style='border-bottom:1px solid #000;'></td></tr>
						</table>";	
				$page_break = 1;
				break;
			}
			
			if($c == (count($rr) -1 )){
				$exit = 1;	
			}			
            
        ?>
            <tr>
                <td><div align="right"><?=$r[quantity]?></div></td>
                <td><?=$unit?></td>
                
                <td><div align="right"><?=number_format($r['kg'],2,'.',',')?></div></td>
                <td><div align="right"><?=number_format($r['kg'] * $r['quantity'],2,'.',',')?></div></td>
                
                <td><?=$stock?></td>
                <td><?=$invoice?></td>
                <td style="text-align:right;" nowrap="nowrap">P<?=number_format($cost,2,'.',',')?></td>
                <td style="text-align:right;" nowrap="nowrap">P<?=number_format($amount,2,'.',',')?></td>
            </tr>
        <?php 
		} 
		
		if(!$exit){
			continue;				
		}
		?>   
        
        <?php
        echo '<tr>';
        echo '<td>&nbsp;</td>';
        echo '<td>&nbsp;</td>';
		echo '<td>&nbsp;</td>';
        echo '<td>&nbsp;</td>';
        echo '<td>********** Nothing Follows **********</td>';
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
			echo '</tr>';
		}
        ?>
   	</table>
    <?php  break; }  ?>
    <table class="content">
        <tr>
            <td style="text-align:right; border:1px solid #000; font-weight:bold; ">
                Total Amount 
            </td>
            <td style="width:10%; border:1px solid #000; font-weight:bold; text-align:right;">P<?=number_format($totalamount,2,'.',',')?></td>
        </tr>
    </table>
    
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
</body>
</html>