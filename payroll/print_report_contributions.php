<?php
include_once("../conf/ucs.conf.php");
include_once("../library/lib.php");

$from_date        = $_REQUEST['from_date'];
$to_date          = $_REQUEST['to_date'];

$from_date_2      = $_REQUEST['from_date_2'];
$to_date_2        = $_REQUEST['to_date_2'];
$project_id       = $_REQUEST['project_id'];
$employee_type_id = $_REQUEST['employee_type_id'];

function getSalary($from_date,$to_date,$from_date_2,$to_date_2, $employeeID){

	$aReturn = array();

	$sql = "
		select
			basic_salary,taxes
		from 
			payroll_accumulator
		where
			pa_from = '$from_date' 
		and pa_to = '$to_date'
		and empID = '$employeeID'
	";
	$arr = lib::getTableAttributes($sql);
	$aReturn['basic_1'] = $arr['basic_salary'];
	$aReturn['tax_1']   = $arr['taxes'];

	$sql = "
		select
			basic_salary,taxes
		from 
			payroll_accumulator
		where
			pa_from = '$from_date_2' 
		and pa_to = '$to_date_2'
		and empID = '$employeeID'
	";
	$arr = lib::getTableAttributes($sql);
	$aReturn['basic_2'] = $arr['basic_salary'];
	$aReturn['tax_2']   = $arr['taxes'];

	return $aReturn;
}


function getReport($from_date,$to_date,$from_date_2,$to_date_2,$project_id,$employee_type_id){

	if( !empty($project_id) ) $project_filter_sql         = "and projectsID = '$project_id'";
	if( !empty($employee_type_id) ) $employee_type_filter = "and employee_type_id = '$employee_type_id'";

	$sql = "		
		select
			concat(employee_lname,', ',employee_fname) as employee_name, 
			sum(a.sss) as sss, sum(a.philhealth) as philhealth, sum(a.hdmf) as hdmf, sum(a.taxes) as taxes,
			sum(a.pagibig_loan) as pagibig_loan, sum(a.sss_loan) as sss_loan, sum(basic_salary) as basic_salary,
			project_name, e.sss as sss_no, e.hdmf as hdmf_no, sss_loan, e.philhealth as philhealth_no, e.employeeID, e.tin
		from
			payroll_accumulator as a 
		inner join employee as e on a.empID = e.employeeID
		and 
		(
			( pa_from = '$from_date' and pa_to = '$to_date' )
			or ( pa_from = '$from_date_2' and pa_to = '$to_date_2' )
		)
		$project_filter_sql
		$employee_type_filter
		left join projects as p on e.projectsID = p.project_id
		group by e.employeeID
		order by employee_lname, employee_fname
	";

	$result = mysql_query($sql) or die(mysql_error());
	$a = array();
	while( $r = mysql_fetch_assoc( $result ) ){
		$arr = getSalary($from_date,$to_date,$from_date_2,$to_date_2, $r['employeeID']);
		$r['basic_1'] = $arr['basic_1'];
		$r['tax_1']   = $arr['tax_1'];
		$r['basic_2'] = $arr['basic_2'];
		$r['tax_2']   = $arr['tax_2'];

		$a[] = $r;
	}

	return $a;	
}

	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>REPORT</title>
<script>
function printPage() { print(); } //Must be present for Iframe printing
</script>
<style type="text/css">

