<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
	
	$issuance_header_id=$_REQUEST[id];
	
	function getIssuanceDetails($issuance_header_id){
		$query="
				select
					quantity,
					d.stock_id,
					p.stock,
					p.stockcode,
					unit,
					d.price,
					d.amount,
					equipment_id,
					quantity_cum,
					driverID,
					_reference,
					_unit
				from
					issuance_detail as d, productmaster as p
				where
					d.stock_id = p.stock_id
				and
					issuance_header_id='$issuance_header_id'
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
			$tmp['driverID'] 		= $r['driverID'];
			$tmp['equipment_id']	= $r['equipment_id'];
			$tmp['quantity_cum'] 	= $r['quantity_cum'];
			$tmp['_unit']			= $r['_unit'];
			$tmp['_reference']		= $r['_reference'];
			$a[] = $tmp;
		}
		return $a;	
	}
	
	$ris = getIssuanceDetails($issuance_header_id);
	
	
	$query="
		select
			  *
		 from
			  issuance_header as h,
			  projects as p
		 where
			h.project_id = p.project_id
		and
			issuance_header_id = '$issuance_header_id'
	";
	$result=mysql_query($query);
	$r=mysql_fetch_assoc($result);
	
	$project_id		= $r['project_id'];
	$project_name	= $r['project_name'];
	$date			= $r['date'];
	
	$status			= $r['status'];
	$user_id		= $r['user_id'];
	
	$work_category_id 		= $r['work_category_id'];
	$sub_work_category_id 	= $r['sub_work_category_id'];
	$scope_of_work 			= $r['scope_of_work'];
	$remarks				= $r['remarks'];
	
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
<style type="text/css">

@media print{
  .page-break{
		display:block;
		page-break-before:always;  
  }
  
  body *,.header table td,.content table td,.content table th{
		font-size:11px;   
  }
  
}
	
body
{
	padding:0px;
	font-family:"Times New Roman";
	font-size:11px;
}
.container{
	width:100%;
}

.header
{
	text-align:center;	
}

.header table, .content table
{
	width:100%;
	text-align:left;
	

}

.issuance_table{
	border-collapse:collapse;
	width:100%;
}
.issuance_table th{
	border:1px solid #000;
}
.issuance_table td{
	border-left:1px solid #000;
	border-right:1px solid #000;	
}
.issuance_table tr:last-child td{
	border-bottom:1px solid #000;	
}

hr
{
	margin:40px 0px;	
	border:1px dashed #999;

}

.clearfix:after {
	content: ".";
	display: block;
	clear: both;
	visibility: hidden;
	line-height: 0;
	height: 0;
}

.clearfix {
	display: inline-block;
}

html[xmlns] .clearfix {
	display: block;
}
 
* html .clearfix {
	height: 1%;
}

.footer td{
	border:none;
}

.align-right{
	text-align:right;	
}

.inline{
	display:inline-block;	
}

