<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
	$accountable_id	= $_REQUEST['id'];
	$account_id = $options->getAttribute('accountables','accountable_id',$accountable_id,'account_id');
	$account	= $options->getAttribute('account','account_id',$account_id,'account');
	$date	 	= $options->getAttribute('accountables','accountable_id',$accountable_id,'date');
	
	function getAccountables($accountable_id){
		$options 	= new options();
		$account_id = $options->getAttribute('accountables','accountable_id',$accountable_id,'account_id');
		$date	 	= $options->getAttribute('accountables','accountable_id',$accountable_id,'date');
		
		$result = mysql_query("
			select 
				p.stock , p.stock_id, d.rr_detail_id, account, qty, proj.project_name, accountable_id,unit,details, a.date,d.cost, asset_code, serial_no, item_status
			from
				accountables as a, rr_detail as d, productmaster as p, account as ac, projects as proj
			where
				a.rr_detail_id  = d.rr_detail_id
			and d.stock_id = p.stock_id
			and a.account_id = ac.account_id
			and proj.project_id = a.project_id
			and date = '$date'
			and a.account_id = '$account_id'
		") or die(mysql_error());
		
		$a = array();
		while($r = mysql_fetch_assoc($result)){
			$a[] = $r;
			$date 			= $r['date'];
			$stock 			= $r['stock'];
			$stock_id 		= $r['stock_id'];
			$account 		= $r['account'];
			$qty			= $r['qty'];
			$project_name 	= $r['project_name'];
			$unit 			= $r['unit'];
			$details 		= $r['details'];
			$cost			= $r['cost'];
			$serial_no		= $r['serial_no'];
			$asset_code		= $r['asset_code'];
			$item_status	= $r['item_status'];
		}
		return $a;
	}
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>ACCOUNTABILITY RECEIPT</title>
<script>
function printPage() { print(); } //Must be present for Iframe printing
</script>
<style type="text/css">
body
{
	size: letter portrait;
	padding:0px;
	font-family:"Times New Roman";
	font-size:14px
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
.line_bottom {
	border-bottom-width: 1px;
	border-bottom-style: solid;
	border-bottom-color: #000000;
	border-left: 0px;
	border-right: 0px;
	border-top: 0px;
	width:120px;
}
table td{
	vertical-align:top;	
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
	<?php require("form_heading_ieee.php") ?>
	<div style="text-align:center; font-weight:bold; text-decoration:underline; font-size:15px;">
    	ACCOUNTABILITY RECEIPT
    </div>
    
    <div style="text-align:right; margin-top:20px;" class="clearfix">
    	<table style="float:right;">
        	<!--<tr>
            	<td>AR # :</td>
                <td><?=str_pad($accountable_id,7,0,STR_PAD_LEFT)?></td>
            </tr> -->
            <tr>
            	<td>DATE :</td>
                <td><?=date("m-d-Y",strtotime($date))?></td>
            </tr>
        </table>
    </div>
    
    <div style="margin-top:10px;">
    	I, <?=$account?> of <!--<input type="text" style="border:none; width:400px;" /> --><?=$project_name?>  hereby received the following item(s) from <?=$title?> :
    </div>
    
    <div style="border-top:1px solid #000;">
    	<table style="margin:auto; width:100%;">
        	<tr>
            	<th><span style="border-bottom:1px solid #000;">AR#</span></th>
				<th><span style="border-bottom:1px solid #000;">QTY</span></th>
                <th><span style="border-bottom:1px solid #000;">U/M</span></th>
                <th><span style="border-bottom:1px solid #000;">AMOUNT</span></th>
                <th><span style="border-bottom:1px solid #000;">ITEM DESCRIPTION</span></th>
                <th><span style="border-bottom:1px solid #000;">DETAILS</span></th>
                <th><span style="border-bottom:1px solid #000;">ASSET CODE</span></th>
                <th><span style="border-bottom:1px solid #000;">SERIAL NO.</span></th>
                <th><span style="border-bottom:1px solid #000;">ITEM STATUS <!--<br />(On Return)!--></span></th>
            </tr>
            <?php
			if(count(getAccountables($accountable_id)) > 0):
				foreach(getAccountables($accountable_id) as $r):
					$date 			= $r['date'];
					$stock 			= $r['stock'];
					$stock_id 		= $r['stock_id'];
					$account 		= $r['account'];
					$qty			= $r['qty'];
					$project_name 	= $r['project_name'];
					$unit 			= $r['unit'];
					$details 		= $r['details'];
					$cost			= $r['cost'];
					$serial_no		= $r['serial_no'];
					$asset_code		= $r['asset_code'];
					$accountable_id = $r['accountable_id'];
            ?>
                <tr>
                	<td style="text-align:center; vertical-align:top;"><?=$accountable_id?></td>
                    <td style="text-align:center; vertical-align:top;"><?=number_format($qty,0)?></td>
                    <td style="text-align:center; vertical-align:top;"><?=$unit?></td>
                    <td style="text-align:center; vertical-align:top;"><?=number_format($cost,2,'.',',')?></td>
                    <td style="text-align:center; vertical-align:top;"><?=htmlentities($stock)?> </td>
                    <td style="text-align:center; vertical-align:top;" nowrap="nowrap"><pre style="font-family:Arial, Helvetica, sans-serif;"><?=$r['details']?></pre></td>
                    <td style="text-align:center; vertical-align:top;"><?=$asset_code?></td>
                    <td style="text-align:center; vertical-align:top;"><?=$serial_no?></td>
                    <td style="text-align:center; vertical-align:top;"><?=$r['item_status']?></td>
                </tr>
            <?php
				endforeach;
			endif;
            ?>
            <tr>
            	<td colspan="9" style="text-align:center;">***NOTHING FOLLOWS***</td>
            </tr>
        </table>
    </div>
    <div style="margin:10px 0px;">
    	That the above item(s) is/are entrusted to me under my due diligence and proper care; <br />
        That we acknowledged to have thoroughly checked and received them in good condition; <br />
        That we fully understand that the subject item(s) is/are of for Company used only and that the same should be surrendered
        upon termination/disengagement of our services from the Company;<br />
        That we are jointly Accountable for the subject item(s);<br />
		And we recognized that this/these are chargable to our Personal Account in cases of loss, damages and all costs, incidental to repairs of
        such item(s) due to my negligence.
    </div>
    <!-- <div style="text-align:center; width:60%; margin:30px auto;">
    	With our Conformity: <br />
        <input type="text" style="border:none; border-bottom:1px solid #000; margin-top:10px; width:70%;" />
    </div> -->
    
    <table cellspacing="0" cellpadding="5" align="center" style="border:none; text-align:center; margin-top:10px;" class="summary">
        <tr>
            <td>Prepared & Released by:<p>
                <input type="text" class="line_bottom" /><br>Central Warehouse</p></td>
            <td>Noted by:<p>
                <input type="text" class="line_bottom" /><br><em>MCD/Acctg Head</em></p></td>
           	<td>Checked and Received by:<p>
                <input type="text" class="line_bottom" /><br><em>Project Warehouseman</em></p></td>
            <td>Conformed by:<p>
            	<input type="text" class="line_bottom" /><br><em><?=$account?></em></p></td>
        </tr>
    </table>
    
    
    
</div>
<div class="divFooter">
    F-WHS-008 <br>
	Rev. 0 10/07/13
</div>
</body>
</html>