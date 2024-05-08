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

function hasAmount($project_id,$from_date,$to_date){
	$query = "
		select
			sum(total_vol * price_unit) as amount
		from
			batch_prod as b
		where
			project_id = '$project_id'
	";
	
	if(!empty($from_date) && !empty($to_date)){
	$query.="
		and date between '$from_date' and '$to_date'
	";		
	}
	
	$result = mysql_query($query) or die(mysql_error());
	$r = mysql_fetch_assoc($result);
	//return true;
	return ($r['amount'] > 0) ? true : false;
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
        	<?=$title?><br />
            <?=$company_address?>
           <!-- <?=$project?> <br /> -->
        </div>           
        <div class="content" style="">
        	<table cellpadding="6">
            	<tr>
	                <th style="text-align:left; width:5%;">DATE</th>
	                <th style="text-align:right;">PROD #</th>
                  	<th style="text-align:right;">VOL.(CU.M)</th>
                    <th style="text-align:left;">SPECIFICATION</th>
                    <th style="text-align:right;">COST/CU.M</th>
                    <th style="text-align:right;">AMOUNT</th>
                    <th style="text-align:right;">CEMENT</th>
                    <th style="text-align:right;">W.SAND</th>
                    <th style="text-align:right;">AGG.G-1</th>
                    <th style="text-align:right;">AGG. 3/4"</th>
                    <th style="text-align:right;">AGG. 3/8"</th>
                    <th style="text-align:right;">ADMIX</th>
                    <th style="text-align:right;">WATER</th>
                    <th style="text-align:right;">ELECTRICITY</th>
                    <th style="text-align:left;">REMARKS</th>
                    <?php if($filter == 1) echo "<th style='text-align:left;'>BILLING</th>"; ?>
                </tr>
                <?php
				# do not include PATAG  AND D.S BENEDICTO
				$query = "
					select
						*
					from
						batch_prod as b, productmaster as p
					where
						b.stock_id = p.stock_id
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
				
				if(!empty($driverID)){
				$query.="
					and eq_operator_id = '$driverID'
				";		
				}
				
				$query .= "
					order by project_id asc, date asc
				";
				
				#echo $query;
				
				$result = mysql_query($query) or die(mysql_error());
				$i = 1;
				
				$subtotal_gross = $subtotal_mechanic = $subtotal_net = 0;
				
				$filter_project_id = $filter_stock_id = 'x';
				
				$t_vol = $t_cost = $t_amount = 0;
				$g_vol = $g_cost = $g_amount = 0;
				$t_cement = $t_wsand = $t_agg_g1 = $t_agg_34 = $t_agg_38 = $t_admix = $t_water = $t_electricity = 0;
				$g_cement = $g_wsand = $g_agg_g1 = $g_agg_34 = $g_agg_38 = $g_admix = $g_water = $g_electricity = 0;
				
				$flag_display_subtotal = 0;
				while( $r = mysql_fetch_assoc($result) ){
					
					
					if($filter == "1" || 1) { # per project always per project
						$r['project_id'] = (empty($r['project_id'])) ? '0' : $r['project_id'];
					
						if(empty($r['project_id']) && $filter_project_id != $r['project_id'] ){
							$filter_project_id = $r['project_id'];
							echo "
								<tr>
									<td colspan='13' style='font-weight:bold;'>NO PROJECT SPECIFIED</td>
								</tr>
							";	
							$flag_display_subtotal = 1;
						}else if($filter_project_id != $r['project_id']){
							
							if(!hasAmount($r['project_id'],$from_date,$to_date)){
								continue;	
							}
							
							#display total
							if($flag_display_subtotal){ #if it has subtotal
								echo "
									<tr class='subtotal'>									
										<td></td>
										<td></td>
										<td style='text-align:right;'>".number_format($t_vol,2)."</td>
										<td></td>
										<td style='text-align:right;'></td>
										<td style='text-align:right;'>".number_format($t_amount,2)."</td>
										
										<td style='text-align:right;'>".number_format($t_cement,2)."</td>
										<td style='text-align:right;'>".number_format($t_wsand,2)."</td>
										<td style='text-align:right;'>".number_format($t_agg_g1,2)."</td>
										<td style='text-align:right;'>".number_format($t_agg_34,2)."</td>
										<td style='text-align:right;'>".number_format($t_agg_38,2)."</td>
										<td style='text-align:right;'>".number_format($t_admix,2)."</td>
										<td style='text-align:right;'>".number_format($t_water,2)."</td>
										<td style='text-align:right;'>".number_format($t_electricity,2)."</td>
										<td style='text-align:left;'></td>
										".( ($filter == 1) ? "<td style='text-align:left;'></td>" : "" )." 
									</tr>
								";
							}
							#add to grand total
							$g_vol 		+= $t_vol;
							$g_cost		+= $t_cost;
							$g_amount 	+= $t_amount;
							
							$g_cement 	+= $t_cement;
							$g_wsand 	+= $t_wsand;
							$g_agg_g1	+= $t_agg_g1;
							$g_agg_34	+= $t_agg_34;
							$g_agg_38	+= $t_agg_38;
							$g_admix	+= $t_admix;
							$g_water	+= $t_water;
							$g_electricity	+= $t_electricity;
							
							$t_vol = $t_cost  = $t_cost = $t_amount =  0;
							$t_cement = $t_wsand = $t_agg_g1 = $t_agg_34 = $t_agg_38 = $t_admix = $t_water = $t_electricity = 0;
							#end of display subtotal
							
							//check next project if to continue
							
							echo "
								<tr>
									<td colspan='13' style='font-weight:bold;'>".$options->getAttribute('projects','project_id',$r['project_id'],'project_name')."</td>
								</tr>
							";	
							$filter_project_id = $r['project_id'];
							$flag_display_subtotal = 1;
						}
						
					} else { #per equipment
						$r['stock_id'] = (empty($r['stock_id'])) ? '0' : $r['stock_id'];
					
						if(empty($r['stock_id']) && $filter_stock_id != $r['stock_id'] ){
							$filter_stock_id = $r['stock_id'];
							echo "
								<tr>
									<td colspan='19' style='font-weight:bold;'>NO ITEM SPECIFIED</td>
								</tr>
							";	
							
							$flag_display_subtotal = 1;
							
						}else if($filter_stock_id != $r['stock_id']){
							
							#display total
							if($flag_display_subtotal){
								echo "
								<tr class='subtotal'>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td style='text-align:right;'>".number_format($t_km,2)."</td>
									<td style='text-align:right;'>".number_format($t_sqm,2)."</td>
									<td style='text-align:right;'>".number_format($t_cum,2)."</td>
									<td></td>
									<td style='text-align:right;'>".number_format($t_actual_hrs,2)."</td>
									<td style='text-align:right;'>".number_format($t_charged_hrs,2)."</td>
									<td style='text-align:right;'></td>
									<td style='text-align:right;'>".number_format($t_charge_amount,2)."</td>
									<td style='text-align:right;'></td>
									<td style='text-align:right;'>".number_format($t_fuel,2)."</td>
									<td style='text-align:right;'></td>
									<td style='text-align:right;'>".number_format($t_fuel_amount,2)."</td>
									<td style='text-align:right;'></td>
								</tr>
								";
							}
							#add to grand total
							
							$g_km 				+= $t_km;
							$g_sqm 				+= $t_sqm;
							$g_cum				+= $t_cum;
							$g_actual_hrs		+= $t_actual_hrs;
							$g_charged_hrs		+= $t_charged_hrs;
							$g_charge_amount	+= $t_charge_amount;
							$g_fuel				+= $t_fuel;
							$g_fuel_amount		+= $t_fuel_amount;
							
							$t_km = $t_sqm  = $t_cum = $t_actual_hrs = $t_charged_hrs = $t_charge_amount = $t_fuel = $t_fuel_amount = 0;
							#end of display subtotal
							
							echo "
								<tr>
									<td colspan='19' style='font-weight:bold;'>".$r['stock']."</td>
								</tr>
							";	
							$filter_stock_id = $r['stock_id'];
							$flag_display_subtotal = 1;
						}
						
					}#end of else
					
					$t_vol += $r['total_vol'];
					$t_cost	+= $r['price_unit'];
					$t_amount += ($r['total_vol'] * $r['price_unit']);
					
					$t_cement	+= $r['cement'];
					$t_wsand	+= $r['wsand'];
					$t_agg_g1	+= $r['agg_g1'];
					$t_agg_34	+= $r['agg_34'];
					$t_agg_38	+= $r['agg_38'];
					$t_admix	+= $r['admix'];
					$t_water	+= $r['water'];
					$t_electricity += $r['electricity'];
					
					$billed_display = ($r['billed']) ? "BILLED" : "UNBILLED";
					
					echo "
						<tr>
							<td>".date("m/d/Y",strtotime($r['date']))."</td>
							<td>$r[batch_prod_id]</td>
							<td style='text-align:right;'>".number_format($r['total_vol'],2)."</td>
							<td>$r[stock]</td>
							<td style='text-align:right;'>".number_format($r['price_unit'],2)."</td>
							<td style='text-align:right;'>".number_format($r['total_vol'] * $r['price_unit'],2)."</td>
							
							<td style='text-align:right;'>".number_format($r['cement'],2)."</td>
							<td style='text-align:right;'>".number_format($r['wsand'],2)."</td>
							<td style='text-align:right;'>".number_format($r['agg_g1'],2)."</td>
							<td style='text-align:right;'>".number_format($r['agg_34'],2)."</td>
							<td style='text-align:right;'>".number_format($r['agg_38'],2)."</td>
							<td style='text-align:right;'>".number_format($r['admix'],2)."</td>
							<td style='text-align:right;'>".number_format($r['water'],2)."</td>
							<td style='text-align:right;'>".number_format($r['electricity'],2)."</td>
							<td style='text-align:right;'>".$r['remarks']."</td>
							".( ($filter == 1) ? "<td style='text-align:left;'>$billed_display</td>" : "" )." 
						</tr>
						";	
				}
				#display total
				echo "
				<tr class='subtotal'>
					<td></td>
					<td></td>
					<td style='text-align:right;'>".number_format($t_vol,2)."</td>
					<td></td>
					<td style='text-align:right;'></td>
					<td style='text-align:right;'>".number_format($t_amount,2)."</td>
					
					<td style='text-align:right;'>".number_format($t_cement,2)."</td>
					<td style='text-align:right;'>".number_format($t_wsand,2)."</td>
					<td style='text-align:right;'>".number_format($t_agg_g1,2)."</td>
					<td style='text-align:right;'>".number_format($t_agg_34,2)."</td>
					<td style='text-align:right;'>".number_format($t_agg_38,2)."</td>
					<td style='text-align:right;'>".number_format($t_admix,2)."</td>
					<td style='text-align:right;'>".number_format($t_water,2)."</td>
					<td style='text-align:right;'>".number_format($t_electricity,2)."</td>					
					<td style='text-align:left;'></td>					
					".( ($filter == 1) ? "<td style='text-align:left;'></td>" : "" )." 
				</tr>
				";
				
				#add to grand total
							
				$g_vol 		+= $t_vol;
				$g_cost		+= $t_cost;
				$g_amount 	+= $t_amount;
				
				$g_cement 	+= $t_cement;
				$g_wsand 	+= $t_wsand;
				$g_agg_g1	+= $t_agg_g1;
				$g_agg_34	+= $t_agg_34;
				$g_agg_38	+= $t_agg_38;
				$g_admix	+= $t_admix;
				$g_water	+= $t_water;
				$g_electricity	+= $t_electricity;
				
				$t_vol = $t_cost  = $t_cost = 0;
				$t_cement = $t_wsand = $t_agg_g1 = $t_agg_34 = $t_agg_38 = $t_admix = $t_water = $t_electricity = 0;
				#end of display subtotal
				
				#echo grand total
				echo "
				<tr class='grandtotal'>
					<td></td>
					<td></td>
					<td style='text-align:right;'>".number_format($g_vol,2)."</td>
					<td></td>
					<td style='text-align:right;'></td>
					<td style='text-align:right;'>".number_format($g_amount,2)."</td>
					
					<td style='text-align:right;'>".number_format($g_cement,2)."</td>
					<td style='text-align:right;'>".number_format($g_wsand,2)."</td>
					<td style='text-align:right;'>".number_format($g_agg_g1,2)."</td>
					<td style='text-align:right;'>".number_format($g_agg_34,2)."</td>
					<td style='text-align:right;'>".number_format($g_agg_38,2)."</td>
					<td style='text-align:right;'>".number_format($g_admix,2)."</td>
					<td style='text-align:right;'>".number_format($g_water,2)."</td>
					<td style='text-align:right;'>".number_format($g_electricity,2)."</td>
					<td style='text-align:left;'></td>
					".( ($filter == 1) ? "<td style='text-align:left;'></td>" : "" )." 
				</tr>
				";

                ?>	
          
				
            </table>
        
        </div><!--End of content-->
    </div><!--End of Form-->
</div>
</body>
</html>