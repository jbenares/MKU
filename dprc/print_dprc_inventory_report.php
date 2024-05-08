<?php
	require_once('../my_Classes/options.class.php');
	include_once("../conf/ucs.conf.php");
	require_once("dprc_options.class.php");
	
	$options=new options();	
	$subd_id	= $_REQUEST['subd_id'];	
	
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
				*
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
<title>DPRC INVENTORY REPORT</title>
<script>

function printPage() { print(); } //Must be present for Iframe printing
</script>
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
	.table-content tr:last-child td{
		border-top:2px solid #000;
		border-bottom:2px solid #000;	
		font-weight:bold;
	}
</style>
</head>
<body>
<div class="container">
     <div><!--Start of Form-->
     	
     	<div style="font-weight:bolder; border-bottom:1px solid #000; margin-bottom:15px;">
        	INVENTORY OF PROPERTIES : <?=$options->getAttribute('subd','subd_id',$subd_id,'subd')?><br />
            <span style="float:right;"><?=date("F j, Y H:i:s")?></span> 
        </div>           
        <div class="content" style="">
        	<table class="table-content">
            	<tr>
                	<td>SUBDIVISION</td>
                    <td>MODEL</td>
                    <td>PHASE</td>
                    <td>BLOCK</td>
                    <td>LOT</td>
                    <td style="text-align:right;">LOT AREA</td>
                    <td style="text-align:right;">FLOOR AREA</td>
                    <td>AVAILABLE</td>
                    <td>APPLICATION</td>
                    <td>OUTBAL</td>
                    <td>NET LOAN</td>
                </tr>
                <?php
				$sql = "
					select
						*
					from
						subd as s, model as m, dprc_inventory as d
					where
						s.subd_id = d.subd_id
					and
						m.model_id = d.model_id
					and
						d.subd_id = '$subd_id'
					order by
						phase asc, block asc, lot asc
				";
			  
				
				$result = mysql_query($sql) or die(mysql_error());
				$records = mysql_num_rows($result);
				$total_amount = 0;

				$t_lot_area = $t_floor_area = $t_outbal = $t_net_loan = 0;
				$prev_phase = $prev_block = null;

				$is_first = true;
				while($r = mysql_fetch_assoc($result)){

					$outstanding_balance = getOutbal($r['application_id']);
					$net_loan            = $options->getAttribute('application','application_id',$r['application_id'],'net_loan');


					if( $is_first ){
						$prev_phase = $r['inv_phase'];
						$prev_block = $r['inv_block'];

						$t_lot_area   += $r['inv_lot_area'];
						$t_floor_area += $r['inv_floor_area'];
						$t_outbal     += $outstanding_balance;
						$t_net_loan   += $net_loan;
						$is_first = false;
					}

					if( $prev_phase != $r['inv_phase'] && $prev_block != $r['inv_block'] ){
						/*display totals here*/
						echo '<tr>';
						echo '<td style="font-weight:bold; border-top:1px solid #000;"></td>';	
						echo '<td style="font-weight:bold; border-top:1px solid #000;"></td>';	
						echo '<td style="font-weight:bold; border-top:1px solid #000;"></td>';	
						echo '<td style="font-weight:bold; border-top:1px solid #000;"></td>';	
						echo '<td style="font-weight:bold; border-top:1px solid #000;"></td>';	
						echo '<td style="font-weight:bold; border-top:1px solid #000; text-align:right;">'.number_format($t_lot_area,4).'</td>';	
						echo '<td style="font-weight:bold; border-top:1px solid #000; text-align:right;">'.number_format($t_floor_area,4).'</td>';	
						echo '<td style="font-weight:bold; border-top:1px solid #000;"></td>';	
						echo '<td style="font-weight:bold; border-top:1px solid #000;"></td>';	
						echo '<td style="font-weight:bold; border-top:1px solid #000; text-align:right;">'.number_format($t_outbal,2).'</td>';	
						echo '<td style="font-weight:bold; border-top:1px solid #000; text-align:right;">'.number_format($t_net_loan,2).'</td>';	
						echo '</tr>';

						/*reset totals here*/
						$prev_phase = $r['inv_phase'];
						$prev_block = $r['inv_block'];

						$t_lot_area = $t_floor_area = $t_outbal = $t_net_loan = 0;
						$t_lot_area   += $r['inv_lot_area'];
						$t_floor_area += $r['inv_floor_area'];
						$t_outbal     += $outstanding_balance;
						$t_net_loan   += $net_loan;
					} else {
						/*add to subtotals*/
						$t_lot_area   += $r['inv_lot_area'];
						$t_floor_area += $r['inv_floor_area'];
						$t_outbal     += $outstanding_balance;
						$t_net_loan   += $net_loan;


					}


					$used = ($r['application_id']) ? "NO" : "YES";
					$customer = ($r['application_id']) ? getCustomerName($r['application_id']) : "";				
						
					echo '<tr>';
					echo '<td>'."$r[subd]".'</td>';	
					echo '<td>'."$r[model]".'</td>';	
					echo '<td>'."$r[inv_phase]".'</td>';	
					echo '<td>'."$r[inv_block]".'</td>';	
					echo '<td>'."$r[inv_lot]".'</td>';	
					echo '<td style="text-align:right;">'."$r[inv_lot_area]".'</td>';	
					echo '<td style="text-align:right;">'."$r[inv_floor_area]".'</td>';	
					echo '<td>'."$used".'</td>';	
					echo '<td>'.htmlentities($customer).'</td>';	
					echo '<td style="text-align:right;">'.number_format($outstanding_balance,2).'</td>';	
					echo '<td style="text-align:right;">'.number_format($net_loan,2).'</td>';	
					echo '</tr>';
				}

				/*display subtotal at the end*/
				echo '<tr>';
				echo '<td style="font-weight:bold; border-top:1px solid #000;"></td>';	
				echo '<td style="font-weight:bold; border-top:1px solid #000;"></td>';	
				echo '<td style="font-weight:bold; border-top:1px solid #000;"></td>';	
				echo '<td style="font-weight:bold; border-top:1px solid #000;"></td>';	
				echo '<td style="font-weight:bold; border-top:1px solid #000;"></td>';	
				echo '<td style="font-weight:bold; border-top:1px solid #000; text-align:right;">'.number_format($t_lot_area,4).'</td>';	
				echo '<td style="font-weight:bold; border-top:1px solid #000; text-align:right;">'.number_format($t_floor_area,4).'</td>';	
				echo '<td style="font-weight:bold; border-top:1px solid #000;"></td>';	
				echo '<td style="font-weight:bold; border-top:1px solid #000;"></td>';	
				echo '<td style="font-weight:bold; border-top:1px solid #000; text-align:right;">'.number_format($t_outbal,2).'</td>';	
				echo '<td style="font-weight:bold; border-top:1px solid #000; text-align:right;">'.number_format($t_net_loan,2).'</td>';	
				echo '</tr>';
               	?>
                <tr>
                	<td>Records : <?=$records?></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </table>
        </div><!--End of content-->
        <!--<table cellspacing="0" cellpadding="5" align="center" width="98%" style="border:none; text-align:center; margin-top:10px;" class="summary">
            <tr>
                <td>Prepared by:<p>
                    <input type="text" class="line_bottom" /><br>Collection/Cashier</p></td>
                <td>Checked by:<p>
                    <input type="text" class="line_bottom" /><br>Database Administrator</p></td>
                <td>Noted by:<p>
                    <input type="text" class="line_bottom" /><br>Database Administrator</p></td>
            </tr>
        </table> -->
        <?php include_once("print_dprc_signatories.php") ?>
    </div><!--End of Form-->
</div>
</body>
</html>