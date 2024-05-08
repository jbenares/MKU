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

    if(empty($form_data['preturn_header_id'])){
        $sql = " insert into  preturn_header ";
    } else {
        $sql = " update preturn_header ";
    }


    $sql .= "
        set
            date        = '$form_data[date]',            
            supplier_id = '$form_data[supplier_id]',                        
            remarks     = '".addslashes($form_data['remarks'])."',               
    ";

    if(empty($form_data['preturn_header_id'])){ #insert
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

    if(!empty($form_data['preturn_header_id'])){
        $sql .= "
            where preturn_header_id = '$form_data[preturn_header_id]'
        ";
    }


    mysql_query($sql) or die(mysql_error());
    $preturn_header_id = $_REQUEST['preturn_header_id'] = (!empty($form_data['preturn_header_id'])) ? $form_data['preturn_header_id'] : mysql_insert_id(); 

    return $preturn_header_id;  
}

function addProduct(){

    if( empty($_REQUEST['stock_id']) ){
        return false;
    }

    $sql = "
        insert into
            preturn_detail
        set
            preturn_header_id = '$_REQUEST[preturn_header_id]',
            stock_id          = '$_REQUEST[stock_id]',
            quantity          = '$_REQUEST[quantity]',
            price             = '$_REQUEST[price]',
            amount            = '$_REQUEST[amount]'
    ";

    DB::conn()->query($sql) or die(DB::conn()->error);
}

function displayDetails($id){


    /*RAW MATERIALS DISPLAY*/
    $sql = "
        select
            d.*, stock
        from 
            preturn_detail as d 
        left join productmaster as p on d.stock_id = p.stock_id
        where
            d.preturn_header_id = '$id'
        and preturn_void = '0'        
    ";

    $arr = lib::getArrayDetails($sql);
    echo "
        <table class='table-css' style='vertical-align:top; display:inline-table;' >
            <caption>ITEM DETAILS</caption>
            <thead>
                <tr>
                    <td style='width:10px;'>#</td>
                    <td style='width:10px;'></td>
                    <td>ITEM</td>
                    <td style='text-align:right; width:10%;'>QUANTITY</td>                    
                    <td style='text-align:right; width:10%;'>PRICE</td>                    
                    <td style='text-align:right; width:10%;'>AMOUNT</td>                    
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

            echo "
                <tr>
                    <td>$i</td>
                    <td><a class='trash' href='admin.php?view=$GLOBALS[view]&preturn_header_id=$id&b=rd&id=$r[preturn_detail_id]' onclick='return approve_confirm();'><img src='images/trash.gif'></a></td>
                    <td>$r[stock]</td>
                    <td style='text-align:right;'>".number_format($r['quantity'],2)."</td>
                    <td style='text-align:right;'>".number_format($r['price'],2)."</td>
                    <td style='text-align:right;'>".number_format($r['amount'],2)."</td>
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
                    <td></td>                    
                    <td style='text-align:right;'><span>".number_format($t_amount,2)."</td>
                </tr>
            </tfoot>
        </table>
    ";
}

