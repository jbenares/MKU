<?php
$aRequest	= array('b','sub_gchart_id','keyword');
$aHeader 	= 	array(
					'sub_gchart'
				);

$aVal = array();
if(count($aRequest) > 0){
	foreach($aRequest as $request){
		$aVal[$request] = $_REQUEST[$request];
	}
}
if(count($aHeader) > 0){
	foreach($aHeader as $header){
		$aVal[$header] = $_REQUEST[$header];
	}
}

if($aVal['b'] == "Submit"){
	if(empty($aVal['sub_gchart_id'])){
		if(count($aHeader) > 0){
			$sql = 
				"insert into 
					sub_gchart 
				set
			";
			foreach($aHeader as $header){
			$sql.="$header = '$aVal[$header]',";	
			}
			$sql = rtrim($sql,",");
		}
		$query = mysql_query($sql) or die(mysql_error());	
		$aVal['sub_gchart_id'] = mysql_insert_id();
		$msg = "Transaction Saved";
	}else{
		if(count($aHeader) > 0){
			$sql = 
				"update
					sub_gchart 
				set
			";
			foreach($aHeader as $header){
			$sql.="$header = '$aVal[$header]',";	
			}
			$sql = rtrim($sql,",");
			$sql.= "where sub_gchart_id = '$aVal[sub_gchart_id]'";
		}
		$query = mysql_query($sql) or die(mysql_error());	
		$msg = "Transaction Updated";
	}
	
}


if($aVal['sub_gchart_id'] && $aVal['b'] != "Search"){
	$result = mysql_query("
		select * from sub_gchart where sub_gchart_id = '$aVal[sub_gchart_id]'
	") or die(mysql_error());
	$aVal = mysql_fetch_assoc($result);
}
?>


<style type="text/css">
.table-form{
	display:inline-table;	
}
.table-form tr td:nth-child(1){
	text-align:right;
	font-weight:bold;
}
</style>
<form enctype='multipart/form-data' method="post" action="" id="newareaform" >
	<div class="module_actions">
    	Search <br />
    	<input type="text" class="textbox" name="keyword" value="<?=$aVal['keyword']?>" /><input type="submit" name="b" value="Search" />
        <a href="?view=<?=$view?>"><input type="button" name="b" value="New" /></a>
    </div>
	<?php if($aVal['b'] == "Search"): ?>
		<?php
        $page = $_REQUEST['page'];
        if(empty($page)) $page = 1;
         
        $limitvalue = $page * $limit - ($limit);
        
        $sql = "
            select
                *
            from
                sub_gchart
			where
				sub_gchart like '%$aVal[keyword]%'
        ";
              
        $pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
                
        $i=$limitvalue;
        $rs = $pager->paginate();
        
        $pagination	= $pager->renderFullNav("$view&b=Search&keyword=$aVal[keyword]");
        ?>
        <div class="pagination">
            <?=$pagination?>
        </div>
        <table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table">
            <tr>				
                <td width="20">#</td>
                <td width="20"></td>
                <td>SUB GCHART</td>
                
            </tr>  
			<?php								
            while($r=mysql_fetch_assoc($rs)) {
                
                echo '<tr>';
                echo '<td width="20">'.++$i.'</td>';
                echo '<td width="15"><a href="admin.php?view='.$view.'&sub_gchart_id='.$r['sub_gchart_id'].'" title="Show Details"><img src="images/edit.gif" border="0"></a></td>';
                echo '<td>'.$r['sub_gchart'].'</td>';	
                echo '</tr>';
            }
            ?>
        </table>
        <div class="pagination">
             <?=$pagination?>
        </div>
    <?php else: #end else?>
    <div class=form_layout_productmaster>
        <?php if(!empty($msg)) echo '<div id="status_update" class="ui-state-highlight ui-corner-all" style="padding: 0px 0.7em; text-align:left;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right:.3em;"></span> '.$msg.'</p></div>'; ?>
        <div class="module_title"><img src='images/user_orange.png'><?=strtoupper($transac->getMname($view))?></div>
        <table class="table-form">
        	<input type="hidden" name="sub_gchart_id" value="<?=$aVal['sub_gchart_id']?>" />
            <tr>
                <td>Description:</td>
                <td><input type="text" class="textbox"  name="sub_gchart" value="<?=$aVal['sub_gchart']?>" /></td>
            </tr>
        </table>
       	<div class="module_actions">
            <input type=submit name=b value='Submit' class=buttons>
            <input type=reset value='Clear Form' class=buttons>
        </div>
    </div>
    
    <?php endif; #end if ?>
</form>