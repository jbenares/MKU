<?php require_once(dirname(__FILE__).'/func_'.basename(__FILE__));  ?>

<script type="text/javascript" src="library/js/numeral.min.js"></script>
<script type="text/javascript" src="library/js/serialize_object.js"></script>

<link rel="stylesheet" href="library/lib.css">
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
<?php

/*PLACE ADDITIONAL FUNCITON AT THE TOP*/

$aStatus = array(
    'F' => 'Finished',
    'S' => 'Saved',
    'C' => 'Cancelled'
);


function insertTransactionToDB($form_data){

    if(empty($form_data['gatepass_id'])){
        $sql = " insert into  gatepass ";
    } else {
        $sql = " update gatepass ";
    }


    $sql .= "
        set
            date                  = '$form_data[date]',            
            time                  = '$form_data[time]',            
            employee_id           = '$form_data[employee_id]',                        
            remarks               = '".addslashes($form_data['remarks'])."',               
            project_id            = '$form_data[project_id]',
            visitor               = '$form_data[visitor]',
            reference             = '$form_data[reference]',
			eur_reference         = '$form_data[eur_reference]',
			stock_id              = '$form_data[stock_id]',
            supplier_id           = '$form_data[supplier_id]',
            check_borrowed_items  = '".( ($form_data['check_borrowed_items']) ? 1 : 0 )."',
            check_for_return      = '".( ($form_data['check_for_return']) ? 1 : 0 )."',
            check_for_project_use = '".( ($form_data['check_for_project_use']) ? 1 : 0 )."',
            check_personal_items  = '".( ($form_data['check_personal_items']) ? 1 : 0 )."',
            check_for_repair      = '".( ($form_data['check_for_repair']) ? 1 : 0 )."',
			check_chargeable_items = '".( ($form_data['check_chargeable_items']) ? 1 : 0 )."',
			check_for_official_use = '".( ($form_data['check_for_official_use']) ? 1 : 0 )."',
			check_for_hauling      = '".( ($form_data['check_for_hauling']) ? 1 : 0 )."',
			check_for_rescue       = '".( ($form_data['check_for_rescue']) ? 1 : 0 )."',
			check_purchase        = '".( ($form_data['check_purchase']) ? 1 : 0 )."',
			items_check            = '".( ($form_data['items_check']) ? 1 : 0 )."',
			vehicle_check          = '".( ($form_data['vehicle_check']) ? 1 : 0 )."',
			check_for_pouring       = '".( ($form_data['check_for_pouring']) ? 1 : 0 )."',

    ";

    if(empty($form_data['gatepass_id'])){ #insert
        $sql .= "
            prepared_by   = '$_SESSION[userID]',
            prepared_time = '".lib::now()."'
        ";     
    } else {  #update
        $sql .= "
            edited_by      = '$_SESSION[userID]',
            last_edit_time = '".lib::now()."'
        ";
    }

    if(!empty($form_data['gatepass_id'])){
        $sql .= "
            where gatepass_id = '$form_data[gatepass_id]'
        ";
    }


    mysql_query($sql) or die(mysql_error());
    $gatepass_id = $_REQUEST['gatepass_id'] = (!empty($form_data['gatepass_id'])) ? $form_data['gatepass_id'] : mysql_insert_id(); 

    return $gatepass_id;  
}

function addProduct(){

    if( empty($_REQUEST['stock_id']) ){
        return false;
    }

    $sql = "
        insert into
            gatepass_detail
        set
            gatepass_id = '$_REQUEST[gatepass_id]',
            stock_id    = '$_REQUEST[stock_id]',
            quantity    = '$_REQUEST[quantity]',
            cost        = '$_REQUEST[price]',
            amount      = '$_REQUEST[amount]'
    ";

    DB::conn()->query($sql) or die(DB::conn()->error);
}

