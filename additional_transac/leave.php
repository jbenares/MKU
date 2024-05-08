<?php require_once('func_gatepass.php');  ?>

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
$_REQUEST['search_employee_id']  = ($_REQUEST['search_name']) ? $_REQUEST['search_employee_id'] : "";

function insertTransactionToDB($form_data){
   
    $options = new options();
    $gpnum="";
    if(empty($form_data['leave_id'])){
        $sql = "insert into leave_info"
                . " set date_requested  = '".$form_data[date_requested]."',";
        $gpnum = $options->generateLFnum();
    } else {
        $sql = "update leave_info"
		. " set date_requested  = '".$form_data[date_requested]."',";;
        $gpnum = $form_data[lf_num];
    }
    
    $sql .= "

            lf_num                = '$gpnum',
            inclusive_date        = '$form_data[inclusive_date]',
			inclusive_date_to     = '$form_data[inclusive_date_to]',
            particular            = '".mysql_real_escape_string($form_data[particular])."',               
            employee_id           = '$form_data[employee_id]',
          
    ";
   
    if(empty($form_data['leave_id'])) { #insert
        $sql .= " 
            prepared_by   = '$_SESSION[userID]',
            prepared_time = '".date("Y-m-d h:i:s")."'
        ";
        
    } else {  #update
        $sql .= "
            edited_by      = '$_SESSION[userID]',
            last_edit_time = '".date("Y-m-d h:i:s")."'
        ";
        
    }

    if(!empty($form_data['leave_id'])){
        $sql .= "
            where leave_id = '$form_data[leave_id]'
        ";
    }
    
    //return $sql;
    
    mysql_query($sql) or die(mysql_error());
    $gatepass_id = $_REQUEST['leave_id'] = (!empty($form_data['leave_id'])) ? $form_data['leave_id'] : mysql_insert_id(); 

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
}

