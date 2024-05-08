<?php
#echo "This module is being maintained. Please wait a few minutes.";
	require_once('../my_Classes/options.class.php');
	include_once("../conf/ucs.conf.php");
	
	$options       = new options();	
	$from_date     = $_REQUEST['from_date'];
	$to_date       = $_REQUEST['to_date'];
	$project_id    = $_REQUEST['project_id'];	
	$po_header_id  = $_REQUEST['po_header_id'];
	$driverID      = $_REQUEST['driverID'];
	$stock_id      = $_REQUEST['stock_id'];
	$eur_income_id = $_REQUEST['eur_income_id'];
	$unit          = $_REQUEST['unit'];
	$eur_ref_id    = $_REQUEST['eur_ref_id'];	
	$filter        = $_REQUEST['filter'];	 #1 - per project , 2 - per equipment
	$type          = $_REQUEST['type']; #S-ummary D-etail
	$eur_no        = $_REQUEST['eur_no'];
	
	$is_po_project	= $_REQUEST['is_po_project'];
	$date_encoded_filter	= $_REQUEST['date_encoded_filter'];
	
	function getDriver($driver_id){
		$sql = mysql_query("select * from drivers as d
				where 
				d.driverID = '$driver_id' and d.driver_void = '0'") or die (mysql_error());
		
		$count = mysql_num_rows($sql);
		if($count > 0){		
			$r = mysql_fetch_assoc($sql);
			
			$driver = $r['driver_name'];
		}else{
			$driver = "No Driver";
		}
		
		return $driver;
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
*{
	font-family: Arial;
	font-size: 11px;
	letter-spacing: 2px;
}
body{
	size: landscape;
}
table{
	border-collapse: collapse;
}
.container{
	width:100%;
}
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
.heading th{
	border-top: 1px solid #000;
	border-bottom: 1px solid #000;
}
</style>
</head>
<body>
<div class="container">
	
     <div><!--Start of Form-->
     	<div style="font-weight:bolder;">
        	<?=$title?><br />
            <?=$company_address?>
            <?=$project?> <br />
        </div>           
        <div class="content" style="">
        	<table cellpadding="6" style="width: 100%">
            	<tr class="heading">
                    <th style="width: 10px;"></th>
					<th style="width: 10px;">DATE</th>
                    <th>TIME ENCODED</th>
                    <th>ITEM</th>
                    <th>EUR#</th>
                    <th>PO#</th>
                    <th>FV#</th>
                    <th>NO. OF TRIPS</th>
                    <th>KM</th>
		      		<th>SQ.M</th>
                    <th>CU.M</th>
                    <th>OPERATOR</th>
                    <th>ACTUAL HRS</th>
                   <!-- <th>CHARGED HRS</th>-->
                    <th>RATE/HR</th>
                    <th>AMOUNT</th>
					 <th>FUEL</th>
					
                    
                    <th>FUEL RATIO KM/LTR</th>
					 
                    
                   
                    <!--<th>PRICE/L</th>
                    <th>AMOUNT</th>-->
                    
                    <th>FUEL RATIO LTR/HR</th>
					<!--<th>AVERAGE RATIO</th>-->
					<th>REMARKS</th>
                </tr>
                <?php
				$query = "
					select
						ed.released_date,
						p.stock,
						/*p.avg_ratio,*/
						eh.eur_no,
						ed.po_header_id,
						eh.fvs_no,
						ed.remarks,
						km,sqm,cum,
						ed.eur_charge_type_id,
						computed_time,
						eh.rate_per_hour,
						eh.no_of_liters,
						eh.price_per_liter,
						ed.no_of_trips,
						eh.encoded_datetime,
						ed.driver_id,
				";
				
				if($filter == 3){
					$query.="
							po_h.project_id,
					";
				}else{
					$query.="
							ed.project_id,
					";
				}
				
				$query.="
						p.stock_id
					from
						eur_header as eh, 
						eur_detail as ed,  
						po_detail as po,
						po_header as po_h,
						productmaster as p
					where
						eh.eur_header_id = ed.eur_header_id
					and ed.po_detail_id = po.po_detail_id
					and eh.stock_id = p.stock_id
					and	po.po_header_id = po_h.po_header_id
					and	ed.eur_void = '0'
				";
				
				if(!empty($eur_no)){
					
					$aEUR = explode("-", $eur_no);
					if( count($aEUR) == 1 ){
						$query .= "and eh.eur_no = '$eur_no'";
					} else {
						$query .= "and eh.eur_no between '$aEUR[0]' and '$aEUR[1]'";
					}
				} 
				
				if(!empty($stock_id)){
				$query.="
					and p.stock_id = '$stock_id'
				";		
				}
				#filter project here
				if(!empty($project_id)){
					if( $is_po_project ){
						$query.="
							and po_h.project_id = '$project_id'
						";		
					}else{
						if($filter == 3){
							$query.="
								and po_h.project_id = '$project_id'
							";		
						}else{
							$query.="
								and ed.project_id = '$project_id'
							";		
						}
					}
				}
				
				if(!empty($po_header_id)){
				$query .= "
					and ed.po_header_id = '$po_header_id'
				";	
				}
				
				if(!empty($from_date) && !empty($to_date)){
					if($date_encoded_filter){
				$query.="
				and eh.encoded_datetime between '$from_date' and '$to_date'
				";	
					}else{
					
				$query.="
					and released_date between '$from_date' and '$to_date'
				";		
				   }
				}
				#echo $query;
				/*
				if(!empty($project_id)){
				$query.="
					and ph.project_id = '$project_id'
				";		
				}
				*/
				
				if(!empty($driverID)){
				$query.="
					and ed.driver_id = '$driverID'
				";		
				}
				
				if(!empty($po_header_id)){
				$query.="
					and ed.po_header_id = '$po_header_id'
				";		
				}
				
				/*if eur no is supplied, order by eur no for the purpose of tracking down
				skipping numbers in the eur*/
				
				if( !empty($eur_no) ){
					$query .= "
						order by eur_no asc
					";
				} else {
					if($filter == "1") { # per project
						$query .= "
							order by ed.project_id asc
						";
					} else {
						$query .= "
							order by p.stock asc
						";
					}

					$query.= ",released_date asc, eur_no asc";
				}
				
				
				
				
				#echo $query;
				
				$result = mysql_query($query) or die(mysql_error());
				$i = 1;
				
				$subtotal_gross = $subtotal_mechanic = $subtotal_net = 0;
				
				$filter_project_id = $filter_stock_id = 'x';
				$t_km = $t_sqm  = $t_cum = $t_actual_hrs = $t_charged_hrs = $t_charge_amount = $t_fuel = $t_fuel_amount = 0;
				$g_km = $g_sqm  = $g_cum = $g_actual_hrs = $g_charged_hrs = $g_charge_amount = $g_fuel = $g_fuel_amount = 0;
				$flag_display_subtotal = 0;
				$c=1;
				while( $r = mysql_fetch_assoc($result) ){
					
					$eur_charge_type_id	= $r['eur_charge_type_id'];
					$computed_time		= $r['computed_time'];
					$rate_per_hour		= $r['rate_per_hour'];
					
					$charged_time = 0;				
					
					$driver = getDriver($r['driver_id']);
												 
					switch($eur_charge_type_id){
						
						case 1:	#CHARGE ACTUAL
							$charged_time = $computed_time;
							break;
							
						case 2:	#CHARGE MINIMUM
						
							#SHOULD CHECK IN PRODUCTMASTER FOR MINIMUM VALUE
							if($charged_time >= 4){
								$charged_time =  4;	
							}else{
								$charged_time = $computed_time;	
							}	
							
							break;	
						case 3: #NO CHARGE
							$charged_time = 0;
							break;
					}
					
					if($filter == "1" || $filter == "3") { # per project
						$r['project_id'] = (empty($r['project_id'])) ? '0' : $r['project_id'];
					
						if(empty($r['project_id']) && $filter_project_id != $r['project_id'] ){
							$filter_project_id = $r['project_id'];
							echo "
								<tr>
									<td colspan='21' style='font-weight:bold;'>NO PROJECT SPECIFIED</td>
								</tr>
							";	
							$flag_display_subtotal = 1;
						}else if($filter_project_id != $r['project_id']){
							#display total
							if($flag_display_subtotal){ #if it has subtotal
								echo "
								<tr class='subtotal'>
									<td></td>
									<td></td>
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
									<!--<td style='text-align:right;'>".number_format($t_charged_hrs,2)."</td>-->
									<td style='text-align:right;'></td>
									<td style='text-align:right;'>".number_format($t_charge_amount,2)."</td>
									<td style='text-align:right;'>".number_format($t_fuel,2)."</td>
									<td style='text-align:right;'></td>
									<td style='text-align:right;'></td>
									<!--<td style='text-align:right;'>".number_format($t_fuel_amount,2)."</td>-->
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
									<td colspan='21' style='font-weight:bold;'>".$options->getAttribute('projects','project_id',$r['project_id'],'project_name')."</td>
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
									<td colspan='21' style='font-weight:bold;'>NO ITEM SPECIFIED</td>
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
									<td></td>
									<td></td>
									<td style='text-align:right;'>".number_format($t_km,2)."</td>
									<td style='text-align:right;'>".number_format($t_sqm,2)."</td>
									<td style='text-align:right;'>".number_format($t_cum,2)."</td>
									<td></td>
									<td style='text-align:right;'>".number_format($t_actual_hrs,2)."</td>
									<!--<td style='text-align:right;'>".number_format($t_charged_hrs,2)."</td>-->
									<td style='text-align:right;'></td>
									<td style='text-align:right;'>".number_format($t_charge_amount,2)."</td>
                                    <td style='text-align:right;'>".number_format($t_fuel,2)."</td>									
									<td style='text-align:right;'></td>
									<td style='text-align:right;'></td>
									<!--<td style='text-align:right;'>".number_format($t_fuel_amount,2)."</td>-->
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
									<td colspan='20' style='font-weight:bold;'>".$r['stock']."</td>
								</tr>
							";	
							$filter_stock_id = $r['stock_id'];
							$flag_display_subtotal = 1;
						}
						
					}#end of else
					
					$t_km 	+= $r['km'];
					$t_sqm 	+= $r['sqm'];
					$t_cum 	+= $r['cum'];
					$t_actual_hrs	+= $r['computed_time'];
					$t_charged_hrs	+= $charged_time;
					$t_charge_amount += $r['rate_per_hour'] * $charged_time;
					$t_fuel	+= $r['no_of_liters'];
					$t_fuel_amount 	+= $r['price_per_liter'] * $r['no_of_liters'];
					
					if($type == "D"):
					echo "
					<tr>
					    <td>".$c++."</td>
						<td>".date("m/d/Y",strtotime($r['released_date']))."</td>
						<td>".$r['encoded_datetime']."</td>
						<td>".$r['stock']."</td>
						<td>".$r['eur_no']."</td>
						<td>".((!empty($r['po_header_id'])) ? str_pad($r['po_header_id'],7,0,STR_PAD_LEFT) : "" )."</td>
						<td>".$r['fvs_no']."</td>
						<td>".$r['no_of_trips']."</td>					
						<td style='text-align:right;'>".number_format($r['km'],2)."</td>
						<td style='text-align:right;'>".number_format($r['sqm'],2)."</td>
						<td style='text-align:right;'>".number_format($r['cum'],2)."</td>
						<td>".$driver."</td>
						<td style='text-align:right;'>".number_format($r['computed_time'],2)."</td>
						<!--<td style='text-align:right;'>".number_format($charged_time,2)."</td>-->
						<td style='text-align:right;'>".number_format($r['rate_per_hour'],2)."</td>
						<td style='text-align:right;'>".number_format($r['rate_per_hour'] * $charged_time,2)."</td>
						<td style='text-align:right;'>".number_format($r['no_of_liters'],2)."</td>
						
						
					
					";
					if( $r['no_of_liters'] > 0) echo "<td style='text-align:right;'>".number_format($r['km'] / $r['no_of_liters'],2)."</td>";
					else  echo "<td style='text-align:right;'>".number_format(0,2)."</td>";
					echo "
						  
						
						<!--<td style='text-align:right;'>".number_format($r['price_per_liter'],2)."</td>
						<td style='text-align:right;'>".number_format($r['price_per_liter'] * $r['no_of_liters'],2)."--></td>";
							
					if( $r['computed_time'] > 0 ) echo "<td style='text-align:right;'>".number_format( $r['no_of_liters'] / $r['computed_time'] ,2)."</td>";
					else echo "<td style='text-align:right;'>".number_format(0,2)."</td>";

					echo " 
					    <td style='text-align:center;'>".$r['remarks']."</td>";
						 "
					 
					</tr>
					";	
					endif;
				}
				#display total
				echo "
				<tr class='subtotal'>
					<td></td>
					<td></td>
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
					<!--<td style='text-align:right;'>".number_format($t_charged_hrs,2)."</td>-->
					<td style='text-align:right;'></td>
					<td style='text-align:right;'>".number_format($t_charge_amount,2)."</td>
				    <td style='text-align:right;'>".number_format($t_fuel,2)."</td>
					<td style='text-align:right;'></td>
					<td style='text-align:right;'></td>
					<!--<td style='text-align:right;'>".number_format($t_fuel_amount,2)."</td>-->
					<td style='text-align:right;'></td>
				</tr>
				";
				
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
				
				#echo grand total
				echo "
				<tr class='grandtotal'>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td style='text-align:right;'>".number_format($g_km,2)."</td>
					<td style='text-align:right;'>".number_format($g_sqm,2)."</td>
					<td style='text-align:right;'>".number_format($g_cum,2)."</td>
					<td></td>
					<td style='text-align:right;'>".number_format($g_actual_hrs,2)."</td>
					<!--<td style='text-align:right;'>".number_format($g_charged_hrs,2)."</td>-->
					<td style='text-align:right;'></td>
					<td style='text-align:right;'>".number_format($g_charge_amount,2)."</td>
					<td style='text-align:right;'>".number_format($g_fuel,2)."</td>
					<td style='text-align:right;'></td>	
					<td style='text-align:right;'></td>
					<!--<td style='text-align:right;'>".number_format($g_fuel_amount,2)."</td>-->
					<td style='text-align:right;'></td>
				</tr>
				";

                ?>	
          
				
            </table>
        
        </div><!--End of content-->
    </div><!--End of Form-->
</div>
</body>
</html>