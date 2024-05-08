<?php
function getLatestOR(){
	$result = mysql_query("
		select * from cr_header where status != 'C'
		order by cr_header_id desc
	") or die(mysql_error());	
	$r = mysql_fetch_assoc($result);
	return  $r['or_no'];
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
	#DO NOT REMOVE
	$b					= $_REQUEST['b'];
	$user_id			= $_SESSION['userID'];
	
	#FOR SEARCH
	$search_project	= $_REQUEST['search_project'];	
	$search_project_id	= ($search_project) ? $_REQUEST['search_project_id'] : "";
	$search_or_no	= $_REQUEST['search_or_no'];
	$search_receive_from	= $_REQUEST['search_receive_from'];
	
	#HEADER
	$cr_header_id			= $_REQUEST['cr_header_id'];
	$invoice_no				= $_REQUEST['invoice_no'];
	$date					= $_REQUEST['date'];
	$project_id				= $_REQUEST['project_id'];
	$amount					= $_REQUEST['amount'];
	$ar_gchart_id			= $_REQUEST['ar_gchart_id'];
	$cash_gchart_id			= $_REQUEST['cash_gchart_id'];
	$or_no					= $_REQUEST['or_no'];
	$bank					= $_REQUEST['bank'];
	$check_date				= $_REQUEST['check_date'];
	$check_no				= $_REQUEST['check_no'];
	$or_type				= $_REQUEST['or_type'];
	$particulars			= $_REQUEST['particulars'];
	$received_from			= $_REQUEST['received_from'];
	
	#DETAILS
	$gchart_id			= $_REQUEST['gchart_id'];
	$_amount			= $_REQUEST['_amount'];
	
	$id	= $_REQUEST['id'];
	
	if($b == "Add"){
		mysql_query("
			insert into
				cr_detail
			set
				cr_header_id		= '$cr_header_id',
				gchart_id			= '$gchart_id',
				_amount				= '$_amount'
		") or die(mysql_error());
		
	}else if($b == "DC"){
		mysql_query("
			delete from
				cr_detail
			where
				cr_detail_id = '$id'
		") or die(mysql_error());
		
	}
	
	if($b=="Submit"){
		$query="
			insert into 
				cr_header
			set
				date = '$date',
				project_id = '$project_id',
				amount = '$amount',
				invoice_no = '$invoice_no',
				ar_gchart_id = '$ar_gchart_id',
				cash_gchart_id = '$cash_gchart_id',
				user_id	 = '$user_id',
				or_no = '$or_no',
				bank = '$bank',
				check_date = '$check_date',
				check_no = '$check_no',
				or_type = '$or_type',
				particulars = '$particulars',
				received_from = '$received_from'
		";	
		
		mysql_query($query) or die(mysql_error());
		
		$cr_header_id = mysql_insert_id();
		
		$msg="Transaction Saved";
		
	}else if($b=="Update"){
		$query="
			update
				cr_header
			set
				date = '$date',
				project_id = '$project_id',
				amount = '$amount',
				invoice_no = '$invoice_no',
				ar_gchart_id = '$ar_gchart_id',
				cash_gchart_id = '$cash_gchart_id',
				user_id	 = '$user_id',
				or_no = '$or_no',
				bank = '$bank',
				check_date = '$check_date',
				check_no = '$check_no',
				or_type = '$or_type',
				particulars = '$particulars',
				received_from = '$received_from'
			where
				cr_header_id = '$cr_header_id'
		";	
		
		mysql_query($query) or die(mysql_error());
		
		$msg = "Transaction Updated";
	}else if($b=="Cancel"){
		$query="
			update
				cr_header
			set
				status='C'
			where
				cr_header_id = '$cr_header_id'
		";	
		mysql_query($query);
		$msg = "Transaction Cancelled";
		//$options->cancelGL($cr_header_id,'cr_header_id','JV');
		#CANCEL GL
		mysql_query("update gltran_header set status = 'C' where header_id = '$cr_header_id' and header = 'cr_header_id' ") or die(mysql_error());
		
	}else if($b=="Finish"){
		$query="
			update
				cr_header
			set
				status='F'
			where
				cr_header_id = '$cr_header_id'
		";	
		
		mysql_query($query);
		
		$gltran_header_id = $options->postCashReceipts($cr_header_id);
		
		$msg = "Transaction Finished and Posted. Click <a style='text-decoration:underline; font-weight:bold; color:#F00;' href='admin.php?view=1da21dd42f2e46c2d13e&gltran_header_id=$gltran_header_id'>me</a> to see Postings.";
		
	}else if ( $b == "Unfinish" ){
		
		$query="
			update
				cr_header
			set
				status = 'S'
			where
				cr_header_id = '$cr_header_id'
		";	
		mysql_query($query) or die(mysql_error());
		$msg = "Transaction Unfinished";

		#CANCEL GL
		mysql_query("update gltran_header set status = 'C' where header_id = '$cr_header_id' and header = 'cr_header_id' ") or die(mysql_error());
	}
	$query="
		select
			*
		from
			cr_header
		where
			cr_header_id ='$cr_header_id'
	";
	
	$result=mysql_query($query) or die(mysql_error());
	$r=mysql_fetch_assoc($result);
	
	$date					= $r['date'];
	$project_id				= $r['project_id'];
	$amount					= $r['amount'];
	$invoice_no				= $r['invoice_no'];	
	$ar_gchart_id			= $r['ar_gchart_id'];	
	$cash_gchart_id			= $r['cash_gchart_id'];
	$status					= $r['status'];
	$user_id				= $r['user_id'];
	$or_no					= $r['or_no'];
	$bank					= $r['bank'];
	$check_date				= $r['check_date'];
	$check_no				= $r['check_no'];
	$or_type				= $r['or_type'];
	$particulars			= $r['particulars'];
	$received_from			= $r['received_from'];
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
    <div class='inline'>
        Project : <br />  
        <input type="text" class="textbox project" name="search_project" value="<?=$search_project?>"  onclick="this.select();"/>
        <input type="hidden" name="search_project_id" value="<?=$search_project_id?>"  />
    </div>   
	<div class="inline">
    	OR # : <br />  
        <input type="text" class="textbox" name="search_or_no" value="<?=$search_or_no?>"  onclick="this.select();"/>
    </div>
	<div class="inline">
    	RECEIVE FROM : <br />  
        <input type="text" class="textbox" name="search_receive_from" value="<?=$search_receive_from?>"  onclick="this.select();"/>
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

$sql = "select * from cr_header as s left join projects as p on s.project_id = p.project_id 
where 1=1 
";

if(  $search_project_id ) $sql .= " and s.project_id = '$search_project_id'";
if(  $search_or_no ) $sql .= " and or_no like '%$search_or_no%'";
if(  $search_receive_from ) $sql .= " and received_from like '%$search_receive_from%'";

$sql.="order by or_no asc";

$pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
		
$i=$limitvalue;
$rs = $pager->paginate();
?>
<div class="pagination">
	<?=$pager->renderFullNav("$view&b=Search&search_project=$search_project&search_project_id=$search_project_id&search_or_no=$search_or_no")?>
</div>
<table id="my_table" cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table">
<tr>				

    <th width="20"><b>#</b></th>
    <th width="20"></th>
    <th>O.R. # </th>
    <th>Received From</th>
    <th>O.R. Type </th>
    <th>Invoice #</th>
    <th>Date</th>
    <th>Project</th>
    <th>Amount</th>
    <th>Status</th>
</tr>  
<?php								
while($r=mysql_fetch_assoc($rs)) {
	
	echo '<tr>';
	echo '<td width="20">'.++$i.'</td>';
	echo '<td width="15"><a href="admin.php?view='.$view.'&cr_header_id='.$r[cr_header_id].'" title="Show Details"><img src="images/edit.gif" border="0"></a></td>';
	echo '<td>'.$r['or_no'].'</td>';
	echo '<td>'.$r['received_from'].'</td>';
	echo '<td>'.$r['or_type'].'</td>';
	echo '<td>'.$r['invoice_no'].'</td>';	
	echo '<td>'.date("F j, Y",strtotime($r['date'])).'</td>';	
	echo '<td>'.$r['project_name'].'</td>';	
	echo '<td>'.$r['amount'].'</td>';	
	echo '<td>'.$options->getTransactionStatusName($r['status']).'</td>';	
	echo '</tr>';
}
?>
</table>
<div class="pagination">
	<?=$pager->renderFullNav("$view&b=Search&search_project=$search_project&search_project_id=$search_project_id&search_or_no=$search_or_no")?>
</div>
<?php
}else{
?>
<div class=form_layout>
	<?php if(!empty($msg)) echo '<div id="status_update" class="ui-state-highlight ui-corner-all" style="padding: 0px 0.7em; text-align:left;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right:.3em;"></span> '.$msg.'</p></div>'; ?>
	<div class="module_title"><img src='images/user_orange.png'>CASH RECEIPTS</div>
    
    <div class="module_actions">
        <input type="hidden" name="cr_header_id" id="cr_header_id" value="<?=$cr_header_id?>" />
        <div id="messageError">
            <ul>
            </ul>
        </div>
        <div class='inline'>
            <div>Date: </div>        
            <div>
                <input type="text" class="datepicker textbox3" title="Please enter date"  name="date" readonly='readonly'  value="<?=$date?>" />
            </div>
        </div>   
        
        <div class='inline'>
            <div>OR #: </div>        
            <div>
            	<?php //$or_no = (empty($cr_header_id)) ? getLatestOR() : $or_no; ?>
                <input type="text" class="textbox3" id="or_no"  name="or_no" value="<?=$or_no?>">
            </div>
        </div> 
        
        <div class='inline'>
            <div>Received From: </div>        
            <div>
            	<input type="text" class="textbox" name="received_from" value="<?=$received_from?>" />
            </div>
        </div>      
        
        <div class="inline">
        	OR Type: <br />
            <select name="or_type" id="or_type">
            	
            	<option value="">Select OR Type:</option>
                <option value="VAT" <?=($or_type == "VAT") ? "selected = 'selected'" : "" ?> >VAT</option>
                <option value="NON-VAT" <?=($or_type == "NON-VAT") ? "selected = 'selected'" : "" ?> >NON-VAT</option>
            </select>
        </div>
        
        <div class='inline'>
            <div>Invoice #: </div>        
            <div>
                <input type="text" class="textbox3"  name="invoice_no" id="invoice_no" value="<?=$invoice_no?>" >
            </div>
        </div> 
        
        
       <div class='inline'>
            Project : <br />  
            <input type="text" class="textbox project" id="project" value="<?=$options->getAttribute('projects','project_id',$project_id,'project_name')?>" onclick="this.select();" />
            <input type="hidden" name="project_id" id="project_id" value="<?=$project_id?>" title="Please select Project" />
        </div>
        
        <div class='inline'>
            Amount: <br />
            <input type="text" class="textbox" name="amount" id="amount" value="<?=$amount?>" autocomplete="off" >
        </div>  
        
        <br />
        
        <div class="inline">
			Check Date : <br />
            <input type="text" class="textbox3 datepicker" name="check_date" value="<?=$check_date?>" />
        </div>
        
        <div class="inline">
        	Check No : <br />
            <input type="text" class="textbox" name="check_no" value="<?=$check_no?>" />
        </div>
        
        <div class="inline">
        	Bank : <br />
            <input type="text" class="textbox" name="bank" value="<?=$bank?>" />
        </div>
       
        <br />
    
        <div class="inline">
            A/R Account : <br />
            <?=$options->option_chart_of_accounts($ar_gchart_id,'ar_gchart_id')?>
        </div>
        
        <div class="inline">
            Cash Account : <br />
            <?=$options->option_chart_of_accounts($cash_gchart_id,'cash_gchart_id')?>
        </div>
        <div>
        	Particulars <br />
            <input type="text" name="particulars" value="<?=$particulars?>" class="textbox2"  />
        </div>
        
        <?php
        if(!empty($status)){
        ?>
        <br />
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
    	<?php if( $status == "F" ){ ?>
        <input type="submit" name="b" value="Unfinish" />
        <?php } ?>
		<?php if($status=="S"){ ?>
        <input type="submit" name="b" id="b" value="Update" />
        <input type="submit" name="b" id="b" value="Finish" />
        <?php }else if($status!="F" && $status!="C"){ ?>
        <input type="submit" name="b" id="b" value="Submit" />
        <?php } ?>
        <?php if($b!="Print Preview" && !empty($status)){ ?>
            <!--<input type="submit" name="b" id="b" value="Print Preview" /> -->
        <?php } ?>
        <?php if($b=="Print Preview"){ ?>	
            <input type="button" value="Print" onclick="printIframe('JOframe');" />
        <?php } ?>
        <?php if($status!="C" && !empty($status)){ ?>
        <input type="submit" name="b" id="b" value="Cancel" />
        <?php } ?>
        <a href="admin.php?view=<?=$view?>"><input type="button" value="New" /></a>
   	</div>
    <?php
	if( $status == 'S'):	
	?>
    <div class="module_actions">
 
    	<div class="inline">
        	Account : <br /> 
            <?php echo $options->option_chart_of_accounts('','gchart_id'); ?>
        </div>  
        
        <div style="display:inline-block; margin-right:20px;">
        	<div>Amount : </div>        
            <div><input type="text" class="textbox3"  name="_amount" /></div>
        </div> 
        
        <input type="submit" name="b" value="Add"  />
    </div>
    
    
    <?php endif ?>
</div>
<?php
if($b == "Print Preview" && $cr_header_id){

	echo "	<iframe id='JOframe' name='JOframe' frameborder='0' src='printIssuance.php?id=$cr_header_id' width='100%' height='500'>
			</iframe>";
?>
<?php
}else{
?>
<div class="clearfix">
	<table cellpadding="5"  class="cp_table" style="width:100%;">
    <tr>
    	<th width="20" style="text-align:left;"></th>
    	<th style="text-align:left;">Account</th>
        <th style="width:20%; text-align:right;">Amount</th>
    </tr>
	<?php
    $result = mysql_query("
		select
			d.cr_detail_id,
			d.gchart_id,
			d._amount
		from
			cr_header as h, cr_detail as d
		where
			h.cr_header_id = d.cr_header_id
		and
			h.cr_header_id = '$cr_header_id'		
	") or die(mysql_error());
	while($r = mysql_fetch_assoc($result)){
		$gchart_id	 	= $r['gchart_id'];
		$total_amount	= $r['_amount'];
		$cr_detail_id	= $r['cr_detail_id'];
	?>		
   	<tr>
    	<td align="center"><a href="admin.php?view=<?=$view?>&cr_header_id=<?=$cr_header_id?>&b=DC&id=<?=$cr_detail_id?>" onclick="return approve_confirm();"><img src="images/trash.gif"  /></a></td>
   		<td><?=$options->getAttribute('gchart','gchart_id',$gchart_id,'gchart')?></td>
   		<td class="align-right"><?=number_format($total_amount,2,'.',',')?></td>
   	</tr>
	<?php } ?>
	</table>		
</div>
<?php } ?>
<?php } ?>
</form>
<script type="text/javascript">
j(function(){	
	j("#invoice_no").change(function(){
		xajax_getDataFromInvoiceNo(xajax.getFormValues('header_form'));
	});
	
	j("#or_type").change(function(){
		xajax_getLatestORSeries(xajax.getFormValues('header_form'));
	});
});
</script>
	