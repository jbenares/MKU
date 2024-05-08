<?php
/********************************************
Author      : Michael Angelo O. Salvio, CpE, MIT
Description : Premix Delivery
********************************************/
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
.S{ font-weight: bold; color:#FFFF00; }
.F{ font-weight: bold; color:#00FF2A; }
.C{ font-weight: bold; color:#FE2712; }
</style>
<?php
function finishOrCancelTranasction(){
    $status = ($_REQUEST['b'] == "Finish") ? "F" : "C";
    $sql  = "
        update
            premix_delivery
        set 
            status = '$status',
            updated_by_id = '$_SESSION[userID]',
            updated_datetime = '".lib::now()."'
        where
            premix_delivery_id = '$_REQUEST[premix_delivery_id]'
    ";
    DB::conn()->query($sql);
    $msg = ($_REQUEST['b'] == "Finish") ? "Transaction Finished" : "Transaction Cancelled";
    return $msg;
}
function finishOrUnfinish(){
    $status = ($_REQUEST['b'] == "Finish") ? "F": "S";
    $sql  = "
        update
            premix_delivery
        set 
            status = '$status',
            updated_by_id = '$_SESSION[userID]',
            updated_datetime = '".lib::now()."'
        where
            premix_delivery_id = '$_REQUEST[premix_delivery_id]'
    ";
    DB::conn()->query($sql);
    $msg = ($_REQUEST['b'] == "Finish") ? "Transaction Finished" : "Transaction Unfinished";
    return $msg;
}

function saveToDB(){
    

    if( !empty($_REQUEST['premix_delivery_id']) ){    
        $sql = "
            update
                premix_delivery
            set
                date             = '$_REQUEST[date]',
                batch_no         = '$_REQUEST[batch_no]',
                project_id       = '$_REQUEST[project_id]',
                premix_id        = '$_REQUEST[premix_id]',
                volume           = '$_REQUEST[volume]',
                price            = '$_REQUEST[price]',
                equipment_id     = '$_REQUEST[equipment_id]',
                remarks          = '$_REQUEST[remarks]',
                driver_id        = '$_REQUEST[driver_id]',                
                updated_by_id    = '$_SESSION[userID]',
                updated_datetime = '".lib::now()."',
                checked_by_id    = '$_REQUEST[checked_by_id]',
                pumpcrete_cost   = '$_REQUEST[pumpcrete_cost]',
				reference        = '$_REQUEST[reference]',
				pl_operator		 = '$_REQUEST[pl_operator]',
				bp_operator		 = '$_REQUEST[bp_operator]'
            where 
                premix_delivery_id = '$_REQUEST[premix_delivery_id]'
        ";
        DB::conn()->query($sql) or die(DB::conn()->error);

            
        $msg = "Tranasction Updated.";
    } else { 
        $sql = "
            insert into 
                premix_delivery
            set
                date             = '$_REQUEST[date]',
                batch_no         = '$_REQUEST[batch_no]',
                project_id       = '$_REQUEST[project_id]',
                premix_id        = '$_REQUEST[premix_id]',
                volume           = '$_REQUEST[volume]',
                price            = '$_REQUEST[price]',
                equipment_id     = '$_REQUEST[equipment_id]',
                remarks          = '$_REQUEST[remarks]',
                driver_id        = '$_REQUEST[driver_id]',                                
                encoded_by_id    = '$_SESSION[userID]',
                encoded_datetime = '".lib::now()."',
                checked_by_id    = '$_REQUEST[checked_by_id]',
                pumpcrete_cost   = '$_REQUEST[pumpcrete_cost]',
				reference        = '$_REQUEST[reference]',
				pl_operator		 = '$_REQUEST[pl_operator]',
				bp_operator		 = '$_REQUEST[bp_operator]'
        ";   
        DB::conn()->query($sql) or die(DB::conn()->error);        
        $_REQUEST['premix_delivery_id'] = DB::conn()->insert_id;
        $msg = "Tranasction Saved.";
    }
    
    return $msg;
}

if( $_REQUEST['b'] == "Submit" ){
    $msg = saveToDB();    
} else if( $_REQUEST['b'] == "Add" ){
    $msg = insertDetail();
} else if( $_REQUEST['b'] == "d" ){
    $msg = deleteDetail();
} else if( $_REQUEST['b'] == "Finish" || $_REQUEST['b'] == "Cancel" ){
    $msg = finishOrCancelTranasction();
}else if( $_REQUEST['b'] == "Unfinish" || $_REQUEST['b'] == "Update"){
    $msg = finishOrUnfinish();
}


if( $_REQUEST['premix_delivery_id'] ){

	$query="
		select
			*
		from
			premix_delivery            
		where
			premix_delivery_id = '$_REQUEST[premix_delivery_id]'
	";

	$aVal = DB::conn()->query($query)->fetch_assoc() or die(DB::conn()->error);

} else {
	$aVal = $_REQUEST;
}


?>

<form name="header_form" id="header_form" action="" method="post">
<div class="module_actions">	
    <div class='inline'>
        REFERENCE # : <br />  
        <input type="text" class="textbox"  name="search_reference" value="<?=$_REQUEST['search_reference']?>"  onclick="this.select();"  autocomplete="off" placeholder="Search" />
    </div>   
    <input type="submit" name="b" value="Search" />
    <a href="admin.php?view=<?=$view?>"><input type="button" value="New" /></a>
</div>

<?php
if($_REQUEST['b'] == "Search"){
?>
	<?php
    $page = $_REQUEST['page'];
    if(empty($page)) $page = 1;
     
    $limitvalue = $page * $limit - ($limit);
    
    $sql = "
		select
        	d.*, p.project_name, pr.stock as premix
        from
			premix_delivery as d
            left join projects as p on d.project_id = p.project_id
            left join productmaster as pr on d.premix_id = pr.stock_id

        where 
            1=1
    ";
        
    if(!empty($_REQUEST['search_reference'])) $sql.=" and reference like '%$_REQUEST[search_reference]%' ";
    
	
	$sql.=" order by reference desc ";


    /*echo "<pre>";
    echo $sql;
    echo "</pre>";*/
  
    $pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
            
    $i=$limitvalue;
    $rs = $pager->paginate();
	
	$pagination	= $pager->renderFullNav("$view&b=Search&search_eur_no=$search_eur_no");
    ?>
    <div class="pagination">
        <?=$pagination?>
    </div>
    <table id="my_table" cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table">
    <tr>				
    
        <th width="20">#</th>
        <th width="20"></th>
        <th style="width:10%;">PREMIX DR #</th>
		 <th style="width:10%;">REFERENCE #</th>
        <th>DATE</th>
        <th>PROEJECT</th>
        <th>PREMIX</th>
        <th style="width:15%;">ENCODED BY</th>
        <th style="width:10%; text-align:center;">STATUS</th>
    </tr>  
    <?php								
    while($r=mysql_fetch_assoc($rs)) {
        
        echo '<tr>';
        echo '<td width="20">'.++$i.'</td>';
        echo '<td width="15"><a href="admin.php?view='.$view.'&premix_delivery_id='.$r['premix_delivery_id'].'" title="Show Details"><img src="images/edit.gif" border="0"></a></td>';
		echo '<td>'.str_pad($r['premix_delivery_id'],7,0,STR_PAD_LEFT).'</td>';	
		echo '<td>PD# '.$r['reference'].'</td>';	
		echo '<td>'.lib::ymd2mdy($r['date']).'</td>';	
		echo '<td>'.$r['project_name'].'</td>';	
		echo '<td>'.$r['premix'].'</td>';	
        echo '<td>'.lib::getUserFullName($r['encoded_by_id']).'</td>';    
        echo "<td style='text-align:center;'><span class='$r[status]'>".$GLOBALS['aStatus'][$r['status']].'<span></td>';    
        echo '</tr>';
    }
    ?>
    </table>
    <div class="pagination">
	   	 <?=$pagination?>
    </div>
<?php
} else {
?>
	<style type="text/css">
		.trans-header{
			width:60%;
			border-collapse: collapse;
		}
		.trans-header tbody td{
			padding:3px 5px 3px 3px;
		}
		.trans-header tbody td:nth-child(even){
			padding-right:20px;
		}
		.trans-header tbody td:nth-child(odd){
			text-align: right;
		}
		.jo-detail{
			width:100%;
			border-collapse: collapse;
		}
		.jo-detail tbody td{
			border:1px solid #c0c0c0;
			padding:3px;
		}
		.jo-detail tbody td:nth-child(2),.jo-detail tbody td:nth-child(3){
			width:20%;
		}
	</style>


    <div class=form_layout>
        <?php if(!empty($msg)) echo '<div id="status_update" class="ui-state-highlight ui-corner-all" style="padding: 0px 0.7em; text-align:left;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right:.3em;"></span> '.$msg.'</p></div>'; ?>
        <div class="module_title"><img src='images/user_orange.png'>PREMIX DELIVERY</div>        
        <div class="module_actions">
            <input type="hidden" name="premix_delivery_id" value="<?=$aVal['premix_delivery_id']?>">
            <table class="trans-header">
            	<tbody>                    
            		<tr>
            			<td>Project</td>
            			<td>
            				<input type="text" class="textbox project" value="<?=lib::getAttribute('projects','project_id',$aVal['project_id'],'project_name')?>">
                            <input type="hidden" name="project_id" value="<?=$aVal['project_id']?>" >
            			</td>            		

            			<td>Date</td>
            			<td><input type="text" name="date" class="textbox datepicker" value="<?=$aVal['date']?>" readonly ></td>
            		</tr>
            		<tr>
            			<td>Batch No</td>
            			<td>
            				<input type="text" class="textbox" name="batch_no" id="batch_no" value="<?=$aVal['batch_no']?>">
            			</td>

            			<td>Equipment</td>
            			<td>
            				<input type="text" class="textbox stock_name" value='<?=lib::getAttribute('productmaster','stock_id',$aVal['equipment_id'],'stock')?>'>
            				<input type="hidden" name="equipment_id" value="<?=$aVal['equipment_id']?>">
            			</td>
            		</tr>
                    <tr>
                        <td>Premix</td>
                        <td>
                            <input type="text" class="textbox ac-premix" value='<?=lib::getAttribute('productmaster','stock_id',$aVal['premix_id'],'stock')?>'>
                            <input type="hidden" name="premix_id" value="<?=$aVal['premix_id']?>">
                        </td>
						<td>PD# (Manual)</td>
            			<td>
            				<input type="text" class="textbox" name="reference" id="reference" value="<?=$aVal['reference']?>">
            			</td>
                    </tr>
                    <tr>
                        <td>Price</td>
                        <td>
                            <input type="text" class="textbox" name="price" id="price" value="<?=$aVal['price']?>">
                        </td>
                    </tr>
                    <tr>
                        <td>Pumpcrete Cost</td>
                        <td>
                            <input type="text" class="textbox" name="pumpcrete_cost" id="pumpcrete_cost" value="<?=$aVal['pumpcrete_cost']?>">
                        </td>
                    </tr>
                    <tr>
                        <td>Volume</td>
                        <td>
                            <input type="text" class="textbox" name="volume" id="volume" value="<?=$aVal['volume']?>">
                        </td>
                    </tr>
            		<tr>
            			<td>T.M Operator</td>
            			<td>
            				<input type="text" class="textbox ac-employee" value="<?=lib::getEmployeeName($aVal['driver_id'])?>">
            				<input type="hidden" name="driver_id" value="<?=$aVal['driver_id']?>" >	
            			</td>

                        <td>Checked & Verified By:</td>
                        <td>
                            <input type="text" class="textbox ac-employee" value="<?=lib::getEmployeeName($aVal['checked_by_id'])?>">
                            <input type="hidden" name="checked_by_id" value="<?=$aVal['checked_by_id']?>" > 
                        </td>
            		</tr>
					<tr>
            			<td>P.L Operator</td>
            			<td>
            				<input type="text" class="textbox ac-employee" value="<?=lib::getEmployeeName($aVal['pl_operator'])?>">
            				<input type="hidden" name="pl_operator" value="<?=$aVal['pl_operator']?>" >	
            			</td>
				   </tr>
					<tr>
            			<td>B.P Operator</td>
            			<td>
            				<input type="text" class="textbox ac-employee" value="<?=lib::getEmployeeName($aVal['bp_operator'])?>">
            				<input type="hidden" name="bp_operator" value="<?=$aVal['bp_operator']?>" >	
            			</td>
				   </tr>
                    <tr>
                        <td style="vertical-align:top;">Remarks</td>
                        <td colspan="3">
                            <textarea style="width:100%; border:1px solid #c0c0c0; height:60px; font-size:11px; font-family:arial;" name="remarks" id="remarks"><?=$aVal['remarks']?></textarea>
                        </td>
                    </tr>
            	</tbody>
            </table>    
            
        </div>
        <?php if(!empty($aVal['status'])){ ?>
        <div class="module_actions">
        	<div style="display:inline-block; margin-right:10px; vertical-align:top;">
            	Status:<br />
                <span style="font-size:15px; font-weight:bold;"><?=$GLOBALS['aStatus'][$aVal['status']]?></span>
            </div>
            <div style="display:inline-block; margin: 0px 10px;">
            	Encoded by:<br />
                <span style="font-size:15px; font-weight:bold;"><?=lib::getUserFullName($aVal['encoded_by_id'])?></span><br>
                <?=$aVal['encoded_datetime']?>
            </div>
            <?php if( !empty($aVal['updated_by_id']) ): ?>
            <div style="display:inline-block;">
                Updated by:<br />
                <span style="font-size:15px; font-weight:bold;"><?=lib::getUserFullName($aVal['updated_by_id'])?></span><br>
                <?=$aVal['updated_datetime']?>
            </div>
            <?php endif; ?>
        </div>
        <?php } ?>
        <div class="module_actions">
            <?php if( $aVal['status'] == "S" || empty($aVal['status']) ){ ?>
            <input type="submit" name="b"  value="Submit" />            
            <?php } ?>
            <?php if( $aVal['status'] == "S" ){ ?>
           <input type="submit" name="b"  value="Finish" />        
            <?php } ?>
			<?php if( $aVal['status'] == "F"){ ?>
           <input type="submit" name="b"  value="Unfinish"/>            
            <?php } ?>
            <?php if( $aVal['status'] == "F" || $aVal['status'] == "S" ){ ?>
            <input type="submit" name="b"  value="Cancel"  onclick='return approve_confirm();'/>            
            <?php } ?>
            <?php if( !empty($aVal['status']) && $_REQUEST['b'] != "Print Preview"){ ?>
            <input type="submit" name="b"  value="Print Preview" />            
            <?php } ?>
            <?php if( $_REQUEST['b'] == "Print Preview" ){ ?>
            <input type="button" value="Print" onclick="printIframe('JOframe');" />
            <?php } ?>
            <a href="admin.php?view=<?=$view?>"><input type="button" value="New" /></a>
        </div>        
    </div>

<?php } ?>
<?php
if($_REQUEST['b'] == "Print Preview"){
	echo "	<iframe id='JOframe' name='JOframe' frameborder='0' src='batching_plant/print_premix_delivery.php?premix_delivery_id=$aVal[premix_delivery_id]' width='100%' height='500'>
			</iframe>";
}
			
?>
</form>
<script type="text/javascript">
jQuery(function(){	
	jQuery(".ac-employee").autocomplete({
		source: "autocomplete/employees.php",
		minLength: 2,
		select: function(event, ui) {
			jQuery(this).val(ui.item.value);
			jQuery(this).next().val(ui.item.id);
		}
	});

    jQuery(".ac-premix").autocomplete({
        source: "stocks.php",
        minLength: 2,
        select: function(event, ui) {
            jQuery(this).val(ui.item.value);
            jQuery(this).next().val(ui.item.id);
            jQuery("#price").val(ui.item.price1);                    
        }
    });
});
</script>
	