body
{
	size: legal portrait;
	font-family:Arial, Helvetica, sans-serif;
	font-size:11px;
}
.container{
	margin:0px auto;
	padding:0.1in;
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

table{
	border-collapse:collapse;	
}

table thead tr td{
	border-top:1px solid #000;
	border-bottom:1px solid #000;
	font-weight:bold;
}
table  tr td:nth-child(n+3){
	text-align:right;	
}
table td{
	padding:3px;	
	padding-right:30px;
}
tfoot td{
	border-top: 1px solid #000;
	font-weight: bold;
	text-align: right;
}
</style>
</head>
<body>
<div class="container">
	
     <div><!--Start of Form-->
     	
     	<div style="text-align:center; font-weight:bolder; margin-bottom:5px;">
        	<?=$title?> <br />           
        </div>

        <p style="font-weight:bold;">
        <?php
        	if( $_REQUEST['display_sss'] ){
        		echo "SCHEDULE OF SSS MONTHLY CONTRIBUTION <br> FOR PAYROLL PERIOD ".lib::ymd2mdy($from_date)."- ".lib::ymd2mdy($to_date). " AND ".lib::ymd2mdy($from_date)."- ".lib::ymd2mdy($to_date_2);
        	}

        	else if( $_REQUEST['display_pagibig'] ){
        		echo "MONTHLY CONTRIBUTION/MPL PAYMENT <br> FOR PAYROLL PERIOD ".lib::ymd2mdy($from_date)."- ".lib::ymd2mdy($to_date). " AND ".lib::ymd2mdy($from_date)."- ".lib::ymd2mdy($to_date_2);
        	}

        	else if( $_REQUEST['display_philhealth'] ){
        		echo "SCHEDULE OF MEDICARE (PHIC) MONTHLY CONTRIBUTION <br> FOR PAYROLL PERIOD ".lib::ymd2mdy($from_date)."- ".lib::ymd2mdy($to_date). " AND ".lib::ymd2mdy($from_date)."- ".lib::ymd2mdy($to_date_2);
        	}

        	else if( $_REQUEST['display_tax'] ){
        		echo "SCHEDULE OF TAX WITHELD ON COMPENSATION <br> FOR PAYROLL PERIOD ".lib::ymd2mdy($from_date)."- ".lib::ymd2mdy($to_date). " AND ".lib::ymd2mdy($from_date)."- ".lib::ymd2mdy($to_date_2);
        	}
        ?>
        </p>
        <div class="content" >
        	<table cellspacing="0">
            	<thead>
                    <tr>
                        <td>EMPLOYEE NAME</td>
                        <td>PROJECT</td>
                        <?php
                        if( $_REQUEST['display_sss'] ){
                        	echo "
                        		<td>EE SHARE</td>
                        		<td>ER SHARE</td>
                        		<td>TOTAL SSS</td>
                        		<td>EC</td>
                        		<td>TOTAL SSS & EC</td>
                        		<td style='text-align:center;'>SSS NO.</td>
                        	";
                        }
                        if( $_REQUEST['display_pagibig'] ){
                        	echo "                        	
                        		<td style='text-align:center;'>HDMFID</td>
                        		<td>EE</td>
                        		<td>ER</td>
                        		<td>MPL PAYMENTS</td>
                        	";
                        }
                        if( $_REQUEST['display_philhealth'] ){
                        	echo "<td>EE SHARE</td>";
                        	echo "<td>ER SHARE</td>";
                        	echo "<td>TOTAL EE & ER SHARE</td>";
                        	echo "<td style='text-align:center;'>PHIC NO.</td>";
                        }
                        
                        if( $_REQUEST['display_tax'] ){
                        	echo "<td style='text-align:center;'>TIN</td>";	
                        
                        	echo "<td>INCOME <br> ".lib::ymd2mdy($from_date)."- ".lib::ymd2mdy($to_date)."</td>";	
                        	echo "<td>W/HELD <br> ".lib::ymd2mdy($from_date)."- ".lib::ymd2mdy($to_date)."</td>";	

                        	echo "<td>INCOME <br> ".lib::ymd2mdy($from_date_2)."- ".lib::ymd2mdy($to_date_2)."</td>";	
                        	echo "<td>W/HELD <br> ".lib::ymd2mdy($from_date_2)."- ".lib::ymd2mdy($to_date_2)."</td>";	

                        	echo "<td>INCOME <br> ".lib::ymd2mdy($from_date)."- ".lib::ymd2mdy($to_date_2)."</td>";	
                        	echo "<td>W/HELD <br> ".lib::ymd2mdy($from_date)."- ".lib::ymd2mdy($to_date_2)."</td>";	
                        }
                        ?>
                    </tr>
               	</thead>
                <tbody>                  
                    <?php                               
                    $aReport= getReport($from_date,$to_date,$from_date_2,$to_date_2,$project_id,$employee_type_id);

                    /*sss contributions*/
                    $t_sss_ee = $t_sss_er =  $t_sss_er_ee = $t_sss_ec = $t_sss_er_ee_ec = 0;

                    /*pagibig contributions*/
                    $t_hdmf_ee = $t_hdmf_er = $t_hdmf_loan = 0;

                    /*philhealth contributions*/
                    $t_phil_ee = $t_phil_er = $t_phil_er_ee = 0;

                    /*for taxes*/
                    $t_basic_1 = $t_tax_1 = $t_basic_2 = $t_tax_2 = $t_basic = $t_tax = 0;

					if(count($aReport))
						foreach( $aReport as $r ){

		                    echo "
		                        <tr>
		                        	<td>$r[employee_name]</td>
		                        	<td>$r[project_name]</td>
		                     ";
		                     

		                  	if( $_REQUEST['display_sss'] ) {
		                  		$sss_er = lib::getAttribute('sss_contrib','ee',$r['sss'],'er');
		                  		$sss_ec = lib::getAttribute('sss_contrib','ee',$r['sss'],'ec');

		                  		echo "<td>".number_format($r['sss'],2)."</td>";
		                  		echo "<td>".number_format($sss_er,2)."</td>";		                  		
		                  		echo "<td>".number_format($r['sss'] + $sss_er,2)."</td>";		                  		
		                  		echo "<td>".number_format($sss_ec,2)."</td>";		                  		
		                  		echo "<td>".number_format($r['sss'] + $sss_er + $sss_ec,2)."</td>";		                  		
		                  		echo "<td style='text-align:center;'>".$r['sss_no']."</td>";		                  		

		                  		/*add to total*/
								$t_sss_ee       += $r['sss'];
								$t_sss_er       += $sss_er;
								$t_sss_er_ee    += $r['sss'] + $sss_er;
								$t_sss_ec       += $sss_ec;
								$t_sss_er_ee_ec += $r['sss'] + $sss_er + $sss_ec;

		                  	}
	                        if( $_REQUEST['display_pagibig'] ){
	                        	echo "<td style='text-align:center;'>".$r['hdmf_no']."</td>";
	                        	echo "<td>".number_format($r['hdmf'],2)."</td>";
	                        	echo "<td>".number_format($r['hdmf'],2)."</td>";
	                        	echo "<td>".number_format($r['sss_loan'],2)."</td>";

	                        	/*add to total*/
								$t_hdmf_ee   += $r['hdmf'];
								$t_hdmf_er   += $r['hdmf'];
								$t_hdmf_loan += $r['sss_loan'];

	                        }
	                        if( $_REQUEST['display_philhealth'] ){
	                        	echo "<td>".number_format($r['philhealth'],2)."</td>";
	                        	echo "<td>".number_format($r['philhealth'],2)."</td>";
	                        	echo "<td>".number_format($r['philhealth'] * 2,2)."</td>";
	                        	echo "<td style='text-align:center;'>".$r['philhealth_no']."</td>";

	                        	/*er and ee share for philhealth are the same*/
								$t_phil_er    += $r['philhealth'];
								$t_phil_ee    += $r['philhealth'];
								$t_phil_er_ee += $r['philhealth'] * 2;

	                        }
	                        
	                        if( $_REQUEST['display_tax'] ){

	                        	echo "<td style='text-align:center;'>".$r['tin']."</td>";   

	                        	echo "<td>".( ($r['basic_1'] > 0) ?  number_format($r['basic_1'],2) : "-" )."</td>";   	
	                        	echo "<td>".( ($r['tax_1'] > 0) ?  number_format($r['tax_1'],2) : "-" )."</td>";   	

	                        	echo "<td>".( ($r['basic_2'] > 0) ?  number_format($r['basic_2'],2) : "-" )."</td>";   	
	                        	echo "<td>".( ($r['tax_2'] > 0) ?  number_format($r['tax_2'],2) : "-" )."</td>";   		                        	

	                        	echo "<td>".( ($r['basic_salary'] > 0) ?  number_format($r['basic_salary'],2) : "-" )."</td>";   	
	                        	echo "<td>".( ($r['taxes'] > 0) ?  number_format($r['taxes'],2) : "-" )."</td>";   	
	                        

	                        	/*add to total*/
								$t_basic_1 += $r['basic_1'];
								$t_tax_1   += $r['tax_1'];
								$t_basic_2 += $r['basic_2'];
								$t_tax_2   += $r['tax_2'];
								$t_basic   += $r['basic_salary'];
								$t_tax     += $r['taxes'];

	                        } 

		                     echo "
		                        </tr>
		                    ";	  
		              	}               
                    ?>
           		</tbody>
           		<tfoot>
           			<tr>
           				<td></td>
           				<td></td>
	           			<?php
	           			if( $_REQUEST['display_sss'] ) {
	           				echo "
	           					<td>".number_format($t_sss_ee,2)."</td>
	           					<td>".number_format($t_sss_er,2)."</td>
	           					<td>".number_format($t_sss_er_ee,2)."</td>
	           					<td>".number_format($t_sss_ec,2)."</td>
	           					<td>".number_format($t_sss_er_ee_ec,2)."</td>
	           					<td></td>
	           				";
	           			}

	           			
	           			if( $_REQUEST['display_pagibig'] ) {
	           				echo "
	           					<td></td>
	           					<td>".number_format($t_hdmf_ee,2)."</td>
	           					<td>".number_format($t_hdmf_er,2)."</td>
	           					<td>".number_format($t_hdmf_loan,2)."</td>	           						           					
	           				";
	           			}

	           			if( $_REQUEST['display_philhealth'] ) {
	           				echo "
	           					<td>".number_format($t_phil_ee,2)."</td>
	           					<td>".number_format($t_phil_er,2)."</td>
	           					<td>".number_format($t_phil_er_ee,2)."</td>	           						           					
	           					<td></td>
	           				";
	           			}

	           			if( $_REQUEST['display_tax'] ){

                        	echo "<td style='text-align:center;'></td>";   

                        	echo "<td>".number_format($t_basic_1,2)."</td>";   	
                        	echo "<td>".number_format($t_tax_1,2)."</td>";   	

                        	echo "<td>".number_format($t_basic_2,2)."</td>";   	
                        	echo "<td>".number_format($t_tax_2,2)."</td>";   	

                        	echo "<td>".number_format($t_basic,2)."</td>";   	
                        	echo "<td>".number_format($t_tax,2)."</td>";   	
                        
                        } 
	           			?>
           			</tr>
           		</tfoot>
            </table>            
        </div><!--End of content-->
    </div><!--End of Form-->

</div>
</body>
</html>

