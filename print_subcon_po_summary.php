<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
	$budget_header_id = $_REQUEST[budget_header_id];
	
					$sql="
						select
							  *,sum(d.price_per_unit * d.total_qty) as amount
						from
							  labor_budget as h, labor_budget_details as d
						where
							h.id = '$budget_header_id'
						and 
							h.id = d.labor_budget_id
						and
							d.is_deleted !='1'
						and
							h.status !='C'
						";
		$rs = mysql_query($sql);
		$rw = mysql_fetch_assoc($rs);
		$budget = $rw[amount];
	/*function getTotalBudget($po_header_id){
		$result = mysql_query("
			select sum(amount) as amount from spo_detail as sp,sub_spo_detail as sb where sp.po_header_id='$po_header_id' and sp.spo_detail_id=sb.spo_detail_id
		") or die(mysql_error());
		$r = mysql_fetch_assoc($result);
		return $r['amount'];
	}*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>JOB ORDER</title>
<script>
function printPage() { print(); } //Must be present for Iframe printing
</script>
<link rel="stylesheet" type="text/css" href="css/print.css"/>
</head>
<body>
<div class="container" style="letter-spacing:2px;line-spacing:1px;">
	
     <div><!--Start of Form-->
		<br/>
		<br/>
     	<center><div style="font-weight:bolder;">
        	SUBCON PO SUMMARY<br />
        </div>  </center>  
			<br/>
			<br/>
        <div class="content" style="">
        	<table border=0 cellpadding="1">
            	<tr>
					<td style="text-align:center;" width="100px"></td>
					<td style="text-align:center;" width="100px"></td>
					<td style="text-align:right;" width="100px">BUDGET AMOUNT</td>
					<td style="text-align:right;" width="100px"><b><u>P <?=number_format($budget,2)?></u></b></td>
				</tr>
				<tr>
					<td style="text-align:center;" width="100px"></td>
					<td style="text-align:center;" width="100px"></td>
					<td style="text-align:center;" width="100px"></td>
					<td style="text-align:center;" width="100px"></td>
				</tr>
				<tr>
					<td style="text-align:center;" width="100px"></td>
					<td style="text-align:center;" width="100px">&nbsp;</td>
					<td style="text-align:center;" width="100px">&nbsp;</td>
					<td style="text-align:center;" width="100px">&nbsp;</td>
				</tr>
				<tr>
					<td style="text-align:center;" width="100px">&nbsp;</td>
					<td style="text-align:center;" width="100px">&nbsp;</td>
					<td style="text-align:center;" width="100px">&nbsp;</td>
					<td style="text-align:center;" width="100px">&nbsp;</td>
				</tr>
            </table>
			<br/><br/><br/>
			<table border=0 cellpadding="1">
				<tr>
					<td style="text-align:center;border-bottom:2px solid #000;" width="50px"><b>Date</b></td>
					<td style="text-align:center;border-bottom:2px solid #000;" width="10px"><b>PO #</b></td>
					<td style="text-align:center;border-bottom:2px solid #000;" width="100px"><b>Supplier</b></td>
					<td style="text-align:center;border-bottom:2px solid #000;" width="100px"><b>Project</b></td>
                    <td style="text-align:right;border-bottom:2px solid #000;" width="10px"><b>Discount</b></td>
					<td style="text-align:right;border-bottom:2px solid #000;" width="10px"><b>PO Amount</b></td>
				</tr>
				<?php
					//ordered
					$total_po=0;
					$sql=mysql_query("SELECT * FROM po_header WHERE budget_header_id='$budget_header_id' AND status='F'");
					while($r=mysql_fetch_assoc($sql)) {
						$po_amount = 0;
							$sql1=mysql_query("SELECT * FROM spo_detail WHERE po_header_id = '".$r[po_header_id]."'");
						while($f=mysql_fetch_assoc($sql1)) {
							$sql2=mysql_query("SELECT * FROM sub_spo_detail WHERE spo_detail_id = '".$f[spo_detail_id]."'");
							while($f2=mysql_fetch_assoc($sql2)){
								$po_amount+=$f2[quantity]*$f2[unit_cost];
							}
						}
                                $po_amount -= $r[discount_amount];
							
								echo '<tr>';
								echo '<td style="text-align:center;width:50px;" >'.date("Y/m/d",strtotime($r[date])).'</td>';
								echo '<td style="text-align:center;width:10px;" >'.$r[po_header_id].'</td>';
								echo '<td style="text-align:center;width:100px;">'.$options->getSupplierName($r[supplier_id]).'</td>';
								echo '<td style="text-align:center;width:100px;" >'.$options->attr_Project($r[project_id],"project_name").'</td>';
                                echo '<td style="text-align:right;width:10px;">'.number_format($r[discount_amount],2).'</td>';
								echo '<td style="text-align:right;width:10px;">'.number_format($po_amount,2).'</td>';
								echo '</tr>';
								
							$total_po = $po_amount + $total_po;
					}
				?>
				<tr>
					<td style="text-align:center;border-top:2px solid #000;" width="50px">&nbsp;</td>
					<td style="text-align:center;border-top:2px solid #000;" width="10px">&nbsp;</td>
					<td style="text-align:center;border-top:2px solid #000;" width="100px">&nbsp;</td>
					<td style="text-align:center;border-top:2px solid #000;" width="100px">&nbsp;</td>
					<td style="text-align:center;border-top:2px solid #000;" width="10px">&nbsp;</td>
                    <td style="text-align:center;border-top:2px solid #000;" width="10px">&nbsp;</td>
				</tr>
				<tr>
					<td style="text-align:center;" width="50px">&nbsp;</td>
					<td style="text-align:right;" width="20px">&nbsp;</td>
					<td style="text-align:center;" width="20px">&nbsp;</td>
                    <td style="text-align:center;" width="20px">&nbsp;</td>
					<td style="text-align:right;border-bottom:1px solid #000;" width="20px"><b>TOTAL </b></td>
					<td style="text-align:right;border-bottom:1px solid #000;" width="20px">P <?=number_format($total_po,2)?></td>
				</tr>
					<tr>
					<td style="text-align:center;" width="50px">&nbsp;</td>
					<td style="text-align:center;" width="20px">&nbsp;</td>
					<td style="text-align:center;" width="20px">&nbsp;</td>
                        <td style="text-align:center;" width="20px">&nbsp;</td>
					<td style="text-align:right;" width="20px"><b>BALANCE </b></td>
					<td style="text-align:right;" width="20px">
					<span style="text-decoration:underline;border-bottom: 1px solid #000;"><b>P <?=number_format($budget - $total_po,2)?></b></span>
					</td>
				</tr>
			</table>
			<br/>
			<br/>
			<br/>
			<div>
				Prepared By<br/><br/><br/>
				<?=$options->getUserName($_SESSION[userID])?>
			</div>
        </div><!--End of content-->
    </div><!--End of Form-->
</div>
</body>
</html>