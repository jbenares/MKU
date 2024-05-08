<?php
if($_REQUEST['ajax']){
	require_once(dirname(__FILE__).'/../conf/ucs.conf.php');

	mysql_query("
		update cv_header set printed = '$_REQUEST[print_status]' where cv_header_id = '$_REQUEST[cv_header_id]'
	") or die(mysql_error());

	$print_status = ($_REQUEST['print_status']) ? "Printed" : "Pending";
	echo "Updated print status of CV to $print_status";
	exit();
}

function getCheckStatusAmount($cleared){
	#1 - cleared
	#0 - uncleared
	$result = mysql_query("
		select 
			cv_header_id, cash_amount
		from
			cv_header as h, supplier as s
		where
			h.supplier_id = s.account_id
		and
			status != 'C'
		and
			cleared = '$cleared'
	") or die(mysql_error());	
	$total_cash_amount = 0;
	while($r = mysql_fetch_assoc($result)){
		$total_cash_amount += $r['cash_amount'];
	}
	return $total_cash_amount;
}
?>
<?php

$b 						= $_REQUEST['b'];
$user_id				= $_SESSION['userID'];	
$keyword 				= $_REQUEST['keyword'];
$checkList 				= $_REQUEST['checkList'];
$list					= $_REQUEST['list'];
$search_check_amount	= $_REQUEST['search_check_amount'];
$search_check_no		= $_REQUEST['search_check_no'];
$search_cv_no			= $_REQUEST['search_cv_no'];
$search_supplier		= $_REQUEST['search_supplier'];
$chk_status				= $_REQUEST['chk_status'];
$project_name			= $_REQUEST['project_name'];
$project_id				= $_REQUEST['project_id'];
$search_sub_apv_no		= $_REQUEST['search_sub_apv_no'];


$id = $_REQUEST['id'];

if($b == "Finish Selected"){
	$ids = $_REQUEST['ids'];	
	$status	= $_REQUEST['status'];
	
	$x = 0;
	
	#print_r($status);
	#echo "<br>";
	#print_r($ids);
	foreach($status as $s){
		if($s == "F"){
			#echo $ids[$x] . "<br>";
			
			$_cv_header_id = $ids[$x];
			$query="
				update
					cv_header
				set
					status='F'
				where
					cv_header_id='$_cv_header_id'
			";	
			mysql_query($query) or die(mysql_error());
			$options->insertAudit($_cv_header_id,'cv_header_id','F');
			
			
			$gltran_header_id  = $options->postCV($_cv_header_id);
		}
		$x++;		
	}
	$msg = "CVs Finished";
}


if($b == "Clear Checked Checks"){	
	if(count($list) > 0){
		foreach($list as $id){
			$date_cleared = (!empty($_REQUEST['date_cleared'])) ? $_REQUEST['date_cleared'] : $_REQUEST['date_'.$id];
			#echo "update cv_header set cleared = '1', date_cleared = '$date_cleared' where cv_header_id = '$id' <br>";
			mysql_query("
				update cv_header set cleared = '1', date_cleared = '$date_cleared' where cv_header_id = '$id'
			") or die(mysql_error());
		}
	}
}
	
?>
<script type="text/javascript">
	jQuery(function(){
		jQuery(".check").click(function(){
			xajax_displayTotalCheckAmount(xajax.getFormValues("_form"));
		});
	});
</script>	
<form name="_form" id="_form" action="" method="post">
<div class=form_layout>
	<div class="module_title"><img src='images/user_orange.png'><?=$transac->getMname($view);?></div>
    <div class="module_actions">       
	    <input type="hidden" name='view' value="<?=$view?>" />
       
        
        <table style="display:inline-table;">
        	<tr>
            	<td>PROJECT</td>
                <td>
                	<input type="text" class="textbox project" name="project_name" value="<?=$project_name?>" />
            		<input type="hidden" name="project_id" value="<?=($project_name) ? $project_id : "" ?>"  />
                </td>
            </tr>
        	<tr>
            	<td>SUPPLIER</td>
                <td><input type="text" class="textbox" name="search_supplier" value="<?=$search_supplier?>" /></td>
            </tr>
            <tr>
            	<td>SEARCH CHECK AMOUNT</td>
                <td><input type="text" class="textbox" name="search_check_amount" value="<?=$search_check_amount?>" /></td>
            </tr>
            <tr>
            	<td>SEARCH CHECK NO.</td>
                <td><input type="text" class="textbox" name="search_check_no" value="<?=$search_check_no?>" /></td>
            </tr>

        </table>
                
        <table style="display:inline-table;">
        	<tr>
            	<td>SEARCH SUB APV#</td>
                <td><input type="text" class="textbox" name="search_sub_apv_no" value="<?=$search_sub_apv_no?>" /></td>
            </tr>
            <tr>
            	<td>SEARCH BANK ACCOUNT.</td>
                <td><?=$options->option_chart_of_accounts($_REQUEST['cash_gchart_id'],'cash_gchart_id')?></td>
            </tr>
        	<tr>
            	<td>CV NO.</td>
                <td><input type="text" class="textbox" name="search_cv_no" value="<?=$search_cv_no?>" /></td>
            </tr>        
        </table>
    </div>
    <div class="module_actions">
    	<input type="submit" name="b" value="Search" />                        
        
        <!--<input type="submit" name="b" value="Show Cleared Checks" />                        
        <input type="submit" name="b" value="Show Uncleared Checks" />   -->
        
        <?php
		$chk1_status = ($chk_status == "") ?  "checked='checked'" : "";
		$chk2_status = ($chk_status == "1") ? "checked='checked'" : "";
		$chk3_status = ($chk_status == "0") ? "checked='checked'" : "";
        ?>
        
        
        <input type="radio" name="chk_status" value="" id="chk1" 	<?=$chk1_status?> /> <label for="chk1">All</label>
        <input type="radio" name="chk_status" value="1" id="chk2"  	<?=$chk2_status?> /> <label for="chk1">Cleared</label>
        <input type="radio" name="chk_status" value="0" id="chk3"  	<?=$chk3_status?> /> <label for="chk1">Uncleared</label>
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;" id="content">
   		  <?php
			$page = $_REQUEST['page'];
			if(empty($page)) $page = 1;
			 
			$limitvalue = $page * $limit - ($limit);
		
			$sql = "
				select
					*
				from
					cv_header as h, supplier as s, cv_detail as d
				where
					h.cv_header_id = d.cv_header_id
				and
					h.supplier_id = s.account_id
				and
					s.account like '$search_supplier%'
			";
			
			if ($project_name){ $sql.=" and d.project_id = '$project_id' "; }
			
			if( $chk_status == "1" ){
				$sql.=" and cleared = '1' ";

			}else if( $chk_status == "0" ){
				$sql.=" and cleared = '0' ";				
			}
			
			if($search_check_no){ $sql.=" and check_no = '$search_check_no' ";	 }
			
			if($_REQUEST['cash_gchart_id']){ $sql.=" and cash_gchart_id = '".$_REQUEST['cash_gchart_id']."' ";	 }
			
			if($search_cv_no){ $sql.=" and cv_no= '".$search_cv_no."' ";	 }
			
			if($search_check_amount){ $sql.=" and	 cash_amount = '$search_check_amount' "; }

			if( $search_sub_apv_no ) $sql .= " and sub_apv_header_id = '$search_sub_apv_no'";
			
			$sql.="
				group by h.cv_header_id
				order by
					cv_date asc
			";
				
			$pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
					
			$i=$limitvalue;
			$rs = $pager->paginate();
		?>
        <div class="pagination">
	        <?php $pagination = $pager->renderFullNav("$view&search_no=$search_check_no&cash_gchart_id=$_REQUEST[cash_gchart_id]&cv_no=$search_cv_no&search_check_amount=$search_check_amount&chk_status=$chk_status&project_id=$project_id&search_supplier=$search_supplier"); ?> 
            <?=$pagination?>
       	</div>
        <table id="my_table" cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table">
            <tr>				
                <th width="20">#</th>                
                <th width="20"></th>
                <th>CV #</th>
                <th>CASH AMOUNT</th>
                <th>SUPPLIER</th>  
                <th>% PAYMENT</th>
                <th>VOUCHER DATE</th>
                <th>CHECK NO.</th>       
                <th>CHECK DATE</th>
                <th>STATUS</th>
                <th>CHECK STATUS</th>
                <th>DATE CLEARED</th>
                <th>PREPARED BY</th>               
                <th>PRINT STATUS</th>               
            </tr>  
            <?php		
				$result = mysql_query($sql) or die(mysql_error());						
                while($r=mysql_fetch_assoc($rs)) {
                        $cv_header_id			= $r['cv_header_id'];
						$cv_header_id_pad		= str_pad($cv_header_id,7,0,STR_PAD_LEFT);
						$percent		= $r['percent'];
						$cv_date		= $r['cv_date'];
						$check_date		= $r['check_date'];
						$check_no		= $r['check_no'];
						$supplier_id	= $r['supplier_id'];
						$supplier 		= $options->getAttribute('supplier','account_id',$supplier_id,'account');
						$cash_gchart_id	= $r['cash_gchart_id'];
						$ap_gchart_id	= $r['ap_gchart_id'];
						$status 		= $r['status'];
						$user_id		= $r['user_id'];
						$cleared		= $r['cleared'];
						$cv_no			= $r['cv_no'];
						
						$cleared_disabled = ($cleared)?"disabled='disabled'":"";
						$check_status = ($cleared)?"CLEARED":"UNCLEARED";
						$check_status_color = ($cleared)?"color:#0F0;":"color:#F00;";
						#$check_amount = number_format($options->getCashAmount($cv_header_id),2,'.','');
						$check_amount = $r['cash_amount'];
						
						$check_box_disabled = ($check_status=="CLEARED" || $status == "C")? 1 : 0;
						$date_cleared = ($r['date_cleared'] == "0000-00-00" || empty($r['date_cleared']) )?"":date("m/d/Y", strtotime($r['date_cleared']));
            ?>
                <tr onmouseover="Tip('<?=$r[particulars];?>');">
                    <td width="20"><?=++$i?></td>                                       
                    <td>
                    	<input type="button" data-id="<?=$cv_header_id?>" data-printed=<?=$r['printed']?> value="Alter Print Status" class="btn-print-status">
                   	</td>
                    <td><?=$cv_no?></td>	
                    <td style="text-align:right;"><?=number_format($check_amount,2,'.',',')?></td>
                    <td><?=$supplier?></td>
                    <td><?=$percent?>%</td>
                    <td><?=date("m/d/Y", strtotime($cv_date))?></td>		
                    <td><?=$check_no?></td>	
                    <td><?=date("m/d/Y", strtotime($check_date))?></td>		
                    <td><?=$options->getTransactionStatusName($status)?></td>	
                    <td style="font-weight:bold; <?=$check_status_color?>"><?=$check_status?></td>
                    <td><?=$date_cleared?></td>		
                    <td><?=$options->getUserName($user_id)?></td>
                    <td class="td-print-status"><?=( ($r['printed']) ? "<b style='color:#F00;' >PRINTED</b>" : "<b style='color:#0F0;'>PENDING</b>" )?></td>
                </tr>
            <?php } ?>
	    </table>
         <div class="pagination">
	        <?=$pagination?>
       	</div>
    </div>
</div>
</form>
<script type="text/javascript">

	jQuery(".btn-print-status").click(function(){
		var cv_header_id = jQuery(this).data('id');
		var printed = jQuery(this).data('printed');
		
		if( printed == "1" ){
			jQuery(this).parent().parent().find('.td-print-status').html("<b style='color:#0F0;'>PENDING</b>");
			jQuery(this).data("printed",0);			
			var print_status = 0;
		} else {
			jQuery(this).parent().parent().find('.td-print-status').html("<b style='color:#F00;' >PRINTED</b>");		
			jQuery(this).data("printed",1);		
			var print_status = 1;
		}
		updatePrintStatus(cv_header_id,print_status);

	});

	function updatePrintStatus(cv_header_id,print_status){

		jQuery.post("admin/admin_check_voucher.php", { ajax : 1, cv_header_id : cv_header_id , print_status : print_status }, function(data){
			alert(data);
		});
	}

</script>