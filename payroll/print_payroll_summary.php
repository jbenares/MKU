<?php
#echo "This module is being maintained. Please wait a few minutes.";
require_once('../my_Classes/options.class.php');
include_once("../conf/ucs.conf.php");

$options          =new options();	
$from_date        = $_REQUEST['from_date'];
$to_date          = $_REQUEST['to_date'];
$project_id       = $_REQUEST['project_id'];	
$companyID        = $_REQUEST['companyID'];
$employee_type_id = $_REQUEST['employee_type_id'];
$work_category_id = $_REQUEST['work_category_id'];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>JOB ORDER</title>
<script>
function printPage() { print(); } //Must be present for Iframe printing
</script>
<link rel="stylesheet" type="text/css" href="../css/print.css"/>
<style type="text/css">
@media print {
   thead {display: table-header-group;}
}
body{ margin:0px; font-size:11px; font-family:Arial, Helvetica, sans-serif;}

.line_bottom {
	border-bottom-width: 1px;
	border-bottom-style: solid;
	border-bottom-color: #000000;
	border-left: 0px;
	border-right: 0px;
	border-top: 0px;
}

.container-body table{
	border-collapse:collapse;
	width:40%;
	display:inline-table;	
}
.table-summary{
	border-collapse:collapse;	
}
.table-summary tr:first-child td{
	border-top:1px solid #000;
	border-bottom:1px solid #000;
	font-weight:bold;
}

