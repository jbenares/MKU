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
	$invadjust_header_id		= $_REQUEST['invadjust_header_id'];
	$invadjust_header_id_pad	= (!empty($invadjust_header_id))?str_pad($invadjust_header_id,7,0,STR_PAD_LEFT):"";
	$date						= $_REQUEST['date'];	
	$remarks					= $_REQUEST['remarks'];
	
	$project_id					= $_REQUEST['project_id'];
	$project_name		= $options->attr_Project($project_id,'project_name');
	$project_code		= $options->attr_Project($project_id,'project_code');
	$project_name_code	= ($project_id)?"$project_name - $project_code":"";
	
	$scope_of_work		= $_REQUEST['scope_of_work'];
	$work_category_id	= $_REQUEST['work_category_id'];
	$sub_work_category_id = $_REQUEST['sub_work_category_id'];
	
	
	$stock_id					= $_REQUEST['stock_id'];
	$quantity					= $_REQUEST['quantity'];
	$cost						= $_REQUEST['cost'];
	$amount	= $quantity * $cost;
	
	$invadjust_detail_id		= $_REQUEST['invadjust_detail_id'];
	$update_quantity			= $_REQUEST['update_quantity'];
	$update_cost				= $_REQUEST['update_cost'];
	
	$user_id					= $_SESSION['userID'];
	$checkList					= $_REQUEST['checkList'];
	
	if($b=="Submit"){
		$query="
			insert into 
				invadjust_header
			set
				date					= '$date',
				remarks					= '$remarks', 
				user_id					= '$user_id',
				status					= 'S',
				work_category_id 		= '$work_category_id',
				sub_work_category_id 	= '$sub_work_category_id',
				scope_of_work			= '$scope_of_work',
				project_id				= '$project_id'

		";	
		
		mysql_query($query) or die(mysql_error());
		
		$invadjust_header_id = mysql_insert_id();
		$options->insertAudit($invadjust_header_id,'invadjust_header_id','I');
		
		$msg="Transaction Saved";
		
	}else if($b=="Update"){
		$query="
			update
				invadjust_header
			set
				date					= '$date',
				remarks					= '$remarks', 
				user_id					= '$user_id',
				status					= 'S',
				work_category_id 		= '$work_category_id',
				sub_work_category_id 	= '$sub_work_category_id',
				scope_of_work			= '$scope_of_work',
				project_id				= '$project_id'
			where
				invadjust_header_id = '$invadjust_header_id'
		";	
		
		mysql_query($query) or die(mysql_error());
		$options->insertAudit($invadjust_header_id,'invadjust_header_id','U');		
		
		$msg = "Transaction Updated";
		
	}else if($b=="Cancel"){
		$query="
			update
				invadjust_header
			set
				status='C'
			where
				invadjust_header_id='$invadjust_header_id'
		";	
		mysql_query($query) or die(mysql_error());
		$options->insertAudit($invadjust_header_id,'invadjust_header_id','C');
		
		$msg = "Transaction Cancelled";
		
	}else if($b=="Finish"){
		$query="
			update
				invadjust_header
			set
				status='F'
			where
				invadjust_header_id='$invadjust_header_id'
		";	
		mysql_query($query) or die(mysql_error());
		$options->insertAudit($invadjust_header_id,'invadjust_header_id','F');
		$msg = "Transaction Finished";
		
	}else if($b=="Add Details"){
		
		mysql_query("
			insert into
				invadjust_detail
			set	
				invadjust_header_id		= '$invadjust_header_id',
				stock_id				= '$stock_id',
				quantity				= '$quantity',
				cost					= '$cost',
				amount					= '$amount'

		") or die(mysql_error());
		
		$msg = "Transaction Added";
	
	}else if($b=="Update Details"){
		$x=0;
		foreach($invadjust_detail_id as $id):
			$amount = $update_quantity[$x] * $update_cost[$x];
			
			mysql_query("
				update
					invadjust_detail
				set
					quantity 	= '$update_quantity[$x]',
					cost		= '$update_cost[$x]',
					amount		= '$amount'
				where
					invadjust_detail_id = '$id'
			") or die(mysql_error());
			$x++;
		endforeach;	
		
		$msg = "Transaction Details Updated";
	}else if($b=="Delete Selected"){
		//print_r($checkList);
		
		if(!empty($checkList)){
			foreach($checkList as $id){

				mysql_query("
					delete from
						invadjust_detail
					where	
						invadjust_detail_id = '$id'
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
			invadjust_header
		where
			invadjust_header_id ='$invadjust_header_id'
	";
	
	$result=mysql_query($query);
	$r=mysql_fetch_assoc($result);
	
	$invadjust_header_id_pad	= (!empty($invadjust_header_id))?str_pad($invadjust_header_id,7,0,STR_PAD_LEFT):"";
	$date						= $r['date'];	
	$project_id					= $r['project_id'];
	
	$project_name		= $options->attr_Project($project_id,'project_name');
	$project_code		= $options->attr_Project($project_id,'project_code');
	$project_display	= ($project_id)?"$project_name - $project_code":"";
	
	$remarks					= $r['remarks'];
	
	$user_id			= $r['user_id'];
	$status				= $r['status'];
	
	$scope_of_work		= $r['scope_of_work'];
	$work_category_id 	= $r['work_category_id'];
	$sub_work_category_id = $r['sub_work_category_id'];
	

?>
<div class=form_layout>
	<?php if(!empty($msg)) echo '<div id="status_update" class="ui-state-highlight ui-corner-all" style="padding: 0px 0.7em; text-align:left;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right:.3em;"></span> '.$msg.'</p></div>'; ?>
	<div class="module_title"><img src='images/user_orange.png'>INVENTORY ADJUSTMENTS</div>
    <form name="header_form" id="header_form" action="" method="post">
    <div class="module_actions">
        <input type="hidden" name="invadjust_header_id" id="invadjust_header_id" value="<?=$invadjust_header_id?>" />
        <div id="messageError">
            <ul>
            </ul>
        </div>
        <div class='inline'>
            Date: <br />
            <input type="text" class="datepicker required textbox3" title="Please enter date"  name="date" readonly='readonly'  value="<?=$date?>">
        </div>  
        
         <div class='inline'>
            Project : <br />  
            <input type="text" class="textbox" id="project_name" value="<?=$project_display?>" onclick="this.select();"  />
            <input type="hidden" name="project_id"  id="project_id" value="<?=$project_id?>" />
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
        <div class="inline">
        	Remarks : <br />
            <input type="text" class="textbox2" name="remarks" value="<?=$remarks?>" />
        </div>
        
        <?php
        if(!empty($status)){
        ?>
        <br />
        <div class="inline">
        	Adjustment # : <br />
            <input type="text" class="textbox3" name="status" id="status" value="<?=$invadjust_header_id_pad?>" readonly="readonly"/>
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
	if( $status == 'S'):	
	?>
    <div class="module_actions">
        <div class="inline">
        	Item : <br />
            <input type="text" class="textbox" name="stock_name" id="stock_name"  />
            <input type="hidden" name="stock_id" id="stock_id"  />
        </div>    
        <div class="inline">
        	<div>Quantity : </div>        
            <div><input type="text" size="20" name="quantity" id="quantity" class="textbox3" /></div>
        </div> 
        <div class="inline">
        	Cost : <br />
            <input type="text" class="textbox3" name="cost" id="cost" />
        </div>
        <input type="submit" name="b" value="Add Details" />
        <input type="submit" name="b" value="Update Details"  />
        <input type="submit" name="b" value="Delete Selected" onclick="return approve_confirm();"  />
   
    </div>
     <?php
    endif;
    ?>
    <?php
    if(!empty($status) && $b!="Print Preview"){
    ?>
    <div class="module_actions" >
	    <div class="module_title"><img src='images/book_open.png'>INVENTORY ADJUSTMENTS DETAILS :  </div>
        <table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table" id="search_table">
            <tr bgcolor="#C0C0C0">			
                <th width="20"><b>#</b></th>
                <th width="20"><input type="checkbox"  name="checkAll" onclick="javascript:check_all('header_form', this)" title="Check/Uncheck All" /></th>	
                <th>Item</th>
                <th width="60">Quantity</th>
                <th width="60">Unit</th>
                <th width="60">kg/pc</th>
                <th width="60">Total kg</th>
                <th width="60">Cost</th>
                <th width="60">Amount</th>
            </tr> 
            <?php
			$result=mysql_query("
				select
					d.stock_id,
					stock,
					quantity,
					unit,
					d.cost,
					invadjust_detail_id,
					amount,
					p.kg
				from
					invadjust_detail as d, productmaster as p
				where
					d.stock_id = p.stock_id
				and
					invadjust_header_id = '$invadjust_header_id'
			") or die(mysql_error());
			
			$i=1;
			while($r=mysql_fetch_assoc($result)){
				$stock_id 		= $r['stock_id'];
				$stock			= $r['stock'];
				$quantity		= $r['quantity'];
				$unit			= $r['unit'];
				$cost			= $r['cost'];
				$amount			= $r['amount'];
				$invadjust_detail_id	= $r['invadjust_detail_id'];
				
				$total_kg 		= $r['kg'] * $r['quantity'];

            ?>
            <tr>
            	<td><?=$i++?></td>
                <td><input type="checkbox" name="checkList[]" value="<?=$invadjust_detail_id?>" onclick="document.header_form.checkAll.checked=false"></td>
                <td><?=$stock?></td>
                <td><input type="text" class="textbox3" name="update_quantity[]" value="<?=$quantity?>" /></td>
                <td><?=$unit?></td>
                
                <td style="text-align:right;"><?=number_format($r['kg'],2,'.',',')?></td>
                <td style="text-align:right;"><?=number_format($total_kg,2,'.',',')?></td>
                
                <td><input type="text" class="textbox3" name="update_cost[]" value="<?=$cost?>" /></td>
                <td style="text-align:right;"><?=number_format($amount,2,'.',',')?></td>
                <input type="hidden" name="invadjust_detail_id[]" value="<?=$invadjust_detail_id?>" />
            </tr>
            <?php
			}
            ?>
        </table>
    </div>
    <?php
    }else if($b == "Print Preview" && $invadjust_header_id){

		echo "	<iframe id='JOframe' name='JOframe' frameborder='0' src='print_report_invadjust.php?id=$invadjust_header_id' width='100%' height='500'>
		       	</iframe>";
	}
    ?>
     </form>
    
</div>
<script type="text/javascript">
j(function(){	
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