function displayDetails($id){


   /*RAW MATERIALS DISPLAY*/
    $sql = "
        select
            d.*, stock
        from 
            gatepass_detail as d 
        left join productmaster as p on d.stock_id = p.stock_id
        where
            d.gatepass_id = '$id'
        and gatepass_void = '0'        
    ";

    $arr = lib::getArrayDetails($sql);
   /* echo "
        <table class='table-css' style='vertical-align:top; display:inline-table;' >
            <caption>ITEM DETAILS</caption>
            <thead>
                <tr>
                    <td style='width:10px;'>#</td>
                    <td style='width:10px;'></td>
                    <td>ITEM</td>
                    <td style='text-align:right; width:10%;'>QUANTITY</td>                    
                    <!--<td style='text-align:right; width:10%;'>COST</td>                    
                    <td style='text-align:right; width:10%;'>AMOUNT</td>-->
                    <td></td>
                </tr>
            </thead>
            <tbody>
        ";
    $t_quantity = $t_amount = 0;
    if( count($arr) ){
        $i = 1;

        foreach ($arr as $r) {

            $t_quantity += $r['quantity'];     
            $t_amount   += $r['amount'];

            $return_btn_value = ( $r['is_returned'] ) ? "Returned" : "Return";
            $return_btn_attr  = ( $r['is_returned'] ) ? "disabled" : "" ;

            echo "
                <tr>
                    <td>$i</td>
                    <td><a class='trash' href='admin.php?view=$GLOBALS[view]&gatepass_id=$id&b=rd&id=$r[gatepass_detail_id]' onclick='return approve_confirm();'><img src='images/trash.gif'></a></td>
                    <td>$r[stock]</td>
                    <td style='text-align:right;'>".number_format($r['quantity'],2)."</td>
                    <!--<td style='text-align:right;'>".number_format($r['cost'],2)."</td>
                    <td style='text-align:right;'>".number_format($r['amount'],2)."</td>-->
                    <td style='text-align:center;'><input type='button' class='return_btn' value='$return_btn_value' data-id='$r[gatepass_detail_id]' $return_btn_attr></td>
                </tr>
            ";

            $i++;
        }
    }

    echo "            
            </tbody>
            <tfoot>
                <tr>
                    <td></td>                    
                    <td></td>                    
                    <td></td>                    
                    <td style='text-align:right;'><span>".number_format($t_quantity,2)."</td>
                    <!--<td></td>                    
                    <td style='text-align:right;'><span>".number_format($t_amount,2)."</td>-->
                    <td></td>                    
                </tr>
            </tfoot>
        </table>
    ";*/
}

