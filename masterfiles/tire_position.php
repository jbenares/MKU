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
	
	#SEARCH
	$search_driver_name		= $_REQUEST['search_driver_name'];

	#HEADER
	$driverID		= $_REQUEST['tire_pos_id'];
	$driver_name		= $_REQUEST['position'];
	
	if($b == 'D'){ 
		$id	= $_REQUEST['id'];
		mysql_query("
			delete from
				tire_position
			where
				tire_pos_id = '$id'
		") or die(mysql_error());
		$msg = "Position Deleted.";
	}
	
	if($b=="Submit"){
		$query="
			insert into 
				tire_position
			set
				position = '$driver_name'
		";
		
		mysql_query($query) or die(mysql_error());
		$driverID = mysql_insert_id();
		$msg = "Transaction Added";
		
	}else if($b=="Update"){
		$query="
			update
				tire_position
			set
				position = '$driver_name'
			where
				tire_pos_id = '$driverID'
		";	
		
		mysql_query($query) or die(mysql_error());
		
		$msg = "Transaction Updated";
	}
	
	$query="
		select
			*
		from
			tire_position
		where
			tire_pos_id = '$driverID'
	";
	
	$result=mysql_query($query) or die(mysql_error());
	$r=mysql_fetch_assoc($result);
	
	$driver_name = $r['position'];
?>

<form name="header_form" id="header_form" action="" method="post">
<div class="module_actions">	
    <div class='inline'>
        Position : <br />
        <input type="text" class="textbox"  name="search_driver_name" value="<?=$search_driver_name?>"  onclick="this.select();"  autocomplete="off" placeholder="Search"  />
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
			tire_position
		where
			1 = 1
    ";
        
    if(!empty($search_driver_name)){
    $sql.="
		and
			position like '$search_driver_name%'
    ";
    }
	
	$sql.="
		order by position asc
	";
  
    $pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
            
    $i=$limitvalue;
    $rs = $pager->paginate();
	
	$pagination	= $pager->renderFullNav("$view&b=Search&search_driver_name=$search_driver_name");
    ?>
    <div class="pagination">
        <?=$pagination?>
    </div>
    <table id="my_table" cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table">
    <tr>				
    
        <th width="20">#</th>
        <th width="20"></th>
        <th>POSITION</th>
    </tr>  
    <?php								
    while($r=mysql_fetch_assoc($rs)) {
        
        echo '<tr>';
        echo '<td width="20">'.++$i.'</td>';
        echo '<td width="15"><a href="admin.php?view='.$view.'&tire_pos_id='.$r['tire_pos_id'].'" title="Show Details"><img src="images/edit.gif" border="0"></a></td>';
		echo '<td>'."$r[position]".'</td>';
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
        <div class="module_title"><img src='images/user_orange.png'>TIRE POSITION</div>
        <div class="module_actions">
            <input type="hidden" name="tire_pos_id" value="<?=$driverID?>" />
            <div id="messageError">
                <ul>
                </ul>
            </div>
            
            <table class="table-form">
                <tr>
                	<td>TIRE POSITION:</td>
                    <td><input type="text" class="textbox" name="position" value="<?=$driver_name?>"/></td>
                </tr>
            </table>
            
        </div>
        <div class="module_actions">
            <?php
            if(!empty($driverID)){
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
            <a href="admin.php?view=<?=$view?>&id=<?=$driverID?>&b=D" onclick="return approve_confirm();"><input type="button" value="Delete" /></a>
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
