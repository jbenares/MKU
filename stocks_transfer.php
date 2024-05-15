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
	$transfer_header_id		= $_REQUEST['transfer_header_id'];
	$date					= $_REQUEST['date'];
	$project_id				= $_REQUEST['project_id'];
	$project_name			= $options->attr_Project($project_id,'project_name');
	$project_code			= $options->attr_Project($project_id,'project_code');
	$project_name_code		= ($project_id)?"$project_name - $project_code":"";
	$remarks				= $_REQUEST['remarks'];
	$auto_issue				= ($_REQUEST['auto_issue'])?1:0;
	
	$reference				= $_REQUEST['reference'];
		
	$stock_id				= $_REQUEST['stock_id'];
	$quantity				= $_REQUEST['quantity'];
	
	$transfer_detail_id		= $_REQUEST['transfer_detail_id'];
	$detail_quantity		= $_REQUEST['detail_quantity'];
	$checkList				= $_REQUEST['checkList'];
	
	$scope_of_work		= $_REQUEST['scope_of_work'];
	$work_category_id	= $_REQUEST['work_category_id'];
	$sub_work_category_id = $_REQUEST['sub_work_category_id'];
	
	$user_id				= $_SESSION['userID'];
	
	if ( $b == "Unfinish" ){
		mysql_query("update transfer_header set status = 'S' where transfer_header_id = '$transfer_header_id'") or die(mysql_error());	
		$msg = "Transaction Unfinished";
	}
	
	if($b=="Submit"){
		$query="
			insert into 
				transfer_header
			set
				project_id           = '$project_id',
				remarks              = '$remarks',
				date                 = '$date',
				status               = 'S',
				user_id              = '$user_id',
				work_category_id     = '$work_category_id',
				sub_work_category_id = '$sub_work_category_id',
				scope_of_work        = '$scope_of_work',
				auto_issue           = '$auto_issue',
				reference            = '$reference',
				from_project_id      = '$_REQUEST[from_project_id]',
				encoded_datetime     = now()
		";	
		
		mysql_query($query) or die(mysql_error());
		
		$transfer_header_id = mysql_insert_id();
		$options->insertAudit($transfer_header_id,'transfer_header_id','I');
				
		$msg="Transaction Saved";
		
	}else if($b=="Update"){
		$query="
			update
				transfer_header
			set
				project_id           = '$project_id',
				remarks              = '$remarks',
				date                 = '$date',
				status               = 'S',
				work_category_id     = '$work_category_id',
				sub_work_category_id = '$sub_work_category_id',
				scope_of_work        = '$scope_of_work',
				auto_issue           = '$auto_issue',
				reference            = '$reference',
				from_project_id      = '$_REQUEST[from_project_id]',
				user_id              = '$user_id'
			where
				transfer_header_id = '$transfer_header_id'
		";	
		
		mysql_query($query) or die(mysql_error());
		$options->insertAudit($transfer_header_id,'transfer_header_id','U');
		
		$msg = "Transaction Updated";
		
	}else if($b=="Add Details"){
		
		mysql_query("
			insert into
				transfer_detail
			set	
				transfer_header_id		= '$transfer_header_id',
				stock_id				= '$stock_id',
				quantity				= '$quantity'
		") or die(mysql_error());
		
		$msg = "Transaction Added";
	
	}else if($b=="Update Details"){
				
		$x=0;
		
		foreach($transfer_detail_id as $id):
			
			mysql_query("
				update
					transfer_detail
				set
					quantity = '$detail_quantity[$x]'
				where
					transfer_detail_id = '$id'
			") or die(mysql_error());
			$x++;
		endforeach;	
		
		$msg = "Transaction Details Updated";
	}else if($b=="Cancel"){
		$query="
			update
				transfer_header
			set
				status='C'
			where
				transfer_header_id = '$transfer_header_id'
		";	
		mysql_query($query);
		$options->insertAudit($transfer_header_id,'transfer_header_id','C');
		$msg = "Transaction Cancelled";
		
	}else if($b=="Finish"){
		$query="
			update
				transfer_header
			set
				status = 'F'
			where
				transfer_header_id = '$transfer_header_id'
		";	
		mysql_query($query);
		$options->insertAudit($transfer_header_id,'transfer_header_id','F');
		
		$msg = "Transaction Finished";
		
		#IF AUTO ISSUE
		if($auto_issue){
			$result = mysql_query("select * from transfer_header where transfer_header_id = '$transfer_header_id'") or die(mysql_error());
			$r = mysql_fetch_assoc($result);
			$project_id = $r['project_id'];
			$date	 	= $r['date'];
			$work_category_id	= $r['work_category_id'];
			$sub_work_category_id	 = $r['sub_work_category_id'];
			
			mysql_query("
				insert into 
					issuance_header
				set
					date = '$date',
					project_id = '$project_id',
					status = 'S',
					user_id = '$user_id',
					work_category_id = '$work_category_id',
					sub_work_category_id = '$sub_work_category_id',
					reference = 'TS# $transfer_header_id'
			") or die(mysql_error());
			$issuance_header_id = mysql_insert_id();
			
			$result = mysql_query("select * from transfer_detail where transfer_header_id = '$transfer_header_id'") or die(mysql_error());
			while($r = mysql_fetch_assoc($result)){
				$stock_id = $r['stock_id'];
				$quantity = $r['quantity'];
				$price 	= $r['price'];
				$amount = $r['amount'];
				
				mysql_query("
					insert into 
						issuance_detail
					set
						issuance_header_id = '$issuance_header_id',
						stock_id = '$stock_id',
						quantity = '$quantity',
						price = '$price',
						amount = '$amount'
				") or die(mysql_error());
			}
			
			header("Location: admin.php?view=02bb738f4e1ab460dd47&issuance_header_id=$issuance_header_id");
				
		}
		
	}else if($b=="Delete Details"){	
		if(!empty($checkList)){
			foreach($checkList as $list){
				
				mysql_query("
					delete from
						transfer_detail
					where
						transfer_detail_id = '$list'
				") or die(mysql_error());
				
				$options->insertAuditTrail($description,$user_id,$header_id,$trans);
				
				
				$stock_id = $options->getAttribute("transfer_detail","transfer_detail_id",$list,"stock_id"); #CHANGE 
				$_header_id = $options->getAttribute("transfer_detail","transfer_detail_id",$list,"transfer_header_id"); #CHANGE
				$stock		= $options->getAttribute("productmaster","stock_id",$stock_id,"stock");
				$_desc = $options->getUserName($user_id)." deleted $stock in TS# $_header_id on ".date("m/d/Y h:i:s A");
				$options->insertAuditTrail($_desc,$user_id,$_header_id,"TS");
			}
		}
	}else if($b=="New"){
		header("Location: admin.php?view=$view");
	}

	$query="
		select
			*
		from
			transfer_header
		where
			transfer_header_id = '$transfer_header_id'
	";
	
	$result=mysql_query($query);
	$r = $aVal = mysql_fetch_assoc($result);

	$project_id			= $r['project_id'];
	$project_name		= $options->attr_Project($project_id,'project_name');
	$project_code		= $options->attr_Project($project_id,'project_code');
	$project_name_code	= ($project_id)?"$project_name - $project_code":"";
	$date				= ($r['date']=="0000-00-00")?"":$r['date'];
	$remarks			= $r['remarks'];
	$status				= $r['status'];
	$auto_issue			= $r['auto_issue'];
	$reference			= $r['reference'];
	
	$scope_of_work		= $r['scope_of_work'];
	$work_category_id 	= $r['work_category_id'];
	$sub_work_category_id = $r['sub_work_category_id'];
	$user_id			 = $r['user_id'];

?>
<div class=form_layout>
	<?php if(!empty($msg)) echo '<div id="status_update" class="ui-state-highlight ui-corner-all" style="padding: 0px 0.7em; text-align:left;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right:.3em;"></span> '.$msg.'</p></div>'; ?>
	<div class="module_title"><img src='images/user_orange.png'>STOCKS TRANSFER</div>
    <form name="header_form" id="header_form" action="" method="post">
    <div class="module_actions">
        <input type="hidden" name="transfer_header_id" id="transfer_header_id" value="<?=$transfer_header_id?>" />
        <input type="hidden" name="view" value="<?=$view?>" />
        <div id="messageError">
            <ul>
            </ul>
        </div>
        
        <div class="inline">
        	Date : <br />
            <input type="text" class="textbox3 required datepicker" title="Enter Date" name="date" value="<?=$date?>" />
        </div>

        <div class='inline'>
        	<?php $aVal['from_project_id'] = (!empty($aVal['from_project_id'])) ? $aVal['from_project_id'] : 9; ?>
        	
            From Project/Location : <br />  
            <input type="text" class="textbox project"  value="<?=$options->getAttribute('projects','project_id',$aVal['from_project_id'],'project_name')?>" onclick="this.select();"  />
            <input type="hidden" class="required" name="from_project_id"  value="<?=$aVal['from_project_id']?>" title="Select From Project" />
        </div>   
        
        <div class='inline'>
            To Project/Location : <br />  
            <input type="text" class="textbox" id="project_name" value="<?=$project_name_code?>" onclick="this.select();"  />
            <input type="hidden" class="required" name="project_id"  id="project_id" value="<?=$project_id?>" title="Select Project" />
        </div>           
        
        <div class="inline">
        	Work Category : <br />
            <?=$options->option_workcategory($work_category_id,'work_category_id','Select Work Category')?>
        </div>
        <div id="subworkcategory_div" style="display:none;" class="inline">
        	Sub Work Category :
			<div id="subworkcategory">
            	
            </div>
        </div>
        
        <br />
         <div class='inline'>
            Remarks : <br />
            <textarea class="textarea_small" name='remarks'><?=$remarks?></textarea>
        </div>          
        <br />    
          <div class='inline'>
        	Reference : <br />
            <input type="text" name="reference" value="<?=$reference?>" class="textbox3" />
        </div>
        <?php
        if(!empty($status)){
        ?>
       <div class='inline' style="vertical-align:top;">
                TS # : <br />
                <input type="text" class="textbox3" name="status" id="status" value="<?=str_pad($transfer_header_id,7,0,STR_PAD_LEFT)?>" readonly="readonly"/>
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
					 <?php
                    }
                    ?>
					
		
		<div class='inline'>
        	<?php if( $aVal['from_project_id'] == 9 ){ ?>
	        	<?php $auto_issue_selected = ($auto_issue)?"checked='checked'":"" ?>
	        	Auto Issue:<input type="checkbox" name="auto_issue" value="1" <?=$auto_issue_selected?>/>
				
	        <?php } ?>
        </div>
    </div>
    <div class="module_actions">
    	<input type="submit" name="b" value="New" />
		<?php if($status=="S"){ ?>
        <input type="submit" name="b" id="b" value="Update" />
        <input type="submit" name="b" id="b" value="Finish" />
        <?php }else if($status!="F" && $status!="C"){ ?>
        <input type="submit" name="b" id="b" value="Submit" />
        <?php } if($b!="Print Preview" && !empty($status)){ ?>
            <input type="submit" name="b" id="b" value="Print Preview" />
        <?php } if($b=="Print Preview"){ ?>	
            <input type="button" value="Print" onclick="printIframe('JOframe');" />
        <?php } if($status!="C" && !empty($status)){ ?>
        <input type="submit" name="b" id="b" value="Cancel" />
        <?php } ?>
        <?php if($status == "F" && !$auto_issue) { ?>
        <input type="submit" name="b" id="b" value="Unfinish" />
        <?php } ?>
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
    <div class="module_actions">
    	<div class="module_title"><img src='images/database_table.png'>SEARCH RESULTS : </div>
        <table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table" id="search_table">
            <tr bgcolor="#C0C0C0">				
                <th width="20"><b>#</b></th>
                <th width="30"></th>
                <th>Item</th>                
                <th width="100">Warehouse Quantity</th>
                <th width="60">Unit</th>
            </tr> 
            <?php
			$result=mysql_query("
				select
					*
				from
					productmaster
				where
					stock like '%".$_REQUEST['search_item']."%'
				order by stock
			") or die(mysql_error());
			
			$i=1;
			while($r=mysql_fetch_assoc($result)){
				$stock_id 		= $r['stock_id'];
				$stock			= $r['stock'];
				$unit			= $r['unit'];
						
				// if( $aVal['from_project_id'] == 9 ){ /*warehouse quantity*/
				// 	$warehouse_qty  = $options->inventory_warehouse($date,$stock_id);
				// } else {
				// 	$warehouse_qty = $options->inventory_projectwarehousebalance($date,$stock_id,$aVal['from_project_id']);
				// }
				$warehouse_qty = $options->inventory_projectwarehousebalance($date,$stock_id,$aVal['from_project_id']);
				if($warehouse_qty > 0){
            ?>
            <tr>
            	<td><?=$i++?></td>
                <td><input type="button" value="Transfer" onclick="xajax_transfer_stock_id_form('<?=$stock_id?>');" /></td>
                <td><?=$stock?></td>
                <td class="align-right"><?=number_format($warehouse_qty,2,'.',',')?></td>
                <td><?=$unit?></td>
            </tr>
            <?php
				}
			}
            ?>
        </table>
    </div>
    <?php } ?>
    <?php
    }
    ?> 
  
    <?php
	if($b == "Print Preview" && $transfer_header_id){

		echo "	<iframe id='JOframe' name='JOframe' frameborder='0' src='print_report_transmittal.php?id=$transfer_header_id' width='100%' height='500'>
		       	</iframe>";
	?>
    <?php
	}else if(!empty($status) && $b!="Print Preview"){
    ?>
    <div class="module_actions" >
	    <div class="module_title"><img src='images/book_open.png'>STOCKS TRANSFER DETAILS :  </div>
        <table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table" id="search_table">
        <div class="module_actions">
        	<?php
			if($status=="S"){
            ?>
	        <input type="submit" name="b" value="Delete Details" onclick="return approve_confirm();"/>
            <?php
			}
            ?>
       	</div>
            <tr bgcolor="#C0C0C0">				
                <th width="20"><b>#</b></th>
                <th width="20"><input type="checkbox"  name="checkAll" onclick="javascript:check_all('header_form', this)" title="Check/Uncheck All" /></th>
                <th>Item</th>
                <th style="width:5%;">Quantity</th>
                <th style="width:5%;">Unit</th>
                
                <th style="width:5%;">kg/pc</th>
                <th style="width:5%;">total kg</th>
                
                <th style="width:5%;">Price</th>
                <th style="width:5%;">Amount</th>
            </tr> 
            <?php
			$result=mysql_query("
				select
					d.stock_id,
					stock,
					quantity,
					unit,
					transfer_detail_id,
					price,
					amount,
					p.kg
				from
					transfer_detail as d, productmaster as p
				where
					d.stock_id = p.stock_id
				and
					transfer_header_id = '$transfer_header_id'
			") or die(mysql_error());
			
			$i=1;
			while($r=mysql_fetch_assoc($result)){
				$stock_id 			= $r['stock_id'];
				$stock				= $r['stock'];
				$quantity			= $r['quantity'];
				$unit				= $r['unit'];
				$transfer_detail_id	= $r['transfer_detail_id'];
				$price 				= $r['price'];
				$amount				= $r['amount'];
			
            ?>
            <tr>
            	<td><?=$i++?></td>
                <td><input type="checkbox" name="checkList[]" value="<?=$transfer_detail_id?>" onclick="document.header_form.checkAll.checked=false"></td>
                <td><?=$stock?></td>
                <td class="align-right"><?=number_format($quantity,2,'.',',')?></td>
                <td><?=$unit?></td>
                
                <td class="align-right"><?=number_format($r['kg'],2,'.',',')?></td>
                <td class="align-right"><?=number_format($r['kg'] * $r['quantity'],2,'.',',')?></td>
                
                <td class="align-right"><?=number_format($price,2,'.',',')?></td>
                <td class="align-right"><?=number_format($amount,2,'.',',')?></td>
            </tr>
            <?php
			}
            ?>
        </table>
    </div>
    <?php
    }
	?>
    </form>
    
</div>
</script>
<script type="text/javascript">
j(function(){	
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
});
</script>	