.table-summary td:nth-child(n+4){
	text-align:right;
}
</style>
</head>
<body>
	<div style='font-weight:bold;'>
		<?=$title?><br>
		SUMMARY OF STAFF PAYROLL <br>
		PERIOD COVERED <?=date("F j, Y",strtotime($from_date))?> to <?=date("F j, Y",strtotime($to_date))?>
	</div>
	<table class="table-summary"> 
    	<tr>
        	<td>EMPLOYEE</td>
        	<td>POSITION</td>
            <!-- <td>FROM DATE</td>
            <td>TO DATE</td> -->
            <td>NO OF DAYS</td>
            <td style='text-align:right;'>SEMI MONTHLY</td>
            <td style='text-align:right;'>NO OF HRS</td>
            <td style='text-align:right;'>REGULAR 125%</td>
            <td style='text-align:right;'>NO OF HRS</td>
            <td style='text-align:right;'>SPECIAL 130%</td>
            <td style='text-align:right;'>NO OF HRS</td>
            <td style='text-align:right;'>LEGAL 200%</td>
            <td style='text-align:right;'>TOTAL OVERTIME</td>
            <td style='text-align:right;'>ABSENCES</td>
                        
            <td style='text-align:right;'>ALLOWANCE</td>
            <td style='text-align:right;'>GROSS</td>
            <td style='text-align:right;'>SSS</td>
            <td style='text-align:right;'>PHILHEALTH</td>
            <td style='text-align:right;'>HDMF</td>
            <td style='text-align:right;'>TAXES</td>
            
            <td style='text-align:right;'>SSS LOAN</td>
            <td style='text-align:right;'>PAG-IBIG LOAN</td>
            
            <td style='text-align:right;'>RMY LENDING</td>
            <td style='text-align:right;'>CANTEEN</td>
            <td style='text-align:right;'>HOUSE RENTAL</td>
            <td style='text-align:right;'>PERSONAL CHARGABLES</td>
            
            <td style='text-align:right;'>TOTAL DEDUCTIONS</td>
            
            <td style='text-align:right;'>NET</td>

            <td style="text-align:center;">BANK</td>
            <td style="text-align:center;">ACCOUNT NO</td>
             <td style="text-align:center;">OUTSTANDING BALANCE</td>
        </tr>
	<?php
	$sql = "
		select
			e.employee_fname, e.employee_lname, e.employee_mname, p.empID,
			p.sss, p.philhealth, p.hdmf, p.taxes, p.rmy_lending,
			p.canteen, p.house_rental, p.personal_chargables, pa_from, pa_to,
			p.no_of_days, p.regular_hrs_ot, p.regular_ot_amount, p.special_hrs_ot, p.special_ot_amount, p.legal_hrs_ot, p.legal_ot_amount, p.basic_salary,sss_loan, pagibig_loan,no_of_absences,p.allowance,p.total_deductions,p.net_amount,p.gross,
			proj.project_name, proj.project_id, e.position, emp_bank, emp_account_no , p.out_bal
		from
			payroll_accumulator as p, employee as e, projects as proj
		where
			p.empID = e.employeeID
		and e.projectsID = proj.project_id
		and pa_from = '$from_date'
		and pa_to = '$to_date'
	";
	if( $project_id ) $sql .= " and e.projectsID = '$project_id'";
	if( $companyID ) $sql .= " and e.companyID = '$companyID'";
	if( $employee_type_id ) $sql .= " and e.employee_type_id = '$employee_type_id'";
	if( $work_category_id ) $sql .= " and e.work_category_id = '$work_category_id'";
		
	$sql .= "
		order by proj.project_name asc, proj.project_id asc
	";
	//echo $sql;
	$result = mysql_query($sql) or die(mysql_error());
	$i = 0;
	$aFields = array(
			'basic_salary','regular_hrs_ot','regular_ot_amount','special_hrs_ot','special_ot_amount','legal_hrs_ot','legal_ot_amount','total_overtime',
			'no_of_absences','allowance','gross','sss','philhealth','hdmf','taxes','sss_loan','pagibig_loan','rmy_lending','canteen','house_rental',
			'personal_chargables','total_deductions','net_amount'
		);
		
	$aOvertime	= array('regular_ot_amount','special_ot_amount','legal_ot_amount');
	$p_id = 'x';
	$aTotals = array();
	$gTotals = array();
	while($r = mysql_fetch_assoc($result)):
		$empID = $r['empID'];
				
		$t_ot = 0;
				
		foreach($aOvertime as $o){	
			$t_ot += $r[$o];
		}				
		
		if($p_id != $r['project_id']){
			
			if( $p_id != 'x' ){
				echo "
					<tr>
						<td style='border-top:1px solid #000;'></td>
						<td style='border-top:1px solid #000;'></td>
						<td style='border-top:1px solid #000;'></td>
				";
				
				foreach($aFields as $field){
					echo "<td style='border-top:1px solid #000;'>".number_format($aTotals[$field],2)."</td>";
				}	
				echo "
						<td style='border-top:1px solid #000;'></td>
						<td style='border-top:1px solid #000;'></td>	
						<td style='border-top:1px solid #000;'></td>		
				";
				echo "</tr>";
				
				$aTotals = array();	
			}
			
			
			echo "
				<tr>
					<td colspan='2' style='font-weight:bold;'>$r[project_name]</td>
				</tr>	
			";	
			$p_id = $r['project_id'];
		}
		
		
		foreach($aFields as $f){
			if( $f == 'total_overtime' ){
				$aTotals[$f] += $t_ot;
				$gTotals[$f] += $t_ot;	
			} else {
				$aTotals[$f] += $r[$f];
				$gTotals[$f] += $r[$f];
			}
		}
		
		
		echo "
			<tr>
				<td>$r[employee_lname], $r[employee_fname] $r[employee_mname]</td>
				<td>$r[position]</td>
				
				<td style='text-align:right;'>".number_format($r['no_of_days'],2)."</td>
				<td style='text-align:right;'>".number_format($r['basic_salary'],2)."</td>
				
				<td style='text-align:right;'>".number_format($r['regular_hrs_ot'],2)."</td>
				<td style='text-align:right;'>".number_format($r['regular_ot_amount'],2)."</td>
				
				<td style='text-align:right;'>".number_format($r['special_hrs_ot'],2)."</td>
				<td style='text-align:right;'>".number_format($r['special_ot_amount'],2)."</td>
				
				<td style='text-align:right;'>".number_format($r['legal_hrs_ot'],2)."</td>
				<td style='text-align:right;'>".number_format($r['legal_ot_amount'],2)."</td>
				
				<td style='text-align:right;'>".number_format($t_ot,2)."</td>
				<td style='text-align:right;'>".number_format($r['no_of_absences'],2)."</td>
				
				<td style='text-align:right;'>".number_format($r['allowance'],2)."</td>
				<td style='text-align:right;'>".number_format($r['gross'],2)."</td>
				
				<td style='text-align:right;'>".number_format($r['sss'],2)."</td>
				<td style='text-align:right;'>".number_format($r['philhealth'],2)."</td>
				<td style='text-align:right;'>".number_format($r['hdmf'],2)."</td>
				<td style='text-align:right;'>".number_format($r['taxes'],2)."</td>
				
				<td style='text-align:right;'>".number_format($r['sss_loan'],2)."</td>
				<td style='text-align:right;'>".number_format($r['pagibig_loan'],2)."</td>
				
				<td style='text-align:right;'>".number_format($r['rmy_lending'],2)."</td>
				<td style='text-align:right;'>".number_format($r['canteen'],2)."</td>
				<td style='text-align:right;'>".number_format($r['house_rental'],2)."</td>
				<td style='text-align:right;'>".number_format($r['personal_chargables'],2)."</td>	
				
				<td style='text-align:right;'>".number_format($r['total_deductions'],2)."</td>
				<td style='text-align:right;'>".number_format($r['net_amount'],2)."</td>	

				<td style='text-align:right;'>".$r['emp_bank']."</td>	
				<td style='text-align:right;'>".$r['emp_account_no']."</td>	
				<td style='text-align:right;'>".number_format($r['out_bal'],2)."</td>	
			</tr>
			
		";
	endwhile;	
	
	echo "
		<tr>
			<td style='border-top:1px solid #000;'></td>
			<td style='border-top:1px solid #000;'></td>
			<td style='border-top:1px solid #000;'></td>
	";
	
	foreach($aFields as $field){
		echo "<td style='border-top:1px solid #000;'>".number_format($aTotals[$field],2)."</td>";
	}	

	echo "
		<td style='border-top:1px solid #000;'></td>
		<td style='border-top:1px solid #000;'></td>	
		<td style='border-top:1px solid #000;'></td>		
	";
	echo "</tr>";
	
	echo "
		<tr>
			<td style='border-top:3px double #000;'></td>
			<td style='border-top:3px double #000;'></td>
			<td style='border-top:3px double #000;'></td>
	";
	
	foreach($aFields as $field){
		echo "<td style='border-top:3px double #000;'>".number_format($gTotals[$field],2)."</td>";
	}	
	echo "
		<td style='border-top:3px double #000;'></td>
		<td style='border-top:3px double #000;'></td>	
		<td style='border-top:3px double #000;'></td>	
	";
	echo "</tr>";
	
	
	?>
    
    
    </table>
	
   
</body>
</html>