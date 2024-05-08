<?php
/********************************************
Author      : Michael Angelo O. Salvio, CpE, MIT
Description : Premix Quotation
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

function updateDetails(){
    if( count($_REQUEST['arr_premix_quotation_detail_id']) ){
        $i = 0;
        foreach ($_REQUEST['arr_premix_quotation_detail_id'] as $id) {
            $sql = "
                update
                    premix_quotation_detail
                set
                    premix_desc      = '".$_REQUEST['arr_premix_desc'][$i]."',
                    quantity         = '".$_REQUEST['arr_quantity'][$i]."',
                    pumpcrete_cost   = '".$_REQUEST['arr_pumpcrete_cost'][$i]."',
                    total_amount     = '".$_REQUEST['arr_total_amount'][$i]."',
                    premix_cost      = '".$_REQUEST['arr_premix_cost'][$i]."',
                    premix_amount    = '".$_REQUEST['arr_premix_amount'][$i]."',
                    pumpcrete_amount = '".$_REQUEST['arr_pumpcrete_amount'][$i]."'
                where 
                    premix_quotation_detail_id = '$id'
            ";
            DB::conn()->query($sql);
            $i++;
        }

    }
}

function getDetails($premix_quotation_header_id){
    $sql = "
        select 
            d.*, p.stock
        from 
            premix_quotation_detail as d
            left join productmaster as p on d.stock_id = p.stock_id
        where 
            premix_quotation_header_id = '$premix_quotation_header_id' 
        and premix_quotation_void = '0'
    ";

    return lib::getArrayDetails($sql);
}

function insertDetail(){
    $sql = "
        insert into 
            premix_quotation_detail
        set
            premix_quotation_header_id = '$_REQUEST[premix_quotation_header_id]',
            premix_desc                = '$_REQUEST[premix_desc]',
            quantity                   = '$_REQUEST[quantity]',
            premix_cost                = '$_REQUEST[premix_cost]',
            pumpcrete_cost             = '$_REQUEST[pumpcrete_cost]',
            premix_amount              = '$_REQUEST[premix_amount]',
            pumpcrete_amount           = '$_REQUEST[pumpcrete_amount]',
            total_amount               = '$_REQUEST[total_amount]',
            stock_id                   = '$_REQUEST[stock_id]'
    ";
    DB::conn()->query($sql);
    return "Premix Added";
}

function deleteDetail(){
    $sql =  "
        update 
            premix_quotation_detail
        set
            premix_quotation_void = '1'
        where
            premix_quotation_detail_id = '$_REQUEST[id]'
    ";
    DB::conn()->query($sql);
    return "Detail Voided.";
}

function finishOrCancelTranasction(){
    $status = ($_REQUEST['b'] == "Finish") ? "F" : "C";
    $sql  = "
        update
            premix_quotation_header
        set 
            status           = '$status',
            updated_by       = '$_SESSION[userID]',
            updated_datetime = '".lib::now()."'
        where
            premix_quotation_header_id = '$_REQUEST[premix_quotation_header_id]'
    ";
    DB::conn()->query($sql);
    $msg = ($_REQUEST['b'] == "Finish") ? "Transaction Finished" : "Transaction Cancelled";
    return $msg;
}

function UnfinishTransac(){
    #$status = ($_REQUEST['b'] == "Finish") ? "F" : "C";
    $sql  = "
        update
            premix_quotation_header
        set 
            status           = 'S',
            updated_by       = '$_SESSION[userID]',
            updated_datetime = '".lib::now()."'
        where
            premix_quotation_header_id = '$_REQUEST[premix_quotation_header_id]'
    ";
    DB::conn()->query($sql);
    $msg = ($_REQUEST['b'] == "Unfinish") ? "Transaction Unfinished" : "Transaction Cancelled";
    return $msg;
}

function saveToDB(){
    

    if( !empty($_REQUEST['premix_quotation_header_id']) ){    
        $sql = "
            update
                premix_quotation_header
            set
                date             = '$_REQUEST[date]',
                client_info      = '".addslashes($_REQUEST['client_info'])."',
                client_address   = '".addslashes($_REQUEST['client_address'])."',
                remarks          = '".addslashes($_REQUEST['remarks'])."',                
                noted_by         = '$_REQUEST[noted_by]',
                updated_datetime = '".lib::now()."',
                updated_by       = '$_SESSION[userID]'
            where 
                premix_quotation_header_id = '$_REQUEST[premix_quotation_header_id]'
        ";
        DB::conn()->query($sql) or die(DB::conn()->error);

        updateDetails();
            
        $msg = "Tranasction Updated.";

    } else { 

        $sql = "
            insert into 
                premix_quotation_header
            set
               date             = '$_REQUEST[date]',
               client_info      = '".addslashes($_REQUEST['client_info'])."',
               client_address   = '".addslashes($_REQUEST['client_address'])."',
               remarks          = '".addslashes($_REQUEST['remarks'])."',
               prepared_by      = '$_SESSION[userID]',
               noted_by         = '$_REQUEST[noted_by]',
               encoded_datetime = '".lib::now()."'

        ";   
        DB::conn()->query($sql) or die(DB::conn()->error);        
        $_REQUEST['premix_quotation_header_id'] = DB::conn()->insert_id;
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
}else if($_REQUEST['b'] == "Unfinish"){
     $msg = UnfinishTransac();
}


if( $_REQUEST['premix_quotation_header_id'] ){

	$query="
		select
			*
		from
			premix_quotation_header            
		where
			premix_quotation_header_id = '$_REQUEST[premix_quotation_header_id]'
	";

	$aVal = DB::conn()->query($query)->fetch_assoc() or die(DB::conn()->error);

} else {
	$aVal = $_REQUEST;
}


?>

<form name="header_form" id="header_form" action="" method="post">
<div class="module_actions">	
    <div class='inline'>
        Search: <br />  
        <input type="text" class="textbox"  name="search_keyword" value="<?=$_REQUEST['search_keyword']?>"  onclick="this.select();"  autocomplete="off" placeholder="Search" />
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
        	*,(premix_cost + pumpcrete_cost) as sum
        from
			premix_quotation_header as ph,
			premix_quotation_detail as pd
        where             
            ph.premix_quotation_header_id = pd.premix_quotation_header_id
			and pd.premix_quotation_void = '0'
    ";
        
    if(!empty($_REQUEST['search_keyword'])) $sql.=" 
        and 
        
		(
            ph.premix_quotation_header_id like '%$_REQUEST[search_keyword]%' 
            or ph.client_info like '%$_REQUEST[search_keyword]%'
        )
    ";    

    
	
	$sql.=" order by date desc ";


   /* echo "<pre>";
    echo $sql;
    echo "</pre>";*/
  
    $pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
            
    $i=$limitvalue;
    $rs = $pager->paginate();
	
	$pagination	= $pager->renderFullNav("$view&b=Search&search_keyword=$_REQUEST[search_keyword]");
    ?>
    <div class="pagination">
        <?=$pagination?>
    </div>
    <table id="my_table" cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table">
    <tr>				
    
        <th width="20">#</th>
        <th width="20"></th>
        <th style="width:10%;">PREMIX QUOTATION #</th>
        <th>DATE</th>
        <th>CLIENT</th>
		<th>ADDRESS</th>
		<th>SPECIFICATION/STRENGTH</th>
		<th>AMOUNT</th>
        <th style="width:15%;">ENCODED BY</th>
        <th style="width:10%; text-align:center;">STATUS</th>
    </tr>  
    <?php								
    while($r=mysql_fetch_assoc($rs)) {
        
        echo '<tr>';
        echo '<td width="20">'.++$i.'</td>';
        echo '<td width="15"><a href="admin.php?view='.$view.'&premix_quotation_header_id='.$r['premix_quotation_header_id'].'" title="Show Details"><img src="images/edit.gif" border="0"></a></td>';
		echo '<td>'.str_pad($r['premix_quotation_header_id'],7,0,STR_PAD_LEFT).'</td>';	
		echo '<td>'.lib::ymd2mdy($r['date']).'</td>';	
		echo '<td>'.$r['client_info'].'</td>';		
		echo '<td>'.$r['client_address'].'</td>';
		echo '<td>'.$options->getAttribute('productmaster','stock_id',$r['stock_id'],'stock').'</td>';
		echo '<td>Php.'.number_format($r['sum'],2).'</td>';		
        echo '<td>'.lib::getUserFullName($r['prepared_by']).'</td>';    
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
        .details-table tbody tr:nth-child(3n) td{
            border-bottom: 1px dashed #000;
            padding-bottom: 20px;
        }

        .details-container{
            max-height: 300px;            
            overflow: auto;
        }
        .details-container thead td{
            font-weight: bold;
            text-align: center;
        }
	</style>


    <div class=form_layout>
        <?php if(!empty($msg)) echo '<div id="status_update" class="ui-state-highlight ui-corner-all" style="padding: 0px 0.7em; text-align:left;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right:.3em;"></span> '.$msg.'</p></div>'; ?>
        <div class="module_title"><img src='images/user_orange.png'>PREMIX QUOTATION</div>        
        <div class="module_actions">
            <input type="hidden" name="premix_quotation_header_id" value="<?=$aVal['premix_quotation_header_id']?>">
            <table class="trans-header">
            	<tbody>                    
            		<tr>
            			<td>Client</td>
            			<td>
            				<input type="text" class="textbox" name="client_info" value="<?=$aVal['client_info']?>">                            
            			</td>            		

            			<td>Date</td>
            			<td><input type="text" name="date" class="textbox datepicker" value="<?=$aVal['date']?>" readonly ></td>
            		</tr>
            		<tr>
            			<td style="vertical-align:top;">Client Address</td>
            			<td colspan="3">
                            <textarea style="width:100%; border:1px solid #c0c0c0; height:60px; font-size:11px; font-family:arial;" name="client_address" id="client_address"><?=$aVal['client_address']?></textarea>
                        </td>            		
            		</tr>                                		
                    <tr>
                        <td style="vertical-align:top;">Remarks</td>
                        <td colspan="3">
                            <textarea style="width:100%; border:1px solid #c0c0c0; height:60px; font-size:11px; font-family:arial;" name="remarks" id="remarks"><?=$aVal['remarks']?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td>Noted By</td>
                        <td>
                            <input type="text" class="textbox ac-employee" value="<?=lib::getEmployeeName($aVal['noted_by'])?>">
                            <input type="hidden" name="noted_by" value="<?=$aVal['noted_by']?>" >   
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
                <span style="font-size:15px; font-weight:bold;"><?=lib::getUserFullName($aVal['prepared_by'])?></span><br>
                <?=$aVal['encoded_datetime']?>
            </div>
            <?php if( !empty($aVal['updated_by']) ): ?>
            <div style="display:inline-block;">
                Updated by:<br />
                <span style="font-size:15px; font-weight:bold;"><?=lib::getUserFullName($aVal['updated_by'])?></span><br>
                <?=$aVal['updated_datetime']?>
            </div>
            <?php endif; ?>
        </div>
        <?php } ?>
        <div class="module_actions">
            <?php if( $aVal['status'] == "S" || empty($aVal['status']) ){ ?>
            <input type="submit" name="b"  value="Submit" />            
            <?php } ?>
             <?php if( $aVal['status'] == "F" ){ ?>
            <input type="submit" name="b"  value="Unfinish" onclick='return approve_confirm();' />            
            <?php } ?>
            <?php if( $aVal['status'] == "S" ){ ?>
            <input type="submit" name="b"  value="Finish" onclick='return approve_confirm();' />            
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
    <?php if( $aVal['status'] == "S" ) { ?>
    <div class="module_actions">
        <table>
            <thead>
                <tr>
                    <td>PREMIX</td>
                    <td>DESCRIPTION</td>
                    <td>QUANTITY (CU.M.)</td>
                    <td>COST/CU.M</td>
                    <td>TOTAL AMOUNT</td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <input type="text" class="textbox pm-ac" onclick="this.select();" onkeypress="if(event.keyCode==13){ jQuery('#premix_desc').focus(); return false; }" >
                        <input type="hidden" name="stock_id" value="">
                    </td>
                    <td><input type="text" class="textbox" name="premix_desc" id="premix_desc" onkeypress="if(event.keyCode==13){ jQuery('#quantity').focus(); return false; }"></td>
                    <td><input type="text" style="text-align:right;" class="textbox" name="quantity" id="quantity" onkeypress="if(event.keyCode==13){ jQuery('#premix_cost').focus(); return false; }"></td>
                    <td><input type="text" style="text-align:right;" class="textbox" name="premix_cost" id="premix_cost" onkeypress="if(event.keyCode==13){ jQuery('#pumpcrete_cost').focus(); return false; }"></td>
                    <td><input type="text" style="text-align:right;" class="textbox" name="premix_amount" id="premix_amount" readonly></td>
                    <td><input type="submit" name="b" id="add_btn" value="Add"></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td style="text-align:right;">Pumpcrete Cost</td>                    
                    <td><input type="text" style="text-align:right;" class="textbox" name="pumpcrete_cost" id="pumpcrete_cost" onkeypress="if(event.keyCode==13){ jQuery('#add_btn').click(); return false; }"></td>
                    <td><input type="text" style="text-align:right;" class="textbox" name="pumpcrete_amount" id="pumpcrete_amount" readonly></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><input type="text" style="text-align:right; border:none; border-top:1px solid #000; color:#F00; font-weight:bold; background-color:none;" class="textbox" name="total_amount" id="total_amount" value="0.00" readonly></td>
                </tr>
            </tbody>
        </table>        
    </div>
    <?php } #end if ?>
    <?php if( !empty( $aVal['status'] ) ){ ?>
    <div class='details-container'>

        <table class="details-table">
            <thead>
                <tr>
                    <td style='width:3px;'></td>
                    <td>SPECIFICATION/STRENGTH</td>
                    <td>DESCRIPTION</td>
                    <td>QUANTITY (CU.M.)</td>
                    <td>COST/CU.M</td>
                    <td>TOTAL AMOUNT</td>
                </tr>
            </thead>
            <tbody>
            <?php
            $arr = getDetails($aVal['premix_quotation_header_id']);
            if( count($arr) ){                
                foreach( $arr as $r ){
            ?>
            
                <tr>
                    <td style="width:3px;">
                        <a onclick="return approve_confirm();" href="admin.php?view=<?=$view?>&premix_quotation_header_id=<?=$r['premix_quotation_header_id']?>&b=d&id=<?=$r['premix_quotation_detail_id']?>" title="Show Details">
                            <img src="images/trash.gif" class="trash" >
                        </a>
                        <input type="hidden" name="arr_premix_quotation_detail_id[]" value="<?=$r['premix_quotation_detail_id']?>">
                    </td>
                    <td><input type="text" class="textbox" value="<?=$r['stock']?>" readonly ></td>
                    <td><input type="text" class="textbox" name="arr_premix_desc[]" onkeypress="if(event.keyCode==13){ jQuery('#quantity').focus(); return false; }" value="<?=$r['premix_desc']?>" ></td>
                    <td><input type="text" style="text-align:right;" class="textbox" name="arr_quantity[]" value="<?=$r['quantity']?>" ></td>
                    <td><input type="text" style="text-align:right;" class="textbox" name="arr_premix_cost[]" value="<?=$r['premix_cost']?>"  ></td>
                    <td><input type="text" style="text-align:right;" class="textbox" name="arr_premix_amount[]" value="<?=$r['premix_amount']?>"  readonly></td>                    
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="text-align:right;">Pumpcrete Cost</td>                    
                    <td><input type="text" style="text-align:right;" class="textbox" name="arr_pumpcrete_cost[]" value="<?=$r['pumpcrete_cost']?>"  ></td>
                    <td><input type="text" style="text-align:right;" class="textbox" name="arr_pumpcrete_amount[]" value="<?=$r['pumpcrete_amount']?>" readonly></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><input type="text" style="text-align:right; border:none; border-top:1px solid #000; color:#F00; font-weight:bold; background-color:none;" class="textbox" name="arr_total_amount[]" value="<?=$r['total_amount']?>" readonly></td>
                </tr>
            <?php
                }#end foreach
            }#end if
            ?>
            </tbody>
        </table>  

    </div>
    <?php } #end if ?>

<?php } ?>
<?php
if($_REQUEST['b'] == "Print Preview"){
	echo "	<iframe id='JOframe' name='JOframe' frameborder='0' src='batching_plant/print_premix_quotation.php?premix_quotation_header_id=$aVal[premix_quotation_header_id]' width='100%' height='500'>
			</iframe>";
}
			
