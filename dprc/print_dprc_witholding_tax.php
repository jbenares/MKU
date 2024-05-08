<?php
	require_once('../my_Classes/options.class.php');
	include_once("../conf/ucs.conf.php");
	require_once("dprc_options.class.php");
	
	
	
	$options=new options();	
	$subd_id	= $_REQUEST['subd_id'];	
	$query		= $_REQUEST['query']; //ajax
	
	if(!empty($query) && $query == "check"){
		$id = $_REQUEST['id'];
		
		mysql_query("
			insert into dprc_app_wtax values ('$id',now())
		") or die(mysql_error());
		
		echo $id;
		exit;
	}
	
	function getCustomerName($application_id){
		$result = mysql_query("	
			select
				*
			from
				application as a, customer as c
			where
				a.customer_id = c.customer_id
			and
				a.application_id = '$application_id'
		") or die(mysql_error());
		$r = mysql_fetch_assoc($result);
		
		return "$r[customer_last_name], $r[customer_first_name] $r[customer_middle_name] $r[customer_appel]";
	}
	
	function getOutbal($application_id){
		$result = mysql_query("
			select 
				min(outbal) as outbal
			from
				dprc_payment as p, dprc_ledger as l
			where
				p.dprc_payment_id = l.dprc_payment_id
			and
				p.application_id = '$application_id'
			order by
				dprc_ledger_id desc
			limit 0,1
		") or die(mysql_error());
		$r = mysql_fetch_assoc($result);
		return $r['outbal'];
	}
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>DPRC WITHOLDING TAX REPORT</title>
<script>
function printPage() { print(); } //Must be present for Iframe printing
</script>
<script type="text/javascript" src="../scripts/jquery.js"></script>
<link rel="stylesheet" type="text/css" href="../css/dprc_print.css"/>
<style type="text/css">
	.table-content{
		margin-top:5px;
		width:100%;	
	}
	.table-content tr:nth-child(1) td{
		border-top:1px solid #000;
		border-bottom:1px solid #000;
	}
	.table-content td{
		padding:1px 5px;	
	}
	.subtotal td{
		border-top:1px solid #000;	
	}
/*	.table-content tr:last-child td{
		border-top:2px solid #000;
		border-bottom:2px solid #000;	
		font-weight:bold;
	}*/
</style>
</head>
<body>
<div class="container">
     <div><!--Start of Form-->
     	
     	<div style="font-weight:bolder; border-bottom:1px solid #000; margin-bottom:15px;">
        	WITHOLDING TAX REPORT<br />
            <span style="float:right;"><?=date("F j, Y H:i:s")?></span> 
        </div>           
        <div class="content" style="">
        	<table class="table-content">
            	<tr>
                	<td width="10"></td>
                	<td>APPLICATION #</td>
                    <td>CUSTOMER</td>
                    <td>PHASE</td>
                    <td>BLOCK</td>
                    <td>LOT</td>
                    <td>LOT AREA</td>
                    <td>FLOOR AREA</td>
                    <td style="text-align:right;">LOAN</td>
                    <td style="text-align:right;">TOTAL PRINCIPAL</td>
                </tr>
                <?php
				$sql = "
					select
						a.application_id, customer_last_name,customer_first_name,phase,block,lot,loan_value,getSumPrincipal(a.application_id) as total_princ, lot_area, floor_area
					from
						application as a, customer as c
					where
						a.customer_id = c.customer_id
					and date_cancelled = '0000-00-00'
					and a.application_id not in (select application_id from dprc_app_wtax)
					having ((loan_value * 0.25) <= total_princ or total_princ = '0')
					order by customer_last_name asc, customer_first_name asc, phase asc, lot asc, block asc
				";
			  
				
				$result = mysql_query($sql) or die(mysql_error());
				$records = mysql_num_rows($result);
				$total_amount = 0;
				$t_loan_value = $t_principal = 0;
				while($r = mysql_fetch_assoc($result)){
											
					$t_loan_value 	+= $r['loan_value'];
					$t_principal	+= $r['total_princ'];
					echo '<tr>';
					echo '<td> <img src="../images/page_tick.gif" class="tick" data-id="'.$r['application_id'].'"> </td>';	
					echo '<td>'.str_pad($r['application_id'],7,0,STR_PAD_LEFT).'</td>';	
					echo '<td>'.htmlentities("$r[customer_last_name], $r[customer_first_name]").'</td>';	
					echo '<td>'."$r[phase]".'</td>';	
					echo '<td>'."$r[block]".'</td>';	
					echo '<td>'."$r[lot]".'</td>';	
					echo '<td>'."$r[lot_area]".'</td>';	
					echo '<td>'."$r[floor_area]".'</td>';	
					echo '<td style="text-align:right;">'.number_format($r['loan_value'],2).'</td>';	
					echo '<td style="text-align:right;">'.number_format($r['total_princ'],2).'</td>';	
					echo '</tr>';
				}
				
				echo '<tr class="subtotal">';
				echo '<td></td>';	
				echo '<td></td>';	
				echo '<td></td>';	
				echo '<td></td>';	
				echo '<td></td>';	
				echo '<td></td>';	
				echo '<td></td>';	
				echo '<td></td>';	
				echo '<td style="text-align:right; font-weight:bold;">'.number_format($t_loan_value,2).'</td>';	
				echo '<td style="text-align:right; font-weight:bold;">'.number_format($t_principal,2).'</td>';	
				echo '</tr>';
               	?>
            </table>
        </div><!--End of content-->
        <table cellspacing="0" cellpadding="5" align="center" width="98%" style="border:none; text-align:center; margin-top:10px;" class="summary">
            <tr>
                <td>Prepared by:<p>
                    <input type="text" class="line_bottom" /><br>Collection/Cashier</p></td>
                <td>Checked by:<p>
                    <input type="text" class="line_bottom" /><br>Database Administrator</p></td>
                <td>Noted by:<p>
                    <input type="text" class="line_bottom" /><br>Database Administrator</p></td>
            </tr>
        </table>
    </div><!--End of Form-->
</div>
</body>
</html>
<script type="text/javascript">

jQuery(function(){
	jQuery(".tick").click(function(){
		if(confirm("Would you like to confirm?")){
			
			jQuery(this).parent().parent().hide(300);
			
			var form_data={
				query:"check",
				id:jQuery(this).data("id")
			};
			
			jQuery.ajax({
				url: "print_dprc_witholding_tax.php",
				data: form_data,
				type: 'POST',
				success:
					function(html){
						//alert(html);
					}
			});	
		}
	});
});

</script>