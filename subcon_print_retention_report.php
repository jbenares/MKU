<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	require_once(dirname(__FILE__).'/library/lib.php');

	//$date			= $_REQUEST['date'];
	$from			= $_REQUEST['from'];
	$to				= $_REQUEST['to'];
	$supplier_id	= $_REQUEST['supplier_id'];
	$project_id		= $_REQUEST['project_id'];
    $work_category_id		= $_REQUEST['work_category_id'];
    $sub_word_category_id		= $_REQUEST['sub_word_category_id'];
	
	$options=new options();	
	
	function getSupName($supplier_id){
		$sql = mysql_query("Select account from supplier where account_id = '$supplier_id' ") or die (mysql_error());
		$r = mysql_fetch_assoc($sql);
		
		return $r['account'];
	}
	
	function getProjName($project_id){
		$sql = mysql_query("Select project_name from projects where project_id = '$project_id'") or die (mysql_error());
		$r = mysql_fetch_assoc($sql);
		
		return $r['project_name'];
	}
	
	$sql = mysql_query(" select 
                        *
                    FROM	
                       	po_header as h, supplier as s
					where
						h.supplier_id = s.account_id
					and status != 'C'
					and po_type = 'S'
					and date between '$from' and '$to'
					and s.subcon = '1'
					and s.account_id = '$supplier_id'
					order by h.project_id
					") or die (mysql_error());
					
	function getPOAmount($po_header_id){
		$result = mysql_query("
			select 
				sum(amount) as amount
			from
				po_header as h, spo_detail as d, sub_spo_detail as s
			where
				h.po_header_id = d.po_header_id
			and
				d.spo_detail_id = s.spo_detail_id
			and
				h.po_header_id = '$po_header_id'
			and
				h.`status` != 'C' 			
		") or die(mysql_error());	
		$r = mysql_fetch_assoc($result);
		
		return $r['amount'];
	}
	
	function getAccomplishmentsAmount($po_header_id){
		$result = mysql_query("
			select
				sum(amount) as amount
			from
				sub_apv_header as h, sub_apv_detail as d
			where
				h.sub_apv_header_id = d.sub_apv_header_id
			and po_header_id = '$po_header_id'
			and h.status != 'C'
		") or die(mysql_error());
		$r = mysql_fetch_assoc($result);
		
		return $r['amount'];
	}	
	
	function getPayments($po_header_id){
		
		$result = mysql_query("
			select
				h.cv_header_id, d.amount, h.check_date
			from
				cv_header as h, 
				cv_detail as d, 
				sub_apv_header as s
			where
				h.cv_header_id = d.cv_header_id
			and
				h.sub_apv_header_id = s.sub_apv_header_id
			and
				s.po_header_id = '$po_header_id'
			and
				h.status != 'C'
			and
				s.status != 'C' order by h.check_date
		") or die(mysql_error());
		while($r = mysql_fetch_assoc($result)){
		$arr[] = $r['amount'];
		}
		
		return $arr;
	}	
	
	function getRetention($po_header_id){
		
		$sql = mysql_query("select
				*
			from
				cv_header as h, 
				cv_detail as d, 
				sub_apv_header as s,
				gltran_header as gh,
				gltran_detail as gd
			where
				h.cv_header_id = d.cv_header_id
			and
				h.sub_apv_header_id = s.sub_apv_header_id
			and	
				h.cv_header_id = gh.header_id
			and
				gh.gltran_header_id = gd.gltran_header_id
			and	
				gh.header = 'cv_header_id'
			and
				s.po_header_id = '$po_header_id'
			and
				h.status != 'C'
			and
				s.status != 'C'
			and
				gh.`status` != 'C'
			and
				h.retention_gchart_id = gd.gchart_id order by h.check_date
				") or die (mysql_error());
		
		while($r = mysql_fetch_assoc($sql)){
			$arr2[] = $r['credit'];
		}
		
		return $arr2;
	}	
	
	function getRefer($po_header_id){
		$sql = mysql_query("select
				*
			from
				cv_header as h, 
				cv_detail as d, 
				sub_apv_header as s,
				gltran_header as gh,
				gltran_detail as gd
			where
				h.cv_header_id = d.cv_header_id
			and
				h.sub_apv_header_id = s.sub_apv_header_id
			and	
				h.cv_header_id = gh.header_id
			and
				gh.gltran_header_id = gd.gltran_header_id
			and	
				gh.header = 'cv_header_id'
			and
				s.po_header_id = '$po_header_id'
			and
				h.status != 'C'
			and
				s.status != 'C'
			and
				gh.`status` != 'C'
			and
				h.retention_gchart_id = gd.gchart_id order by h.check_date") or die (mysql_error());
		while($r = mysql_fetch_assoc($sql)){
			$arr3[] = $r['generalreference'];
		}
		
		return $arr3;
	}
	
	function getRefer2($po_header_id){
		$sql = mysql_query("select
			 h.cv_header_id
			from
				cv_header as h, 
				cv_detail as d, 
				sub_apv_header as s
			where
				h.cv_header_id = d.cv_header_id
			and
				h.sub_apv_header_id = s.sub_apv_header_id
			and
				s.po_header_id = '$po_header_id'
			and
				h.status != 'C'
			and
				s.status != 'C' order by h.check_date") or die (mysql_error());
		while($r = mysql_fetch_assoc($sql)){
			$arr4[] = $r['cv_header_id'];
		}
		
		return $arr4;
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
	font-size:11px;
	letter-spacing:1px;
}
.container{
	width: 100%;
	height: 100%;
	margin: 0 auto;
	text-align: center;
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
	border-collapse:collapse;
}
.content table td,.content table th{
	/*border:1px solid #000;*/
	padding:10px;
}
.withborder td,.withborder th{
	border:1px solid #000;
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
	width: 1000px;
}

.alignRight{
	text-align:right;	
}

</style>
</head>
<body>
<div class="container">
    
     <div style="margin-bottom:100px;"><!--Start of Form-->
     
     	<?php
			require("form_heading.php");
        ?>

        <div style="text-align:center; font-size:14px; margin-bottom:20px;">
           	SUBCONTRACTOR RETENTION REPORT<br />
			<span><?=getSupName($supplier_id)?></span><br />
			<span style="font-size:8px; font-style:italic;">From <?=date("F j, Y",strtotime($from))?> to <?=date("F j, Y",strtotime($to))?></span>
        </div>   
        <div class="content" >
            <table  class="noborder" border="1" width="100%" style="margin: auto;">
				<thead>
				<tr>
					<td>PO #</td>
					<td>Project</td>
					<td>PO Amount</td>
					<td>Accomplishments</td>
					<td>References</td>
					<td>Payments</td>
					<td>Retention</td>
					<td>Balance</td>
				</tr>
				</thead>
				<?php while($r = mysql_fetch_assoc($sql)){ 
				$accomp = 0;
				$po_header_id = $r['po_header_id'];
				$arr = getPayments($po_header_id);
				$arr2 = getRetention($po_header_id);
				$arr3 = getRefer($po_header_id);
				$arr4 = getRefer2($po_header_id);
				$accomp = getAccomplishmentsAmount($r['po_header_id']);
				$total_payment = 0;
				$total_retention = 0;
				$po_amount = 0;
				$balance = 0;
				
				$po_amount = getPOAmount($r['po_header_id']);
				?>
				<tr>
					<td><?=str_pad($r['po_header_id'],7,0,STR_PAD_LEFT)?></td>
					<td><?=getProjName($r['project_id'])?></td>
					<td style="text-align: right;"><?=number_format($po_amount,2)?></td>
					<td style="text-align: right;">
						<?php if($accomp > 0 || $accomp < 0){ ?>
							<?=number_format($accomp,2)?>
						<?php } ?>
					</td>
					<td>
						<?php
						
							if(!empty($arr3)){
								
								foreach($arr3 as $value3){
									echo $value3,"<br />";
								}
							}else if(!empty($arr4)){
								foreach($arr4 as $value4){
									echo "CV # : ",str_pad($value4,7,0,STR_PAD_LEFT),"<br />";
								}								
								
							}
						?>
						<br />
					</td>					
					<td style="text-align: right;">
						<?php 
							foreach($arr as $value){
								$total_payment += $value;
								echo number_format($value,2), "<br />";
							}
							
							$balance = $po_amount - $total_payment;
						?>
						<?php if($total_payment > 0 || $total_payment < 0){ ?>
							<div style="border-top: 1px solid black;"><?=number_format($total_payment,2)?></div>
						<?php } ?>
					</td>
					<td style="text-align: right;">
						<?php 
							foreach($arr2 as $value2){
								$total_retention += $value2;
								echo number_format($value2,2), "<br />";
							}
						?>
						<?php if($total_retention > 0 || $total_retention < 0){ ?>
						<div style="border-top: 1px solid black;"><?=number_format($total_retention,2)?></div>
						<?php } ?>
					</td>
					<td style="text-align: right;">
					<?php if($balance > 0 || $balance < 0){ ?>
						<?=number_format($balance,2)?>
					<?php } ?>
					</td>
				</tr>
				<?php } ?>
            </table>
			<br />
			<br />
			<br />
			<table width="100%" style="margin: auto; text-align: center;" >
				<tr>
                	<td>Prepared by:</td>
                    <td>Checked by:</td>
                    <td>Approved by:</td>
                    <td>Released by:</td>
                    <td>Received by:</td>
                </tr>
			</table>
        </div><!--End of content-->
    </div><!--End of Form-->
   
</div>
</body>
</html>