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

	$search_company			= $_REQUEST['search_company'];
	$companyID				= $_REQUEST['companyID'];
	
	
	$company_name			= $_REQUEST['company_name'];
	$company_abbrevation	= $_REQUEST['company_abbrevation'];
	
	if($b == 'D'){ 
		mysql_query("
			update 
				companies
			set
				company_void = '1'
			where
				companyID = '$companyID'
		") or die(mysql_error());
		$msg = "Transaction Voided.";
	}
	
	if($b=="Submit"){
		$query="
			insert into 
				companies
			set
				company_name = '',
				company_abbrevation = ''
		";	
		
		mysql_query($query) or die(mysql_error());
		$companyID = mysql_insert_id();
		$msg = "Transaction Added";
		
	}else if($b=="Update"){
		$query="
			update
				companies
			set
				company_name = '',
				company_abbrevation = ''
			where
				companyID = '$companyID'
		";	
		
		mysql_query($query) or die(mysql_error());
		
		$msg = "Transaction Updated";
	}
	
	$query="
		select
			*
		from
			companies 
		where
			companyID = '$companyID'
		and
			company_void = '0'
	";
	
	$result=mysql_query($query) or die(mysql_error());
	$r=mysql_fetch_assoc($result);
	
	$company_name			= $r['company_name'];
	$company_abbrevation	= $r['company_abbrevation'];
?>

<form name="header_form" id="header_form" action="" method="post">
<div class="module_actions">	
    <div class='inline'>
        SEARCH COMPANY : <br />  
        <input type="text" class="textbox"  name="search_company" value="<?=$search_company?>"  onclick="this.select();"  autocomplete="off" placeholder="SEARCH" />
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
			companies
		where
			company_void = '0'
    ";
        
    if(!empty($search_company)){
    $sql.="		
		and
			company_name like '%$search_company%'
    ";
    }
	
	$sql.="
		order by company_name asc
	";
  
    $pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
            
    $i=$limitvalue;
    $rs = $pager->paginate();
	
	$pagination	= $pager->renderFullNav("$view&b=Search&search_company=$search_company");
    ?>
    <div class="pagination">
        <?=$pagination?>
    </div>
    <table id="my_table" cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table">
    <tr>				
    
        <th width="20">#</th>
        <th width="20"></th>
        <th>COMPANY NAME</th>
        <th>COMPANY ABBREVIATION</th>
    </tr>  
    <?php								
    while($r=mysql_fetch_assoc($rs)) {
        
        echo '<tr>';
        echo '<td width="20">'.++$i.'</td>';
        echo '<td width="15"><a href="admin.php?view='.$view.'&companyID='.$r['companyID'].'" title="Show Details"><img src="images/edit.gif" border="0"></a></td>';
		echo '<td>'."$r[company_name]".'</td>';	
		echo '<td>'."$r[company_abbrevation]".'</td>';	
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
        <div class="module_title"><img src='images/user_orange.png'>COMPANIES</div>
        <div class="module_actions">
            <input type="hidden" name="companyID" value="<?=$companyID?>" />
            <div id="messageError">
                <ul>
                </ul>
            </div>
            <table>
                <tr>
                    <td>Company Name:</td>
                    <td><input type="text" class="textbox" name="company_name" value="<?=$company_name?>"  /></td>
                </tr>
                
                <tr>
                    <td>Company Abbrevation:</td>
                    <td><input type="text" class="textbox" name="company_abbrevation" value="<?=$company_abbrevation?>"  /></td>
                </tr>
            </table>
        </div>
        <div class="module_actions">
            <?php if(!empty($companyID)){ ?>
            <input type="submit" name="b" id="b" value="Update" />
            <?php }else{ ?>
            <input type="submit" name="b" id="b" value="Submit" />
            <?php } ?>
            <a href="admin.php?view=<?=$view?>"><input type="button" value="New" /></a>
            <?php
			if(!empty($companyID)){
            ?>
            <a href="admin.php?view=<?=$view?>&companyID=<?=$companyID?>&b=D" onclick="return approve_confirm();"><input type="button" value="Delete" /></a>
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
	