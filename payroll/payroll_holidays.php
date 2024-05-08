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

	$search_holiday			= $_REQUEST['search_holiday'];
	$holiday_id				= $_REQUEST['holiday_id'];
	
	$date					= $_REQUEST['date'];
	$description			= $_REQUEST['description'];
	$rate					= $_REQUEST['rate'];
	
	if($b == 'D'){ 
		mysql_query("
			update 
				holiday
			set
				holiday_void = '1'
			where
				holiday_id = '$holiday_id'
		") or die(mysql_error());
		$msg = "Transaction Voided.";
	}
	
	if($b=="Submit"){
		$query="
			insert into 
				holiday
			set
				date = '$date',
				description = '$description',
				rate = '$rate'			
		";	
		
		mysql_query($query) or die(mysql_error());
		$holiday_id = mysql_insert_id();
		$msg = "Transaction Added";
		
	}else if($b=="Update"){
		$query="
			update
				holiday
			set
				date = '$date',
				description = '$description',
				rate = '$rate'	
			where
				holiday_id = '$holiday_id'
		";	
		
		mysql_query($query) or die(mysql_error());
		
		$msg = "Transaction Updated";
	}
	
	$query="
		select
			*
		from
			holiday 
		where
			holiday_id = '$holiday_id'
	";
	
	$result=mysql_query($query) or die(mysql_error());
	$r=mysql_fetch_assoc($result);
	
	$date						= $r['date'];
	$description				= $r['description'];
	$rate						= $r['rate'];
?>

<form name="header_form" id="header_form" action="" method="post">
<div class="module_actions">	
    <div class='inline'>
        DESCRIPTION : <br />  
        <input type="text" class="textbox"  name="search_holiday" value="<?=$search_holiday?>"  onclick="this.select();"  autocomplete="off" placeholder="SEARCH" />
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
			holiday
		where
			holiday_void = '0'
    ";
        
    if(!empty($search_holiday)){
    $sql.="		
		and
			description like '$search_holiday%'
    ";
    }
	
	$sql.="
		order by date asc
	";
  
    $pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
            
    $i=$limitvalue;
    $rs = $pager->paginate();
	
	$pagination	= $pager->renderFullNav("$view&b=Search&search_holiday=$search_holiday");
    ?>
    <div class="pagination">
        <?=$pagination?>
    </div>
    <table id="my_table" cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table">
    <tr>				
    
        <th width="20">#</th>
        <th width="20"></th>
        <th>DATE</th>
        <th>DESCRIPTION</th>
        <th style="text-align:right;">RATE</th>
    </tr>  
    <?php								
    while($r=mysql_fetch_assoc($rs)) {
        
        echo '<tr>';
        echo '<td width="20">'.++$i.'</td>';
        echo '<td width="15"><a href="admin.php?view='.$view.'&holiday_id='.$r['holiday_id'].'" title="Show Details"><img src="images/edit.gif" border="0"></a></td>';
		echo '<td>'."$r[date]".'</td>';	
		echo '<td>'."$r[description]".'</td>';	
		echo '<td style="text-align:right;">'.number_format($r['rate'],4).'</td>';	
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
        <div class="module_title"><img src='images/user_orange.png'>HOLIDAY</div>
        <div class="module_actions">
            <input type="hidden" name="holiday_id" value="<?=$holiday_id?>" />
            <div id="messageError">
                <ul>
                </ul>
            </div>
            <table>
            	<tr>
                	<td>Date:</td>
                    <td><input type="text" class="textbox datepicker" name="date" value="<?=$date?>"  /></td>
                </tr>
                <tr>
                    <td>Description:</td>
                    <td><textarea style="border:1px solid #c0c0c0; width:100%;" name="description" ><?=$description?></textarea></td>
                </tr>
                <tr>
                	<td>Rate</td>
                    <td><input type="text" class="textbox" name="rate" value="<?=$rate?>"  />%</td>
                </tr>
            </table>
        </div>
        <div class="module_actions">
            <?php if(!empty($holiday_id)){ ?>
            <input type="submit" name="b" id="b" value="Update" />
            <?php }else{ ?>
            <input type="submit" name="b" id="b" value="Submit" />
            <?php } ?>
            <a href="admin.php?view=<?=$view?>"><input type="button" value="New" /></a>
            <?php
			if(!empty($holiday_id)){
            ?>
            <a href="admin.php?view=<?=$view?>&holiday_id=<?=$holiday_id?>&b=D" onclick="return approve_confirm();"><input type="button" value="Delete" /></a>
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
	