<?php
	require_once('my_Classes/options.class.php');
	require_once('my_Classes/numbertowords.class.php');
	require_once('my_Classes/numtowords.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();		
	$po_header_id	= $_REQUEST['id'];
	$supplier_id	= $_REQUEST['supplier_id'];
	
	$query="
		select
				*
			from
				po_header as h, spo_detail as d, sub_spo_detail as sub
			where
				h.po_header_id = d.po_header_id
			and
				d.spo_detail_id = sub.spo_detail_id
			and
				h.po_header_id = '$po_header_id'		
	";
	$result=mysql_query($query);
	$r=mysql_fetch_assoc($result);
	
	$supplier_id			= $r['supplier_id'];
	$supplier		= $options->attr_Supplier($supplier_id,'account');	
	

	$_GLOBALS['used_detail'] = array();
	global $z;
	$z = array();

	function hasCheckIssuance($sub_apv_header_id){
		$result = mysql_query("
			select * from cv_header where status != 'C' and sub_apv_header_id = '$sub_apv_header_id'
		") or die(mysql_error());
		$bool = (mysql_num_rows($result) > 0) ? 1 : 0;
		
		return $bool;
	}
	
	function numform($num) {
		if($num==0) $num = "&nbsp;";
		else if($num < 0 ) $num = "( ".number_format(abs($num),2)." )";
		else $num = number_format($num, 2);
		
		return $num;
	}
	
	function dateform($date){
		return date("m/d/Y",strtotime($date));
	}
	

	function getSubcontractorAPV($po_header_id,$description,$sub_description,$unit,$unit_cost){
		global $z;

		$sub_description = addslashes($sub_description);
		
		$sql = "
			select 
				*
			from
				sub_apv_header as h, sub_apv_detail as d
			where
				h.sub_apv_header_id = d.sub_apv_header_id
			and status != 'C'
			and po_header_id = '$po_header_id'
			and description = '$description'
			and sub_description = '$sub_description'
			and unit = '$unit'
			and unit_cost = '$unit_cost'
		";

		if($z){
			$sql .= " and d.sub_apv_detail_id not in (".implode(",", $z).")";
		}
		

		$result = mysql_query($sql) or die(mysql_error());	
		$a = array();
		while($r = mysql_fetch_assoc($result)){
			$tmp = array();
			$z[] = $r['sub_apv_detail_id'];
			
			$tmp['sub_apv_header_id'] = $r['sub_apv_header_id'];
			$tmp['date']              = $r['date'];
			$tmp['description']       = $r['description'];
			$tmp['sub_description']   = $r['sub_description'];
			$tmp['quantity']          = $r['quantity'];
			$tmp['unit']              = $r['unit'];
			$tmp['unit_cost']         = $r['unit_cost'];
			$tmp['amount']            = $r['amount'];

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
<style type="text/css">
body{
	font-size:12px;
}
*{
	font-size:12px;	
	font-family: Arial;
}
td,th {
	text-align:left;
	padding:3px;
}
.table-content{
	width:100%;	
	border-collapse:collapse
}
.table-content th{
	border-top:1px solid #000;
	border-bottom:1px solid #000;	
}
.table-content th:nth-child(1){
	border-left:1px solid #000;	
}
.table-content th:last-child{
	border-right:1px solid #000;	
}

.table-content td{
	border-left:1px solid #000;
	border-right:1px solid #000;
}
.table-content tr:last-child td{
	border-bottom:1px solid #000;
}
.line_bottom {
	border-bottom-width: 1px;
	border-bottom-style: solid;
	border-bottom-color: #000000;
	width:150px;
	border-top-width: 0px;
	border-right-width: 0px;
	border-left-width: 0px;
	border-top-style: none;
}
</style>

</head>
<body>
	<div style="font-weight:bold;">
    	<?=$title?><br />
        SUBCONTRACTOR LABOR/MATERIALS LEDGER<br />
		<?=$supplier?>
    </div>
	<table class="table-content">
    	<tr>
        	<th>DATE</th>
        	<th>REFERENCE</th>
            <!--<th>DESC</th> -->
            <th>SUB DESC</th>
            <th style="text-align:right;">QTY</th>
            <th>UNIT</th>
            <th style="text-align:right;">U/P</th>
            <th style="text-align:right;" nowrap="nowrap">PO AMOUNT</th>
            <th style="text-align:right;">PREV PAYMENTS</th>
            <th style="text-align:right;">THIS PERIOD</th>
            <th style="text-align:right;">BALANCE</th>
        </tr>
        <?php
		$total_balance 	= $total_payments = $total_po_amount = $total_this_period = $total_prev_period = 0;
		$result = mysql_query("
			select
				*
			from
				po_header as h, spo_detail as d, sub_spo_detail as sub
			where
				h.po_header_id = d.po_header_id
			and
				d.spo_detail_id = sub.spo_detail_id
			and
				h.po_header_id = '$po_header_id'		
		") or die(mysql_error());
		while($r = mysql_fetch_assoc($result)){
		$supplier_id	= $r['supplier_id'];
		$supplier		= $options->attr_Supplier($supplier_id,'account');		
		$balance = $r['amount'];
		$total_balance += $r['amount'];
		
		$total_po_amount += $r['amount'];
        ?>
		
		
        <tr>
        	<td><?=dateform($r['date'])?></td>
	        <td>PO#<?=str_pad($r['po_header_id'],7,0,STR_PAD_LEFT)?></td>
        	<!--<td><?=$r['description']?></td> -->
            <td><?=$r['sub_description']?></td>
            <td style="width:5%; text-align:right;"><?=numform($r['quantity'])?></td>
            <td style="width:5%;"><?=$r['unit']?></td>
            <td style="width:5%; text-align:right"><?=numform($r['unit_cost'])?></td>
            <td style="width:5%; text-align:right;"><?=numform($r['amount'])?></td>
            <td style="width:5%; text-align:right;"></td>
            <td style="width:5%; text-align:right;"></td>
            <td style="width:5%; text-align:right;"></td>
        </tr>
        <?php

		$sub_apv = getSubcontractorAPV($r['po_header_id'],$r['description'],$r['sub_description'],$r['unit'],$r['unit_cost']);
		if(!empty($sub_apv)){
			foreach($sub_apv as $apv){
				$balance 		-= $apv['amount'];
				$total_balance 	-= $apv['amount'];	
				$total_payments += $apv['amount'];
		?>
		
        	<tr>
                <td><?=dateform($apv['date'])?></td>
                <td>SUB APV#<?=str_pad($apv['sub_apv_header_id'],7,0,STR_PAD_LEFT)?></td>
                <!--<td><?=$apv['description']?></td> -->
                <td><?=$apv['sub_description']?></td>
                <td style="width:5%; text-align:right;"><?=numform($apv['quantity'])?></td>
                <td style="width:5%;"><?=$apv['unit']?></td>
                <td style="width:5%; text-align:right"><?=numform($apv['unit_cost'])?></td>
                <td style="width:5%; text-align:right"></td>
                
                <?php 
				if(hasCheckIssuance($apv['sub_apv_header_id'])){ #HAS CHECK ISSUANCE - PREV PAYMENTS 
					$total_prev_period += $apv['amount'];
				?>
	                <td style="width:5%; text-align:right;"><?=numform($apv['amount'])?></td>
	                <td style="width:5%; text-align:right;"></td>
                <?php 
				}else{ 
					$total_this_period += $apv['amount'];
				?>
	                <td style="width:5%; text-align:right;"></td>
	                <td style="width:5%; text-align:right;"><?=numform($apv['amount'])?></td>
                <?php } ?>
                <td style="width:5%; text-align:right;"><?=numform($balance)?></td>
            </tr>
			
        <?php	
			}
		}
        ?>
        <?php } ?>
        <tr>
        	<td style="border-top:1px solid #000;"></td>
            <td style="border-top:1px solid #000;"></td>
            <td style="border-top:1px solid #000;"></td>
            <td style="border-top:1px solid #000;"></td>
            <td style="border-top:1px solid #000;"></td>
            <td style="border-top:1px solid #000;"></td>
            
            <td style="text-align:right; font-weight:bold; border-top:1px solid #000;"><?=numform($total_po_amount)?></td>
            <td style="text-align:right; font-weight:bold; border-top:1px solid #000;"><?=numform($total_prev_period)?></td>
            <td style="text-align:right; font-weight:bold; border-top:1px solid #000;"><?=numform($total_this_period)?></td>
           	<td style="text-align:right; font-weight:bold; border-top:1px solid #000;"><?=numform($total_balance)?></td>
        </tr>
		
    </table>
	 <table cellspacing="0" cellpadding="5" align="center" width="98%" style="border:none; text-align:center; margin-top:30px;" class="summary">
        <tr>
            <td style="text-align:left;font-size:14px;">Prepared & Checked By:<p>
                <input type="text" class="line_bottom" /><br>May M. Domingo<br>
                <!--<span style='font-size:10px;'><?=$aTrans['datetime_encoded']?></span>-->
            </p></td>
            <!--<td>Checked By:<p>
                <input type="text" class="line_bottom" /><br>Purchasing Manager</p></td>
            <td>Approved By:<p>
                <input type="text" class="line_bottom" /><br>President / G.M.</p></td>-->
        </tr>
    </table>
</body>
</html>
