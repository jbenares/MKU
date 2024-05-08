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
	$b								= $_REQUEST['b'];
	$user_id						= $_SESSION['userID'];
	$checkList						= $_REQUEST['checkList'];
	
	
	/*
		HEADERS
	*/
	$financial_budget_header_id		= $_REQUEST['financial_budget_header_id'];
	$project_id						= $_REQUEST['project_id'];
	
	/*
		DETAILS
	*/
	
	$gchart_id	= $_REQUEST['gchart_id'];
	$amount		= $_REQUEST['amount'];
	
	/*
		UPDATES	
	*/
	$update_gchart_id			= $_REQUEST['update_gchart_id'];
	$update_amount				= $_REQUEST['update_amount'];
	$financial_budget_detail_id	= $_REQUEST['financial_budget_detail_id'];
	
	
	if($b=="Submit"){
		
		if($options->hasFinancialBudget($project_id)){
			
			$id = $options->getFinancialBudgetId($project_id);
			
			$msg = "Financial Budget is already avaible. Click <a  style='color:#F00;' href='admin.php?view=2e6ffd8f1c122408e585&financial_budget_header_id=$id'>HERE</a> to proceed to that budget.";			
		}else{
			$query="
				insert into 
					financial_budget_header
				set
					project_id = '$project_id',
					user_id='$user_id',
					status='S'
			";	
			
			mysql_query($query) or die(mysql_error());
			
			$financial_budget_header_id = mysql_insert_id();
			$options->insertAudit($financial_budget_header_id,'financial_budget_header_id','I');
			
			$msg="Transaction Saved";
		}
		
	}else if($b=="Update"){
		$query="
			update
				financial_budget_header
			set
				project_id = '$project_id',
				user_id='$user_id',
				status='S'
			where
				financial_budget_header_id='$financial_budget_header_id'
		";	
		
		mysql_query($query) or die(mysql_error());
		$options->insertAudit($financial_budget_header_id,'financial_budget_header_id','U');		
		
		$msg = "Transaction Updated";
		
	}else if($b=="Cancel"){
		$query="
			update
				financial_budget_header
			set
				status='C'
			where
				financial_budget_header_id='$financial_budget_header_id'
		";	
		mysql_query($query) or die(mysql_error());
		$options->insertAudit($financial_budget_header_id,'financial_budget_header_id','C');
		
		$msg = "Transaction Cancelled";
		
	}else if($b=="Finish"){
		$query="
			update
				financial_budget_header
			set
				status='F'
			where
				financial_budget_header_id='$financial_budget_header_id'
		";	
		
		
	}else if($b=="Update Details"){	
		
		$x=0;
		
		foreach($financial_budget_detail_id as $id):
			
			mysql_query("
				update
					financial_budget_detail
				set
					amount = '$update_amount[$x]'
				where
					financial_budget_detail_id = '$id'
			") or die(mysql_error());
			$x++;

		endforeach;	
		
		$msg = "Transaction Details Updated";
	}else if($b == "Add"){
		mysql_query("
			insert into
				financial_budget_detail
			set
				financial_budget_header_id	= '$financial_budget_header_id',
				gchart_id					= '$gchart_id',
				amount						= '$amount'
				
		") or die(mysql_error());
	
	}else if($b=="Delete"){
		if(!empty($checkList)){
			foreach($checkList as $id){

				mysql_query("
					delete from
						financial_budget_detail
					where	
						financial_budget_detail_id = '$id'
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
			financial_budget_header as h, projects as p
		where
			h.project_id = p.project_id
		and
			financial_budget_header_id ='$financial_budget_header_id'
	";
	
	$result=mysql_query($query);
	$r=mysql_fetch_assoc($result);
	
	
	$financial_budget_header_id		= $r['financial_budget_header_id'];
	$financial_budget_header_id_pad	= (!empty($financial_budget_header_id))?str_pad($financial_budget_header_id,7,0,STR_PAD_LEFT):"";
	
	$project_id 	 				= $r['project_id'];
	$project_name		= $options->attr_Project($project_id,'project_name');
	$project_code		= $options->attr_Project($project_id,'project_code');
	$project_display	= ($project_id)?"$project_name - $project_code":"";
	
	
	$user_id			= $r['user_id'];
	$status				= $r['status'];
	

?>
<form name="header_form" id="header_form" action="" method="post">
<div class=form_layout>
	<?php if(!empty($msg)) echo '<div id="status_update" class="ui-state-highlight ui-corner-all" style="padding: 0px 0.7em; text-align:left;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right:.3em;"></span> '.$msg.'</p></div>'; ?>
	<div class="module_title"><img src='images/user_orange.png'>FINANCIAL BUDGET</div>
    
    <div style="width:50%; float:left;">
        <div class="module_actions">
            <input type="hidden" name="financial_budget_header_id" id="financial_budget_header_id" value="<?=$financial_budget_header_id?>" />
            <input type="hidden" name="view" value="<?=$view?>" />
            <div id="messageError">
                <ul>
                </ul>
            </div>
           
            <div class="inline">
                Project : <br />
                <input type="text" class="textbox" id="project_name"  value="<?=$project_display?>" />
                <input type="hidden" name="project_id" id="project_id" value="<?=$project_id?>" />
            </div>
            
            
            <?php
            if(!empty($status)){
            ?>
            <br />
            <div class="inline">
                Finanacial Budget # : <br />
                <input type="text" class="textbox3" name="status" id="status" value="<?=$financial_budget_header_id_pad?>" readonly="readonly"/>
            </div>
            
            <div class='inline'>
                <div>Status : </div>        
                <div>
                    <input type="text" class="textbox3" name="status" id="status" value="<?=$options->getTransactionStatusName($status)?>" readonly="readonly"/>
                </div>
            </div> 
            <br />
            
            <div class='inline'>
                <div>Encoded by : </div>        
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
        	<div class="inline">
                Account : <br />
                <?=$options->option_chart_of_accounts()?>
           	</div>
            <div class="inline">
            	Amount : <br />
                <input type="text" class="textbox3" name="amount" autocomplete="off" />
            </div>
            <input type="submit" name="b" value="Add"  />
        </div>
        <?php
        }
        ?> 
    </div>
    <div style="width:50%; float:right;">
        <div class="module_title"><img src='images/book_open.png'>FINANCIAL BUDGET DETAILS:  </div>
        <div class="module_actions">
            <?php
            if($status=="S"){
            ?>
            <input type="submit" name="b" value="Delete" onclick="return approve_confirm();"/>
            <input type="submit" name="b" value="Update Details" onclick="return approve_confirm();"/>
            <?php
            }
            ?>
        </div>
        <table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table" id="search_table">
            <tr bgcolor="#C0C0C0">				
                <th width="20"><b>#</b></th>
                <th width="20"><input type="checkbox"  name="checkAll" onclick="javascript:check_all('header_form', this)" title="Check/Uncheck All" /></th>
                <th>Account</th>
                <th width="100">Amount</th>
            </tr> 
            <?php
            $result=mysql_query("
                select
                    *
                from
                    financial_budget_detail as d, gchart as g
                where
					d.gchart_id	= g.gchart_id
                and
                    financial_budget_header_id = '$financial_budget_header_id'
            ") or die(mysql_error());
            
            $i=1;
            while($r=mysql_fetch_assoc($result)){
                $financial_budget_detail_id 	= $r['financial_budget_detail_id'];
				$gchart							= $r['gchart'];
				$amount							= $r['amount'];
            ?>
            <tr>
                <td><?=$i++?></td>
                <td><input type="checkbox" name="checkList[]" value="<?=$financial_budget_detail_id?>" onclick="document.header_form.checkAll.checked=false"></td>
                <td><?=$gchart?></td>
                <td><input type="text" class="textbox3" name="update_amount[]" value="<?=$amount?>"  /></td>
                <input type="hidden" name="financial_budget_detail_id[]" value="<?=$financial_budget_detail_id?>"  />
            </tr>
            <?php
            }
            ?>
        </table>
   	</div>
    <div style="clear:both;">
		<?php
        if($b == "Print Preview" && $financial_budget_header_id){
    
            echo "	<iframe id='JOframe' name='JOframe' frameborder='0' src='print_report_rr.php?id=$financial_budget_header_id' width='100%' height='500'>
                    </iframe>";
        }
        ?>
   	</div>
     
    
</div>
</form>
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
});

</script>
	