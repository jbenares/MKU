
<?php
include_once("../conf/ucs.conf.php");
include_once("../library/lib.php");
include_once("../my_Classes/options.class.php");
require_once(dirname(__FILE__).'/../library/DB.php');

define('TITLE', "EUR");

$eur_header_id = $_REQUEST['eur_header_id'];
$options        = new options();

function getHoursDiff($date1,$date2){
		
		$date2 = new DateTime($date2);
		$date1 = new DateTime($date1);
	
		$interval = date_diff($date2,$date1,true);

		$hours	= $interval->format("%h");
		$mins	= $interval->format("%i");		
		
		return "$hours.$mins";
	}
function getTime($name,$end,$selected=NULL){
	$content = "
		<select name='$name' id = '$id'>
	";
	
	for($x = 0 ; $x <= $end ; $x++){
		$s = "";
		if($x == $selected){
			$s = "selected='selected'";	
		}
		$content .="
			<option $s>".str_pad($x,2,0,STR_PAD_LEFT)."</option>
		";
	}
	
	$content.="</select>";
	
	return $content;
}
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>REPORT</title>
<script>
function printPage() { print(); } //Must be present for Iframe printing
window.onload = print();
</script>
<style type="text/css">

body
{
	font-family:Arial, Helvetica, sans-serif;
	font-size:14px;
}
.container{
	margin:0px auto;	
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
	width:100%;
	border-collapse:collapse;	
}

table thead tr th{
	border-top:1px solid #000;
	border-bottom:1px solid #000;
	font-weight:bold;
}
/*table  tr td:nth-child(n+2){
	text-align:right;	
}*/
table td{
	padding:3px;
	
}

.eur-table td:nth-child(odd){
	font-weight: bold;
}


