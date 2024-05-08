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

	$search_eur_ref		= $_REQUEST['search_eur_ref'];
	
	$eur_ref_id			= $_REQUEST['eur_ref_id'];
	$eur_unit_id		= $_REQUEST['eur_unit_id'];
	$eur_ref			= $_REQUEST['eur_ref'];
	
	
		
	if($b=="Submit"){
		$query="
			insert into 
				eur_ref
			set
				eur_unit_id = '$eur_unit_id',
				eur_ref = '$eur_ref'
		";	
		
		mysql_query($query) or die(mysql_error());
		$eur_ref_id = mysql_insert_id();
		$msg = "Transaction Added";
		
	}else if($b=="Update"){
		$query="
			update
				eur_ref
			set
				eur_unit_id = '$eur_unit_id',
				eur_ref = '$eur_ref'
			where
				eur_ref_id = '$eur_ref_id'
		";	
		
		mysql_query($query) or die(mysql_error());
		
		$msg = "Transaction Updated";
	}else if($b == "Void"){
		mysql_query("
			update eur_ref set eur_ref_void = '1' where eur_ref_id = '$eur_ref_id'
		") or die(mysql_error());
		
	}
	
	$query="
		select
			*
		from
			eur_ref 
		where
			eur_ref_id = '$eur_ref_id'
	";
	
	$result=mysql_query($query) or die(mysql_error());
	$r=mysql_fetch_assoc($result);
	
	$eur_unit_id			= $r['eur_unit_id'];
	$eur_ref				= $r['eur_ref'];
?>

<form name="header_form" id="header_form" action="" method="post">
<div class="module_actions">	
    <div class='inline'>
        REFERENCE : <br />  
        <input type="text" class="textbox"  name="search_eur_ref" value="<?=$search_eur_ref?>"  onclick="this.select();"  autocomplete="off" />
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
			eur_ref as r, eur_unit as u
		where
			eur_ref_void = '0'
		and
			eur_unit_void = '0'
		and
			r.eur_unit_id = u.eur_unit_id
    ";
        
    if(!empty($search_eur_ref)){
    $sql.="
		and
			eur_ref like '$search_eur_ref%'
    ";
    }
	
	$sql.="
		order by eur_ref asc
	";
  
    $pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
            
    $i=$limitvalue;
    $rs = $pager->paginate();
	
	$pagination	= $pager->renderFullNav("$view&b=Search&search_eur_ref=$search_eur_ref");
    ?>
    <div class="pagination">
        <?=$pagination?>
    </div>
    <table id="my_table" cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table">
    <tr>				
    
        <th width="20">#</th>
        <th width="20"></th>
        <th>UNIT</th>
        <th>REFERENCE</th>
    </tr>  
    <?php								
    while($r=mysql_fetch_assoc($rs)) {
        
        echo '<tr>';
        echo '<td width="20">'.++$i.'</td>';
        echo '<td width="15"><a href="admin.php?view='.$view.'&eur_ref_id='.$r['eur_ref_id'].'" title="Show Details"><img src="images/edit.gif" border="0"></a></td>';
		echo '<td>'."$r[eur_unit]".'</td>';	
		echo '<td>'.$r['eur_ref'].'</td>';	
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
            <input type="hidden" name="eur_ref_id" value="<?=$eur_ref_id?>" />
            <div id="messageError">
                <ul>
                </ul>
            </div>
            <table>
            	<tr>
                	<td>UNIT:</td>
                    <td><?=$options->getTableAssoc($eur_unit_id,'eur_unit_id','Select Unit',"select * from eur_unit where eur_unit_void = '0' order by eur_unit asc",'eur_unit_id','eur_unit')?></td>
                </tr>
                <tr>
                	<td>REFERENCE:</td>
                    <td><input type="text" class="textbox" name="eur_ref" value="<?=$eur_ref?>" /></td>
                </tr>
            </table>
        </div>
        <div class="module_actions">
            <?php if(!empty($eur_ref_id)){ ?>
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
	