<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	require_once(dirname(__FILE__).'/library/lib.php');
	$options=new options();	
	
	$transfer_header_id = $_REQUEST[id];
	
	function getTSDetails($transfer_header_id){
		$query="
				select
					quantity,
					d.stock_id,
					p.stock,
					p.stockcode,
					unit,
					d.price,
					d.amount,
					kg
				from
					transfer_detail as d, productmaster as p
				where
					d.stock_id = p.stock_id
				and
					transfer_header_id='$transfer_header_id'
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
            $tmp['price']			= $r['price'];
            $tmp['amount']			= $r['amount'];
			$tmp['kg']			= $r['kg'];
			$a[] = $tmp;
		}
		return $a;	
	}
	
	$ts = getTSDetails($transfer_header_id);
	
	$query="
		select
			  *
		 from
			  transfer_header as h,
			  projects as p
		 where
			h.project_id = p.project_id
		and
			transfer_header_id = '$transfer_header_id'
	";
	$result=mysql_query($query);
	$r = $aVal = mysql_fetch_assoc($result);
	$user_id		= $r['user_id'];
	$project_id		= $r['project_id'];
	$date			= $r['date'];
	
	$project_name	= $r['project_name'];
	$work_category_id 		= $r['work_category_id'];
	$sub_work_category_id 	= $r['sub_work_category_id'];
	$scope_of_work 			= $r['scope_of_work'];
	
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
<style type="text/css">
*{
	font-family: "Times New Roman";
	font-size:11px;
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
</style>
</head>
<body>
<div class="container">
	<?php 
	$totalamount       = 0;
	$c                 = 0;
	$page_beak_content = "<div style='page-break-after:always;'></div>";
	$exit              = 0;
	$page_break        = 0;
	$items             = 23;
	$t_quantity        = $t_kg = 0;
	$total_items       = 0;
	while(1){ 
		if($page_break) echo $page_beak_content;
		$page_break = 0;
	?>
    	<?php require("form_heading_ieee.php"); ?>
    
	
        <div style="text-align:right; font-weight:bolder;">
            TRANSMITTAL #. : <?=str_pad($transfer_header_id,7,0,STR_PAD_LEFT)?><br />
        </div>   
        <div style="text-align:center; font-size:15px; font-weight:bold;">
            TRANSMITTAL SLIP
        </div>
        <div class="header" style="margin-bottom:10px;">
        	<table style="width:100%;">
            <tr>
                <td width="7%">Date:</td>
                <td width="27%" style="border-bottom:1px solid #000;"><?=date("F j, Y",strtotime($date))?></td>
            </tr>
            <tr>
                <td width="7%">From Project / Location:</td>
                <td width="27%" style="border-bottom:1px solid #000;"><?=lib::getAttribute('projects','project_id',$aVal['from_project_id'],'project_name')?></td>
            </tr>
            <tr>
              <td>To Project / Location:</td>
              <td style="border-bottom:1px solid #000;"><?="$project_name | $work_category | $sub_work_category " ?></td>              
            </tr>
           
        </table>
     	</div><!--End of header-->
        
        <table cellspacing="0" cellpadding="3" class="content">
            <tr>
                <th style="width:5%; text-align:right;">Qty</th>
                <th  style="width:5%;">Unit</th>
                
                <th style="width:5%; text-align:right;">kg/pc</th>
                <th style="width:5%; text-align:right;">total kg</th>
                
                <th>Item Description </th>
                <th style="width:10%;">U.Price</th>
                <th style="width:10%;">Amount</th>
                
            </tr>
            <?php if(empty($ts)){ echo "</table>"; break; } ?>
			<?php
            $i=0;
            for( ; $c < count($ts) ; $c++ ){
				$i++;
				$r = $ts[$c];
                $quantity	= $r['quantity'];
                $unit		= $r['unit'];
                $price		= $r['price'];
                $stock		= $r['stock'];
                $amount		= $r['amount'];
                
				if($i > $items){
					echo "<tr><td colspan='6' style='border-bottom:1px solid #000;'></td></tr>
							</table>";	
					$page_break = 1;
					break;
				}
				
				if($c == (count($ts) -1 )){
					$exit = 1;	
				}	

				$totalamount+=$r['amount'];						
				$t_kg += $r['kg'] * $r['quantity'];
				$t_quantity += $r['quantity'];
				
            ?>
                <tr>
                    <td><div align="right"><?=$r[quantity]?></div></td>
                    <td><?=$unit?></td>
                    
                    <td><div align="right"><?=number_format($r['kg'],2,'.',',')?></div></td>
                    <td><div align="right"><?=number_format($r['kg'] * $r['quantity'],2,'.',',')?></div></td>
                    
                    <td><?=$stock?></td>
                    <td><div align="right"><?=number_format($price,2,'.',',')?></div></td>
                    <td><div align="right"><?=number_format($amount,2,'.',',')?></div></td>
                </tr>
             <?php 
			} 
			
			if(!$exit){
				continue;				
			}
			
            echo '<tr>';
            echo '<td>&nbsp;</td>';
            echo '<td>&nbsp;</td>';
			echo '<td>&nbsp;</td>';
            echo '<td>&nbsp;</td>';
            echo '<td>********** Nothing Follows **********</td>';
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
				echo '</tr>';
			}
            ?>
        </table>
        <?php  break; }  ?>
      	<table class="content">
            <tr>
                <td colspan="5" style="text-align:right; border:1px solid #000;">
                	Total Quantity > <span style="padding:0px 5px; font-weight:bolder; text-align:right;"><?=number_format($t_quantity,2,'.',',')?></span> <br />
                	Total KG > <span style="padding:0px 5px; font-weight:bolder; text-align:right;"><?=number_format($t_kg,2,'.',',')?></span> <br />
                    Total > <span style="padding:0px 5px; font-weight:bolder; text-align:right;"><?=number_format($totalamount,2,'.',',')?></span> <br />
                </td>
            </tr>
        </table>
        
        <table cellspacing="0" cellpadding="5" align="center" width="98%" style="border:none; text-align:center; margin-top:10px;" class="summary">
            <tr>
                <td>Endorsed & Encoded by:<p>
                    <input type="text" class="line_bottom" /><br>Central Warehouse</p></td>
                <td>Noted by:<p>
                    <input type="text" class="line_bottom" /><br>MCD Head</p></td>
                <td>Received & Checked by:<p>
                    <!-- <input type="text" class="line_bottom" /><br><?=$options->getUserName($user_id);?></p></td> -->
                    <input type="text" class="line_bottom" /><br>Project Warehouseman</p></td>
            </tr>
        </table>
            
    </div><!--End of Form-->
    

  


</div>
<div class="divFooter">
    F-WHS-004b<br>
    Rev. 0 10/07/13
</div>
</body>
</html>