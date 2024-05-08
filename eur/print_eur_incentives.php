<?php
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
	$from_ref		= $_REQUEST['from_ref'];
	$to_ref			= $_REQUEST['to_ref'];
	$eur_position	= $_REQUEST['eur_position'];
	
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
        	<table cellpadding="6">
            	<tr>
                	<th>NO</th>
                    <th>DATE</th>
                    <th>PROJECT</th>
                    <th>PO#</th>
                    <th>OPERATOR</th>
                    <th>EQUIPMENT</th>
                    <th>COMPUTED HRS</th>
                    <th>RATE/HR</th>
                    <th>CHARGE TYPE</th>
                    <th>VALUE</th>
                    <th>UNIT</th>
                    <th>FROM REF</th>
                    <th>TO REF</th>
                    <th>REF POS</th>
                    <th>RATE/UNIT</th>
                    <th>GROSS INCENTIVES</th>
                    <th>MECHANIC SHARE (30%)</th>
                    <th>NET OPERATOR INCENTIVES</th>
                </tr>
                <?php
				$query = "
					select
						released_date,
						driver_name,
						stock,
						eh.rate_per_hour,
						eur_charge_type,
						eur_unit,
						value,
						eur_unit_rate,
						ed.eur_charge_type_id,
						computed_time,
						ed.po_header_id,
						project_name,
						from_ref,
						to_ref,
						eur_position
					from
						eur_header as eh, 
						eur_detail as ed, 
						po_header as ph, 
						projects as pr, 
						drivers as d, 
						productmaster as p, 
						eur_unit as eu, 
						eur_charge_type as ec
					where
						eh.eur_header_id = ed.eur_header_id
					and
						eh.stock_id = p.stock_id
					and
						ed.po_header_id = ph.po_header_id
					and
						ph.project_id = pr.project_id
					and
						ed.driver_id = d.driverID
					and
						ed.unit = eu.eur_unit_id
					and
						ed.eur_charge_type_id = ec.eur_charge_type_id
				";
				
				if(!empty($stock_id)){
				$query.="
					and eh.stock_id = '$stock_id'
				";		
				}
				
				if(!empty($from_date) && !empty($to_date)){
				$query.="
					and released_date between '$from_date' and '$to_date'
				";		
				}
				
				if(!empty($project_id)){
				$query.="
					and ph.project_id = '$project_id'
				";		
				}
				
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
				
				if(!empty($from_ref) && !empty($to_ref)){
				$query.="
					and ed.from_ref >= '$from_ref'
					and ed.to_ref <= '$to_ref'
				";		
				}
				
				if(!empty($eur_position)){
				$query.="
					and ed.eur_position = '$eur_position'
				";		
				}
				
				if(!empty($unit)){
				$query.="
					and ed.unit = '$unit'
				";		
				}
				
				
				$result = mysql_query($query) or die(mysql_error());
				$i = 1;
				
				$subtotal_gross = $subtotal_mechanic = $subtotal_net = 0;
				while( $r = mysql_fetch_assoc($result) ){
					
					$eur_charge_type_id	= $r['eur_charge_type_id'];
					$computed_time		= $r['computed_time'];
					$rate_per_hour		= $r['rate_per_hour'];
					$value		 		= $r['value'];
					$eur_unit_rate		= $r['eur_unit_rate'];
					
					$gross = 0;				
					if( $eur_income_id == 1 ){ # per hour
												 
						switch($eur_charge_type_id){
							
							case 1:	#CHARGE ACTUAL
								$gross = $rate_per_hour * $computed_time;
								break;
							case 2:	#CHARGE MINIMUM
								if($computed_time <= 4){
									$gross = $rate_per_hour * 4;	
								}else{
									$gross = $rate_per_hour * $computed_time;	
								}	
								break;	
							case 3: #NO CHARGE
								$gross = 0;
								break;
						}
						 
					} else { # per span
						
						$gross = $eur_unit_rate * $value;
						
					}
					
					$mechanic_inc = $gross * 0.30;
					$net  = $gross - $mechanic_inc;					
					
					$subtotal_gross 	+= $gross;
					$subtotal_net 		+= $net;
					$subtotal_mechanic 	+= $mechanic_inc;
					
					echo "
					<tr>
						<td>".$i++."</td>
						<td>".date("m/d/Y",strtotime($r['released_date']))."</td>
						<td>".$r['project_name']."</td>
						<td>".str_pad($r['po_header_id'],5,0,STR_PAD_LEFT)."</td>
						<td>".$r['driver_name']."</td>
						<td>".$r['stock']."</td>
						<td style='text-align:right;'>".number_format($r['computed_time'],2)."</td>
						<td style='text-align:right;'>".number_format($r['rate_per_hour'],2)."</td>
						<td>".$r['eur_charge_type']."</td>
						<td style='text-align:right;'>".number_format($r['value'],2)."</td>
						<td>".$r['eur_unit']."</td>
						<td style='text-align:right;'>".number_format($r['from_ref'],2)."</td>
						<td style='text-align:right;'>".number_format($r['to_ref'],2)."</td>
						<td>".$r['eur_position']."</td>
						<td style='text-align:right;'>".number_format($r['eur_unit_rate'],2)."</td>
						<td style='text-align:right;'>".number_format($gross,2)."</td>
						<td style='text-align:right;'>".number_format($mechanic_inc,2)."</td>
						<td style='text-align:right;'>".number_format($net,2)."</td>
					</tr>
					";			
				}
				echo "
					<tr>
						<td style='border-top:1px solid #000; font-weight:bold;'></td>
						<td style='border-top:1px solid #000; font-weight:bold;'></td>
						<td style='border-top:1px solid #000; font-weight:bold;'></td>
						<td style='border-top:1px solid #000; font-weight:bold;'></td>
						<td style='border-top:1px solid #000; font-weight:bold;'></td>
						<td style='border-top:1px solid #000; font-weight:bold;'></td>
						<td style='border-top:1px solid #000; font-weight:bold;'></td>
						<td style='border-top:1px solid #000; font-weight:bold;'></td>
						<td style='border-top:1px solid #000; font-weight:bold;'></td>
						<td style='border-top:1px solid #000; font-weight:bold;'></td>
						<td style='border-top:1px solid #000; font-weight:bold;'></td>
						<td style='border-top:1px solid #000; font-weight:bold;'></td>
						<td style='border-top:1px solid #000; font-weight:bold;'></td>
						<td style='border-top:1px solid #000; font-weight:bold;'></td>
						<td style='border-top:1px solid #000; font-weight:bold;'></td>
						<td style='border-top:1px solid #000; font-weight:bold; text-align:right;'>".number_format($gross,2)."</td>
						<td style='border-top:1px solid #000; font-weight:bold; text-align:right;'>".number_format($mechanic_inc,2)."</td>
						<td style='border-top:1px solid #000; font-weight:bold; text-align:right;'>".number_format($net,2)."</td>
					</tr>
				";
                ?>	
          
				
            </table>
        
        </div><!--End of content-->
    </div><!--End of Form-->
</div>
</body>
</html>