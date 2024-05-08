<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
	$from_date		= $_REQUEST['from_date'];
	$to_date		= $_REQUEST['to_date'];
	$project_id		= $_REQUEST['project_id'];
	$project = $options->getAttribute("projects","project_id",$project_id,"project_name");	
	$work_category_id		= $_REQUEST['work_category_id'];
	$sub_work_category_id	= $_REQUEST['sub_work_category_id'];
	$supplier_id			= $_REQUEST['supplier_id'];
	
	function getTotalAmount($apv_header_id){
		$result = mysql_query("
			select sum(amount) as total from apv_detail where apv_header_id = '$apv_header_id'
		") or die(mysql_error());
		$r = mysql_fetch_assoc($result);
		return $r['total'];
	}
	
	function hasPayment($apv_header_id){
		$result = mysql_query("
			select
				*
			from
				cv_header as h, cv_detail as d
			where
				h.cv_header_id = d.cv_header_id
			and
				h.status != 'C'
			and
				d.apv_header_id = '$apv_header_id'
		") or die(mysql_error());
		
		if( mysql_num_rows($result) > 0 ){
			return true;	
		} else { 
			return false;
		}
	}

	
	#echo "DRIVER ID : $driverID ; $equipment_id";
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
 a{
	color:#000;
	text-decoration:none; 
 }
 a:hover{
	text-decoration:underline; 
 }
 
 table tr:last-child td{
	text-align:right;
	font-weight:bold;
	border-top:1px solid #000; 
 }
</style>


<link rel="stylesheet" type="text/css" href="css/print.css"/>
</head>
<body>
<div class="container">
	
     <div><!--Start of Form-->
     	
     	<div style="font-weight:bolder;">
        	APV HISTORY REPORT <br />
            <?=$project?> <br />
            From <?=date("m/d/Y",strtotime($from_date))?> to <?=date("m/d/Y",strtotime($to_date))?>
        </div>           
        <div class="content" style="">
        	<table cellpadding="6">
            	<tr>
                    <th style="width:5%; text-align:left;">APV DATE</th>
                    <!--<th style="width:5%; text-align:left;">DUE DATE</th>-->
                    <th style="width:5%; text-align:left;">APV#</th>
                    <th style="width:5%; text-align:left;">PO#</th>
                    <th style="text-align:left;">PROJECT</th>
                    <th style="text-align:left;">SCOPE OF WORK</th>
                    <th style="text-align:left;">SUPPLIER</th>
                    <th style="width:5%; text-align:left;">TERMS</th>
                    <th style="text-align:right;">AMOUNT</th>
                </tr>	
                
             	<?php
					$query="
						select
							vatable,
							w_tax,
							apv_header_id,
							discount_amount,
							date,
							po_header_id,
							project_id,
							work_category_id,
							sub_work_category_id,
							supplier_id,
							terms							
						from	
							apv_header 
						where
							1 = 1
					";
					
					if($project_id){
					$query.="
						and
							project_id = '$project_id'
					";	
					}
					
					if($supplier_id){
					$query.="
						and
							supplier_id = '$supplier_id'
					";	
					}
					
					$query.="
						and date between '$from_date' and '$to_date'
					";
					
					$query.="
						order by
							date asc
					";
					$result=mysql_query($query) or die(mysql_error());
					$sub_total_amount = 0;
					while($r=mysql_fetch_assoc($result)){
						
						if(!hasPayment($r['apv_header_id'])){
						$vatable				= $r['vatable'];
						$w_tax					= $r['w_tax'];
						
						
						$gross_amount = $total_amount = getTotalAmount($r['apv_header_id']);
						$total_amount -= $r['discount_amount'];
						
						$vatable_amount = ($vatable) ? $totalamount / 1.12 : 0;
						$vat = $vatable_amount * 0.12;
						
						if($vatable_amount){
							$witholding_tax_amount = $vatable_amount * ($w_tax / 100);
						}else{
							$witholding_tax_amount = $total_amount * ($w_tax / 100);
						}
						$net_amount = $total_amount - $witholding_tax_amount;
						$sub_total_amount += $net_amount;
				?>	
                        <tr>
                            <td><?=date("m/d/Y",strtotime($r['date']))?></td>
                            <!--<td><?=date("m/d/Y",strtotime($r['due_date']))?></td>-->
							<td><?=str_pad($r['apv_header_id'],7,0,STR_PAD_LEFT)?></td>
                            <td><?=str_pad($r['po_header_id'],7,0,STR_PAD_LEFT)?></td>
                            <td><?=$options->getAttribute('projects','project_id',$r['project_id'],'project_name')?></td>
                            <td><?=$options->getAttribute('work_category','work_category_id',$r['work_category_id'],'work')?> <?=$options->getAttribute('work_category','work_category_id',$r['sub_work_category_id'],'work')?></td>
                            <td><?=$options->getAttribute('supplier','account_id',$r['supplier_id'],'account')?></td>
                            <td><?=$r['terms']?></td>
                            <td style="text-align:right;"><?=number_format($net_amount,2)?></td>
                      	</tr>
                      <?php } ?>
				<?php } ?>
                	<tr>
                    	<td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <!--<td></td>-->
                        <td></td>
                        <td></td>
                        <td></td>
                        <td style="text-align:right;"><?=number_format($sub_total_amount,2)?></td>
                    </tr>
            </table>
        
        </div><!--End of content-->
    </div><!--End of Form-->
</div>
</body>
</html>