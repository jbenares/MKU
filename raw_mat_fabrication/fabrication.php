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

if( $_REQUEST['action'] ==  "computeExcessQuantity" ){
    computeExcessQuantity($_REQUEST['fabrication_id']);
}

function getNumberOfRawMaterials($fabrication_id){
    return DB::conn()->query("select *  from fabrication_raw_mat where fabrication_id = '$fabrication_id' and raw_mat_void = '0' ")->num_rows;

}

function insertTransactionToDB($form_data){

    if(empty($form_data['fabrication_id'])){
        $sql = " insert into  fabrication ";
    } else {
        $sql = " update fabrication ";
    }


    $sql .= "
        set
            date            = '$form_data[date]',            
            from_project_id = '$form_data[from_project_id]',
            to_project_id   = '$form_data[to_project_id]',
            excess_stock_id = '$form_data[excess_stock_id]',
            excess_quantity = '$form_data[excess_quantity]',            
            remarks         = '".addslashes($form_data['remarks'])."',               
    ";

    if(empty($form_data['fabrication_id'])){ #insert
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

    if(!empty($form_data['fabrication_id'])){
        $sql .= "
            where fabrication_id = '$form_data[fabrication_id]'
        ";
    }

    mysql_query($sql) or die(mysql_error());
    $fabrication_id = (!empty($form_data['fabrication_id'])) ? $form_data['fabrication_id'] : mysql_insert_id(); 

    return $fabrication_id;  
}

function addRawMaterials(){
    $options = new options();

    if( empty($_REQUEST['raw_mat_stock_id']) ){
        return false;
    }
    /*echo "<pre>";
    print_r($_REQUEST);
    echo "</pre>";*/

    /*check for available quantity*/
    $project_warehouse_qty = $options->inventory_projectwarehousebalance($_REQUEST['date'],$_REQUEST['raw_mat_stock_id'],$_REQUEST['from_project_id']);  
    
    if( $_REQUEST['raw_mat_quantity'] > $project_warehouse_qty ){
        return "Unable to add item. Only $project_warehouse_qty is/are available";
    }

    $sql = "
        insert into
            fabrication_raw_mat
        set
            fabrication_id          = '$_REQUEST[fabrication_id]',
            raw_mat_stock_id        = '$_REQUEST[raw_mat_stock_id]',
            raw_mat_quantity        = '$_REQUEST[raw_mat_quantity]',
            raw_mat_weight_per_unit = '$_REQUEST[raw_mat_weight_per_unit]',
            raw_mat_total_weight    = '$_REQUEST[raw_mat_total_weight]'
    ";

    DB::conn()->query($sql) or die(DB::conn()->error);
    $msg = "Successfully added and item.";
}

function addProduct(){

    if( empty($_REQUEST['product_stock_id']) ){
        return false;
    }

    $sql = "
        insert into
            fabrication_product
        set
            fabrication_id          = '$_REQUEST[fabrication_id]',
            product_stock_id        = '$_REQUEST[product_stock_id]',
            product_quantity        = '$_REQUEST[product_quantity]',    
            product_weight_per_unit = '$_REQUEST[product_weight_per_unit]',    
            product_total_weight    = '$_REQUEST[product_total_weight]',
            clr_no                  = '$_REQUEST[clr_no]'    
    ";

    DB::conn()->query($sql) or die(DB::conn()->error);
}

function computeExcessQuantity($id){
    $sql = "
        select
            *
        from
            fabrication_raw_mat as f
            inner join productmaster as p on f.raw_mat_stock_id = p.stock_id
        where
            fabrication_id = '$id'
        and raw_mat_void = '0'
    ";

    $obj_raw_mat            = DB::conn()->query($sql)->fetch_object();
    $raw_mat_length         = $obj_raw_mat->stock_length;
    $raw_mat_weight         = $obj_raw_mat->kg;
    if( $raw_mat_weight == 0 ) $raw_mat_nominal_weight = 0;
    else  $raw_mat_nominal_weight = $raw_mat_weight / $raw_mat_length;
    $raw_mat_quantity       = $obj_raw_mat->raw_mat_quantity;


    $sql =  "
        select
            stock_length, product_quantity
        from 
            fabrication_product as f
            inner join productmaster as p on f.product_stock_id = p.stock_id
        where
            fabrication_id = '$id'
        and product_void = '0'

    ";
    $arr = lib::getArrayDetails($sql);
    $excess_length = $raw_mat_length;

    if( count($arr) ){
        foreach ($arr as $r) {
            $product_length = $r['stock_length'];
            if( $raw_mat_quantity == 0 ) $ratio = 0;
            else $ratio = $r['product_quantity'] / $raw_mat_quantity;
            $excess_length -= ($product_length * $ratio);
        }
    }

    $excess_quantity     = $raw_mat_quantity;
    $excess_total_weight = $raw_mat_quantity * $excess_length * $raw_mat_nominal_weight;
    $excess_weight_per_unit = $raw_mat_nominal_weight * $excess_length;

    DB::conn()->query("
        update
            fabrication
        set
            excess_quantity        = '$excess_quantity',
            excess_total_weight    = '$excess_total_weight',
            excess_weight_per_unit = '$excess_weight_per_unit',
            excess_length          = '$excess_length'
        where 
            fabrication_id = '$id'
    ");

}

function displayDetails($id){


    /*RAW MATERIALS DISPLAY*/
    $sql = "
        select
            d.*, stock
        from 
            fabrication_raw_mat as d 
        left join productmaster as p on d.raw_mat_stock_id = p.stock_id
        where
            d.fabrication_id = '$id'
        and raw_mat_void = '0'        
    ";

    $arr = lib::getArrayDetails($sql);
    echo "
        <table class='table-css' style='vertical-align:top; width:45%; display:inline-table;' >
            <caption>RAW MATERIALS</caption>
            <thead>
                <tr>
                    <td style='width:10px;'>#</td>
                    <td style='width:10px;'></td>
                    <td>ITEM</td>
                    <td style='text-align:right; width:10%;'>QUANTITY</td>                    
                    <td style='text-align:right; width:10%;'>WEIGHT</td>                    
                    <td style='text-align:right; width:10%;'>TOTAL WEIGHT</td>                    
                </tr>
            </thead>
            <tbody>
        ";

    $t_quantity = $t_amount = $t_total_weight = 0;
    if( count($arr) ){
        $i = 1;

        foreach ($arr as $r) {

            $t_quantity     += $r['raw_mat_quantity'];     
            $t_total_weight += $r['raw_mat_total_weight'];
            echo "
                <tr>
                    <td>$i</td>
                    <td><a class='trash' href='admin.php?view=$GLOBALS[view]&fabrication_id=$id&b=rd&id=$r[fabrication_raw_mat_id]' onclick='return approve_confirm();'><img src='images/trash.gif'></a></td>
                    <td>$r[stock]</td>
                    <td style='text-align:right;'>".number_format($r['raw_mat_quantity'],4)."</td>
                    <td style='text-align:right;'>".number_format($r['raw_mat_weight_per_unit'],4)."</td>
                    <td style='text-align:right;'>".number_format($r['raw_mat_total_weight'],4)."</td>
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
                    <td style='text-align:right;'><span>".number_format($t_quantity,4)."</td>
                    <td></td>                    
                    <td style='text-align:right;'><span>".number_format($t_total_weight,4)."</td>
                </tr>
            </tfoot>
        </table>
    ";

    /*FINISHED PRODUCT DISPLAY*/
    $sql = "
        select
            d.*, stock
        from 
            fabrication_product as d 
        left join productmaster as p on d.product_stock_id = p.stock_id
        where
            d.fabrication_id = '$id'
        and product_void = '0'        
    ";

    $arr = lib::getArrayDetails($sql);
    echo "
        <table class='table-css' style='display:inline-table; vertical-align:top; width:45%;' >
            <caption>FINISHED PRODUCT MATERIALS</caption>
            <thead>
                <tr>
                    <td style='width:3%;'>#</td>
                    <td style='width:3%;'></td>
                    <td>CLR#</td>
                    <td>ITEM</td>
                    <td style='text-align:right;'>QUANTITY</td>                    
                    <td style='text-align:right;'>WEIGHT</td>                    
                    <td style='text-align:right;'>TOTAL WEIGHT</td>                    
                </tr>
            </thead>
            <tbody>
        ";

    $t_quantity = $t_amount = $t_total_weight = 0;
    if( count($arr) ){
        $i = 1;

        foreach ($arr as $r) {

            $t_quantity     += $r['product_quantity'];
            $t_total_weight += $r['product_total_weight'];

            echo "
                <tr>
                    <td>$i</td>
                    <td><a class='trash' href='admin.php?view=$GLOBALS[view]&fabrication_id=$id&b=pd&id=$r[fabrication_product_id]' onclick='return approve_confirm();'><img src='images/trash.gif'></a></td>
                    <td>$r[clr_no]</td>
                    <td>$r[stock]</td>
                    <td style='text-align:right;'>".number_format($r['product_quantity'],4)."</td>
                    <td style='text-align:right;'>".number_format($r['product_weight_per_unit'],4)."</td>
                    <td style='text-align:right;'>".number_format($r['product_total_weight'],4)."</td>
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
                    <td></td>
                    <td style='text-align:right;'><span>".number_format($t_quantity,4)."</td>
                    <td></td>
                    <td style='text-align:right;'><span>".number_format($t_total_weight,4)."</td>
                </tr>
            </tfoot>
        </table>
    ";
    
}

if( $_REQUEST['b'] == "Submit" ){

    if( $_REQUEST['date'] ){

        $fabrication_id  =  $_REQUEST['fabrication_id'] = insertTransactionToDB($_REQUEST);            
        $msg = "Transaction Saved.";    

    } else {
        $msg = "Please fill in date.";
    }
} else if( $_REQUEST['b'] == "Cancel" ){
    
    /*when unfished you are not allowed to add and delete entries in the collections*/
    mysql_query("
        update fabrication set status = 'C' where fabrication_id = '$_REQUEST[fabrication_id]'
    ") or die(mysql_error());

    $msg = "Transaction Cancelled";

} else if( $_REQUEST['b'] == "Add Raw Materials" ){
    $msg = addRawMaterials();
    computeExcessQuantity($_REQUEST['fabrication_id']);
} else if( $_REQUEST['b'] == "Add Product" ){
    addProduct();
    computeExcessQuantity($_REQUEST['fabrication_id']);
}else if($_REQUEST['b'] == "rd" ){
    $sql = "
        update
            fabrication_raw_mat
        set
            raw_mat_void = '1'
        where
            fabrication_raw_mat_id = '$_REQUEST[id]'
    ";
    DB::conn()->query($sql) or die(DB::conn()->error);
    computeExcessQuantity($_REQUEST['fabrication_id']);

} else if($_REQUEST['b'] == "pd" ){

    $sql = "
        update
            fabrication_product
        set
            product_void = '1'
        where
            fabrication_product_id = '$_REQUEST[id]'
    ";
    DB::conn()->query($sql) or die(DB::conn()->error);
    computeExcessQuantity($_REQUEST['fabrication_id']);
}


if($_REQUEST['fabrication_id'] && $_REQUEST['b'] != "Search"){
	$result = mysql_query("
		select 
            *
        from 
            fabrication        
        where fabrication_id = '$_REQUEST[fabrication_id]'
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

                        <td>FAB #</td>
                        <td><input type="text" class="textbox" name="search_fabrication_id" id="search_fabrication_id" value="<?=$_REQUEST['search_fabrication_id']?>"></td>                        
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
                f.*, to_project.project_name as to_project_name
            from
                fabrication as f
                left join projects as to_project on f.to_project_id = to_project.project_id
			where
                1=1
        ";
        if( $_REQUEST['search_date'] ) $sql.=" and date like '%$_REQUEST[search_date]%'";        
        if( $_REQUEST['search_fabrication_id'] ) $sql.=" and fabrication_id = '$_REQUEST[search_fabrication_id]'";

        $sql .= "
            order by date desc
        ";

        $pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
                
        $i=$limitvalue;
        $rs = $pager->paginate();
        
        $pagination	= $pager->renderFullNav("$view&b=Search&search_date=$_REQUEST[search_date]&search_fabrication_id=$_REQUEST[search_fabrication_id]");
        ?>
        <div class="pagination">
            <?=$pagination?>
        </div>
        <table width="100%" align="left" style="text-align:left;" class="table-css">
        	<thead>
                <tr>				
                    <td width="20">#</td>
                    <td width="20"></td>                    
                    <td style="text-align:left;">FAB#</td>
                    <td style="text-align:left;">DATE</td>                                        
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
                echo '<td width="15"><a href="admin.php?view='.$view.'&fabrication_id='.$r['fabrication_id'].'" title="Show Details"><img src="images/edit.gif" border="0"></a></td>';                
                echo '<td>'.str_pad($r['fabrication_id'],7,0,STR_PAD_LEFT).'</td>';
                echo '<td>'.lib::ymd2mdy($r['date']).'</td>';                
                echo "<td>$r[to_project_name]</td>";
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

            <input type="hidden" name="fabrication_id" id="fabrication_id" value="<?=$aVal['fabrication_id']?>" />

            <fieldset style="border:none;">
                <p>                
                    <?php if( !empty($aVal['fabrication_id']) ){ ?>
                    <div>
                        FAB#: <br>
                        <span style='font-weight:bold; font-size:14px;'><?=str_pad($aVal['fabrication_id'], 7,0,STR_PAD_LEFT)?></span>
                    </div>
                    <?php } ?>

                    <?php 
                    if( empty($aVal['date']) ) $aVal['date'] = date("Y-m-d");                      
                    ?>
                    <div class="inline">
                       Date <br>
                        <input type="text" class="textbox datepicker" name="date" id="date" value="<?=$aVal['date']?>"> 
                    </div>

                    <?php
                    if(empty($aVal['from_project_id'])) $aVal['from_project_id'] = 80;
                    ?>

                    <div class="inline">
                        Fabrication Department <br>
                        <input type="text" class="textbox project" value="<?=lib::getAttribute('projects','project_id',$aVal['from_project_id'],'project_name')?>">
                        <input type="hidden" name="from_project_id" value="<?=$aVal['from_project_id']?>" >
                    </div>

                    <div class="inline">
                        To Project <br>
                        <input type="text" class="textbox project" value="<?=lib::getAttribute('projects','project_id',$aVal['to_project_id'],'project_name')?>">
                        <input type="hidden" name="to_project_id" value="<?=$aVal['to_project_id']?>" >
                    </div>
                    
                </p>           
                <p>
                    Remarks <br>
                    <textarea style="width:90%; padding:5px; font-size:10px; font-family:arial; border:1px solid #c0c0c0;" name="remarks"><?=$aVal['remarks']?></textarea>    
                </p>
            </fieldset>

            
            <?php  if( $aVal['status'] == "S" ): ?>

            <fieldset style="border:1px solid #c0c0c0;">
                <legend>Waste Cut Details</legend>
                <div class="inline">
                    Waste Cut Material <br>
                    <input type="text" class="textbox ac-stock" id="excess_stock_name" value='<?=lib::getAttribute('productmaster','stock_id',$aVal['excess_stock_id'],'stock')?>'>
                    <input type="hidden" name="excess_stock_id" id="excess_stock_id" value="<?=$aVal['excess_stock_id']?>">
                </div>

                <div class="inline" >
                    Waste Cut Length <br>
                    <input type="text" class="textbox3" name="excess_length" id="excess_length" value="<?=$aVal['excess_length']?>" readonly >
                </div>

                <div class="inline" >
                    Waste Cut Quantity <br>
                    <input type="text" class="textbox3" name="excess_quantity" id="excess_quantity" value="<?=$aVal['excess_quantity']?>" readonly >
                </div>

                <div class="inline" >
                    Waste Cut Weight <br>
                    <input type="text" class="textbox3" name="excess_weight_per_unit" id="excess_weight_per_unit" value="<?=$aVal['excess_weight_per_unit']?>" readonly >
                </div>

                <div class="inline" >
                    Waste Cut Total Weight <br>
                    <input type="text" class="textbox3" name="excess_total_weight" id="excess_total_weight" value="<?=$aVal['excess_total_weight']?>" readonly >
                </div>

                <input type="button" id="generate_btn"  value="Generate Waste Cut Item">
            </fieldset>
            <?php if( getNumberOfRawMaterials($aVal['fabrication_id']) <= 0){ ?>
            <fieldset style="border:none; border:1px solid #c0c0c0; display:inline-block;">
                <legend>RAW MATERIALS ENTRY</legend>
                
                    <div style="display:inline-block;">
                        Item : <br>
                        <input type="text" class="textbox ac-stock"  value="" onkeypress="if(event.keyCode==13){ return false; }" autofocus>
                        <input type="hidden" name="raw_mat_stock_id" id="raw_mat_stock_id">
                        <input type="hidden" class="length" >
                    </div>

                    <div style="display:inline-block;">
                        Quantity : <br>
                        <input type="text" class="textbox3 quantity" name="raw_mat_quantity"  onkeypress="if(event.keyCode==13){ jQuery('#raw_mat_weight_per_unit').focus(); return false; }">
                    </div>

                    <div style="display:inline-block;">
                        Weight (kg) : <br>
                        <input type="text" class="textbox3 weight" name="raw_mat_weight_per_unit"  id="raw_mat_weight_per_unit"  onkeypress="if(event.keyCode==13){ jQuery('#add_raw_mat_btn').click(); return false; }">
                    </div>

                    <div style="display:inline-block;">
                        Total Weight (kg) : <br>
                        <input type="text" class="textbox3 total_weight" name="raw_mat_total_weight"  onkeypress="if(event.keyCode==13){ jQuery('#add_raw_mat_btn').click(); return false; }" readonly >
                    </div>
                    
                    <input type="submit" value="Add Raw Materials" name="b" id="add_raw_mat_btn">                                    
            </fieldset>
            <?php } ?>

            <fieldset style="border:none; border:1px solid #c0c0c0; display:inline-block;">
                <legend>FINISHED PRODUCT ENTRY</legend>

                    <div style="display:inline-block;">
                        CLR#: <br>
                        <input type="text" class="textbox3" name="clr_no" id="clr_no">
                    </div>
                
                    <div style="display:inline-block;">
                        Item : <br>
                        <input type="text" class="textbox ac-stock"  value="" onkeypress="if(event.keyCode==13){ return false; }">
                        <input type="hidden" name="product_stock_id" id="product_stock_id">
                        <input type="hidden" class="length" >
                    </div>
                    <div style="display:inline-block;">
                        Quantity : <br>
                        <input type="text" class="textbox3 quantity" name="product_quantity"  onkeypress="if(event.keyCode==13){ jQuery('#product_weight_per_unit').focus(); return false; }">
                    </div>

                    <div style="display:inline-block;">
                        Weight (kg) : <br>
                        <input type="text" class="textbox3 weight" name="product_weight_per_unit" id="product_weight_per_unit"  onkeypress="if(event.keyCode==13){ jQuery('#add_product_btn').click(); return false; }">
                    </div>

                    <div style="display:inline-block;">
                        Total Weight (kg) : <br>
                        <input type="text" class="textbox3 total_weight" name="product_total_weight"  onkeypress="if(event.keyCode==13){ jQuery('#product_btn').click(); return false; }" readonly >
                    </div>

                    <input type="submit" value="Add Product" name="b" id="add_product_btn">
                        
            </fieldset>
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
        <?php if( $aVal['fabrication_id'] ){ 
        /*$aVal['status'] = "S";*/
        require_once(dirname(__FILE__).'/transaction-status.php');
        } ?>
        
        <?php if( $aVal['fabrication_id'] ): ?>
        <div>
            <?=displayDetails($aVal['fabrication_id']);?>    
        </div>
        <?php endif; ?>
        <?php if($_REQUEST['b'] == "Print Preview" && $aVal['fabrication_id']){ 
            echo " <iframe id='JOframe' name='JOframe' frameborder='0' src='raw_mat_fabrication/print_fabrication.php?id=$aVal[fabrication_id]' width='100%' height='500'>
            </iframe>";    
        } 
        ?>
    <?php endif; #end if ?>    
</form>
<script>    
    <?php if( $aVal['status'] == "C" ){ ?>
        jQuery(".trash").remove();        
    <?php } ?>
    
    jQuery(".ac-stock").autocomplete({
        source: "autocomplete/productmaster.php",
        minLength: 1,
        select: function(event, ui) {
            jQuery(this).val(ui.item.value);
            jQuery(this).next().val(ui.item.stock_id);             
            jQuery(this).next().next().val(ui.item.stock_length);         
            

            jQuery(this).parent().parent().find(".weight").val(ui.item.kg);

            var length = numeral(jQuery(this).parent().parent().find(".length").val()).format('0.0000');
            var quantity = numeral(jQuery(this).parent().parent().find(".quantity").val()).format('0.0000');
            var total_weight = numeral(parseFloat(quantity) * parseFloat(ui.item.kg)).format('0.0000');

            jQuery(this).parent().parent().find(".total_weight").val(total_weight);
        }
    });

    jQuery(".quantity,.weight").keyup(function(){

        var quantity = parseFloat(jQuery(this).parent().parent().find(".quantity").val());
        var length = parseFloat(jQuery(this).parent().parent().find(".length").val());
        var weight = parseFloat(jQuery(this).parent().parent().find(".weight").val());

        var total_weight = parseFloat(weight * quantity);
        jQuery(this).parent().parent().find(".total_weight").val(numeral(total_weight).format('0.0000'));
    });

    jQuery("input[type='text']").keypress(function(e){
        if( e.keyCode == 13 ){        
            return false;
        }
    });

    jQuery("#generate_btn").click(function(){
        jQuery.post("raw_mat_fabrication/ajax/fabrication.php", { action : "generateStockID" , data : jQuery("form").serializeObject() }, function(data){
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
   
</script>