?>
</form>
<script type="text/javascript">
jQuery(function(){	

    jQuery("input[name='arr_quantity[]'], input[name='arr_premix_cost[]'], input[name='arr_pumpcrete_cost[]']").keyup(function(){
        var quantity       = parseFloat(jQuery(this).parent().parent().find("input[name='arr_quantity[]']").val());
        var premix_cost    = parseFloat(jQuery(this).parent().parent().find("input[name='arr_premix_cost[]']").val());
        var pumpcrete_cost = parseFloat(jQuery(this).parent().parent().next().find("input[name='arr_pumpcrete_cost[]']").val());

        var premix_amount    = quantity * premix_cost;
        var pumpcrete_amount = pumpcrete_cost * quantity;
        var total_amount     = premix_amount + pumpcrete_amount;

        console.log(pumpcrete_cost);
        
        jQuery(this).parent().parent().find("input[name='arr_premix_amount[]']").val(premix_amount.round(2));
        jQuery(this).parent().parent().next().find("input[name='arr_pumpcrete_amount[]']").val(pumpcrete_amount.round(2));
        jQuery(this).parent().parent().next().next().find("input[name='arr_total_amount[]']").val(total_amount.round(2));

    });

    jQuery("input[name='arr_pumpcrete_cost[]']").keyup(function(){
        var quantity       = parseFloat(jQuery(this).parent().parent().prev().find("input[name='arr_quantity[]']").val());
        var premix_cost    = parseFloat(jQuery(this).parent().parent().prev().find("input[name='arr_premix_cost[]']").val());
        var pumpcrete_cost = parseFloat(jQuery(this).parent().parent().find("input[name='arr_pumpcrete_cost[]']").val());

        var premix_amount    = quantity * premix_cost;
        var pumpcrete_amount = pumpcrete_cost * quantity;
        var total_amount     = premix_amount + pumpcrete_amount;

        console.log(pumpcrete_cost);
        
        jQuery(this).parent().parent().prev().find("input[name='arr_premix_amount[]']").val(premix_amount.round(2));
        jQuery(this).parent().parent().find("input[name='arr_pumpcrete_amount[]']").val(pumpcrete_amount.round(2));
        jQuery(this).parent().parent().next().find("input[name='arr_total_amount[]']").val(total_amount.round(2));

    });


    jQuery("#quantity,#premix_cost,#pumpcrete_cost").keyup(function(){
        var quantity       = parseFloat(jQuery("#quantity").val())
        var premix_cost    = parseFloat(jQuery("#premix_cost").val())
        var pumpcrete_cost = parseFloat(jQuery("#pumpcrete_cost").val())


        var premix_amount    = quantity * premix_cost;
        var pumpcrete_amount = quantity * pumpcrete_cost;
        var total_amount     = premix_amount + pumpcrete_amount;
        //console.log(premix_cost);
        jQuery("#premix_amount").val(premix_amount.round(2))
        jQuery("#pumpcrete_amount").val(pumpcrete_amount.round(2))
        jQuery("#total_amount").val(total_amount.round(2))


    });


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

    jQuery(".pm-ac").autocomplete({
        source: "autocomplete/productmaster.php",
        minLength: 1,
        select: function(event, ui) {
            jQuery(this).val(ui.item.value);
            jQuery(this).next().val(ui.item.stock_id);
            jQuery("#premix_desc").val(ui.item.description)
            jQuery("#premix_cost").val(ui.item.cost)
        }
    });

    <?php if( $aVal['status'] != "S"  ){ ?>
    jQuery(".trash").remove();
    <?php } ?>
});

Number.prototype.round = function(places){
    places = Math.pow(10, places); 
    return Math.round(this * places)/places;
}

</script>
	