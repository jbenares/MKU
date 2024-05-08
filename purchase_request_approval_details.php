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
	$b					= $_REQUEST['b'];
	$pr_header_id		= $_REQUEST['pr_header_id'];
	$project_id			= $_REQUEST['project_id'];
	$date				= $_REQUEST['date'];
	$date_needed		= $_REQUEST['date_needed'];
	$project_name		= $options->attr_Project($project_id,'project_name');
	$project_code		= $options->attr_Project($project_id,'project_code');
	$project_name_code	= ($project_id)?"$project_name - $project_code":"";
	$description		= $_REQUEST['description'];
	$work_category_id	= $_REQUEST['work_category_id'];
	$sub_work_category_id = $_REQUEST['sub_work_category_id'];
	$scope_of_work		= $_REQUEST['scope_of_work'];

	$stock_id			= $_REQUEST['stock_id'];
	$quantity			= $_REQUEST['quantity'];

	$pr_detail_id		= $_REQUEST['pr_detail_id'];
	$detail_quantity	= $_REQUEST['detail_quantity'];
	$checkList			= $_REQUEST['checkList'];

	$user_id			= $_SESSION['userID'];
	$s					= $_REQUEST['s'];
	$id					= $_REQUEST['id'];
  $checkList 		= $_REQUEST['checkList'];

	if( $b == "Undo" ){
		mysql_query("
			update
				pr_header
			set
				approval_status = 'P',
				approved_by = '$user_id',
				approval_date = ''
			where
				pr_header_id = '$pr_header_id'
		") or die(mysql_error());
		$msg = "Transaction set back to Pending Status";
	}

	if($b == 'Approve Details') {

      if(!empty($checkList)){
        foreach($checkList as $id){
          $query="
      			update
      				pr_detail
      			set
      				allowed='1'
      			where
      				pr_detail_id='$id'
      		";
      		mysql_query($query) or die(mysql_error());

        }

      }
		$msg = "Updated Details";

	}else if($b == 'M' && $s == 'D') {

		$query="
			update
				pr_detail
			set
				allowed='0'
			where
				pr_detail_id='$id'
		";
		mysql_query($query) or die(mysql_error());

		$msg = "Updated Details";
	}else if($b == 'E' && $s == 'A') {

		$query="
			update
				pr_equipment_detail
			set
				allowed='1'
			where
				pr_equipment_detail_id='$id'
		";
		mysql_query($query) or die(mysql_error());

		$msg = "Updated Details";

	}else if($b == 'E' && $s == 'D') {


		$query="
			update
				pr_equipment_detail
			set
				allowed='0'
			where
				pr_equipment_detail_id = '$id'
		";
		mysql_query($query) or die(mysql_error());

		$msg = "Updated Details";
	}else if($b == 'S' && $s == 'A') {

		$query="
			update
				pr_service_detail
			set
				allowed='1'
			where
				pr_service_detail_id = '$id'
		";
		mysql_query($query) or die(mysql_error());

		$msg = "Updated Details";

	}else if($b == 'S' && $s == 'D') {


		$query="
			update
				pr_service_detail
			set
				allowed='0'
			where
				pr_service_detail_id = '$id'
		";
		mysql_query($query) or die(mysql_error());

		$msg = "Updated Details";
	}else if($b == 'F' && $s == 'A') {

		$query="
			update
				pr_fuel_detail
			set
				allowed='1'
			where
				pr_fuel_detail_id = '$id'
		";
		mysql_query($query) or die(mysql_error());

		$msg = "Updated Details";

	}else if($b == 'F' && $s == 'D') {


		$query="
			update
				pr_fuel_detail
			set
				allowed='0'
			where
				pr_fuel_detail_id = '$id'
		";
		mysql_query($query) or die(mysql_error());

		$msg = "Updated Details";
	}



	else if($b=="Cancel"){
		$query="
			update
				budget_header
			set
				status='C'
			where
				pr_header_id = '$pr_header_id'
		";
		mysql_query($query);

		$msg = "Transaction Cancelled";

	}else if($b=="Finish"){
		$query="
			update
				pr_header
			set
				status='F'
			where
				pr_header_id = '$pr_header_id'
		";
		mysql_query($query);

		$msg = "Transaction Finished";

	}else if($b=="Search"){
		header("location: admin.php?view=c4f6f92bbbb72914dcda");
	}else if($b=="Approve"){
		//$date = "";
		mysql_query("
			update
				pr_header
			set
				approval_status = 'A',
				approved_by = '$user_id',
				approval_date  = '".date("Y-m-d h:i:s")."'
			where
				pr_header_id = '$pr_header_id'
		") or die(mysql_error());
		$msg = "Purchase Request Approved";

		$options->insertAudit($pr_header_id,'pr_header_id','A');

	}else if($b=="Disapprove"){
		mysql_query("
			update
				pr_header
			set
				approval_status = 'D'
			where
				pr_header_id = '$pr_header_id'
		") or die(mysql_error());
		$msg = "Purchase Request Disapproved";

		$options->insertAudit($pr_header_id,'pr_header_id','D');
	}

	$query="
		select
			*
		from
			pr_header
		where
			pr_header_id ='$pr_header_id'
	";

	$result=mysql_query($query) or die(mysql_error());
	$r=mysql_fetch_assoc($result);

	$project_id			= $r['project_id'];
	$date				= $r['date'];
	$date_needed		= $r['date_needed'];
	$project_name		= $options->attr_Project($project_id,'project_name');
	$project_code		= $options->attr_Project($project_id,'project_code');
	$project_name_code	= ($project_id)?"$project_name - $project_code":"";
	$description		= $r['description'];
	$status				= $r['status'];
	$approval_status	= $r['approval_status'];
	$work_category_id 	= $r['work_category_id'];
	$sub_work_category_id = $r['sub_work_category_id'];
	$scope_of_work		= $r['scope_of_work'];

	$user_id			= $r['user_id'];
	$approved_by		= $r['approved_by'];
?>
<?php if($approval_status == "A" || $approval_status == "D") { ?>
<style type="text/css">
	.display_table tr td:nth-child(2), .display_table tr th:nth-child(2),.display_table tr td:nth-child(3),.display_table tr th:nth-child(3){
		display:none;
	}
</style>
<?php } ?>



<form name="header_form" id="header_form" action="" method="post">
<div class=form_layout>
	<?php if(!empty($msg)) echo '<div id="status_update" class="ui-state-highlight ui-corner-all" style="padding: 0px 0.7em; text-align:left;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right:.3em;"></span> '.$msg.'</p></div>'; ?>
	<div class="module_title"><img src='images/user_orange.png'>PURCHASE REQUEST</div>


    <div class="module_actions">
        <input type="hidden" name="pr_header_id" id="pr_header_id" value="<?=$pr_header_id?>" />
        <div id="messageError">
            <ul>
            </ul>
        </div>
        <div class="inline">
            Date Requested : <br />
            <input type="text" class="textbox3 datepicker required" name="date" value="<?=$date?>" />
        </div>

        <div class="inline">
            Date Needed : <br />
            <input type="text" class="textbox3 datepicker required" name="date_needed" value="<?=$date_needed?>" />
        </div>

        <div class='inline'>
            Project : <br />
            <input type="text" class="textbox" id="project_name" value="<?=$project_name_code?>" onclick="this.select();"  />
            <input type="hidden" name="project_id"  id="project_id" value="<?=$project_id?>" />
        </div>

        <!--<div class="inline">
            Scope of Work : <br />
            <input type="text"  value="<?=$scope_of_work?>" name='scope_of_work' class="textbox" />
        </div>-->
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
            Description : <br />
            <textarea class="textarea_small" name='description'><?=$description?></textarea>
        </div>

      	<div class="inline" style="vertical-align:top;">
	   	<?php
        if(!empty($status)){
        ?>

        	<div class='inline'>
                PR #: <br />
                <input type="text" class="textbox" value="<?=str_pad($pr_header_id,7,0,STR_PAD_LEFT)?>" readonly="readonly"/>
            </div>

            <div class='inline'>
                Requested by : <br />
                <input type="text" class="textbox" name="status" id="status" value="<?=$options->getUserName($user_id)?>" readonly="readonly"/>
            </div>

            <div class='inline'>
                Approval Status : <br />
                <input type="text" class="textbox3"  value="<?=$options->getApprovalStatus($approval_status)?>" readonly="readonly"/>
            </div>

            <?php
            if(!empty($approved_by)){
            ?>
            <div class='inline'>
                Approved by : <br />
                <input type="text" class="textbox" name="status" id="status" value="<?=$options->getUserName($approved_by)?>" readonly="readonly"/>
            </div>
            <?php
            }
            ?>

            <div class='inline'>
                Status : <br />
                <input type="text" class="textbox3" value="<?=$options->getTransactionStatusName($status)?>" readonly="readonly"/>
            </div>
        <?php
        }
        ?>
      	</div>
    </div>

    <div class="module_actions">

        <input type="submit" name="b" id="b" value="Search" />
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

        <?php if(in_array($approval_status,array("A","D"))){ ?>
        <input type="submit" name="b" id="b" value="Undo" />
        <?php } ?>
    </div>

 	<?php
	if($approval_status=="P"){
	?>
        <div class="module_actions">
            <input type="submit" name="b" id="b" value="Approve" onclick="return approve_confirm();" />
            <input type="submit" name="b" id="b" value="Disapprove" onclick="return approve_confirm();" />
            <input type="submit" name="b" id="b" value="Approve Details" onclick="return approve_confirm();" />
        </div>
  	<?php
	}
    ?>

    <div style="clear:both" >
    	<div id="tabs">
            <ul>
					<!-- NEW CODE STARTS HERE !-->
				<li><a href="#tabs-0">LABOR</a></li>
					<!-- NEW CODE ENDS HERE !-->
                <li><a href="#tabs-1">MATERIALS</a></li>
                <li><a href="#tabs-2">SERVICES</a></li>
                <li><a href="#tabs-3">EQUIPMENT RENTALS</a></li>
                <li><a href="#tabs-4">FUEL OIL LUBRICANTS</a></li>
            </ul>
       <!-- NEW CODE STARTS HERE !-->
		<div id="tabs-0">
				<table cellspacing="2" cellpadding="5" width="100%" align="center" id="search_table" style="border:1px solid #000;">
				<tr bgcolor="#333333">
					<td colspan="4"></td>
					<td colspan="4"><font color="#ffffff"><center><b>Budget</b></center></font></td>
					<!--<td></td>-->
					<td colspan="2"><font color="#ffffff"><center><b>Requested</b></center></font></td>
					<td colspan="2"><font color="#ffffff"><center><b>Balance</b></center></font></td>
				</tr>
				<tr bgcolor="#C0C0C0" style="text-align:left;">
					<th width="20">#</th>
					<th>Date Requested</th>
					<th>Description</th>
					<th width="60">Unit</th>
					<th width="60">Total Qty</th>
					<th width="100">Price/Unit</th>
					<th width="100">Total</th>
					<th width="20"></th>
					<th width="60">Total Qty</th>
					<th width="100">Amount</th>
					<th width="100">Total Qty</th>
					<th width="100">Amount</th>
				</tr>
				<?php
					$query = "
						select
							*
						from
							labor_budget_pr as l,
							labor_budget_details as d,
							work_type as w
						where
							l.labor_budget_details_id = d.id
						and
							w.work_code_id = d.work_code_id
						and
							l.is_deleted != '1'
						and
							d.is_deleted != '1'
						and
							w.is_deleted != '1'
						and
							l.pr_header_id = '$pr_header_id'

					";
					$result=mysql_query($query) or die(mysql_error());
				$i=1;
				while($r=mysql_fetch_assoc($result)){
					$pr_lb_id			= $r['pr_lb_id'];
					$description			= $r['description'];
					$unit				= $r['unit'];
					$qty				= $r['qty'];

					$trqty 			= $r[total_req_qty];
						if($r['tag']==1){
							$price_per_unit				= $r['wt_price_per_unit'];
						}else{
							$price_per_unit				= $r['price_per_unit'];
						}
					$date_requested			= $r['date_requested'];
					$dr = date("M d, Y",strtotime($date_requested));
					$t_total = $trqty * $price_per_unit;



					$req_qty				= $r['requested_qty'];
					$req_price = $trqty * $price_per_unit;
					$balqty = $qty - $req_qty;
					$balamt = $t_total - $req_price;
				?>
					<tr>
						<td><?=$i++?></td>
						<!--<td><a href="admin.php?view=<?=$view?>&pr_header_id=<?=$pr_header_id?>&b=Delete"><img src="images/trash.gif" onclick="return approve_confirm();" /></a></td>!-->
						<td><?=$dr?></td>
						<td><?=$description?></td>
						<td><?=$unit?></td>
						<td><?=$trqty?></td>
						<td><?=$price_per_unit?></td>
						<td><?=number_format($t_total, 2)?></td>
						<td></td>
						<td><?=$req_qty?></td>
						<td><?=number_format($req_price, 2)?></td>
						<td><?=$balqty?></td>
						<td><?=number_format($balamt, 2)?></td>
						<input type='hidden' name='pr_service_detail_id[]' value='<?=$pr_service_detail_id?>' />
					</tr>
					<?php
					}
					?>
				</table>
			</div>
			<!-- NEW CODE ENDS HERE !-->
            <div id="tabs-1">
                <table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table" id="search_table">
                <tr>
                    <th width="20">#</th>
                    <!--<td width="15"></td>-->
                    <th width="20" align="center"><input type="checkbox"  name="checkAll" value="Comfortable with" onclick="javascript:check_all('header_form', this)" title="Check/Uncheck All" /></th>
                    <th width="15"></th>
                    <th>Item</th>
                    <th width="40">Request Quantity</th>
                    <th width="40">Warehouse Quantity</th>
                    <th width="40">Total Quantity</th>
                    <th width="100">Unit</th>
                    <th width="100">Approved Purchase Request</th>
                    <th width="100">In-House Budget</th>
                    <th width="100">Acutal Received</th>
                    <th width="100">Balance</th>
                    <th width="100">Status</th>
                </tr>
                <?php
                 $query = "
                          select
                              *
                          from
                              pr_detail as d,
                              productmaster as pm
                          where
                              d.pr_header_id	= '$pr_header_id'
                          and
                              pm.stock_id = d.stock_id
                        ";
                $result=mysql_query($query) or die(mysql_error());
                $i=1;
                while($r=mysql_fetch_assoc($result)){
                    $pr_detail_id		= $r['pr_detail_id'];
                    $stock_id			= $r['stock_id'];
                    $stock				= $r['stock'];
                    $quantity			= $r['quantity'];
          					$request_quantity	= $r['request_quantity'];
          					$warehouse_quantity = $r['warehouse_quantity'];
                    $unit				= $r['unit'];
                    $in_stock			= $r['in_stock'];
                    $in_budget			= $r['in_budget'];
                    $allowed			= $r['allowed'];

                    $allowed_name		= ($allowed)?"ALLOWED":"NOT ALLOWED";


                    $requested_qty				= $options->total_approved_stocks_requested($stock_id,$project_id,$work_category_id,$sub_work_category_id,$scope_of_work);
                    $actual_received 			= $options->inventory_actual_received(NULL,$stock_id,$project_id,$scope_of_work,$work_category_id,$sub_work_category_id);
                    $total_budget 				= $options->budget_stock($stock_id,$project_id,$scope_of_work,$work_category_id,$sub_work_category_id);

                    $balance = $total_budget - $actual_received;

                ?>
                    <tr>
                        <td><?=$i++?></td>
                        <!--<td>
                        	<a href="admin.php?view=<?=$view?>&pr_header_id=<?=$pr_header_id?>&id=<?=$pr_detail_id?>&b=M&s=A" onclick="return approve_confirm();">
	                        	<img src="images/icon_accept.gif" />
                           	</a>
                        </td>--><td><input type="checkbox" name="checkList[]" value="<?=$pr_detail_id?>" onclick="document.header_form.checkAll.checked=false" class="check_box" rel="<?=$pr_detail_id?>" ></td>

                        <td>
                        	<a href="admin.php?view=<?=$view?>&pr_header_id=<?=$pr_header_id?>&id=<?=$pr_detail_id?>&b=M&s=D" onclick="return approve_confirm();">
	                        	<img src="images/action_stop.gif" />
                           	</a>
                        </td>
                        <td><?=$stock?></td>
                        <td><input type='text' class='textbox-int align-right' value='<?=$request_quantity?>'  /></td>
                        <td><input type='text' class='textbox-int align-right' value='<?=$warehouse_quantity?>'  /></td>
                        <td><input type='text' class='textbox-int align-right' value='<?=$quantity?>'  /></td>
                        <td><?=$unit?></td>
                        <td class="align-right"><?=number_format($requested_qty,2,'.',',')?></td>
                        <td class="align-right"><?=number_format($total_budget,2,'.',',')?></td>
                        <td class="align-right"><?=number_format($actual_received,2,'.',',')?></td>
                        <td class="align-right"><?=number_format($balance,2,'.',',')?></td>
                        <td><?=$allowed_name?></td>
                        <input type='hidden' value='<?=$stock_id?>' />
                        <input type='hidden' name='pr_detail_id[]' value='<?=$pr_detail_id?>' />
                    </tr>
                <?php
                }
                ?>
            </table>
            </div>

            <div id="tabs-2">
                <table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table" id="search_table">
                    <tr>
                        <th width="20">#</th>
                        <td width="20" align="center"><!--<input type="checkbox"  name="checkAll" value="Comfortable with" onclick="javascript:check_all('header_form', this)" title="Check/Uncheck All" />--></td>
                        <td width="20" align="center"></td>
                        <th>Designation</th>
                        <th width="60">No</th>
                        <th width="60">No. of Days</th>
                        <th width="60">Rate/Day</th>
                        <th width="100">Amount</th>
                        <th width="100">In House Budget</th>
                        <th width="100">Approved Request</th>
                        <th width="100">Service Receieved</th>
                        <th width="100">Status</th>
                    </tr>
                    <?php
                    $query = "
                        select
                            *
                        from
                            pr_service_detail as d,
                            productmaster as pm
                        where
                            d.pr_header_id	= '$pr_header_id'
                        and
                            pm.stock_id = d.stock_id
                    ";
                    $result=mysql_query($query) or die(mysql_error());
                    $i=1;
                    while($r=mysql_fetch_assoc($result)){
                        $pr_service_detail_id		= $r['pr_service_detail_id'];
                        $stock_id			= $r['stock_id'];
                        $stock				= $r['stock'];
                        $quantity			= $r['quantity'];
                        $days				= $r['days'];
                        $rate_per_day		= $r['rate_per_day'];
                        $unit				= $r['unit'];
                        $amount				= $r['amount'];
                        $allowed			= $r['allowed'];

                        $allowed_name		= ($allowed)?"ALLOWED":"NOT ALLOWED";

                        $service_received = $options->service_received($project_id,$scope_of_work,$work_category_id,$sub_work_category_id,$stock_id);
                        $budget = $options->service_budget($project_id,$scope_of_work,$work_category_id,$sub_work_category_id,$stock_id);
                        $service_approved_request = $options->service_approved_request($project_id,$scope_of_work,$work_category_id,$sub_work_category_id,$stock_id);
                    ?>
                        <tr>
                            <td><?=$i++?></td>
                            <td>
                                <a href="admin.php?view=<?=$view?>&pr_header_id=<?=$pr_header_id?>&id=<?=$pr_service_detail_id?>&b=S&s=A" onclick="return approve_confirm();">
                                    <img src="images/icon_accept.gif" />
                                </a>
                            </td>
                            <td>
                                <a href="admin.php?view=<?=$view?>&pr_header_id=<?=$pr_header_id?>&id=<?=$pr_service_detail_id?>&b=S&s=D" onclick="return approve_confirm();">
                                    <img src="images/action_stop.gif" />
                                </a>
                            </td>
                            <td><?=$stock?></td>
                            <td><input type='text' class='textbox3 align-right' name='update_service_quantity[]' value='<?=$quantity?>'  /></td>
                            <td><input type='text' class='textbox3 align-right' name='update_service_days[]' value='<?=$days?>'  /></td>
                            <td><input type='text' class='textbox3 align-right' name='update_service_rate_per_day[]' value='<?=$rate_per_day?>'  /></td>
                            <td class="align-right"><?=number_format($amount,2,'.',',')?></td>
                            <td class="align-right"><?=number_format($budget,2,'.',',')?></td>
                            <td class="align-right"><?=number_format($service_approved_request,2,'.',',')?></td>
                            <td class="align-right"><?=number_format($service_received,2,'.',',')?></td>
                            <td><?=$allowed_name?></td>
                            <input type='hidden' name='pr_service_detail_id[]' value='<?=$pr_detail_id?>' />
                        </tr>
                    <?php
                    }
                    ?>
                </table>
            </div>

            <div id="tabs-3">
                <table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table" id="search_table">
                    <tr>
                        <th width="20">#</th>
                        <td width="20" align="center"></td>
                        <td width="20"></td>
                        <th>Description</th>
                        <th width="60">No</th>
                        <th width="60">No. of Days</th>
                        <th width="60">Rental/Day</th>
                        <th width="100">Amount</th>
                        <th width="100">In House Budget</th>
                        <th width="100">Approved Request</th>
                        <th width="100">Equipment Receieved</th>
                        <th width="100">Status</th>
                    </tr>
                    <?php
                    $query = "
                        select
                            *
                        from
                            pr_equipment_detail as d,
                            productmaster as pm
                        where
                            d.pr_header_id	= '$pr_header_id'
                        and
                            pm.stock_id = d.stock_id
                    ";
                    $result=mysql_query($query) or die(mysql_error());
                    $i=1;
                    while($r=mysql_fetch_assoc($result)){
                        $pr_equipment_detail_id		= $r['pr_equipment_detail_id'];
                        $stock_id			= $r['stock_id'];
                        $stock				= $r['stock'];
                        $quantity			= $r['quantity'];
                        $days				= $r['days'];
                        $rate_per_day		= $r['rate_per_day'];
                        $unit				= $r['unit'];
                        $amount				= $r['amount'];
                        $allowed			= $r['allowed'];

                        $allowed_name		= ($allowed)?"ALLOWED":"NOT ALLOWED";

                        $equipment_received = $options->equipment_received($project_id,$scope_of_work,$work_category_id,$sub_work_category_id,$stock_id);
                        $budget = $options->equipment_budget($project_id,$scope_of_work,$work_category_id,$sub_work_category_id,$stock_id);
                        $equipment_approved_request = $options->equipment_approved_request($project_id,$scope_of_work,$work_category_id,$sub_work_category_id,$stock_id);
                    ?>
                        <tr>
                            <td><?=$i++?></td>
                            <td>
                                <a href="admin.php?view=<?=$view?>&pr_header_id=<?=$pr_header_id?>&id=<?=$pr_equipment_detail_id?>&b=E&s=A" onclick="return approve_confirm();">
                                    <img src="images/icon_accept.gif" />
                                </a>
                            </td>
                            <td>
                                <a href="admin.php?view=<?=$view?>&pr_header_id=<?=$pr_header_id?>&id=<?=$pr_equipment_detail_id?>&b=E&s=D" onclick="return approve_confirm();">
                                    <img src="images/action_stop.gif" />
                                </a>
                            </td>
                            <td><?=$stock?></td>
                            <td><input type='text' class='textbox3 align-right' name='update_equipment_quantity[]' value='<?=$quantity?>'  /></td>
                            <td><input type='text' class='textbox3 align-right' name='update_equipment_days[]' value='<?=$days?>'  /></td>
                            <td><input type='text' class='textbox3 align-right' name='update_equipment_rate_per_day[]' value='<?=$rate_per_day?>'  /></td>
                            <td class="align-right"><?=number_format($amount,2,'.',',')?></td>
                            <td class="align-right"><?=number_format($budget,2,'.',',')?></td>
                            <td class="align-right"><?=number_format($equipment_approved_request,2,'.',',')?></td>
                            <td class="align-right"><?=number_format($equipment_received,2,'.',',')?></td>
                            <td><?=$allowed_name?></td>
                            <input type='hidden' name='pr_equipment_detail_id[]' value='<?=$pr_detail_id?>' />
                        </tr>
                    <?php
                    }
                    ?>
                </table>
            </div>

            <div id="tabs-4">
                <table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table" id="search_table">
                    <tr>
                        <th width="20">#</th>
                        <td width="20" align="center"><!--<input type="checkbox"  name="checkAll" value="Comfortable with" onclick="javascript:check_all('header_form', this)" title="Check/Uncheck All" />--></td>
                        <td width="20"></td>
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
                        <th width="100">In House Budget</th>
                        <th width="100">Approved Request</th>
                        <th width="100">Fuel Receieved</th>
                        <th width="100">Status</th>
                    </tr>
                    <?php
                    $query = "
                        select
							*
						from
							pr_fuel_detail
						where
							pr_header_id	= '$pr_header_id'
                    ";
                    $result=mysql_query($query) or die(mysql_error());
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
						$allowed					= $r['allowed'];
						$allowed			= $r['allowed'];
						$fuel		= $options->attr_stock($fuel_id,'stock');
						$equipment	= $options->attr_stock($equipment_id,'stock');

						$allowed_name		= ($allowed)?"ALLOWED":"NOT ALLOWED";

                        $fuel_received = $options->fuel_received($project_id,$scope_of_work,$work_category_id,$sub_work_category_id,$fuel_id,$equipment_id);
                        $budget = $options->fuel_budget($project_id,$scope_of_work,$work_category_id,$sub_work_category_id,$fuel_id,$equipment_id);
                        $fuel_approved_request = $options->fuel_approved_request($project_id,$scope_of_work,$work_category_id,$sub_work_category_id,$fuel_id,$equipment_id);
                    ?>
                        <tr>
                            <td><?=$i++?></td>
                            <td>
                                <a href="admin.php?view=<?=$view?>&pr_header_id=<?=$pr_header_id?>&id=<?=$pr_fuel_detail_id?>&b=F&s=A" onclick="return approve_confirm();">
                                    <img src="images/icon_accept.gif" />
                                </a>
                            </td>
                            <td>
                                <a href="admin.php?view=<?=$view?>&pr_header_id=<?=$pr_header_id?>&id=<?=$pr_fuel_detail_id?>&b=F&s=D" onclick="return approve_confirm();">
                                    <img src="images/action_stop.gif" />
                                </a>
                            </td>
                            <td><?=$fuel?></td>
                            <td><?=$equipment?></td>
                            <td><input type='text' class='textbox-int align-right' value='<?=$consumption_per_day?>'  /></td>
                            <td><input type='text' class='textbox-int align-right' value='<?=$days?>'  /></td>

                            <td><input type='text' class='textbox-int align-right' value='<?=$request_quantity?>'  /></td>
                            <td><input type='text' class='textbox-int align-right' value='<?=$request_quantity * $consumption_per_day * $days?>'  /></td>
                            <td><input type='text' class='textbox-int align-right' value='<?=$warehouse_quantity?>'  /></td>
                            <td><input type='text' class='textbox-int align-right' value='<?=$total_quantity?>'  /></td>


                            <td><input type='text' class='textbox-int align-right' value='<?=$cost_per_litter?>'  /></td>
                            <td class="align-right highlight"><?=number_format($amount,2,'.',',')?></td>
                            <td class="align-right"><?=number_format($budget,2,'.',',')?></td>
                            <td class="align-right"><?=number_format($fuel_approved_request,2,'.',',')?></td>
                            <td class="align-right"><?=number_format($fuel_received,2,'.',',')?></td>
                            <td><?=$allowed_name?></td>
                            <input type='hidden' name='pr_equipment_detail_id[]' value='<?=$pr_detail_id?>' />
                        </tr>
                    <?php
                    }
                    ?>
                </table>
            </div>
        </div>
		<?php
        if($b == "Print Preview" && $pr_header_id){

        echo "	<iframe id='JOframe' name='JOframe' frameborder='0' src='print_report_purchase_request.php?id=$pr_header_id' width='100%' height='500'>
                    </iframe>";
        ?>
        <?php
        }
        ?>
    </div>


</div>
</form>
<script type="text/javascript">
j(function(){
	<?php
		switch($b){
			case 'M':
				$i = 0;
				break;
			case 'S':
				$i = 1;
				break;
			case 'E':
				$i = 2;
				break;
			case 'F':
				$i = 3;
				break;
			default:
				$i = 0;
		}
	?>

	j("#tabs").tabs({ selected : <?=$i?> });

	//j("#status_update").show(500);

	/*setTimeout(function(){
		j("#status_update").hide(500);
	},3000);
	*/
	j("#work_category_id").change(function(){
		xajax_display_subworkcategory(this.value);
	});

	<?php
	if(!empty($status)){
	?>
		xajax_display_subworkcategory('<?=$work_category_id?>','<?=$sub_work_category_id?>');
	<?php
	}
	?>
});
</script>
