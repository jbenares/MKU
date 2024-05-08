<?php
function hasPayment($apv_header_id){
	$options = new options();
	#apv
	#check sum amount of apv
	$result = mysql_query("
		select 
			sum(amount) as amount
		from
			apv_detail 
		where
			apv_header_id = '$apv_header_id'
	") or die(mysql_error());
	$r = mysql_fetch_assoc($result);
	$sum_amount = $r['amount'];
	
	#discount amount
	$discount_amount = $options->getAttribute('apv_header','apv_header_id',$apv_header_id,'discount_amount');
	
	if($discount_amount >= $sum_amount){
		return true;	
	}
		
	$result = mysql_query("
		select
			*
		from
			cv_header as h, cv_detail as d
		where
			h.cv_header_id = d.cv_header_id
		and
			h.status != 'C'
		and
			d.apv_header_id = '$apv_header_id'
	") or die(mysql_error());
	
	if( mysql_num_rows($result) > 0 ){
		return true;	
	} else { 
		return false;
	}
}
?>

<?php

	$b 				= $_REQUEST['b'];
	$user_id		= $_SESSION['userID'];	
	$keyword 		= $_REQUEST['keyword'];
	$checkList 		= $_REQUEST['checkList'];
	
	$search_supplier	= $_REQUEST['search_supplier'];
	
	$apv			= $_REQUEST['apv'];
	$percent		= $_REQUEST['percent'];
	$cv_date		= $_REQUEST['cv_date'];
	$check_date		= $_REQUEST['check_date'];
	$check_no		= $_REQUEST['check_no'];
	$supplier_id	= $_REQUEST['supplier_id'];
	$cash_gchart_id	= $_REQUEST['cash_gchart_id'];
	$ap_gchart_id	= $_REQUEST['ap_gchart_id'];

	if($_SESSION[userID] == '20160719-110150' || $_SESSION[userID] == '20200311-050946' || $_SESSION[userID] == '20170830-120801' || $_SESSION[userID] == '20200319-055723'){
		$old_id_cv = $_REQUEST['old_id_cv'];
	}
	
	function getNextCV(){
	$result = mysql_query("
		select * from cv_header where status != 'C' order by cv_no desc
	") or die(mysql_error());
	$r = mysql_fetch_assoc($result);
	return ($r['cv_no'] + 1);
	}
	
	$cv_no = getNextCV();

	if($b == "Generate CV"){
		
			$sql = mysql_query("select * from cv_header where cv_header_id = '$old_id_cv'") or die(mysql_error());	
			$count = mysql_num_rows($sql);
			if($count > 0){
				$msg = "Error, CV already exist";				
			}else{
				
				if($old_id_cv > 0){
					
					mysql_query("
					insert into
					cv_header
					set
					cv_header_id = '$old_id_cv',
					percent = '$percent',
					cv_date = '$cv_date',
					check_date = '$check_date',
					check_no = '$check_no',
					cv_no = '$cv_no',
					supplier_id = '$supplier_id',
					cash_gchart_id = '$cash_gchart_id',
					ap_gchart_id = '$ap_gchart_id',
					user_id = '$user_id'
					") or die(mysql_error());	
		
					$cv_header_id = $old_id_cv;						
					
					
				}else{
					
					mysql_query("
					insert into
					cv_header
					set
					percent = '$percent',
					cv_date = '$cv_date',
					check_date = '$check_date',
					check_no = '$check_no',
					cv_no = '$cv_no',
					supplier_id = '$supplier_id',
					cash_gchart_id = '$cash_gchart_id',
					ap_gchart_id = '$ap_gchart_id',
					user_id = '$user_id'
					") or die(mysql_error());	
		
					$cv_header_id = mysql_insert_id();		
			
						
				}
				
				
				if($apv){
					
					foreach($apv as $apv_header_id){
						$sql = mysql_query("Select vatable, w_tax from apv_header where apv_header_id = '$apv_header_id' and status != 'C'") or die (mysql_error());
						$r = mysql_fetch_assoc($sql);
						
						$vatable 	= $r['vatable'];
						$w_tax 		= $r['w_tax'];
						
						//922 - creditable wv (12%)
						//924 - ewt 1%
						//928 - ewt 2%
						$vat_gchart = 922;
						
						if($vatable == 1){
							mysql_query("Update cv_header set vat = '12', vat_gchart_id = '$vat_gchart' where cv_header_id = '$cv_header_id'") or die (mysql_error());
						}
						
						if($w_tax == 1){
							$wtax = 924;
						}else if($w_tax == 2){
							$wtax = 928;
						}else{
							$wtax = 0;
						}
						
						mysql_query("Update cv_header set wtax_gchart_id = '$wtax', wtax = '$w_tax' where cv_header_id = '$cv_header_id'") or die (mysql_error());
										
						$amount = $options->computeAPV($apv_header_id,$percent);
						mysql_query("
							insert into
								cv_detail
							set
								cv_header_id = '$cv_header_id',
								apv_header_id = '$apv_header_id',
								amount = '$amount'
						") or die(mysql_error());	
					}	
				}
				
			}
		
		
		
		echo "<script type=\"text/javascript\">
				window.open('admin.php?view=9d825239df14c9830e3b&cv_header_id=".$cv_header_id."', '_blank')
			  </script>";
		
		//header("Location:admin.php?view=9d825239df14c9830e3b&cv_header_id=$cv_header_id");
	}
	
	if($b == "Generate AP Voucher"){
		if(!empty($checkList)){
			mysql_query("
				insert into
					apv_header
				set
					date = '".date("Y-m-d")."',
					supplier_id = '$ap_supplier'
			") or die(mysql_error());
			
			$apv_header_id = mysql_insert_id();			
			
			foreach($checkList as $ap_id){
				mysql_query("
					insert into
						apv_detail
					set
						apv_header_id = '$apv_header_id',
						ap_id = '$ap_id'
				");
			}
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

<form name="_form" id="_form" action="" method="post">
<div class=form_layout>
	<div class="module_title"><img src='images/user_orange.png'><?=$transac->getMname($view);?></div>
	<!--<div style="padding:3px; font-weight:bold; color:#FFF; background-color:#000; font-size:14px;">
			APV Report
	</div>
	<div style="background-color:#FFFFCC; border:1px solid #000; margin-top:0px; padding:10px;">		
		<div class="inline">
			From Date <br>
			<input type="text" class="textbox3 datepicker" name="report_from_date" id="report_from_date" value="<?=$aVal['report_from_date']?>">
		</div>

		<div class="inline">
			To Date <br>
			<input type="text" class="textbox3 datepicker" name="report_to_date" id="report_to_date" value="<?=$aVal['report_to_date']?>">
		</div>

		<div class="inline">
			<?php
			$arr_payment_status = array(
				"A" => "All",
				"P" => "Paid",
				"U" => "Unpaid"
			);
			echo lib::getArraySelect(NULL,'report_payment_status',"Select Payment Status", $arr_payment_status);
			?>
		</div>

		<input type="button" value="Print APV Report" onclick="openinnewTab();" />
	</div>-->

    <div class="module_actions">       
        <div style="display:inline-block;">
			<div class="inline">
                Supplier : <br />
                <input type="text" class="textbox" name="search_supplier" value="<?=$search_supplier?>" />
            </div>
            
			<div class="inline">
           		APV # : <br />
                <input type="text" class="textbox" name="search_apv_header_id"  value="<?=$_REQUEST['search_apv_header_id']?>"  />
            </div>
            
            <div class="inline">
           		PO # : <br />
                <input type="text" class="textbox" name="search_po_header_id"  value="<?=$_REQUEST['search_po_header_id']?>"  />
            </div>
			
			<?php if($_SESSION[userID] == '20160719-110150' || $_SESSION[userID] == '20200311-050946' || $_SESSION[userID] == '20170830-120801' || $_SESSION[userID] == '20200319-055723'){ ?>
		
			<div>
				CV Header ID : <br />
				<input type="text" class="textbox" name="old_id_cv" />
			</div>
			<?php } ?>
            <input type="submit" name="b" value="Search" />                        
            <input type="button"  value="Generate Check Voucher" onclick="j('#_dialog').dialog('open');" />                        
        </div>
        <input type="hidden" name='view' value="<?=$view?>" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;" id="content">
        <table id="my_table" cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table">
            <?php
                $page = $_REQUEST['page'];
                if(empty($page)) $page = 1;
                 
                $limitvalue = $page * $limit - ($limit);
            
                $sql = "
                    select
						apv_header_id,
						po_header_id,
						date,
						po_date,
						project_id,
						work_category_id,
						sub_work_category_id,
						supplier_id,
						terms,
						status,
						user_id
                    from
                        apv_header as h, supplier as s
                    where
                        h.supplier_id = s.account_id
                    and
                        s.account like '$search_supplier%'
					and
						h.status !='C'
                    
                ";
				
				if($_REQUEST['search_apv_header_id']){
				$sql.="
					and	
						h.apv_header_id = '".$_REQUEST['search_apv_header_id']."'
				";	
				}
				
				if($_REQUEST['search_po_header_id']){
				$sql.="
					and	
						h.po_header_id = '".$_REQUEST['search_po_header_id']."'
				";	
				}
				$sql.="
					order by	
                        apv_header_id desc			
				";
                    
                $pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
                        
                $i=$limitvalue;
                $rs = $pager->paginate();
            ?>
            <tr>				
                <th width="20">#</th>
                <th width="20" align="center"></th>
                <th width="20"></th>
                <th>APV #</th>
                <th>PO #</th>
                <th>DATE</th>
                <th>SUPPLIER</th>  
                <th>TERMS</th>       
                <th>DUE DATE</th>
                <th>PROJECT</th>
                <th>SCOPE OF WORK</th>
                <th>STATUS</th>
                <th>PREPARED BY</th>
                <th width="20">PAYMENT STATUS</th>
            </tr>  
            <?php								
                while($r=mysql_fetch_assoc($rs)) {
                        $apv_header_id			= $r['apv_header_id'];
                        $apv_header_id_pad		= str_pad($apv_header_id,7,0,STR_PAD_LEFT);
                        $po_header_id			= $r['po_header_id'];
                        $po_header_id_pad		= str_pad($po_header_id,7,0,STR_PAD_LEFT);
                        $date 					= $r['date'];
                        $po_date				= $r['po_date'];
                        $project_id 			= $r['project_id'];
                        $project				= $options->getAttribute('projects','project_id',$project_id,'project_name');
                        $work_category_id 		= $r['work_category_id'];
                        $work_category			= $options->getAttribute('work_category','work_category_id',$work_category_id,'work');
                        $sub_work_category_id 	= $r['sub_work_category_id'];
                        $sub_work_category		= $options->getAttribute('work_category','work_category_id',$sub_work_category_id,'work');
                        $supplier_id 			= $r['supplier_id'];
                        $supplier				= $options->getAttribute('supplier','account_id',$supplier_id,'account');
                        $terms 					= $r['terms'];
                        $status					= $r['status'];
                        $user_id				= $r['user_id'];
                        $due_date				= $r['due_date'];

                        $hasPayment = hasPayment($apv_header_id);
    
            ?>
                <tr>
                    <td width="20"><?=++$i?></td>
                    <td><input type="checkbox" name="apv[]" value="<?=$apv_header_id?>" /></td>
                    <td><a href="admin.php?view=687b880d1beb02fa41b1&apv_header_id=<?=$apv_header_id?>" ><img src="images/edit.gif" style="cursor:pointer;"/></a></td>
                    <td><?=$apv_header_id_pad?></td>	
                    <td><?=$po_header_id_pad?></td>
                    <td><?=date("m/d/Y", strtotime($date))?></td>		
                    <td><?=$supplier?></td>	
                    <td><?=$terms?></td>
                    <td><?=$due_date?></td>
                    <td><?=$project?></td>
                    <td><?=$work_category?> <?=$sub_work_category?></td> 
                    <td><?=$options->getTransactionStatusName($status)?></td>	
                    <td><?=$options->getUserName($user_id)?></td>
                    <td <?=($hasPayment) ? "style='color:#0F0;'" : "style='color:#F00;'" ?>><?=($hasPayment) ? "PAID" : "UNPAID" ?></td>
                </tr>
            <?php
                }
            ?>
            </table>
        <table cellspacing="2" cellpadding="5" width="100%" class="search_table">
            <tr>
                <td colspan="5" align="left">
                    <?php
                        echo $pager->renderFullNav("$view&search_supplier=$search_supplier&search_apv_header_id=$_REQUEST[search_apv_header_id]&search_po_header_id=$_REQUEST[search_po_header_id]");
                    ?>                
                </td>
            </tr>
        </table>
    </div>
</div>
<div id="_dialog">
<!--<form name="_form2" id="_form2" action="" method="post"> -->
    <div id="_dialog_content">
    	<div style="margin-bottom:5px;">
            Supplier :<br />
            <?php
            $query = "select * from supplier order by account asc ";
            echo $options->getOptions('supplier_id','Select Supplier',$query,'account_id','account',$supplier_id);
            ?>
       	</div>
        
        <div style="margin-bottom:5px;">
	   		Percent (%): <br />
	    	<input type="text" class="textbox" name="percent" value="100" />
        </div>
        
        <div style="margin-bottom:5px;">
	   		Voucher Date : <br />
	    	<input type="text" class="textbox datepicker" name="cv_date" />
        </div>
        
        <div style="margin-bottom:5px;">
	   		Check No. : <br />
	    	<input type="text" class="textbox" name="check_no" />
        </div>
        
        <div style="margin-bottom:5px;">
	   		Check Date : <br />
	    	<input type="text" class="textbox datepicker" name="check_date" />
        </div>
        
        <div style="margin-bottom:5px;">
	   		Cash/PDC Account : <br />
	    	<?=$options->option_chart_of_accounts($cash_gchart_id,'cash_gchart_id')?>
        </div>
        
        <div style="margin-bottom:5px;">
	   		A/P Account : <br />
	    	<?=$options->option_chart_of_accounts($cash_gchart_id,'ap_gchart_id')?>
        </div>
        
        <input type="submit" name="b" value="Generate CV"  />
    </div>
<!--</form> -->
</div>
</form>

<script type="text/javascript">
	j(function(){
		var dlg = j("#_dialog").dialog({autoOpen: false , modal:true , show: 'slide' , hide : 'slide' , width : 'auto', resizable : false, height : 'auto', maxHeight : 600, title : "Check Voucher Details"});
		dlg.parent().appendTo(jQuery("form:first"));
	});
</script>
<script type="text/javascript">
   function openinnewTab() {

			var report_from_date      = jQuery("#report_from_date").val();
			var report_to_date        = jQuery("#report_to_date").val();
			var report_payment_status = jQuery("#report_payment_status").val();

    	var win = window.open("transactions/print_apv_report.php?from_date=" + report_from_date + "&to_date=" + report_to_date + "&payment_status=" + report_payment_status , '_blank');
       	win.focus();
   }
</script>