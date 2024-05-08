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
	$bd_options = new bd_options();
	#DO NOT REMOVE
	$b					= $_REQUEST['b'];
	$user_id			= $_SESSION['userID'];

	#PO #
	$keyword			= $_REQUEST['keyword'];
	#SEARCH FILTERS
	$search_supplier		= $_REQUEST['search_supplier'];
	
	#HEADER
	$pr_header_id			= $_REQUEST['pr_header_id'];
	$po_header_id			= $_REQUEST['po_header_id'];
	$date					= $_REQUEST['date'];
	$supplier_id			= $_REQUEST['supplier_id'];
	$terms					= $_REQUEST['terms'];
	$remarks_				= $_REQUEST['remarks_'];
	$discount_amount		= $_REQUEST['discount_amount'];
	$spo_id 				= $_REQUEST['spo_id'];
	
	
	#DETAILS
	$description 			= $_REQUEST['description'];
	
	#SUB DETAILS
	$sub_description		= $_REQUEST['sub_description'];
	$unit					= $_REQUEST['unit'];
	$unit_cost				= $_REQUEST['unit_cost'];
	$amount					= $_REQUEST['amount'];
	$person					= $_REQUEST['person'];
	$chargables				= $_REQUEST['chargables'];
	
	$pr_lb_id = $_REQUEST['pr_lb_id'];
	
	$id	= $_REQUEST['id'];
	
	if($b == "Unfinish"){
		mysql_query("update po_header set status = 'S' where po_header_id = '$po_header_id'") or die(mysql_error());
		
		mysql_query("delete from po_header_payroll_det where po_header_id ='$po_header_id'") or die(mysql_error());
		
		/*$query="
			update
				po_header
			set
				status='C'
			where
				po_header_id = '$po_header_id'
		";	
		mysql_query($query);*/
		$r=mysql_query("select * from po_header where po_header_id='$po_header_id'");
		$o=mysql_fetch_assoc($r);
		//update pr status for is_used
		mysql_query("update pr_header set is_used='0' where pr_header_id='".$o[pr_header_id]."'");		
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
		//$pr = $pr_header_id;
		$get = "
				select 
					*,l.project_id as p,l.work_category_id as w,l.sub_work_category_id as sub
				from
					pr_header as ph,
					labor_budget as l,
					labor_budget_details as lb,
					labor_budget_pr as lbr
				where
					ph.pr_header_id='$_REQUEST[pr_header_id]'
				and
					lbr.pr_header_id='$_REQUEST[pr_header_id]'
				and
				    lb.id =	lbr.labor_budget_details_id
				and
					l.id=lb.labor_budget_id
				and
					l.status !='C'
				and
					l.is_deleted !='1'
				and
					lb.is_deleted !='1'
				and
					lbr.is_deleted !='1'
				";
		$get2 = mysql_query($get);
		$f = mysql_fetch_assoc($get2);
		extract($f);
		
		$query="
			insert into 
				po_header
			set
				date = '$_REQUEST[date]',
				project_id = '$p',
				pr_header_id = '$_REQUEST[pr_header_id]',
				work_category_id = '$w',
				sub_work_category_id = '$sub',
				supplier_id = '$supplier_id',
				terms = '$terms',
				remarks = '$remarks_',
				po_type = 'L',
				status = 'S',
				user_id = '$user_id',
				approval_status = 'P',
				discount_amount = '$discount_amount'
		";	
		mysql_query($query) or die(mysql_error());
		
		$po_header_id = mysql_insert_id();
		mysql_query("
			insert into
				spo_detail
			set
				po_header_id		= '$po_header_id',
				description			= '$description'
		") or die(mysql_error());
		
		$spo_d_id = mysql_insert_id();
				
				$sl = "
						select
							*
						from
							labor_budget_pr as l,
							labor_budget_details as lb,
							work_type as w
						where
							l.pr_header_id = '$_REQUEST[pr_header_id]'
						and
							l.is_deleted !='1'
						and
							l.labor_budget_details_id = lb.id
						and
							lb.work_code_id = w.work_code_id
					";
				$lw = mysql_query($sl);
		
		while($lp = mysql_fetch_assoc($lw)){
			$desc = $lp['description'];
			$pr_lb_id=$lp['pr_lb_id'];
			$re = $lp['total_req_qty'];
			$unit = $lp['unit'];
						if($lp['tag']==1){
							$wt_price_per_unit				= $lp['wt_price_per_unit'];
						}else{
							$wt_price_per_unit				= $lp['price_per_unit'];
						}
			$total_t = $wt_price_per_unit*$re;
			mysql_query("
				insert into
					sub_spo_detail
				set
					spo_detail_id		= '$spo_d_id',
					sub_description			= '$desc',
					quantity = '$re',
					unit	= '$unit',
					unit_cost = '$wt_price_per_unit',
					amount = '$total_t',
					pr_lb_id = '$pr_lb_id',
					person = '$lp[requested_no_per]'
			") or die(mysql_error());	
		}
		$msg="Transaction Saved";
		
	}else if($b=="Update"){
		$get = "
				select 
					*
				from
					pr_header as ph,
					labor_budget as l,
					labor_budget_details as lb,
					labor_budget_pr as lbr
				where
					ph.pr_header_id='$_REQUEST[pr_header_id]'
				and
					lbr.pr_header_id = ph.pr_header_id
				and
					lbr.labor_budget_details_id = lb.id
				and
					lb.labor_budget_id = l.id
				";
		$get2 = mysql_query($get);
		$f = mysql_fetch_assoc($get2);
		extract($f);
		
		$query="
				update
					po_header
				set
					date = '$_REQUEST[date]',
					project_id = '$project_id',
					pr_header_id = '$_REQUEST[pr_header_id]',
					work_category_id = '$work_category_id',
					sub_work_category_id = '$sub_work_category_id',
					supplier_id = '$supplier_id',
					terms = '$terms',
					remarks = '$remarks_',
					po_type = 'L',
					status = 'S',
					user_id = '$user_id',
					approval_status = 'P',
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
		
		mysql_query("delete from po_header_payroll_det where po_header_id ='$po_header_id'") or die(mysql_error());
		
		mysql_query($query);
		$r=mysql_query("select * from po_header where po_header_id='$po_header_id'");
		$o=mysql_fetch_assoc($r);
		//update pr status for is_used
		mysql_query("update pr_header set is_used='0' where pr_header_id='".$o[pr_header_id]."'");
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
		$r=mysql_query("select * from po_header where po_header_id='$po_header_id'");
		$o=mysql_fetch_assoc($r);
		//update pr status for is_used
		mysql_query("update pr_header set is_used='1' where pr_header_id='".$o[pr_header_id]."'");
	
		#SET PURCHASE REQUEST INTO F
			mysql_query("
						update
							pr_header as ph,po_header as po
						set
							ph.status='F'
						where
							po.po_header_id='$po_header_id'
						and
							po.pr_header_id=ph.pr_header_id
							");
		
		$msg = "Transaction Finished";
	}
	$query1="
		select
			*
		from
			po_header
		where
			po_header_id ='$po_header_id'
		and
			po_type = 'L'
	";
	
	$result1=mysql_query($query1) or die(mysql_error());
	$rr=mysql_fetch_assoc($result1);
	
	//get spo_id
	$query2 ="select * from spo_detail where po_header_id='$po_header_id'";
	$q = mysql_query($query2);
	$m = mysql_fetch_assoc($q);
	
	$spo_id 				= $m['spo_detail_id'];
	$supplier_id			= $rr['supplier_id'];
	$terms					= $rr['terms'];
	$remarks				= $rr['remarks'];
	$discount_amount 		= $rr['discount_amount'];
	$pr_header_id 			= $rr['pr_header_id'];
	
	$date			= ($_REQUEST['date']!="0000-00-00")?$_REQUEST['date']:"";
	$project_name	= $options->attr_Project($project_id,'project_name');
	$supplier_name	= (!empty($supplier_id))?$options->getSupplierName($supplier_id):"";
	$work_category		= $options->attr_workcategory($work_category_id,'work');
	$sub_work_category	= $options->attr_workcategory($sub_work_category_id,'work');
	//$prdesc 			= (!empty($pr_header_id))?$bd_options->getprdesc($pr_header_id):"";

	
	#DO NOT REMOVE
	$po_header_id_pad	= str_pad($po_header_id,7,0,STR_PAD_LEFT);
	$status			= $rr['status'];
	$user_id		= $rr['user_id'];

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
        <div class='inline'>PO # : <br />  
        <input type="text" class="textbox3"  name="keyword" value="<?=$keyword?>"/>
		</div>
		<div class='inline'>Supplier: <br />
		<input type="text" class="textbox3"  name="search_supplier" value="<?=$search_supplier?>"/></div>
    <input type="submit" name="b" value="Search" />
 	<a href="admin.php?view=<?=$view?>">
	<input type="button" value="New" /></a>
	<input type="submit" name="b" value="Release Payroll" />
</div>

<?php
if($b == "Search"){
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
			h.po_type = 'L'
	";
	
if(!empty($search_supplier)){
$sql.="
	and
		s.account like '%$search_supplier%'	
	";
}else if(!empty($keyword)){
	$sql.="
			and
			h.po_header_id like '%$keyword%'
			";
}
$sql.="
		order 
			by h.date desc
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
    <th>PO # :</th>
    <th><b>Date</b></th>
	<th><b>Project</b></th>
    <th><b>Supplier</b></th>
	<th>Work Category</th>
    <th>Sub Work Category</th>
    <th><b>Status</b></th>
</tr>  
<?php								
while($r=mysql_fetch_assoc($rs)) {
	
	echo '<tr>';
	echo '<td width="20"><a href="admin.php?view=906dc84edef3edc8b174&po_header_id='.$r['po_header_id'].'"><input type="button" value="Generate APV"></td></a>';
	echo '<td width="20">'.++$i.'</td>';
	echo '<td width="15"><a href="admin.php?view='.$view.'&po_header_id='.$r[po_header_id].'&b=s" title="Show Details"><img src="images/edit.gif" border="0"></a></td>';
	echo '<td>'.str_pad($r['po_header_id'],7,0,STR_PAD_LEFT).'</td>';
	echo '<td>'.date("F j, Y",strtotime($r['date'])).'</td>';	
	echo '<td>'.$options->getAttribute('projects','project_id',$r['project_id'],'project_name').'</td>';	
	echo '<td>'.$options->getSupplierName($r[supplier_id]).'</td>';
	echo '<td>'.$options->getAttribute('work_category','work_category_id',$r[work_category_id],'work').'</td>';
	echo '<td>'.$options->getAttribute('work_category','work_category_id',$r[sub_work_category_id],'work').'</td>';		
	echo '<td>'.$options->getTransactionStatusName($r[status]).'</td>';
	echo '</tr>';
}
?>
</table>
<div class="pagination">
	<?=$pager->renderFullNav("$view&b=Search&search_supplier=$search_supplier")?>
</div>
<?php
}else if($b=="Release Payroll"){
$page = $_REQUEST['page'];
if(empty($page)) $page = 1;
 
$limitvalue = $page * $limit - ($limit);

$sql = "select
			  *
		from
			po_header as h
		where
			(h.po_type = 'L' or h.po_type = 'S')
		and
			h.payroll_header_id != '0'
		and
			h.status = 'F'
	";
if(!empty($keyword)){
	$sql.="and
			h.po_header_id like '%$keyword%'";
}
$sql.="
		order 
			by date desc
	";

$pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
		
$i=$limitvalue;
$rs = $pager->paginate();
if($b!='Release Payroll'){
	$b = 'Search';
}
?>
<div class="pagination">
	<?=$pager->renderFullNav("$view&b=$b&search_supplier=$search_supplier")?>
</div>
<table id="my_table" cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table">
<tr>				
    <th width="20"><b>#</b></th>
    <th width="20"></th>
	<th width="20"></th>
    <th>PO # :</th>
    <th><b>Date</b></th>
	<th><b>Project</b></th>
    <th><b>Supplier</b></th>
	<th>Work Category</th>
    <th>Sub Work Category</th>
    <th><b>Status</b></th>
</tr>  
<?php								
while($r=mysql_fetch_assoc($rs)) {
	//echo $r[payroll_header_id].'<br/>';
	echo '<tr>';
	echo '<td width="20">'.++$i.'</td>';
	echo '<td width="15"><a href="admin.php?view='.$view.'&po_header_id='.$r[po_header_id].'&b=s" title="Show Details"><img src="images/edit.gif" border="0"></a></td>';
	//if($r[payroll_det]=='0.0000'){
		echo '<td width="15"><a href="javascript:void(0);" onclick="xajax_add_payroll('.$r[po_header_id].','.$r[payroll_header_id].')" title="Add Payroll"><img src="images/money.png" border="0"></a></td>';
	//}else{
	//	echo '<td width="20"></td>';
	//}
	echo '<td>'.str_pad($r['po_header_id'],7,0,STR_PAD_LEFT).'</td>';
	echo '<td>'.date("F j, Y",strtotime($r['date'])).'</td>';	
	echo '<td>'.$options->getAttribute('projects','project_id',$r['project_id'],'project_name').'</td>';	
	echo '<td>'.$options->getSupplierName($r[supplier_id]).'</td>';	
	echo '<td>'.$options->getAttribute('work_category','work_category_id',$r[work_category_id],'work').'</td>';
	echo '<td>'.$options->getAttribute('work_category','work_category_id',$r[sub_work_category_id],'work').'</td>';
	echo '<td>'.$options->getTransactionStatusName($r[status]).'</td>';		
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
		<input type="hidden" name="spo_id" id="spo_id" value="<?=$spo_id?>" />
        <input type="hidden" name="view" value="<?=$view?>" />
        <div id="messageError">
            <ul>
            </ul>
        </div>
    
        <div class='inline'>
            Date : <br />
            <input type="text" name="date" id="date" class="textbox3 datepicker" readonly="readonly" value="<?=$rr['date'];?>" title="Please Enter Date" />
        </div>    	
        
		  <?php if(empty($b)) {?>
        <div class="inline">
            Purchase Request:<br/>
                <?=$bd_options->option_pr2('pr_header_id','Select Purchase Request')?>
        </div>
        <?php }else{
				?>
			 <div class="inline">
				Purchase Request :<br/>
					<input type=text class=textbox3 readonly=readonly name=pr_header_id value="<?=$pr_header_id?>">
					<?php
						//$options->getAttribute('pr_header','pr_header_id',$pr_header_id,'pr_header_id');
					//$bd_options->option_pr($pr_header_id,'pr_header_id','Select Purchase Request')
					
					?>
			</div>
		<?php 
				
			} ?>
		
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
            <textarea class="textarea_small" name='remarks_'><?=$remarks?></textarea>
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
        <input type="submit" name="b" id="b" value="Unfinish" onclick="return approve_confirm();" />
        <?php } ?>
        <a href="admin.php?view=<?=$view?>"><input type="button" value="New" /></a>
   	</div>
</div>
<?php
if($b == "Print Preview" && $po_header_id){

	echo "	<iframe id='JOframe' name='JOframe' frameborder='0' src='print_report_spo.php?id=$po_header_id' width='100%' height='500'>
			</iframe>";
}else{
/* CHANGES STARTS HERE */
			$q=mysql_query("
							select
								*
							from
								pr_header as ph,labor_budget as l,
								labor_budget_details as lb,labor_budget_pr as lbp
							where
								ph.pr_header_id='$pr_header_id'
							and
								ph.project_id=l.project_id
							and
								ph.work_category_id=l.work_category_id
							and
								ph.sub_work_category_id=l.sub_work_category_id
							and
								ph.approval_status='A'
							and
								ph.type='labor'
							and
								l.id=lb.labor_budget_id
							and
								lb.id=lbp.labor_budget_details_id
							and
								l.is_deleted !='1'
							and
								lb.is_deleted !='1'
							and
								lbp.is_deleted !='1'
							and
								lbp.pr_header_id=ph.pr_header_id
								");
				
		/* CHANGES ENDS HERE */
?>
<div class="clearfix">
	<table cellpadding="3"  border="0" class="cp_table" style="width:100%;">
    <tr>
    	<th width="20">#</th>
        <th width="20"></th>
    	<th colspan="2">DESCRIPTION</th>
		<th width="20">TOTAL REQUESTED QTY</th>
        <th style="text-align:right;">UNIT</th>
        <th style="text-align:right;">PRICE/UNIT</th>
        <th style="text-align:right;">TOTAL AMOUNT</th>
        <!--<th>DEDUCT TO <br /> MAT. BUDGET</th>
        <th>DEDUCT TO <br /> SERV. BUDGET</th> -->
    </tr>
	<?php
    $result = mysql_query("
		select
			*
		from
			sub_spo_detail as ss, po_header as p, spo_detail as sd
		where
			sd.spo_detail_id = '$spo_id'
		and
			sd.spo_detail_id = ss.spo_detail_id
		and
			sd.po_header_id = p.po_header_id
	") or die(mysql_error());
	$i=1;
	while($r = mysql_fetch_assoc($result)){
		$pr_header_id 	= $r['pr_header_id'];
		$description	= $r['sub_description'];
		$qty 			= $r['quantity'];
		$person			= $r['person'];
		//$total_qty		= $person*$qty;
		
		$unit 			= $r['unit'];
		$unit_cost 		= $r['unit_cost'];
		$amount			= $r['amount'];
		$sub_spo_id		= $r['sub_spo_detail_id'];
    ?>
		
    <tr>
    	<td width="20"><?=$i++?></td>
    	<td align="center" width="20"><a class="hide"  href="admin.php?view=<?=$view?>&b=DS&id=<?=$sub_spo_id?>&po_header_id=<?=$po_header_id?>" onclick="return approve_confirm();"><img src="images/trash.gif"  /></a></td>
        <td colspan="2"><?=$description?></td>
		<td width="30"><?=$qty?></td>
        <td style="text-align:right;"><?=$unit?></td>
        <td style="text-align:right;"><?=number_format($unit_cost, 2)?></td>
        <td style="text-align:right;"><?=number_format($amount, 2)?></td>
			
   	</tr>
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
	