if( $_REQUEST['b'] == "Submit" ){

    if( $_REQUEST['date'] ){

        $preturn_header_id  =  $_REQUEST['preturn_header_id'] = insertTransactionToDB($_REQUEST);            
        $msg = "Transaction Saved.";    

    } else {
        $msg = "Please fill in date.";
    }
} else if( $_REQUEST['b'] == "Cancel" ){
    
    /*when unfished you are not allowed to add and delete entries in the collections*/
    mysql_query("
        update preturn_header set status = 'C' where preturn_header_id = '$_REQUEST[preturn_header_id]'
    ") or die(mysql_error());

    $msg = "Transaction Cancelled";

} else if( $_REQUEST['b'] == "Add Raw Materials" ){
    $msg = addRawMaterials();
    computeExcessQuantity($_REQUEST['preturn_header_id']);
} else if( $_REQUEST['b'] == "Add Product" ){
    addProduct();
}else if($_REQUEST['b'] == "rd" ){
    $sql = "
        update
            preturn_detail
        set
            preturn_void = '1'
        where
            preturn_detail_id = '$_REQUEST[id]'
    ";
    DB::conn()->query($sql) or die(DB::conn()->error);    
}


if($_REQUEST['preturn_header_id'] && $_REQUEST['b'] != "Search"){
	$result = mysql_query("
		select 
            *
        from 
            preturn_header        
        where preturn_header_id = '$_REQUEST[preturn_header_id]'
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

                        <td>PR #</td>
                        <td><input type="text" class="textbox" name="search_preturn_header_id" id="search_preturn_header_id" value="<?=$_REQUEST['search_preturn_header_id']?>"></td>                        
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
                h.*, account
            from
                preturn_header as h
                left join supplier as s on h.supplier_id = s.account_id
			where
                1=1
        ";
        if( $_REQUEST['search_date'] ) $sql.=" and date = '$_REQUEST[search_date]'";        
        if( $_REQUEST['search_preturn_header_id'] ) $sql.=" and preturn_header_id = '$_REQUEST[search_preturn_header_id]'";

        $sql .= "
            order by date desc
        ";

        $pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
                
        $i=$limitvalue;
        $rs = $pager->paginate();
        
        $pagination	= $pager->renderFullNav("$view&b=Search&search_date=$_REQUEST[search_date]&search_preturn_header_id=$_REQUEST[search_preturn_header_id]");
        ?>
        <div class="pagination">
            <?=$pagination?>
        </div>
        <table width="100%" align="left" style="text-align:left;" class="table-css">
        	<thead>
                <tr>				
                    <td width="20">#</td>
                    <td width="20"></td>                    
                    <td style="text-align:left;">PR#</td>
                    <td style="text-align:left;">DATE</td>                                        
                    <td style="text-align:left;">SUPPLIER</td>                                        
                    <td style="text-align:left;">REMARKS</td>                                                    
                    <td style="text-align:center;">STATUS</td>
                </tr>  
            </thead>
            <tbody>
			<?php								
            while($r=mysql_fetch_assoc($rs)) {
                
                echo '<tr>';
                echo '<td width="20">'.++$i.'</td>';
                echo '<td width="15"><a href="admin.php?view='.$view.'&preturn_header_id='.$r['preturn_header_id'].'" title="Show Details"><img src="images/edit.gif" border="0"></a></td>';                
                echo '<td>'.str_pad($r['preturn_header_id'],7,0,STR_PAD_LEFT).'</td>';
                echo '<td>'.lib::ymd2mdy($r['date']).'</td>';                
                echo "<td>$r[account]</td>";
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

            <input type="hidden" name="preturn_header_id" id="preturn_header_id" value="<?=$aVal['preturn_header_id']?>" />

            <fieldset style="border:none;">
                <p>                
                    <?php if( !empty($aVal['preturn_header_id']) ){ ?>
                    <div>
                        PR#: <br>
                        <span style='font-weight:bold; font-size:14px;'><?=str_pad($aVal['preturn_header_id'], 7,0,STR_PAD_LEFT)?></span>
                    </div>
                    <?php } ?>

                    <?php 
                    if( empty($aVal['date']) ) $aVal['date'] = date("Y-m-d");                      
                    ?>
                    <div class="inline">
                       Date <br>
                        <input type="text" class="textbox datepicker" name="date" id="date" value="<?=$aVal['date']?>"> 
                    </div>

                    
                    <div class="inline">
                        Supplier <br>

                        <input type="text" class="textbox" name="supplier_id_display" value="<?=lib::getAttribute('supplier','account_id',$aVal['supplier_id'],'account')?>" id="supplier_name" onclick="this.select();" />
                        <input type="hidden" name="supplier_id" id="account_id" value="<?=$aVal['supplier_id']?>" title="Please Select Supplier" />                        
                    </div>
                    
                </p>           
                <p>
                    Remarks <br>
                    <textarea style="width:90%; padding:5px; font-size:10px; font-family:arial; border:1px solid #c0c0c0;" name="remarks"><?=$aVal['remarks']?></textarea>    
                </p>
            </fieldset>

            
            <?php  if( $aVal['status'] == "S" ): ?>
                
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
                        
            </fieldset>
            <?php endif; ?>

           	<div>
                <?php if( !in_array($aVal['status'], array("F","C"))){ ?>
                <input type='submit' name='b' value='Submit' class='buttons' id="submit_button">            
                <?php } ?>

                <?php if($_REQUEST['b'] != "Print Preview" && !empty($aVal['status'])){ ?>
                <!-- d -->
                <?php } else if($_REQUEST['b'] == "Print Preview"){ ?>    
                <input type="button" value="Print" onclick="printIframe('JOframe');" /> 
                <?php } ?>

                <?php if( in_array($aVal['status'], array("S"))){ ?>            
                <input type='submit' name='b' value='Cancel' class='buttons' >
                <?php } ?>
                
                <?php if( $aVal['status'] == "S" ){ ?> <!-- <input type='submit' name='b' value='Finish' class='buttons' id="submit_button"> --> <?php } ?>            
            </div>
          
        </div>
        <?php if( $aVal['preturn_header_id'] ){ 
        /*$aVal['status'] = "S";*/
        require_once(dirname(__FILE__).'/transaction-status.php');
        } ?>
        
        <?php if( $aVal['preturn_header_id'] ): ?>
        <div>
            <?=displayDetails($aVal['preturn_header_id']);?>    
        </div>
        <?php endif; ?>
        <?php if($_REQUEST['b'] == "Print Preview" && $aVal['preturn_header_id']){ 
            echo " <iframe id='JOframe' name='JOframe' frameborder='0' src='raw_mat_preturn_header/print_preturn_header.php?id=$aVal[preturn_header_id]' width='100%' height='500'>
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
        jQuery.post("raw_mat_preturn_header/ajax/preturn_header.php", { action : "generateStockID" , data : jQuery("form").serializeObject() }, function(data){
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