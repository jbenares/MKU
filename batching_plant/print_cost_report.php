<?php
#echo "This module is being maintained. Please wait a few minutes.";
	require_once('../my_Classes/options.class.php');
	include_once("../conf/ucs.conf.php");
	
	$options=new options();	
	$from_date		= $_REQUEST['from_date'];
	$to_date		= $_REQUEST['to_date'];
	$project_id		= $_REQUEST['project_id'];	
	$po_header_id	= $_REQUEST['po_header_id'];
	$driverID		= $_REQUEST['driverID'];
	$stock_id		= $_REQUEST['stock_id'];
	$eur_income_id	= $_REQUEST['eur_income_id'];
	$unit			= $_REQUEST['unit'];
	$eur_ref_id		= $_REQUEST['eur_ref_id'];	
	$filter 		= $_REQUEST['filter'];	 #1 - projects and clients, 2 - dbcci projects
	
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
.content table td:nth-child(n+2),.content table th:nth-child(n+2){
	text-align:right;	
}
.content table tr:last-child td{
	border-top:1px solid #000;
	font-weight:bold;	
}

</style>
</head>
<body>
<div class="container">
	
     <div><!--Start of Form-->
     	<div style="font-weight:bolder;">
        	<?=$title?><br />
            <?=$company_address?>
            <?php
			if(!empty($from_date) && !empty($to_date)){
				echo "<br>".date("m/d/Y",strtotime($from_date))." - ".date("m/d/Y",strtotime($to_date));
			}
            ?>
            
        </div>           
        <div class="content" style="">
        	<table cellpadding="6">
                <?php
				$query = "
					select
						sum(cement) as cement,
						sum(cement * cement_price) as cement_amount,
						sum(wsand) as wsand,
						sum(wsand * wsand_price) as wsand_amount,
						sum(agg_g1) as agg_g1,
						sum(agg_g1 * agg_g1_price) as agg_g1_amount,
						sum(agg_34) as agg_34,
						sum(agg_34 * agg_34_price) as agg_34_amount,
						sum(agg_38) as agg_38,
						sum(agg_38 * agg_38_price) as agg_38_amount,
						sum(admix) as admix,
						sum(admix * admix_price) as admix_amount,
						sum(water) as water,
						sum(water * water_price) as water_amount,
						sum(electricity) as electricity,
						sum(electricity * electricity_price) as electricity_amount,
						
						sum(cement_price) as cement_price,
						sum(wsand_price) as wsand_price,
						sum(agg_g1_price) as agg_g1_price,
						sum(agg_34_price) as agg_34_price,
						sum(agg_38_price) as agg_38_price,
						sum(admix_price) as admix_price,
						sum(water_price) as water_price,
						sum(electricity_price) as electricity_price

					from
						batch_prod
					where
						1 = 1
				";
				
				if(!empty($from_date) && !empty($to_date)){
				$query.="
					and date between '$from_date' and '$to_date'
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
				
				$result = mysql_query($query) or die(mysql_error());
				$r = mysql_fetch_assoc($result);
				?>
                <tr>
                	<th>DESCRIPTION</th>
                    <th>QUANTITY</th>
                    <th>AMOUNT</th>
                </tr>
                <tr>
                	<td>Cement (40kg bag)</td>
               		<td><?=number_format($r['cement'],2)?></td>
                    <td><?=number_format($r['cement_amount'],2)?></td>
              	</tr>
                <tr>
                    <td>3/8" Gravel (cu.m)</td>
                    <td><?=number_format($r['agg_38'],2)?></td>
                    <td><?=number_format($r['agg_38_amount'],2)?></td>
              	</tr>
                <tr>
                    <td>3/4" Gravel (cu.m)</td>
                    <td><?=number_format($r['agg_34'],2)?></td>
                    <td><?=number_format($r['agg_34_amount'],2)?></td>
              	</tr>
                <tr>
                    <td>G-1" Gravel (cu.m)</td>
                    <td><?=number_format($r['agg_g1'],2)?></td>
                    <td><?=number_format($r['agg_g1_amount'],2)?></td>
              	</tr>
                <tr>
                    <td>W. Sand (cu.m)</td>
                    <td><?=number_format($r['wsand'],2)?></td>
                    <td><?=number_format($r['wsand_amount'],2)?></td>
              	</tr>
                <tr>
                    <td>Admixture (Li)</td>
                    <td><?=number_format($r['admix'],2)?></td>
                    <td><?=number_format($r['admix_amount'],2)?></td>
              	</tr>
                <tr>
                    <td>Water (cu.m)</td>
                    <td><?=number_format($r['water'],2)?></td>
                    <td><?=number_format($r['water_amount'],2)?></td>
              	</tr>
                <tr>
                    <td>Electricity (kwh)</td>
                    <td><?=number_format($r['electricity'],2)?></td>
                    <td><?=number_format($r['electricity_amount'],2)?></td>
              	</tr>
                <?php
				$t_qty = $r['cement'] + $r['agg_38'] + $r['agg_34'] + $r['agg_g1'] + $r['wsand'] + $r['admix'] + $r['water'] + $r['electricity'];
				$t_amount = $r['cement_amount'] + $r['agg_38_amount'] + $r['agg_34_amount'] + $r['agg_g1_amount'] + $r['wsand_amount'] + $r['admix_amount'] + $r['water_amount'] + $r['electricity_amount'];
                ?>
                
                <tr>
                	<td></td>
                    <td></td>
                    <td><?=number_format($t_amount,2)?></td>
                </tr>
            </table>
        
        </div><!--End of content-->
    </div><!--End of Form-->
</div>
</body>
</html>