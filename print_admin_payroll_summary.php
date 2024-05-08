<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
	$supplier_id = $_REQUEST[supplier_id];
	
					$sql="
						select
							  *
						from
							  po_header as h, supplier as s, projects p
						where
							h.supplier_id = '$supplier_id'
						and
							h.supplier_id = s.account_id
						and
							(h.po_type = 'L' or h.po_type = 'S')
						and
							p.project_id = h.project_id
						and
							h.status = 'F' 
						and
							h.approval_status = 'A'
						";
		$rs = mysql_query($sql);
		
	
	function getTotalBudget($po_header_id){
		$result = mysql_query("
			select sum(amount) as amount from spo_detail as sp,sub_spo_detail as sb where sp.po_header_id='$po_header_id' and sp.spo_detail_id=sb.spo_detail_id
		") or die(mysql_error());
		$r = mysql_fetch_assoc($result);
		return $r['amount'];
	}
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
     	<div style="font-weight:bolder;">
        	Schedules of Laborers Semi-Monthly Payroll <?=(!empty($supplier_id))?$options->getSupplierName($supplier_id):"";?> <br />
        </div>    
			<br/>
			<br/>
        <div class="content" style="">
        	<?php
        		while($rw = mysql_fetch_assoc($rs)):
        			$po_header_id = $rw[po_header_id];
					$project_name = $rw[project_name];
					$work_category_id = $rw[work_category_id];
					$sub_work_category_id = $rw[sub_work_category_id];
					$payroll_header_id = $rw[payroll_header_id];
        	?>
        	<table border=0 cellpadding="1">
            	<tr>
					<td style="text-align:center;" width="100px"><u>In-House Budget</u></td>
					<td style="text-align:center;" width="100px"><u>Scope of Work</u></td>
					<td style="text-align:center;" width="100px"><u>Total In-House Budget</u></td>
				</tr>
				<tr>
					<td style="text-align:center;" width="100px">&nbsp;</td>
					<td style="text-align:center;" width="100px">&nbsp;</td>
					<td style="text-align:center;" width="100px">&nbsp;</td>
				</tr>
				<tr>
					<td style="text-align:center;" width="100px">PO # <?=$po_header_id?></td>
					<td style="text-align:center;" width="100px"><?=$options->getAttribute('work_category','work_category_id',$sub_work_category_id,'work');?></td>
					<td style="text-align:center;" width="100px">P <?=number_format(getTotalBudget($po_header_id),2)?></td>
				</tr>
				<tr>
					<td style="text-align:center;" width="100px">&nbsp;</td>
					<td style="text-align:center;" width="100px">&nbsp;</td>
					<td style="text-align:center;" width="100px">&nbsp;</td>
				</tr>
				<tr>
					<td style="text-align:center;" width="100px">&nbsp;</td>
					<td style="text-align:center;" width="100px">&nbsp;</td>
					<td style="text-align:right;" width="100px">_______________________</td>
				</tr>
				<tr>
					<td style="text-align:center;" width="100px"><b>TOTAL</b></td>
					<td style="text-align:center;" width="100px">&nbsp;</td>
					<td style="text-align:right;" width="100px">
					<span style="text-decoration:underline;border-bottom: 1px solid #000;"><b>P <?=number_format(getTotalBudget($po_header_id),2)?></b></span>
					</td>
				</tr>
            </table>
			<br/><br/><br/>
			<table border=0 cellpadding="1">
				<tr>
					<td style="text-align:center;" width="50px">&nbsp;</td>
					<td style="text-align:center;" width="10px">Regular Payroll</td>
					<td style="text-align:center;" width="10px">Overtime</td>
					<td style="text-align:center;" width="10px">Total</td>
					<td style="text-align:center;" width="10px">&nbsp;</td>
				</tr>
				<?php
					$query="select * from po_header_payroll where payroll_header_id='$payroll_header_id'";
					$q=mysql_query($query);
					$s_a=0;
					$s_t=0;
					while($res=mysql_fetch_assoc($q)){
						extract($res);
						echo '<tr>';
						echo '<td style="text-align:left;width:50px;" >'.date("Y/m/d",strtotime($date_from)).' - '.date("Y/m/d",strtotime($date_to)).'</td>';
						echo '<td style="text-align:right;width:10px;" >'.number_format($amount,2).'</td>';
						echo '<td style="text-align:right;width:10px;">'.number_format($overtime,2).'</td>';
						echo '<td style="text-align:right;width:10px;" >'.number_format($overtime+$amount,2).'</td>';
						echo '<td style="text-align:right;width:10px;">&nbsp;</td>';
						echo '</tr>';
						
						$s_a+=$amount;
						$s_t+=$overtime+$amount;					
					}
				?>
				<tr>
					<td style="text-align:center;" width="50px">&nbsp;</td>
					<td style="text-align:center;" width="20px">&nbsp;</td>
					<td style="text-align:center;" width="20px">&nbsp;</td>
					<td style="text-align:center;" width="20px">&nbsp;</td>
					<td style="text-align:center;" width="20px">&nbsp;</td>
				</tr>
				<tr>
					<td style="text-align:center;" width="50px">&nbsp;</td>
					<td style="text-align:center;" width="20px">&nbsp;</td>
					<td style="text-align:center;" width="20px">&nbsp;</td>
					<td style="text-align:center;" width="20px">&nbsp;</td>
					<td style="text-align:center;" width="20px">&nbsp;</td>
				</tr>
				<tr>
					<td style="text-align:center;" width="50px">&nbsp;</td>
					<td style="text-align:center;" width="20px">&nbsp;</td>
					<td style="text-align:center;" width="20px">&nbsp;</td>
					<td style="text-align:center;" width="20px">&nbsp;</td>
					<td style="text-align:center;" width="20px">&nbsp;</td>
				</tr>
				<tr>
					<td style="text-align:center;" width="50px">&nbsp;</td>
					<td style="text-align:center;" width="20px">&nbsp;</td>
					<td style="text-align:center;" width="20px">&nbsp;</td>
					<td style="text-align:center;" width="20px">&nbsp;</td>
					<td style="text-align:center;" width="20px">&nbsp;</td>
				</tr>
				<tr>
					<td style="text-align:center;" width="50px">&nbsp;</td>
					<td style="text-align:center;" width="20px">&nbsp;</td>
					<td style="text-align:center;" width="20px">&nbsp;</td>
					<td style="text-align:center;" width="20px">&nbsp;</td>
					<td style="text-align:center;" width="20px">&nbsp;</td>
				</tr>
				<tr>
					<td style="text-align:center;" width="50px"><b>Total Payment</b></td>
					<td style="text-align:right;border-bottom:1px solid #000;" width="20px"><?=number_format($s_a,2)?></td>
					<td style="text-align:center;" width="20px">&nbsp;</td>
					<td style="text-align:right;border-bottom:1px solid #000;" width="20px"><?=number_format($s_t,2)?></td>
					<td style="text-align:right;border-bottom:1px solid #000;" width="20px"><?=number_format($s_t,2)?></td>
				</tr>
					<tr>
					<td style="text-align:center;" width="50px"><b>Balance</b></td>
					<td style="text-align:center;" width="20px">&nbsp;</td>
					<td style="text-align:center;" width="20px">&nbsp;</td>
					<td style="text-align:center;" width="20px">&nbsp;</td>
					<td style="text-align:right;" width="20px">
					<span style="text-decoration:underline;border-bottom: 1px solid #000;"><b>P <?=number_format(getTotalBudget($po_header_id)-$s_t,2)?></b></span>
					</td>
				</tr>
			</table>
			<br><br/>
			<hr/>
			<?php
				endwhile;
			?>
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