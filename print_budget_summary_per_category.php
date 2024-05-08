<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
	
	$project_id=$_REQUEST[id];
	$from_date = $_REQUEST['from_date'];
	$to_date	= $_REQUEST['to_date'];
	
	
	function budget_monitoring($project_id) {
		$query="
			select
				h.work_category_id,
				work,
				sum(d.amount) as amount
			from
				budget_header as h,
				budget_detail as d,
				work_category as w
			where
				h.budget_header_id = d.budget_header_id
			and w.work_category_id = h.work_category_id
			and project_id = '$project_id'
			and h.status != 'C'
			and w.level = '1'
			group by h.work_category_id
		";
		$result = mysql_query($query) or die(mysql_error());
		$a = array();
		while( $r = mysql_fetch_assoc($result) ){
			$a[] = $r;	
		}
		
		return $a;
	}
	
	function versusBudget($project_id,$work_category_id,$from_date,$to_date) {
		$a = array();
		
		$result = mysql_query("
			select
				sum(amount) as amount
			from
				rr_header as h, rr_detail as d, po_header as po
			where
			 	h.rr_header_id = d.rr_header_id
			and h.po_header_id = po.po_header_id
			and h.status != 'C'
			and h.project_id = '$project_id'
			and work_category_id = '$work_category_id'
			and h.date between '$from_date' and '$to_date'
		") or die(mysql_error());
		$r = mysql_fetch_assoc($result);
		$a['mrr'] = $r['amount'];
		
		
		$result = mysql_query("
			select
				sum(amount) as amount
			from
				issuance_header as h, issuance_detail as d
			where
			 	h.issuance_header_id = d.issuance_header_id
			and status != 'C'
			and project_id = '$project_id'
			and work_category_id = '$work_category_id'
			and date between '$from_date' and '$to_date'
		") or die(mysql_error());
		$r = mysql_fetch_assoc($result);
		$a['ris'] = $r['amount'];
		return $a;
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>ORDER SHEET</title>
<script>
function printPage() { print(); } //Must be present for Iframe printing
</script>
<style type="text/css">
	
body
{
	size: legal portrait;		
	padding:0px;
	font-family:Arial, Helvetica, sans-serif;
	font-size:12px;
}
.container{
	width:100%;
}

.header
{
	text-align:center;	
	margin-top:20px;
}

.header table, .content table
{
	
	text-align:left;
	

}
.header table td, .content table td
{
	padding:3px;	
}

.content table{
	margin-top:10px;
	margin-left:30px;
	width:90%;
	border-collapse:collapse;
}
.content table td,.content table th{
	padding:3px;
}
.content table tr th{
	border-top:1px solid #000;
	border-bottom:1px solid #000;
	text-align:center;
}

.content table tr:last-child td{
	border-top:1px solid #000;
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

.footer td{
	border:none;
}
.align-right{
	text-align:right;	
}
.align-center{
	text-align:center;	
}

.costing-header{
	margin-left:10px;	
}

.last-content{
	page-break-after:always;  
} 

.content table td:nth-child(n+2){
	text-align:right;
}


</style>
</head>
<body>
<div class="container">
	<?php
		require("form_heading.php");
	?>
	
	<?php
	$query="
		select
			 *		  
		 from
			  projects
		 where
			project_id = '$project_id'

	";
	$result=mysql_query($query) or die(mysql_error());
	$r=mysql_fetch_assoc($result);
	
	$project_id		= $r['project_id'];
	$project_name	= $r['project_name'];
	$owner			= $r['owner'];
	$location		= $r['location'];
	?>

	<div class="header" style="font-weight:bold; margin-bottom:20px;">
        <table>
            <tr>
                <td>Project</td>
                <td>: <?=$project_name?></td>
            </tr>
            <tr>
              <td>Location</td>
              <td >: <?=$location?></td>
            </tr>
            <tr>
              <td>Owner</td>
              <td >: <?=$owner?></td>
            </tr>
        </table>
    </div>
	

    <div class="content" >   
        <table cellspacing="0" class='budget-table'>
            <tr>
                <th>CATEGORY</th>
                <th style="width:10%; text-align:right;">INHOUSE BUDGET</th>
                <th style="width:10%; text-align:right;">TOTAL MRR</th>
                <th style="width:10%; text-align:right;">TOTAL RIS</th>
                <th style="width:10%; text-align:right;">BALANCE VS MRR</th>
                <th style="width:10%; text-align:right;">BALANCE VS RIS</th>
            </tr>
            <?php
			$t_inhouse = $t_mrr = $t_ris = 0;
			$t_budget_mrr = 0;
			$t_budget_ris = 0;
			foreach(budget_monitoring($project_id) as $r) {
				$t_inhouse += $r['amount'];
				$aAmount = versusBudget($project_id,$r['work_category_id'],$from_date,$to_date);
				$t_mrr += $aAmount['mrr'];
				$t_ris += $aAmount['ris'];
				
				echo "
					<tr>
						<td>$r[work]</td>			
						<td>".number_format($r['amount'],2)."</td>		
							
						<td>".number_format($aAmount['mrr'],2)."</td>			
						<td>".number_format($aAmount['ris'],2)."</td>			
						
						
						<td>".number_format($r['amount'] - $aAmount['mrr'],2)."</td>			
						<td>".number_format($r['amount'] - $aAmount['ris'],2)."</td>			
						
						<td></td>			
					</tr>
				";	
			}
			echo "
				<tr>
					<td></td>			
					<td>".number_format($t_inhouse,2)."</td>			
					<td>".number_format($t_mrr,2)."</td>			
					<td>".number_format($t_ris,2)."</td>			
					<td></td>			
					<td></td>			
				</tr>
			";	
            ?>
               

           
        </table>
    </div><!--End of content-->
                
   
    
    </div>
</div>
</body>
</html>

