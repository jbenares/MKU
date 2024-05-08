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
	$return_header_id		= $_REQUEST['return_header_id'];
	$date					= $_REQUEST['date'];
	$project_id				= $_REQUEST['project_id'];
	$project_name			= $options->attr_Project($project_id,'project_name');
	$project_code			= $options->attr_Project($project_id,'project_code');
	$project_name_code		= ($project_id)?"$project_name - $project_code":"";
	$remarks				= $_REQUEST['remarks'];
		
	$stock_id				= $_REQUEST['stock_id'];
	$quantity				= $_REQUEST['quantity'];
	$price					= $_REQUEST['price'];
	
	$scope_of_work		= $_REQUEST['scope_of_work'];
	$work_category_id	= $_REQUEST['work_category_id'];
	$sub_work_category_id = $_REQUEST['sub_work_category_id'];
		
	$user_id				= $_SESSION['userID'];
	
	$checkList				= $_REQUEST['checkList'];
	$search_item			= $_REQUEST['search_item'];	
	
	if($b=="Submit"){
		$query="
			insert into 
				return_header
			set
				project_id		= '$project_id',
				remarks			= '$remarks',
				date 			= '$date',
				status			= 'S',
				user_id			= '$user_id',
				work_category_id 		= '$work_category_id',
				sub_work_category_id 	= '$sub_work_category_id',
				scope_of_work			= '$scope_of_work'
		";	
		
		mysql_query($query) or die(mysql_error());
		
		$return_header_id = mysql_insert_id();
		$options->insertAudit($return_header_id,'return_header_id','I');
				
		$msg="Transaction Saved";
		
	}else if($b=="Update"){
		$query="
			update
				return_header
			set
				project_id		= '$project_id',
				remarks			= '$remarks',
				date 			= '$date',
				status			= 'S',
				user_id			= '$user_id',
				work_category_id 		= '$work_category_id',
				sub_work_category_id 	= '$sub_work_category_id',
				scope_of_work			= '$scope_of_work'
			where
				return_header_id	= '$return_header_id'
		";	
		
		mysql_query($query) or die(mysql_error());
		$options->insertAudit($return_header_id,'return_header_id','U');
		
		$msg = "Transaction Updated";
	
	}else if($b=="Cancel"){
		$query="
			update
				return_header
			set
				status='C'
			where
				return_header_id = '$return_header_id'
		";	
		mysql_query($query);
		$options->insertAudit($return_header_id,'return_header_id','C');
		
		$msg = "Transaction Cancelled";
		
	}else if($b=="Unfinish"){
		$query="
			update
				return_header
			set
				status='S'
			where
				return_header_id = '$return_header_id'
		";	
		mysql_query($query);
		$options->insertAudit($return_header_id,'return_header_id','S');
		
		$msg = "Transaction Unfinished";
		
	}else if($b=="Finish"){
		$query="
			update
				return_header
			set
				status = 'F'
			where
				return_header_id = '$return_header_id'
		";	
		mysql_query($query);
		$options->insertAudit($return_header_id,'return_header_id','F');
		
		$msg = "Transaction Finished";
		
	}else if($b=="Update Details"){
		$rr_detail_id=$_REQUEST[rr_detail_id];
		
		$quantity		= $_REQUEST[quantity];
		$cost			= $_REQUEST[cost];
		$package_id		= $_REQUEST[package_id];
		
		$x=0;
		
		foreach($rr_detail_id as $id):
			$packageqty=$options->getPackageQty($package_id[$x]);
		
			if($package_id[$x]){
				$amount=$quantity[$x] * $cost[$x] * $packageqty;
			}else{
				$amount=$quantity[$x] * $cost[$x];
			}
			
			mysql_query("
				update
					rr_detail
				set
					quantity='$quantity[$x]',
					amount='$amount'
				where
					rr_detail_id='$id'
			") or die(mysql_error());
			$x++;
		endforeach;	
		
		$msg = "Transaction Details Updated";
	}else if($b=="Delete Details"){
		//print_r($checkList);
		
		if(!empty($checkList)){
			foreach($checkList as $return_detail){

				mysql_query("
					delete from
						return_detail
					where	
						return_detail_id = '$return_detail'
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
			return_header
		where
			return_header_id = '$return_header_id'
	";
	
	$result=mysql_query($query);
	$r=mysql_fetch_assoc($result);

	$project_id			= $r['project_id'];
	$project_name		= $options->attr_Project($project_id,'project_name');
	$project_code		= $options->attr_Project($project_id,'project_code');
	$project_name_code	= ($project_id)?"$project_name - $project_code":"";
	$date				= ($r['date']=="0000-00-00")?"":$r['date'];
	
	$remarks			= $r['remarks'];
		
	$status				= $r['status'];
	$user_id			= $r['user_id'];
	
	$scope_of_work		= $r['scope_of_work'];
	$work_category_id 	= $r['work_category_id'];
	$sub_work_category_id = $r['sub_work_category_id'];

?>
<style type="text/css">
.inline{
	display:inline-block;	
}
</style>
<div class=form_layout>
	<?php if(!empty($msg)) echo '<div id="status_update" class="ui-state-highlight ui-corner-all" style="padding: 0px 0.7em; text-align:left;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right:.3em;"></span> '.$msg.'</p></div>'; ?>
	<div class="module_title"><img src='images/user_orange.png'>STOCKS RETURN</div>
    <form name="header_form" id="header_form" action="" method="post">
    <div class="module_actions">
        <input type="hidden" name="return_header_id" id="return_header_id" value="<?=$return_header_id?>" />
        <input type="hidden" name="view" value="<?=$view?>" />
        <div id="messageError">
            <ul>
            </ul>
        </div>
        
        <div style="display:inline-block;">
        	Date : <br />
            <input type="text" class="textbox3 required datepicker" title="Enter Date" name="date" value="<?=$date?>" />
        </div>
        
        <div style="display:inline-block;">
            Project : <br />  
            <input type="text" class="textbox" id="project_name" value="<?=$project_name_code?>" onclick="this.select();"  />
            <input type="hidden" class="required" name="project_id"  id="project_id" value="<?=$project_id?>" title="Select Project" />
        </div>   
        <div style="display:inline-block;">
        	Scope of Work :
            <div id="div_scope_of_work">
            	<select class="select">
                	<option value="">Select Project First...</option>
                </select>
            </div>
        </div>
        
        <div style="display:inline-block;">
        	Work Category : <br />
            <?=$options->option_workcategory($work_category_id,'work_category_id','Select Work Category')?>
        </div>
        <div id="subworkcategory_div" style="display:none;" >
        	Sub Work Category :
			<div id="subworkcategory">
            	
            </div>
        </div>
        <br />
        <div style="display:inline-block;">
            Remarks : <br />
            <textarea class="textarea_small" name='remarks'><?=$remarks?></textarea>
        </div>        
        <br />
        <?php
        if(!empty($status)){
        ?>
        <div class='inline'>
            Status : <br />
	     	<input type="text" class="textbox3" name="status" id="status" value="<?=$options->getTransactionStatusName($status)?>" readonly="readonly"/>
        </div> 
        <?php
        }
        ?>
    </div>
    <div class="module_actions">
	    <input type="submit" name="b" id="b" value="New" />
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
		
		if($return_header_id && $status == "F"){
        ?>	
            <input type="submit" value="Unfinish" name="b" id="b" />
    
        <?php
        }
		
        if($status!="C" && !empty($status)){
        ?>
        <input type="submit" name="b" id="b" value="Cancel" />
        <?php
        }
		?>
   	</div>
	<?php if($status=="S"){ ?>
    <div class="module_actions">
    	<div class="module_title"><img src='images/database_table.png'>SEARCH RESULTS : </div>
        <div class="module_actions">
        	Search Item : <input type="text" class="textbox" name="search_item" value="<?=$search_item?>"  onkeypress="if(event.keyCode==13){ jQuery('#search_button').click(); return false; }"/> 
            <input type="submit" name="b" value="Search"  id="search_button"/>
        </div>
        
		<?php if($b == "Search"): ?>
        <table cellspacing="2" cellpadding="5" width="48%" align="center" class="display_table" id="search_table" style="display:inline-table; vertical-align:top;">
            <tr bgcolor="#C0C0C0">				
                <th width="20"><b>#</b></th>
                <th width="30"></th>
                <th>Item</th>
                <th width="60">Unit</th>
                <th width="100">Project Qty</th>
                
                <!--<th>Warehouse Quantity</th>
                <th>Project Warehouse Quantity</th>
                <th>Issued Quantity</th>-->
            </tr> 
            <?php
			
			/*$sub_sql  = "
			(
				select
					distinct(d.stock_id) as stock_id
				from
					pr_detail as d, productmaster as p, pr_header as h, categories as c
				where
					h.pr_header_id= d.pr_header_id
				and d.stock_id = p.stock_id
				and project_id = '$project_id'
				and work_category_id = '$work_category_id'
				and sub_work_category_id = '$sub_work_category_id'
				and c.categ_id = p.categ_id1
				and c.category_type = 'M'
			)
			union all
			(
				select
					distinct(d.stock_id) as stock_id
				from
					transfer_header as h, transfer_detail as d
				where
				 	h.transfer_header_id = d.transfer_header_id
				and h.status != 'C'
				and h.project_id = '$project_id'
				and work_category_id = '$work_category_id'
				and sub_work_category_id = '$sub_work_category_id'
			)
			union all
			(
				select
					distinct(d.stock_id) as stock_id
				from
					invadjust_header as h, invadjust_detail as d
				where
					h.invadjust_header_id = d.invadjust_header_id
				and h.status != 'C'
				and h.project_id = '$project_id'
				and work_category_id = '$work_category_id'
				and sub_work_category_id = '$sub_work_category_id'
			)
			union all
			(
				select
					distinct(d.stock_id) as stock_id
				from
					rr_header as h, rr_detail as d
				where
					h.rr_header_id = d.rr_header_id
				and h.status != 'C'
				and h.project_id = '$project_id'
			)
			";*/
			
			/*$sql = "
				select
					t.stock_id,p.stock,p.unit
				from
					($sub_sql) as t left join productmaster as p on t.stock_id = p.stock_id
				where
					p.stock like '%$search_item%'
				group by stock_id
			";*/

			$sql = "
				select
					stock_id, stock, unit
				from 
					productmaster as p
				where
					p.stock like '%$search_item%'
				group by stock_id
			";

			#echo "<pre>";
			#echo $sql;
			#echo "</pre>";

			// echo $sql;
			$result=mysql_query($sql) or die(mysql_error());
			
			$i=1;
			while($r=mysql_fetch_assoc($result)){
				$stock_id 		= $r['stock_id'];
				$stock			= $r['stock'];
				$unit			= $r['unit'];
				
				$project_qty	= $options->inventory_projectwarehousebalance($date,$stock_id,$project_id);
				
				#$project_qty 	= $options->inventory_projectqty(NULL,$stock_id,$project_id,$scope_of_work,$work_category_id,$sub_work_category_id);
				#$issued_qty		= $options->issuance_issuedToProject($stock_id,$project_id,$work_category_id,$sub_work_category_id,$scope_of_work);
				
				#$remaining = $project_qty - $issued_qty;
				

				
            ?>
            <tr>
            	<td><?=$i++?></td>
                <td><input type="button" value="RETURN" onclick="xajax_returns_stock_id_form('<?=$stock_id?>');" /></td>
                <td><?=$stock?></td>
                <td><?=$unit?></td>
                <td class="align-right"><?=number_format($project_qty,2,'.',',')?></td>
                <!--<td class="align-right"><?=number_format($issued_qty,2,'.',',')?></td>
                <td class="align-right"><?=number_format($remaining,2,'.',',')?></td> -->
                
                <!--<td class="align-right"><?=number_format($warehouse_qty,2,'.',',')?></td>
                <td class="align-right"><?=number_format($project_warehouse_qty,2,'.',',')?></td>
                <td class="align-right"><?=number_format($issued_qty,2,'.',',')?></td>-->
                
            </tr>
            <?php
			}
            ?>
        </table>
        <?php endif; ?>
        <table cellspacing="2" cellpadding="5" width="45%" align="center" class="display_table" id="search_table" style="display:inline-table;">
        	<caption>
                <div class="module_actions" >
                    <div class="module_title"><img src='images/book_open.png'>STOCKS RETURN DETAILS:  </div>      
                </div>
                <div class="module_actions">
                    <?php if($status=="S"){ ?>
                    <input type="submit" name="b" value="Delete Details" onclick="return approve_confirm();"/>
                    <?php } ?>
                </div>
           	</caption>
            <tr bgcolor="#C0C0C0">				
                <th width="20"><b>#</b></th>
                <th width="20"><input type="checkbox"  name="checkAll" onclick="javascript:check_all('header_form', this)" title="Check/Uncheck All" /></th>
                <th>Item</th>
                <th width="100">Quantity</th>
                <th width="60">Unit</th>
            </tr> 
            <?php
			$result=mysql_query("
				select
					d.stock_id,
					stock,
					sum(quantity) as quantity,
					unit,
					return_detail_id
				from
					return_detail as d, productmaster as p
				where
					d.stock_id = p.stock_id
				and
					return_header_id = '$return_header_id'
				group by
					stock_id
			") or die(mysql_error());
			
			$i=1;
			while($r=mysql_fetch_assoc($result)){
				$stock_id 		= $r['stock_id'];
				$stock			= $r['stock'];
				$quantity		= $r['quantity'];
				$unit			= $r['unit'];
				$return_detail_id	= $r['return_detail_id'];
				$amount	= $quantity * $cost;
				
            ?>
            <tr>
            	<td><?=$i++?></td>
                <td><input type="checkbox" name="checkList[]" value="<?=$return_detail_id?>" onclick="document.header_form.checkAll.checked=false"></td>
                <td><?=$stock?></td>
                <td class="align-right"><?=number_format($quantity,2,'.',',')?></td>
                <td><?=$unit?></td>
            </tr>
            <?php } ?>
        </table>
    </div>
    <?php
    }
	if(!empty($status) && $b!="Print Preview"){
    ?>
    
    <?php
	}else if($b == "Print Preview" && $return_header_id){

		echo "	<iframe id='JOframe' name='JOframe' frameborder='0' src='print_report_stocks_return.php?id=$return_header_id' width='100%' height='500'>
		       	</iframe>";
	?>
   
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