.line_bottom {
    border-bottom-width: 1px;
    border-bottom-style: solid;
    border-bottom-color: #000000;
    border-left: 0px;
    border-right: 0px;
    border-top: 0px;
    width:140px;
    font-size: 11px;
    text-align: center;
}
</style>
</head>
<body>
<div class="container">
	<?php
	$r = $aTrans = lib::getTableAttributes("select * from eur_header where eur_header_id = '$eur_header_id'");
	$eur_no				= $r['eur_no'];
	$stock_id			= $r['stock_id'];
	$rate_per_hour		= $r['rate_per_hour'];
	$status				= $r['status'];
	$user_id			= $r['user_id'];
	$before_filling		= $r['before_filling'];
	$after_filling		= $r['after_filling'];
	$fvs_no				= $r['fvs_no'];
	$place_of_origin	= $r['place_of_origin'];
	$fuel_station		= $r['fuel_station'];
	$no_of_liters		= $r['no_of_liters'];
	$price_per_liter	= $r['price_per_liter'];
	$fv_remarks 		= $r['fv_remarks'];
	?>
	
	<div style="font-weight:bold; font-size:15px; text-align:center;">
    	EQUIPMENT UTILIZATION REPORT
    </div>
    <div style="font-weight:bold; font-size:15px;">
    	SYS EUR #: <?=$eur_header_id?>
    </div>
    <table class="eur-table" style="vertical-align:top;">    	
    	<tr>
    		<td colspan='2' style="font-weight:bold; text-align:center;">EUR</td>
    		<td colspan='2' style="font-weight:bold; text-align:center;">FUEL VALE</td>
    	</tr>
        <tr>
            <td>EUR NO:</td>
            <td><?=$eur_no?></td>

            <td>
                Before Filling
            </td>
            <td>
                <?=$before_filling?>
			</td>
            <td>
                Remarks
            </td>
            <td>
            	<?=$fv_remarks?>
            </td>
        </tr>
        <tr>
            <td>EQUIPMENT:</td>
            <td>
                <?=$options->getAttribute('productmaster','stock_id',$stock_id,'stock')?>                    
            </td>

            <td>
                After Filling: 
           	</td>
           	<td>
                <?=$after_filling?>
            </td>
        </tr>
        <tr>
            <td>RATE PER HOUR:</td>
            <td><?=$rate_per_hour?></td>

            <td>
                FVS #:
           	</td>
           	<td>
                <?=$fvs_no?>
            </td>
        </tr>

        <tr>
        	<td></td>
        	<td></td>

        	<td>
                Place of Origin: 
           	</td>
           	<td>
                <?=$place_of_origin?>
            </td>
        </tr>
        <tr>
        	<td></td>
        	<td></td>
			<td>
                Fuel Station:
           	</td>
           	<td>
                <?=$fuel_station?>
            </td>

        </tr>
        <tr>
        	<td></td>
        	<td></td>

        	<td>
                <div style="display:inline-block;">
                    # of Liters: <br />
                    <?=$no_of_liters?>
                </div>
           	</td>
            <td>
                <div style="display:inline-block;">
                    Price/Liter: <br />
                    <?=$price_per_liter?>
                </div>
            </td>
        </tr>

    </table>   	

    <div>
    	<?php
        $result = mysql_query("select * from eur_detail where eur_header_id = '$eur_header_id' and eur_void = '0' ") or die(mysql_error());
        while($r = mysql_fetch_assoc($result)){
        ?>
        <!--<div class="module_actions"> -->
            <div style="border:1px solid #CCC; width:30%; display:inline-block; vertical-align:top;">
                <table class="table-form">
                	<input type="hidden" name="_eur_detail_id[]" value="<?=$r['eur_detail_id']?>"  />
                    <tr>
                    	<?php
						$sql = "
							select stock, po_detail_id from po_detail as d, productmaster as p where d.stock_id = p.stock_id and po_header_id = '$r[po_header_id]' and po_detail_id = '$r[po_detail_id]'
						";		
						$rs = DB::conn()->query($sql);
						if( $rs->num_rows ) $content = $rs->fetch_object()->stock;
                        ?>
                        <td>
                            PO #: <br />
                            <?=$r['po_header_id']?>
                            <?=$content?>
                        </td>
                   	</tr>
                    <tr>
                        <td>
                            Project: <br />
                            <?=lib::getAttribute('projects','project_id',$r['project_id'],'project_name')?>                            
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Driver: <br />
                            <?=$options->getAttribute('drivers','driverID',$r['driver_id'],'driver_name')?>                            
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Released Date: <br />
                            <?=$r['released_date']?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            No of trips: <br />
                          	<?=$r['no_of_trips']?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                        	<?php
							$start_time			= $r['start_time'];
							$aStartTime			= explode(":",$start_time);
							$start_time_hour 	= $aStartTime[0];
							$start_time_min 	= $aStartTime[1];
							
							$end_time			= $r['end_time'];
							$aEndTime			= explode(":",$end_time);
							$end_time_hour	 	= $aEndTime[0];
							$end_time_min	 	= $aEndTime[1];
                            ?>
                            <div style="display:inline-block;">
                                Start Time: <br />
                                <?=$start_time_hour?>:<?=$start_time_min?>
                            </div>
                            &nbsp;&nbsp;&nbsp;
                            <div style="display:inline-block;">
                            End Time: <br />
                            <?=$end_time_hour?>:<?=$end_time_min?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div style="display:inline-block;">
                                KM:<br />
                                <?=$r['km']?>
                            </div>
                            <div style="display:inline-block; margin: 0px 10px;">
                                CU.M.:<br />
                                <?=$r['cum']?>
                            </div>
                            <div style="display:inline-block;">
                                SQ.M.:<br />
                                <?=$r['sqm']?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        	<td>&nbsp;</td>
                        </tr>
                        <tr>
                        	<td style="color:#000;font-weight:bold;">
                            	Incentives
                            </td>
                        </tr>
                        <tr>
                        	<td>&nbsp;</td>
                        </tr>
                    <tr>
                        <td>
                            <div style="display:inline-block; vertical-align:top; text-align:center;">
                                Value:<br />
                               	<?=$r['value']?>
                            </div>
                            <div style="display:inline-block; vertical-align:top; margin:0px 20px;">
                                Unit:<br />                                
                                <?php
                                $sql = " select * from eur_unit where eur_unit_void = '0' and eur_unit_id = '$r[unit_id]'";
                                echo  DB::conn()->query($sql)->fetch_object()->eur_unit;                                 
                                ?>
                            </div>
                            <div style="display:inline-block; vertical-align:top;">
                                Rate/Unit:<br />
								<?=$r['unit_rate']?>
                            </div>
                        </td>
                    </tr>
                    <!--<tr>
                        <td>Reference<br />
                        <?=$options->getTableAssoc($r['eur_ref_id'],'_eur_ref_id[]','Select Reference',"select * from eur_ref where eur_ref_void = '0' order by eur_ref asc",'eur_ref_id','eur_ref')?></td>
                    </tr> -->
                    <tr>
                        	<td>
                            	<div style="display:inline-block;">
                                	From Ref: <br />
                                    <?=$r['from_ref']?>
                                </div>
                                <div style="display:inline-block; margin:0px 20px;">
                                	To Ref: <br />
                                    <?=$r['to_ref']?>
                                </div>
                                <div style="display:inline-block;">
                                	Position: <br />

									<?php
									$sql = " select * from eur_position where eur_position = '$r[eur_position]'";
                                	echo  DB::conn()->query($sql)->fetch_object()->eur_position;                                 
									?>
                                </div>
                            </td>
                        </tr>
                    <tr>
                        <td>
                            <div>
                                Remarks: <br />
                                <?=$r['remarks']?>
                            </div>
                        </td>
                    </tr>
                    <tr>	
                        <td>
                            Charge Type:

                            <?php
							$sql = "select * from eur_charge_type where eur_charge_type_id = '$r[eur_charge_type_id]'";
                        	echo  DB::conn()->query($sql)->fetch_object()->eur_charge_type;                                 
							?>                            
                        </td>                           	 
                    </tr>                    
                </table>
            </div>
        <!--</div> -->
        <?php } ?>
    </div>
    
    <table cellspacing="0" cellpadding="5" align="center" width="98%" style="border:none; text-align:center; margin-top:10px;" class="summary">
	    <tr>
	        <td>Prepared By:<p>
	            <input type="text" class="line_bottom" /><br><?=lib::getUserFullName($aTrans['user_id'])?></p></td>	            
	        <td>Checked By:<p>
	            <input type="text" class="line_bottom" /><br>&nbsp;</p></td>
	        <td>Approved By:<p>
	            <input type="text" class="line_bottom" /><br>&nbsp;</p></td>
	    </tr>
	</table>
  
     
</div>
</body>
</html>