.line_bottom {
	border-bottom-width: 1px;
	border-bottom-style: solid;
	border-bottom-color: #000000;
	border-left: 0px;
	border-right: 0px;
	border-top: 0px;
	width:120px;
}
.kurit td{
	border-top:1px solid #000;
	border-bottom:1px solid #000;	
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
	$c = 0;
	$page_beak_content = "<div style='page-break-after:always;'></div>";
	$exit = 0;
	$page_break = 0;
	$items = 7;
	while(1){ 
		if($page_break) echo $page_beak_content;
		$page_break = 0;
	?>
	<?php
        require("form_heading_ieee.php");
    ?>
    <div style="text-align:right; font-weight:bolder;">
        R.I.S. # : <?=str_pad($issuance_header_id,7,0,STR_PAD_LEFT)?><br />
    </div>   
    <div style="text-align:center; font-size:15px; font-weight:bold;">
        Issuance Slip
    </div>
        
    <div class="header" style="">
        <table style="width:100%;">
            <tr>
                <td width="19%">Project / Section:</td>
                <td width="54%" style="border-bottom:1px solid #000;"><?=$project_name?></td>
                
                <td width="7%">Date:</td>
                <td width="20%" style="border-bottom:1px solid #000;"><?=date("F j, Y",strtotime($date))?></td>
            </tr>
            <tr>
              <td>Scope of Work:</td>
              <td style="border-bottom:1px solid #000;"><?=$scope_of_work." | ".$work_category." | ".$sub_work_category?></td>
            </tr>
            <tr>
              <td>Remarks:</td>
              <td style="border-bottom:1px solid #000;"><?=$remarks?></td>
            </tr>
        </table>
    </div><!--End of header-->
    <table cellspacing="0" class="issuance_table" style="margin-top:10px;" cellpadding="3">
        <tr>
            <th style="width:10%;">Reference</th>
            <th style="width:10%;">Driver</th>
            <th style="width:10%;">Equip</th>
            <th style="width:10%;">Qty</th>
            <th style="width:10%;">Unit</th>
            <th style="width:10%;">(Qty)</th>
            <th style="width:10%;">(Unit)</th>
            <th>Item Description</th>
            <th style="width:10%;">U.Price</th>
            <th style="width:10%;">Amount</th>
        </tr>
        <?php if(empty($ris)){ echo "</table>"; break; } ?>
        <?php
        $totalamount=0;
        $total_qty = 0;
		$total_qty_aggr = 0;
        $total_items = 0;
        $i=0;
        for( ; $c < count($ris) ; $c++ ){
            $i++;
			$r = $ris[$c];
            $quantity	= $r['quantity'];
            $unit		= $r['unit'];
            $stock		= $r['stock'];
            $price		= $r['price'];
            $amount		= $r['amount'];
            $driverID	= $r['driverID'];
            $quantity_cum = $r['quantity_cum'];
			$unit		= $r['_unit'];
            $totalamount += $r['amount'];		
            $_unit		= $r['_unit'];
            $equipment_id = $r['equipment_id'];			
			$_reference	= $r['_reference'];
            
            $total_qty += $r['quantity'];
            $total_qty_aggr += $r['quantity_cum'];
			
			if($i > $items){
				echo "<tr><td colspan='10' style='border-bottom:1px solid #000;'></td></tr>
						</table>";	
				$page_break = 1;
				break;
			}
			
			if($c == (count($ris) -1 )){
				$exit = 1;	
			}		
        	?>
            <tr>
                <td><?=$r['_reference']?></td>
                <td><?=$options->getAttribute('drivers','driverID',$r['driverID'],'driver_name')?></td>
                <td><?=$options->getAttribute('equipment','eqID',$r['equipment_id'],'eq_name')?>(<?=$options->getAttribute('equipment','eqID',$r['equipment_id'],'eqModel')?>)</td>
                <td><div align="right"><?=$r[quantity]?></div></td>
                <td><?=$r['unit']?></td>
                <td><div align="right"><?=$r[quantity_cum]?></div></td>
                <td><?=$r['_unit']?></td>
                <td><?=$r[stock]?></td>
                <td><div align="right">P <?=number_format($r[price],2,'.',',')?></div></td>
                <td><div align="right">P <?=number_format($r[amount],2,'.',',')?></div></td>
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
   	</table>
    <?php  break; }  ?>
    <table class="issuance_table" style="width:100%;" cellpadding="3">
        <tr>
            <td colspan="9" style="border:1px solid #000;"><div align="right">Total</div></td>
            <td style="text-align:right; font-weight:bolder; width:10%;">P <?=number_format($totalamount,2,'.',',')?></td>
        </tr>
    </table>
    
    <table cellspacing="0" cellpadding="5" align="center" width="98%" style="border:none; text-align:center; margin-top:10px;" class="summary">
        <tr>
            <td>Issued By:<p>
                <input type="text" class="line_bottom" value="<?=$options->getUserName($user_id);?>" /><br>Warehouseman</p></td>
          	<td>Approved By:<p>
                <input type="text" class="line_bottom" /><br>P.I.C / Section Head</p></td>
            <td>Received By:<p>
                <input type="text" class="line_bottom" /><br>End User</p></td>
        </tr>
    </table>
    
</div>
<div class="divFooter">
    F-WHS-003b<br>
    Rev. 0 10/07/13
</div>
</body>
</html>