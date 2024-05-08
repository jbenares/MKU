<?php
if( $_REQUEST['ajax'] ){
	require_once(dirname(__FILE__).'/../conf/ucs.conf.php');
	require_once(dirname(__FILE__).'/../library/lib.php');

	if( $_REQUEST['computeRemainingBalanceOfPO'] ){
		$po_detail_id = $_REQUEST['po_detail_id'];
		$po_qty = lib::getAttribute('po_detail','po_detail_id',$po_detail_id,'quantity');

		$sql = "
			select 
				sum(computed_time) as computed_time
			from
				eur_header as h 
				inner join eur_detail as d on h.eur_header_id = d.eur_header_id
			and h.status != 'C'
			and eur_void = '0'
			and d.po_detail_id = '$po_detail_id'
		";
		$aEUR = lib::getTableAttributes($sql);
		$total_computed_time = $aEUR['computed_time'];

		$balance = $po_qty - $total_computed_time;
		echo "Remaining Balance : ".$balance;
	}

	exit();
}

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

<script type="text/javascript">
function printIframe(id)
{
    var iframe = document.frames ? document.frames[id] : document.getElementById(id);
    var ifWin = iframe.contentWindow || iframe;
    iframe.focus();
    ifWin.printPage();
    return false;
}
</script>
<style type="text/css">
.align-right{
	text-align:right;
}
.cp_table{
	width:50%;
	border-collapse:collapse;
}
.cp_table tr th {
	border-top:1px solid #000;
	border-bottom:1px solid #000;
}
</style>
<?php
	$b					= $_REQUEST['b'];
	$user_id			= $_SESSION['userID'];

	#SEARCH
	$search_eur_no		= $_REQUEST['search_eur_no'];
	
	#HEADER
	$eur_header_id		= $_REQUEST['eur_header_id'];
	$eur_no				= $_REQUEST['eur_no'];
	$stock_id			= $_REQUEST['stock_id'];
	$rate_per_hour		= $_REQUEST['rate_per_hour'];
	$fv_remarks			= $_REQUEST['fv_remarks'];
	
	#DETAILS
	$eur_detail_id		= $_REQUEST['eur_detail_id'];
	$po_header_id		= $_REQUEST['po_header_id'];
	$driver_id			= $_REQUEST['driver_id'];
	$released_date		= $_REQUEST['released_date'];
	$start_time_hour	= $_REQUEST['start_time_hour'];
	$start_time_min		= $_REQUEST['start_time_min'];
	$end_time_hour		= $_REQUEST['end_time_hour'];
	$end_time_min		= $_REQUEST['end_time_min'];
	$computed_time		= $_REQUEST['computed_time'];
	$value				= $_REQUEST['value'];
	$unit				= $_REQUEST['unit'];
	$remarks			= $_REQUEST['remarks'];
	$before_filling		= $_REQUEST['before_filling'];
	$after_filling		= $_REQUEST['after_filling'];
	$fvs_no				= $_REQUEST['fvs_no'];
	$place_of_origin	= $_REQUEST['place_of_origin'];
	$fuel_station		= $_REQUEST['fuel_station'];
	$no_of_liters		= $_REQUEST['no_of_liters'];
	$price_per_liter	= $_REQUEST['price_per_liter'];
	$eur_ref_id			= $_REQUEST['eur_ref_id'];
	$eur_charge_type_id	= $_REQUEST['eur_charge_type_id'];
	$unit_rate			= $_REQUEST['unit_rate'];
	$km					= $_REQUEST['km'];
	$sqm				= $_REQUEST['sqm'];
	$cum				= $_REQUEST['cum'];
	$po_detail_id		= $_REQUEST['po_detail_id'];
	$project_id			= $_REQUEST['project_id'];
	
	$from_ref			= $_REQUEST['from_ref'];
	$to_ref				= $_REQUEST['to_ref'];
	$eur_position		= $_REQUEST['eur_position'];
	$no_of_trips		= $_REQUEST['no_of_trips'];
	
	$_eur_detail_id      = $_REQUEST['_eur_detail_id'];
	$_po_header_id       = $_REQUEST['_po_header_id'];
	$_driver_id          = $_REQUEST['_driver_id'];
	$_released_date      = $_REQUEST['_released_date'];
	$_start_time_hour    = $_REQUEST['_start_time_hour'];
	$_start_time_min     = $_REQUEST['_start_time_min'];
	$_end_time_hour      = $_REQUEST['_end_time_hour'];
	$_end_time_min       = $_REQUEST['_end_time_min'];
	$_computed_time      = $_REQUEST['_computed_time'];
	$_value              = $_REQUEST['_value'];
	$_unit               = $_REQUEST['_unit'];
	$_remarks            = $_REQUEST['_remarks'];
	$_before_filling     = $_REQUEST['_before_filling'];
	$_after_filling      = $_REQUEST['_after_filling'];
	$_fvs_no             = $_REQUEST['_fvs_no'];
	$_place_of_origin    = $_REQUEST['_place_of_origin'];
	$_fuel_station       = $_REQUEST['_fuel_station'];
	$_no_of_liters       = $_REQUEST['_no_of_liters'];
	$_price_per_liter    = $_REQUEST['_price_per_liter'];
	$_eur_ref_id         = $_REQUEST['_eur_ref_id'];
	$_eur_charge_type_id = $_REQUEST['_eur_charge_type_id'];
	$_unit_rate          = $_REQUEST['_unit_rate'];
	$_km                 = $_REQUEST['_km'];
	$_sqm                = $_REQUEST['_sqm'];
	$_cum                = $_REQUEST['_cum'];
	$_po_detail_id       = $_REQUEST['_po_detail_id'];
	$_project_id         = $_REQUEST['_project_id'];
	
	$_from_ref			= $_REQUEST['_from_ref'];
	$_to_ref			= $_REQUEST['_to_ref'];
	$_eur_position		= $_REQUEST['_eur_position'];
	$_no_of_trips		= $_REQUEST['_no_of_trips'];
	$branding_num 		= $_REQUEST['branding_num'];
	
	if($b == "Submit"){
		$query="
			insert into
				eur_header
			set
				eur_no           = '$eur_no',
				stock_id         = '$stock_id',
				rate_per_hour    = '$rate_per_hour',
				user_id          = '$user_id',
				before_filling   = '$before_filling',
				after_filling    = '$after_filling',
				fvs_no           = '$fvs_no',
				place_of_origin  = '$place_of_origin',
				fuel_station     = '$fuel_station',
				no_of_liters     = '$no_of_liters',
				price_per_liter  = '$price_per_liter',
				fv_remarks       = 'fv_$remarks',
				encoded_datetime = now()
		";	
		
		mysql_query($query) or die(mysql_error());
		$eur_header_id = mysql_insert_id();

		if(!empty($branding_num)){
			foreach($branding_num as $ch) {
				$insertBranding="insert into 
								eur_branding
							 set
							 	eur_header_id = '$eur_header_id',
							 	branding_num  = '$ch'";
				mysql_query($insertBranding);
			}
		}
		
		$msg = "Transaction Added";
				
	}else if($b=="Update"){
		$options = new options();
		//$eqID = $options->getAttribute("eur_header","eur_header_id",$eur_header_id,"stock_id");

		$query="
			update
				eur_header
			set
				eur_no          = '$eur_no',
				stock_id        = '$stock_id',
				rate_per_hour   = '$rate_per_hour',
				user_id         = '$user_id',
				before_filling  = '$before_filling',
				after_filling   = '$after_filling',
				fvs_no          = '$fvs_no',
				place_of_origin = '$place_of_origin',
				fuel_station    = '$fuel_station',
				no_of_liters    = '$no_of_liters',
				price_per_liter = '$price_per_liter',
				fv_remarks      = '$fv_remarks'
			where
				eur_header_id		= '$eur_header_id'
		";	
		mysql_query($query) or die(mysql_error());
		
		mysql_query("delete from eur_branding WHERE eur_header_id = '$eur_header_id'");
		//if($stock_id != $eqID){
			if(!empty($branding_num)){
				foreach($branding_num as $ch) {
					$insertBranding="insert into 
									eur_branding
								 set
								 	eur_header_id = '$eur_header_id',
								 	branding_num  = '$ch'";
					mysql_query($insertBranding);
				}
			}
		//}

		#UPDATE DETAILS
		
		if(!empty($_eur_detail_id)){
			$i = 0;
			foreach($_eur_detail_id as $id){
				
				$_start_time	= "$_start_time_hour[$i]:$_start_time_min[$i]";
				$_end_time 		= "$_end_time_hour[$i]:$_end_time_min[$i]";
				
				$_computed_time = getHoursDiff("$_released_date[$i] $_start_time", "$_released_date[$i] $_end_time");
				
				$query="
					update
						eur_detail
					set
						po_header_id		= '$_po_header_id[$i]',
						driver_id			= '$_driver_id[$i]',
						released_date		= '$_released_date[$i]',
						start_time			= '$_start_time',
						end_time			= '$_end_time',
						computed_time		= '$_computed_time',
						value				= '$_value[$i]',
						unit				= '$_unit[$i]',
						remarks				= '$_remarks[$i]',
						eur_ref_id 			= '$_eur_ref_id[$i]',
						eur_charge_type_id	= '$_eur_charge_type_id[$i]',
						unit_rate			= '$_unit_rate[$i]',
						km					= '$_km[$i]',
						cum					= '$_cum[$i]',
						sqm					= '$_sqm[$i]',
						po_detail_id		= '$_po_detail_id[$i]',
						from_ref			= '$_from_ref[$i]',
						to_ref 				= '$_to_ref[$i]',
						eur_position		= '$_eur_position[$i]',
						project_id			= '$_project_id[$i]',
						no_of_trips			= '$_no_of_trips[$i]'
					where
						eur_detail_id = '$id'
				";	
				$i++;
				mysql_query($query) or die(mysql_error());
			}
		}
		
		$msg = "Transaction Updated";
	}
	
	if($b=="ADD"){
		$start_time	= "$start_time_hour:$start_time_min";
		$end_time 	= "$end_time_hour:$end_time_min";
		
		$computed_time = getHoursDiff("$released_date $start_time", "$released_date $end_time");
		
		$query="
			insert into 
				eur_detail
			set
				eur_header_id		= '$eur_header_id',
				po_header_id		= '$po_header_id',
				driver_id			= '$driver_id',
				released_date		= '$released_date',
				start_time			= '$start_time',
				end_time			= '$end_time',
				computed_time		= '$computed_time',
				value				= '$value',
				unit				= '$unit',
				remarks				= '$remarks',
				eur_ref_id			= '$eur_ref_id',
				eur_charge_type_id	= '$eur_charge_type_id',
				unit_rate			= '$unit_rate',
				km					= '$km',
				cum					= '$cum',
				sqm					= '$sqm',
				po_detail_id		= '$po_detail_id',
				from_ref			= '$from_ref',
				to_ref 				= '$to_ref',
				eur_position		= '$eur_position',
				project_id			= '$project_id',
				no_of_trips			= '$no_of_trips'
		";	
		
		mysql_query($query) or die(mysql_error());

		if(!empty($branding_num)){
				foreach($branding_num as $ch) {
					$insertBranding="insert into 
									eur_branding
								 set
								 	eur_header_id = '$eur_header_id',
								 	branding_num  = '$ch'";
					mysql_query($insertBranding);
				}
		}

		$msg = "Transaction Added";
		
	}else if($b == "d"){
		mysql_query("
			update eur_detail set eur_void = '1' where eur_detail_id = '$eur_detail_id'
		") or die(mysql_error());
		
		$msg = "Transaction Voided";
	} else if( $b == "Cancel" ){
		mysql_query("
			update
				eur_header
			set
				status = 'C'
			where
				eur_header_id = '$eur_header_id'
		") or die(mysql_error());
		$msg = "Transaction Cancelled";
	}
	
	$query="
		select
			*
		from
			eur_header 
		where
			eur_header_id = '$eur_header_id'
	";

	$result=mysql_query($query) or die(mysql_error());
	$r = $aVal = mysql_fetch_assoc($result);
	
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

<form name="header_form" id="header_form" action="" method="post">
<div class="module_actions">	
    <div class='inline'>
        EUR # : <br />  
        <input type="text" class="textbox"  name="search_eur_no" value="<?=$search_eur_no?>"  onclick="this.select();"  autocomplete="off" />
    </div>   
    <input type="submit" name="b" value="Search" />
    <a href="admin.php?view=<?=$view?>"><input type="button" value="New" /></a>
</div>

<?php
if($b == "Search"){
?>
	<?php
    $page = $_REQUEST['page'];
    if(empty($page)) $page = 1;
     
    $limitvalue = $page * $limit - ($limit);
    
    $sql = "
		select
        	*
        from
			eur_header
    ";
        
    if(!empty($search_eur_no)){
    $sql.="
		where
			eur_no like '$search_eur_no%'
    ";
    }
	
	$sql.="
		order by eur_no asc
	";
  
    $pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
            
    $i=$limitvalue;
    $rs = $pager->paginate();
	
	$pagination	= $pager->renderFullNav("$view&b=Search&search_eur_no=$search_eur_no");
    ?>
    <div class="pagination">
        <?=$pagination?>
    </div>
    <table id="my_table" cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table">
    <tr>				
    
        <th width="20">#</th>
        <th width="20"></th>
        <th style="width:15%;">EUR #</th>
        <th>EQUIPMT</th>
        <th style="width:10%;">RATE PER HOUR</th>
        <th style="width:20%;">ENCODED BY</th>
        <th style="width:5%;">STATUS</th>
    </tr>  
    <?php								
    while($r=mysql_fetch_assoc($rs)) {
        
        echo '<tr>';
        echo '<td width="20">'.++$i.'</td>';
        echo '<td width="15"><a href="admin.php?view='.$view.'&eur_header_id='.$r['eur_header_id'].'" title="Show Details"><img src="images/edit.gif" border="0"></a></td>';
		echo '<td>'."$r[eur_no]".'</td>';	
		echo '<td>'.$options->getAttribute('productmaster','stock_id',$r['stock_id'],'stock').'</td>';	
		echo '<td>'.number_format($r['rate_per_hour'],2).'</td>';	
		echo '<td>'.$options->getUserName($r['user_id']).'</td>';	
		echo '<td>'.$GLOBALS['aStatus'][$r['status']].'</td>';	
        echo '</tr>';
    }
    ?>
    </table>
    <div class="pagination">
	   	 <?=$pagination?>
    </div>
<?php
}else{
?>
    <div class=form_layout>
        <?php if(!empty($msg)) echo '<div id="status_update" class="ui-state-highlight ui-corner-all" style="padding: 0px 0.7em; text-align:left;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right:.3em;"></span> '.$msg.'</p></div>'; ?>
        <div class="module_title"><img src='images/user_orange.png'>EQUIPMENT UTILIZATION REPORT</div>
        <div class="module_actions">
            <input type="hidden" name="eur_header_id" id="eur_header_id" value="<?=$eur_header_id?>" />
            <div id="messageError">
                <ul>
                </ul>
            </div>
            <fieldset style="border:1px solid #CCC; width:30%; height:300px; display:inline-block; vertical-align:top;">
                <legend>EUR</legend>
                <table>
                    <tr>
                        <td>EUR NO:</td>
                        <td><input type="text" class="textbox" name="eur_no" value="<?=$eur_no?>" autocomplete="off" /></td>
                    </tr>
                    <tr>
                        <td>EQUIPMENT:</td>
                        <td>
                            <input type="text" class="textbox stock_name2" value="<?=$options->getAttribute('productmaster','stock_id',$stock_id,'stock')?>" onclick="this.select();" />
                            <input type="hidden" name="stock_id" id='stock_id' value="<?=$stock_id?>"  />
                        </td>
                    </tr>
                    <tr>
                        <td>RATE PER HOUR:</td>
                        <td><input type="text" id="rate_per_hour" class="textbox" name="rate_per_hour" value="<?=$rate_per_hour?>" /></td>
                    </tr>
                    
                </table>
           	</fieldset>
            
            <fieldset style="border:1px solid #CCC; height:280px; display:inline-block; vertical-align:top;">
                <legend>FUEL VALE SLIP</legend>
                <table class="table-form" style="display:inline-block;">
                    <tr>
                        <td>
                            Before Filling: <br />
                            <input type="text" class="textbox" name="before_filling" value="<?=$before_filling?>" />
						</td>
                        <td rowspan="3" style="vertical-align:top;">
                            Remarks
                        	<textarea style="border:1px solid #c0c0c0; width:100%;font-size:11px; font-family:Arial, Helvetica, sans-serif;" name="fv_remarks"><?=$fv_remarks?></textarea>    
                        </td>
                    </tr>
                    <tr>
                        <td>
                            After Filling: <br />
                            <input type="text" class="textbox" name="after_filling" value="<?=$after_filling?>" />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            FVS #: <br />
                            <input type="text" class="textbox" name="fvs_no" value="<?=$fvs_no?>" />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Place of Origin: <br />
                            <input type="text" class="textbox" name="place_of_origin" value="<?=$place_of_origin?>" />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Fuel Station: <br />
                            <input type="text" class="textbox" name="fuel_station" value="<?=$fuel_station?>" />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div style="display:inline-block;">
                                # of Liters: <br />
                                <input type="text" class="textbox" name="no_of_liters" value="<?=$no_of_liters?>" />
                            </div>
                       	</td>
                        <td>
                            <div style="display:inline-block;">
                                Price/Liter: <br />
                                <input type="text" class="textbox" name="price_per_liter" value="<?=$price_per_liter?>" />
                            </div>
                        </td>
                    </tr>
                </table>
            </fieldset>
            <fieldset style="border:1px solid #CCC; width:30%; height:300px; display:inline-block; vertical-align:top;">
                <legend>BRANDING NUMBER</legend>
                <div id="b_list" style='overflow:scroll;height:300px;'>

                </div>
           	</fieldset>
        </div>
        <?php if(!empty($status)){ ?>
        <div class="module_actions">
        	<div style="display:inline-block; margin-right:10px; vertical-align:top;">
            	Status:<br />
                <strong><?=$options->getTransactionStatusName($status)?></strong>
            </div>
            <div style="display:inline-block; vertical-align:top;">
            	Encoded by:<br />
                <strong><?=$options->getUserName($user_id)?></strong>
                <?php
                if( !empty($aVal['encoded_datetime']) ){
                	echo "<br>".$aVal['encoded_datetime'];
                }
                ?>
            </div>
        </div>
        <?php } ?>
        <div class="module_actions">
            <?php if($status == "S"){ ?>
            <input type="submit" name="b" id="b" value="Update" />
            <input type="button" value="Print" onclick="openinnewTab();" />
            <?php }else if(empty($status)) { ?>
            <input type="submit" name="b" id="b" value="Submit" />
            <?php } ?>

            <?php if($status == "S"){ ?>
            <input type="submit" name="b" value="Cancel" onclick="return approve_confirm();" />
            <?php } ?>
            <a href="admin.php?view=<?=$view?>"><input type="button" value="New" /></a>
        </div>
        <?php if($status == "S"){ ?>
        <div class="accordion">	
        	<h3 style="padding:5px;">EUR DETAILS</h3>
        	<div>
                <fieldset style="border:1px solid #CCC; width:50%;display:inline-block; vertical-align:top;">
                    <legend>EUR DETAILS</legend>
                    <table class="table-form">
                        <tr>
                            <td>
                                PO #: <br />
                                <input type="text" class="textbox po_header_id" name="po_header_id" value="" autocomplete="off" />
                               	<div id="po_div" style="display:inline-block;">
                                </div>
                            </td>
                            <td>
                            	<span id="remaining_balance" style="font-weight:bold; color:#F00;" ></span>
                            </td>
                        </tr>
                        <tr>
                        	<td>
                            	Project: <br />
                                <?=$options->getTableAssoc('','project_id','Select Project',"select * from projects order by project_name asc",'project_id','project_name')?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Driver: <br />
                                <input type="text" class="textbox driver_name" name="driver_name" value="<?=$_REQUEST['driver_name']?>" />
                                <input type="hidden" name="driver_id" value="<?=$driver_id?>" />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Released Date: <br />
                                <input type="text" class="textbox datepicker" name="released_date" id="released_date"/>
                            </td>
                        </tr>
                        <tr>
                        	<td>
                            	No of trips: <br />
                                <input type="text" class="textbox" name="no_of_trips" />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div style="display:inline-block;">
                                    Start Time: <br />
                                    <?=getTime('start_time_hour',24)?>:
                                    <?=getTime('start_time_min',59)?>
                                </div>
                                &nbsp;&nbsp;&nbsp;
                                <div style="display:inline-block;">
                                End Time: <br />
                                <?=getTime('end_time_hour',24)?>:
                                <?=getTime('end_time_min',59)?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                        	<td>
                            	<div style="display:inline-block;">
                                    KM:<br />
                                    <input type="text" class="textbox3" name="km" />
                                </div>
                                <div style="display:inline-block;">
                                    CU.M.:<br />
                                    <input type="text" class="textbox3" name="cum" />
                                </div>
                                <div style="display:inline-block;">
                                    SQ.M.:<br />
                                    <input type="text" class="textbox3" name="sqm" />
                                </div>
                            </td>
                        </tr>
                        <tr>
                        	<td>&nbsp;</td>
                        </tr>
                        <tr>
                        	<td style="color:#FFF; background-color:#000; font-weight:bold;">
                            	Incentives
                            </td>
                        </tr>
                        <tr>
                        	<td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>
                                <div style="display:inline-block;">
                                    Value:<br />
                                    <input type="text" class="textbox3" name="value" />
                                </div>
                                <div style="display:inline-block;">
                                    Unit:<br />
                                    <?=$options->getTableAssoc($unit,'unit','Select Unit',"select * from eur_unit where eur_unit_void = '0' order by eur_unit asc",'eur_unit_id','eur_unit')?>
                                </div>
                                <div style="display:inline-block;">
                                (Incentives)Rate/Unit:<br />
								<input type="text" class="textbox3" name="unit_rate" id="unit_rate" value=""  />
                            </div>
                            </td>
                        </tr>
                        <!--<tr>
                        	<td>Reference<br />
                            <?=$options->getTableAssoc($eur_ref_id,'eur_ref_id','Select Reference',"select * from eur_ref where eur_ref_void = '0' order by eur_ref asc",'eur_ref_id','eur_ref')?></td>
                        </tr> -->
                        <tr>
                        	<td>
                            	<div style="display:inline-block;">
                                	From Ref: <br />
                                    <input type="text" class="textbox3" name="from_ref" />
                                </div>
                                <div style="display:inline-block;">
                                	To Ref: <br />
                                    <input type="text" class="textbox3" name="to_ref" />
                                </div>
                                <div style="display:inline-block;">
                                	Position: <br />
									<?=$options->getTableAssoc('','eur_position','Select Position',"select * from eur_position",'eur_position','eur_position')?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div>
                                    Remarks: <br />
                                    <textarea name="remarks" style="border:1px solid #c0c0c0; width:300px; height:50px; font-family:Arial, Helvetica, sans-serif; font-size:11px;"></textarea>
                                </div>
                            </td>
                        </tr>
                        <tr>	
                        	<td>
                            	Charge Type:
                                <?php
								$eur_charge_type_id = (empty($_eur_charge_type_id)) ? 2 : $eur_charge_type_id;
                                ?>
								<?=$options->getTableAssoc($eur_charge_type_id,'eur_charge_type_id','Select Charge Type',"select * from eur_charge_type",'eur_charge_type_id','eur_charge_type')?>
                           	</td>                           	 
                        </tr>
                    </table>
                </fieldset>
                
            
                <br />
    
                <input type="submit" name="b" value="ADD" />
            </div>
        </div>
        <?php } ?>
    </div>
	<div>
		<?php
        $result = mysql_query("select * from eur_detail where eur_header_id = '$eur_header_id' and eur_void = '0' ") or die(mysql_error());
        while($r = mysql_fetch_assoc($result)){
        ?>
        <!--<div class="module_actions"> -->
            <fieldset style="border:1px solid #CCC; width:30%; display:inline-block; vertical-align:top;">
                <legend>EUR DETAILS</legend>
                <table class="table-form">
                	<input type="hidden" name="_eur_detail_id[]" value="<?=$r['eur_detail_id']?>"  />
                    <tr>
                    	<?php
						$sql = "
							select stock, po_detail_id from po_detail as d, productmaster as p where d.stock_id = p.stock_id and po_header_id = '$r[po_header_id]'
						";
						
						$content = $options->getTableAssoc($r['po_detail_id'],'_po_detail_id[]','Select Item',$sql,'po_detail_id','stock');
                        ?>
                        <td>
                            PO #: <br />
                            <input type="text" class="textbox" name="_po_header_id[]" value="<?=$r['po_header_id']?>" autocomplete="off" />
                            <?=$content?>
                        </td>
                   	</tr>
                    <tr>
                        <td>
                            Project: <br />
                            <?=$options->getTableAssoc($r['project_id'],'_project_id[]','Select Project',"select * from projects order by project_name asc",'project_id','project_name')?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Driver: <br />
                            <input type="text" class="textbox driver_name" value="<?=$options->getAttribute('drivers','driverID',$r['driver_id'],'driver_name')?>" />
                            <input type="hidden" name="_driver_id[]" value="<?=$r['driver_id']?>" />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Released Date: <br />
                            <input type="text" class="textbox datepicker" name="_released_date[]" value="<?=$r['released_date']?>" id='released_date2'/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            No of trips: <br />
                            <input type="text" class="textbox" name="_no_of_trips[]" value="<?=$r['no_of_trips']?>" />
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
                                <?=getTime('_start_time_hour[]',24,$start_time_hour)?>:
                                <?=getTime('_start_time_min[]',59,$start_time_min)?>
                            </div>
                            &nbsp;&nbsp;&nbsp;
                            <div style="display:inline-block;">
                            End Time: <br />
                            <?=getTime('_end_time_hour[]',24,$end_time_hour)?>:
                            <?=getTime('_end_time_min[]',59,$end_time_min)?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div style="display:inline-block;">
                                KM:<br />
                                <input type="text" class="textbox3" name="_km[]" value="<?=$r['km']?>" />
                            </div>
                            <div style="display:inline-block;">
                                CU.M.:<br />
                                <input type="text" class="textbox3" name="_cum[]" value="<?=$r['cum']?>" />
                            </div>
                            <div style="display:inline-block;">
                                SQ.M.:<br />
                                <input type="text" class="textbox3" name="_sqm[]"  value="<?=$r['sqm']?>" />
                            </div>
                        </td>
                    </tr>
                    <tr>
                        	<td>&nbsp;</td>
                        </tr>
                        <tr>
                        	<td style="color:#FFF; background-color:#000; font-weight:bold;">
                            	Incentives
                            </td>
                        </tr>
                        <tr>
                        	<td>&nbsp;</td>
                        </tr>
                    <tr>
                        <td>
                            <div style="display:inline-block;">
                                Value:<br />
                                <input type="text" class="textbox3" name="_value[]" value="<?=$r['value']?>" />
                            </div>
                            <div style="display:inline-block;">
                                Unit:<br />
                                <?=$options->getTableAssoc($r['unit'],'_unit[]','Select Unit',"select * from eur_unit where eur_unit_void = '0' order by eur_unit asc",'eur_unit_id','eur_unit')?>
                            </div>
                            <div style="display:inline-block;">
                                Rate/Unit:<br />
								<input type="text" class="textbox3" name="_unit_rate[]"  value="<?=$r['unit_rate']?>"  />
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
                                    <input type="text" class="textbox3" name="_from_ref[]" value="<?=$r['from_ref']?>" />
                                </div>
                                <div style="display:inline-block;">
                                	To Ref: <br />
                                    <input type="text" class="textbox3" name="_to_ref[]" value="<?=$r['to_ref']?>" />
                                </div>
                                <div style="display:inline-block;">
                                	Position: <br />
									<?=$options->getTableAssoc($r['eur_position'],'_eur_position[]','Select Position',"select * from eur_position",'eur_position','eur_position')?>
                                </div>
                            </td>
                        </tr>
                    <tr>
                        <td>
                            <div>
                                Remarks: <br />
                                <textarea name="_remarks[]" style="border:1px solid #c0c0c0; width:300px; height:50px; font-family:Arial, Helvetica, sans-serif; font-size:11px;"><?=$r['remarks']?></textarea>
                            </div>
                        </td>
                    </tr>
                    <tr>	
                        <td>
                            Charge Type:
                            <?=$options->getTableAssoc($r['eur_charge_type_id'],'_eur_charge_type_id[]','Select Charge Type',"select * from eur_charge_type",'eur_charge_type_id','eur_charge_type')?>
                        </td>                           	 
                    </tr>
                    <tr>
                    	<td>
                        	<a href="admin.php?view=<?=$view?>&eur_header_id=<?=$eur_header_id?>&eur_detail_id=<?=$r['eur_detail_id']?>&b=d" onclick="return approve_confirm();"><input type="button" class='trash' value="Delete" /></a>
                        </td>
                    </tr>
                </table>
            </fieldset>
        <!--</div> -->
        <?php } ?>
    </div>
<?php } ?>
<?php
/*if($b == "Print Preview" && !empty($eur_header_id)){

	echo "	<iframe id='JOframe' name='JOframe' frameborder='0' src='eur/print_iso_eur.php?id=$eur_header_id' width='100%' height='500'>
			</iframe>";
}*/

			
?>
</form>
<script type="text/javascript">
j(function(){	

	if(j('#stock_id').val()!="" && j('#released_date2').val()!=""){
		xajax_getBranding(j('#stock_id').val(),j('#released_date2').val());
	}

	<?php if( $status == "C" || $status == "F" ) echo "jQuery('.trash').remove();"; ?>

	jQuery('#released_date, #released_date2').change(function(){
		xajax_getBranding(jQuery('#stock_id').val(),jQuery(this).val());
	});

	jQuery('.accordion').accordion({
	collapsible: true
	});
	
	jQuery(".po_header_id").change(function(){
		xajax_displayPOItem(jQuery(this).val());
	});
	
		
	jQuery(".driver_name").autocomplete({
		source: "list_drivers.php",
		minLength: 1,
		select: function(event, ui) {
			jQuery(this).val(ui.item.value);
			jQuery(this).next().val(ui.item.id);
		}
	});
	
	jQuery("#unit").change(function(){
		xajax_getUnitRate(jQuery(this).val());
	});

});

function computeRemainingBalanceOfPO(e){
	var po_detail_id = jQuery(e).val();

	jQuery.post("eur/eur.php", { ajax : 1 ,computeRemainingBalanceOfPO : 1, po_detail_id : po_detail_id }, function(data){
	    //actions	    
	    jQuery("#remaining_balance").html(data);
	} );

}

</script>
<script type="text/javascript">
   function openinnewTab() {

		var eur_header_id      = jQuery("#eur_header_id").val();

    	/*var win = window.open("eur/print_eur.php?eur_header_id=" + eur_header_id , '_blank');*/
    	var win = window.open("eur/print_iso_eur.php?id=" + eur_header_id , '_blank');
       	win.focus();
   }
</script>