if( $_REQUEST['b'] == "Submit" ){

    if( $_REQUEST['date_requested'] ){
        
        $gatepass_id  =  $_REQUEST['leave_id'] = insertTransactionToDB($_REQUEST);
        
        $msg = "Transaction Saved.";    

    } else {
        $msg = "Please fill in date.";
    }
} else if( $_REQUEST['b'] == "Cancel" ){
    
    /*when unfished you are not allowed to add and delete entries in the collections*/
    mysql_query("
        update leave_info set status = 'C' where leave_id = '$_REQUEST[leave_id]'
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


if($_REQUEST['leave_id'] && $_REQUEST['b'] != "Search"){
	$result = mysql_query("
		select 
                    *,g.status as st
                from 
                    leave_info as g      
                where 
                    g.leave_id = '$_REQUEST[leave_id]'
                ") or die(mysql_error());

	$aVal = mysql_fetch_assoc($result);    

} else {
    $aVal = $_REQUEST;
}
?>
<form enctype='multipart/form-data' method="post" action="" id="form" name="form" >
	<div class="home_module_actions">
        <fieldset style="border:1px solid #c0c0c0; margin-bottom:5px;">
            <legend>SEARCH <?=$transac->getMname($view)?></legend>
            <table>
                <tbody>
                    <tr>
                        <td>Date</td>
                        <td><input type="text" class="textbox3 datepicker" name="search_date" value="<?=$aVal['search_date']?>" /></td>

                        <td>LF#</td>
                        <td><input type="text" class="textbox3" name="search_leave_id" id="search_leave_id" value="<?=$_REQUEST['search_leave_id']?>"></td>                         
						<td>Employee</td>
                        <td><input type="text" class="textbox" name="employee" id="employee" value="<?=$_REQUEST['employee']?>"></td>     
					</tr>                    
                </tbody>
            </table>
        </fieldset>
    	<input type="submit" name="b" value="Search" />
        <a href="?view=<?=$view?>"><input type="button" name="b" value="New" /></a>
    </div>
	<?php if($_REQUEST['b'] == "Search"):
             
        $page = $_REQUEST['page'];
        if(empty($page)) $page = 1;
         
        $limitvalue = $page * $limit - ($limit);
        
        $sql = "
            select *,g.status as st ,concat(employee_lname,', ',employee_fname) as name
			from leave_info as g 
            left join employee as e on g.employee_id = e.employeeID
			where 1=1
        ";
        if( $_REQUEST['search_date'] ) $sql.=" and date_requested = '$_REQUEST[search_date]'";        
        if( $_REQUEST['search_leave_id'] ) $sql.=" and leave_id = '$_REQUEST[search_leave_id]'";
	    if( $_REQUEST['employee'] ) $sql.=" and e.employee_lname  like '%$_REQUEST[employee]%'";
		
        $sql .= "
            order by date_requested desc
        ";
		#echo $sql;
        $pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
                
        $i=$limitvalue;
        $rs = $pager->paginate();
        
        $pagination	= $pager->renderFullNav("$view&b=Search&search_date=$_REQUEST[search_date]&search_leave_id=$_REQUEST[search_leave_id]&employee=$_REQUEST[employee]");
        ?>
        <table cellspacing="2" cellpadding="5" width="100%" class="search_table pagination">
            <tr>
                <td colspan="5" align="left">
                   <?=$pagination?>            
                </td>
            </tr>
        </table>
        <table width="100%" align="left" style="text-align:left;" class="table-css">
        	<thead>
                <tr>				
                    <td width="20">#</td>
                    <td width="20"></td>                    
                    <td style="text-align:left;">LF#</td>
                    <td style="text-align:left;">DATE REQUESTED</td>                                        
                    <td style="text-align:left;">INCLUSIVE DATE</td>
                    <td style="text-align:left;">REQUESTED BY</td> 
                    <td style="text-align:left;">PARTICULARS</td>  					
                    <td style="text-align:center;">STATUS</td>
                </tr>  
            </thead>
            <tbody>
            <?php								
            while($r=mysql_fetch_assoc($rs)) {
                
                echo '<tr>';
                echo '<td width="20">'.++$i.'</td>';
                echo '<td width="15"><a href="admin.php?view='.$view.'&leave_id='.$r['leave_id'].'" title="Show Details"><img src="images/edit.gif" border="0"></a></td>';                
                echo '<td>'.$r[lf_num].'</td>';
                echo '<td>'.date("F d, Y", strtotime($r[date_requested])).'</td>';
                echo '<td>'.date("m/d/Y", strtotime($r[inclusive_date])).' - '.date("m/d/Y", strtotime($r[inclusive_date_to])).'</td>';                 
                echo "<td>".strtoupper($r['name'])."</td>"; 
               echo "<td>".$r['particular']."</td>";  				
                echo '<td style="width:10%; text-align:center;">'.$aStatus[$r['st']].'</td>';                
                echo '</tr>';
            }
            ?>
            </tbody>
        </table>
         <table cellspacing="2" cellpadding="5" width="100%" class="search_table pagination">
            <tr>
                <td colspan="5" align="left">
                   <?=$pagination?>            
                </td>
            </tr>
        </table>      
    <?php else: #end else?>
        <div class="module_actions">
            <?php if(!empty($msg)) echo '<div onclick="jQuery(this).hide(500);" id="status_update" class="ui-state-highlight ui-corner-all" style="padding: 0px 0.7em; text-align:left;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right:.3em;"></span> '.$msg.'</p></div>'; ?>
            <div class="module_title"><img src='images/user_orange.png'><?=strtoupper($transac->getMname($view))?></div>

            <input type="hidden" name="leave_id" id="leave_id" value="<?=$aVal['leave_id']?>" />
            <input type="hidden" name="lf_num" id="lf_num" value="<?=$aVal['lf_num']?>" />

            <fieldset style="border:none;">
                <p>                
                    <?php if( !empty($aVal['leave_id']) ){ ?>
                            <div>
                                LF#: <br>
                                <span style='font-weight:bold; font-size:14px;'><?=$aVal['lf_num'];?></span>
                            </div>
                    <?php } ?>
                        <div class="inline">
                            Date Requested: <br/>
                            <input type="text" class="textbox3 datepicker" name="date_requested" value="<?=$aVal['date_requested']?>" />
                       </div>
                
                       <div class="inline">
                            From: <br/>
                            <input type="text" class="textbox3 datepicker" name="inclusive_date" value="<?=$aVal['inclusive_date']?>" />
                       </div>
					    <div class="inline">
                            To:: <br/>
                            <input type="text" class="textbox3 datepicker" name="inclusive_date_to" value="<?=$aVal['inclusive_date_to']?>" />
                       </div>
                <br/> <br/>
                <div class="inline">
                    Particular:<br/><input type="text" name="particular" class="textbox2" value="<?=$aVal['particular'];?>">
                </div>
                <br/></br>
                <div class="inline">
                    Requested By:<br/>
					 <input type="text" class="textbox ac-employee" value="<?=lib::getEmployeeName($aVal['employee_id'])?>">
                        <input type="hidden" name="employee_id" value="<?=$aVal['employee_id']?>" >
                </div>
            </fieldset>

           	<div>
                <?php if( !in_array($aVal['st'], array("F","C"))){ ?>
                <input type='submit' name='b' value='Submit' class='buttons' id="submit_button">            
                <?php } ?>
                <?php if($_REQUEST['b'] != "Print Preview" && !empty($aVal['st'])){ ?>                
                <input type="submit" name="b" id="b" value="Print Preview" />
                <?php } else if($_REQUEST['b'] == "Print Preview"){ ?>    
                <input type="button" value="Print" onclick="printIframe('JOframe');" /> 
                <?php } ?>

                <?php if( in_array($aVal['st'], array("S"))){ ?>            
                <input type='submit' name='b' value='Cancel' class='buttons' >
                <?php } ?>
                
                <?php if( $aVal['st'] == "S" ){ ?> <!-- <input type='submit' name='b' value='Finish' class='buttons' id="submit_button"> --> <?php } ?>            
            </div>
          
        </div>
        <?php if( $aVal['leave_id'] ){ 
        /*$aVal['status'] = "S";*/
        require_once('transaction-status.php');
        } ?>

        <?php if($_REQUEST['b'] == "Print Preview" && $aVal['leave_id']) { 
            echo " <iframe id='JOframe' name='JOframe' frameborder='0' src='additional_transac/print_leave_form.php?leave_id=$aVal[leave_id]' width='100%' height='500'>
                   </iframe>";    
        } 
        ?>
    <?php endif; #end if ?>    
</form>
<script>    
    <?php if( $aVal['st'] == "C" ){ ?>
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