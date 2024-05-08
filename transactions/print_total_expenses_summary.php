<?php
require_once('../my_Classes/options.class.php');
include_once("../conf/ucs.conf.php");

$options=new options();	

function getProjects(){
	$result = mysql_query("	
		select * from projects order by project_name asc
	") or die(mysql_error());	
	$a = array();
	while($r = mysql_fetch_assoc($result)){
		$a[] = $r;		
	}
	return $a;
}
function getTotalBudget($project_id){
	$result = mysql_query("
		select 
			sum(amount) as amount
		from
			budget_header as h, budget_detail as d
		where
			h.budget_header_id = d.budget_header_id
		and h.status != 'C'
		and h.project_id = '$project_id'
	") or die(mysql_error());
	$r = mysql_fetch_assoc($result);
	return $r['amount'];
}

function getTotalMaterialExpenses($project_id){
	$result = mysql_query("
		select
			sum(amount) as amount
		from
			issuance_header as h, issuance_detail as d
		where
			h.issuance_header_id = d.issuance_header_id
		and h.status != 'C'
		and h.project_id = '$project_id'
	") or die(mysql_error());
	$r = mysql_fetch_assoc($result);
	return $r['amount'];
	
}
function getTotalSubconExpenses($project_id){
	$result = mysql_query("
		select
			sum(amount) as amount
		from
			po_header as h, spo_detail as d, sub_spo_detail as sub
		where 
			h.po_header_id = d.po_header_id
		and d.spo_detail_id = sub.spo_detail_id
		and h.status != 'C'
		and h.project_id = '$project_id'
	") or die(mysql_error());
	$r = mysql_fetch_assoc($result);
	return $r['amount'];	
}
function getTotalFinancialBudget($project_id){
	$result = mysql_query("
		select
			sum(amount) as amount
		from
			financial_budget_header as h, financial_budget_detail as d
		where
			h.financial_budget_header_id = d.financial_budget_header_id
		and h.status != 'C'
		and h.project_id = '$project_id'
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
<style type="text/css">
*{ font-family:Arial, Helvetica, sans-serif; font-size:11px; }
thead{ display:table-header-group; }
table{ border-collapse:collapse; width:100%; }
table thead td{
	font-weight:bold;	
	border-top:1px solid #000;
	border-bottom:1px solid #000;
	
}
table td{
	vertical-align:top;
	padding:3px;
}
table td:nth-child(n+2){
	text-align:right;	
}
.subtotal td{ border-top:1px solid #000; font-weight:bold; }
.grandtotal td{ border-top:1px solid #000; border-bottom:3px double #000; font-weight:bold; }
</style>
</head>
<body>
<div class="container">
	
     <div><!--Start of Form-->
     	<div style="font-weight:bolder;">
        	TOTAL EXPENSES SUMMARY<br />
        </div>           
        
        <table>
        	<thead>
                <tr>
              		<td>PROJECT</td>  
                    <td>BUDGET</td>  
                    <td>TOTAL MATERIAL EXPENSES</td>  
                    <td>SUBCON TOTAL EXPENSES</td>  
                    <td>TOTAL FINANCIAL BUDGET</td>  
                </tr>
           	</thead>
            <tbody>
            	<?php
				$g_budget = $g_material = $g_subcon = $g_financial = 0;
				foreach(getProjects() as $r):
					$t_budget 		= getTotalBudget($r['project_id']);
					$t_material 	= getTotalMaterialExpenses($r['project_id']);
					$t_subcon		= getTotalSubconExpenses($r['project_id']);
					$t_financial	= getTotalFinancialBudget($r['project_id']);
					
					$g_budget 		+= $t_budget;
					$g_material 	+= $t_material;
					$g_subcon		+= $t_subcon;
					$g_financial	+= $t_financial;
					
					echo "
						<tr>
							<td>$r[project_name]</td>
							<td>".number_format($t_budget,2)."</td>
							<td>".number_format($t_material,2)."</td>
							<td>".number_format($t_subcon,2)."</td>
							<td>".number_format($t_financial,2)."</td>
						</tr>
					";
				endforeach;
					echo "
						<tr class='grandtotal'>
							<td></td>
							<td>".number_format($g_budget,2)."</td>
							<td>".number_format($g_material,2)."</td>
							<td>".number_format($g_subcon,2)."</td>
							<td>".number_format($g_financial,2)."</td>
						</tr>
					";
                ?>
            </tbody>
        </table>
    </div><!--End of Form-->
</div>
</body>
</html>