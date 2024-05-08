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

.table-form tr td:nth-child(1){
	text-align:right;
	font-weight:bold;
}


</style>
<?php
	$b					= $_REQUEST['b'];
	$user_id			= $_SESSION['userID'];
	
	#SEARCH
	$search_deposit_id	= $_REQUEST['search_deposit_id'];

	#HEADER
	$deposit_id			= $_REQUEST['deposit_id'];
	$from_date			= $_REQUEST['from_date'];
	$to_date			= $_REQUEST['to_date'];
	$deposit_date		= $_REQUEST['deposit_date'];
	$undeposited		= $_REQUEST['undeposited'];
	$deposited			= $_REQUEST['deposited'];
	$remarks			= $_REQUEST['remarks'];
	
	
	if($b=="Submit"){
		$query="
			insert into 
				dprc_deposits
			set
				from_date 		= '$from_date',
				to_date 		= '$to_date',
				undeposited 	= '$undeposited',
				deposited 		= '$deposited',
				remarks 		= '$remarks',
				user_id			= '$user_id',
				deposit_date	= '$deposit_date'
		";	
		
		mysql_query($query) or die(mysql_error());
		$deposit_id = mysql_insert_id();
		$msg = "Transaction Added";
		
	}else if($b=="Update"){
		$query="
			update
				dprc_deposits
			set
				from_date 		= '$from_date',
				to_date 		= '$to_date',
				undeposited 	= '$undeposited',
				deposited 		= '$deposited',
				remarks 		= '$remarks',
				user_id			= '$user_id',
				deposit_date	= '$deposit_date'
			where
				deposit_id = '$deposit_id'
		";	
		
		mysql_query($query) or die(mysql_error());
		
		$msg = "Transaction Updated";
	}else if( $b == "Finish" ){
		
		mysql_query("
			update dprc_deposits set status = 'F' where deposit_id = '$deposit_id'
		") or die(mysql_error());
		
		$msg = "Transaction Finished";
		
	}else if( $b == "Cancel" ){
		
		mysql_query("
			update dprc_deposits set status = 'C' where deposit_id = '$deposit_id'
		") or die(mysql_error());
		
		$msg = "Transaction Cancelled";
		
	}
	
	$query="
		select
			*
		from
			dprc_deposits
		where
			deposit_id = '$deposit_id'
	";
	
	$result=mysql_query($query) or die(mysql_error());
	$r=mysql_fetch_assoc($result);
	
	$from_date			= $r['from_date'];
	$to_date			= $r['to_date'];
	$undeposited		= $r['undeposited'];
	$deposited			= $r['deposited'];
	$remarks			= $r['remarks'];
	$user_id			= $r['user_id'];
	$status				= $r['status'];
	$deposit_date		= $r['deposit_date'];
?>

<form name="header_form" id="header_form" action="" method="post">
<div class="module_actions">	
    <div class='inline'>
        DEPOSIT # : <br />  
        <input type="text" class="textbox"  name="search_deposit_id" value="<?=$search_deposit_id?>"  onclick="this.select();"  autocomplete="off" placeholder="Search"  />
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
			dprc_deposits
		where
			1=1
    ";
        
    if(!empty($search_deposit_id)){
    $sql.="
		and
			deposit_id like '$search_deposit_id%'
    ";
    }
	
	$sql.="
		order by deposit_id desc
	";
  
    $pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
            
    $i=$limitvalue;
    $rs = $pager->paginate();
	
	$pagination	= $pager->renderFullNav("$view&b=Search&search_deposit_id=$search_deposit_id");
    ?>
    <div class="pagination">
        <?=$pagination?>
    </div>
    <table id="my_table" cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table">
    <tr>				
    
        <th width="20">#</th>
        <th width="20"></th>
        <th>DEPOSIT #</th>
        <th>FROM DATE</th>
        <th>TO DATE</th>
        <th>DEPOSIT DATE</th>
        <th>UNDEPOSITED AMOUNT</th>
        <th>DEPOSITED AMOUNT</th>
        <th>REMARKS</th>
        <th>STATUS</th>
        <th>ENCODED BY</th>
    </tr>  
    <?php								
    while($r=mysql_fetch_assoc($rs)) {
        
        echo '<tr>';
        echo '<td width="20">'.++$i.'</td>';
        echo '<td width="15"><a href="admin.php?view='.$view.'&deposit_id='.$r['deposit_id'].'" title="Show Details"><img src="images/edit.gif" border="0"></a></td>';
		echo '<td>'.str_pad($r['deposit_id'],7,0,STR_PAD_LEFT).'</td>';	
		echo '<td>'.$r['from_date'].'</td>';	
		echo '<td>'.$r['to_date'].'</td>';	
		echo '<td>'.$r['deposit_date'].'</td>';	
		echo '<td style="text-align:right;">'.number_format($r['undeposited'],2).'</td>';	
		echo '<td style="text-align:right;">'.number_format($r['deposited'],2).'</td>';	
		echo '<td>'.$r['remarks'].'</td>';	
		echo '<td>'.$options->getTransactionStatusName($r['status']).'</td>';	
		echo '<td>'.$options->getUserName($r['user_id']).'</td>';	
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
        <div class="module_title"><img src='images/user_orange.png'>BANK DEPOSITS ENTRY</div>
        <div class="module_actions">
            <input type="hidden" name="deposit_id" value="<?=$deposit_id?>" />
            <div id="messageError">
                <ul>
                </ul>
            </div>
            
            <table class="table-form" style="display:inline-table;">
                <tr>
                	<td>FROM DATE</td>
                    <td>
                    	<input type="text" class="textbox datepicker hinder-submit" id="from_date"  value="<?=$from_date?>" name="from_date" readonly="readonly" />
                   	</td>
                </tr>
                
                <tr>
                	<td>TO DATE</td>
                    <td>
                    	<input type="text" class="textbox datepicker hinder-submit" id="to_date" value="<?=$to_date?>" name="to_date" readonly="readonly" />
                   	</td>
                </tr>
                
                <tr>
                	<td>DEPOSIT DATE</td>
                    <td>
                    	<input type="text" class="textbox datepicker hinder-submit" value="<?=$deposit_date?>" name="deposit_date" />
                   	</td>
                </tr>
                
                <tr style="display:none;">
                	<td>UNDEPOSITED COLLECTION</td>
                    <td>
                    	<input type="text" class="textbox hinder-submit" style="text-align:right;" id="undeposited" value="<?=$undeposited?>" name="undeposited" readonly="readonly"/>
                    </td>
                </tr>
                <tr>
                	<td>DEPOSITED COLLECTION</td>
                    <td>
                    	<input type="text" class="textbox hinder-submit" id="deposited" style="text-align:right;" value="<?=$deposited?>" name="deposited" />
                    </td>
                </tr>
                
                <tr>
                	<td>BALANCE</td>
                    <td>
                    	<input type="text" class="textbox hinder-submit" id="balance" style="text-align:right;" value="<?=$undeposited - $deposited?>" name="balance" readonly="readonly" />
                    </td>
                </tr>
                <tr>
                	<td style="vertical-align:top;">REMARKS</td>
                    <td colspan="3">
                    	<textarea style="border:1px solid #c0c0c0; width:100%;" name="remarks"><?=$remarks?></textarea>
                    </td>
                </tr>
			</table>
            <?php if(!empty($deposit_id)) { ?>
            <table class="table-form" style="display:inline-table;">
            	
            	<tr>
                	<td>DEPOSIT #</td>
                    <td><input type="text" class="textbox" value="<?=str_pad($deposit_id,7,0,STR_PAD_LEFT)?>" readonly="readonly" /></td>
                </tr>
				<tr>
                	<td>STATUS</td>
                    <td><input type="text" class="textbox" value="<?=$options->getTransactionStatusName($status)?>" readonly="readonly" /></td>
                </tr>
                <tr>
                	<td>ENCODED BY</td>
                    <td><input type="text" class="textbox" value="<?=$options->getUserName($user_id)?>" readonly="readonly" /></td>
                </tr>
            </table>
            <?php } ?>
        </div>
        <div class="module_actions">
            
            <?php if($status=="S"){ ?>
            <input type="submit" name="b" id="b" value="Update" />
            <input type="submit" name="b" id="b" value="Finish" />
            <?php
            }else if($status!="F" && $status!="C"){
            ?>
            <input type="submit" name="b" id="b" value="Submit" />
            <?php } ?>
            
            <?php if($b!="Print Preview" && !empty($status)){ ?>
                <input type="submit" name="b" id="b" value="Print Preview" />
            <?php } ?>
        
            <?php if($b=="Print Preview" && $status == "F"){ ?>	
                <input type="button" value="Print" onclick="printIframe('JOframe');" />
            <?php } ?>
                        
            <?php if($status!="C" && !empty($status)){ ?>
            <input type="submit" name="b" id="b" value="Cancel" />
            <?php } ?>
            <a href="admin.php?view=<?=$view?>"><input type="button" value="New" /></a>
    </div>
    </div>
    
<?php
}
?>
<?php
/*if($b == "Print Preview" && $model){

	echo "	<iframe id='JOframe' name='JOframe' frameborder='0' src='printIssuance.php?id=$model' width='100%' height='500'>
			</iframe>";
}
*/
			
?>
</form>
<script type="text/javascript">
	jQuery(function(){	
	
		jQuery("#from_date,#to_date").change(function(){
			var from_date = jQuery("#from_date").val();
			var to_date = jQuery("#to_date").val();
			
			if(from_date != "" && to_date != ""){
				xajax_getUndepositedAmount(xajax.getFormValues('header_form'));
			}
			
		});
		
		jQuery("#undeposited,#deposited").change(function(){
			var deposited 	= jQuery("#deposited").val();
			var undeposited = jQuery("#undeposited").val();
			
			var bal = deposited - undeposited;
			
			jQuery("#balance").val(bal);
		});
	});
</script>
	