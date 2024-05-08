<?php
function getCustomerName($application_id){
	$result = mysql_query("	
		select
			*
		from
			application as a, customer as c
		where
			a.customer_id = c.customer_id
		and
			a.application_id = '$application_id'
	") or die(mysql_error());
	$r = mysql_fetch_assoc($result);
	
	return "$r[customer_last_name], $r[customer_first_name] $r[customer_middle_name] $r[customer_appel]";
}

function getOutbal($application_id){
	$result = mysql_query("
		select 
			*
		from
			dprc_payment as p, dprc_ledger as l
		where
			p.dprc_payment_id = l.dprc_payment_id
		and
			p.application_id = '$application_id'
		order by
			dprc_ledger_id desc
		limit 0,1
	") or die(mysql_error());
	$r = mysql_fetch_assoc($result);
	return $r['outbal'];
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

.table-form tr td:nth-child(1){
	text-align:right;
	font-weight:bold;
}


</style>
<?php
	#DO NOT REMOVE
	$b					= $_REQUEST['b'];
	$user_id			= $_SESSION['userID'];

	#HEADER
	$search_inv			= $_REQUEST['search_inv'];
	
	#ID
	$inv_id				= $_REQUEST['inv_id'];
	
	$subd_id			= $_REQUEST['subd_id'];
	$model_id			= $_REQUEST['model_id'];
	$inv_phase			= $_REQUEST['inv_phase'];
	$inv_block			= $_REQUEST['inv_block'];
	$inv_lot			= $_REQUEST['inv_lot'];
	$inv_lot_area		= $_REQUEST['inv_lot_area'];
	$inv_floor_area		= $_REQUEST['inv_floor_area'];
	$application_id		= $_REQUEST['application_id'];
	
	if($b == 'D'){ 
		mysql_query("
			update
				dprc_inventory
			set	
				inv_void = '1'
			where
				inv_id = '$inv_id'
		") or die(mysql_error());
	}
	
	if($b=="Submit"){
		$query="
			insert into 
				dprc_inventory
			set
				subd_id				= '$subd_id',
				model_id			= '$model_id',
				inv_phase			= '$inv_phase',
				inv_block			= '$inv_block',
				inv_lot				= '$inv_lot',
				inv_lot_area		= '$inv_lot_area',
				inv_floor_area		= '$inv_floor_area',
				application_id		= '$application_id'
		";	
		
		mysql_query($query) or die(mysql_error());
		$inv_id = mysql_insert_id();
		$msg = "Transaction Added";
		
	}else if($b=="Update"){
		$query="
			update
				dprc_inventory
			set
				subd_id				= '$subd_id',
				model_id			= '$model_id',
				inv_phase			= '$inv_phase',
				inv_block			= '$inv_block',
				inv_lot				= '$inv_lot',
				inv_lot_area		= '$inv_lot_area',
				inv_floor_area		= '$inv_floor_area',
				application_id		= '$application_id'
			where
				inv_id = '$inv_id'
		";	
		
		mysql_query($query) or die(mysql_error());
		
		$msg = "Transaction Updated";
	}
	
	$query="
		select
			*
		from
			dprc_inventory 
		where
			inv_id = '$inv_id'
		and
			inv_void = '0'
	";
	
	$result=mysql_query($query) or die(mysql_error());
	$r=mysql_fetch_assoc($result);
	
	$subd_id			= $r['subd_id'];
	$model_id			= $r['model_id'];
	$inv_phase			= $r['inv_phase'];
	$inv_block			= $r['inv_block'];
	$inv_lot			= $r['inv_lot'];
	$inv_lot_area		= $r['inv_lot_area'];
	$inv_floor_area		= $r['inv_floor_area'];
	$application_id		= $r['application_id'];
?>

<form name="header_form" id="header_form" action="" method="post">
<div class="module_actions">	
    <div class='inline'>
        Model : <br />  
        <input type="text" class="textbox"  name="search_model" value="<?=$search_model?>"  onclick="this.select();"  autocomplete="off" />
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
			subd as s, model as m, dprc_inventory as d
		where
			s.subd_id = d.subd_id
		and
			m.model_id = d.model_id
    ";
        
    if(!empty($search_model)){
    $sql.="
		and
			model like '$search_model%'
    ";
    }
	
	$sql.="
		order by model asc
	";
  
    $pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
            
    $i=$limitvalue;
    $rs = $pager->paginate();
	
	$pagination	= $pager->renderFullNav("$view&b=Search&search_model=$search_model");
    ?>
    <div class="pagination">
        <?=$pagination?>
    </div>
    <table id="my_table" cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table">
    <tr>				
    
        <th width="20">#</th>
        <th width="20"></th>
        <th>SUBDIVISION</th>
        <th>MODEL</th>
        <th>PHASE</th>
        <th>BLOCK</th>
        <th>LOT</th>
        <th>LOT AREA</th>
        <th>FLOOR AREA</th>
		<th>USED</th>
        <th>APPLICATION</th>
        <th>OUTBAL</th>
        <th>NET LOAN</th>
    </tr>  
    <?php								
    while($r=mysql_fetch_assoc($rs)) {
		
		$used = ($r['application_id']) ? "UNAVAILABLE" : "AVAILABLE";
		$used_style = ($r['application_id']) ? "style='color:#F00;'" : "style='color:#0F0;'";
        
		$customer = getCustomerName($r['application_id']);
		
        echo '<tr>';
        echo '<td width="20">'.++$i.'</td>';
        echo '<td width="15"><a href="admin.php?view='.$view.'&inv_id='.$r['inv_id'].'" title="Show Details"><img src="images/edit.gif" border="0"></a></td>';
		echo '<td>'."$r[subd]".'</td>';	
		echo '<td>'."$r[model]".'</td>';	
		echo '<td>'."$r[inv_phase]".'</td>';	
		echo '<td>'."$r[inv_block]".'</td>';	
		echo '<td>'."$r[inv_lot]".'</td>';	
		echo '<td>'."$r[inv_lot_area]".'</td>';	
		echo '<td>'."$r[inv_floor_area]".'</td>';	
		echo '<td '.$used_style.'>'."$used".'</td>';	
		echo '<td>'."$customer".'</td>';	
		echo '<td style="text-align:right;">'.number_format(getOutbal($r['application_id']),2).'</td>';	
		echo '<td style="text-align:right;">'.number_format($options->getAttribute('application','application_id',$r['application_id'],'net_loan'),2).'</td>';	
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
        <div class="module_title"><img src='images/user_orange.png'>DPRC INVENTORY</div>
        <div class="module_actions">
            <input type="hidden" name="inv_id" value="<?=$inv_id?>" />
            <div id="messageError">
                <ul>
                </ul>
            </div>
            
            <table class="table-form">
            	<tr>
                	<td>Subdivision:</td>
                    <td><?=$options->getTableAssoc($subd_id,'subd_id','Select Subdivision',"select * from subd order by subd asc",'subd_id','subd')?></td>
                </tr>
                <tr>
                	<td>Model:</td>
                    <td><?=$options->getTableAssoc($model_id,'model_id','Select Model',"select * from model order by model asc",'model_id','model')?></td>
                </tr>
                <tr>
                	<td>Phase:</td>
                    <td><input type="text" class="textbox" style="width:100%;" name="inv_phase" value="<?=$inv_phase?>"/></td>
                </tr>
                <tr>
                	<td>Block:</td>
                    <td><input type="text" class="textbox" style="width:100%;" name="inv_block" value="<?=$block?>" /></td>
                </tr>
                <tr>
                	<td>Lot:</td>
                    <td><input type="text" class="textbox" style="width:100%;" name="inv_lot" value="<?=$inv_lot?>" /></td>
                </tr>
                <tr>
                	<td>Lot Area:</td>
                    <td>
                    	<input type="text" class="textbox3" name="inv_lot_area"  value="<?=$inv_lot_area?>"/>
                    	<b>Floor Area:</b>
                    	<input type="text" class="textbox3" name="inv_floor_area" value="<?=$inv_floor_area?>" />
                   	</td>
                </tr>
                <tr>
                	<td>Lot:</td>
                    <td><input type="text" class="textbox" style="width:100%;" name="inv_lot" value="<?=$inv_lot?>" /></td>
                </tr>
                <tr>
                	<?php
					$q = "
						select
							a.application_id, c.customer_last_name, c.customer_first_name
						from
							application as a, customer as c
						where
							a.customer_id = c.customer_id
					";
                    ?>
                	<td>Application:</td>
                    <td><?=$options->getTableAssoc($application_id,'application_id','Select Application',$q,'application_id', NULL, array('application_id','customer_first_name','customer_last_name'))?></td>
                </tr>            
            </table>
            
        </div>
        <div class="module_actions">
            <?php
            if(!empty($model)){
            ?>
            <input type="submit" name="b" id="b" value="Update" />
            <?php
            }else{
            ?>
            <input type="submit" name="b" id="b" value="Submit" />
            <?php
            }
            ?>
            <a href="admin.php?view=<?=$view?>"><input type="button" value="New" /></a>
            <?php
			if(!empty($model)){
            ?>
            <a href="admin.php?view=<?=$view?>&inv_id=<?=$inv_id?>&b=D" onclick="return approve_confirm();"><input type="button" value="Delete" /></a>
            <?php
			}
            ?>
        </div>
    </div>
    
<?php
}
?>
<?php
/*if($b == "Print Preview" && $model){

	echo "	<iframe id='JOframe' name='JOframe' frameborder='0' src='printIssuance.php?id=$model' width='100%' height='500'>
			</iframe>";
}
*/
			
?>
</form>
<script type="text/javascript">
j(function(){	
});
</script>
	