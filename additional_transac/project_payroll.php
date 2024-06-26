<?php require_once('additional_transac/func_gatepass.php');  ?>

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
/*BENARES*/
/*PLACE ADDITIONAL FUNCITON AT THE TOP*/

$aStatus = array(
    'F' => 'Finished',
    'S' => 'Saved',
    'C' => 'Cancelled'
);

$query_foreman= mysql_query("select
                 *
             from
                 employee
             where
                 employee_type_id='8'");
         
function insertTransactionToDB($form_data){

    if(empty($form_data['project_payroll_header_id'])){
        $sql = " insert into  project_payroll_header ";
    } else {
        $sql = " update project_payroll_header ";
    }


    $sql .= "
        set
            date       = '$form_data[date]',            
            project_id = '$form_data[project_id]', 
            foreman_id = '$form_data[foreman_id]',  
            contract_revenue = '$form_data[contract_revenue]',              
            remarks    = '".addslashes($form_data['remarks'])."',               
    ";

    if(empty($form_data['project_payroll_header'])){ #insert
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

    if(!empty($form_data['project_payroll_header_id'])){
        $sql .= "
            where project_payroll_header_id = '$form_data[project_payroll_header_id]'
        ";
    }

    mysql_query($sql) or die(mysql_error());
    $project_payroll_header_id = $_REQUEST['project_payroll_header_id'] = (!empty($form_data['project_payroll_header_id'])) ? $form_data['project_payroll_header_id'] : mysql_insert_id(); 

    return $project_payroll_header_id;  
}

function addExpense(){

    if( empty($_REQUEST['labor_expense']) ){
        return false;
    }

    $sql = "
        insert into
            project_payroll_detail
        set
            project_payroll_header_id = '$_REQUEST[project_payroll_header_id]',
            labor_expense               = '$_REQUEST[labor_expense]',
            uom                 = '$_REQUEST[uom]',
            quantity               = '$_REQUEST[quantity]',
            price                  = '$_REQUEST[price]',
            total_price            = '$_REQUEST[amount]'
            
    ";
  //echo $sql;
    DB::conn()->query($sql) or die(DB::conn()->error);
}


function addDiscount(){

    if( empty($_REQUEST['discount_name']) ){
        return false;
    }

    $sql = "
        insert into
            project_payroll_discount
        set
            project_payroll_header_id = '$_REQUEST[project_payroll_header_id]',
            discount_name               = '$_REQUEST[discount_name]',
            discount_amount                 = '$_REQUEST[discount_amount]'
            
    ";
  //echo $sql;
    DB::conn()->query($sql) or die(DB::conn()->error);
}

function displayDetails($id){


    /*RAW MATERIALS DISPLAY*/
    $sql = "
        select
           *
        from 
            project_payroll_detail 
        where
            project_payroll_header_id = '$id'
             and project_payroll_void = '0'      
               
    ";

    $arr = lib::getArrayDetails($sql);

     $sql_disc = "
        select
           *
        from 
            project_payroll_discount
        where
            project_payroll_header_id = '$id'  
            and void='0'   
    ";

    $arr_disc = lib::getArrayDetails($sql_disc);

    echo "
        <table class='table-css' style='vertical-align:top; display:inline-table;' >
            <caption>PAYROLL DETAILS</caption>
            <thead>
                <tr>
                    <td style='width:10px;'>#</td>
                    <td style='width:10px;'></td>
                    <td>LABOR EXPENSE</td>
                    <td style='text-align:right; width:10%;'>UOM</td>      
                    <td style='text-align:right; width:10%;'>QUANTITY</td>          
                    <td style='text-align:right; width:10%;'>PRICE</td>          
                    <td style='text-align:right; width:10%;'>TOTAL PRICE</td>                               
                </tr>
            </thead>
            <tbody>
        ";

    $t_quantity = $t_amount = $t_price = 0;
    if( count($arr) ){
        $i = 1;

        foreach ($arr as $r) {

            $t_quantity += $r['quantity'];     
            $t_amount   += $r['price'];
            $t_price   += $r['total_price'];

            echo "
                <tr>
                    <td>$i</td>
                    <td><a class='trash' href='admin.php?view=$GLOBALS[view]&project_payroll_header_id=$id&b=rd&id=$r[project_payroll_detail_id]' onclick='return approve_confirm();'><img src='images/trash.gif'></a></td>
                    <td>$r[labor_expense]</td>
                    <td style='text-align:right;'>$r[uom]</td>
                    <td style='text-align:right;'>".number_format($r['quantity'],2)."</td>
                    <td style='text-align:right;'>".number_format($r['price'],2)."</td>
                    <td style='text-align:right;'>".number_format($r['total_price'],2)."</td>
                </tr>
            ";

            $i++;
        }
    }
   
    $retention = $t_price * 0.1;
    echo "  <tr>
            <td></td>
            <td></td>
            <td  style='text-align:right;'>10% Retention</td>
            <td></td>
            <td></td>
            <td></td>
            <td style='text-align:right;'>(".number_format($retention,2).")</td>
        </tr>";
    $discount_total =0;
    if( count($arr_disc) ){
       

        foreach ($arr_disc as $r_disc) {

                 
             $discount_total   += $r_disc['discount_amount'];

            echo "
                <tr>
                   <td></td>
                    <td><a class='trash' href='admin.php?view=$GLOBALS[view]&project_payroll_header_id=$id&b=rd_disc&disc_id=$r_disc[project_payroll_discount_id]' onclick='return approve_confirm();'><img src='images/trash.gif'></a></td>
                    <td  style='text-align:right;'>$r_disc[discount_name]</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td style='text-align:right;'>(".number_format($r_disc['discount_amount'],2).")</td>
                </tr>
            ";

            
        }
    }

    $total_amount= $t_price -$retention - $discount_total;

    echo "        
          
            </tbody>
            <tfoot>
                <tr>
                    <td></td>                    
                    <td></td>                    
                    <td></td>     
                     <td></td>                  
                    <td style='text-align:right;'><span>".number_format($t_quantity,2)."</td>       
                    <td style='text-align:right;'><span>".number_format($t_amount,2)."</td>
                    <td style='text-align:right;'><span>".number_format($total_amount,2)."</td>
                </tr>
            </tfoot>
        </table>
    ";


      /*DISCOUNT DISPLAY*/
    // $sql = "
    //     select
    //        *
    //     from 
    //         project_payroll_discount
    //     where
    //         project_payroll_header_id = '$id'     
    // ";

    // $arr_disc = lib::getArrayDetails($sql);
    // echo "<br><br>
    //     <table class='table-css' style='vertical-align:top; display:inline-table;' >
    //         <caption>DISCOUNT</caption>
    //         <thead>
    //             <tr>
    //                 <td style='width:10px;'>#</td>
    //                 <td style='width:10px;'></td>
    //                 <td>DISCOUNT NAME</td>
    //                 <td style='text-align:right; width:10%;'>DISCOUNT</td>                       
    //             </tr>
    //         </thead>
    //         <tbody>
    //     ";

    // $t_quantity = $t_amount = 0;
    // if( count($arr) ){
    //     $i = 1;

    //     foreach ($arr_disc as $r_disc) {

                 
    //          $discount_total   += $r_disc['discount'];

    //         echo "
    //             <tr>
    //                 <td>$i</td>
    //                 <td><a class='trash' href='admin.php?view=$GLOBALS[view]&project_payroll_header_id=$id&b=rd&id=$r[sales_return_discount_id]' onclick='return approve_confirm();'><img src='images/trash.gif'></a></td>
    //                 <td>$r[discount_name]</td>
    //                 <td style='text-align:right;'>".number_format($r['discount'],2)."</td>
    //             </tr>
    //         ";

    //         $i++;
    //     }
    // }

    // echo "            
    //         </tbody>
    //         <tfoot>
    //             <tr>
    //                 <td></td>                    
    //                 <td></td>                    
    //                 <td></td>                       
    //                 <td style='text-align:right;'><span>".number_format($discount_total,2)."</td>
    //             </tr>
    //         </tfoot>
    //     </table>
    // ";
}

if( $_REQUEST['b'] == "Submit" ){

    if( $_REQUEST['date'] ){

        $project_payroll_header_id  =  $_REQUEST['project_payroll_header_id'] = insertTransactionToDB($_REQUEST);            
        $msg = "Transaction Saved.";    

    } else {
        $msg = "Please fill in date.";
    }
} else if( $_REQUEST['b'] == "Cancel" ){
    
    /*when unfished you are not allowed to add and delete entries in the collections*/
    mysql_query("
        update project_payroll_header set status = 'C' where project_payroll_header_id = '$_REQUEST[project_payroll_header_id]'
    ") or die(mysql_error());

    $msg = "Transaction Cancelled";

} else if( $_REQUEST['b'] == "Add Raw Materials" ){
    $msg = addRawMaterials();
    computeExcessQuantity($_REQUEST['project_payroll_header']);
} else if( $_REQUEST['b'] == "Add Expense" ){
    addExpense();
}else if( $_REQUEST['b'] == "Add Deduction" ){
    addDiscount();
}else if($_REQUEST['b'] == "rd" ){
    $sql = "
        update
            project_payroll_detail
        set
            project_payroll_void = '1'
        where
            project_payroll_detail_id = '$_REQUEST[id]'
    ";
    DB::conn()->query($sql) or die(DB::conn()->error);    
}
else if($_REQUEST['b'] == "rd_disc" ){
    $sql = "
        update
            project_payroll_discount
        set
            void = '1'
        where
            project_payroll_discount_id = '$_REQUEST[disc_id]'
    ";
    DB::conn()->query($sql) or die(DB::conn()->error);    
}


if($_REQUEST['project_payroll_header_id'] && $_REQUEST['b'] != "Search"){
	$result = mysql_query("
		select 
            *
        from 
            project_payroll_header        
        where project_payroll_header_id = '$_REQUEST[project_payroll_header_id]'
	") or die(mysql_error());

	$aVal = mysql_fetch_assoc($result);    

} else {
    $aVal = $_REQUEST;
}
?>
<form enctype='multipart/form-data' method="post" action="" id="form" >
	<div class="home_module_actions">
        <fieldset style="border:1px solid #c0c0c0; margin-bottom:5px;">
            <legend>SEARCH PROJECT PAYROLL</legend>
            <table>
                <tbody>
                    <tr>
                        <td>Date</td>
                        <td><input type="text" class="textbox datepicker" name="search_date" value="<?=$aVal['search_date']?>" /></td>

                        <td>PP #</td>
                        <td><input type="text" class="textbox" name="search_sales_return_header_id" id="search_sales_return_header_id" value="<?=$_REQUEST['search_sales_return_header_id']?>"></td>                        
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
                h.*, project_name
            from
                project_payroll_header as h
                left join projects as p on h.project_id = p.project_id
			where
                1=1
        ";
        if( $_REQUEST['search_date'] ) $sql.=" and date = '$_REQUEST[search_date]'";        
        if( $_REQUEST['search_sales_return_header_id'] ) $sql.=" and sales_return_header_id = '$_REQUEST[search_sales_return_header_id]'";

        $sql .= "
            order by date desc
        ";

        $pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
                
        $i=$limitvalue;
        $rs = $pager->paginate();
        
        $pagination	= $pager->renderFullNav("$view&b=Search&search_date=$_REQUEST[search_date]&search_sales_return_header_id=$_REQUEST[search_sales_return_header_id]");
        ?>
        <div class="pagination">
            <?=$pagination?>
        </div>
        <table width="100%" align="left" style="text-align:left;" class="table-css">
        	<thead>
                <tr>				
                    <td width="20">#</td>
                    <td width="20"></td>                    
                    <td style="text-align:left;">PP#</td>
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
                echo '<td width="15"><a href="admin.php?view='.$view.'&project_payroll_header_id='.$r['project_payroll_header_id'].'" title="Show Details"><img src="images/edit.gif" border="0"></a></td>';                
                echo '<td>'.str_pad($r['project_payroll_header_id'],7,0,STR_PAD_LEFT).'</td>';
                echo '<td>'.lib::ymd2mdy($r['date']).'</td>';                
                echo "<td>$r[project_name]</td>";
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

            <input type="hidden" name="project_payroll_header_id" id="project_payroll_header_id" value="<?=$aVal['project_payroll_header_id']?>" />

            <fieldset style="border:none;">
                <p>                
                    <?php if( !empty($aVal['project_payroll_header_id']) ){ ?>
                    <div>
                        PP#: <br>
                        <span style='font-weight:bold; font-size:14px;'><?=str_pad($aVal['project_payroll_header_id'], 7,0,STR_PAD_LEFT)?></span>
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
                        Project <br>
                        <input type="text" class="textbox project" value="<?=lib::getAttribute('projects','project_id',$aVal['project_id'],'project_name')?>">
                        <input type="hidden" name="project_id" value="<?=$aVal['project_id']?>" >
                    </div>

                      <div class="inline">
                        Foreman <br>

                      
                          <select name='foreman_id'>
                            <option></option>
                            <?php while($fetch_foreman = mysql_fetch_array($query_foreman)){ 
                                $selected=($aVal['foreman_id']==$fetch_foreman[employeeID])?"selected='selected'":"";
                                ?>
                            <option value="<?php echo $fetch_foreman['employeeID']; ?>" <?php echo $selected; ?>><?php echo $fetch_foreman['employee_fname'] . " " . $fetch_foreman['employee_lname']; ?></option>
                            <?php } ?>
                         </select> 
                    </div>

                      <div class="inline">
                        Contract Revenue <br>

                      
                           <input type="text" class="textbox" name="contract_revenue" id="contract_revenue" value="<?=$aVal['contract_revenue']?>"> 
                    </div>


                    
                </p>           
                <p>
                    Remarks <br>
                    <textarea style="width:90%; padding:5px; font-size:10px; font-family:arial; border:1px solid #c0c0c0;" name="remarks"><?=$aVal['remarks']?></textarea>    
                </p>
            </fieldset>

            
            <?php  if( $aVal['status'] == "S" ): ?>
                
            <fieldset style="border:none; border:1px solid #c0c0c0; display:inline-block;">
                <legend>EXPENSE ENTRY</legend>
                                    
                    <div style="display:inline-block;">
                        Labor Expense : <br>
                        <input type="text" class="textbox labor_expense" name="labor_expense" id="labor_expense" onkeypress="if(event.keyCode==13){ jQuery('#uom').focus(); return false; }" autofocus>                    
                    </div>
                     <div style="display:inline-block;">
                        UOM : <br>
                        <input type="text" class="textbox3 uom" name="uom" id="uom" onkeypress="if(event.keyCode==13){ jQuery('#quantity').focus(); return false; }">
                    </div>


                    <div style="display:inline-block;">
                        Quantity : <br>
                        <input type="text" class="textbox3 quantity" name="quantity" id="quantity"  onkeypress="if(event.keyCode==13){ jQuery('#price').focus(); return false; }">
                    </div>

                    <div style="display:inline-block;">
                        Price : <br>
                        <input type="text" class="textbox3 price" name="price" id="price"  onkeypress="if(event.keyCode==13){ jQuery('#amount').click(); return false; }">
                    </div>

                    <div style="display:inline-block;">
                        Amount : <br>
                        <input type="text" class="textbox3 amount" name="amount"  onkeypress="if(event.keyCode==13){ jQuery('#product_btn').click(); return false; }" readonly >
                    </div>

                    <input type="submit" value="Add Expense" name="b" id="product_btn">
                        
            </fieldset>

             <fieldset style="border:none; border:1px solid #c0c0c0; display:inline-block;">
                <legend>DEDUCTION ENTRY</legend>
                                    
                    <div style="display:inline-block;">
                        Deduction Name : <br>
                        <input type="text" class="textbox " name="discount_name" id="discount_name">                    
                    </div>
                    <div style="display:inline-block;">
                    Deduction Amount : <br>
                        <input type="text" class="textbox3 price" name="discount_amount" id="discount_amount"  onkeypress="if(event.keyCode==13){ jQuery('#discount_btn').click(); return false; }">
                    </div>

               
                    <input type="submit" value="Add Deduction" name="b" id="discount_btn">
                        
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
                
                <?php if( $aVal['status'] == "S" ){ ?>  <!--<input type='submit' name='b' value='Finish' class='buttons' id="submit_button">--><?php } ?>            
            </div>
          
        </div>
        <?php if( $aVal['project_payroll_header_id'] ){ 
        /*$aVal['status'] = "S";*/
        require_once(dirname(__FILE__).'/transaction-status.php');
        } ?>
        
        <?php if( $aVal['project_payroll_header_id'] && $_REQUEST['b'] != "Print Preview"): ?>
        <div>
            <?=displayDetails($aVal['project_payroll_header_id']);?>    
        </div>
        <?php endif; ?>
        <?php if($_REQUEST['b'] == "Print Preview" && $aVal['project_payroll_header_id']){ 
            echo " <iframe id='JOframe' name='JOframe' frameborder='0' src='print_project_payroll.php?id=$aVal[project_payroll_header_id]' width='100%' height='500'>
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
        jQuery.post("raw_mat_sales_return_header/ajax/sales_return_header.php", { action : "generateStockID" , data : jQuery("form").serializeObject() }, function(data){
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