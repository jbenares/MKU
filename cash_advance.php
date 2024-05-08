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

	
	$search_account_name	= $_REQUEST['search_account_name'];
	$search_account_id		= (!empty($search_account_name))?$_REQUEST['search_account_id']:"";
	
	$search_stock_name		= $_REQUEST['search_stock_name'];
	$search_stock_id		= (!empty($search_stock_name))?$_REQUEST['search_stock_id']:"";
	
	$cash_advance_id	= $_REQUEST['cash_advance_id'];
	$date				= $_REQUEST['date'];
	$account_id			= $_REQUEST['account_id'];
	$stock_id			= $_REQUEST['stock_id'];	
	
	$project_id			= $_REQUEST['project_id'];
	$scope_of_work		= $_REQUEST['scope_of_work'];
	$work_category_id	= $_REQUEST['work_category_id'];
	$sub_work_category_id = $_REQUEST['sub_work_category_id'];
	
	$id	= $_REQUEST['id'];
	
	if($b=="Submit"){
		$query="
			insert into 
				cash_advance
			set
				date		= '$date',
				account_id	= '$account_id',
				stock_id	= '$stock_id',
				status		= 'S',
				user_id		= '$user_id',
				project_id	= '$project_id',
				work_category_id 		= '$work_category_id',
				sub_work_category_id 	= '$sub_work_category_id',
				scope_of_work			= '$scope_of_work'
		";	
		
		mysql_query($query) or die(mysql_error());
		
		$cash_advance_id = mysql_insert_id();
		
		$msg="Transaction Saved";
		
	}else if($b=="Update"){
		$query="
			update
				cash_advance
			set
				date		= '$date',
				account_id	= '$account_id',
				stock_id	= '$stock_id',
				status		= 'S',
				user_id		= '$user_id',
				project_id	= '$project_id',
				work_category_id 		= '$work_category_id',
				sub_work_category_id 	= '$sub_work_category_id',
				scope_of_work			= '$scope_of_work'
			where
				cash_advance_id = '$cash_advance_id'
		";	
		
		mysql_query($query) or die(mysql_error());
		
		$msg = "Transaction Updated";
	}else if($b=="Cancel"){
		$query="
			update
				cash_advance
			set
				status='C'
			where
				cash_advance_id = '$cash_advance_id'
		";	
		mysql_query($query);
		
		$msg = "Transaction Cancelled";
		
	}else if($b=="Finish"){
		$query="
			update
				cash_advance
			set
				status='F'
			where
				cash_advance_id = '$cash_advance_id'
		";	
		mysql_query($query);
		
		$msg = "Transaction Finished";
		
	}else if($b=="d"){
		mysql_query("
			delete from
				ap_check
			where
				ap_check_id = '$id'
		") or die(mysql_error());
	}

	$query="
		select
			*
		from
			cash_advance
		where
			cash_advance_id ='$cash_advance_id'
	";
	
	$result=mysql_query($query) or die(mysql_error());
	$r=mysql_fetch_assoc($result);
	
	
	$date			= ($r['date']!="0000-00-00")?$r['date']:"";
	$account_id		= $r['account_id'];
	$account_name	= ($account_id)?$options->attr_Account($account_id,'account'):"";
	
	$stock_id		= $r['stock_id'];
	$stock_name		= $options->attr_stock($stock_id,'stock');

	$user_id		= $r['user_id'];
	$status			= $r['status'];
	
	$project_id			= $r['project_id'];
	$project_name		= $options->attr_Project($project_id,'project_name');
	$project_code		= $options->attr_Project($project_id,'project_code');
	$project_name_code	= ($project_id)?"$project_name - $project_code":"";
	
	$scope_of_work		= $r['scope_of_work'];
	$work_category_id 	= $r['work_category_id'];
	$sub_work_category_id = $r['sub_work_category_id'];
?>

<?php
if( $status=="F" || $status == "C" ){
?>
<style type="text/css">
.cp_table tr td:nth-child(1),.cp_table  tr th:nth-child(1){
	display:none;	
}
</style>
<?php
}
?>
<form name="header_form" id="header_form" action="" method="post">
<div class="module_actions">	
	<img src="images/find.png" />
    <div class='inline'>
        Account : <br />  
        <input type="text" class="textbox accounts"  name="search_account_name" value="<?=$search_account_name?>"  onclick="this.select();"/>
        <input type="hidden" name="search_account_id"  value="<?=$search_account_id?>" />
    </div>   
    
    <div class='inline'>
        Item : <br />  
        <input type="text" class="textbox stock_name"  name="search_stock_name" value="<?=$search_stock_name?>"  onclick="this.select();"/>
        <input type="hidden" name="search_stock_id"  value="<?=$search_stock_id?>" />
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

$sql = "select
			  *
		 from
			  cash_advance
	";
	

if(!empty($search_account_id)){
$sql.="
	where
		account_id = '$search_account_id'	
	";
}

if(!empty($search_stock_id)){
	if(!empty($search_account_id)){
		$sql.="
			and
				stock_id = '$search_stock_id'	
			";
	}else{
		$sql.="
			where
				stock_id = '$search_stock_id'	
			";
	}
}
$sql.="
		order 
			by date desc
	";
$pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
		
$i=$limitvalue;
$rs = $pager->paginate();
?>
<div class="pagination">
	<?=$pager->renderFullNav("$view&b=Search&search_supplier_id=$search_supplier_id&search_supplier=$search_supplier")?>
</div>
<table id="my_table" cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table">
<tr>				

    <th width="20">#</th>
    <th width="20"></th>
    <th>CA#</th>
    <th>Date</th>
    <th>Account</th>
    <th>Item</th>
    <th>Status</th>
</tr>  
<?php								
while($r=mysql_fetch_assoc($rs)) {
	
	echo '<tr>';
	echo '<td width="20">'.++$i.'</td>';
	echo '<td width="15"><a href="admin.php?view='.$view.'&cash_advance_id='.$r[cash_advance_id].'" title="Show Details"><img src="images/edit.gif" border="0"></a></td>';
	echo '<td>'.str_pad($r['cash_advance_id'],7,0,STR_PAD_LEFT).'</td>';
	echo '<td>'.date("F j, Y",strtotime($r['date'])).'</td>';	
	echo '<td>'.$options->attr_Account($r['account_id'],'account').'</td>';	
	echo '<td>'.$options->attr_stock($r['stock_id'],'stock').'</td>';	
	echo '<td>'.$options->getTransactionStatusName($r['status']).'</td>';	
	echo '</tr>';
}
?>
</table>
<div class="pagination">
<?=$pager->renderFullNav("$view&b=Search&search_supplier_id=$search_supplier_id&search_supplier=$search_supplier")?>
</div>
<?php
}else{
?>
<div class=form_layout>
	<?php if(!empty($msg)) echo '<div id="status_update" class="ui-state-highlight ui-corner-all" style="padding: 0px 0.7em; text-align:left;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right:.3em;"></span> '.$msg.'</p></div>'; ?>
	<div class="module_title"><img src='images/user_orange.png'>CASH ADVANCE</div>
    
    <div class="module_actions">
        <input type="hidden" name="cash_advance_id" id="cash_advance_id" value="<?=$cash_advance_id?>" />
        <div id="messageError">
            <ul>
            </ul>
        </div>
        <div class='inline'>
            <div>Date: </div>        
            <div>
                <input type="text" class="datepicker textbox3" title="Please enter date"  name="date" readonly='readonly'  value="<?=$date?>">
            </div>
        </div>    
        
        <div class='inline'>
            Account : <br />  
            <input type="text" class="textbox accounts" name="account_name" value="<?=$account_name?>"  onclick="this.select();"/>
            <input type="hidden" name="account_id"  value="<?=$account_id?>" title="Please Select Supplier" />
        </div>   
        
        <div class="inline">
        	Item : <br />
            <input type="text" class="textbox" name="stock_name" id="stock_name" value="<?=$stock_name?>" />
            <input type="hidden" name="stock_id" id="stock_id" value="<?=$stock_id?>"   />
        </div>
        
        <div class='inline'>
            Project : <br />  
            <input type="text" class="textbox" id="project_name" value="<?=$project_name_code?>" onclick="this.select();"  />
            <input type="hidden" name="project_id"  id="project_id" value="<?=$project_id?>" />
        </div>   
        
        <!--<div class="inline">
            Scope of Work :
            <div id="div_scope_of_work">
                <select class="select">
                    <option value="">Select Project First...</option>
                </select>
            </div>
        </div>-->
        
        <div class="inline">
            Work Category : <br />
            <?=$options->option_workcategory($work_category_id,'work_category_id','Select Work Category')?>
        </div>
        
        <div id="subworkcategory_div" style="display:none;" class="inline">
            Sub Work Category :
            <div id="subworkcategory">
                
            </div>
        </div>
        
        <?php
        if(!empty($status)){
        ?>
        
        <div class='inline'>
            <div>Status : </div>        
            <div>
                <input type="text" class="textbox3" name="status" id="status" value="<?=$options->getTransactionStatusName($status)?>" readonly="readonly"/>
            </div>
        </div> 
        
        <div class='inline'>
            <div>Encoded by : </div>        
            <div>
                <input type='text' class="textbox" value="<?=$options->getUserName($user_id);?>" readonly="readonly" />
            </div>
        </div> 
        <?php
        }
        ?>
    </div>
    <div class="module_actions">
		<?php
        if($status=="S"){
        ?>
        <input type="submit" name="b" id="b" value="Update" />
        <input type="submit" name="b" id="b" value="Finish" />
        
        <?php
        }else if($status!="F" && $status!="C"){
        ?>
        <input type="submit" name="b" id="b" value="Submit" />
        <?php
        }
        
        if($b!="Print Preview" && !empty($status)){
        ?>
           <!-- <input type="submit" name="b" id="b" value="Print Preview" />-->
        <?php
        }
    
        if($b=="Print Preview"){
        ?>	
           <!-- <input type="button" value="Print" onclick="printIframe('JOframe');" />-->
    
        <?php
        }
        if($status!="C" && !empty($status)){
        ?>
        <input type="submit" name="b" id="b" value="Cancel" />
        <?php
        }
		?>
        <a href="admin.php?view=<?=$view?>"><input type="button" value="New" /></a>
   	</div>
</div>
<?php
}
?>
</form>
<script type="text/javascript">
j(function(){	
	j("#work_category_id").change(function(){
		xajax_display_subworkcategory(this.value);
	});
	
	<?php
	if(!empty($status)){
	?>
		xajax_display_subworkcategory('<?=$work_category_id?>','<?=$sub_work_category_id?>');
		xajax_update_scope_of_work('<?=$project_id?>','<?=$scope_of_work?>');
	<?php
	}
	?>
});
</script>
	