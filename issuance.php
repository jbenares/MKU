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
	$b						= $_REQUEST['b'];
	$issuance_header_id		= $_REQUEST['issuance_header_id'];
	$date					= $_REQUEST['date'];
	$project_id				= $_REQUEST['project_id'];
	$reference				= $_REQUEST['reference'];
	$project_name			= $options->attr_Project($project_id,'project_name');
	$project_code			= $options->attr_Project($project_id,'project_code');
	$project_name_code		= ($project_id)?"$project_name - $project_code":"";
	
	$work_category_id		= $_REQUEST['work_category_id'];
	$sub_work_category_id	= $_REQUEST['sub_work_category_id'];
	$scope_of_work			= $_REQUEST['scope_of_work'];
	
	$stock_name				= $_REQUEST['stock_name'];	
	
	$stock_id				= $_REQUEST['stock_id'];
	$description			= $_REQUEST['description'];
	$search_stock_id		= $_REQUEST['search_stock_id'];
	$quantity				= $_REQUEST['quantity'];
	$price					= $_REQUEST['price'];
	$remarks				= $_REQUEST['remarks'];
	
	$user_id				= $_SESSION['userID'];
	$checkList				= $_REQUEST['checkList'];
	
	if($b == "Unfinish"){
		mysql_query("
			update issuance_header set status = 'S' where issuance_header_id = '$issuance_header_id'
		") or die(mysql_error());	
		
		mysql_query("
			update gltran_header set status = 'C' where header_id = '$issuance_header_id' and header = 'issuance_header_id'
		") or die(mysql_error());
		
		$msg = "Issuace Unfinished";
	}else if($b == "delete"){
		$id = $_REQUEST['id'];
		mysql_query("
			delete from issuance_detail where issuance_detail_id = '$id'
		") or die(mysql_error());
		
		$msg = "Transaction detail deleted.";
	}
	
	if($b=="Submit"){
		$query="
			insert into 
				issuance_header
			set
				project_id           = '$project_id',
				date                 = '$date',
				status               = 'S',
				user_id              = '$user_id',
				work_category_id     = '$work_category_id',
				sub_work_category_id = '$sub_work_category_id',
				scope_of_work        = '$scope_of_work',
				reference            = '$reference',
				remarks              = '$remarks',
				encoded_datetime     = now()
		";	
		
		mysql_query($query) or die(mysql_error());
		
		$issuance_header_id = mysql_insert_id();
		$options->insertAudit($issuance_header_id,'issuance_header_id','I');
		$msg="Transaction Saved";
		
	}else if($b=="Update"){
		$query="
			update
				issuance_header
			set
				project_id		= '$project_id',
				date 			= '$date',
				status			= 'S',
				user_id			= '$user_id',
				work_category_id = '$work_category_id',
				sub_work_category_id = '$sub_work_category_id',
				scope_of_work	= '$scope_of_work',
				reference = '$reference',
				remarks			= '$remarks'
			where
				issuance_header_id	= '$issuance_header_id'
		";	
		
		mysql_query($query) or die(mysql_error());
		$options->insertAudit($issuance_header_id,'issuance_header_id','U');
		$msg = "Transaction Updated";
		
		#udpate details
		$update_issuance_detail_id 	= $_REQUEST['update_issuance_detail_id'];
		$update_reference 			= $_REQUEST['update_reference'];
		$update_account_id 			= $_REQUEST['update_account_id'];
		$update_equipment_id 		= $_REQUEST['update_equipment_id'];
		$update_driver_id			= $_REQUEST['update_driver_id'];
		
		if(!empty($update_issuance_detail_id)){
			$x = 0;
			foreach($update_issuance_detail_id as $id){
				mysql_query("
					update
						issuance_detail
					set
						_reference 		= '$update_reference[$x]',
						account_id 		= '$update_account_id[$x]',
						equipment_id 	= '$update_equipment_id[$x]',
						driverID 		= '$update_driver_id[$x]'
					where
						issuance_detail_id = '$id'
				") or die(mysql_error());	
				
				$x++;
			}
		}
		
		
	}else if($b=="Cancel"){
		$query="
			update
				issuance_header
			set
				status='C'
			where
				issuance_header_id = '$issuance_header_id'
		";	
		mysql_query($query) or die(mysql_error());
		$options->insertAudit($issuance_header_id,'issuance_header_id','C');
		
		$msg = "Transaction Cancelled";
		
	}else if($b=="Finish"){
		$query="
			update
				issuance_header
			set
				status='F'
			where
				issuance_header_id = '$issuance_header_id'
		";	
		mysql_query($query) or die(mysql_error());
		$options->insertAudit($issuance_header_id,'issuance_header_id','F');
		$options->postIssuance($issuance_header_id);
		$msg = "Transaction Finished and Posted";
		
	}else if($b=="Delete Details"){
		
		if(!empty($checkList)){
			foreach($checkList as $id){
				
				#INSERT AUDIT TRAIL
				
				$detail_header = "issuance_detail";
				$detail_header_id = "issuance_detail_id";
				
				
				$stock_id = $options->getAttribute($detail_header,$detail_header_id,$id,"stock_id"); #CHANGE 
				$_header_id = $options->getAttribute($detail_header,$detail_header_id,$id,"issuance_header_id"); #CHANGE
				$stock		= $options->getAttribute("productmaster","stock_id",$stock_id,"stock");
				$_desc = $options->getUserName($user_id)." deleted $stock in RIS# $_header_id on ".date("m/d/Y h:i:s A");
				$options->insertAuditTrail($_desc,$user_id,$_header_id,"RIS");

				mysql_query("
					delete from
						issuance_detail
					where	
						issuance_detail_id = '$id'
				") or die(mysql_error());
			}
		}
	}else if($b=="New"){
		header("Location: admin.php?view=$view");
	}

	$query="
		select
			*
		from
			issuance_header
		where
			issuance_header_id = '$issuance_header_id'
	";
	
	$result=mysql_query($query);
	$r = $aVal = mysql_fetch_assoc($result);

	$id_pad				= str_pad($issuance_header_id,7,0,STR_PAD_LEFT);
	$project_id			= $r['project_id'];
	$project_name		= $options->attr_Project($project_id,'project_name');
	$project_code		= $options->attr_Project($project_id,'project_code');
	$project_name_code	= ($project_id)?"$project_name - $project_code":"";
	$date				= ($r['date']=="0000-00-00")?"":$r['date'];
	$reference 			= $r['reference'];
	
	$work_category_id	= $r['work_category_id'];
	$sub_work_category_id = $r['sub_work_category_id'];
	$scope_of_work		= $r['scope_of_work'];
	$remarks			= $r['remarks'];
		
	$status				= $r['status'];
	$user_id			= $r['user_id'];
	
?>
<form name="header_form" id="header_form" action="" method="post">
<div class=form_layout>
	<?php if(!empty($msg)) echo '<div id="status_update" class="ui-state-highlight ui-corner-all" style="padding: 0px 0.7em; text-align:left;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right:.3em;"></span> '.$msg.'</p></div>'; ?>
	<div class="module_title"><img src='images/user_orange.png'>ISSUANCE</div>
    
    <div style="width:50%; float:left;">
    
        <div class="module_actions">
            <input type="hidden" name="issuance_header_id" id="issuance_header_id" value="<?=$issuance_header_id?>" />
            <input type="hidden" name="view" value="<?=$view?>" />
            <div id="messageError">
                <ul>
                </ul>
            </div>
            
            <div class="inline">
                Date : <br />
                <input type="text" class="textbox3 datepicker" title="Enter Date" name="date" value="<?=$date?>" required />
            </div>
            
            <div class='inline'>
                Project / Location : <br />  
                <input type="text" class="textbox" id="project_name" value="<?=$project_name_code?>" onclick="this.select();" required />
                <input type="hidden" name="project_id"  id="project_id" value="<?=$project_id?>" title="Select Project" required />
            </div>
            
            <!--<div class="inline">
                Scope of Work :
                <div id="div_scope_of_work">
                    <select class="select">
                        <option value="">Select Project First...</option>
                    </select>
                </div>
            </div> -->
            
             <div class="inline">
                Work Category : <br />
                <?=$options->option_workcategory($work_category_id,'work_category_id','Select Work Category')?>
            </div>
            
            <div id="subworkcategory_div" style="display:none;" class="inline">
                Sub Work Category :
                <div id="subworkcategory">
                    
                </div>
            </div>
            
            <div class="inline">
            	Reference : <br />
                <input type="text" class="textbox" name="reference" placeholder="Control Number Here..." value="<?=$reference?>" required />
            </div>	
            	
            <br />
            
            <div class="inline">
            	Remarks : <br />
                <textarea name="remarks" style="border:1px solid #c0c0c0; width:300px; height:50px;" ><?=$remarks?></textarea>
            </div>	           
        </div>
        <?php
        if(!empty($status)){
        ?>
        <div class="module_actions">
	        <div class="inline" style="vertical-align:top;">
	            Issuance # : <br />
	            <input type="text" class="textbox3" value="<?=$id_pad?>" readonly="readonly"  />
	        </div>
	        
	        <div class='inline' style="vertical-align:top;">
	            Status : <br />
	            <input type="text" class="textbox3" name="status" id="status" value="<?=$options->getTransactionStatusName($status)?>" readonly="readonly"/>
	        </div> 

	        <div class='inline' style="vertical-align:top;">
                <div>Encoded by : </div>        
                <div>
                    <input type='text' class="textbox" value="<?=$options->getUserName($user_id);?>" readonly="readonly" /> 
                    <?php
                    if( !empty($aVal['encoded_datetime']) ){
                    	echo "<br>".$aVal['encoded_datetime'];
                    }
                    ?>
                </div>
            </div> 
        </div>
        <?php } ?>
        <div class="module_actions">
            <input type="submit" name="b" value="New" />
            <?php
            if($status=="S"){
            ?>
            <input type="submit" name="b" id="b" value="Update" />
            <input type="submit" name="b" id="b" value="Finish"  />
            
            <?php
            }else if($status!="F" && $status!="C"){
            ?>
            <input type="submit" name="b" id="b" value="Submit" />
            <?php } ?>
            
            <?php if($b!="Print Preview" && !empty($status)){ ?>
            <input type="submit" name="b" id="b" value="Print Preview" />
            <?php } ?>
        
            <?php if($b=="Print Preview"){ ?>	
            <input type="button" value="Print" onclick="printIframe('JOframe');" />        
            <?php } ?>
            
            <?php if($b!="Print Preview Aggr" && !empty($status)){ ?>
	        <input type="submit" name="b" id="b" value="Print Preview Aggr" />
            <?php } ?>
        
            <?php if($b=="Print Preview Aggr"){ ?>	
            <input type="button" value="Print Aggr" onclick="printIframe('JOframe');" />
            <?php } ?>
            
            <?php if($status == "F"){ ?>
            <input type="submit" name="b" value="Unfinish" />	
            <?php } ?>
            
           	<?php if($status!="C" && !empty($status)){ ?>
            <input type="submit" name="b" id="b" value="Cancel" />
            <?php
            }
            ?>
        </div>
        
        <?php
        if($status=="S"){
        ?>
        <div class="module_actions">
        	<img src="images/find.png" />
            <input type="text" class="textbox" name="search_item" placeholder="Search Item" onkeypress="if(event.keyCode == 13){ document.getElementById('search_btn').click();   return false;}" />
            <input type="submit" name="b" value="Search" id="search_btn" />
        </div>
        <?php if($b == "Search"){ ?>
        <div class="module_title"><img src='images/database_table.png'>SEARCH RESULTS : </div>
        <table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table" id="search_table">
            <tr bgcolor="#C0C0C0">				
                <th width="20"><b>#</b></th>
                <th width="30"></th>
                <th>Item</th>
                <th width="60">Unit</th>
                <th width="60">Balance</th>
            </tr> 
            <?php
			$query="
				select
                    stock,stock_id,unit
                from
                    productmaster
				where
					stock like '%".$_REQUEST['search_item']."%'
				order by stock asc
			";
            $result=mysql_query($query) or die(mysql_error());
            
            $i=1;
            while($r=mysql_fetch_assoc($result)){
                $stock_id 		= $r['stock_id'];
				$unit = $r['unit'];				
				$stock = $r['stock'];
				#$unit = $options->getAttribute('productmaster','stock_id',$stock_id,'unit');
				#$stock = $options->getAttribute('productmaster','stock_id',$stock_id,'stock');

                //$project_warehouse_qty		= $options->inventory_projectqty(NULL,$stock_id,$project_id,$scope_of_work,$work_category_id,$sub_work_category_id);
				// if($project_id == 14 ){
				// 	$project_warehouse_qty = $options->inventory_warehouse($date,$stock_id);	
				// }else{
				// 	$project_warehouse_qty = $options->inventory_warehouse($date,$stock_id);	
					
				// }

				$project_warehouse_qty = $options->inventory_warehouse($date,$stock_id);	
				
                $issued_qty 				= $options->issuance_issuedToProject($stock_id,$project_id,$work_category_id,$sub_work_category_id,$scope_of_work);
                $requested_qty				= $options->total_approved_stocks_requested($stock_id,$project_id,$work_category_id,$sub_work_category_id,$scope_of_work);
				
                $balance = $project_warehouse_qty;
                
				if($balance > 0){
            ?>
            <tr>
                <td><?=$i++?></td>
                <td><input type="button" value="ISSUE" onclick="xajax_issuance_stock_id_form('<?=$stock_id?>',xajax.getFormValues('header_form'));" /></td>
                <td><?=$stock?></td>
                <td><?=$unit?></td>
                <td class="align-right highlight"><?=number_format($balance,2,'.',',')?></td>
            </tr>
            <?php
				}
            }
            ?>
        </table>
        <?php } ?>
        <?php
        }
        ?> 
    
    </div>
    <div style="width:50%; max-height:420px; overflow:auto; float:right;">
        <div class="module_title"><img src='images/book_open.png'>ISSUANCE DETAILS:  </div>
		<?php
        $result=mysql_query("
            select
                _reference,
                d.stock_id,
                stock,
                quantity,
                unit,
                issuance_detail_id,
                price,
                d.description,
                equipment_id,
                account_id,
                joborder_header_id,
				driverID,
				amount,
				p.kg
            from
                issuance_detail as d, productmaster as p
            where
                d.stock_id = p.stock_id
            and
                issuance_header_id = '$issuance_header_id'

        ") or die(mysql_error());
        
        $i=1;
        $total_amount = 0;
        $total_quantity = 0;
        while($r=mysql_fetch_assoc($result)){
            
            $total_amount+=$r['amount'];
            $total_quantity+=$r['quantity'];
            
        ?>
        <table style="border-bottom:1px dashed #c0c0c0; margin:5px 0px; width:100%;">
        	<input type="hidden" name="update_issuance_detail_id[]" value="<?=$r['issuance_detail_id']?>" />
        	<tr>
            	<td>Reference</td>
            	<td><input type="text" class="textbox" name="update_reference[]" value="<?=$r['_reference']?>" /></td>
                
                <td>Quantity</td>
                <td><input type="text" class="textbox3" value="<?=number_format($r['quantity'],2,'.',',')?>" disabled="disabled" /></td>
            </tr>
            <tr>
            
            	<td>kg/pc</td>
                <td><input type="text" class="textbox3" value="<?=number_format($r['kg'],2,'.',',')?>" disabled="disabled" /></td>
                
                <td>Total Kg</td>
                <td><input type="text" class="textbox3" value="<?=number_format($r['quantity'] * $r['kg'],2,'.',',')?>" disabled="disabled" /></td>
            
            </tr>
            <tr>
            	<td>Item</td>
                <td><?=$r['stock']?></td>
                
                <td>Unit</td>
                <td><input type="text" class="textbox3" value="<?=$r['unit']?>" disabled="disabled" /></td>
            </tr>
            <tr>
            	<td>Account</td>
                <td>
                	<input type="text" class="textbox accounts" value="<?=$options->getAttribute('account','account_id',$r['account_id'],'account')?>"  />
                    <input type="hidden" name="update_account_id[]" value="<?=$r['account_id']?>" />					
                </td>
                
                <td>Price</td>
                <td><input type="text" class="textbox3" value="<?=number_format($r['price'],2,'.',',')?>" disabled="disabled" /></td>
            </tr>
            <tr>
            	<td>Equipment</td>
                <td>
					<input type="text" class="textbox eq_he" value="<?=$options->getAttribute('equipment','eqID',$r['equipment_id'],'eq_name')?>"  />
                    <input type="hidden" name="update_equipment_id[]" value="<?=$r['equipment_id']?>" />					
               	</td>
                
                <td>Amount</td>
                <td><input type="text" class="textbox3" value="<?=number_format($r['amount'],2,'.',',')?>" disabled="disabled" /></td>
            </tr>
            <tr>
            	<td>Driver</td>
                <td>
                	<input type="text" class="textbox driver_name" value="<?=$options->getAttribute('drivers','driverID',$r['driverID'],'driver_name')?>"  />
                    <input type="hidden" name="update_driver_id[]" value="<?=$r['driverID']?>" />					
              	</td>
                
                <td>J.O. #</td>
                <td><?=($joborder_header_id)?str_pad($joborder_header_id,7,0,STR_PAD_LEFT):""?></td>
            </tr>
            <?php if($status == "S"){ ?>
            <tr>
                <td colspan="2">
                	<a href="admin.php?view=<?=$view?>&issuance_header_id=<?=$issuance_header_id?>&b=delete&id=<?=$r['issuance_detail_id']?>" onclick="return approve_confirm();"><input type="button" value="Delete" /></a>
                </td>
            </tr>        
            <?php } ?>
        </table>
        <?php } ?>
        <div style="font-weight:bolder; color:#F00; text-align:right;">
        	Total Quantity : <span style="width:400px;"><?=number_format($total_quantity,2,'.',',')?></span> <br />
            Total Amount : <span style="width:400px;"><?=number_format($total_amount,2,'.',',')?></span>
        </div>
   	</div>
    
    <div style="clear:both;">
		<?php if($b == "Print Preview" && $issuance_header_id){ ?>
            <iframe id='JOframe' name='JOframe' frameborder='0' src='print_report_issuance.php?id=<?=$issuance_header_id?>' width='100%' height='500'>
            </iframe>
        <?php } else if($b == "Print Preview Aggr" && $issuance_header_id){ ?>
        	<iframe id='JOframe' name='JOframe' frameborder='0' src='print_report_issuance_aggr.php?id=<?=$issuance_header_id?>' width='100%' height='500'>
            </iframe>
        <?php } ?>
   	</div>
     
    
</div>
</form>
<script type="text/javascript">
j(function(){	

	jQuery(".driver_name").autocomplete({
		source: "list_drivers.php",
		minLength: 1,
		select: function(event, ui) {
			jQuery(this).val(ui.item.value);
			jQuery(this).next().val(ui.item.id);
		}
	});
	
	jQuery(".eq_he").autocomplete({
		source: "dd_equipment_he.php",
		minLength: 2,
		select: function(event, ui) {
			j(this).val(ui.item.value);
			j(this).next().val(ui.item.id);
		}
	});

	j("#work_category_id").change(function(){
		xajax_display_subworkcategory(this.value);
	});
	
	<?php
	if(!empty($status)){
	?>
		xajax_display_subworkcategory('<?=$work_category_id?>','<?=$sub_work_category_id?>');
		xajax_update_scope_of_work('<?=$project_id?>','<?=$scope_of_work?>');
	<?php
	}
	?>
	
	j("#cost,#quantity").keyup(function(){
		var price = document.getElementById("cost").value;
		var quantity = document.getElementById("quantity").value;
		
		var amount = price * quantity;
		var amountFormatted = Number(amount);
		document.getElementById("amount").value=amountFormatted.toFixed(2);
	});
});

</script>