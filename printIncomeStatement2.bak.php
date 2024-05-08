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
		$a[] = "'p-$list'";
	}
	$gchart_projects = implode(",",$a);
	
	
	#echo $projects;
	#echo "<br>";
	#echo $gchart_projects;
	//$month=$_REQUEST[month];
	//$year=$_REQUEST[year];	
	
	$options=new options();	
	
	function getBalance($from_date,$to_date,$gchart_id,$gchart_projects,$debit=TRUE){
			
		$query = "
			select
				sum(debit) as debit,
				sum(credit) as credit
			from
				gltran_header as h, gltran_detail as d
			where
				h.gltran_header_id = d.gltran_header_id
			and
				gchart_id = '$gchart_id'
			and
				date between '$from_date' and '$to_date'
			and
				status != 'C'
		";
		if($gchart_projects){
		$query.="
			and
				account_id in ($gchart_projects)
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
				gltran_header as h, gltran_detail as d
			where
				h.gltran_header_id = d.gltran_header_id
			and
				gchart_id = '$gchart_id'
			and
				date between '$from_date' and '$to_date'
			and
				status != 'C'
		";
		if($project_id){
		$query.="
			and
				account_id = 'p-$project_id'
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
                
                <?php $total_revenue = 0 ?>
                <tr>
                    <td style="font-weight:bold;">REVENUE</td>
                    <td></td>
                </tr>
                
                <?php
                $query="
                    select 
                        *
                    FROM	
                       gchart
					where
						mclass='I'
					and
						enable='Y'
                ";
                
                $result=mysql_query($query) or die(mysql_error());		
				
                ?>  
                <?php
                while($r=mysql_fetch_assoc($result)):
					$amount = getBalance($from_date,$to_date,$r['gchart_id'],$project_id,FALSE);
					$total_revenue += $amount;
										
					if($amount):
				?>	
				<tr>
                    <td style="padding-left:10px;"><?=$r[gchart]?></td>
                    <td class="alignRight"><?=number_format($amount,2,'.',',')?></td>
                </tr>	
				<?php	
					endif;
				endwhile;
                ?>
                <tr>
                    <td style="font-weight:bold;">TOTAL REVENUE</td>
                    <td class="alignRight"><span style="text-decoration:underline;"><?=number_format($total_revenue,2,'.',',')?></span></td>
                </tr>  
                
                <tr>
                	<td colspan="2"></td>
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
                       gchart
					where
						mclass='E'
					and
						enable='Y'
                ";
                
                $result=mysql_query($query) or die(mysql_error());		
				
                ?>  
                <?php
				$total_expense = 0;
                while($r=mysql_fetch_assoc($result)):
					$amount = getProjectBalance($from_date,$to_date,$r['gchart_id'],$project_id,TRUE);
					$total_expense += $amount;
					if($amount):
				?>	
				<tr>
                    <td style="padding-left:10px;"><?=$r[gchart]?></td>
                    <td class="alignRight"><?=number_format($amount,2,'.',',')?></td>
                </tr>	
				<?php	
					endif;
				endwhile;
                ?>
                
                <?php
				#DO BE COMMENTED IF ALL CV ARE FINISHED
				$query = "
					select 
						sum(amount) as amount, gchart, acode
					from 
						cv_header as h, cv_detail as d, gchart as g
					where
						h.cv_header_id = d.cv_header_id
					and
						d.gchart_id = g.gchart_id
					and
						h.status != 'C'
					and
						h.cv_date between '$from_date' and '$to_date'
				";
				if($project_id){
				$query.="
					and 
						d.project_id = $project_id
				";	
				}
				$query.="
					group by d.gchart_id
				";
				
				$result = mysql_query($query) or die(mysql_error());
				while($r = mysql_fetch_assoc($result)){
					$amount = $r['amount'];
					$total_expense += $amount;
                ?>
                <tr>
                    <td style="padding-left:10px;"><?=$r[gchart]?></td>
                    <td class="alignRight"><?=number_format($amount,2,'.',',')?></td>
                </tr>	
                <?php } #END ?>
                
                <tr>
                    <td style="font-weight:bold;">TOTAL EXPENSES</td>
                    <td class="alignRight"><span style="text-decoration:underline;"><?=number_format($total_expense,2,'.',',')?></span></td>
                </tr>  
                
                <tr>
                	<td colspan="2"></td>
                </tr>
                
                <tr>
                    <td>TOTAL NET INCOME/LOSSES</td>
                    <td class="alignRight"><span style="border-bottom:3px double #000;"><?=number_format($total_revenue - $total_expense,2,'.',',')?></span></td>
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
                
                <?php $total_revenue = 0 ?>
                <tr>
                    <td style="font-weight:bold;">REVENUE</td>
                    <td></td>
                </tr>
                
                <?php
                $query="
                    select 
                        *
                    FROM	
                       gchart
					where
						mclass='I'
					and
						enable='Y'
                ";
                
                $result=mysql_query($query) or die(mysql_error());		
				
                ?>  
                <?php
                while($r=mysql_fetch_assoc($result)):
					$amount = getBalance($from_date,$to_date,$r['gchart_id'],$gchart_projects,FALSE);
					$total_revenue += $amount;
										
					if($amount):
				?>	
				<tr>
                    <td style="padding-left:10px;"><?=$r[gchart]?></td>
                    <td class="alignRight"><?=number_format($amount,2,'.',',')?></td>
                </tr>	
				<?php	
					endif;
				endwhile;
                ?>
                <tr>
                    <td style="font-weight:bold;">TOTAL REVENUE</td>
                    <td class="alignRight"><span style="text-decoration:underline;"><?=number_format($total_revenue,2,'.',',')?></span></td>
                </tr>  
                
                <tr>
                	<td colspan="2"></td>
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
                       gchart
					where
						mclass='E'
					and
						enable='Y'
                ";
                
                $result=mysql_query($query) or die(mysql_error());		
				
                ?>  
                <?php
				$total_expense = 0;
                while($r=mysql_fetch_assoc($result)):
					$amount = getBalance($from_date,$to_date,$r['gchart_id'],$gchart_projects,TRUE);
					$total_expense += $amount;
					if($amount):
				?>	
				<tr>
                    <td style="padding-left:10px;"><?=$r[gchart]?></td>
                    <td class="alignRight"><?=number_format($amount,2,'.',',')?></td>
                </tr>	
				<?php	
					endif;
				endwhile;
                ?>
                
                <?php
				#TO BE DELETED ONCE CV ARE FINISHED
				$query = "
					select 
						sum(amount) as amount, gchart, acode
					from 
						cv_header as h, cv_detail as d, gchart as g
					where
						h.cv_header_id = d.cv_header_id
					and
						d.gchart_id = g.gchart_id
					and
						h.status != 'C'
					and
						h.cv_date between '$from_date' and '$to_date'
				";
				if($projects){
				$query.="
					and 
						d.project_id in ($projects)
				";	
				}
				$query.="
					group by d.gchart_id
				";
				
				$result = mysql_query($query) or die(mysql_error());
				while($r = mysql_fetch_assoc($result)){
					$amount = $r['amount'];
					$total_expense += $amount;
                ?>
                <tr>
                    <td style="padding-left:10px;"><?=$r[gchart]?></td>
                    <td class="alignRight"><?=number_format($amount,2,'.',',')?></td>
                </tr>	
                <?php } #END ?>
                
                <tr>
                    <td style="font-weight:bold;">TOTAL EXPENSES</td>
                    <td class="alignRight"><span style="text-decoration:underline;"><?=number_format($total_expense,2,'.',',')?></span></td>
                </tr>  
                
                <tr>
                	<td colspan="2"></td>
                </tr>
                
                <tr>
                    <td>TOTAL NET INCOME/LOSSES</td>
                    <td class="alignRight"><span style="border-bottom:3px double #000;"><?=number_format($total_revenue - $total_expense,2,'.',',')?></span></td>
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