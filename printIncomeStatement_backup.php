<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");

	$from_date 	= $_REQUEST['startingdate'];
	$to_date	= $_REQUEST['endingdate'];
	$project_id = $_REQUEST['project_id'];
	
	$checkList2		= $_REQUEST['checkList2'];
	$aProjects		= $checkList2;
	$projects = implode(",",$checkList2);
	
	$a = array();
	foreach($checkList2 as $list){
		$a[] = "$list";
	}
	$gchart_projects = implode(",",$a);
	
	
	#echo $projects;
	#echo "<br>";
	#echo $gchart_projects;
	//$month=$_REQUEST[month];
	//$year=$_REQUEST[year];	
	
	$options=new options();	
	
	function getBudget($gchart_id,$project_id){
		$sql = "
			select 
				sum(amount) as amount
			from
				financial_budget_header as h, financial_budget_detail as d
			where
				h.financial_budget_header_id = d.financial_budget_header_id
			and gchart_id = '$gchart_id'			
			and status != 'C'
		";
		
		if($project_id) $sql .= "and project_id = '$project_id'";
		
		$result  = mysql_query($sql) or die(mysql_error());
		$r = mysql_fetch_assoc($result);
		return $r['amount'];
	}
	
	function getBudgetOfProjects($gchart_id,$projects){
		$sql = "
			select 
				sum(amount) as amount
			from
				financial_budget_header as h, financial_budget_detail as d
			where
				h.financial_budget_header_id = d.financial_budget_header_id
			and gchart_id = '$gchart_id'			
			and status != 'C'
		";
		
		if($projects) $sql .= "and project_id in ($projects)";
		
		$result  = mysql_query($sql) or die(mysql_error());
		$r = mysql_fetch_assoc($result);
		return $r['amount'];
	}
	
	function getBalance($from_date,$to_date,$gchart_id,$gchart_projects,$debit=TRUE){
			
		$query = "
			select
				sum(debit) as debit,
				sum(credit) as credit
			from
				gltran_header as h, gltran_detail as d, gchart as g
			where
				d.gchart_id  = g.gchart_id
			and
				h.gltran_header_id = d.gltran_header_id
			and
				d.gchart_id = '$gchart_id'
			and
				date between '$from_date' and '$to_date'
			and
				status != 'C'
		";
		if($gchart_projects){
		$query.="
			and
			(
				project_id in ($gchart_projects)
			or
				account_id in ($gchart_projects)
			)
		";	
		}
		$result = mysql_query($query) or die(mysql_error());
		$r = mysql_fetch_assoc($result);
		if($debit){
			return $r['debit'] - $r['credit'];
		}else{
			return $r['credit'] - $r['debit'];	
		}
	}
	
	function getProjectBalance($from_date,$to_date,$gchart_id,$project_id,$debit=TRUE){
			
		$query = "
			select
				sum(debit) as debit,
				sum(credit) as credit
			from
				gltran_header as h, gltran_detail as d, gchart as g
			where
				d.gchart_id = g.gchart_id
			and
				h.gltran_header_id = d.gltran_header_id
			and
				d.gchart_id = '$gchart_id'
			and
				date between '$from_date' and '$to_date'
			and
				status != 'C'
		";
		if($project_id){
		$query.="
			and
			(
				project_id = '$project_id'
			or
				account_id = 'p-$project_id'
			)
		";	
		}
		$result = mysql_query($query) or die(mysql_error());
		$r = mysql_fetch_assoc($result);
		if($debit){
			return $r['debit'] - $r['credit'];
		}else{
			return $r['credit'] - $r['debit'];	
		}
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

body
{
	size: legal portrait;
	font-family:Arial, Helvetica, sans-serif;
	font-size:11px;
}

.table-content{
	border-collapse:collapse;
}
.table-content td{
	padding:3px;	
}

hr
{
	margin:40px 0px;	
	border:1px dashed #999;

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

.noborder{
	border:none;	
}

.alignRight{
	text-align:right;	
}

</style>
</head>
<body>
<div class="container">
    
     <div style="margin-bottom:100px;"><!--Start of Form-->
     
     	<div style="font-weight:bolder; margin-bottom:10px;">
        	Income Statement<br />
			As of <?php echo date("m/d/Y",strtotime($from_date))?> to <?php echo date("m/d/Y",strtotime($to_date))?><br />
       	</div>
        <?php foreach($aProjects as $project_id){ ?>
        <b>Project: <?=$options->getAttribute('projects','project_id',$project_id,'project_name')?></b>
        <div class="content" style="margin:20px; 0px;" >
        	<table cellspacing="0" class="table-content">
                <tr>
                	<td style="font-weight:bold;">ACCOUNT</td>
                    <td style="font-weight:bold; text-align:center;">ACUTAL</td>
                    <td style="font-weight:bold; text-align:center;">BUDGET</td>
                </tr>
                <tr>
                    <td style="font-weight:bold;">REVENUE</td>
                    <td></td>
                </tr>
                
                <?php
                $query="
                    select 
                        *
                    FROM	
                       gchart as g left join sub_gchart as s on g.sub_mclass = s.sub_gchart_id
					where
						mclass='I'
					and enable='Y'
					order by sub_gchart asc
                ";
                $total_revenue = 0;
				$t_f_budget_revenue = 0;
				
				$t_revenue = 0;
				$t_budget = 0;
				
				$g_revenue = 0;
				$g_budget_revenue = 0;
				$sub_gchart = 'x';
                $result=mysql_query($query) or die(mysql_error());		
                ?>  
                <?php
                while($r=mysql_fetch_assoc($result)):
				
					if($sub_gchart != $r['sub_gchart']){
						#first hit
						
						if($sub_gchart != 'x'){
						?>
						<tr>
                            <td style="font-weight:bold;"></td>
                            <td class="alignRight" style="border-top:1px solid #000;"><span><?=number_format($t_revenue,2,'.',',')?></span></td>
                            <td class="alignRight" style="border-top:1px solid #000;"><span><?=number_format($t_budget,2,'.',',')?></span></td>
                        </tr>  
                        <?php
						$g_revenue += $t_revenue;
						$g_budget_revenue += $t_budget;
						$t_revenue = $t_budget = 0;
						}
						#second hit
						echo "
							<tr>
								<td style='font-weight:bold; text-decoration:underline; padding-left:10px;'>$r[sub_gchart]</td>
							</tr>
						";	
						$sub_gchart = $r['sub_gchart'];
						
					}
				
					$amount = getBalance($from_date,$to_date,$r['gchart_id'],$project_id,FALSE);
					$f_budget = getBudget($r['gchart_id'],$project_id);
					if($amount || $f_budget):
					$t_revenue += $amount;
					$t_budget += $f_budget;
					
				?>	
				<tr>
                    <td style="padding-left:20px;"><?=$r[gchart]?></td>
                    <td class="alignRight"><?=number_format($amount,2,'.',',')?></td>
                    <td class="alignRight"><?=number_format($f_budget,2,'.',',')?></td>
                </tr>	
				<?php	
					endif;
				endwhile;
                ?>
                <tr>
                    <td style="font-weight:bold;"></td>
                    <td class="alignRight" style="border-top:1px solid #000;"><span><?=number_format($t_revenue,2,'.',',')?></span></td>
                    <td class="alignRight" style="border-top:1px solid #000;"><span><?=number_format($t_budget,2,'.',',')?></span></td>
                </tr>  
				<?php
                $g_revenue += $t_revenue;
                $g_budget_revenue += $t_budget;
                $t_revenue = $t_budget = 0;
				?>
                <tr>
                	<td colspan="3"></td>
                </tr>
                <tr>
                    <td style="font-weight:bold;">TOTAL REVENUE</td>
                    <td class="alignRight"><span style="border-bottom:3px double #000; font-weight:bold;"><?=number_format($g_revenue,2,'.',',')?></span></td>
                    <td class="alignRight"><span style="border-bottom:3px double #000; font-weight:bold;"><?=number_format($g_budget_revenue,2,'.',',')?></span></td>
                </tr>  
                
                <tr>
                	<td colspan="3"></td>
                </tr>
                
                <tr>
                    <td style="font-weight:bold;">LESS : EXPENSES</td>
                    <td></td>
                </tr>
                
                <?php
                $query="
                    select 
                        *
                    FROM	
                       gchart as g left join sub_gchart as s on g.sub_mclass = s.sub_gchart_id
					where
						mclass='E'
					and enable='Y'
					order by sub_gchart asc
                ";
                
                $result=mysql_query($query) or die(mysql_error());		
				
                ?>  
                <?php
				$total_expense = 0;
				$t_f_budget_expense = 0;
				
				$t_expense = $t_budget_expense = 0;
				$g_expense = $g_budget_expense = 0;		
				
				$sub_gchart = 'x';
                while($r=mysql_fetch_assoc($result)):
				
					if($sub_gchart != $r['sub_gchart']){
						#first hit
						
						if($sub_gchart != 'x'){
						?>
						<tr>
                            <td style="font-weight:bold;"></td>
                            <td class="alignRight" style="border-top:1px solid #000;"><span><?=number_format($t_expense,2,'.',',')?></span></td>
                            <td class="alignRight" style="border-top:1px solid #000;"><span><?=number_format($t_budget_expense,2,'.',',')?></span></td>
                        </tr>  
                        <?php
						$g_expense += $t_expense;
						$g_budget_expense += $t_budget_expense;
						$t_expense = $t_budget_expense = 0;
						}
						#second hit
						echo "
							<tr>
								<td style='font-weight:bold; text-decoration:underline; padding-left:10px;'>$r[sub_gchart]</td>
							</tr>
						";	
						$sub_gchart = $r['sub_gchart'];
						
					}
				
					$amount = getProjectBalance($from_date,$to_date,$r['gchart_id'],NULL,TRUE); #null project
					$f_budget = getBudget($r['gchart_id'],$project_id);
					if($amount):
					$t_expense += $amount;
					$t_budget_expense += $f_budget;
				?>	
				<tr>
                    <td style="padding-left:20px;"><?=$r[gchart]?></td>
                    <td class="alignRight"><?=number_format($amount,2,'.',',')?></td>
                    <td class="alignRight"><?=number_format($f_budget,2,'.',',')?></td>
                </tr>	
				<?php	
					endif;
				endwhile;
                ?>
                <tr>
                    <td style="font-weight:bold;"></td>
                    <td class="alignRight" style="border-top:1px solid #000;"><span><?=number_format($t_expense,2,'.',',')?></span></td>
                    <td class="alignRight" style="border-top:1px solid #000;"><span><?=number_format($t_budget_expense,2,'.',',')?></span></td>
                </tr>  
				<?php
                $g_expense += $t_expense;
                $g_budget_expense += $t_budget_expense;
                $t_expense = $t_budget_expense = 0;
				?>
                
                <tr>
                	<td colspan="3"></td>
                </tr>
                <tr>
                    <td style="font-weight:bold;">TOTAL EXPENSES</td>
                    <td class="alignRight" style="border-top:1px solid #000;"><span><?=number_format($g_expense,2,'.',',')?></span></td>
                    <td class="alignRight" style="border-top:1px solid #000;"><span><?=number_format($g_budget_expense,2,'.',',')?></span></td>
                </tr>  
                
                <tr>
                	<td colspan="2"></td>
                </tr>
                
                <tr>
                    <td>TOTAL NET INCOME/LOSSES</td>
                    <td class="alignRight"><span style="border-bottom:3px double #000;"><?=number_format($g_revenue - $g_expense,2,'.',',')?></span></td>
                    <td class="alignRight"><span style="border-bottom:3px double #000;"><?=number_format($g_budget_revenue - $g_budget_expense,2,'.',',')?></span></td>
                </tr>
                
            </table>
           <!-- <table  class="noborder" style="border:none; margin-top:20px;">
            	<tr>
                	<td>Prepared by:</td>
                    <td>Checked by:</td>
                    <td>Approved by:</td>
                    <td>Released by:</td>
                    <td>Received by:</td>
                </tr>
            </table> -->
        </div><!--End of content-->
        <?php } ?>
        
        <?php if(count($aProjects) > 1){ ?>
        <b>TOTAL PROJECTS SELECTED</b>
        <div class="content" style="margin:20px; 0px;" >
        	<table cellspacing="0" class="table-content">
                <tr>
                	<td style="font-weight:bold;">ACCOUNT</td>
                    <td style="font-weight:bold; text-align:center;">ACUTAL</td>
                    <td style="font-weight:bold; text-align:center;">BUDGET</td>
                </tr>
                <tr>
                    <td style="font-weight:bold;">REVENUE</td>
                    <td></td>
                </tr>
                
                <?php
                $query="
                    select 
                        *
                    FROM	
                       gchart as g left join sub_gchart as s on g.sub_mclass = s.sub_gchart_id
					where
						mclass='I'
					and enable='Y'
					order by sub_gchart asc
                ";
                $t_revenue = 0;
				$t_budget = 0;
				
				$g_revenue = 0;
				$g_budget_revenue = 0;
				$sub_gchart = 'x';
                $result=mysql_query($query) or die(mysql_error());		
                while($r=mysql_fetch_assoc($result)):
					if($sub_gchart != $r['sub_gchart']){
						#first hit
						
						if($sub_gchart != 'x'){
						?>
						<tr>
                            <td style="font-weight:bold;"></td>
                            <td class="alignRight" style="border-top:1px solid #000;"><span><?=number_format($t_revenue,2,'.',',')?></span></td>
                            <td class="alignRight" style="border-top:1px solid #000;"><span><?=number_format($t_budget,2,'.',',')?></span></td>
                        </tr>  
                        <?php
						$g_revenue += $t_revenue;
						$g_budget_revenue += $t_budget;
						$t_revenue = $t_budget = 0;
						}
						#second hit
						echo "
							<tr>
								<td style='font-weight:bold; text-decoration:underline; padding-left:10px;'>$r[sub_gchart]</td>
							</tr>
						";	
						$sub_gchart = $r['sub_gchart'];
						
					}
				

					$amount = getBalance($from_date,$to_date,$r['gchart_id'],$gchart_projects,FALSE);
					$f_budget = getBudgetOfProjects($r['gchart_id'],$gchart_projects);
										
					if($amount || $f_budget):
					$t_revenue += $amount;
					$t_budget	+= $f_budget;
				?>	
				<tr>
                    <td style="padding-left:20px;"><?=$r[gchart]?></td>
                    <td class="alignRight"><?=number_format($amount,2,'.',',')?></td>
                    <td class="alignRight"><?=number_format($f_budget,2,'.',',')?></td>
                </tr>	
				<?php	
					endif;
				endwhile;
                ?>
				<tr>
                    <td style="font-weight:bold;"></td>
                    <td class="alignRight" style="border-top:1px solid #000;"><span><?=number_format($t_revenue,2,'.',',')?></span></td>
                    <td class="alignRight" style="border-top:1px solid #000;"><span><?=number_format($t_budget,2,'.',',')?></span></td>
                </tr>  
                <?php
                $g_revenue += $t_revenue;
                $g_budget_revenue += $t_budget;
                $t_revenue = $t_budget = 0;
				?>
                <tr>
                	<td colspan="3"></td>
                </tr>
                <tr>
                    <td style="font-weight:bold;">TOTAL REVENUE</td>
                    <td class="alignRight"><span style="border-bottom:3px double #000; font-weight:bold;"><?=number_format($g_revenue,2,'.',',')?></span></td>
                    <td class="alignRight"><span style="border-bottom:3px double #000; font-weight:bold;"><?=number_format($g_budget_revenue,2,'.',',')?></span></td>
                </tr>  
                <tr>
                	<td colspan="3"></td>
                </tr>
                
                <tr>
                    <td style="font-weight:bold;">LESS : EXPENSES</td>
                    <td></td>
                </tr>
                
                <?php
                $query="
                    select 
                        *
                    FROM	
                       gchart as g left join sub_gchart as s on g.sub_mclass = s.sub_gchart_id
					where
						mclass='E'
					and enable='Y'
					order by sub_gchart asc
                ";
                
                $result=mysql_query($query) or die(mysql_error());		
				
                ?>  
                <?php
				$total_expense = 0;
				$t_f_budget_expense = 0;
				
				$t_expense = $t_budget_expense = 0;
				$g_expense = $g_budget_expense = 0;		
				
				$sub_gchart = 'x';
                while($r=mysql_fetch_assoc($result)):
					
					if($sub_gchart != $r['sub_gchart']){
						#first hit
						
						if($sub_gchart != 'x'){
						?>
						<tr>
                            <td style="font-weight:bold;"></td>
                            <td class="alignRight" style="border-top:1px solid #000;"><span><?=number_format($t_expense,2,'.',',')?></span></td>
                            <td class="alignRight" style="border-top:1px solid #000;"><span><?=number_format($t_budget_expense,2,'.',',')?></span></td>
                        </tr>  
                        <?php
						$g_expense += $t_expense;
						$g_budget_expense += $t_budget_expense;
						$t_expense = $t_budget_expense = 0;
						}
						#second hit
						echo "
							<tr>
								<td style='font-weight:bold; text-decoration:underline; padding-left:10px;'>$r[sub_gchart]</td>
							</tr>
						";	
						$sub_gchart = $r['sub_gchart'];
						
					}

			
					$amount = getBalance($from_date,$to_date,$r['gchart_id'],NULL,TRUE);
					$f_budget = getBudgetOfProjects($r['gchart_id'],NULL);
					if($amount || $f_budget):
					$t_expense += $amount;
					$t_budget_expense	+= $f_budget;
				?>	
				<tr>
                    <td style="padding-left:20px;"><?=$r[gchart]?></td>
                    <td class="alignRight"><?=number_format($amount,2,'.',',')?></td>
                    <td class="alignRight"><?=number_format($f_budget,2,'.',',')?></td>
                </tr>	
				<?php	
					endif;
				endwhile;
                ?>
				<tr>
                    <td style="font-weight:bold;"></td>
                    <td class="alignRight" style="border-top:1px solid #000;"><span><?=number_format($t_expense,2,'.',',')?></span></td>
                    <td class="alignRight" style="border-top:1px solid #000;"><span><?=number_format($t_budget_expense,2,'.',',')?></span></td>
                </tr>  
				<?php
                $g_expense += $t_expense;
                $g_budget_expense += $t_budget_expense;
                $t_expense = $t_budget_expense = 0;
				?>
                <tr>
                	<td colspan="3"></td>
                </tr>
                 
                <tr>
                    <td style="font-weight:bold;">TOTAL EXPENSES</td>
                    <td class="alignRight" style="border-top:1px solid #000;"><span><?=number_format($g_expense,2,'.',',')?></span></td>
                    <td class="alignRight" style="border-top:1px solid #000;"><span><?=number_format($g_budget_expense,2,'.',',')?></span></td>
                </tr>  
                
                <tr>
                	<td colspan="2"></td>
                </tr>
                
                <tr>
                    <td>TOTAL NET INCOME/LOSSES</td>
                    <td class="alignRight"><span style="border-bottom:3px double #000;"><?=number_format($g_revenue - $g_expense,2,'.',',')?></span></td>
                    <td class="alignRight"><span style="border-bottom:3px double #000;"><?=number_format($g_budget_revenue - $g_budget_expense,2,'.',',')?></span></td>
                </tr>
                
            </table>
           <!-- <table  class="noborder" style="border:none; margin-top:20px;">
            	<tr>
                	<td>Prepared by:</td>
                    <td>Checked by:</td>
                    <td>Approved by:</td>
                    <td>Released by:</td>
                    <td>Received by:</td>
                </tr>
            </table> -->
        </div><!--End of content-->
        <?php } ?>
    </div><!--End of Form-->
   
</div>
</body>
</html>