if( $_REQUEST['b'] == "Submit" ){

    if( $_REQUEST['date'] ){

        $gatepass_id  =  $_REQUEST['gatepass_id'] = insertTransactionToDB($_REQUEST);            
        $msg = "Transaction Saved.";    

    } else {
        $msg = "Please fill in date.";
    }
} else if( $_REQUEST['b'] == "Cancel" ){
    
    /*when unfished you are not allowed to add and delete entries in the collections*/
    mysql_query("
        update gatepass set status = 'C' where gatepass_id = '$_REQUEST[gatepass_id]'
    ") or die(mysql_error());

    $msg = "Transaction Cancelled";

} else if( $_REQUEST['b'] == "Add Raw Materials" ){
    $msg = addRawMaterials();
    computeExcessQuantity($_REQUEST['gatepass_id']);
} else if( $_REQUEST['b'] == "Add Product" ){
    addProduct();
}else if($_REQUEST['b'] == "rd" ){
    $sql = "
        update
            gatepass_detail
        set
            gatepass_void = '1'
        where
            gatepass_detail_id = '$_REQUEST[id]'
    ";
    DB::conn()->query($sql) or die(DB::conn()->error);    
}


if($_REQUEST['gatepass_id'] && $_REQUEST['b'] != "Search"){
	$result = mysql_query("
		select 
            *
        from 
            gatepass        
        where gatepass_id = '$_REQUEST[gatepass_id]'
	") or die(mysql_error());

	$aVal = mysql_fetch_assoc($result);    

} else {
    $aVal = $_REQUEST;
}
?>
<form enctype='multipart/form-data' method="post" action="" id="form" >
	<div class="home_module_actions">
        <fieldset style="border:1px solid #c0c0c0; margin-bottom:5px;">
            <legend>SEARCH <?=$transac->getMname($view)?></legend>
            <table>
                <tbody>
                    <tr>
                        <td>Date</td>
                        <td><input type="text" class="textbox datepicker" name="search_date" value="<?=$aVal['search_date']?>" /></td>

                        <td>GP#</td>
                        <td><input type="text" class="textbox" name="search_gatepass_id" id="search_gatepass_id" value="<?=$_REQUEST['search_gatepass_id']?>"></td>
						<td>Project</td>
                        <td><input type="text" class="textbox" name="project" id="project" value="<?=$_REQUEST['project']?>"></td>                        
                    </tr>                    
                </tbody>
            </table>
        </fieldset>
    	<input type="submit" name="b" value="Search" />
        <a href="?view=<?=$view?>"><input type="button" name="b" value="New" /></a>
    </div>
	<?php if($_REQUEST['b'] == "Search"): ?>
		<?php
        $page = $_REQUEST['page'];
        if(empty($page)) $page = 1;
         
        $limitvalue = $page * $limit - ($limit);
        
        $sql = "
            select
                h.*, concat(employee_lname,', ',employee_fname) as name
            from
                gatepass as h
                left join employee as e on h.employee_id = e.employeeID
				left join projects as p on h.project_id = p.project_id
			where
                1=1
        ";
        if( $_REQUEST['search_date'] ) $sql.=" and date = '$_REQUEST[search_date]'";        
        if( $_REQUEST['search_gatepass_id'] ) $sql.=" and gatepass_id = '$_REQUEST[search_gatepass_id]'";
		if( $_REQUEST['project'] ) $sql.=" and p.project_name like '%$_REQUEST[project]%'";     
		
        $sql .= "
            order by date desc
        ";
		//echo $sql;
        $pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
                
        $i=$limitvalue;
        $rs = $pager->paginate();
        
        $pagination	= $pager->renderFullNav("$view&b=Search&search_date=$_REQUEST[search_date]&search_gatepass_id=$_REQUEST[search_gatepass_id]");
        ?>
        <div class="pagination">
            <?=$pagination?>
        </div>
        <table width="100%" align="left" style="text-align:left;" class="table-css">
        	<thead>
                <tr>				
                    <td width="20">#</td>
                    <td width="20"></td>                    
                    <td style="text-align:left;">GP#</td>
                    <td style="text-align:left;">DATE</td>                                        
                    <td style="text-align:left;">EMPLOYEE/DRIVER</td>  
					<td style="text-align:left;">PROJECT</td> 
                    <td style="text-align:left;">REMARKS</td>                                                    
                    <td style="text-align:center;">STATUS</td>
                </tr>  
            </thead>
            <tbody>
			<?php								
            while($r=mysql_fetch_assoc($rs)) {
                
                echo '<tr>';
                echo '<td width="20">'.++$i.'</td>';
                echo '<td width="15"><a href="admin.php?view='.$view.'&gatepass_id='.$r['gatepass_id'].'" title="Show Details"><img src="images/edit.gif" border="0"></a></td>';                
                echo '<td>'.str_pad($r['gatepass_id'],7,0,STR_PAD_LEFT).'</td>';
                echo '<td>'.lib::ymd2mdy($r['date'])." $r[time] ".'</td>';                
                echo "<td>$r[name]</td>";
				echo '<td>'.$options->getAttribute('projects','project_id',$r['project_id'],'project_name').'</td>';
                echo '<td>'.$r['remarks'].'</td>';                
                echo '<td style="width:10%; text-align:center;">'.$aStatus[$r['status']].'</td>';                
                echo '</tr>';
            }
            ?>
            </tbody>
        </table>
        <div class="pagination">
             <?=$pagination?>
        </div>        
    <?php else: #end else?>
        <div class="module_actions">
            <?php if(!empty($msg)) echo '<div onclick="jQuery(this).hide(500);" id="status_update" class="ui-state-highlight ui-corner-all" style="padding: 0px 0.7em; text-align:left;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right:.3em;"></span> '.$msg.'</p></div>'; ?>
            <div class="module_title"><img src='images/user_orange.png'><?=strtoupper($transac->getMname($view))?></div>

            <input type="hidden" name="gatepass_id" id="gatepass_id" value="<?=$aVal['gatepass_id']?>" />

            <fieldset style="border:none;">
                <p>                
                    <?php if( !empty($aVal['gatepass_id']) ){ ?>
                    <div>
                        GP#: <br>
                        <span style='font-weight:bold; font-size:14px;'><?=str_pad($aVal['gatepass_id'], 7,0,STR_PAD_LEFT)?></span>
                    </div>
                    <?php } ?>

                    <?php 
                    if( empty($aVal['date']) ) $aVal['date'] = date("Y-m-d");                      
                    ?>
                    <div class="inline">
                        Date <br>
                        <input type="text" class="textbox3 datepicker" name="date" id="date" value="<?=$aVal['date']?>"> 
                    </div>
                
                    <!--<div class="inline">
                        Time <em>(HH:mm:ss)</em><br>
                        <input type="text" class="textbox3" name="time" id="time" value="<?=$aVal['time']?>"> 
                    </div>-->

                    <div class="inline">
                        Project <br>
                        <input type="text" class="textbox project" value="<?=lib::getAttribute('projects','project_id',$aVal['project_id'],'project_name')?>">
                        <input type="hidden" name="project_id" value="<?=$aVal['project_id']?>" >
                    </div>
                    
                    <div class="inline">                       
                        Employee <br>
                        <input type="text" class="textbox ac-employee" value="<?=lib::getEmployeeName($aVal['employee_id'])?>">
                        <input type="hidden" name="employee_id" value="<?=$aVal['employee_id']?>" >
                    </div>


                    <div class="inline">                       
                        Supplier <br>
                        <?=lib::getTableAssoc($aVal['supplier_id'],'supplier_id','Select Supplier',"select * from supplier order by account asc",'account_id','account')?>
                    </div>
                </p>
                <p>
                    <div class="inline">
                       Reference<br>
                        <input type="text" class="textbox3" name="reference" id="visitor" value="<?=$aVal['reference']?>"> <br>
                    </div>
                    <div class="inline">
                       Visitor <br>
                        <input type="text" class="textbox" name="visitor" id="reference" value="<?=$aVal['visitor']?>"> 
                    </div>
					<div class="inline">
					Vehicle:<br>
                        
                            <input type="text" class="textbox stock_name" value="<?=lib::getAttribute('productmaster','stock_id',$aVal['stock_id'],'stock')?>" onclick="this.select();" />
                            <input type="hidden" name="stock_id" value="<?=$aVal['stock_id']?>"  />
                       </div>
					    <div class="inline">
                        E.U.R. Reference<br>
                        <input type="text" class="textbox3" name="eur_reference" id="eur_reference" value="<?=$aVal['eur_reference']?>"> 
                    </div>
					<p>
                </p>
               <!-- <p>
                    Remarks <br>
                    <textarea style="width:90%; padding:5px; font-size:10px; font-family:arial; border:1px solid #c0c0c0;" name="remarks"><?=$aVal['remarks']?></textarea>    
                </p>-->
				 <p>                    
                    <table style="width:100%;">
                        <tbody>
                            <tr>
                                  <td><input type="checkbox" name="items_check" value="1" <?php if( $aVal['items_check'] ) echo "checked"; ?>>Please check for items</td>                   
                            </tr>
							 <tr>
                                <td><input type="checkbox" name="vehicle_check" value="1" <?php if( $aVal['vehicle_check'] ) echo "checked"; ?>>Please check for vehicle</td>                        
                            </tr>
                    
                        </tbody>
                    </table>
                </p>

                <p>                    
                    <table style="width:100%;">
                        <tbody>
                            <tr>
                                <td>Purpose:</td>
                                  <td><input type="checkbox" name="check_for_repair" value="1" <?php if( $aVal['check_for_repair'] ) echo "checked"; ?>>For Repair/Sample</td> 
                                <td><input type="checkbox" name="check_personal_items" value="1" <?php if( $aVal['check_personal_items'] ) echo "checked"; ?>>Personal Items/Use</td>                        
                            </tr>
                            <tr>
                                <td></td>
                                <td><input type="checkbox" name="check_for_return" value="1" <?php if( $aVal['check_for_return'] ) echo "checked"; ?>>For Return/Exchange</td>
                                <td><input type="checkbox" name="check_chargeable_items" value="1" <?php if( $aVal['check_chargeable_items'] ) echo "checked"; ?>>Chargeable/Purchased Items</td>                        
                            </tr>
                            <tr>
                                <td></td>
                                <td><input type="checkbox" name="check_for_project_use" value="1" <?php if( $aVal['check_for_project_use'] ) echo "checked"; ?>>For Project Use</td>
                                 <td><input type="checkbox" name="check_borrowed_items" value="1" <?php if( $aVal['check_borrowed_items'] ) echo "checked"; ?> >Borrowed Items</td>								
                            </tr>
							<tr>
                                <td></td>
                                <td><input type="checkbox" name="check_for_official_use" value="1" <?php if( $aVal['check_for_official_use'] ) echo "checked"; ?>>Official Use</td>
                                <td><input type="checkbox" name="check_purchase" value="1" <?php if( $aVal['check_purchase'] ) echo "checked"; ?> >Emission</td>								
                            </tr>
							<tr>
                                <td></td>
                                <td><input type="checkbox" name="check_for_hauling" value="1" <?php if( $aVal['check_for_hauling'] ) echo "checked"; ?>>For Hauling/Pick-up</td>
                                 <td><input type="checkbox" name="check_for_rescue" value="1" <?php if( $aVal['check_for_rescue'] ) echo "checked"; ?> >For Rescue</td>								
                            </tr>
							<tr>
                                <td></td>
                                <td><input type="checkbox" name="check_for_pouring" value="1" <?php if( $aVal['check_for_pouring'] ) echo "checked"; ?> >Pouring</td>
								<td></td>
                            </tr>
                        </tbody>
                    </table>
                </p>
            </fieldset>

            
            <?php  if( $aVal['status'] == "S" ): ?>
            

           <!-- <fieldset style="border:none; border:1px solid #c0c0c0; display:inline-block;">
                <legend>STOCKS TRANSFER REFERENCE</legend>
                                                    
                    <div style="display:inline-block;">
                        Stocks Transfer # : <br>
                        <input type="text" class="textbox3" name="transfer_header_id" id="transfer_header_id"  onkeypress="if(event.keyCode==13){ jQuery('#search_btn').click(); return false; }">
                    </div>                

                    <input type="button" value="Search" name="search_btn" id="search_btn">
                        
            </fieldset>

            <fieldset style="border:none; border:1px solid #c0c0c0; display:inline-block;">
                <legend>ITEM ENTRY</legend>
                                    
                    <div style="display:inline-block;">
                        Item : <br>
                        <input type="text" class="textbox ac-stock"  value="" onkeypress="if(event.keyCode==13){ jQuery('#quantity').focus(); return false; }" autofocus >
                        <input type="hidden" name="stock_id" id="stock_id">                        
                    </div>

                    <div style="display:inline-block;">
                        Quantity : <br>
                        <input type="text" class="textbox3 quantity" name="quantity" id="quantity"  onkeypress="if(event.keyCode==13){ jQuery('#price').focus(); return false; }">
                    </div>

                     <div style="display:inline-block;">
                        Price : <br>
                        <input type="text" class="textbox3 price" name="price" id="price"  onkeypress="if(event.keyCode==13){ jQuery('#product_btn').click(); return false; }">
                    </div>

                    <div style="display:inline-block;">
                        Amount : <br>
                        <input type="text" class="textbox3 amount" name="amount"  onkeypress="if(event.keyCode==13){ jQuery('#product_btn').click(); return false; }" readonly >
                    </div> 

                    <input type="submit" value="Add Product" name="b" id="product_btn">
                        
            </fieldset-->
            <?php endif; ?>

           	<div>
                <?php if( !in_array($aVal['status'], array("F","C"))){ ?>
                <input type='submit' name='b' value='Submit' class='buttons' id="submit_button">            
                <?php } ?>

                <?php if($_REQUEST['b'] != "Print Preview" && !empty($aVal['status'])){ ?>                
                <input type="submit" name="b" id="b" value="Print Preview" />
                <?php } else if($_REQUEST['b'] == "Print Preview"){ ?>    
                <input type="button" value="Print" onclick="printIframe('JOframe');" /> 
                <?php } ?>

                <?php if( in_array($aVal['status'], array("S"))){ ?>            
                <input type='submit' name='b' value='Cancel' class='buttons' >
                <?php } ?>
                
                <?php if( $aVal['status'] == "S" ){ ?> <!-- <input type='submit' name='b' value='Finish' class='buttons' id="submit_button"> --> <?php } ?>            
            </div>
          
        </div>
        <?php if( $aVal['gatepass_id'] ){ 
        /*$aVal['status'] = "S";*/
        require_once(dirname(__FILE__).'/transaction-status.php');
        } ?>
        
       <?php if( $aVal['gatepass_id'] ): ?>
        <div>
            <?=displayDetails($aVal['gatepass_id']);?>    
        </div>
        <?php endif; ?>
        <?php if($_REQUEST['b'] == "Print Preview" && $aVal['gatepass_id']){ 
            echo " <iframe id='JOframe' name='JOframe' frameborder='0' src='additional_transac/print_gatepass.php?id=$aVal[gatepass_id]' width='100%' height='500'>
            </iframe>";    
        } 
        ?>
    <?php endif; #end if ?>    
</form>
<script>    
    <?php if( $aVal['status'] == "C" ){ ?>
        jQuery(".trash").remove();        
    <?php } ?>


    jQuery(".ac-employee").autocomplete({
        source: "autocomplete/employees.php",
        minLength: 2,
        select: function(event, ui) {
            jQuery(this).val(ui.item.value);
            jQuery(this).next().val(ui.item.id);
        }
    });

    
    jQuery(".ac-stock").autocomplete({
        source: "autocomplete/productmaster.php",
        minLength: 1,
        select: function(event, ui) {
            jQuery(this).val(ui.item.value);
            jQuery(this).next().val(ui.item.stock_id);     

            jQuery("#quantity").focus();
        }
    });

    jQuery(".quantity,.price").keyup(function(){

        var quantity = parseFloat(jQuery(this).parent().parent().find(".quantity").val());
        var price = parseFloat(jQuery(this).parent().parent().find(".price").val());

        var amount = parseFloat(price * quantity);
        jQuery(this).parent().parent().find(".amount").val(numeral(amount).format('0.00'));
    });

    jQuery("input[type='text']").keypress(function(e){
        if( e.keyCode == 13 ){        
            return false;
        }
    });

    jQuery("#generate_btn").click(function(){
        jQuery.post("raw_mat_gatepass/ajax/gatepass.php", { action : "generateStockID" , data : jQuery("form").serializeObject() }, function(data){
            //actions
            var obj = JSON.parse(data);
            if( obj.error_flag == 1 ){
                alert(obj.error);
                return false;
            }

            jQuery("#excess_stock_id").val(obj.stock.stock_id);
            jQuery("#excess_stock_name").val(obj.stock.stock);
            
        });
    });

    jQuery("#search_btn").click(function(){
        displayGroupAddForm(this);
    });

    jQuery(".return_btn").click(function(){
        var e = jQuery(this);
        var form_data = {
            id : jQuery(this).data("id")
        };

        if( confirm("Confirm return item ? ") ){
            jQuery.post('<?=("additional_transac/func_".basename(__FILE__) )?>', { action : "returnItem", data : form_data }, function(data){            

                jQuery(e).attr("disabled","disabled");
                jQuery(e).val("Returned");
                            
            });           

        }
    });

    function displayGroupAddForm(e){

        var form_data = {            
            transfer_header_id : jQuery("#transfer_header_id").val(),
            gatepass_id : jQuery("#gatepass_id").val()
        }

        jQuery.post('<?=("additional_transac/func_".basename(__FILE__) )?>', { action : "displayGroupAddForm", data : form_data }, function(data){            

            obj = JSON.parse(data);

            if( obj.error_status == 1 ){
                alert(obj.error_msg);                
            } else {                
                jQuery("#dialog_content").html(obj.html);
                openDialog();

                jQuery("#dialog_form").attr("onsubmit","");                
                jQuery("#dialog_form").attr("method","post");                

            }
            
            
        });

    }

    function computeAmount(e){
        var quantity = parseFloat(jQuery(e).parent().parent().find(".quantity").val());
        var price = parseFloat(jQuery(e).parent().parent().find(".price").val());
        if( isNaN(quantity) ) quantity = 0;
        if( isNaN(price) ) price = 0;

        var amount = parseFloat(price * quantity);
        jQuery(e).parent().parent().find(".amount").val(numeral(amount).format('0.00'));
    }
   
</script>