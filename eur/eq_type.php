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

	$search_eq_type		= $_REQUEST['search_eq_type'];
	
	$eq_type_id			= $_REQUEST['eq_type_id'];
	$eq_type			= $_REQUEST['eq_type'];
	
	
		
	if($b=="Submit"){
		$query="
			insert into 
				eq_type
			set
				eq_type_id = '$eq_type_id',
				eq_type = '$eq_type'
		";	
		
		mysql_query($query) or die(mysql_error());
		$eq_type_id = mysql_insert_id();
		$msg = "Transaction Added";
		
	}else if($b=="Update"){
		$query="
			update
				eq_type
			set
				eq_type = '$eq_type'
			where
				eq_type_id = '$eq_type_id'
		";	
		
		mysql_query($query) or die(mysql_error());
		
		$msg = "Transaction Updated";
	}else if($b == "Void"){
		mysql_query("
			update eq_type set eq_type_void = '1' where eq_type_id = '$eq_type_id'
		") or die(mysql_error());
		
	}
	
	$query="
		select
			*
		from
			eq_type 
		where
			eq_type_id = '$eq_type_id'
	";
	
	$result=mysql_query($query) or die(mysql_error());
	$r=mysql_fetch_assoc($result);
	
	$eq_type_id			= $r['eq_type_id'];
	$eq_type			= $r['eq_type'];
?>

<form name="header_form" id="header_form" action="" method="post">
<div class="module_actions">	
    <div class='inline'>
        EQPMT TYPE : <br />  
        <input type="text" class="textbox"  name="search_eq_type" value="<?=$search_eq_type?>"  onclick="this.select();"  autocomplete="off" />
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
			eq_type
		and
			eq_type_void = '0'
    ";
        
    if(!empty($search_eq_type)){
    $sql.="
		and
			eq_type like '$search_eq_type%'
    ";
    }
	
	$sql.="
		order by eq_type asc
	";
  
    $pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
            
    $i=$limitvalue;
    $rs = $pager->paginate();
	
	$pagination	= $pager->renderFullNav("$view&b=Search&search_eq_type=$search_eq_type");
    ?>
    <div class="pagination">
        <?=$pagination?>
    </div>
    <table id="my_table" cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table">
    <tr>				
    
        <th width="20">#</th>
        <th width="20"></th>
        <th>EQPMT TYPE</th>
    </tr>  
    <?php								
    while($r=mysql_fetch_assoc($rs)) {
        
        echo '<tr>';
        echo '<td width="20">'.++$i.'</td>';
        echo '<td width="15"><a href="admin.php?view='.$view.'&eq_type_id='.$r['eq_type_id'].'" title="Show Details"><img src="images/edit.gif" border="0"></a></td>';
		echo '<td>'."$r[eq_type]".'</td>';	
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
        <div class="module_title"><img src='images/user_orange.png'>EUR REFERENCE</div>
        <div class="module_actions">
            <input type="hidden" name="eq_type_id" value="<?=$eq_type_id?>" />
            <div id="messageError">
                <ul>
                </ul>
            </div>
            <table>
                <tr>
                	<td>REFERENCE:</td>
                    <td><input type="text" class="textbox" name="eq_type" value="<?=$eq_type?>" /></td>
                </tr>
            </table>
        </div>
        <div class="module_actions">
            <?php if(!empty($eq_type_id)){ ?>
            <input type="submit" name="b" id="b" value="Update" />
            <input type="submit" name="b" id="b" value="Void" />
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
});
</script>
	