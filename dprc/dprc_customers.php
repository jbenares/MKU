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

	$search_customer		= $_REQUEST['search_customer'];
	$customer_id			= $_REQUEST['customer_id'];
	$customer_last_name		= $_REQUEST['customer_last_name'];
	$customer_first_name	= $_REQUEST['customer_first_name'];
	$customer_middle_name	= $_REQUEST['customer_middle_name'];
	$customer_appel			= $_REQUEST['customer_appel'];
	$customer_gender		= $_REQUEST['customer_gender'];
	$customer_civil_status	= $_REQUEST['customer_civil_status'];
	$customer_tel			= $_REQUEST['customer_tel'];
	$customer_email			= $_REQUEST['customer_email'];
	$customer_address1		= $_REQUEST['customer_address1'];
	$customer_address2		= $_REQUEST['customer_address2'];
	$remarks				= $_REQUEST['remarks'];
	
	
	if($b == 'D'){ 
		mysql_query("
			delete from
				customer
			where
				customer_id = '$customer_id'
		") or die(mysql_error());
	}
	
	if($b=="Submit"){
		$query="
			insert into 
				customer
			set
				customer_last_name = '$customer_last_name',
				customer_first_name = '$customer_first_name',
				customer_middle_name = '$customer_middle_name',
				customer_appel = '$customer_appel',
				customer_gender = '$customer_gender',
				customer_civil_status = '$customer_civil_status',
				customer_tel = '$customer_tel',
				customer_email = '$customer_email',
				customer_address1 = '$customer_address1',
				customer_address2 = '$customer_address2',
				remarks	= '$remarks'
		";	
		
		mysql_query($query) or die(mysql_error());
		$customer_id = mysql_insert_id();
		$msg = "Transaction Added";
		
	}else if($b=="Update"){
		$query="
			update
				customer
			set
				customer_last_name = '$customer_last_name',
				customer_first_name = '$customer_first_name',
				customer_middle_name = '$customer_middle_name',
				customer_appel = '$customer_appel',
				customer_gender = '$customer_gender',
				customer_civil_status = '$customer_civil_status',
				customer_tel = '$customer_tel',
				customer_email = '$customer_email',
				customer_address1 = '$customer_address1',
				customer_address2 = '$customer_address2',
				remarks	= '$remarks'
			where
				customer_id = '$customer_id'
		";	
		
		mysql_query($query) or die(mysql_error());
		
		$msg = "Transaction Updated";
	}
	
	$query="
		select
			*
		from
			customer 
		where
			customer_id = '$customer_id'
	";
	
	$result=mysql_query($query) or die(mysql_error());
	$r=mysql_fetch_assoc($result);
	
	$customer_last_name		= $r['customer_last_name'];
	$customer_first_name	= $r['customer_first_name'];
	$customer_middle_name	= $r['customer_middle_name'];
	$customer_appel			= $r['customer_appel'];
	$customer_gender		= $r['customer_gender'];
	$customer_civil_status	= $r['customer_civil_status'];
	$customer_tel			= $r['customer_tel'];
	$customer_email			= $r['customer_email'];
	$customer_address1		= $r['customer_address1'];
	$customer_address2		= $r['customer_address2'];
	$remarks				= $r['remarks'];
?>

<form name="header_form" id="header_form" action="" method="post">
<div class="module_actions">	
    <div class='inline'>
        Customer : <br />  
        <input type="text" class="textbox"  name="search_customer" value="<?=$search_customer?>"  onclick="this.select();"  autocomplete="off" />
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
			customer
    ";
        
    if(!empty($search_customer)){
    $sql.="
		where
			customer_last_name like '$search_customer%'
    ";
    }
	
	$sql.="
		order by customer_last_name asc
	";
  
    $pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
            
    $i=$limitvalue;
    $rs = $pager->paginate();
	
	$pagination	= $pager->renderFullNav("$view&b=Search&search_customer=$search_customer");
    ?>
    <div class="pagination">
        <?=$pagination?>
    </div>
    <table id="my_table" cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table">
    <tr>				
    
        <th width="20">#</th>
        <th width="20"></th>
        <th>LAST NAME</th>
        <th>FIRST NAME</th>
    </tr>  
    <?php								
    while($r=mysql_fetch_assoc($rs)) {
        
        echo '<tr>';
        echo '<td width="20">'.++$i.'</td>';
        echo '<td width="15"><a href="admin.php?view='.$view.'&customer_id='.$r['customer_id'].'" title="Show Details"><img src="images/edit.gif" border="0"></a></td>';
		echo '<td>'."$r[customer_last_name]".'</td>';	
		echo '<td>'."$r[customer_first_name]".'</td>';	
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
        <div class="module_title"><img src='images/user_orange.png'>CUSTOMERS</div>
        <div class="module_actions">
            <input type="hidden" name="customer_id" value="<?=$customer_id?>" />
            <div id="messageError">
                <ul>
                </ul>
            </div>
            
            <table>
            	<tr>
                	<?php if(!empty($customer_id)){ ?>
                	<td>
                    	Customer ID:<br />
                        <input type="text" class="textbox" value="<?=str_pad($customer_id,7,0,STR_PAD_LEFT)?>" />
                    </td>
                    <?php } ?>
                	<td>
                    	Last Name:<br />
                        <input type="text" class="textbox" name="customer_last_name" value="<?=$customer_last_name?>"  />
                    </td>
                    <td>
                    	First Name:<br />
                        <input type="text" class="textbox" name="customer_first_name" value="<?=$customer_first_name?>" />
                    </td>
                    <td>
                    	Middle Name:<br />
                        <input type="text" class="textbox" name="customer_middle_name"  value="<?=$customer_middle_name?>" />
                    </td>
                    <td>
                    	Appellation:<br />
                        <input type="text" class="textbox" name="customer_appel"  value="<?=$customer_appel?>" />
                    </td>
                </tr>
                <tr>
                	<td>
                    	Gender:<br />
                        <select name="customer_gender">
                        	<option>Select Gender:</option>
							<option <?=($customer_gender == "M") ? "selected = 'selected'" : "" ?>>MALE</option>
                            <option <?=($customer_gender == "F") ? "selected = 'selected'" : "" ?>>FEMALE</option>
                        </select>
                    </td>
                    <td>
                    	Civil Status:<br />
                        <select name="customer_civil_status">
                        	<option>Select Civil Status:</option>
							<option <?=($customer_civil_status == "S") ? "selected = 'selected'" : "" ?>>SINGLE</option>
                            <option <?=($customer_civil_status == "M") ? "selected = 'selected'" : "" ?>>MARRIED</option>
                        </select>
                    </td>
                    <td>
                    	Telephone:<br />
                        <input type="text" class="textbox" name="customer_tel" value="<?=$customer_tel?>" />
                    </td>
                </tr>
            </table>
            <table>
            	<tr>
                	<td>Email Add:</td>
                    <td><input type="text" style="width:500px; border: 1px solid #C0C0C0; padding: 3px;" name="customer_email" value="<?=$customer_email?>" /></td>
                </tr>
                <tr>
                	<td>Address 1:</td>
                    <td><input type="text" style="width:500px; border: 1px solid #C0C0C0; padding: 3px;" name="customer_address1" value="<?=$customer_address1?>" /></td>
                </tr>
                <tr>
                	<td>Address 2:</td>
                    <td><input type="text" style="width:500px; border: 1px solid #C0C0C0; padding: 3px;" name="customer_address2" value="<?=$customer_address2?>" /></td>
                </tr>
                <tr>
                	<td style="vertical-align:top;">
                    	Remarks:
                    </td>
                    <td>
                        <textarea class="textarea" style="width:100%; border:1px solid #c0c0c0; padding:3px;" name="remarks"><?=$remarks?></textarea>
                    </td>
                </tr>
            </table>
        </div>
        <div class="module_actions">
            <?php
            if(!empty($customer_id)){
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
			if(!empty($customer_id)){
            ?>
            <a href="admin.php?view=<?=$view?>&customer_id=<?=$customer_id?>&b=D" onclick="return approve_confirm();"><input type="button" value="Delete" /></a>
            <?php
			}
            ?>
        </div>
    </div>
    
<?php
}
?>
<?php
/*if($b == "Print Preview" && $customer){

	echo "	<iframe id='JOframe' name='JOframe' frameborder='0' src='printIssuance.php?id=$customer' width='100%' height='500'>
			</iframe>";
}
*/
			
?>
</form>
<script type="text/javascript">
j(function(){	
});
</script>
	