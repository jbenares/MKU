<?php
function getNextCV(){
	$result = mysql_query("
		select * from cv_header where status != 'C' order by cv_no desc
	") or die(mysql_error());
	$r = mysql_fetch_assoc($result);
	return ($r['cv_no'] + 1);
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
	$b					= $_REQUEST['b'];
	$user_id			= $_SESSION['userID'];

	$search_supplier		= $_REQUEST['search_supplier'];
	$search_supplier_id		= ($search_supplier)?$_REQUEST['search_supplier_id']:"";
	
	
	$ev_header_id		= $_REQUEST['ev_header_id'];
	$date				= $_REQUEST['date'];
	$vat				= $_REQUEST['vat'];
	$wtax				= $_REQUEST['wtax'];
	$vat_gchart_id		= $_REQUEST['vat_gchart_id'];
	$wtax_gchart_id		= $_REQUEST['wtax_gchart_id'];
	$supplier_id		= $_REQUEST['supplier_id'];
	$cash_gchart_id		= $_REQUEST['cash_gchart_id'];
	$project_id			= $_REQUEST['project_id'];
	$po_header_id		= $_REQUEST['po_header_id'];
	$labor_mat_po		= $_REQUEST['labor_mat_po'];
	
	
	$gchart_id		= $_REQUEST['gchart_id'];
	$amount			= $_REQUEST['amount'];
	
	$id	= $_REQUEST['id'];
	
	if($b == "Update PO Reference"){
		mysql_query("	
			update 
				ev_header
			set
				po_header_id = '$po_header_id',
				labor_mat_po = '$labor_mat_po'
			where
				ev_header_id = '$ev_header_id'
		") or die(mysql_error());	
		$msg = "PO Reference Updated";
	}
	
	if($b == "Generate CV"){
		$result = mysql_query("
			select * from ev_header where ev_header_id = '$ev_header_id'	
		") or die(mysql_error());
		$r = mysql_fetch_assoc($result);
		$_date = date("Y-m-d");
		$percent = 100;
		$supplier_id = $r['supplier_id'];
		$cash_gchart_id	= $r['cash_gchart_id'];
		$project_id = $r['project_id'];
		
		mysql_query("
			insert into cv_header (percent,cv_date,check_date,supplier_id,cash_gchart_id,status,user_id,type,wtax,vat,wtax_gchart_id,vat_gchart_id,cv_no) values
			('100','$_date','$_date','$supplier_id','$cash_gchart_id','S','$user_id','E','$wtax','$vat','$wtax_gchart_id','$vat_gchart_id','".getNextCV()."')
		") or die(mysql_error());
		
		$cv_header_id = mysql_insert_id();
		
		mysql_query("Update ev_header set cv_header_id = '$cv_header_id' where ev_header_id = '$ev_header_id'") or die (mysql_error());
		
		$result = mysql_query("select * from ev_detail where ev_header_id = '$ev_header_id'") or die(mysql_error());
		while($r = mysql_fetch_assoc($result)){
			$gchart_id = $r['gchart_id'];	
			$amount	= $r['amount'];
			$project_id = $r['project_id'];
			
			mysql_query("insert into cv_detail (cv_header_id,gchart_id,amount,project_id) values ('$cv_header_id','$gchart_id','$amount','$project_id') ") or die(mysql_error());
		}
		
		header("Location:admin.php?view=9d825239df14c9830e3b&cv_header_id=$cv_header_id");
		
	}
	
	if($b == "Add"){
		mysql_query("
			insert into
				ev_detail
			set
				ev_header_id		= '$ev_header_id',
				gchart_id			= '$gchart_id',
				amount				= '$amount',
				project_id			= '$project_id'
		") or die(mysql_error());
		
	}else if($b == "DC"){
		mysql_query("
			delete from
				ev_detail
			where
				ev_detail_id = '$id'
		") or die(mysql_error());
		
	}
	
	if($b=="Submit"){
		
		if($cash_gchart_id){
				$query="
					insert into 
						ev_header
					set
						date		= '$date',
						supplier_id	= '$supplier_id',
						user_id		= '$user_id',
						status		= 'S',
						cash_gchart_id	= '$cash_gchart_id',
						wtax = '$wtax',
						vat = '$vat',
						wtax_gchart_id = '$wtax_gchart_id',
						vat_gchart_id = '$vat_gchart_id',
						po_header_id = '$po_header_id',
						labor_mat_po = '$labor_mat_po'
				";	
				
				mysql_query($query) or die(mysql_error());
				
				$ev_header_id = mysql_insert_id();
				
				$msg="Transaction Saved";
				
		}else{
				$msg="<div style='color: red; text-decoration: underline;'>TRANSACTION NOT SAVED! PLEASE CHOOSE A CASH ACCOUNT AND TRY AGAIN";
		}
		
	}else if($b=="Update"){
		$query="
			update
				ev_header
			set
				date		= '$date',
				supplier_id	= '$supplier_id',
				user_id		= '$user_id',
				status		= 'S',
				cash_gchart_id	= '$cash_gchart_id',
				wtax = '$wtax',
				vat = '$vat',
				wtax_gchart_id = '$wtax_gchart_id',
				vat_gchart_id = '$vat_gchart_id',
				po_header_id = '$po_header_id',
				labor_mat_po = '$labor_mat_po'
			where
				ev_header_id = '$ev_header_id'
		";	
		
		mysql_query($query) or die(mysql_error());
		
		$msg = "Transaction Updated";
	}else if($b=="Cancel"){
		$query="
			update
				ev_header
			set
				status='C'
			where
				ev_header_id = '$ev_header_id'
		";	
		mysql_query($query);
		$msg = "Transaction Cancelled";
		$options->cancelGL($ev_header_id,'ev_header_id','JV');
		
	}else if($b=="Finish") {
		$query="
			update
				ev_header
			set
				status='F'
			where
				ev_header_id = '$ev_header_id'
		";	
		
		mysql_query($query);
		$msg = "Transaction Finished";
	}
	$query="
		select
			*
		from
			ev_header
		where
			ev_header_id ='$ev_header_id'
	";
	
	$result=mysql_query($query) or die(mysql_error());
	$r=mysql_fetch_assoc($result);
	
	$date			= ($r['date']!="0000-00-00")?$r['date']:"";
	$supplier_id	= $r['supplier_id'];
	$account_name	= (!empty($supplier_id))?$options->getSupplierName($supplier_id):"";

	$user_id		= $r['user_id'];
	$status			= $r['status'];
	$cash_gchart_id	= $r['cash_gchart_id'];
	
	$wtax			= $r['wtax'];
	$vat			= $r['vat'];
	$wtax_gchart_id	= $r['wtax_gchart_id'];
	$vat_gchart_id	= $r['vat_gchart_id'];
	$po_header_id	= $r['po_header_id'];
	$labor_mat_po	= $r['labor_mat_po'];
	
	$project_id			= $r['project_id'];
	$project_name		= $options->attr_Project($project_id,'project_name');
	$project_code		= $options->attr_Project($project_id,'project_code');
	$project_name_code	= ($project_id)?"$project_name - $project_code":"";
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
        Supplier/Client : <br />  
        <input type="text" class="textbox supplier"  name="search_supplier" value="<?=$search_supplier?>"  onclick="this.select();"/>
        <input type="hidden" name="search_supplier_id"  value="<?=$search_supplier_id?>" title="Please Select Supplier" />
    </div>   
    <div class="inline">
    	PO # : <br />
        <input type="text" class="textbox" name="search_po_header_id" value="<?=$_REQUEST['search_po_header_id']?>"  />
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
			  ev_header
		where 
			1=1 
	";
	
if(!empty($search_supplier_id)){
$sql.="
	and
		supplier_id = '$search_supplier_id'	
	";
}
	
if(!empty($_REQUEST['search_po_header_id'])){
$sql.="
	and
		po_header_id = '$_REQUEST[search_po_header_id]'	
	";
}
$sql.="
		order 
			by date desc
	";
	
//echo $sql;
$pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
		
$i=$limitvalue;
$rs = $pager->paginate();
?>
<div class="pagination">
	<?=$pager->renderFullNav("$view&b=Search&search_supplier_id=$search_supplier_id&search_supplier=$search_supplier")?>
</div>
<table id="my_table" cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table">
<tr>				

    <th width="20"><b>#</b></th>
    <th width="20"></th>
    <th>EV # :</th>
    <th><b>Date</b></th>
    <th><b>Supplier</b></th>
    <th><b>Status</b></th>
</tr>  
<?php								
while($r=mysql_fetch_assoc($rs)) {
	
	echo '<tr>';
	echo '<td width="20">'.++$i.'</td>';
	echo '<td width="15"><a href="admin.php?view='.$view.'&ev_header_id='.$r[ev_header_id].'" title="Show Details"><img src="images/edit.gif" border="0"></a></td>';
	echo '<td>'.str_pad($r['ev_header_id'],7,0,STR_PAD_LEFT).'</td>';
	echo '<td>'.date("F j, Y",strtotime($r['date'])).'</td>';	
	echo '<td>'.$options->getSupplierName($r[supplier_id]).'</td>';	
	echo '<td>'.$options->getTransactionStatusName($r[status]).'</td>';	
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
	<div class="module_title"><img src='images/user_orange.png'>DISBURSEMENT VOUCHER</div>
    
    <div class="module_actions">
        <input type="hidden" name="ev_header_id" id="ev_header_id" value="<?=$ev_header_id?>" />
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
            Supplier / Client :  <br />  
            <input type="text" class="textbox supplier" value="<?=$account_name?>"  onclick="this.select();"  />
            <input type="hidden" name="supplier_id"  id="account_id" value="<?=$supplier_id?>" title="Please Select Supplier"  />

        </div>
        
        <div class="inline">
        	PO # <em>(For Advance Payments)</em> : <br />
            <input type="text" class="textbox" name="po_header_id" value="<?=$po_header_id?>" />
        </div>
        
        <div class="inline">
        	Labor Mat PO # <em>(For Subcontractors)</em> : <br />
            <input type="text" class="textbox" name="labor_mat_po" value="<?=$labor_mat_po?>" />
        </div>
        
       <!-- <div class='inline'>
            Project : <br />  
            <input type="text" class="textbox" id="project_name" value="<?=$project_name_code?>" onclick="this.select();"  />
            <input type="hidden" name="project_id"  id="project_id" value="<?=$project_id?>" title="Please select Project" />
        </div>  -->
        
        <br />
        
        <div class='inline'>
         	VAT ( % ): <br />
	        <input type="text" class="textbox3" name="vat" value="<?=$vat?>">
        </div>  
        
        <div class='inline'>
         	Witholding Tax ( % ): <br />
	        <input type="text" class="textbox3" name="wtax" value="<?=$wtax?>" placeholder="">
        </div>            
        
        <br />
        <div class="inline">
            Cash Account : <br />
            <?=$options->option_chart_of_accounts($cash_gchart_id,'cash_gchart_id')?>
        </div>
        
        <div class="inline">
            Tax Payables Account : <br />
            <?=$options->option_chart_of_accounts($wtax_gchart_id,'wtax_gchart_id')?>
        </div>
        
        <div class="inline">
            VAT Account : <br />
            <?=$options->option_chart_of_accounts($vat_gchart_id,'vat_gchart_id')?>
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
		<?php if($status=="S"){ ?>
        <input type="submit" name="b" id="b" value="Update" />
        <input type="submit" name="b" id="b" value="Finish" onClick="return confirm('ARE YOU SURE?')"/>
        <?php }else if($status!="F" && $status!="C"){ ?>
        <input type="submit" name="b" id="b" value="Submit" onClick="return confirm('SUBMIT?')"/>
        <?php } ?>
        <?php if($b!="Print Preview" && !empty($status)){ ?>
            <input type="submit" name="b" id="b" value="Print Preview" />
        <?php } ?>
        <?php if($b=="Print Preview"){ ?>	
            <input type="button" value="Print" onclick="printIframe('JOframe');" />
        <?php } ?>
        <?php if($status!="C" && !empty($status)){ ?>
        <input type="submit" name="b" id="b" value="Cancel" onClick="return confirm('READ BEFORE YOU PROCEED: CANCELLING THIS TRANSACTION WILL ALSO CANCEL THE CHECK VOUCHER UNDER THIS.')"/>
        <?php } ?>
        <?php if($status=="F"){ ?>
        <input type="submit" name="b" value="Generate CV" onClick="return confirm('CREATE CHECK VOUCHER? PLEASE CHECK THE TAX FIELD IF NECESSARY.')"/>
        <?php } ?>
        <?php if(!empty($status)){ ?>
        <input type="submit" name="b" value="Update PO Reference" />
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
            <div><input type="text" class="textbox3"  name="amount" /></div>
        </div> 
        
        <div style="display:inline-block; margin-right:20px;">
        	Project : <br />
            <?=$options->option_projects();?>
        </div> 
        
        <input type="submit" name="b" value="Add"  />
    </div>
    
    
    <?php endif ?>
</div>
<?php
if($b == "Print Preview" && $ev_header_id){

	echo "	<iframe id='JOframe' name='JOframe' frameborder='0' src='print_disbursement_voucher.php?ev_header_id=$ev_header_id' width='100%' height='500'>
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
        <th style="text-align:left;">Project</th>
        <th style="width:20%; text-align:right;">Amount</th>
    </tr>
	<?php
    $result = mysql_query("
		select
			*
		from
			ev_header as h, ev_detail as d
		where
			h.ev_header_id = d.ev_header_id
		and
			h.ev_header_id = '$ev_header_id'		
	") or die(mysql_error());
	$total = 0;
	while($r = mysql_fetch_assoc($result)){
		$gchart_id	 	= $r['gchart_id'];
		$total_amount	= $r['amount'];
		$ev_detail_id	= $r['ev_detail_id'];
		$project_name 	= $options->getAttribute("projects","project_id",$r['project_id'],"project_name");
		$total			+= $r['amount'];
	?>		
   	<tr>
    	<td align="center"><a href="admin.php?view=<?=$view?>&ev_header_id=<?=$ev_header_id?>&b=DC&id=<?=$ev_detail_id?>" onclick="return approve_confirm();"><img src="images/trash.gif"  /></a></td>
   		<td><?=$options->getAttribute('gchart','gchart_id',$gchart_id,'gchart')?></td>
        <td><?=$project_name?></td>
   		<td class="align-right"><?=number_format($total_amount,2,'.',',')?></td>
   	</tr>
	<?php } ?>
    <tr>
    	<td></td>
        <td></td>
        <td></td>
        <td style="text-align:right; font-weight:bold;"><?=number_format($total,2,'.',',')?></td>
    </tr>
	</table>		
</div>
<?php } ?>
<?php } ?>
</form>
<script type="text/javascript">
j(function(){	
});
</script>