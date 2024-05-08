<?php include('library/acctg.php'); ?>
<?php
function getPosted(){
	$result = mysql_query("
		select DISTINCT(h_id) as h_id from gltran_detail where h = 'dprc_receipt_id'
	") or die(mysql_error());
	$a  = array();
	while( $r = mysql_fetch_assoc($result) ){
		$a[] = $r['h_id'];
	}
	return $a;
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

.table-form tr td:nth-child(1){
	text-align:right;
	font-weight:bold;
}


</style>
<?php
	$b					= $_REQUEST['b'];
	$user_id			= $_SESSION['userID'];

	$dprc_receipt_id		= $_REQUEST['dprc_receipt_id'];
	
	$search_reference		= $_REQUEST['search_reference'];
	
	$dprc_receipt_id		= $_REQUEST['dprc_receipt_id'];
	$customer_id			= $_REQUEST['customer_id'];
	$date					= $_REQUEST['date'];
	$or_no					= $_REQUEST['or_no'];
	$amount					= $_REQUEST['amount'];
	$remarks				= $_REQUEST['remarks'];
	
	
	if( $b == "Post" ){
		$aID 				= $_REQUEST['aID'];
		$post_date			= $_REQUEST['post_date'];
		$post_remarks		= $_REQUEST['post_remarks'];
		$post_project_id	= $_REQUEST['post_project_id'];
		
		//INSERT GL HERE
		$gltran_header_id = Accounting::postWaterBill($aID,$post_date,$post_remarks,$post_project_id);
		$msg = "Click <a style='color:#F00; font-weight:bold; font-size:13px;' href='admin.php?view=1da21dd42f2e46c2d13e&gltran_header_id=$gltran_header_id'>here</a> to see GL postings.";
	}
	
	if($b == 'D'){ 
		mysql_query("
			update
				dprc_receipt
			set
				status  = 'C'
			where
				dprc_receipt_id = '$dprc_receipt_id'
		") or die(mysql_error());
		$msg = "Transaction Deleted";
	}
	
	if($b=="Submit"){
		$query="
			insert into 
				dprc_receipt
			set
				customer_id = '$customer_id',
				date = '$date',
				or_no = '$or_no',
				amount = '$amount',
				remarks = '$remarks',
				user_id = '$user_id'
		";	
		
		mysql_query($query) or die(mysql_error());
		$dprc_receipt_id = mysql_insert_id();
		$msg = "Transaction Added";
		
	}else if($b=="Update"){
		$query="
			update
				dprc_receipt
			set
				customer_id = '$customer_id',
				date = '$date',
				or_no = '$or_no',
				amount = '$amount',
				remarks = '$remarks',
				user_id = '$user_id'
			where
				dprc_receipt_id = '$dprc_receipt_id'
		";	
		
		mysql_query($query) or die(mysql_error());
		
		$msg = "Transaction Updated";
	}
	
	$query="
		select
			*
		from
			dprc_receipt 
		where
			dprc_receipt_id = '$dprc_receipt_id'
	";
	
	$result=mysql_query($query) or die(mysql_error());
	$r=mysql_fetch_assoc($result);
	
	$dprc_receipt_id		= $r['dprc_receipt_id'];
	$customer_id			= $r['customer_id'];
	$date					= $r['date'];
	$or_no					= $r['or_no'];
	$amount					= $r['amount'];
	$remarks				= $r['remarks'];
	$user_id				= $r['user_id'];
?>

<form name="header_form" id="header_form" action="" method="post">
<div class="module_actions">	
    <div class='inline'>
        Search Reference : <br />  
        <input type="text" class="textbox"  name="search_reference" value="<?=$search_reference?>"  onclick="this.select();"  autocomplete="off" />
    </div>   
    <input type="submit" name="b" value="Search" />
    <a href="admin.php?view=<?=$view?>"><input type="button" value="New" /></a>
</div>

<?php
if($b == "Search"){
?>
	<?php
	
	
	$aPosted = getPosted();
	
    $page = $_REQUEST['page'];
    if(empty($page)) $page = 1;
     
    $limitvalue = $page * $limit - ($limit);
    
    $sql = "
		select
        	*
        from
			dprc_receipt as wc, customer as c
		where 
			wc.customer_id = c.customer_id
    ";
        
    if(!empty($search_reference)){
    $sql.=" and or_no like '$search_reference%' ";
    }
	
	$sql.=" order by date asc ";
  
    $pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
            
    $i=$limitvalue;
    $rs = $pager->paginate();
	
	$pagination	= $pager->renderFullNav("$view&b=Search&search_reference=$search_reference");
    ?>
    <div class="module_actions">
    	<input type="button" class="buttons" onclick="jQuery('#_dialog_gl').dialog('open');" value="POST to GL"  />
    </div>
    <div class="pagination">
        <?=$pagination?>
    </div>
    <table id="my_table" cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table">
    <tr>				
    
        <th width="20">#</th>
        <th width="20"></th>
        <th width="20"></th>
        <th>CUSTOMER</th>
        <th>DATE</th>
        <th style="text-align:right;">AMOUNT</th>
    </tr>  
    <?php								
    while($r=mysql_fetch_assoc($rs)) {
		
        $check_box_disabled = (in_array($r['dprc_receipt_id'],$aPosted));
		
        echo '<tr>';
        echo '<td width="20">'.++$i.'</td>';
		echo "<td><input type='checkbox' name='aID[]' value='$r[dprc_receipt_id]' ".( ( $check_box_disabled ) ? "disabled='disabled'" : "" )."  ></td>";
        echo '<td width="15"><a href="admin.php?view='.$view.'&dprc_receipt_id='.$r['dprc_receipt_id'].'" title="Show Details"><img src="images/edit.gif" border="0"></a></td>';
		echo '<td>'."$r[customer_last_name], $r[customer_first_name] $r[customer_middle_name]".'</td>';	
		echo '<td>'."$r[date]".'</td>';	
		echo '<td style="text-align:right;">'.number_format($r['amount'],2).'</td>';	
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
        <div class="module_title"><img src='images/user_orange.png'>WATER CONSUMPTION MODULE</div>
        <div class="module_actions">
            <input type="hidden" name="dprc_receipt_id" value="<?=$dprc_receipt_id?>" />
            <div id="messageError">
                <ul>
                </ul>
            </div>
            
            <table class="table-form">
            	<tr>
                	<td>Customer:</td>
                    <td><?=$options->getTableAssoc($customer_id,'customer_id','Select Customer',"select * from customer order by customer_first_name asc",'customer_id','customer_last_name', array("customer_first_name",'customer_middle_name','customer_last_name'))?></td>
                </tr>
                <tr>
                	<td>Date:</td>
                    <?php $date = (empty($date)) ? date("Y-m-d") : $date ?>
                    <td><input type="text" class="textbox datepicker" style="width:100%;" name="date" value="<?=$date?>"/></td>
                </tr>
                <tr>
                	<td>OR No:</td>
                    <td><input type="text" class="textbox" style="width:100%;" name="or_no" value="<?=$or_no?>" /></td>
                </tr>
                <tr>
                	<td>Amount:</td>
                    <td><input type="text" class="textbox" style="width:100%;" name="amount" value="<?=$amount?>" /></td>
                </tr>
                
                <tr>
                	<td style="vertical-align:top;">Remarks</td>
                    <td colspan="3">
                    	<textarea style="border:1px solid #c0c0c0; width:100%;" name="remarks"><?=$remarks?></textarea>
                    </td>
                </tr>
                
                <?php
				if(!empty($user_id))
				echo "
					<tr>
						<td>Encoded By:</td>
						<td><input type=\"text\" class=\"textbox\" style=\"width:100%;\" value=\"".$options->getUserName($user_id)."\" /></td>
					</tr>
				";
                ?>
            
            </table>
            
        </div>
        <div class="module_actions">
            <?php
            if(!empty($dprc_receipt_id)){
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
			if(!empty($dprc_receipt_id)){
            ?>
            <a href="admin.php?view=<?=$view?>&dprc_receipt_id=<?=$dprc_receipt_id?>&b=D" onclick="return approve_confirm();"><input type="button" value="Cancel" /></a>
            <?php
			}
            ?>
        </div>
    </div>
    
<?php
}
?>
<?php
/*if($b == "Print Preview" && $dprc_receipt){

	echo "	<iframe id='JOframe' name='JOframe' frameborder='0' src='printIssuance.php?id=$dprc_receipt' width='100%' height='500'>
			</iframe>";
}
*/
			
?>
</form>
<div id="_dialog_gl" style="padding:10px;">
	<table>
    	<tr>
        	<td>Post Date:</td>
            <td><input type="text" class="datepicker" name="post_date" readonly="readonly" /></td>
       	</tr>
        <tr>
        	<td>Project:</td>
            <td>
            	<input type="text" class="textbox project" />
                <input type="hidden" name="post_project_id" />
           	</td>
        </tr>
        <tr>
        	<td>Remarks:</td>
            <td colspan="3"><textarea style="border:1px solid #c0c0c0; width:100%;" name="post_remarks" ></textarea></td>
        </tr>
    </table>
    <input type="submit" name="b" value="Post" onclick="return approve_confirm();" />
</div>
<script type="text/javascript">
j(function(){	
	var dialog_gl = j("#_dialog_gl").dialog({autoOpen: false , modal:true , show: 'slide' , hide : 'slide' , width : 'auto', resizable : false, height : 'auto', title : "POST TO GENERAL LEDGER"});
	dialog_gl.parent().appendTo(jQuery("form:first"));;
});
</script>
	