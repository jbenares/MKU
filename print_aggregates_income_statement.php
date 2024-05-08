<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
	$from_date		= $_REQUEST['from_date'];
	$to_date		= $_REQUEST['to_date'];	
	$project_id		= $_REQUEST['project_id'];
	
	function getProjects($project_id){
		
		$projects = array();
		if($project_id){
			$result = mysql_query("
				select * from projects where project_id = '$project_id' order by project_name asc
			") or die(mysql_error());
		}else{
			$result = mysql_query("
				select * from projects order by project_name asc
			") or die(mysql_error());
		}
		
		while( $r = mysql_fetch_assoc($result) ){
			$projects[] = $r;
		}
		
		return $projects;
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
	padding:0px;
	font-family:Arial, Helvetica, sans-serif;
	font-size:11px
}
.container{
	margin:0px auto;
}

.header
{
	margin:20px 0px;
}

.header table td
{
	border:none;	
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
table{
	border-collapse:collapse;	
}
</style>
</head>
<body>
<div class="container">
	
     <div><!--Start of Form-->
     	
        <?php foreach( getProjects($project_id) as $p ){  ?>
     	<div style="font-weight:bolder;">
        	AGGREGATES INCOME STATEMENT<br />
            <?=$p['project_name']?> <br />
            From <?=date("m/d/Y",strtotime($from_date))?> to <?=date("m/d/Y",strtotime($to_date))?>
        </div>           
        <div class="content" style="">
        	<table cellpadding="6">
            	<tr>
                	<th colspan="2" style="text-align:left;">REVENUE</th>
                </tr>
             	<?php
				$query="
					select
						sum(amount) as amount,
						stock
					from
						issuance_header as h, issuance_detail as d, productmaster as p, projects as pr
					where
						h.issuance_header_id = d.issuance_header_id
					and
						d.stock_id = p.stock_id
					and
						h.project_id = pr.project_id
					and
						h.status != 'C'
					and
						h.date between '$from_date' and '$to_date'
					and
						pr.project_id = '$p[project_id]' 
					and
						p.categ_id1 = '1'
					group by
						d.stock_id asc
				";					
				
				#echo $query;
				$result=mysql_query($query) or die(mysql_error());
				$total_amount = 0;
				$total_revenue = 0;
				while($r=mysql_fetch_assoc($result)){
					$stock	= $r['stock'];					
					$amount	= $r['amount'];
					
					$total_amount += $amount;
				?>	
                <tr>
                	<td style="width:40px;"></td>
                    <td nowrap="nowrap"><?=$stock?></td>
                    <td style="text-align:right;"><?=number_format($amount,2,'.',',')?></td>
                </tr>
				<?php 
				} 
				$total_revenue += $total_amount;
				?>
                
            	<tr>
                	<th colspan="2" style="text-align:left;">LESS: DIRECT COST</th>
                </tr>
             	<?php
				$query="
					select
						SUM(d.amount) as amount
					from
						rr_header as h, rr_detail as d, productmaster as p, projects as pr
					where
						h.rr_header_id = d.rr_header_id
					and
						d.stock_id = p.stock_id
					and
						h.project_id = pr.project_id
					and
						h.status != 'C'
					and
						h.date between '$from_date' and '$to_date'
					and
						p.categ_id1 = '1'
					and
						pr.project_id = '$p[project_id]'
				";	
								
				$result=mysql_query($query) or die(mysql_error());
				$total_amount = 0;
				$total_expenses = 0;
				while($r=mysql_fetch_assoc($result)){	
					$amount	= $r['amount'];
					$total_amount += $amount;
				?>	
                <tr>
                	<td style="width:40px;"></td>
                    <td nowrap="nowrap">COST OF SALES</td>
                    <td style="text-align:right;"><?=number_format($amount,2,'.',',')?></td>
                </tr>
				<?php
                } 
				$total_expenses += $total_amount;
				?>
                <?php
				#INSERT OTHER EXPENSES HERE
                ?>
                
                <tr>
                	<td style="width:40px; border-top:1px solid #000;" ></td>
                	<td style="border-top:1px solid #000; font-weight:bold;">TOTAL INCOME</td>
                    <td style="font-weight:bold; border-top:1px solid #000; border-bottom:3px double #000;"><?=number_format($total_revenue-$total_expenses,2,'.',',')?></td>
                </tr>
            </table>            
        </div><!--End of content-->
        <div style="margin:5px;"></div>
        <?php } #END FOREACH ?>
    </div><!--End of Form-->
</div>
</body>
</html>