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
	$b					= $_REQUEST['b'];
	//$user_id			= $_SESSION['userID'];

	$model_id			= $_REQUEST['model_id'];
	
	$search_model		= $_REQUEST['search_model'];
	$subd_id			= $_REQUEST['subd_id'];
	$model				= $_REQUEST['model'];
	$package_type		= $_REQUEST['package_type'];
	$lot_price			= $_REQUEST['lot_price'];
	$floor_price		= $_REQUEST['floor_price'];
	$lot				= $_REQUEST['lot'];
	$block				= $_REQUEST['block'];
	$phase				= $_REQUEST['phase'];
	$remarks			= $_REQUEST['remarks'];
	
	if($b == 'D'){ 
		mysql_query("
			delete from
				model
			where
				model_id = '$model_id'
		") or die(mysql_error());
	
	}
	
	if($b=="Submit"){
		$query="
			insert into 
				model
			set
				subd_id = '$subd_id',
				model = '$model',
				package_type = '$package_type',
				lot_price = '$lot_price',
				floor_price = '$floor_price',
				lot = '$lot',
				block = '$block',
				phase = '$phase',
				remarks = '$remarks'
		";	
		
		mysql_query($query) or die(mysql_error());
		$model_id = mysql_insert_id();
		$msg = "Transaction Added";
		
	}else if($b=="Update"){
		$query="
			update
				model
			set
				subd_id = '$subd_id',
				model = '$model',
				package_type = '$package_type',
				lot_price = '$lot_price',
				floor_price = '$floor_price',
				lot = '$lot',
				block = '$block',
				phase = '$phase',
				remarks = '$remarks'
			where
				model_id = '$model_id'
		";	
		
		mysql_query($query) or die(mysql_error());
		
		$msg = "Transaction Updated";
	}
	
	$query="
		select
			*
		from
			model 
		where
			model_id = '$model_id'
	";
	
	$result=mysql_query($query) or die(mysql_error());
	$r=mysql_fetch_assoc($result);
	
	$search_model		= $r['search_model'];
	$subd_id			= $r['subd_id'];
	$model				= $r['model'];
	$package_type		= $r['package_type'];
	$lot_price			= $r['lot_price'];
	$floor_price		= $r['floor_price'];
	$lot				= $r['lot'];
	$block				= $r['block'];
	$phase				= $r['phase'];
	$remarks			= $r['remarks'];
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
			model
    ";
        
    if(!empty($search_model)){
    $sql.="
		where
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
        <th>MODEL</th>
    </tr>  
    <?php								
    while($r=mysql_fetch_assoc($rs)) {
        
        echo '<tr>';
        echo '<td width="20">'.++$i.'</td>';
        echo '<td width="15"><a href="admin.php?view='.$view.'&model_id='.$r['model_id'].'" title="Show Details"><img src="images/edit.gif" border="0"></a></td>';
		echo '<td>'."$r[model]".'</td>';	
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
        <div class="module_title"><img src='images/user_orange.png'>MODEL</div>
        <div class="module_actions">
            <input type="hidden" name="model_id" value="<?=$model_id?>" />
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
                	<td>Phase:</td>
                    <td><input type="text" class="textbox" style="width:100%;" name="phase" value="<?=$phase?>"/></td>
                </tr>
                <tr>
                	<td>Block:</td>
                    <td><input type="text" class="textbox" style="width:100%;" name="block" value="<?=$block?>" /></td>
                </tr>
                <tr>
                	<td>Lot:</td>
                    <td><input type="text" class="textbox" style="width:100%;" name="lot" value="<?=$lot?>" /></td>
                </tr>
                <tr>
                	<td>Model Name:</td>
                    <td><input type="text" class="textbox" style="width:100%;" name="model" value="<?=$model?>" /></td>
                </tr>
                <tr>
                	<td>Package Type:</td>
                    <td>
                    	<?=$options->getTableAssoc($package_type,'package_type','Select Package Type',"select * from dprc_package_types order by package_type asc",'package_type_id','package_type')?>
                    </td>
                </tr>
                <tr>
                	<td>Lot Price (per sqm):</td>
                    <td>
                    	<input type="text" class="textbox3" name="lot_price"  value="<?=$lot_price?>"/>
                    	<b>Floor Price (per sqm):</b>
                    	<input type="text" class="textbox3" name="floor_price" value="<?=$floor_price?>" />
                   	</td>
                </tr>
                <tr>
                	<td style="vertical-align:top;">Remarks</td>
                    <td colspan="3">
                    	<textarea style="border:1px solid #c0c0c0; width:100%;" name="remarks"><?=$remarks?></textarea>
                    </td>
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
            <a href="admin.php?view=<?=$view?>&model_id=<?=$model_id?>&b=D" onclick="return approve_confirm();"><input type="button" value="Delete" /></a>
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
	