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

	$search_batch_prod		= $_REQUEST['search_batch_prod'];
	$search_project_name	= $_REQUEST['search_project_name'];
	$search_project_id		= ($search_project_name) ? $_REQUEST['search_project_id'] : "";
	
	$batch_prod_id			= $_REQUEST['batch_prod_id'];
	$operator_id			= $_REQUEST['operator_id'];
	$eq_operator_id			= $_REQUEST['eq_operator_id'];
	$date					= $_REQUEST['date'];
	$weather				= $_REQUEST['weather'];
	$cement					= $_REQUEST['cement'];
	$wsand 					= $_REQUEST['wsand'];
	$agg_g1					= $_REQUEST['agg_g1'];
	$agg_34					= $_REQUEST['agg_34'];
	$agg_38					= $_REQUEST['agg_38'];
	$admix					= $_REQUEST['admix'];
	$water					= $_REQUEST['water'];
	$electricity			= $_REQUEST['electricity'];
	$stock_id				= $_REQUEST['stock_id'];
	$project_id				= $_REQUEST['project_id'];
	$from_project_id		= $_REQUEST['from_project_id'];
	$tm_rental				= $_REQUEST['tm_rental'];
	$pl_rental				= $_REQUEST['pl_rental'];
	$tmd_rental				= $_REQUEST['tmd_rental'];
	$manpower				= $_REQUEST['manpower'];
	$fuel				    = $_REQUEST['fuel'];
	$incentives				= $_REQUEST['incentives'];
	$depre  				= $_REQUEST['depre'];
	
	$cement_price			= $_REQUEST['cement_price'];
	$wsand_price 			= $_REQUEST['wsand_price'];
	$agg_g1_price			= $_REQUEST['agg_g1_price'];
	$agg_34_price			= $_REQUEST['agg_34_price'];
	$agg_38_price			= $_REQUEST['agg_38_price'];
	$admix_price			= $_REQUEST['admix_price'];
	$water_price			= $_REQUEST['water_price'];
	$electricity_price		= $_REQUEST['electricity_price'];
	$tm_rental_price		= $_REQUEST['tm_rental_price'];
	$pl_rental_price		= $_REQUEST['pl_rental_price'];
	$tmd_rental_price		= $_REQUEST['tmd_rental_price'];
	$manpower_price			= $_REQUEST['manpower_price'];
	$fuel_price			    = $_REQUEST['fuel_price'];
	$incentives_price		= $_REQUEST['incentives_price'];
	$depre_cost  			= $_REQUEST['depre_cost'];
	
	$total_vol				= $_REQUEST['total_vol'];
	$price_unit				= $_REQUEST['price_unit'];
	
	$remarks				= $_REQUEST['remarks'];
	
	$billed					= ($_REQUEST['billed']) ? 1 : 0;
	if($b=="Submit"){
		$query="
			insert into 
				batch_prod
			set
				operator_id			= '$operator_id',
				eq_operator_id		= '$eq_operator_id',
				date				= '$date',
				weather				= '$weather',
				cement				= '$cement',
				wsand 				= '$wsand',
				agg_g1				= '$agg_g1',
				agg_34				= '$agg_34',
				agg_38				= '$agg_38',
				admix				= '$admix',
				water				= '$water',
				electricity			= '$electricity',
				tm_rental			= '$tm_rental',
				pl_rental			= '$pl_rental',
				tmd_rental			= '$tmd_rental',
				manpower			= '$manpower',
				fuel   		        = '$fuel',
				incentives			= '$incentives',
				depre 				= '$depre',
				cement_price		= '$cement_price',
				wsand_price 		= '$wsand_price',
				agg_g1_price		= '$agg_g1_price',
				agg_34_price		= '$agg_34_price',
				agg_38_price		= '$agg_38_price',
				admix_price			= '$admix_price',
				water_price			= '$water_price',
				electricity_price	= '$electricity_price',
				tm_rental_price  	= '$tm_rental_price',
			    pl_rental_price  	= '$pl_rental_price',
				tmd_rental_price  	= '$tmd_rental_price',
				manpower_price	    = '$manpower_price',
				fuel_price	        = '$fuel_price',
				incentives_price	= '$incentives_price',
				depre_cost 			= '$depre_cost',
				total_vol			= '$total_vol',
				price_unit			= '$price_unit',
				user_id				= '$user_id',
				stock_id 			= '$stock_id',
				project_id			= '$project_id',
				from_project_id		= '$from_project_id',
				remarks				= '$remarks',
				billed				= '$billed'
		";	
		
		mysql_query($query) or die(mysql_error());
		$batch_prod_id = mysql_insert_id();
		$msg = "Transaction Added";
		
	}else if($b=="Update"){
		$query="
			update
				batch_prod
			set
				operator_id			= '$operator_id',
				eq_operator_id		= '$eq_operator_id',
				date				= '$date',
				weather				= '$weather',
				cement				= '$cement',
				wsand 				= '$wsand',
				agg_g1				= '$agg_g1',
				agg_34				= '$agg_34',
				agg_38				= '$agg_38',
				admix				= '$admix',
				water				= '$water',
				electricity			= '$electricity',
				tm_rental			= '$tm_rental',
				pl_rental			= '$pl_rental',
				tmd_rental			= '$tmd_rental',
				manpower			= '$manpower',
				fuel   		        = '$fuel',
				incentives			= '$incentives',
				depre 				= '$depre',
				cement_price		= '$cement_price',
				wsand_price 		= '$wsand_price',
				agg_g1_price		= '$agg_g1_price',
				agg_34_price		= '$agg_34_price',
				agg_38_price		= '$agg_38_price',
				admix_price			= '$admix_price',
				water_price			= '$water_price',
				electricity_price	= '$electricity_price',
				tm_rental_price  	= '$tm_rental_price',
			    pl_rental_price  	= '$pl_rental_price',
				tmd_rental_price  	= '$tmd_rental_price',
				manpower_price	    = '$manpower_price',
				fuel_price	        = '$fuel_price',
				incentives_price	= '$incentives_price',
				depre_cost 			= '$depre_cost',
				total_vol			= '$total_vol',
				price_unit			= '$price_unit',
				user_id				= '$user_id',
				stock_id 			= '$stock_id',
				project_id			= '$project_id',
				from_project_id		= '$from_project_id',
				remarks				= '$remarks',
				billed				= '$billed'
			where
				batch_prod_id = '$batch_prod_id'
		";	
		
		mysql_query($query) or die(mysql_error());
		
		$msg = "Transaction Updated";
	}else if($b == "Void"){
		mysql_query("
			update batch_prod set batch_prod_void = '1' where batch_prod_id = '$batch_prod_id'
		") or die(mysql_error());
		
	}
	
	$query="
		select
			*
		from
			batch_prod 
		where
			batch_prod_id = '$batch_prod_id'
	";
	
	$result=mysql_query($query) or die(mysql_error());
	$r=mysql_fetch_assoc($result);
	
	$operator_id			= $r['operator_id'];
	$eq_operator_id			= $r['eq_operator_id'];
	$date					= $r['date'];
	$weather				= $r['weather'];
	$cement					= $r['cement'];
	$wsand 					= $r['wsand'];
	$agg_g1					= $r['agg_g1'];
	$agg_34					= $r['agg_34'];
	$agg_38					= $r['agg_38'];
	$admix					= $r['admix'];
	$water					= $r['water'];
	$electricity			= $r['electricity'];
	$stock_id				= $r['stock_id'];
	$project_id				= $r['project_id'];
	$from_project_id		= $r['from_project_id'];
	$tm_rental				= $r['tm_rental'];
	$pl_rental				= $r['pl_rental'];
	$tmd_rental				= $r['tmd_rental'];
	$manpower				= $r['manpower'];
	$fuel				    = $r['fuel'];
	$incentives				= $r['incentives'];
	$depre 					= $r['depre'];
	
	$cement_price			= $r['cement_price'];
	$wsand_price 			= $r['wsand_price'];
	$agg_g1_price			= $r['agg_g1_price'];
	$agg_34_price			= $r['agg_34_price'];
	$agg_38_price			= $r['agg_38_price'];
	$admix_price			= $r['admix_price'];
	$water_price			= $r['water_price'];
	$electricity_price		= $r['electricity_price'];
	$tm_rental_price		= $r['tm_rental_price'];
	$pl_rental_price		= $r['pl_rental_price'];
	$tmd_rental_price		= $r['tmd_rental_price'];
	$manpower_price			= $r['manpower_price'];
	$fuel_price			    = $r['fuel_price'];
	$incentives_price		= $r['incentives_price'];
	$depre_cost 			= $r['depre_cost'];
	
	$total_vol				= $r['total_vol'];
	$price_unit				= $r['price_unit'];
	
	$status					= $r['status'];
	$user_id				= $r['user_id'];
	
	$remarks				= $r['remarks'];
	$billed					= $r['billed'];
?>

<form name="header_form" id="header_form" action="" method="post">
<div class="module_actions">	
    <div class='inline'>
        PROD #: <br />  
        <input type="text" class="textbox"  name="search_batch_prod" value="<?=$search_batch_prod?>"  onclick="this.select();"  autocomplete="off" />
    </div>   
    <div class='inline'>
        SEARCH PROJECT: <br />  
        <input type="text" class="textbox project" name="search_project_name"  value="<?=$search_project_name?>"  onclick="this.select();"  autocomplete="off" />
        <input type="hidden" name="search_project_id" value="<?=$search_project_id?>" />
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
			batch_prod
		where
			1=1
    ";
        
    if(!empty($search_batch_prod)){
    $sql.="
		and
			batch_prod_id like '$search_batch_prod%'
    ";
    }
	
	if(!empty($search_project_id)){
    $sql.="
		and project_id ='$search_project_id'
    ";
    }
	
	$sql.="
		order by batch_prod_id desc,date desc
	";
  
    $pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
            
    $i=$limitvalue;
    $rs = $pager->paginate();
	
	$pagination	= $pager->renderFullNav("$view&b=Search&search_batch_prod=$search_batch_prod");
    ?>
    <div class="pagination">
        <?=$pagination?>
    </div>
    <table id="my_table" cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table">
    <tr>				
    
        <th width="20">#</th>
        <th width="20"></th>
		<th width="40">DATE</th>
        <th width="50">PROD#</th>
		<th>PRODJECT</th>
		<th>VOLUME</th>
		<th>UNIT PRICE</th>
		<th>REMARKS</th>
		<th>BILLING STATUS</th>
		
    </tr>  
    <?php								
    while($r=mysql_fetch_assoc($rs)) {
		
        $billed_display = ($r['billed']) ? "BILLED" : "UNBILLED";
	
        echo '<tr>';
        echo '<td width="20">'.++$i.'</td>';
        echo '<td width="15"><a href="admin.php?view='.$view.'&batch_prod_id='.$r['batch_prod_id'].'" title="Show Details"><img src="images/edit.gif" border="0"></a></td>';
		echo '<td>'.date("m/d/Y",strtotime($r['date'])).'</td>';	
		echo '<td>'.str_pad($r['batch_prod_id'],7,0,STR_PAD_LEFT).'</td>';	
		echo '<td>'.$options->getAttribute('projects','project_id',$r['project_id'],'project_name').'</td>';
        echo '<td>'.$r['total_vol'].'</td>';
		echo '<td>'.$r['price_unit'].'</td>';
        echo '<td>'.$r['remarks'].'</td>';	
        echo '<td>'.$billed_display.'</td>';			
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
        <div class="module_title"><img src='images/user_orange.png'>BATCHING PLANT PRODUCTION REPORT</div>
        <div class="module_actions">
            <input type="hidden" name="batch_prod_id" value="<?=$batch_prod_id?>" />
            <div id="messageError">
                <ul>
                </ul>
            </div>
            <fieldset style="border:none; border-top:1px solid #CCC; display:inline-block; vertical-align:top;">
                <legend>PRODUCTION</legend>
                <table class="table-form">
                	<tr>
                    	<td>DATE:</td>
                        <td><input type="text" class="textbox datepicker" name="date" value="<?=$date?>" /></td>
                    </tr>
                    <tr>
                        <td>FROM PROJECT : </td>
                        <td>
                            <input type="text" class="textbox project" value="<?=$options->getAttribute('projects','project_id',$from_project_id,'project_name')?>"  />
                       		<input type="hidden" name="from_project_id" value="<?=$from_project_id?>" />
                        </td>
                    </tr>
                    <tr>
                        <td>TO PROJECT : </td>
                        <td>
                            <input type="text" class="textbox project" value="<?=$options->getAttribute('projects','project_id',$project_id,'project_name')?>"  />
                       		<input type="hidden" name="project_id" value="<?=$project_id?>" />
                        </td>
                    </tr>
                    <tr>
                        <td>ITEM :</td>
                        <td>
                            <input type="text" class="textbox stock_name" name="stock_name" value="<?=$options->getAttribute('productmaster','stock_id',$stock_id,'stock')?>" onclick="this.select();"  />
                            <input type="hidden" name="stock_id" value="<?=$stock_id?>"  />
                        </td>
                    </tr>	
                    <tr>
                    	<td>BATCHING PLANT OPERATOR:</td>
                        <td>
                        	<?php
							$operator_name = ($operator_id) ? $options->getAttribute('employee','employeeID',$operator_id,'employee_lname')." ,".$options->getAttribute('employee','employeeID',$operator_id,'employee_fname') : "";
                            ?>
                        	<input type="text" class="textbox ac_employee_name" value="<?=$operator_name?>" />
                            <input type="hidden" name="operator_id" value="<?=$operator_id?>" />
                       	</td>
                    </tr>
                    <!--<tr>
                    	<td>EQUIPMENT OPERATOR:</td>
                        <td>
                        	<input type="text" class="textbox ac_driver_name" value="<?=$options->getAttribute('drivers','driverID',$eq_operator_id,'driver_name')?>" />
                            <input type="hidden" name="eq_operator_id" value="<?=$eq_operator_id?>" />
                       	</td>
                    </tr> -->
                    <tr>	
                        <td>TOTAL VOLUME:</td>
                        <td><input type="text" class="textbox" name="total_vol" value="<?=$total_vol?>" /></td>
                    </tr>
                    <tr>
                        <td>PRICE/UNIT:</td>
                        <td><input type="text" class="textbox" name="price_unit" value="<?=$price_unit?>" /></td>
                    </tr>
                    <tr>
                    	<td style="vertical-align:top;">REMARKS:</td>
                        <td><textarea name="remarks" id="remarks" style="border:1px solid #c0c0c0; font-family:Arial, Helvetica, sans-serif; font-size:11px; width:100%; height:80px;" ><?=$remarks?></textarea></td>
                    </tr>
                    <tr>
                    	<td>BILLED? <em>(Optional)</em> <br /> <em>Check if YES</em></td>
                        <td><input type='checkbox' name="billed" value="1" <?php if($billed) echo "checked = 'checked'" ?>  /></td>
                    </tr>
                </table>	
            </fieldset>
            
            <fieldset style="border:none; border-top:1px solid #CCC; display:inline-block;">
                <legend>CONUSUMPTION</legend>
                <table class="table-form">
                	<tr>
                    	<td></td>
                        <td><em>QUANTITY</em></td>
                        <td><em>UNIT PRICE</em></td>
                        <td><em>AMOUNT</em></td>
                    </tr>
                    <tr>
                        <td>CEMENT:</td>
                        <td><input type="text" class="textbox first-field" name="cement" value="<?=$cement?>" /></td>
                        <td><input type="text" class="textbox second-field" name="cement_price" value="<?=$cement_price?>" /></td>
                        <td><input type="text" class="textbox third-field" value="<?=number_format($cement_price*$cement,2)?>" readonly="readonly" /></td>
                    </tr>
                    <tr>
                        <td>W.SAND:</td>
                        <td><input type="text" class="textbox first-field" name="wsand" value="<?=$wsand?>" /></td>
                        <td><input type="text" class="textbox second-field" name="wsand_price" value="<?=$wsand_price?>" /></td>
                        <td><input type="text" class="textbox third-field" value="<?=number_format($wsand*$wsand_price,2)?>" readonly="readonly" /></td>
                    </tr>
                    <tr>
                        <td>AGG. G-1:</td>
                        <td><input type="text" class="textbox first-field" name="agg_g1" value="<?=$agg_g1?>" /></td>
                        <td><input type="text" class="textbox second-field" name="agg_g1_price" value="<?=$agg_g1_price?>" /></td>
                        <td><input type="text" class="textbox third-field" value="<?=number_format($agg_g1*$agg_g1_price,2)?>" readonly="readonly" /></td>
                    </tr>
                    <tr>
                        <td>AGG. 3/4":</td>
                        <td><input type="text" class="textbox first-field" name="agg_34" value="<?=$agg_34?>" /></td>
                        <td><input type="text" class="textbox second-field" name="agg_34_price" value="<?=$agg_34_price?>" /></td>
                        <td><input type="text" class="textbox third-field" value="<?=number_format($agg_34*$agg_34_price,2)?>" readonly="readonly" /></td>
                    </tr>
                    <tr>
                        <td>AGG. 3/8":</td>
                        <td><input type="text" class="textbox first-field" name="agg_38" value="<?=$agg_38?>" /></td>
                        <td><input type="text" class="textbox second-field" name="agg_38_price" value="<?=$agg_38_price?>" /></td>
                        <td><input type="text" class="textbox third-field" value="<?=number_format($agg_38*$agg_38_price,2)?>" readonly="readonly" /></td>
                    </tr>
                    <tr>
                        <td>ADMIX:</td>
                        <td><input type="text" class="textbox first-field" name="admix" value="<?=$admix?>" /></td>
                        <td><input type="text" class="textbox second-field" name="admix_price" value="<?=$admix_price?>" /></td>
                        <td><input type="text" class="textbox third-field" value="<?=number_format($admix*$admix_price,2)?>" readonly="readonly" /></td>
                    </tr>
                    <tr>
                        <td>WATER:</td>
                        <td><input type="text" class="textbox first-field" name="water" value="<?=$water?>" /></td>
                        <td><input type="text" class="textbox second-field" name="water_price" value="<?=$water_price?>" /></td>
                        <td><input type="text" class="textbox third-field" value="<?=number_format($water*$water_price,2)?>" readonly="readonly" /></td>
                    </tr>
                    <tr>
                        <td>ELECTRICITY:</td>
                        <td><input type="text" class="textbox first-field" name="electricity" value="<?=$electricity?>" /></td>
                        <td><input type="text" class="textbox second-field" name="electricity_price" value="<?=$electricity_price?>" /></td>
                        <td><input type="text" class="textbox third-field" value="<?=number_format($electricity*$electricity_price,2)?>" readonly="readonly" /></td>
                    </tr>
					<tr>
                        <td>T. MIXER RENTAL:</td>
                        <td><input type="text" class="textbox first-field" name="tm_rental" value="<?=$tm_rental?>" /></td>
                        <td><input type="text" class="textbox second-field" name="tm_rental_price" value="<?=$tm_rental_price?>" /></td>
                        <td><input type="text" class="textbox third-field" value="<?=number_format($tm_rental*$tm_rental_price,2)?>" readonly="readonly" /></td>
                    </tr>
					<td>P.LOADER RENTAL:</td>
                        <td><input type="text" class="textbox first-field" name="pl_rental" value="<?=$pl_rental?>" /></td>
                        <td><input type="text" class="textbox second-field" name="pl_rental_price" value="<?=$pl_rental_price?>" /></td>
                        <td><input type="text" class="textbox third-field" value="<?=number_format($pl_rental*$pl_rental_price,2)?>" readonly="readonly" /></td>
                    </tr>
					<td>T. MOUNTED RENTAL:</td>
                        <td><input type="text" class="textbox first-field" name="tmd_rental" value="<?=$tmd_rental?>" /></td>
                        <td><input type="text" class="textbox second-field" name="tmd_rental_price" value="<?=$tmd_rental_price?>" /></td>
                        <td><input type="text" class="textbox third-field" value="<?=number_format($tmd_rental*$tmd_rental_price,2)?>" readonly="readonly" /></td>
                    </tr>
					<tr>
                        <td>MANPOWER:</td>
                        <td><input type="text" class="textbox first-field" name="manpower" value="<?=$manpower?>" /></td>
                        <td><input type="text" class="textbox second-field" name="manpower_price" value="<?=$manpower_price?>" /></td>
                        <td><input type="text" class="textbox third-field" value="<?=number_format($manpower*$manpower_price,2)?>" readonly="readonly" /></td>
                    </tr>
					<tr>
                        <td>FUEL & LUBRICANTS:</td>
                        <td><input type="text" class="textbox first-field" name="fuel" value="<?=$fuel?>" /></td>
                        <td><input type="text" class="textbox second-field" name="fuel_price" value="<?=$fuel_price?>" /></td>
                        <td><input type="text" class="textbox third-field" value="<?=number_format($fuel*$fuel_price,2)?>" readonly="readonly" /></td>
                    </tr>
					<tr>
                        <td>INCENTIVES:</td>
                        <td><input type="text" class="textbox first-field" name="incentives" value="<?=$incentives?>" /></td>
                        <td><input type="text" class="textbox second-field" name="incentives_price" value="<?=$incentives_price?>" /></td>
                        <td><input type="text" class="textbox third-field" value="<?=number_format($incentives*$incentives_price,2)?>" readonly="readonly" /></td>
                    </tr>
					<tr>
                        <td>DEPRECIATION COST:</td>
                        <td><input type="text" class="textbox first-field" name="depre" value="<?=$depre?>" /></td>
                        <td><input type="text" class="textbox second-field" name="depre_cost" value="<?=$depre_cost?>" /></td>
                        <td><input type="text" class="textbox third-field" value="<?=number_format($depre*$depre_cost,2)?>" readonly="readonly" /></td>
                    </tr>
                </table>
            </fieldset>
        </div>
        <?php if(!empty($status)){ ?>
        <div class="module_actions">
        	<div style="display:inline-block; margin-right:10px;">
            	Status:<br />
                <strong><?=$options->getTransactionStatusName($status)?></strong>
            </div>
            <div style="display:inline-block;">
            	Encoded by:<br />
                <strong><?=$options->getUserName($user_id)?></strong>
            </div>
        </div>
        <?php } ?>
        <div class="module_actions">
            <?php if(!empty($batch_prod_id)){ ?>
            <input type="submit" name="b" id="b" value="Update" />
            <?php }else{ ?>
            <input type="submit" name="b" id="b" value="Submit" />
            <?php } ?>
            <a href="admin.php?view=<?=$view?>"><input type="button" value="New" /></a>
        </div>
    </div>
    
<?php
}
?>
<?php
/*if($b == "Print Preview" && $eur_unit){

	echo "	<iframe id='JOframe' name='JOframe' frameborder='0' src='printIssuance.php?id=$eur_unit' width='100%' height='500'>
			</iframe>";
}
*/
			
?>
</form>
<script type="text/javascript">
j(function(){	

	jQuery(".first-field").keyup(function(){
		var quantity = jQuery(this).val();
		var price = jQuery(this).parent().next().find('.second-field').val();
		jQuery(this).parent().next().next().find('.third-field').val((quantity * price).toFixed(2));
	});
	
	jQuery(".second-field").keyup(function(){
		var price = jQuery(this).val();
		var quantity = jQuery(this).parent().prev().find('.first-field').val();
		jQuery(this).parent().next().find('.third-field').val((quantity * price).toFixed(2));
	});

	jQuery(".ac_employee_name").autocomplete({	
		source: "autocomplete/employees.php",
		minLength: 1,
		select: function(event, ui) {
			jQuery(this).val(ui.item.value);
			jQuery(this).next().val(ui.item.id);
		}
	});

	jQuery(".ac_driver_name").autocomplete({	
		source: "autocomplete/drivers.php",
		minLength: 1,
		select: function(event, ui) {
			jQuery(this).val(ui.item.value);
			jQuery(this).next().val(ui.item.id);
		}
	});
});
</script>
	