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

	$search_subd		= $_REQUEST['search_subd'];
	$subd				= $_REQUEST['subd'];
	$subd_id			= $_REQUEST['subd_id'];
	$subd_address1 		= $_REQUEST['subd_address1'];
	$subd_address2 		= $_REQUEST['subd_address2'];
	
	
	if($b == 'D'){ 
		mysql_query("
			delete from
				subd
			where
				subd_id = '$subd_id'
		") or die(mysql_error());
	
	}
	
	if($b=="Submit"){
		$query="
			insert into 
				subd
			set
				subd		= '$subd',
				subd_address1 = '$subd_address1',
				subd_address2 = '$subd_address2'
		";	
		
		mysql_query($query) or die(mysql_error());
		$subd_id = mysql_insert_id();
		$msg = "Transaction Added";
		
	}else if($b=="Update"){
		$query="
			update
				subd
			set
				subd	= '$subd',
				subd_address1 = '$subd_address1',
				subd_address2 = '$subd_address2'
			where
				subd_id = '$subd_id'
		";	
		
		mysql_query($query) or die(mysql_error());
		
		$msg = "Transaction Updated";
	}
	
	$query="
		select
			*
		from
			subd 
		where
			subd_id = '$subd_id'
	";
	
	$result=mysql_query($query) or die(mysql_error());
	$r=mysql_fetch_assoc($result);
	
	$subd			= $r['subd'];
?>

<form name="header_form" id="header_form" action="" method="post">
<div class="module_actions">	
    <div class='inline'>
        Subdivision : <br />  
        <input type="text" class="textbox"  name="search_subd" value="<?=$search_subd?>"  onclick="this.select();"  autocomplete="off" />
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
			subd
    ";
        
    if(!empty($search_subd)){
    $sql.="
		where
			subd like '$search_subd%'
    ";
    }
	
	$sql.="
		order by subd asc
	";
  
    $pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
            
    $i=$limitvalue;
    $rs = $pager->paginate();
	
	$pagination	= $pager->renderFullNav("$view&b=Search&search_subd=$search_subd");
    ?>
    <div class="pagination">
        <?=$pagination?>
    </div>
    <table id="my_table" cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table">
    <tr>				
    
        <th width="20">#</th>
        <th width="20"></th>
        <th>SUBDIVISION</th>
    </tr>  
    <?php								
    while($r=mysql_fetch_assoc($rs)) {
        
        echo '<tr>';
        echo '<td width="20">'.++$i.'</td>';
        echo '<td width="15"><a href="admin.php?view='.$view.'&subd_id='.$r['subd_id'].'" title="Show Details"><img src="images/edit.gif" border="0"></a></td>';
		echo '<td>'."$r[subd]".'</td>';	
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
        <div class="module_title"><img src='images/user_orange.png'>SUBDIVISION</div>
        <div class="module_actions">
            <input type="hidden" name="subd_id" value="<?=$subd_id?>" />
            <div id="messageError">
                <ul>
                </ul>
            </div>
            <table>
            	<tr>
                	<td>Subdivision:</td>
                    <td><input type="text" class="textbox" style="width:500px;" name="subd" value="<?=$subd?>" /></td>
                </tr>
                <tr>
                	<td>Address 1:</td>
                    <td><input type="text" class="textbox" style="width:500px;" name="subd_address1" value="<?=$subd_address1?>" /></td>
                </tr>
                <tr>
                	<td>Address 2:</td>
                    <td><input type="text" class="textbox" style="width:500px;" name="subd_address2" value="<?=$subd_address2?>" /></td>
                </tr>
            </table>
        </div>
        <div class="module_actions">
            <?php
            if(!empty($subd_id)){
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
			if(!empty($subd_id)){
            ?>
            <a href="admin.php?view=<?=$view?>&subd_id=<?=$subd_id?>&b=D" onclick="return approve_confirm();"><input type="button" value="Delete" /></a>
            <?php
			}
            ?>
        </div>
    </div>
    
<?php
}
?>
<?php
/*if($b == "Print Preview" && $subd){

	echo "	<iframe id='JOframe' name='JOframe' frameborder='0' src='printIssuance.php?id=$subd' width='100%' height='500'>
			</iframe>";
}
*/
			
?>
</form>
<script type="text/javascript">
j(function(){	
});
</script>
	