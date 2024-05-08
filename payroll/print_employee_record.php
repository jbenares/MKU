<?php
	ob_start();
	session_start();
	
	include_once("../my_Classes/options.class.php");
	require_once(dirname(__FILE__).'/../library/lib.php');
	
	function numform($num) {
		if($num==0) $num = "&nbsp;";
		else $num = number_format($num, 2);
		
		return $num;
	}
	function getProjects($filter = NULL, $search_keyword = NULL){
		
		$sql  = "
			select * from projects
			where 1=1
		";
		if( $filter == "project_name" ) $sql .= " and project_name like '%$search_keyword%'";
		$sql  .= "
			order by project_name asc
		";
		#echo $sql;
		$result = mysql_query($sql) or die(mysql_error());
		$a = array();
		while($r = mysql_fetch_assoc($result)){
			$a[] = $r;
		}
		#print_r($a);
		return $a;
	}
	
	function computeTotalPackage($employeeID){
		$result = mysql_query("
			select * from employee where employeeID = '$employeeID'
		") or die(mysql_error());
		$aEmp = mysql_fetch_assoc($result);
		
		
		$fixed_ot = $aEmp['fixed_ot'];
		
		#compute for monthly pay
		if($aEmp['employee_statusID'] == 1){ #monthly
			$emp_base_rate = $aEmp['base_rate'];
			$hourly_rate = ($emp_base_rate / 26) / 8;
			$ot_hourly_rate	= $hourly_rate * 1.25;
			$emp_fixed_ot_amount  = $ot_hourly_rate * $fixed_ot * 26;
			$emp_allowance = $aEmp['allowance'];
		}else{ #daily
			$emp_base_rate = $aEmp['base_rate'] * 26;		
			$hourly_rate = ($aEmp['base_rate']) / 8;
			$ot_hourly_rate	= $hourly_rate * 1.25;
			$emp_fixed_ot_amount  = $ot_hourly_rate * $fixed_ot * 26;
			$emp_allowance = $aEmp['allowance'] * 26;
		}
		
		$a = array();
		$a['employee_base_rate'] = $emp_base_rate;
		$a['employee_allowance'] = $emp_allowance;
		$a['employee_fixed_ot'] = $emp_fixed_ot_amount;
		
		return $a;
		
	}
	
	function getLatestContract($employeeID,$project_id){
		$sql=mysql_query("SELECT * FROM employee_contracts WHERE employeeID = '$employeeID' AND projectsID = '$project_id' order by contract_id desc");
		$r=mysql_fetch_assoc($sql);
		return $r[contract_num];
	}
?>
<html>

<head>

<link rel="stylesheet" type="text/css" media="screen" href="css/stylemain.css" />

<style>
td {
	font-family: Arial;
	font-size: 12px;
	text-align:left;
	vertical-align:bottom;
}

.line_bottom {
	border-bottom-width: 1px;
	border-bottom-style: solid;
	border-bottom-color: #000000;
	border-left: 0px;
	border-right: 0px;
	border-top: 0px;
}
</style>

<script>
function printPage() { print(); } //Must be present for Iframe printing
</script>

</head>

<body>
<?php
	include_once("../conf/ucs.conf.php");
	
	function addZeros($num) {
		return str_pad($num, 7, "0", STR_PAD_LEFT);
	}

	$b 					= $_REQUEST['b'];
	$search_keyword		= $_REQUEST['search_keyword'];
	$filter 			= $_REQUEST['filter'];
	$fdate 				= $_REQUEST['fdate'];
	$tdate 				= $_REQUEST['tdate'];
	$rediscountID 		= $_REQUEST['rediscountID'];
	$client				= $_REQUEST['client'];
	
	$options = new options();
	
	$grand_total=0;
	if(!empty($search_keyword) or empty($search_keyword)) 
	{

	
		echo '<table width="100%" border="0" cellspacing="0" cellpadding="3">		
				<tr>
					<td colspan=14 style="border-bottom-width:1px;border-bottom-style:solid;border-bottom-color:#000000;">
						<p><b>Employee Records :</b><br>
					</td>
				</tr>
			  </table>';
		
		echo '<table width="100%" border="0" cellspacing="0" cellpadding="3">';
		
		echo '<tr>
					<td class=line_bottom width=30><b>#</b></td>
					<td class=line_bottom width=30><b>EMP. #</b></td>
					<td class=line_bottom align="center"><b>Employee Name</b></td>

					<td class=line_bottom align="center"><b>Date Hired</b></td>
					<td class=line_bottom align="center"><b>Contract#</b></td>
					<td class=line_bottom style="text-align:right;"><b>BASE RATE</b></td>
					<td class=line_bottom style="text-align:right;"><b>ALLOWANCE</b></td>
					<td class=line_bottom style="text-align:right;"><b>FIXED OT</b></td>
					<td class=line_bottom style="text-align:right;"><b>TOTAL</b></td>
					
					<td class=line_bottom style="text-align:center;"><b>T.I.N.</b></td>
					<td class=line_bottom style="text-align:center;"><b>SSS No.</b></td>
					<td class=line_bottom style="text-align:center;"><b>PhilHealth No.</b></td>
					<td class=line_bottom style="text-align:center;"><b>HDMF No.</b></td>
					<!--<td class=line_bottom style="text-align:left;"><b>Projects</b></td> -->
				</tr>';
	
		
		foreach(getProjects($filter,$search_keyword) as $p):

			if( !empty($_REQUEST['hired_from_date']) && !empty($_REQUEST['hired_to_date']) ){
				$date_hired_filter = " and effectivity_date between '$_REQUEST[hired_from_date]' and '$_REQUEST[hired_to_date]'";
			} else{
				$date_hired_filter = "";	
			}

			$getData = mysql_query("select 
										* 
									from 
										employee as e,
										projects as p,
										employee_contracts as ec
									where 
										$filter like '%$search_keyword%'
										and e.employeeID = ec.employeeID AND
										e.employee_void='0' and
										( e.projectsID = '$p[project_id]' or ec.projectsID = '$p[project_id]' )
										and e.inactive = '0'
										and ( ec.projectsID = p.project_id or e.projectsID = p.project_id )
										$date_hired_filter
									order by
										$filter asc");
		
			/*if( !empty($_REQUEST['hired_from_date']) && !empty($_REQUEST['hired_to_date']) ){
				$date_hired_filter = " and e.datehired between '$_REQUEST[hired_from_date]' and '$_REQUEST[hired_to_date]'";
			} else{
				$date_hired_filter = "";	
			}
			
			$getData = mysql_query("select * 
									from 
										employee as e,
										projects as p
									where 
										$filter like '%$search_keyword%' and
										e.projectsID = p.project_id and
										e.employee_void='0' and
										e.projectsID = '$p[project_id]'
										and e.inactive = '0'
										$date_hired_filter
									order by
										$filter asc
										");*/
										
			if(mysql_num_rows($getData) <= 0){
				continue;	
			}else{
				echo "
					<tr>
						<td colspan='7' style='font-weight:bold; padding:10px 0px;'>$p[project_name]</td>
					</tr>
				";	
			}
			$subtotal=0;
			while($rData=mysql_fetch_array($getData)) 
			
			{	
				
				$i=1;
				$number += $i;
				$employee = $rData[employee_lname].', '.$rData[employee_fname].' '.$rData[employee_mname].'';
				
				$aEmp = computeTotalPackage($rData['employeeID']);
				$employee_base_rate = round($aEmp['employee_base_rate'],2);
				$employee_allowance = round($aEmp['employee_allowance'],2);
				$employee_fixed_ot = round($aEmp['employee_fixed_ot'],2);
				
				$t_package = $employee_base_rate + $employee_allowance + $employee_fixed_ot;
				
				
				echo '<tr>
								<td width=30>'.$number.'.</td>
								<td width=30>'.str_pad($rData['employeeID'],7,0,STR_PAD_LEFT).'</td>
								<td>'.$employee.'</td>

								<td>'.lib::ymd2mdy($rData['effectivity_date']).'</td>
								<td style="text-align:right;">'.$rData[contract_num].'</td>
								<td style="text-align:right;">'.number_format($employee_base_rate,2).'</td>
								<td style="text-align:right;">'.number_format($employee_allowance,2).'</td>
								<td style="text-align:right;">'.number_format($employee_fixed_ot,2).'</td>
								<td style="text-align:right; font-weight:bold;">'.number_format($t_package,2).'</td>
								
								<td style="text-align:center;">'.$rData[tin].'</td>
								<td style="text-align:center;">'.$rData[sss].'</td>
								<td style="text-align:center;">'.$rData[philhealth].'</td>
								<td style="text-align:center;">'.$rData[hdmf].'</td>
								<!--<td style="text-align:left;">'.$rData[project_name].'</td> -->
					</tr>';
				
				$subtotal +=$t_package;
			}
				echo '<tr style="border-bottom:2px solid #000 !important;"><td>&nbsp;</td></tr>';
				echo '<tr >
							<td width=30 >SUBTOTAL</td>
							<td width=30></td>
							<td></td>
							<td></td>
							<td></td>
							<td style="text-align:right;"></td>
							<td style="text-align:right;"></td>
							<td style="text-align:right;"></td>
							<td style="text-align:right; font-weight:bold;"><u>'.number_format($subtotal,2).'</u></td>
							
							<td style="text-align:center;"></td>
							<td style="text-align:center;"></td>
							<td style="text-align:center;"></td>
							<td style="text-align:center;"></td>
					</tr>';
			$grand_total +=$subtotal;
		endforeach;
		
		echo '<tr><td  colspan="13" style="border-bottom:2px solid #000 !important;">&nbsp;</td></tr>';
		echo '<tr >
							<td width=30 >GRANDTOTAL</td>
							<td width=30></td>
							<td></td>
							<td></td>
							<td></td>
							<td style="text-align:right;"></td>
							<td style="text-align:right;"></td>
							<td style="text-align:right;"></td>
							<td style="text-align:right; font-weight:bold;"><u>'.number_format($grand_total,2).'</u></td>
							
							<td style="text-align:center;"></td>
							<td style="text-align:center;"></td>
							<td style="text-align:center;"></td>
							<td style="text-align:center;"></td>
					</tr>';
	}
	
?>
</body>
</html>