<?php
#echo "This module is being maintained. Please wait a few minutes.";
require_once('../my_Classes/options.class.php');
include_once("../conf/ucs.conf.php");

$options=new options();	
$from_date		= $_REQUEST['from_date'];
$to_date		= $_REQUEST['to_date'];
$project_id		= $_REQUEST['project_id'];	
$filter 		= $_REQUEST['filter'];	 #1 - projects and clients, 2 - dbcci projects
$project 		= $options->getAttribute("projects","project_id",$project_id,"project_name");
$stock_id		= $_REQUEST['stock_id'];
	
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
td{
	vertical-align:top;
}	
.subtotal td{
	border-top:1px solid #000;
	font-weight:bold;	
}

.grandtotal td{
	border-top:3px double #000;
	font-weight:bold;	
}

</style>
</head>
<body>
<div class="container">
	
     <div><!--Start of Form-->
     	<div style="font-weight:bolder;">
        	PREMIX DELIVERY REPORT<br />
            <?=$company_address?><br/>
            <?=$project?> <br /> 
           From <?=date("m/d/Y",strtotime($from_date))?> to <?=date("m/d/Y",strtotime($to_date))?>
        </div>           
        <div class="content" style="">
      <table>    
		 <tr>
        <th>DATE</th>		 
        <th style="text-align:left;">PREMIX DR#</th>
		<th style="text-align:left;">REFERENCE</th>
        <th style ="text-align:left;">PROEJECT</th>
        <th>PREMIX</th>
		<th>VOLUME</th>
		<th>UNIT COST</th>
		<th>PUMCRETE COST</th>
		<th style ="text-align:right;">AMOUNT</th>
    </tr>  
    <?php	
    $query = "
					select
						*
					from
						premix_delivery as b, productmaster as p
					where
						b.premix_id = p.stock_id
				";
				if(!empty($stock_id)){
				$query.="
					and p.stock_id = '$stock_id'
				";		
				}
				if(!empty($project_id)){
				$query.="
					and project_id = '$project_id'
				";		
				}
				
				if($filter == 1){#projects and clients
				$query .= "
				and project_id in (select project_id from projects where client_project = '1')
				";
				}else if($filter == 2){#dbcci projects
				$query .= "
				and project_id in (select project_id from projects where client_project = '0')
				";
				}
				
				if(!empty($from_date) && !empty($to_date)){
				$query.="
					and date between '$from_date' and '$to_date'
				";		
				}
				
				$query .= "
					order by reference asc, date asc
				";
				
               $result = mysql_query($query) or die(mysql_error());
			   
			#echo $query;
        while( $r = mysql_fetch_assoc($result) ){
			$total_volume += $r['volume'];
			$amount = ($r['price'] + $r['pumpcrete_cost']) * $r['volume'];
			$total_amount += $amount;
		
	    echo '<td>'.date("m/d/Y",strtotime($r['date'])).'</td>';	
		echo '<td>'.str_pad($r['premix_delivery_id'],7,0,STR_PAD_LEFT).'</td>';	
		echo '<td> PD # '.$r['reference'].'</td>';	
		echo '<td>'.$options->getAttribute('projects','project_id',$r['project_id'],'project_name').'</td>';	
		echo '<td>'.$options->getAttribute('productmaster','stock_id',$r['stock_id'],'stock').'</td>';
        echo '<td style="text-align:right;">'.$r['volume'].'</td>';
		echo '<td style="text-align:center;">'.number_format(($r['price']),2).'</td>';
		echo '<td style="text-align:center;">'.$r['pumpcrete_cost'].'</td>';
        echo '<td style="text-align:right;">'.number_format(($amount),2).'</td>';			
        echo '</tr>';
		
    }
    ?>	
	 <tr>
        <th style ="text-align:left;">TOTAL</th>		 
        <th></th>
		<th></th>
        <th></th>
        <th></th>
		<th style ="text-align:right;"><?=number_format(($total_volume),4)?></th>
		<th></th>
		<th></th>
		<th style ="text-align:right;"><?=number_format(($total_amount),2)?></th>
    </tr>  
   </table>     
        </div><!--End of content-->
    </div><!--End of Form-->
</div>
</body>
</html>