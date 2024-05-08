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

#SEARCH
$search_issuance_header_id			= $_REQUEST['search_issuance_header_id'];

if( $b == "GENERATE A/P" ){
	
	$result = mysql_query("
		select
			*
		from
			issuance_header as h, issuance_detail as d
		where
			h.issuance_header_id = d.issuance_header_id
		and
			status = 'F'
		and
			dprc_exported = '0' 
		and
			h.project_id in ('36','45','46')
	") or die(mysql_error());
	
	while($r = mysql_fetch_assoc($result)){
		
		mysql_query("
			insert into
				dprc_ap
			set
				issuance_detail_id = '$r[issuance_detail_id]',
				issuance_header_id = '$r[issuance_header_id]',
				stock_id = '$r[stock_id]',
				quantity = '$r[quantity]',
				price = '$r[price]',
				amount  = '$r[amount]',
				description = '$r[description]',
				account_id = '$r[account_id]',
				equipment_id = '$r[equipment_id]',
				joborder_header_id =  '$r[joborder_header_id]',
				quantity_cum = '$r[quantity_cum]',
				driverID = '$r[driverID]',
				_reference = '$r[_reference]',
				_unit = '$r[_unit]'
		") or die(mysql_error());	
		
		
		$issuance_detail_id = $r['issuance_detail_id'];
		mysql_query("
			update
				issuance_detail
			set
				dprc_exported = '1'
			where
				issuance_detail_id = '$issuance_detail_id'
		") or die(mysql_error());
	}
}
?>

<form name="header_form" id="header_form" action="" method="post">
<div class="module_actions">	
	<img src="images/find.png" />
    <div class='inline'>
        R.I.S # : <br />  
        <input type="text" class="textbox"  name="search_issuance_header_id" value="<?=$search_issuance_header_id?>"  onclick="this.select();"  autocomplete="off" />
    </div>   
    <input type="submit" name="b" value="Search" />
    <input type="submit" name="b" value="GENERATE A/P" />
</div>

<?php
if($b == "Search" || 1){
?>
	<?php
    $page = $_REQUEST['page'];
    if(empty($page)) $page = 1;
     
    $limitvalue = $page * $limit - ($limit);
    
    $sql = "
		select
        	*
        from
			dprc_ap ap, productmaster as p, issuance_header as h, projects as pr
		where
			ap.issuance_header_id = h.issuance_header_id
		and
			ap.stock_id = p.stock_id
		and
			h.project_id = pr.project_id
    ";
        
    if(!empty($search_issuance_header_id)){
    $sql.="
		and
			ap.issuance_header_id like '$search_issuance_header_id%'
    ";
    }
	
	/*
	$sql.="
		order by model asc
	";
  */
    $pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
            
    $i=$limitvalue;
    $rs = $pager->paginate();
	
	$pagination	= $pager->renderFullNav("$view&b=Search&search_issuance_header_id=$search_issuance_header_id");
    ?>
    <div class="pagination">
        <?=$pagination?>
    </div>
    <table id="my_table" cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table">
    <tr>				
    
        <th width="20">#</th>
        <th width="20"></th>
        <th>PROJECT</th>
        <th>R.I.S. #</th>
        <th>ITEM</th>
        <th>QUANTITY</th>
        <th>UNIT</th>
        <th>PRICE</th>
        <th>AMOUNT</th>
    </tr>  
    <?php								
    while($r=mysql_fetch_assoc($rs)) {
		
		$used = ($r['application_id']) ? "AVAILABLE" : "UNAVAILABLE";
		$used_style = ($r['application_id']) ? "style='color:#0F0;'" : "style='color:#F00;'";
        
        echo '<tr>';
        echo '<td width="20">'.++$i.'</td>';
        echo '<td width="15"><input type="checkbox" name="ids" value="'.$r['dprc_ap_id'].'" ></td>';
		echo '<td>'."$r[project_name]".'</td>';	
		echo '<td>'.str_pad($r['issuance_header_id'],7,0,STR_PAD_LEFT).'</td>';	
		echo '<td>'."$r[stock]".'</td>';	
		echo '<td style="text-align:right;">'.number_format($r[quantity],2).'</td>';	
		echo '<td>'."$r[unit]".'</td>';	
		echo '<td style="text-align:right;">'.number_format($r[price],2).'</td>';	
		echo '<td style="text-align:right;">'.number_format($r[amount],2).'</td>';	
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
	