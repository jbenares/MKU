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
	$b							= $_REQUEST['b'];
	$service_rr_header_id		= $_REQUEST['service_rr_header_id'];
	$po_header_id				= $_REQUEST['po_header_id'];
	$po_header_id_pad 			= (!empty($po_header_id))?str_pad($po_header_id,7,0,STR_PAD_LEFT):"";
	$date						= $_REQUEST['date'];
	$paytype					= $_REQUEST['paytype'];
	$rr_in						= $_REQUEST['rr_in'];
	$project_id					= $_REQUEST['project_id'];
	$supplier_id				= $_REQUEST['supplier_id'];
	
	$scope_of_work		= $_REQUEST['scope_of_work'];
	$work_category_id	= $_REQUEST['work_category_id'];
	$sub_work_category_id = $_REQUEST['sub_work_category_id'];
		
	$user_id			= $_SESSION['userID'];
	
	$stock_id 	= $_REQUEST['stock_id'];
	$stock_name	= $_REQUEST['stock_name'];
	
	$checkList	= $_REQUEST['checkList'];
	
	if($b=="Submit"){
		$query="
			insert into 
				service_rr_header
			set
				date='$date',
				user_id='$user_id',
				status='S',
				po_header_id='$po_header_id',
				paytype = '$paytype',
				project_id = '$project_id',
				supplier_id = '$supplier_id',
				work_category_id 		= '$work_category_id',
				sub_work_category_id 	= '$sub_work_category_id',
				scope_of_work			= '$scope_of_work'
		";	
		
		mysql_query($query) or die(mysql_error());
		
		$service_rr_header_id = mysql_insert_id();
		$options->insertAudit($service_rr_header_id,'service_rr_header_id','I');
		
		$msg="Transaction Saved";
		
	}else if($b=="Update"){
		$query="
			update
				service_rr_header
			set
				date='$date',
				user_id='$user_id',
				status='S',
				po_header_id='$po_header_id',
				paytype = '$paytype',
				project_id = '$project_id',
				supplier_id = '$supplier_id',
				work_category_id 		= '$work_category_id',
				sub_work_category_id 	= '$sub_work_category_id',
				scope_of_work			= '$scope_of_work'
			where
				service_rr_header_id='$service_rr_header_id'
		";	
		
		mysql_query($query) or die(mysql_error());
		$options->insertAudit($service_rr_header_id,'service_rr_header_id','U');		
		
		$msg = "Transaction Updated";
		
	}else if($b=="Cancel"){
		$query="
			update
				service_rr_header
			set
				status='C'
			where
				service_rr_header_id='$service_rr_header_id'
		";	
		mysql_query($query) or die(mysql_error());
		$options->insertAudit($service_rr_header_id,'service_rr_header_id','C');
		
		$msg = "Transaction Cancelled";
		
	}else if($b=="Finish"){
		$query="
			update
				service_rr_header
			set
				status='F'
			where
				service_rr_header_id='$service_rr_header_id'
		";	
		mysql_query($query) or die(mysql_error());
		$options->insertAudit($service_rr_header_id,'service_rr_header_id','F');
		$msg = "Transaction Finished";
		
	}else if($b=="Update Details"){
		$service_rr_detail_id=$_REQUEST[service_rr_detail_id];
		
		$quantity		= $_REQUEST[quantity];
		$cost			= $_REQUEST[cost];
		$package_id		= $_REQUEST[package_id];
		
		$x=0;
		
		foreach($service_rr_detail_id as $id):
			$packageqty=$options->getPackageQty($package_id[$x]);
		
			if($package_id[$x]){
				$amount=$quantity[$x] * $cost[$x] * $packageqty;
			}else{
				$amount=$quantity[$x] * $cost[$x];
			}
			
			mysql_query("
				update
					service_rr_detail
				set
					quantity='$quantity[$x]',
					amount='$amount'
				where
					service_rr_detail_id='$id'
			") or die(mysql_error());
			$x++;
		endforeach;	
		
		$msg = "Transaction Details Updated";
	}else if($b=="Delete Details"){
		
		if(!empty($checkList)){
			foreach($checkList as $id){

				mysql_query("
					delete from
						service_rr_detail
					where	
						service_rr_detail_id = '$id'
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
			service_rr_header as h, projects as p
		where
			h.project_id = p.project_id
		and
			service_rr_header_id ='$service_rr_header_id'
	";
	
	$result=mysql_query($query);
	$r=mysql_fetch_assoc($result);
	
	
	$service_rr_header_id		= $r['service_rr_header_id'];
	$service_rr_header_id_pad	= (!empty($service_rr_header_id))?str_pad($service_rr_header_id,7,0,STR_PAD_LEFT):"";
	$po_header_id		= $r['po_header_id'];
	$po_header_id_pad 	= (!empty($po_header_id))?str_pad($po_header_id,7,0,STR_PAD_LEFT):"";
	$date				= $r['date'];
	$paytype			= $r['paytype'];
	$project_id 	 	= $r['project_id'];
	$rr_in				= $r['rr_in'];
	
	$project_name		= $options->attr_Project($project_id,'project_name');
	$project_code		= $options->attr_Project($project_id,'project_code');
	$project_display	= ($project_id)?"$project_name - $project_code":"";
	
	$supplier_id		= $r['supplier_id'];
	$supplier			= $options->attr_Supplier($supplier_id,'account');
	
	$user_id			= $r['user_id'];
	$status				= $r['status'];
	
	$scope_of_work		= $r['scope_of_work'];
	$work_category_id 	= $r['work_category_id'];
	$sub_work_category_id = $r['sub_work_category_id'];
	
	$work_category		= $options->attr_workcategory($work_category_id,'work');
	$sub_work_category	= $options->attr_workcategory($sub_work_category_id,'work');
	

?>
<div class=form_layout>
	<?php if(!empty($msg)) echo '<div id="status_update" class="ui-state-highlight ui-corner-all" style="padding: 0px 0.7em; text-align:left;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right:.3em;"></span> '.$msg.'</p></div>'; ?>
	<div class="module_title"><img src='images/user_orange.png'>SERVICE RECEIVING</div>
    <form name="header_form" id="header_form" action="" method="post">
    <div class="module_actions">
        <input type="hidden" name="service_rr_header_id" id="service_rr_header_id" value="<?=$service_rr_header_id?>" />
        <input type="hidden" name="view" value="<?=$view?>" />
        <div id="messageError">
            <ul>
            </ul>
        </div>
        <div class='inline'>
            <div>Date: </div>        
            <div>
                <input type="text" class="datepicker required textbox3" title="Please enter date"  name="date" readonly='readonly'  value="<?=$date?>">
            </div>
        </div>    
                
        <div class='inline'>
            <div>PO # : </div>        
            <div>
            	<input type="text" class="textbox3" id="po_name" value="<?=$po_header_id_pad?>"/>
                <input type="hidden" name="po_header_id" id="po_header_id" value="<?=$po_header_id?>"  />
                <img src="images/folder.png" id="folder" style="cursor:pointer;" />
            </div>
        </div>  
        <br />
        <div class="inline">
        	Project : <br />
        	<input type="text" class="textbox" id="project_display" value="<?=$project_display?>" readonly="readonly" />
            <input type="hidden" name="project_id" id="project_id" value="<?=$project_id?>" />
        </div>
        
        <div class="inline">
        	Supplier : <br />
            <input type="text" class="textbox" id="supplier_name" value="<?=$supplier?>" readonly="readonly" />
            <input type="hidden" name="supplier_id" id="supplier_id" value="<?=$supplier_id?>" />
        </div>
        
        <br />
        
        <div class="inline">
        	Scope of Work : <br />
            <input type="text" class="textbox" id="scope_of_work"  name="scope_of_work" value="<?=$scope_of_work?>" readonly="readonly" />
        </div>
        
        <div class="inline">
        	Work Category : <br />
            <input type="text" class="textbox" name="work_category" id="work_category" value="<?=$work_category?>"  readonly="readonly" />
            <input type="hidden" id="work_category_id" name="work_category_id" value="<?=$work_category_id?>"  />
        </div>
        
        <div class="inline">
        	Sub Work Category : <br />
            <input type="text" class="textbox" name="sub_work_category" id="sub_work_category" value="<?=$sub_work_category?>" readonly="readonly"  />
            <input type="hidden" name="sub_work_category_id" id="sub_work_category_id" value="<?=$sub_work_category_id?>" />
        </div>
        
        <br />
        <!--
        <div>
        	Receive in: <br />
			<?=$options->option_rr_in($rr_in)?>
        </div>
        -->
        <div class='inline'>
            <div>Pay Type : </div>        
            <div>
            <?php
                echo $options->getPayTypeOptions($paytype);
            ?>
            </div>
        </div> 
        
        <?php
        if(!empty($status)){
        ?>
        <br />
        <div class="inline">
        	Service RR # : <br />
            <input type="text" class="textbox3" name="status" id="status" value="<?=$service_rr_header_id_pad?>" readonly="readonly"/>
        </div>
        
        <div class='inline'>
            <div>Status : </div>        
            <div>
                <input type="text" class="textbox3" name="status" id="status" value="<?=$options->getTransactionStatusName($status)?>" readonly="readonly"/>
            </div>
        </div> 
        <br />
        
        <div class='inline'>
            <div>User : </div>        
            <div>
                <input type='text' class="textbox2" value="<?=$options->getUserName($user_id);?>" readonly="readonly" />
            </div>
        </div> 
        <?php
        }
        ?>
    </div>
    <div class="module_actions">
    	<input type="submit" name="b" value="New" />
		<?php
        if($status=="S"){
        ?>
        <input type="submit" name="b" id="b" value="Update" />
        <input type="submit" name="b" id="b" value="Finish" />
        
        <?php
        }else if($status!="F" && $status!="C"){
        ?>
        <input type="submit" name="b" id="b" value="Submit" />
        <?php
        }
        
        if($b!="Print Preview" && !empty($status)){
        ?>
            <input type="submit" name="b" id="b" value="Print Preview" />
        <?php
        }
    
        if($b=="Print Preview"){
        ?>	
            <input type="button" value="Print" onclick="printIframe('JOframe');" />
    
        <?php
        }
        if($status!="C" && !empty($status)){
        ?>
        <input type="submit" name="b" id="b" value="Cancel" />
        <?php
        }
		?>
   	</div>
	<?php
    if($status=="S"){
    ?>
    <div class="module_actions">
    	<div class="module_title"><img src='images/database_table.png'>SEARCH RESULTS : </div>
        <table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table" id="search_table">
            <tr bgcolor="#C0C0C0">				
                <th width="20"><b>#</b></th>
                <th width="30"></th>
                <th>Designation</th>
                <th>No</th>
                <th>No. of Days</th>
                <th>Rate/Day</th>
                <th>Amount</th>
            </tr> 
            <?php
			$result=mysql_query("
				select
					d.stock_id,
					stock,
					quantity,
					days,
					rate_per_day,
					amount
				from
					po_service_detail as d, productmaster as p,po_header as h, categories as c
				where
					h.po_header_id = d.po_header_id
				and
					d.stock_id = p.stock_id
				and
					p.categ_id1 = c.categ_id
				and
					h.supplier_id = '$supplier_id'
				and
					project_id = '$project_id'
				and
					d.po_header_id = '$po_header_id'
				and
					c.category_type = 'S'
			") or die(mysql_error());
			
			$i=1;
			while($r=mysql_fetch_assoc($result)){
				$stock_id					= $r['stock_id'];
				$stock						= $r['stock'];
				$quantity					= $r['quantity'];
				$days						= $r['days'];
				$rate_per_day				= $r['rate_per_day'];
				$unit						= $r['unit'];
				$amount						= $r['amount'];
				
            ?>
            <tr>
            	<td><?=$i++?></td>
                <td><input type="button" value="Receive" onclick="xajax_service_receive_stock_id_form('<?=$stock_id?>','<?=$quantity?>','<?=$days?>','<?=$rate_per_day?>','<?=$amount?>');" /></td>
                <td><?=$stock?></td>
                <td class="align-right"><?=$quantity?></td>
                <td class="align-right"><?=$days?></td>
                <td class="align-right"><?=$rate_per_day?></td>
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
    
    <?php
    if(!empty($status) && $b!="Print Preview"){
    ?>
    <div class="module_actions" >
	    <div class="module_title"><img src='images/book_open.png'>SERVICE RECEIVING DETAILS:  </div>
        <div class="module_actions">
        	<?php
			if($status=="S"){
            ?>
	        <input type="submit" name="b" value="Delete Details" onclick="return approve_confirm();"/>
            <?php
			}
            ?>
       	</div>
        <table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table" id="search_table">
            <tr bgcolor="#C0C0C0">				
                <th width="20"><b>#</b></th>
                <th width="20"><input type="checkbox"  name="checkAll" onclick="javascript:check_all('header_form', this)" title="Check/Uncheck All" /></th>
                <th>Designation</th>
                <th>No</th>
                <th>No. of Days</th>
                <th>Rate/Day</th>
                <th>Amount</th>
            </tr> 
            <?php
			$result=mysql_query("
				select
					stock,
					d.stock_id,
					quantity,
					days,
					rate_per_day,
					amount,
					service_rr_detail_id
				from
					service_rr_detail as d,
					service_rr_header as h,
					productmaster as pm
				where
					h.service_rr_header_id = d.service_rr_header_id
				and
					pm.stock_id = d.stock_id
				and
					h.service_rr_header_id = '$service_rr_header_id'
			") or die(mysql_error());
			
			$i=1;
			while($r=mysql_fetch_assoc($result)){
				$stock_id					= $r['stock_id'];
				$stock						= $r['stock'];
				$quantity					= $r['quantity'];
				$days						= $r['days'];
				$rate_per_day				= $r['rate_per_day'];
				$unit						= $r['unit'];
				$amount						= $r['amount'];
				$service_rr_detail_id		= $r['service_rr_detail_id'];
				
            ?>
            <tr>
            	<td><?=$i++?></td>
                <td><input type="checkbox" name="checkList[]" value="<?=$service_rr_detail_id?>" onclick="document.header_form.checkAll.checked=false"></td>
                <td><?=$stock?></td>
                <td class="align-right"><?=$quantity?></td>
                <td class="align-right"><?=$days?></td>
                <td class="align-right"><?=$rate_per_day?></td>
                <td class="align-right"><?=number_format($amount,2,'.',',')?></td>
            </tr>
            <?php
			}
            ?>
        </table>
    </div>
    <?php
    }else if($b == "Print Preview" && $service_rr_header_id){

		echo "	<iframe id='JOframe' name='JOframe' frameborder='0' src='print_report_service_rr.php?id=$service_rr_header_id' width='100%' height='500'>
		       	</iframe>";
	}
    ?>
     </form>
    
</div>
<script type="text/javascript">
j(function(){	
	
	j("#cost,#quantity").keyup(function(){
		var price = document.getElementById("cost").value;
		var quantity = document.getElementById("quantity").value;
		
		var amount = price * quantity;
		var amountFormatted = Number(amount);
		document.getElementById("amount").value=amountFormatted.toFixed(2);
	});
	
	j("#folder").dblclick(function(){
		xajax_show_po();
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
});
</script>


	