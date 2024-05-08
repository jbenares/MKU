<?php
function getItemName($rr_detail_id){
	$result = mysql_query("
				select 
					p.stock,d.details
				from
					rr_detail as d, productmaster as p
				where
					d.stock_id = p.stock_id
				and
					d.rr_detail_id = '$rr_detail_id'
			") or die(mysql_error()); 
	$r = mysql_fetch_assoc($result);
	if(mysql_num_rows($result)){
		return $r['stock']. "($r[details])";
	}else{
		return "";	
	}
}
?>
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
	$b                  = $_REQUEST['b'];
	$user_id            = $_SESSION['userID'];
	
	$search_project = $_REQUEST['search_project'];
	$search_employee    = $_REQUEST['search_employee'];
	
	$accountable_id     = $_REQUEST['accountable_id'];
	$rr_detail_id       = $_REQUEST['rr_detail_id'];
	$date               = $_REQUEST['date'];
	$time               = $_REQUEST['time'];
	$project_id         = $_REQUEST['project_id'];
	$account_id         = $_REQUEST['account_id'];
	$qty                = $_REQUEST['qty'];
	$received           = $_REQUEST['received'];
	$item_status        = $_REQUEST['item_status'];
	$remarks            = $_REQUEST['remarks'];
	
	if($b == 'D'){ 
		mysql_query("
			delete from
				accountable
			where
				accountable_id = '$accountable_id'
		") or die(mysql_error());
	
	}
	
	if($b == "IN" && !empty($accountable_id)){
		$result = mysql_query("select * from accountables where accountable_id = '$accountable_id'") or die(mysql_error());
		$r = mysql_fetch_assoc($result);
		
		$query="
			insert into 
				accountables
			set
				rr_detail_id = '$r[rr_detail_id]',
				date         = '".date("Y-m-d")."',
				time         = '".date("H:i:s")."',
				project_id   = '$r[project_id]',
				account_id   = '$r[account_id]',
				qty          = '$r[qty]',
				received     = '1'			
		";	
		
		mysql_query($query) or die(mysql_error());
		$accountable_id = mysql_insert_id();
	}
	
	if($b=="Submit"){
		$query="
			insert into 
				accountables
			set
				rr_detail_id = '$rr_detail_id',
				date         = '$date',
				time         = '$time',
				project_id   = '$project_id',
				account_id   = '$account_id',
				qty          = '$qty',
				received     = '0'
		";	
		
		mysql_query($query) or die(mysql_error());
		$accountable_id = mysql_insert_id();
		$msg = "Transaction Added";
		
	}else if($b=="Update"){
		$query="
			update
				accountables
			set
				rr_detail_id = '$rr_detail_id',
				date         = '$date',
				time         = '$time',
				project_id   = '$project_id',
				account_id   = '$account_id',
				qty          = '$qty',
				item_status  = '$item_status',
				remarks       = '$remarks'
			where
				accountable_id = '$accountable_id'
		";	
		
		mysql_query($query) or die(mysql_error());
		
		$msg = "Transaction Updated";
	}
	
	$query="
		select
			*
		from
			accountables
		where
			accountable_id = '$accountable_id'
	";
	
	$result=mysql_query($query) or die(mysql_error());
	$r=mysql_fetch_assoc($result);
	
	$rr_detail_id = $r['rr_detail_id'];
	$date         = $r['date'];
	$time         = $r['time'];
	$project_id   = $r['project_id'];
	$account_id   = $r['account_id'];
	$qty          = $r['qty'];
	$received     = $r['received'];
	$item_status  = $r['item_status'];
	$remarks      = $r['remarks'];
?>

<form name="header_form" id="header_form" action="" method="post">
<div class="module_actions">	
    <div class='inline'>
        PROJECT : <br />  
        <input type="text" class="textbox project"  name="search_project" value="<?=$_REQUEST['search_project']?>"  onclick="this.select();"  autocomplete="off" />
        <input type="hidden" name="search_project_id" value="<?php if($_REQUEST['search_project']) echo $_REQUEST['search_project_id'] ?>">
    </div>   
    <div class='inline'>
        EMPLOYEE : <br />  
        <input type="text" class="textbox"  name="search_employee" value="<?=$search_employee?>"  onclick="this.select();"  autocomplete="off" />
    </div>   

    <div class='inline'>
    	ITEM: <br>
    	<input type="text" class="textbox accountability-search" name='search_item_name' value="<?=($_REQUEST['search_item_name'] ? getItemName($_REQUEST['search_item_id']) : '' )?>" onclick='this.select();' />
	    <input type="hidden" name="search_item_id" id="search_item_id" value="<?=($_REQUEST['search_item_name'] ? $_REQUEST['search_item_id'] : '')?>" />
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
			p.stock , p.stock_id, d.rr_detail_id, account, qty, proj.project_name, accountable_id, a.received
		from
			accountables as a, rr_detail as d, productmaster as p, account as ac, projects as proj
		where
			a.rr_detail_id  = d.rr_detail_id
		and
			d.stock_id = p.stock_id
		and
			a.account_id = ac.account_id
		and
			proj.project_id = a.project_id
		
    ";
        
    if(!empty($_REQUEST['search_project'])){ $sql.=" and a.project_id = '$_REQUEST[search_project_id]' ";  }
    if(!empty($_REQUEST['search_item_name'])){ $sql.=" and d.rr_detail_id = '$_REQUEST[search_item_id]' ";  }
	
	if(!empty($search_employee)){
    $sql.="
		and
			account like '$search_employee%'
    ";
    }
	
	$sql.="
		order by accountable_id desc
	";
  
    $pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
            
    $i=$limitvalue;
    $rs = $pager->paginate();
	
	$pagination	= $pager->renderFullNav("$view&b=Search&search_project=$search_project");
    ?>
    <div class="pagination">
        <?=$pagination?>
    </div>
    <table id="my_table" cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table">
    <tr>				
        <th width="20">#</th>
        <th width="20"></th>
        <th>EMPLOYEE</th>
        <th>ITEM</th>
        <th>QTY</th>
        <th>PROJECT</th>
        <th>TRANS</th>
    </tr>  
    <?php								
    while($r=mysql_fetch_assoc($rs)) {
        $trans  = ($r['received']) ? "IN" : "OUT";
        echo '<tr>';
        echo '<td width="20">'.++$i.'</td>';
        echo '<td width="15"><a href="admin.php?view='.$view.'&accountable_id='.$r['accountable_id'].'" title="Show Details"><img src="images/edit.gif" border="0"></a></td>';
		echo '<td>'."$r[account]".'</td>';	
		echo '<td>'."$r[stock]".'</td>';	
		echo '<td>'."$r[qty]".'</td>';	
		echo '<td>'."$r[project_name]".'</td>';	
		echo '<td>'.$trans.'</td>';	
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
        <div class="module_title"><img src='images/user_orange.png'>ACCOUNTABILITY RECEIPT</div>
        <div class="module_actions">
            <input type="hidden" name="accountable_id" value="<?=$accountable_id?>" />
            <div id="messageError">
                <ul>
                </ul>
            </div>
            <table>
            	<tr>
                	<td>TRANSACTION</td>
                    <td>
                    	<?php $received = (empty($received)) ? 0 : $received;?>
                    	<select name="received" disabled="disabled">
                        	<option value="">Select IN/OUT:</option>
                        	<option value="1" <?=($received) ? "selected='selected'" : "" ?>>IN</option>
                            <option value="0" <?=($received == "0") ? "selected='selected'" : "" ?>>OUT</option>
                        </select>
                    </td>
                </tr>
            	<tr>
                	<td>DATE:</td>
                    <td>
                    	<input type="text" class="textbox datepicker" name="date"  value="<?=$date?>"  readonly="readonly"/>
                    </td>
                </tr>
                <tr>
                	<td>TIME:</td>
                    <td>
                    	<?php $time = (!empty($time)) ? $time : date("H:i:s") ?>
                    	<input type="text" class="textbox" name="time"  value="<?=$time?>" />
                    </td>
                </tr>
            	<tr>
                	<td>EMPLOYEE:</td>
                    <td>
                    	<input type="text" class="textbox accounts"  value="<?=$options->getAttribute('account','account_id',$account_id,'account')?>" />
                        <input type="hidden" name="account_id" value="<?=$account_id?>" />
                    </td>
                </tr>
                <tr>
                	<td>ITEM:</td>
                    <td>
                    	<input type="text" class="textbox accountability" value="<?=getItemName($rr_detail_id)?>" />
                        <input type="hidden" name="rr_detail_id" id="rr_detail_id" value="<?=$rr_detail_id?>" />
                    </td>
                </tr>
                <tr>
                	<td>QUANTITY:</td>
                    <td><input type="text" class="textbox" name="qty" value="<?=$qty?>"  autocomplete="off" /></td>
                </tr>
                <tr>
                	<td>PROJECT:</td>
                    <td>
                    	<input type="text" class="textbox project" id="project_name" value="<?=$options->getAttribute('projects','project_id',$project_id,'project_name')?>" />
                        <input type="hidden" name="project_id" value="<?=$project_id?>" id="project_id" />
                    </td>
                </tr>	
                <?php if($received){ ?>
                <tr>
                	<td>ITEM STATUS:</td>
                    <td>
                    	<select name="item_status">
                        	<option>Select Item Status:</option>
                        	<option value="FUNCTIONING" <?php if($item_status == "FUNCTIONING") echo "selected='selected'"; ?> >FUNCTIONING</option>
                            <option value="DAMAGED" <?php if($item_status == "DAMAGED") echo "selected='selected'"; ?> >DAMAGED</option>
                        </select>
                    </td>
                </tr>
                <tr>
                	<td style="vertical-align:top;">REMARKS:</td>
                	<td>	
                		<textarea name="remarks" style="border:1px solid #c0c0c0; width:500px; height:100px; font-size:11px; font-family:arial;"><?=$remarks?></textarea>
                	</td>
                </tr>
                <?php } ?>
            </table>
        </div>
        <div class="module_actions">
            <?php if(!empty($accountable_id)){ ?>
            <input type="submit" name="b" id="b" value="Update" />
            <?php if(!$received){ ?>
            <input type="submit" name="b" id="b" value="IN" />
            <?php } ?>
			<?php if($b == "Print Preview"){ ?>
            <input type="button" value="Print" onclick="printIframe('JOframe');" />
            <?php } else { ?>
            <input type="submit" name="b" value="Print Preview" />    
            <?php } ?>
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
if($b == "Print Preview"){

	echo "	<iframe id='JOframe' name='JOframe' frameborder='0' src='print_accountability.php?id=$accountable_id' width='100%' height='500'>
			</iframe>";
}

			
?>
</form>
<script type="text/javascript">
j(function(){	
	j(".accountability").autocomplete({
		source: "list_accountability.php",
		minLength: 1,
		select: function(event, ui) {
			j(this).val(ui.item.value);
			j("#rr_detail_id").val(ui.item.rr_detail_id);
			j("#project_id").val(ui.item.project_id);
			j("#project_name").val(ui.item.project_name);
		}
	});
	j(".accountability-search").autocomplete({
		source: "list_accountability.php",
		minLength: 1,
		select: function(event, ui) {
			j(this).val(ui.item.value);
			j("#search_item_id").val(ui.item.rr_detail_id);			
		}
	});
});
</script>
	