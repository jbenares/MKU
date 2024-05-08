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
	text-align:left;
}


</style>
<?php
	#DO NOT REMOVE
	$b					= $_REQUEST['b'];
	$user_id			= $_SESSION['userID'];

	#SEARCH FILTERS
	$search_supplier		= $_REQUEST['search_supplier'];
	$keyword        		= $_REQUEST['keyword'];
	
	
	#HEADER
	$po_header_id			= $_REQUEST['po_header_id'];
	$budget_header_id		= $_REQUEST['budget_header_id'];
	$date					= $_REQUEST['date'];
	$project_id 			= $_REQUEST['project_id'];
	$work_category_id		= $_REQUEST['work_category_id'];
	$sub_work_category_id	= $_REQUEST['sub_work_category_id'];
	$supplier_id			= $_REQUEST['supplier_id'];
	$terms					= $_REQUEST['terms'];
	$remarks				= $_REQUEST['remarks'];
	$expense_gchart_id		= $_REQUEST['expense_gchart_id'];
	$ap_gchart_id			= $_REQUEST['ap_gchart_id'];
	$discount_amount		= $_REQUEST['discount_amount'];
	
	#DETAILS
	$description 			= $_REQUEST['description'];
	
	#SUB DETAILS
	$sub_description		= $_REQUEST['sub_description'];
	$unit					= $_REQUEST['unit'];
	$unit_cost				= $_REQUEST['unit_cost'];
	$amount					= $_REQUEST['amount'];
	$person					= $_REQUEST['person'];
	$chargables				= $_REQUEST['chargables'];
	
	
	$id	= $_REQUEST['id'];
	
	if($b == "Unfinish"){
		mysql_query("update po_header set status = 'S' where po_header_id = '$po_header_id'") or die(mysql_error());	
	}
	
	if($b == "Add"){
		mysql_query("
			insert into
				spo_detail
			set
				po_header_id		= '$po_header_id',
				description			= '$description'
		") or die(mysql_error());
		
	}else if($b == "DD"){
		#DD - DELETE DETAILS
		mysql_query("
			delete from
				spo_detail
			where
				spo_detail_id = '$id'
		") or die(mysql_error());
		
		mysql_query("
			delete from
				sub_spo_detail
			where
				spo_detail_id = '$id'
		") or die(mysql_error());
		
		$msg = "Details Added";

		$msg = "Detail Deleted";
		
	}else if($b == "DS"){
		#DS - DELETE SUB DETAILS
		mysql_query("
			delete from
				sub_spo_detail
			where
				sub_spo_detail_id = '$id'
		") or die(mysql_error());
		$msg = "Sub Detail Deleted";
	}	
	if($b=="Submit"){
		$query="
			insert into 
				po_header
			set
				date = '$date',
				budget_header_id='$budget_header_id',
				project_id = '$project_id',
				work_category_id = '$work_category_id',
				sub_work_category_id = '$sub_work_category_id',
				supplier_id = '$supplier_id',
				terms = '$terms',
				remarks = '$remarks',
				po_type = 'S',
				status = 'S',
				user_id = '$user_id',
				expense_gchart_id = '$expense_gchart_id',
				ap_gchart_id = '$ap_gchart_id',
				discount_amount = '$discount_amount'
		";	
		mysql_query($query) or die(mysql_error());
		
		$po_header_id = mysql_insert_id();
		
		$msg="Transaction Saved";
		
	}else if($b=="Update"){
		$query="
			update
				po_header
			set
				date = '$date',
				budget_header_id='$budget_header_id',
				project_id = '$project_id',
				work_category_id = '$work_category_id',
				sub_work_category_id = '$sub_work_category_id',
				supplier_id = '$supplier_id',
				terms = '$terms',
				remarks = '$remarks',
				po_type = 'S',
				status = 'S',
				user_id = '$user_id',
				expense_gchart_id = '$expense_gchart_id',
				ap_gchart_id = '$ap_gchart_id',
				discount_amount = '$discount_amount'
			where
				po_header_id = '$po_header_id'
		";	
		
		mysql_query($query) or die(mysql_error());
		
		$msg = "Transaction Updated";
	}else if($b=="Cancel"){
		$query="
			update
				po_header
			set
				status='C'
			where
				po_header_id = '$po_header_id'
		";	
		mysql_query($query);
		$msg = "Transaction Cancelled";
		#$options->cancelGL($po_header_id,'po_header_id','JV');
	}else if($b=="Finish"){
		
		//set payroll header
		mysql_query("
			insert into
				po_header_payroll_det
			set	
				po_header_id	= '$po_header_id'
		");
		
		$payroll_header_id = mysql_insert_id();
		
		$query="
			update
				po_header
			set
				status='F',
				payroll_header_id='$payroll_header_id'
			where
				po_header_id = '$po_header_id'
		";	
		
		mysql_query($query);
		$msg = "Transaction Finished";
	}
	$query="
		select
			*
		from
			po_header
		where
			po_header_id ='$po_header_id'
	";
	
	$result=mysql_query($query) or die(mysql_error());
	$r=mysql_fetch_assoc($result);
	
	$date					= $r['date'];
	$project_id 			= $r['project_id'];
	$budget_header_id		= $r['budget_header_id'];
	$work_category_id		= $r['work_category_id'];
	$sub_work_category_id	= $r['sub_work_category_id'];
	$supplier_id			= $r['supplier_id'];
	$terms					= $r['terms'];
	$remarks				= $r['remarks'];
	$expense_gchart_id		= $r['expense_gchart_id'];
	$ap_gchart_id			= $r['ap_gchart_id'];
	$discount_amount 		= $r['discount_amount'];
	
	
	$date			= ($r['date']!="0000-00-00")?$date:"";
	$project_name	= $options->attr_Project($project_id,'project_name');
	$supplier_name	= (!empty($supplier_id))?$options->getSupplierName($supplier_id):"";
	$work_category		= $options->attr_workcategory($work_category_id,'work');
	$sub_work_category	= $options->attr_workcategory($sub_work_category_id,'work');
	

	
	#DO NOT REMOVE
	$po_header_id_pad	= str_pad($po_header_id,7,0,STR_PAD_LEFT);
	$status			= $r['status'];
	$user_id		= $r['user_id'];

?>

<?php
if( $status=="F" || $status == "C" ){
?>
<style type="text/css">
.cp_table tr td:nth-child(1),.cp_table  tr th:nth-child(1){
	display:none;	
}
.hide{
	display:none;	
}
</style>
<?php
}
?>
<form name="header_form" id="header_form" action="" method="post">
<div class="module_actions">	
    <div class='inline'>
        Supplier : <br />  
        <input type="text" class="textbox"  name="search_supplier" value="<?=$search_supplier?>"  onclick="this.select();"/>
    </div>   
	<div class='inline'>
        PO # : <br />  
        <input type="text" class="textbox"  name="keyword" value="<?=$keyword?>"  onclick="this.select();"/>
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
			  po_header as h, supplier as s
		where
			h.supplier_id = s.account_id
		and
			po_type = 'S'
		and
		po_header_id like '%$keyword%'	
	";
	
if(!empty($search_supplier)){
$sql.="
	and
		account like '%$search_supplier%'	
	";
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
	<?=$pager->renderFullNav("$view&b=Search&search_supplier=$search_supplier")?>
</div>
<table id="my_table" cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table">
<tr>				
    <th width="20"></th>
    <th width="20"><b>#</b></th>
    <th width="20"></th>
    <th><b>PO # :</b></th>
    <th><b>Date</b></th>
	<th><b>Project</b></th>
    <th><b>Supplier</b></th>
    <th><b>Status</b></th>
	<th><b>Close Status</b></th>
</tr>  
<?php								
while($r=mysql_fetch_assoc($rs)) {
	
	$closed = $r['closed'];
	$disabled = ($closed == "1") ? "disabled='disabled'" : "";
					
	
	echo '<tr>';
	echo '<td width="20"><a href="admin.php?view=906dc84edef3edc8b174&po_header_id='.$r['po_header_id'].'"><input type="button"'.$disabled.' value="Generate APV"></td></a>';
	echo '<td width="20">'.++$i.'</td>';
	echo '<td width="15"><a href="admin.php?view='.$view.'&po_header_id='.$r[po_header_id].'" title="Show Details"><img src="images/edit.gif" border="0"></a></td>';
	echo '<td>'.str_pad($r['po_header_id'],7,0,STR_PAD_LEFT).'</td>';
	echo '<td>'.date("F j, Y",strtotime($r['date'])).'</td>';	
	echo '<td>'.$options->getAttribute('projects','project_id',$r['project_id'],'project_name').'</td>';	
	echo '<td>'.$options->getSupplierName($r[supplier_id]).'</td>';	
	echo '<td>'.$options->getTransactionStatusName($r[status]).'</td>';	
	echo '<td>'.(($r['closed']) ? "<span style='font-weight:bold; color:#F00;'>CLOSED</span>" : "").'</td>';
	echo '</tr>';
}
?>
</table>
<div class="pagination">
	<?=$pager->renderFullNav("$view&b=Search&search_supplier=$search_supplier")?>
</div>
<?php
}else{
?>
<div class=form_layout>

	<?php if(!empty($msg)) echo '<div id="status_update" class="ui-state-highlight ui-corner-all" style="padding: 0px 0.7em; text-align:left;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right:.3em;"></span> '.$msg.'</p></div>'; ?>
	<div class="module_title"><img src='images/user_orange.png'><?=$transac->getMname($view)?></div>
    
    <div class="module_actions">
        <input type="hidden" name="po_header_id" id="po_header_id" value="<?=$po_header_id?>" />
        <input type="hidden" name="view" value="<?=$view?>" />
        <div id="messageError">
            <ul>
            </ul>
        </div>
    
        <div class='inline'>
            Date : <br />
            <input type="text" name="date" id="date" class="textbox3 datepicker" readonly="readonly" value="<?=$date;?>" title="Please Enter Date" />
        </div>    	
                
        <div class='inline'>
            Project : <br />  
            <input type="text" class="textbox" id="project_name" value="<?=$project_name?>" onclick="this.select();"  />
            <input type="hidden" name="project_id"  id="project_id" value="<?=$project_id?>" title="Please select Project" />
        </div>   
        
        <div class="inline">
            Work Category : <br />
            <?=$options->option_workcategory($work_category_id,'work_category_id','Select Work Category')?>
        </div>
        
        <div id="subworkcategory_div" style="display:none;" class="inline">
            Sub Work Category :
            <div id="subworkcategory">
                
            </div>
        </div>
		
        <div class="inline" id="div_terms">
            Budget #: <br />
            <input type="text" class="textbox3" name="budget_header_id" id="budget_header_id" value="<?=$budget_header_id?>" />
        </div>
		
        <br />
        <div class="inline" id="supplier_div">
            Supplier : <br />
            <input type="text" class="textbox" name="supplier_id_display" value="<?=$supplier_name?>" id="supplier_name" onclick="this.select();" />
            <input type="hidden" name="supplier_id" id="account_id" value="<?=$supplier_id?>" title="Please Select Supplier" />
        </div>
        
        <div class="inline" id="div_terms">
            Terms ( Days ): <br />
            <input type="text" class="textbox" name="terms" id="term" value="<?=$terms?>" onmouseover="Tip('Term in Days. e.g. 90 for 90 days');" />
        </div>
        
        <div class="inline">
            Discount Amount : <br />
            <input type="text" class="textbox" name="discount_amount" value="<?=$discount_amount?>" />
        </div>
        
        <div>
            Remarks : <br />
            <textarea class="textarea_small" name='remarks'><?=$remarks?></textarea>
        </div>   
        
         <br />
         
        <!--<div class="inline">
            Expense Account : <br />
            <?=$options->option_chart_of_accounts($expense_gchart_id,'expense_gchart_id')?>
        </div>
        
        <div class="inline">
            A/P Account : <br />
            <?=$options->option_chart_of_accounts($ap_gchart_id,'ap_gchart_id')?>
        </div> -->
        
        <?php if(!empty($status)){ ?>
        <br />
        <div class='inline'>
            <div>PO # : </div>        
            <div>
                <input type="text" readonly="readonly" value="<?=$po_header_id_pad?>" class="textbox3" />
            </div>
        </div>  
        
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
        <?php } ?>
    </div>
    <div class="module_actions">
		<?php if($status=="S"){ ?>
        <input type="submit" name="b" id="b" value="Update" />
        <input type="submit" name="b" id="b" value="Finish" />
        <?php }else if($status!="F" && $status!="C"){ ?>
        <input type="submit" name="b" id="b" value="Submit" />
        <?php } ?>
        <?php if($b!="Print Preview" && !empty($status)){ ?>
            <input type="submit" name="b" id="b" value="Print Preview" />
        <?php } ?>
        <?php if($b=="Print Preview"){ ?>	
            <input type="button" value="Print" onclick="printIframe('JOframe');" />
        <?php } ?>
        <?php if($status!="C" && !empty($status)){ ?>
        <input type="submit" name="b" id="b" value="Cancel" />
		<?php
			if($status=="F"){
		?>
        <input type="submit" name="b" id="b" value="Unfinish" onclick="return approve_confirm();" />
			<?php } ?>
		<?php } ?>
        <a href="admin.php?view=<?=$view?>"><input type="button" value="New" /></a>
   	</div>
    <?php
	if( $status == 'S'):	
	?>
    <div class="module_actions">
    	<div class="inline">
        	DESCRIPTIONS : <br /> 
            <input type="text" class="textbox" name="description"  />
        </div>  
        
        <input type="submit" name="b" value="Add"  />
    </div>
    
    
    <?php endif ?>
</div>
<?php
if($b == "Print Preview" && $po_header_id){

	echo "	<iframe id='JOframe' name='JOframe' frameborder='0' src='print_report_spo.php?id=$po_header_id' width='100%' height='500'>
			</iframe>";
}else{
?>
<div class="clearfix">
	<table cellpadding="3"  class="cp_table" style="width:100%;">
    <tr>
    	<th width="20"></th>
        <th width="20"></th>
    	<th colspan="2">DESCRIPTION</th>
        <th>CHARGABLES</th>
        <th>PERSON</th>
        <th style="text-align:right;">QTY</th>
        <th>UNIT</th>
        <th style="text-align:right;">UNIT COST</th>
        <th style="text-align:right;">AMOUNT</th>
        <!--<th>DEDUCT TO <br /> MAT. BUDGET</th>
        <th>DEDUCT TO <br /> SERV. BUDGET</th> -->
    </tr>
	<?php
    $result = mysql_query("
		select
			*
		from
			po_header as h, spo_detail as d
		where
			h.po_header_id = d.po_header_id
		and
			h.po_header_id = '$po_header_id'		
	") or die(mysql_error());
	while($r = mysql_fetch_assoc($result)){
		$spo_detail_id 	= $r['spo_detail_id'];
		$description	= $r['description'];
	?>		
   	<tr>
    	<td align="center"><img src="images/add.png" class="hide" style="cursor:pointer;" onclick="xajax_spo_form('<?=$spo_detail_id?>');"  /></td>
    	<td align="center"><a class="hide" href="admin.php?view=<?=$view?>&po_header_id=<?=$po_header_id?>&b=DD&id=<?=$spo_detail_id?>" onclick="return approve_confirm();"><img src="images/trash.gif"  /></a></td>
   		<td colspan="6" style="font-weight:bold;" ><?=$description?></td>
   	</tr>
    <?php
	$rs = mysql_query("
		select * from spo_detail as h, sub_spo_detail as d where h.spo_detail_id = d.spo_detail_id and h.spo_detail_id = '$spo_detail_id'
	") or die(mysql_error());
	while($s = mysql_fetch_assoc($rs)){
	?>
    
    <?php
	$q_mat = "
		select
			stock,
			d.stock_id
		from
			budget_header as h,
			budget_detail as d,
			productmaster as p
		where
			h.budget_header_id = d.budget_header_id
		and
			d.stock_id = p.stock_id
		and
			h.work_category_id = '$work_category_id'
		and
			h.sub_work_category_id = '$sub_work_category_id'
		and
			h.status != 'C'
		and
			project_id = '$project_id'
		group by
			d.stock_id
		order by stock asc
	";
	
	$q_service = "
		select
			stock,
			d.stock_id
		from
			budget_header as h,
			budget_service_detail as d,
			productmaster as p
		where
			h.budget_header_id = d.budget_header_id
		and
			d.stock_id = p.stock_id
		and
			h.work_category_id = '$work_category_id'
		and
			h.sub_work_category_id = '$sub_work_category_id'
		and
			h.status != 'C'
		and
			project_id = '$project_id'
		group by
			d.stock_id
		order by stock asc
	";
	
	$material_select = $options->getTableAssoc('','mat_stock_id[]','Select From Budget',$q_mat,'stock_id','stock');
	$service_select  = $options->getTableAssoc('','service_stock_id[]','Select From Budget',$q_service,'stock_id','stock');
	
    ?>
    <tr>
    	<td>&nbsp;</td>
        <td>&nbsp;</td>
    	<td align="center" style="width:5%;"><a class="hide"  href="admin.php?view=<?=$view?>&po_header_id=<?=$po_header_id?>&b=DS&id=<?=$s['sub_spo_detail_id']?>" onclick="return approve_confirm();"><img src="images/trash.gif"  /></a></td>
   		<td><?=$s['sub_description']?></td>
        <td><?=$s['chargables']?></td>
        <td><?=$s['person']?></td>
        <td style="width:5%; text-align:right;"><?=$s['quantity']?></td>
        <td style="width:5%;"><?=$s['unit']?></td>
        <td style="width:5%; text-align:right"><?=$s['unit_cost']?></td>
        <td style="width:5%; text-align:right;"><?=$s['amount']?></td>
       <!-- <td style="text-align:center;"><?=$material_select?></td>
        <td style="text-align:center;"><?=$service_select?></td> -->
   	</tr>
    <?php } ?>
	<?php } ?>
	</table>		
</div>
<?php } ?>
<?php } ?>
</form>
<script type="text/javascript">
j(function(){	
	j("#work_category_id").change(function(){
		xajax_display_subworkcategory(this.value);
	});
	
	<?php if(!empty($status)){ ?>
		xajax_display_subworkcategory('<?=$work_category_id?>','<?=$sub_work_category_id?>');
	<?php } ?>
});
</script>
	