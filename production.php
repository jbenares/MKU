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
	$production_header_id		= $_REQUEST['production_header_id'];
	$production_header_id_pad	= (!empty($production_header_id))?str_pad($production_header_id,7,0,STR_PAD_LEFT):"";
	$date						= $_REQUEST['date'];	
	$stock_id 					= $_REQUEST['stock_id'];
	$formulation_header_id		= $_REQUEST['formulation_header_id'];
	$actualoutput				= $_REQUEST['actualoutput'];
	
	$detail_stock_id			= $_REQUEST['detail_stock_id'];
	$detail_quantity			= $_REQUEST['detail_quantity'];
	$detail_cost				= $_REQUEST['detail_cost'];
	
	$user_id					= $_SESSION['userID'];
	
	if($b=="Submit"){
		$query="
			insert into 
				production_header
			set
				date					= '$date',
				stock_id				= '$stock_id',
				formulation_header_id	= '$formulation_header_id',
				actualoutput			= '$actualoutput',
				user_id					= '$user_id',
				status='S'
		";	
		
		mysql_query($query) or die(mysql_error());
		
		$production_header_id = mysql_insert_id();
		$options->insertAudit($production_header_id,'production_header_id','I');
		
		/*INSERT PRODUCTION DETAILS*/
		$i=0;
		foreach($detail_stock_id as $stock_id){
			$amount = $detail_quantity[$i] * $detail_cost[$i];
			
			mysql_query("
				insert into
					production_detail
				set
					production_header_id 	= '$production_header_id',
					stock_id				= '$stock_id',
					quantity				= '$detail_quantity[$i]',
					cost					= '$detail_cost[$i]',
					amount					= '$amount'
			") or die(mysql_error());
			
			$i++;
		}
		
		$msg="Transaction Saved";
		
	}else if($b=="Update"){
		$query="
			update
				production_header
			set
				date					= '$date',
				stock_id				= '$stock_id',
				formulation_header_id	= '$formulation_header_id',
				actualoutput			= '$actualoutput',
				user_id					= '$user_id',
				status='S'
			where
				production_header_id = '$production_header_id'
		";	
		
		mysql_query($query) or die(mysql_error());
		$options->insertAudit($production_header_id,'production_header_id','U');		
		
		$msg = "Transaction Updated";
		
	}else if($b=="Cancel"){
		$query="
			update
				production_header
			set
				status='C'
			where
				production_header_id='$production_header_id'
		";	
		mysql_query($query) or die(mysql_error());
		$options->insertAudit($production_header_id,'production_header_id','C');
		
		$msg = "Transaction Cancelled";
		
	}else if($b=="Finish"){
		$query="
			update
				production_header
			set
				status='F'
			where
				production_header_id='$production_header_id'
		";	
		mysql_query($query) or die(mysql_error());
		$options->insertAudit($production_header_id,'production_header_id','F');
		$msg = "Transaction Finished";
		
	}else if($b=="New"){
		header("Location: admin.php?view=$view");
	}

	$query="
		select
			*
		from
			production_header as h, productmaster as p, formulation_header as f
		where
			h.stock_id = p.stock_id
		and
			h.formulation_header_id = f.formulation_header_id
		and
			production_header_id ='$production_header_id'
	";
	
	$result=mysql_query($query);
	$r=mysql_fetch_assoc($result);
	
	$production_header_id_pad	= (!empty($production_header_id))?str_pad($production_header_id,7,0,STR_PAD_LEFT):"";
	$date						= $r['date'];	
	$stock_id 					= $r['stock_id'];
	$stock						= $r['stock'];
	$stockcode					= $r['stockcode'];
	$stock_display				= (!empty($stock_id))?"$stock - $stockcode":"";
	$formulation_header_id		= $r['formulation_header_id'];
	$formulation_code			= $r['formulation_code'];
	
	$actualoutput				= $r['actualoutput'];
	
	$user_id			= $r['user_id'];
	$status				= $r['status'];
	

?>
<div class=form_layout>
	<?php if(!empty($msg)) echo '<div id="status_update" class="ui-state-highlight ui-corner-all" style="padding: 0px 0.7em; text-align:left;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right:.3em;"></span> '.$msg.'</p></div>'; ?>
	<div class="module_title"><img src='images/user_orange.png'>PRODUCTION</div>
    <form name="header_form" id="header_form" action="" method="post">
    <div class="module_actions">
        <input type="hidden" name="production_header_id" id="production_header_id" value="<?=$production_header_id?>" />
        <div id="messageError">
            <ul>
            </ul>
        </div>
        <div class='inline'>
            Date: <br />
            <input type="text" class="datepicker required textbox3" title="Please enter date"  name="date" readonly='readonly'  value="<?=$date?>">
        </div>    
        
        <div class='inline'>
			Item : <br />
            <input type="text" class="textbox" id="stock_name" value="<?=$stock_display?>" onclick="this.select();" <?php if($status){ echo "readonly='readonly'";}?> />
            <input type="hidden" name="stock_id" id="stock_id" value="<?=$stock_id?>"  />
        </div>  
        
        <div class='inline'>
			Formulation : <br />
            <input type="text" class="textbox" id="formulation_production" value="<?=$formulation_code?>" onclick="this.select();" <?php if($status){ echo "readonly='readonly'";}?> />
            <input type="hidden" name="formulation_header_id" id="formulation_header_id" value="<?=$formulation_header_id?>"  />
        </div>  
        
        <div class="inline">
        	Actual Output : <br />
            <input type="text" class="textbox3" name="actualoutput" value="<?=$actualoutput?>" />
        </div>
        
        <?php
        if(!empty($status)){
        ?>
        <br />
        <div class="inline">
        	Production # : <br />
            <input type="text" class="textbox3" name="status" id="status" value="<?=$production_header_id_pad?>" readonly="readonly"/>
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
    if(!empty($status) && $b!="Print Preview"){
    ?>
    <div class="module_actions" >
	    <div class="module_title"><img src='images/book_open.png'>PRODUCTION DETAILS:  </div>
        <table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table" id="search_table">
            <tr bgcolor="#C0C0C0">				
                <th width="20"><b>#</b></th>
                <th>Item</th>
                <th>Unit</th>
                <th>Quantity</th>
                <th>Cost</th>
                <th>Amount</th>
            </tr> 
            <?php
			$result=mysql_query("
				select
					d.stock_id,
					stock,
					sum(quantity) as quantity,
					unit,
					d.cost,
					production_detail_id
				from
					production_detail as d, productmaster as p
				where
					d.stock_id = p.stock_id
				and
					production_header_id = '$production_header_id'
				group by
					stock_id
				order by
					stock asc
			") or die(mysql_error());
			
			$i=1;
			while($r=mysql_fetch_assoc($result)){
				$stock_id 		= $r['stock_id'];
				$stock			= $r['stock'];
				$quantity		= $r['quantity'];
				$unit			= $r['unit'];
				$cost			= $r['cost'];
				$production_detail_id	= $r['production_detail_id'];
				$amount	= $quantity * $cost;
				
            ?>
            <tr>
            	<td><?=$i++?></td>
                <td><?=$stock?></td>
                <td><?=$unit?></td>
                <td class="align-right"><?=number_format($quantity,2,'.',',')?></td>
                <td class="align-right"><?=number_format($cost,2,'.',',')?></td>
                <td class="align-right"><?=number_format($amount,2,'.',',')?></td>
            </tr>
            <?php
			}
            ?>
        </table>
    </div>
    <?php
    }else if($b == "Print Preview" && $production_header_id){

		echo "	<iframe id='JOframe' name='JOframe' frameborder='0' src='print_report_rr.php?id=$production_header_id' width='100%' height='500'>
		       	</iframe>";
	}else if(empty($production_header_id)){
    ?>
    <div class="module_actions" >
	    <div class="module_title"><img src='images/book_open.png'>PRODUCTION DETAILS:  </div>
        <table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table" id="search_table">
            <tr bgcolor="#C0C0C0">				
                <th width="20"><b>#</b></th>
                <th>Item</th>
                <th>Unit</th>
                <th>Quantity</th>
                <th>Cost</th>
                <th>Amount</th>
            </tr> 
        </table>
    </div>
    
    <?php
	}
    ?>
     </form>
    
</div>
<script type="text/javascript">
j(function(){	
	j("#formulation_production").keyup(function(){
		j(this).autocomplete({
			source: "dd_formulations.php?stock_id="+document.getElementById("stock_id").value,
			minLength: 0,
			select: function(event, ui) {
				j("#formulation_production").val(ui.item.value);
				j("#formulation_header_id").val(ui.item.id);
				
				/*
				DISPLAY FORMULATION TABLE
				*/
				xajax_productionFormulationDetails(ui.item.id);
			}
		});
	});	
});

</script>
	