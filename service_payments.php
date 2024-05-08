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
	$h							= $_REQUEST['h'];
	$service_payment_detail_id  = $_REQUEST['service_payment_detail_id'];
	$service_pay_check_id		= $_REQUEST['service_pay_check_id'];
	$service_pay_cash_id		= $_REQUEST['service_pay_cash_id'];
	$user_id					= $_SESSION['userID'];
	
	$service_pay_header_id		= $_REQUEST['service_pay_header_id'];
	$date						= $_REQUEST['date'];
	$supplier_id				= $_REQUEST['supplier_id'];
	
	$bank			= $_REQUEST['bank'];
	$checkno		= $_REQUEST['checkno'];
	$datecheck		= $_REQUEST['datecheck'];
	$checkamount	= $_REQUEST['checkamount'];
	$checkstatus	= $_REQUEST['checkstatus'];

	$cashamount		= $_REQUEST['cashamount'];
	
	$checkList	= $_REQUEST['checkList'];
	
	if($b=="Submit"){
		$query="
			insert into 
				service_pay_header
			set
				date		= '$date',
				supplier_id	= '$supplier_id',
				status		= 'S',
				user_id		= '$user_id'
		";	
		
		mysql_query($query) or die(mysql_error());
		
		$service_pay_header_id = mysql_insert_id();
		$options->insertAudit($service_pay_header_id,'service_pay_header_id','I');
		
		$msg="Transaction Saved";
		
	}else if($b=="Update"){
		$query="
			update
				service_pay_header
			set
				date		= '$date',
				supplier_id	= '$supplier_id',
				status		= 'S',
				user_id		= '$user_id'
			where
				service_pay_header_id='$service_pay_header_id'
		";	
		
		mysql_query($query) or die(mysql_error());
		$options->insertAudit($service_pay_header_id,'service_pay_header_id','U');		
		
		$msg = "Transaction Updated";
		
	}else if($b=="Cancel"){
		$query="
			update
				service_pay_header
			set
				status='C'
			where
				service_pay_header_id='$service_pay_header_id'
		";	
		mysql_query($query) or die(mysql_error());
		$options->insertAudit($service_pay_header_id,'service_pay_header_id','C');
		
		$msg = "Transaction Cancelled";
		
	}else if($b=="Finish"){
		$query="
			update
				service_pay_header
			set
				status='F'
			where
				service_pay_header_id='$service_pay_header_id'
		";	
		mysql_query($query) or die(mysql_error());
		$options->insertAudit($service_pay_header_id,'service_pay_header_id','F');
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
	}else if($b=="New"){
		header("Location: admin.php?view=$view");
	}else if($b=="Pay"){
		if(!empty($h)) {
			foreach($h as $ch) {	
			
				$result=mysql_query("
					select 
						sum(amount) as amount
					from
						service_rr_header as h, service_rr_detail as d
					where
						h.service_rr_header_id = d.service_rr_header_id 
					and
						h.service_rr_header_id = '$ch'
				") or die(mysql_error());
			
				$r=mysql_fetch_assoc($result);
				$amount = $r['amount'];
				mysql_query("
					insert into
						service_payment_detail
					set
						service_pay_header_id 	= '$service_pay_header_id',
						service_rr_header_id	= '$ch',
						amount					= '$amount'
				") or die(mysql_error());
			}
			$msg="Added Service Details";
	 	}else{
			$msg="No Service Details Added";	
		}
		
	}else if($b=="Delete Service Details"){
		if(!empty($service_payment_detail_id)) {
			foreach($service_payment_detail_id as $ch) {	
			
				mysql_query("
					delete from
						service_payment_detail
					where
						service_payment_detail_id = '$ch'
				") or die(mysql_error());
			}
			$msg="Deleted Service Details";
	 	}else{
			$msg="No Service Details Deleted";	
		}
		
	}else if($b=="Add Check"){
		mysql_query("
			insert into	
				service_pay_check
			set
				service_pay_header_id 	= '$service_pay_header_id',
				bank					= '$bank',
				checkno					= '$checkno',
				datecheck				= '$datecheck',
				checkamount				= '$checkamount',
				checkstatus				= '$checkstatus'
		") or die(mysql_error());
	}else if($b=="Add Cash"){
		mysql_query("
			insert into
				service_pay_cash
			set
				service_pay_header_id 	= '$service_pay_header_id',
				amount					= '$cashamount'
		") or die(mysql_error());
		
	}else if($b=="Delete Details"){
		
		if(!empty($service_pay_check_id)) {
			foreach($service_pay_check_id as $ch) {	
			
				mysql_query("
					delete from
						service_pay_check
					where
						service_pay_check_id = '$ch'
				") or die(mysql_error());
			}
		
	 	}
		
		if(!empty($service_pay_cash_id)) {
			foreach($service_pay_cash_id as $ch) {	
			
				mysql_query("
					delete from
						service_pay_cash
					where
						service_pay_cash_id = '$ch'
				") or die(mysql_error());
			}			
	 	}
			
		$msg = "CASH / CHECK PAYMENTS DELETED";
	}
		

	$query="
		select
			*
		from
			service_pay_header 
		where
			service_pay_header_id ='$service_pay_header_id'
	";
	
	$result=mysql_query($query);
	$r=mysql_fetch_assoc($result);
	
	
	$service_pay_header_id		= $r['service_pay_header_id'];
	$service_pay_header_id_pad	= (!empty($service_pay_header_id))?str_pad($service_pay_header_id,7,0,STR_PAD_LEFT):"";

	$date				= $r['date'];

	$supplier_id		= $r['supplier_id'];
	$supplier			= $options->attr_Supplier($supplier_id,'account');
	
	$user_id			= $r['user_id'];
	$status				= $r['status'];	

?>
<div class=form_layout>
	<?php if(!empty($msg)) echo '<div id="status_update" class="ui-state-highlight ui-corner-all" style="padding: 0px 0.7em; text-align:left;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right:.3em;"></span> '.$msg.'</p></div>'; ?>
	<div class="module_title"><img src='images/user_orange.png'>SERVICE PAYMENTS</div>
    <form name="header_form" id="header_form" action="" method="post">
    <div class="module_actions">
        <input type="hidden" name="service_pay_header_id" id="service_pay_header_id" value="<?=$service_pay_header_id?>" />
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
        
        <div class="inline">
        	Supplier : <br />
            <input type="text" class="textbox" id="supplier_name" value="<?=$supplier?>" />
            <input type="hidden" name="supplier_id" id="account_id" value="<?=$supplier_id?>" />
        </div>
        
        <?php
        if(!empty($status)){
        ?>
        <br />
        <div class="inline">
        	Service Payment # : <br />
            <input type="text" class="textbox3" name="status" id="status" value="<?=$service_pay_header_id_pad?>" readonly="readonly"/>
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
     	<div class="module_title"><img src='images/money.png'>CHECK DETAILS : </div>
        <div style="display:inline-block; margin-right:20px;">
        	<div>Bank : </div>        
            <div><input type="text" name="bank" id="bank" class="textbox" onclick="this.select();" /></div>
        </div>  
        
        <div style="display:inline-block; margin-right:20px;">
        	<div>Check # : </div>        
            <div><input type="text" name="checkno" id="checkno" class="textbox" onclick="this.select();" /></div>
        </div>  
        
        <div style="display:inline-block; margin-right:20px;">
        	<div>Check Date: </div>        
            <div>
            	<input type="text" name="datecheck" id="datecheck" class="textbox3 datepicker" readonly="readonly"/>
         	</div>
        </div>    	
        
        <div style="display:inline-block; margin-right:20px;">
        	<div>Check Amount : </div>        
            <div><input type="text" name="checkamount" id="checkamount" class="textbox3" onclick="this.select();" /></div>
        </div> 
        
        <div style="display:inline-block; margin-right:20px;">
        	<div>Check Status : </div>        
            <div><?=$options->getCheckStatusOptions()?></div>
        </div> 
        <input type="submit" name="b" value="Add Check"  />
    </div>
    
    <div class="module_actions">
     	<div class="module_title"><img src='images/money.png'>CASH DETAILS : </div>
        <div style="display:inline-block; margin-right:20px;">
        	<div>Cash Amount : </div>        
            <div><input type="text" name="cashamount" class="textbox" onclick="this.select();" /></div>
        </div>  

        <input type="submit" name="b" value="Add Cash"  />
    </div>
    <div class="module_actions">
    	<div class="module_title"><img src='images/database_table.png'>SEARCH RESULTS : </div>
        <input type="submit" name="b" value="Pay" />
        <table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table" id="search_table">
            <tr bgcolor="#C0C0C0">				
                <th width="20"><b>#</b></th>
                <th width="20"></th>
                <th>Date</th>
                <th>Project</th>
                <th>Supplier</th>
            </tr> 
            <?php
			$result=mysql_query("
				select
					p.project_name,
					s.account,
					h.date,
					h.service_rr_header_id			
				from
					service_rr_header as h, projects as p, supplier as s
				where
					h.supplier_id = s.account_id
				and
					h.project_id = p.project_id
				and
					h.supplier_id = '$supplier_id'	
				and
					h.status != 'C'
				and
					service_rr_header_id
				not in
				(
					select
						service_rr_header_id
					from
						service_pay_header as h, service_payment_detail as d
					where
						h.service_pay_header_id = d.service_pay_header_id
					and
						h.status != 'C'
				)
			") or die(mysql_error());
			
			$i=1;
			while($r=mysql_fetch_assoc($result)){
				$date						= $r['date'];
				$project_name				= $r['project_name'];
				$account					= $r['account'];
				$service_rr_header_id	 	= $r['service_rr_header_id'];
				
            ?>
            <tr>
            	<td><?=$i++?></td>
                <td><input type="checkbox" name="h[]" value="<?=$service_rr_header_id?>" /></td>
                <td><?=date("F j, Y",strtotime($date))?></td>
                <td><?=$project_name?></td>
                <td><?=$date?></td>
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
    <div style="width:50%; float:left;">
	    <div class="module_title"><img src='images/book_open.png'>SERVICE DETAILS:  </div>
        <div class="module_actions">
        	<?php
			if($status=="S"){
            ?>
	        <input type="submit" name="b" value="Delete Service Details" onclick="return approve_confirm();"/>
            <?php
			}
            ?>
       	</div>
        <table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table" id="search_table">
            <tr bgcolor="#C0C0C0">				
                <th width="20"><b>#</b></th>
                <th width="20"></th>
                <th>Service RR #</th>
                <th>Amount</th>
            </tr> 
            <?php
			$result=mysql_query("
				select
					*
				from
					service_payment_detail
				where
					service_pay_header_id = '$service_pay_header_id'
			") or die(mysql_error());
			
			$i=1;
			while($r=mysql_fetch_assoc($result)){
				$service_rr_header_id		= $r['service_rr_header_id'];
				$service_rr_header_id_pad	= str_pad($service_rr_header_id,7,0,STR_PAD_LEFT);
				$amount						= $r['amount'];
				$service_payment_detail_id	= $r['service_payment_detail_id'];
				
            ?>
            <tr>
            	<td><?=$i++?></td>
                <td><input type="checkbox" name="service_payment_detail_id[]" value="<?=$service_payment_detail_id?>" onclick="document.header_form.checkAll.checked=false"></td>
                <td><?=$service_rr_header_id_pad?></td>
                <td class="align-right"><?=number_format($amount,2,'.',',')?></td>
            </tr>
            <?php
			}
            ?>
        </table>
    </div>
	
    <div style="width:50%; float:right;">
	    <div class="module_title"><img src='images/book_open.png'>SERVICE PAYMENTS  </div>
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
                <th width="20"></th>
                <th>Bank</th>
                <th>Check No.</th>
                <th>Check Date</th>
                <th>Check Status</th>
                <th>Check Amount</th>
            </tr> 
            <?php
			$result=mysql_query("
				select
					*
				from
					service_pay_check
				where
					service_pay_header_id = '$service_pay_header_id'
			") or die(mysql_error());
			
			$i=1;
			while($r=mysql_fetch_assoc($result)){
				$bank			= $r['bank'];
				$checkno		= $r['checkno'];
				$datecheck		= $r['datecheck'];
				$checkamount	= $r['checkamount'];
				$checkstatus 	= $r['checkstatus'];
				$service_pay_check_id	= $r['service_pay_check_id'];
				
            ?>
            <tr>
            	<td><?=$i++?></td>
                <td><input type="checkbox" name="service_pay_check_id[]" value="<?=$service_pay_check_id?>" onclick="document.header_form.checkAll.checked=false"></td>
                <td><?=$bank?></td>
                <td><?=$checkno?></td>
                <td><?=$datecheck?></td>
                <td><?=$checkstatus?></td>
                <td class="align-right"><?=number_format($checkamount,2,'.',',')?></td>
            </tr>
            <?php
			}
            ?>
           	<?php
            $result=mysql_query("
				select
					*
				from
					service_pay_cash
				where
					service_pay_header_id = '$service_pay_header_id'
			") or die(mysql_error());
			while($r=mysql_fetch_assoc($result)){
				$cashamount 			= $r['amount'];
				$service_pay_cash_id	= $r['service_pay_cash_id'];
			?> 
            <td><?=$i++?></td>
                <td><input type="checkbox" name="service_pay_cash_id[]" value="<?=$service_pay_cash_id?>" onclick="document.header_form.checkAll.checked=false"></td>
                <td colspan="4">CASH PAYMENT</td>
                <td class="align-right"><?=number_format($cashamount,2,'.',',')?></td>
            <?php
			}
            ?>
           
        </table>
    </div>
    <div style="clear:both;"></div>

    <?php
    }else if($b == "Print Preview" && $service_pay_header_id){

		echo "	<iframe id='JOframe' name='JOframe' frameborder='0' src='print_report_rr.php?id=$service_pay_header_id' width='100%' height='500'>
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


	