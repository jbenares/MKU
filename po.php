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
	$project_id			= $options->getProjectIDFromPR($pr_header_id);
	$project_name		= $options->attr_Project($project_id,'project_name');
	$project_code		= $options->attr_Project($project_id,'project_code');
	$project_name_code	= ($project_id)?"$project_name - $project_code":"";

	$b					= $_REQUEST['b'];
	$warehouse_cb		= $_REQUEST['warehouse_cb'];
	
	$po_header_id		= $_REQUEST['po_header_id'];
	$pr_header_id		= $_REQUEST['pr_header_id'];
	$project_id			= $_REQUEST['project_id'];
	
	$supplier_id		= ($warehouse_cb)?0:$_REQUEST['supplier_id'];
	$terms				= $_REQUEST['terms'];
	$remarks			= $_REQUEST['remarks'];
	
	$scope_of_work		= $_REQUEST['scope_of_work'];
	$work_category_id	= $_REQUEST['work_category_id'];
	$sub_work_category_id = $_REQUEST['sub_work_category_id'];
	$discount_amount 	= $_REQUEST['discount_amount'];
	
	$checkList			= $_REQUEST['checkList'];
	
	$date				= $_REQUEST['date'];
	$user_id			= $_SESSION['userID'];
	
	$stock_id			= $_REQUEST['stock_id'];
	$quantity			= $_REQUEST['quantity'];
	$cost				= $_REQUEST['cost'];

	$id					= $_REQUEST['id'];
	
	$vat				= $_REQUEST['vat'];
	$wtax				= $_REQUEST['wtax'];
	
	//$note 				= $_REQUEST['note'];
	
	if($_SESSION[userID] == '20160719-110150' || $_SESSION[userID] == '20200311-050946' || $_SESSION[userID] == '20170830-120801' || $_SESSION[userID] == '20200319-055723'){
		$old_id_po = $_REQUEST['old_id_po'];
	}
	
	function checkExistRR($po_header_id){
		$c = 0;
		
		$q =  mysql_query("select *
							from
							po_header as h, rr_header as r where
							h.po_header_id = r.po_header_id and
							h.po_header_id = '$po_header_id' and
							r.`status` != 'C'") or die (mysql_error());
		$c = mysql_num_rows($q);

		if($c > 0){
			return true;
		}else{
			return false;
		}
	}
	
	if($b == "Unfinish"){
		mysql_query("update po_header set status = 'S' where po_header_id = '$po_header_id'") or die(mysql_error());	
	}
	
	if($b == "M"){
		#INSERT AUDIT TRAIL
		$stock_id = $options->getAttribute("po_detail","po_detail_id",$id,"stock_id");
		$_header_id = $options->getAttribute("po_detail","po_detail_id",$id,"po_header_id");
		$stock		= $options->getAttribute("productmaster","stock_id",$stock_id,"stock");
		$_desc = $options->getUserName($user_id)." deleted $stock in PO# $_header_id on ".date("m/d/Y h:i:s A");
		$options->insertAuditTrail($_desc,$user_id,$_header_id,"PO");
		
		mysql_query("
			delete from
				po_detail
			where
				po_detail_id = '$id'
		") or die(mysql_error());	
	}else if($b == "F"){
		mysql_query("
			delete from
				po_fuel_detail
			where
				po_fuel_detail_id = '$id'
		") or die(mysql_error());	
	}else if($b == "S"){
		mysql_query("
			delete from
				po_service_detail
			where
				po_service_detail_id = '$id'
		") or die(mysql_error());	
	}else if($b == "E"){
		mysql_query("
			delete from
				po_equipment_detail
			where
				po_equipment_detail_id = '$id'
		") or die(mysql_error());	
	}
	
	if($b=="Submit"){
			
		if($old_id_po > 0){	
		$sql = mysql_query("select * from po_header where po_header_id = '$old_id_po'") or die(mysql_error());	
			$count = mysql_num_rows($sql);
			if($count > 0){
				$msg = "Error, PO already exist";
			}else{
				$query="
				insert into
				po_header
				set
				po_header_id		 = '$old_id_po',
				date                 ='$date',
				project_id           ='$project_id',
				status               ='S',
				user_id              = '$user_id',
				pr_header_id         = '$pr_header_id',
				supplier_id          = '$supplier_id',
				terms                = '$terms',
				work_category_id     = '$work_category_id',
				sub_work_category_id = '$sub_work_category_id',
				scope_of_work        = '$scope_of_work',
				remarks              = '$remarks',
				vat                  = '$vat',
				wtax                 = '$wtax',
				discount_amount      = '$discount_amount',
				note                 = '$note',
				no_of_days_delivery  = '$_REQUEST[no_of_days_delivery]',
				datetime_encoded     = '".lib::now()."',
				approval_status 	 = 'A'
				";	
				
				$msg = "Transaction Saved";		
				mysql_query($query) or die(mysql_error());
						
				$options->insertAudit($old_id_po,'po_header_id','I');
				header("Location: admin.php?view=$view&po_header_id=".$old_id_po);
			}
			
		
		
		}else{
			$query="
			insert into
				po_header
			set
				date                 ='$date',
				project_id           ='$project_id',
				status               ='S',
				user_id              = '$user_id',
				pr_header_id         = '$pr_header_id',
				supplier_id          = '$supplier_id',
				terms                = '$terms',
				work_category_id     = '$work_category_id',
				sub_work_category_id = '$sub_work_category_id',
				scope_of_work        = '$scope_of_work',
				remarks              = '$remarks',
				vat                  = '$vat',
				wtax                 = '$wtax',
				discount_amount      = '$discount_amount',
				note                 = '$note',
				no_of_days_delivery  = '$_REQUEST[no_of_days_delivery]',
				datetime_encoded     = '".lib::now()."',
				approval_status 	 = 'A'
			";	
			mysql_query($query) or die(mysql_error());
		
			$po_header_id = mysql_insert_id();
			$options->insertAudit($po_header_id,'po_header_id','I');
		}
		
	}else if($b=="Update"){
			
		$query="
			update
				po_header
			set
				date                 ='$date',
				project_id           ='$project_id',
				status               ='S',
				user_id              = '$user_id',
				pr_header_id         = '$pr_header_id',
				supplier_id          = '$supplier_id',
				terms                = '$terms',
				work_category_id     = '$work_category_id',
				sub_work_category_id = '$sub_work_category_id',
				scope_of_work        = '$scope_of_work',
				remarks              = '$remarks',
				vat                  = '$vat',
				wtax                 = '$wtax',
				discount_amount      = '$discount_amount',
				note                 = '$note',
				no_of_days_delivery  = '$_REQUEST[no_of_days_delivery]'
			where
				po_header_id='$po_header_id'
		";	
		
		mysql_query($query) or die(mysql_error());
		$options->insertAudit($po_header_id,'po_header_id','U');
		
		$msg = "Transaction Updated";		
		
	}else if($b=="Cancel"){
		
		$check_exist = checkExistRR($po_header_id);
		
		if($check_exist == true){
			$msg = "Invalid Request, Materials Receiving Order exist under this Purchase Order Reference!";
			
		}else{
		$query="
			update
				po_header
			set
				status='C'
			where
				po_header_id='$po_header_id'
		";	
		mysql_query($query) or die(mysql_error());
		$options->insertAudit($po_header_id,'po_header_id','C');
		}
	}
	else if($b=="Finish"){
		$query="
			update
				po_header
			set
				status='F'
			where
				po_header_id='$po_header_id'
		";	
		mysql_query($query) or die(mysql_error());
		$options->insertAudit($po_header_id,'po_header_id','F');
		
		/*
		INSERT INTO ACCOUNTS PAYABLE SERVICE PO
		*/
		$po_header_id_pad	= str_pad($po_header_id,7,0,STR_PAD_LEFT);
		
		$result=mysql_query("
			select
				sum(amount) as amount,
				terms,
				supplier_id,
				date
			from	
				po_header as h, po_service_detail as d
			where
				h.po_header_id = d.po_header_id
			and
				h.po_header_id = '$po_header_id'
		") or die(mysql_error());
		
		$r = mysql_fetch_assoc($result);
		$netamount 		= $r['amount'];
		$terms			= $r['terms'];
		$supplier_id 	= $r['supplier_id'];
		$date			= $r['date'];
		
		$due_date = date("Y-m-d",strtotime("+$terms days",strtotime($date)));
		
		
		if( $amount > 0 ) {
			mysql_query("
				insert into
					accounts_payable
				set
					header 			= 'po_header_id',
					header_id 		= '$po_header_id',
					total_amount	= '$netamount',
					due_date		= '$due_date',
					supplier_id		= '$supplier_id',
					reference		= 'P.O # : ".$po_header_id_pad."',
					tbl				= 'po_header_id',
					type			= 'S'
					
			") or die(mysql_error());
		}
	}else if($b=="Add"){
		$amount = $cost * $quantity;
		
		mysql_query("
			insert into
				po_detail
			set
				po_header_id = '$po_header_id',
				stock_id = '$stock_id',
				quantity = '$quantity',
				cost = '$cost',
				amount = '$amount'
		") or die(mysql_error());
	}else if($b=="Delete Details"){
		if(!empty($checkList)) {
			foreach($checkList as $ch) {	
				$query="
					delete from
						po_detail
					where
						po_detail_id='$ch'
				";
				mysql_query($query) or die(mysql_error());
				
			}
		  }
	}else if($b=="Delete Service Selected"){
		if(!empty($checkList)) {
			foreach($checkList as $ch) {	
				$query="
					delete from
						po_service_detail
					where
						po_service_detail_id='$ch'
				";
				mysql_query($query) or die(mysql_error());
			}
		  }
	}else if($b=="Delete Equipment Selected"){
		if(!empty($checkList)) {
			foreach($checkList as $ch) {	
				$query="
					delete from
						po_equipment_detail
					where
						po_equipment_detail_id='$ch'
				";
				mysql_query($query) or die(mysql_error());
			}
		  }
	}else if($b=="New"){
		header("Location: admin.php?view=$view");
	}

	
	$query="
		select
			*
		from
			po_header
		where
			po_header_id='$po_header_id'
	";
	
	$result=mysql_query($query);
	$r = $aTrans = mysql_fetch_assoc($result);
	
	$project_id           = $r['project_id'];
	$project_name         = $options->attr_Project($project_id,'project_name');
	$project_code         = $options->attr_Project($project_id,'project_code');
	$project_name_code    = (!empty($project_id))?"$project_name - $project_code":"";
	$terms                = $r['terms'];
	
	$date                 = $r['date'];
	$po_header_id_pad     =	str_pad($po_header_id,7,"0",STR_PAD_LEFT);
	
	$pr_header_id         = $r['pr_header_id'];
	$pr_header_id_pad     =(!empty($pr_header_id))?str_pad($pr_header_id,7,"0",STR_PAD_LEFT):"";
	
	$supplier_id          = $r['supplier_id'];
	$supplier_name        = (!empty($supplier_id))?$options->getSupplierName($supplier_id):"";
	
	$status               = $r['status'];
	$approval_status      = $r['approval_status'];
	
	$scope_of_work        = $r['scope_of_work'];
	$work_category_id     = $r['work_category_id'];
	$sub_work_category_id = $r['sub_work_category_id'];
	
	$work_category        = $options->attr_workcategory($work_category_id,'work');
	$sub_work_category    = $options->attr_workcategory($sub_work_category_id,'work');
	
	$remarks              = $r['remarks'];
	
	$wtax                 = $r['wtax'];
	$vat                  = $r['vat'];
	$discount_amount      = $r['discount_amount'];
	
	$note                 = $r['note'];

?>


<form name="header_form" id="header_form" action="" method="post">
<div class=form_layout>
	<?php if(!empty($msg)) echo '<div id="status_update" class="ui-state-highlight ui-corner-all" style="padding: 0px 0.7em; text-align:left;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right:.3em;"></span> '.$msg.'</p></div>'; ?>
	<div class="module_title"><img src='images/user_orange.png'>PURCHASE ORDER</div>
    
    <div>
        <div class="module_actions">
			
			<?php if($old_id_po > 0){
				$po_header_id = $old_id_po;
			} ?>
            <input type="hidden" name="po_header_id" id="po_header_id" value="<?=$po_header_id?>" />
            <input type="hidden" name="view" value="<?=$view?>" />
            <div id="messageError">
                <ul>
                </ul>
            </div>
        
            <div class='inline'>
                Date : <br />
                <input type="text" name="date" id="date" class="textbox3 datepicker required" readonly="readonly" value="<?=$date;?>" title="Please Enter Date" />
            </div>    	
            
            <div class="inline">
                PR # : (Double click folder to select PR #) <br />
                <input type="text" class="textbox3" name="pr_header_id_display" id="pr_name" value="<?=$pr_header_id_pad?>" readonly="readonly"  />
                <input type="hidden" class="required" name="pr_header_id" id="pr_header_id" value="<?=$pr_header_id?> " title="Please Select PR#" />
                <img src="images/folder.png" id="folder" style="cursor:pointer;" />
            </div>           
            
            <div class="inline">
                Project / Location : <br />
                <input type="text" class="textbox" id="project_display" readonly="readonly" value="<?=$project_name_code?>" />
                <input type="hidden" name="project_id" id="project_id" value="<?=$project_id?>" />
            </div>
            
            <!--<div class="inline">
                Scope of Work : <br />
                <input type="text" class="textbox" id="scope_of_work"  name="scope_of_work" value="<?=$scope_of_work?>" readonly="readonly" />
            </div>-->
            
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
            <div class="inline" id="supplier_div">
                Supplier : <br />
                <input type="text" class="textbox" name="supplier_id_display" value="<?=$supplier_name?>" id="supplier_name" onclick="this.select();" />
                <input type="hidden" name="supplier_id" id="account_id" value="<?=$supplier_id?>" title="Please Select Supplier" required/>
            </div>
            
            <!--<div class="inline" style="">
            	MCD Warehouse  : <br /> 
                <?php
				$warehouse_selected = ($supplier_id=='0')?"checked='checked'":"";
                ?>
                <input type="checkbox" name="warehouse_cb" id="warehouse" value="1" <?=$warehouse_selected?> />
            </div> -->
            
            <div class="inline" id="div_terms">
                Terms ( Days ): <br />
                <input type="text" class="textbox" name="terms" id="term" value="<?=$terms?>" onmouseover="Tip('Term in Days. e.g. 90 for 90 days');" />
            </div>
            
            <div class="inline">
            	Witholding Tax (%) : <br />
				<select name="wtax" class="textbox5" style="font-family: calibri;">
					<?php if($wtax == ""){?>
							<option selected disabled value="">Choose</option>
							<option value="0" >NO WITHOLDING TAX</option>
							<option value="1" >EWT(1%) GOODS</option>
							<option value="2" >EWT(2%) SERVICES</option>					
					<?php }else if($wtax == 1){ ?>
							<option value="0" >NO WITHOLDING TAX</option>
							<option value="1" selected >EWT(1%) GOODS</option>
							<option value="2" >EWT(2%) SERVICES</option>
					<?php }else if($wtax == 2){ ?>
							<option value="0" >NO WITHOLDING TAX</option>
							<option value="1" >EWT(1%) GOODS</option>
							<option value="2" selected>EWT(2%) SERVICES</option>
					<?php }else if($wtax == 0){ ?>
							<option value="0" selected>NO WITHOLDING TAX</option>
							<option value="1" >EWT(1%) GOODS</option>
							<option value="2" >EWT(2%) SERVICES</option>
					<?php } ?>
				</select>
            </div>
            
            <div class="inline">
            	VAT (%) : <br />
				<select name="vat" class="textbox5" style="font-family: calibri;">
					<?php if($vat == ""){?>
							<option selected disabled value="">Choose</option>
							<option value="0" >NON-VAT</option>
							<option value="12" >VATABLE(12%)</option>							
					<?php }else if($vat == 0){ ?>
							<option value="0" selected>NON-VAT</option>
							<option value="12" >VATABLE(12%)</option>
					<?php }else if($vat == 12){ ?>
							<option value="0" >NON-VAT</option>
							<option value="12" selected>VATABLE(12%)</option>
					<?php } ?>
				</select>
            </div>			
            <!--<div class="inline">
            	Witholding Tax (%) : <br />
                <input type="text" class="textbox3"  name="wtax" value="<?=$wtax?>"  />
            </div>
            
            <div class="inline">
            	VAT (%) : <br />
                <input type="text" class="textbox3"  name="vat" value="<?=$vat?>"  />
            </div>-->
            
            <div class="inline">
            	Discount Amount : <br />
            	<input type="text" class="textbox" name="discount_amount" value="<?=$discount_amount?>" />
            </div>
            <br />

            <div class="inline">
            	# of days for delivery: <br>
            	<input type="text" class="textbox" name="no_of_days_delivery" value="<?=$aTrans['no_of_days_delivery']?>" required />
            </div>


            <br>
            <div style="display:inline-block;">
                Remarks : <br />       
                <div>
                <textarea class="textarea_small" name='remarks'><?=$remarks?></textarea>
            </div>                     
            
            <br />
			
			<?php if($_SESSION[userID] == '20160719-110150' || $_SESSION[userID] == '20200311-050946' || $_SESSION[userID] == '20170830-120801' || $_SESSION[userID] == '20200319-055723'){ ?>
		
			<div>
				PO Header ID : <br />
				<input type="text" class="textbox" name="old_id_po" value="<?=$old_id_po?>" />
			</div>
            <?php
			}
            if(!empty($status)){
            ?>
            <div class='inline'>
                <div>PO # : </div>        
                <div>
                    <input type="text" readonly="readonly" value="<?=$po_header_id_pad?>" class="textbox3" />
                </div>
            </div>  
            
            <div class='inline'>
                <div>Status : </div>        
                <div>
                    <input type="text" name="status" id="status" value="<?=$options->getTransactionStatusName($status)?>" readonly="readonly" class="textbox3"/>
                </div>
            </div> 
			<!--<div class='inline'>
                <div>Approval Status : </div>        
                <div>
                    <input type="text" name="status" id="status" value="<?=$options->getApprovalStatus($approval_status)?>" readonly="readonly" class="textbox3"/>
                </div>
            </div> -->
    
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
			
            }else if(empty($status)){
            ?>
            <input type="submit" name="b" id="b" value="Submit" />
            <?php
            }
            
            if($b!="Print Preview" && $b!="Print Preview No Price" && !empty($status)){
            ?>
                <input type="submit" name="b" id="b" value="Print Preview" />
                <input type="submit" name="b" id="b" value="Print Preview No Price" />
            <?php
            }
        
            if($b=="Print Preview" or $b=="Print Preview No Price"){
            ?>	
                <input type="button" value="Print" onclick="printIframe('JOframe');" />
        
            <?php
            }
            if($status!="C" && !empty($status)){
            ?>
			<input type="submit" name="b" id="b" value="Cancel" onclick="return confirm('Proceed to Cancel?');" />
            <?php if($status == "F"){ 
					
			?>       
			<input type="button" name="b" id="b" value="Edit Details" onclick="xajax_update_supplier('<?=$po_header_id?>','<?=$supplier_id?>',  '<?=$view?>',  '<?=$remarks?>');"  />
				<!-- <input type="submit" name="b" id="b" value="Unfinish" onclick="return confirm('Warning: Make sure the item was not received yet!');" /> -->
            <?php
				}
            }
            ?>   
             
        </div>
        <?php
        if($status=="S"){
        ?>
        	<?php
			if($supplier_id!=0){
            ?>
                <div id="accordion">
                    <?php
                    /*******************
                    MATERIALS	
                    *******************/
                    
                    $result=mysql_query("
                        select
                            d.stock_id,
                            stock,
                            sum(quantity) as quantity,
                            unit
                        from
                            pr_detail as d, productmaster as p
                        where
                            d.stock_id = p.stock_id
                        and
                            pr_header_id = '$pr_header_id'
                        and
                            allowed = '1'
                        group by
                            stock_id
                    ") or die(mysql_error());
                    $rows = mysql_num_rows($result);
                    ?>
                    <?php
                    if($rows > 0):
                    ?>
                    <h3><a href="#">MATERIAL SEARCH RESULTS  </a></h3>
                    <div>
                    <table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table" id="search_table">
                        <tr bgcolor="#C0C0C0">				
                            <th width="20"><b>#</b></th>
                            <th width="30"></th>
                            <th>Item</th>
                            <th width="60">Unit</th>
                            <th width="100">PR Quantity</th>
                            <th width="100">PO Quantity</th>
                            <th width="100">Balance</th>
                        </tr> 
                        <?php
                        $i=1;
                        while($r=mysql_fetch_assoc($result)){
                            $stock_id 		= $r['stock_id'];
                            $stock			= $r['stock'];
                            $pr_quantity	= $r['quantity'];
                            $unit			= $r['unit'];
                            
                            $total_stocks_po = $options->po_totalStocksPO($pr_header_id,$stock_id);
                            
                            $balance = $pr_quantity - $total_stocks_po;
                        ?>
                        <tr>
                            <td><?=$i++?></td>
                            <td><input type="button" value="PO" onclick="xajax_po_stock_id_form('<?=$stock_id?>');" /></td>
                            <td><?=$stock?></td>
                            <td><?=$unit?></td>
                            <td class="align-right"><?=number_format($pr_quantity,4,'.',',')?></td>
                            <td class="align-right"><?=number_format($total_stocks_po,4,'.',',')?></td>
                            <td class="align-right"><?=number_format($balance,4,'.',',')?></td>
                        </tr>
                        <?php
                        }
                        ?>
                    </table>
                    </div>
                    <?php
                    endif;
                    ?>
                   
                    
                    <?php
                    /*******************
                    EQUIPMENTS
                    *******************/
                    $result=mysql_query("
                        select
                            stock,
                            d.stock_id,
                            quantity,
                            days,
                            rate_per_day,
                            amount
                        from
                            pr_equipment_detail as d,
                            pr_header as h,
                            productmaster as pm
                        where
                            h.pr_header_id = d.pr_header_id
                        and
                            pm.stock_id = d.stock_id
                        and
                            h.pr_header_id = '$pr_header_id'
                        and
                            h.project_id = '$project_id'
                        and
                            h.scope_of_work = '$scope_of_work'
                        and
                            work_category_id = '$work_category_id'
                        and
                            sub_work_category_id = '$sub_work_category_id'
                        and
                            allowed = '1'
                        
                    ") or die(mysql_error());
                    $rows = mysql_num_rows($result);
                    ?>
                    <?php
                    if($rows > 0):
                    ?>
                    <h3><a href="#">EQUIPMENT RENTAL SEARCH RESULTS </a></h3>
                    <div>
                    <table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table" id="search_table">
                        <tr bgcolor="#C0C0C0">				
                            <th width="20">#</th>
                            <td width="20" align="center"></td>
                            <th>Description</th>
                            <th width="60">No</th>
                            <th width="60">No. of Days</th>
                            <th width="100">Rental/Day</th>
                            <th width="100">Amount</th>
                            <th width="100">PR Amount</th>
                            <th width="100">PO Amount</th>
                            <th width="100">Balance</th>
                        </tr> 
                        <?php
                        $i=1;
                        while($r=mysql_fetch_assoc($result)){
                            $stock_id					= $r['stock_id'];
                            $stock						= $r['stock'];
                            $quantity					= $r['quantity'];
                            $days						= $r['days'];
                            $rate_per_day				= $r['rate_per_day'];
                            $unit						= $r['unit'];
                            $amount						= $r['amount'];
                            
                            $pr_amount	= $options->equipment_pr($pr_header_id,$stock_id);
                            $po_amount = $options->equipment_po_pr($pr_header_id,$stock_id);	
                    
                            $balance = $pr_amount - $po_amount;
                            
                        ?>
                        <tr>
                            <td><?=$i++?></td>
                            <td><input type="button" value="PO" onclick="xajax_equipment_po_stock_id_form('<?=$stock_id?>','<?=$quantity?>','<?=$days?>','<?=$rate_per_day?>','<?=$amount?>');" /></td>
                            <td><?=$stock?></td>
                            <td class="align-right"><?=$quantity?></td>
                            <td class="align-right"><?=$days?></td>
                            <td class="align-right"><?=$rate_per_day?></td>
                            <td class="align-right"><?=number_format($amount,4,'.',',')?></td>
                            <td class="align-right"><?=number_format($pr_amount,4,'.',',')?></td>
                            <td class="align-right"><?=number_format($po_amount,4,'.',',')?></td>
                            <td class="align-right"><?=number_format($balance,4,'.',',')?></td>
                            <input type='hidden' name='budget_equipment_detail_id[]' value='<?=$budget_equipment_detail_id?>' />
                        </tr>
                        <?php
                        }
                        ?>
                    </table>
                    </div>
                    <?php
                    endif;
                    ?>
                    
                    <?php
                    /*******************
                    FUEL
                    *******************/
                    $result=mysql_query("
                        select
                            *
                        from
                            pr_header as h, pr_fuel_detail as d
                        where
                            h.pr_header_id = d.pr_header_id
                        and
                            h.pr_header_id	= '$pr_header_id' 
                        and
                            h.pr_header_id = '$pr_header_id'
                        and
                            h.project_id = '$project_id'
                        and
                            h.scope_of_work = '$scope_of_work'
                        and
                            work_category_id = '$work_category_id'
                        and
                            sub_work_category_id = '$sub_work_category_id'
                        and
                            allowed = '1'
                        
                    ") or die(mysql_error());
                    $rows = mysql_num_rows($result);
                    ?>
                    <?php
                    if($rows > 0):
                    ?>
                    <h3><a href="#">FUEL, OIL, AND LUBRICANTS SEARCH RESULTS </a></h3>
                    <div>
                        <table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table" id="search_table">
                            <tr bgcolor="#C0C0C0">				
                                <th width="20">#</th>
                                <td width="20" align="center"></td>
                                <th>Fuel</th>
                                <th>Equipment</th>
                                <th width="60">Consumption / Day</th>
                                <th width="60">No. of Days</th>
                                <th width="60">Quantity</th>
                                
                                <th width="60">Fuel Request</th>
                                <th width="60">Warehouse Request</th>
                                <th width="60">Total Fuel Quantity</th>
                                
                                <th width="60">Fuel Cost/Litter</th>
                                <th width="100">Amount</th>
                                <th width="100">PR Amount</th>
                                <th width="100">PO Amount</th>
                                <th width="100">Balance</th>
                            </tr> 
                            <?php
                            $i=1;
                            while($r=mysql_fetch_assoc($result)){
                                $pr_fuel_detail_id	= $r['pr_fuel_detail_id'];
                                $fuel_id					= $r['fuel_id'];
                                $equipment_id				= $r['equipment_id'];
                                $consumption_per_day		= $r['consumption_per_day'];
                                $request_quantity			= $r['request_quantity'];
                                $warehouse_quantity			= $r['warehouse_quantity'];
                                $total_quantity			 	= $r['quantity'];
                                
                                $days						= $r['days'];
                                $cost_per_litter			= $r['cost_per_litter'];
                                $amount						= $r['amount'];
                                
                                $pr_amount	= $options->fuel_pr($pr_header_id,$fuel_id,$equipment_id);
                                $po_amount = $options->fuel_po_pr($pr_header_id,$fuel_id,$equipment_id);	
                                
                                $fuel		= $options->attr_stock($fuel_id,'stock');
                                $equipment	= $options->attr_stock($equipment_id,'stock');
                        
                                $balance = $pr_amount - $po_amount;
                                
                            ?>
                            <tr>
                                <td><?=$i++?></td>
                                <td><input type="button" value="PO" onclick="xajax_fuel_po_stock_id_form('<?=$pr_fuel_detail_id?>');" /></td>
                                <td><?=$fuel?></td>
                                <td><?=$equipment?></td>
                                <td><?=$consumption_per_day?></td>
                                <td><?=$days?></td>
                                
                                <td><?=$request_quantity?></td>
                                <td><?=$request_quantity * $consumption_per_day * $days?></td>
                                <td><?=$warehouse_quantity?></td>
                                <td><?=$total_quantity?></td>
                                
                                <td><?=$cost_per_litter?></td>
                                <td class="align-right highlight"><?=number_format($amount,4,'.',',')?></td>
                                <td class="align-right"><?=number_format($pr_amount,4,'.',',')?></td>
                                <td class="align-right"><?=number_format($po_amount,4,'.',',')?></td>
                                <td class="align-right"><?=number_format($balance,4,'.',',')?></td>
                                <input type='hidden' name='budget_equipment_detail_id[]' value='<?=$budget_equipment_detail_id?>' />
                            </tr>
                            <?php
                            }
                            ?>
                        </table>
                    </div>
                    <?php
                    endif;
                    ?>
                </div>
          	<?php
			}else{
            ?>
            <div id="accordion">
            	<?php
                    /*******************
                    MATERIALS	
                    *******************/
                    
                    $result=mysql_query("
                        select
                            d.stock_id,
                            stock,		
							quantity,
                            unit
                        from
                            pr_detail as d, productmaster as p
                        where
                            d.stock_id = p.stock_id
                        and
                            pr_header_id = '$pr_header_id'
                        and
                            allowed = '1'
                    ") or die(mysql_error());
                    $rows = mysql_num_rows($result);
                    ?>
                    <?php
                    if($rows > 0):
                    ?>
                    <h3><a href="#">MATERIAL SEARCH RESULTS  </a></h3>
                    <div>
                    <table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table" id="search_table">
                        <tr bgcolor="#C0C0C0">				
                            <th width="20"><b>#</b></th>
                            <th width="30"></th>
                            <th>Item</th>
                            <th width="60">Unit</th>
                            <th width="100">PR Quantity</th>
                            <th width="100">PO Quantity</th>
                            <th width="100">Balance</th>
                        </tr> 
                        <?php
                        $i=1;
                        while($r=mysql_fetch_assoc($result)){
                            $stock_id 		= $r['stock_id'];
                            $stock			= $r['stock'];
                            $pr_quantity	= $r['quantity'];
                            $unit			= $r['unit'];
                            
                            $total_stocks_po = $options->po_warehouse_totalStocksPO($pr_header_id,$stock_id);
                            
                            $balance = $pr_quantity - $total_stocks_po;
                        ?>
                        <tr>
                            <td><?=$i++?></td>
                            <td><input type="button" value="PO" onclick="xajax_po_warehouse_stock_id_form('<?=$stock_id?>');" /></td>
                            <td><?=$stock?></td>
                            <td><?=$unit?></td>
                            <td class="align-right"><?=number_format($pr_quantity,4,'.',',')?></td>
                            <td class="align-right"><?=number_format($total_stocks_po,4,'.',',')?></td>
                            <td class="align-right"><?=number_format($balance,4,'.',',')?></td>
                        </tr>
                        <?php
                        }
                        ?>
                    </table>
                    </div>
                    <?php
                    endif;
                    ?>
                    
                <?php
                    /*******************
                    FUEL
                    *******************/
                    $result=mysql_query("
                        select
                            *
                        from
                            pr_header as h, pr_fuel_detail as d
                        where
                            h.pr_header_id = d.pr_header_id
                        and
                            h.pr_header_id	= '$pr_header_id' 
                        and
                            h.project_id = '$project_id'
                        and
                            h.scope_of_work = '$scope_of_work'
                        and
                            work_category_id = '$work_category_id'
                        and
                            sub_work_category_id = '$sub_work_category_id'
                        and
                            allowed = '1'
                    ") or die(mysql_error());
                    $rows = mysql_num_rows($result);
                    ?>
                    <?php
                    if($rows > 0):
                    ?>
                    <h3><a href="#">FUEL, OIL, AND LUBRICANTS SEARCH RESULTS </a></h3>
                    <div>
                        <table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table" id="search_table">
                            <tr bgcolor="#C0C0C0">				
                                <th width="20">#</th>
                                <td width="20" align="center"></td>
                                <th>Fuel</th>
                                <th>Equipment</th>
                                <th width="60">Fuel Warehouse Request</th>
                                <th width="100">Cost</th>
                                <th width="100">Amount</th>
                                <th width="100">PR Amount</th>
                                <th width="100">PO Amount</th>
                                <th width="100">Balance</th>
                            </tr> 
                            <?php
                            $i=1;
                            while($r=mysql_fetch_assoc($result)){
                                $pr_fuel_detail_id	= $r['pr_fuel_detail_id'];
                                $fuel_id					= $r['fuel_id'];
                                $equipment_id				= $r['equipment_id'];
                                $warehouse_quantity			= $r['warehouse_quantity'];
                                $cost_per_litter			= $r['cost_per_litter'];
								
                                $amount						= $warehouse_quantity * $cost_per_litter;
                                
                                $pr_amount	= $options->fuel_warehouse_pr($pr_header_id,$fuel_id,$equipment_id);
                                $po_amount = $options->fuel_po_pr($pr_header_id,$fuel_id,$equipment_id,1);	
                                
                                $fuel		= $options->attr_stock($fuel_id,'stock');
                                $equipment	= $options->attr_stock($equipment_id,'stock');
                        
                                $balance = $pr_amount - $po_amount;
                                
                            ?>
                            <tr>
                                <td><?=$i++?></td>
                                <td><input type="button" value="PO" onclick="xajax_fuel_po_warehouse_stock_id_form('<?=$pr_fuel_detail_id?>');" /></td>
                                <td><?=$fuel?></td>
                                <td><?=$equipment?></td>
                                <td><?=$warehouse_quantity?></td>
                                <td><?=$cost_per_litter?></td>
                                <td class="align-right highlight"><?=number_format($amount,4,'.',',')?></td>
                                <td class="align-right"><?=number_format($pr_amount,4,'.',',')?></td>
                                <td class="align-right"><?=number_format($po_amount,4,'.',',')?></td>
                                <td class="align-right"><?=number_format($balance,4,'.',',')?></td>
                                <input type='hidden' name='budget_equipment_detail_id[]' value='<?=$budget_equipment_detail_id?>' />
                            </tr>
                            <?php
                            }
                            ?>
                        </table>
                    </div>
                    <?php
                    endif;
                    ?>
          	</div>
            <?php
			}
            ?>
        <?php
        }
        ?> 
   	</div>
    
    <?php
	if($supplier_id!=0){
    ?>
    <div>
    	<?php
		/******************************
		MATERIALS
		*******************************/
		
		$query = "
			select
				po_detail_id,
				d.details,
				d.stock_id,
				stock,
				d.quantity,
				d.cost,
				chargables,
				person,
				d.discount,
				d.amount,
				pm.categ_id1
			from
				po_detail as d,
				productmaster as pm
			where
				d.po_header_id = '$po_header_id' 
			and
				pm.stock_id = d.stock_id
		";
		$result=mysql_query($query) or die(mysql_error());
		?>
        <?php
		$rows = mysql_num_rows($result);
		if($rows > 0):
        ?>
        <div style="background-color::#FFF; font-weight:bolder; margin-top:10px;">PURCHASE ORDER MATERIAL DETAILS </div>
        <table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table" id="search_table" style="border:1px solid #000;">
            <tr>
                <th width="20">#</th>
                <td width="20" align="center"></td>
                <th>Item</th>
                <th>Details</th>
                <th>Chargables</th>
                <th>Person</th>
                <th width="100">Quantity</th>
                <th width="100">Cost</th>
                <th width="100">Discount</th>
                <th width="100">Amount</th>
                <th width="40">Edit</th>
            </tr>
            <?php
            $i=1;
            while($r=mysql_fetch_assoc($result)){
                $po_detail_id		= $r['po_detail_id'];
				$details			= $r['details'];
                $stock_id			= $r['stock_id'];
                $stock				= $r['stock'];
                $quantity			= $r['quantity'];
                $cost				= $r['cost'];
                $category				= $r['categ_id1'];
                
                $amount				= $r['amount'];
            ?>
                <tr>
                    <td><?=$i++?></td>
                    <td>

                    <a href="admin.php?view=<?=$view?>&po_header_id=<?=$po_header_id?>&id=<?=$po_detail_id?>&b=M" onclick="return approve_confirm();"  ><img src="images/trash.gif" />Del</a></td>
                    <td><?=$stock?></td>
                    <td><?=$details?></td>
                    <td><?=$r['chargables']?></td>
                    <td><?=$r['person']?></td>
                    <td><input type='text' class='textbox3 align-right' name='detail_quantity[]' value='<?=$quantity?>'  /></td>
                    <td class="align-right"><?=number_format($cost,4,'.',',')?></td>
                   
                    <td class="align-right"><?=number_format($r['discount']*$quantity,4,'.',',')?></td>
                    <td class="align-right"><?=number_format($amount,4,'.',',')?></td>
                    <td class="align-right"><input type="button" value="Edit" onclick="xajax_admin_override('<?=$po_detail_id?>', '<?=$po_header_id?>', '<?=str_replace('"', '', $stock)?>', '<?=$stock_id?>', '<?=$quantity?>', '<?=$cost?>', '<?=$view?>', xajax.getFormValues('header_form'), '<?=$details?>', '<?=$category?>');"  /></td>
                    <input type='hidden' name='po_detail_id[]' value='<?=$po_detail_id?>' />
                </tr>
            <?php
            }
            ?>
        </table>
        <?php
		endif;
        ?>
       
        <?php
		/******************************
		EQUIPMENTS
		*******************************/
		
		$query = "
			select
				*
			from
				po_equipment_detail as d,
				productmaster as pm
			where
				d.po_header_id	= '$po_header_id' 
			and
				pm.stock_id = d.stock_id
		";
		$result=mysql_query($query) or die(mysql_error());
		?>
        <?php
		$rows = mysql_num_rows($result);
		if($rows > 0):
        ?> 
        <div style="background-color::#FFF; font-weight:bolder; margin-top:10px;">PURCHASE ORDER EQUIPMENT RENTAL DETAILS  </div>
        <table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table" id="search_table" style="border:1px solid #000;">
            <tr>
                <th width="20">#</th>
                <td width="20" align="center"></td>
                <th>Description</th>
                <th width="60">No</th>
                <th width="60">No. of Days</th>
                <th width="100">Rental/Day</th>
                <th width="100">Amount</th>
            </tr>
            <?php
            $i=1;
            while($r=mysql_fetch_assoc($result)){
                $po_equipment_detail_id		= $r['po_equipment_detail_id'];
                $stock_id			= $r['stock_id'];
                $stock				= $r['stock'];
                $quantity			= $r['quantity'];
                $days				= $r['days'];
                $rate_per_day		= $r['rate_per_day'];
                $unit				= $r['unit'];
                $amount				= $r['amount'];
            ?>
                <tr>
                    <td><?=$i++?></td>
                    <td><a href="admin.php?view=<?=$view?>&po_header_id=<?=$po_header_id?>&id=<?=$po_equipment_detail_id?>&b=E" ><img src="images/trash.gif" onclick="return approve_confirm();" /></a></td>
                    <td><?=$stock?></td>
                    <td><input type='text' class='textbox3 align-right' name='update_equipment_quantity[]' value='<?=$quantity?>'  /></td>
                    <td><input type='text' class='textbox3 align-right' name='update_equipment_days[]' value='<?=$days?>'  /></td>
                    <td><input type='text' class='textbox3 align-right' name='update_equipment_rate_per_day[]' value='<?=$rate_per_day?>'  /></td>
                    <td class="align-right"><?=number_format($amount,4,'.',',')?></td>
                    <input type='hidden' name='po_equipment_detail_id[]' value='<?=$po_equipment_detail_id?>' />
                </tr>
            <?php
            }
            ?>
        </table>
        <?php
		endif;
        ?>
        
        <?php
		/******************************
		FUELS
		*******************************/
		
		$query = "
			select
				*
			from
				po_fuel_detail
			where
				po_header_id	= '$po_header_id' 
			
		";
		$result=mysql_query($query) or die(mysql_error());
		?>
        <?php
		$rows = mysql_num_rows($result);
		if($rows > 0):
        ?> 
        <div style="background-color::#FFF; font-weight:bolder; margin-top:10px;">PURCHASE ORDER FUEL, OIL, AND LUBRICANTS DETAILS : </div>
        <table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table" id="search_table" style="border:1px solid #000;">
            <tr>
                <th width="20">#</th>
                <th width="20" align="center"></th>
                <th>Fuel</th>
                <th>Equipment</th>
                <th width="60">Consumption / Day</th>
                <th width="60">No. of Days</th>
                <th width="60">Quantity</th>
                
                <th width="60">Total Fuel Quantity</th>
                
                <th width="60">Fuel Cost/Litter</th>
                <th width="100">Amount</th>
            </tr>
            <?php
            $i=1;
            while($r=mysql_fetch_assoc($result)){
				$po_fuel_detail_id			= $r['po_fuel_detail_id'];
                $fuel_id					= $r['fuel_id'];
                $equipment_id				= $r['equipment_id'];
                $consumption_per_day		= $r['consumption_per_day'];
                $request_quantity			= $r['request_quantity'];
                $warehouse_quantity			= $r['warehouse_quantity'];
				
				$days						= $r['days'];
                $total_quantity			 	= ($request_quantity * $consumption_per_day * $days ) ;
                
                $cost_per_litter			= $r['cost_per_litter'];
                $amount						= $r['amount'];
				
				$fuel		= $options->attr_stock($fuel_id,'stock');
                $equipment	= $options->attr_stock($equipment_id,'stock');
            ?>
                <tr>
                    <td><?=$i++?></td>
                    <td><a href="admin.php?view=<?=$view?>&po_header_id=<?=$po_header_id?>&id=<?=$po_fuel_detail_id?>&b=F" ><img src="images/trash.gif" onclick="return approve_confirm();" /></a></td>
                    <td><?=$fuel?></td>
                    <td><?=$equipment?></td>
                    <td><input type='text' class='textbox-int align-right' value='<?=$consumption_per_day?>'  /></td>
                    <td><input type='text' class='textbox-int align-right' value='<?=$days?>'  /></td>
                    
                    <td><input type='text' class='textbox-int align-right' value='<?=$request_quantity?>'  /></td>
                    <td><input type='text' class='textbox-int align-right' value='<?=$total_quantity?>'  /></td>
                    
                    <td><input type='text' class='textbox-int align-right' value='<?=$cost_per_litter?>'  /></td>
                    <td class="align-right "><?=number_format($amount,4,'.',',')?></td>
                </tr>
            <?php
            }
            ?>
        </table>
        <?php
		endif;
        ?>
    </div>
    <?php
	}else{
    ?>
    <div>
	<?php
    /******************************
    MATERIALS
    *******************************/
    
    $query = "
        select
            po_detail_id,
            d.details,
            d.stock_id,
            stock,
            d.quantity,
            d.cost
        from
            po_detail as d,
            productmaster as pm
        where
            d.po_header_id = '$po_header_id' 
        and
            pm.stock_id = d.stock_id
    ";
    $result=mysql_query($query) or die(mysql_error());
    ?>
    <?php
    $rows = mysql_num_rows($result);
    if($rows > 0):
    ?>
    <div style="background-color::#FFF; font-weight:bolder; margin-top:10px;">PURCHASE ORDER MATERIAL DETAILS </div>
    <table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table" id="search_table" style="border:1px solid #000;">
        <tr>
            <th width="20">#</th>
            <td width="20" align="center"></td>
            <th>Item</th>
            <th>Details</th>
            <th width="100">Quantity</th>
            <th width="100">Cost</th>
            <th width="100">Amount</th>
        </tr>
        <?php
        $i=1;
        while($r=mysql_fetch_assoc($result)){
            $po_detail_id		= $r['po_detail_id'];
            $details			= $r['details'];
            $stock_id			= $r['stock_id'];
            $stock				= $r['stock'];
            $quantity			= $r['quantity'];
            $cost				= $r['cost'];
            
            $amount				= $cost * $quantity;
        ?>
            <tr>
                <td><?=$i++?></td>

                <td><a href="admin.php?view=<?=$view?>&po_header_id=<?=$po_header_id?>&id=<?=$po_detail_id?>&b=M" ><img src="images/trash.gif" onclick="return approve_confirm();" /></a></td>
                <td><?=$stock?></td>
                <td><?=$details?></td>
                <td><input type='text' class='textbox3 align-right' name='detail_quantity[]' value='<?=$quantity?>'  /></td>
                <td class="align-right"><?=number_format($cost,4,'.',',')?></td>
                <td class="align-right"><?=number_format($amount,4,'.',',')?></td>
                <input type='hidden' name='po_detail_id[]' value='<?=$po_detail_id?>' />
            </tr>
        <?php
        }
        ?>
    </table>
    <?php
    endif;
    ?>
     <?php
		/******************************
		FUELS
		*******************************/
		
		$query = "
			select
				*
			from
				po_fuel_detail
			where
				po_header_id	= '$po_header_id' 
			
		";
		$result=mysql_query($query) or die(mysql_error());
		?>
        <?php
		$rows = mysql_num_rows($result);
		if($rows > 0):
        ?> 
        <div style="background-color::#FFF; font-weight:bolder; margin-top:10px;">PURCHASE ORDER FUEL, OIL, AND LUBRICANTS DETAILS : </div>
        <table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table" id="search_table" style="border:1px solid #000;">
            <tr>
                <th width="20">#</th>
                <th width="20" align="center"></th>
                <th>Fuel</th>
                <th>Equipment</th>
                <th width="60">Consumption / Day</th>
                <th width="60">No. of Days</th>
                <th width="60">Quantity</th>
                <th width="60">Total Fuel Quantity</th>
                <th width="60">Fuel Cost/Litter</th>
                <th width="100">Amount</th>
            </tr>
            <?php
            $i=1;
            while($r=mysql_fetch_assoc($result)) {
				$po_fuel_detail_id			= $r['po_fuel_detail_id'];
                $fuel_id					= $r['fuel_id'];
                $equipment_id				= $r['equipment_id'];
                $consumption_per_day		= $r['consumption_per_day'];
                $request_quantity			= $r['request_quantity'];
                $warehouse_quantity			= $r['warehouse_quantity'];
				
				$days						= $r['days'];
                $total_fuel_quantity		= $consumption_per_day * $days * $request_quantity;
                
                $cost_per_litter			= $r['cost_per_litter'];
                $amount						= $r['amount'];
				
				$fuel		= $options->attr_stock($fuel_id,'stock');
                $equipment	= $options->attr_stock($equipment_id,'stock');
            ?>
                <tr>
                    <td><?=$i++?></td>
                    <td><a href="admin.php?view=<?=$view?>&po_header_id=<?=$po_header_id?>&id=<?=$po_fuel_detail_id?>&b=F" ><img src="images/trash.gif" onclick="return approve_confirm();" /></a></td>
                    <td><?=$fuel?></td>
                    <td><?=$equipment?></td>
                    <td><input type='text' class='textbox-int align-right' value='<?=$consumption_per_day?>'  /></td>
                    <td><input type='text' class='textbox-int align-right' value='<?=$days?>'  /></td>
                    
                    <td><input type='text' class='textbox-int align-right' value='<?=$request_quantity?>'  /></td>
                    <td><input type='text' class='textbox-int align-right' value='<?=$total_fuel_quantity?>'  /></td>
                    
                    
                    <td><input type='text' class='textbox-int align-right' value='<?=$cost_per_litter?>'  /></td>
                    <td class="align-right "><?=number_format($amount,4,'.',',')?></td>
                </tr>
            <?php
            }
            ?>
        </table>
        <?php
		endif;
        ?>
    
    </div>
    
    <?php
	}
    ?>
    <div style="clear:both;">
		<?php
        if($b == "Print Preview" && $po_header_id){
        ?>
            <iframe id='JOframe' name='JOframe' frameborder='0' src='print_report_po5.php?id=<?=$po_header_id?>' width='100%' height='500'></iframe>
        <?php
        }else if($b == "Print Preview No Price" && $po_header_id){
        ?>
			<iframe id='JOframe' name='JOframe' frameborder='0' src='print_report_po6.php?id=<?=$po_header_id?>' width='100%' height='500'></iframe>
		<?php
		}
		?>
   	</div>
  
</div>
</form>

<script type="text/javascript" src="scripts/script_po.js">	
</script>
<script type="text/javascript">
j(function(){
	
	<?php if($status == "F"){ ?>
	jQuery('img').each(function(index,e){
		var bg = jQuery(e).attr('src');
		if(bg =="images/trash.gif" ){
			this.remove();
		}
		
	});
	<?php } ?>

	j("#accordion").accordion();
	
	j("#cost,#quantity").keyup(function(){
		var price = document.getElementById("cost").value;
		var quantity = document.getElementById("quantity").value;
		
		var amount = price * quantity;
		var amountFormatted = Number(amount);
		document.getElementById("amount").value=amountFormatted.toFixed(2);
	});
	
	j("#folder").dblclick(function(){
		xajax_show_purchase_request();
	});
	
	j("#warehouse").change(function(){
		if(j(this).is(":checked")){
			j("#supplier_div").hide();		
		}else{
			j("#supplier_div").show();		
		}
	});
	
	if(j("#warehouse").is(":checked")){
		j("#supplier_div").hide();		
	}else{
		j("#supplier_div").show();		
	}


});

</script>
	
