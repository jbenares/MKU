<?php
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
$list2					= $_REQUEST['list2'];
$search_check_amount	= $_REQUEST['search_check_amount'];
$search_check_no		= $_REQUEST['search_check_no'];
$search_cv_no			= $_REQUEST['search_cv_no'];
$search_supplier		= $_REQUEST['search_supplier'];
$chk_status				= $_REQUEST['chk_status'];
$rl_status				= $_REQUEST['rl_status'];
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

if($b == "Release Checked Checks"){	
	if(count($list2) > 0){
		foreach($list2 as $id){
			$date_released = (!empty($_REQUEST['date_released'])) ? $_REQUEST['date_released'] : $_REQUEST['date2_'.$id];
			#echo "update cv_header set cleared = '1', date_cleared = '$date_cleared' where cv_header_id = '$id' <br>";
			mysql_query("
				update cv_header set released = '1', date_released = '$date_released' where cv_header_id = '$id'
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
            <tr>
            	<td>SEARCH SUB APV#</td>
                <td><input type="text" class="textbox" name="search_sub_apv_no" value="<?=$search_sub_apv_no?>" /></td>
            </tr>
            <tr>
            	<td>SEARCH BANK ACCOUNT.</td>
                <td><?=$options->option_chart_of_accounts($_REQUEST['cash_gchart_id'],'cash_gchart_id')?></td>
            </tr>
        </table>
        
        <?php
		$cleared_check_amount = getCheckStatusAmount(1);
		$uncleared_check_amount = getCheckStatusAmount(0);
		
		$balance = $uncleared_check_amount - $cleared_check_amount;
        ?>
        <table style="display:inline-table;">
        	<tr>
            	<td>CV NO.</td>
                <td><input type="text" class="textbox" name="search_cv_no" value="<?=$search_cv_no?>" /></td>
            </tr>
        	<tr>
            	<td>CLEARED CHECKS</td>
                <td><input type="text" class="textbox" name="cleared_checks" id="cleared_checks" style="font-weight:bold; color:#F00; text-align:right;" readonly="readonly" value="<?=number_format($cleared_check_amount,2,'.',',')?>" /></td>
            </tr>
            <tr>
            	<td>UNCLEARED CHECKS</td>
                <td><input type="text" class="textbox" name="uncleared_checks" id="uncleared_checks" style="font-weight:bold; color:#F00; text-align:right;" readonly="readonly" value="<?=number_format($uncleared_check_amount,2,'.',',')?>" /></td>
            </tr>
            <tr>
            	<td>BALANCE</td>
                <td><input type="text" class="textbox" name="balance" id="balance" style="font-weight:bold; color:#F00; text-align:right;" readonly="readonly" value="<?=number_format($balance,2,'.',',')?>" /></td>
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
		$chk4_status = ($rl_status == "1") ? "checked='checked'" : "";
		$chk5_status = ($rl_status == "0") ? "checked='checked'" : "";
        ?>
        
        
        <input type="radio" name="chk_status" value=""  id="chk1" 	<?=$chk1_status?> /> <label for="chk1">All</label>
        <input type="radio" name="chk_status" value="1" id="chk2"  	<?=$chk2_status?> /> <label for="chk1">Cleared</label>
        <input type="radio" name="chk_status" value="0" id="chk3"  	<?=$chk3_status?> /> <label for="chk1">Uncleared</label>&nbsp; | &nbsp;
		<input type="radio" name="rl_status"  value="1"  id="chk4"  <?=$chk4_status?> /> <label for="chk1">Released</label>
		<input type="radio" name="rl_status"  value="0"  id="chk5"  <?=$chk5_status?> /> <label for="chk1">Unreleased</label>
    </div>
    <div class="module_actions">
    	<input type="text" name="date_cleared" class="textbox datepicker" />
	    <input type="submit" name="b" value="Clear Checked Checks" onclick="return approve_confirm();" />
		<input type="text" name="date_released" class="textbox datepicker" />
		<input type="submit" name="b" value="Release Checked Checks" onclick="return approve_confirm();" />		
        <input type="submit" name="b" value="Finish Selected"  />
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
			
			if ($project_name){
			$sql.="
				and
					d.project_id = '$project_id'
			";
			}
			
			if( $chk_status == "1" ){
			$sql.="
				and
					cleared = '1'
			";
			}else if( $chk_status == "0" ){
			$sql.="
			and
				cleared = '0'
			";				
			}
			
			if($rl_status == "1"){
				$sql .="and
							released = '1'
				";
			}else if($rl_status == "0"){
				$sql .="and
							released = '0'
				";
			}
			
			if($search_check_no){
			$sql.="
				and
					check_no = '$search_check_no'
			";	
			}
			
			if($_REQUEST['cash_gchart_id']){
			$sql.="
				and
					cash_gchart_id = '".$_REQUEST['cash_gchart_id']."'
			";	
			}
			
			if($search_cv_no){
			$sql.="
				and
					cv_no= '".$search_cv_no."'
			";	
			}
			
			if($search_check_amount){
			$sql.="
				and	
					cash_amount = '$search_check_amount'
			";	
			}

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
                <th width="20" align="center"></th>
				<th width="20" align="center"></th>
                <th>DATE CLEARED</th>
				<th>DATE RELEASED</th>
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
				<th>RELEASE STATUS</th>
                <th>DATE CLEARED</th>
				<th>DATE RELEASED</th>
                <th>PREPARED BY</th>
                <th></th>
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
						$released		= $r['released'];
						$cv_no			= $r['cv_no'];
						
						
						$cleared_disabled = ($cleared)?"disabled='disabled'":"";
						$check_status = ($cleared)?"CLEARED":"UNCLEARED";
						$check_status_color = ($cleared)?"color:#0F0;":"color:#F00;";
						
						$released_disabled = ($released)?"disabled='disabled'":"";
						$check_status2 = ($released)?"RELEASED":"UNRELEASED";
						$check_status_color2 = ($released)?"color:#0F0;":"color:#F00;";
						
						#$check_amount = number_format($options->getCashAmount($cv_header_id),2,'.','');
						$check_amount = $r['cash_amount'];
						
						$check_box_disabled = ($check_status=="CLEARED" || $status == "C")? 1 : 0;
						$check_box_disabled2 = ($check_status2=="RELEASED" || $status == "C")? 1 : 0;
						$date_cleared = ($r['date_cleared'] == "0000-00-00" || empty($r['date_cleared']) )?"":date("m/d/Y", strtotime($r['date_cleared']));
						$date_released = ($r['date_released'] == "0000-00-00" || empty($r['date_released']) )?"":date("m/d/Y", strtotime($r['date_released']));
            ?>
                <tr onmouseover="Tip('<?=$r[particulars];?>');">
                    <td width="20"><?=++$i?></td>
                    
                    <td>
                    <?php if(!$check_box_disabled && $check_status2=="RELEASED"){ ?>
                    <input type="checkbox" name="list[]" value="<?=$cv_header_id?>" class="check" />
                    <?php } ?>
                    </td>
					<td>
					<?php if(!$check_box_disabled2){ ?>
                    <input type="checkbox" name="list2[]" value="<?=$cv_header_id?>" class="check" />
                    <?php } ?>
					</td>
                    <td>
						<?phpif($check_status2 == "RELEASED" && $check_status !="CLEARED" ):?>
						<input type="text" class="textbox3 datepicker" name="date_<?=$cv_header_id?>" />
						<?php endif; ?>
					</td>
					<td>
						<?phpif($check_status2 != "RELEASED"):?>
						<input type="text" class="textbox3 datepicker" name="date2_<?=$cv_header_id?>" />
						<?php endif; ?>
					</td>
                    <td><a href="admin.php?view=9d825239df14c9830e3b&cv_header_id=<?=$cv_header_id?>" ><img src="images/edit.gif" style="cursor:pointer;"/></a></td>
                    <td><?=$cv_no?></td>	
                    <td style="text-align:right;"><?=number_format($check_amount,2,'.',',')?></td>
                    <td><?=$supplier?></td>
                    <td><?=$percent?>%</td>
                    <td><?=date("m/d/Y", strtotime($cv_date))?></td>		
                    <td><?=$check_no?></td>	
                    <td><?=date("m/d/Y", strtotime($check_date))?></td>		
                    <td><?=$options->getTransactionStatusName($status)?></td>	
                    <td style="font-weight:bold; <?=$check_status_color?>"><?=$check_status?></td>
					<td style="font-weight:bold; <?=$check_status_color2?>"><?=$check_status2?></td>
                    <td><?=$date_cleared?></td>
					<td><?=$date_released?></td>					
                    <td><?=$options->getUserName($user_id)?></td>
                    <td>
                    	 <?php if($status == "S"){ ?>
                    	<select name="status[]" >
                        	<option value="">Select Status:</option>
                            <option value="F">Finish</option>
                        </select>
                        <input type="hidden" name="ids[]" value="<?=$cv_header_id?>" />
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
	    </table>
         <div class="pagination">
	        <?=$pagination?>
       	</div>
    </div>
</div>
</form>