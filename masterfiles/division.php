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
	$b						= $_REQUEST['b'];
	$user_id				= $_SESSION['userID'];

	$search_division		= $_REQUEST['search_division'];
	$divisionID				= $_REQUEST['divisionID'];
	
	$companyID				= $_REQUEST['companyID'];
	$division_name			= $_REQUEST['division_name'];
	$division_abbrevation	= $_REQUEST['division_abbrevation'];
	
	if($b == 'D'){ 
		mysql_query("
			update 
				division
			set
				division_void = '1'
			where
				divisionID = '$divisionID'
		") or die(mysql_error());
		$msg = "Transaction Voided.";
	}
	
	if($b=="Submit"){
		$query="
			insert into 
				division
			set
				companyID = '$companyID',
				division_name = '$division_name',
				division_abbrevation = '$division_abbrevation'
		";	
		
		mysql_query($query) or die(mysql_error());
		$divisionID = mysql_insert_id();
		$msg = "Transaction Added";
		
	}else if($b=="Update"){
		$query="
			update
				division
			set
				companyID = '$companyID',
				division_name = '$division_name',
				division_abbrevation = '$division_abbrevation'
			where
				divisionID = '$divisionID'
		";	
		
		mysql_query($query) or die(mysql_error());
		
		$msg = "Transaction Updated";
	}
	
	$query="
		select
			*
		from
			division 
		where
			divisionID = '$divisionID'
		and
			division_void = '0'
	";
	
	$result=mysql_query($query) or die(mysql_error());
	$r=mysql_fetch_assoc($result);
	
	$companyID				= $r['companyID'];
	$division_name			= $r['division_name'];
	$division_abbrevation	= $r['division_abbrevation'];
?>

<form name="header_form" id="header_form" action="" method="post">
<div class="module_actions">	
    <div class='inline'>
        SEARCH DIVISION : <br />  
        <input type="text" class="textbox"  name="search_division" value="<?=$search_division?>"  onclick="this.select();"  autocomplete="off" placeholder="SEARCH" />
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
			division as d, companies as c
		where
			d.companyID = c.companyID
		and
			company_void = '0'
		and
			division_void = '0'
    ";
        
    if(!empty($search_division)){
    $sql.="		
		and
			division_name like '%$search_division%'
    ";
    }
	
	$sql.="
		order by division_name asc
	";
  
    $pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
            
    $i=$limitvalue;
    $rs = $pager->paginate();
	
	$pagination	= $pager->renderFullNav("$view&b=Search&search_division=$search_division");
    ?>
    <div class="pagination">
        <?=$pagination?>
    </div>
    <table id="my_table" cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table">
    <tr>				
    
        <th width="20">#</th>
        <th width="20"></th>
        <th>COMPANY</th>
        <th>DIVISION NAME</th>
        <th>DIVISION ABBREVIATION</th>
    </tr>  
    <?php								
    while($r=mysql_fetch_assoc($rs)) {
        
        echo '<tr>';
        echo '<td width="20">'.++$i.'</td>';
        echo '<td width="15"><a href="admin.php?view='.$view.'&divisionID='.$r['divisionID'].'" title="Show Details"><img src="images/edit.gif" border="0"></a></td>';
		echo '<td>'."$r[company_name]".'</td>';	
		echo '<td>'."$r[division_name]".'</td>';	
		echo '<td>'."$r[division_abbrevation]".'</td>';	
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
        <div class="module_title"><img src='images/user_orange.png'>DIVISION</div>
        <div class="module_actions">
            <input type="hidden" name="divisionID" value="<?=$divisionID?>" />
            <div id="messageError">
                <ul>
                </ul>
            </div>
            <table>
            	<tr>
                    <td>Company:</td>
                    <td><?=$options->getTableAssoc($companyID,'companyID','Select Company',"select * from companies where company_void = '0' order by company_name asc",'companyID','company_name');?></td>
                </tr>
                
                <tr>
                    <td>Division Name:</td>
                    <td><input type="text" class="textbox" name="division_name" value="<?=$division_name?>"  /></td>
                </tr>
                
                <tr>
                    <td>Division Abbrevation:</td>
                    <td><input type="text" class="textbox" name="division_abbrevation" value="<?=$division_abbrevation?>"  /></td>
                </tr>
            </table>
        </div>
        <div class="module_actions">
            <?php if(!empty($divisionID)){ ?>
            <input type="submit" name="b" id="b" value="Update" />
            <?php }else{ ?>
            <input type="submit" name="b" id="b" value="Submit" />
            <?php } ?>
            <a href="admin.php?view=<?=$view?>"><input type="button" value="New" /></a>
            <?php
			if(!empty($divisionID)){
            ?>
            <a href="admin.php?view=<?=$view?>&divisionID=<?=$divisionID?>&b=D" onclick="return approve_confirm();"><input type="button" value="Delete" /></a>
            <?php
			}
            ?>
        </div>
    </div>
    
<?php
}
?>
<?php
/*if($b == "Print Preview" && $holiday){

	echo "	<iframe id='JOframe' name='JOframe' frameborder='0' src='printIssuance.php?id=$holiday' width='100%' height='500'>
			</iframe>";
}
*/
			
?>
</form>
<script type="text/javascript">
j(function(){	
});
</script>
	