<script language="JavaScript" src="scripts/cwcalendar/calendar.js"></script>
 <link rel="stylesheet" href="scripts/cwcalendar/cwcalendar.css"></link>
<script type="text/javascript">
	<!--
	//var TSort_Data = new Array ('my_table', '','','','','','s','s', 's','s','s','s');
	//tsRegister();
	// -->
</script>
<?php

$_REQUEST['listing_type'] == 2;
?>
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

<form name="_form" action="" method="post">
<div class=form_layout>
	<div class="module_title"><img src='images/user_orange.png'><?=$transac->getMname($view);?></div>
    <div class="module_actions">       
    
    	<div class="inline">
            Journal<br />
            <?=$options->option_chart_of_accounts($_REQUEST[gchart_id]);?>
        </div>

        
        <div class="inline">
            Starting Date<br />
            <input type="text" class="textbox3 datepicker" id='startingdate' name="startingdate" value='<?php echo $_REQUEST[startingdate];?>' readonly='readonly' >
        </div>
        
        <div class="inline">
            Ending Date<br />
            <input type="text" class="textbox3 datepicker" id='endingdate' name="endingdate" value='<?php echo $_REQUEST[endingdate];?>' readonly='readonly' >
        </div>

		
       <!--<div class="inline">
            Options<br />	
			<select name="listing_type" required >
				<?php if($_REQUEST['listing_type'] == '1'){ ?>
				<option disabled>Choose Option</option>
				<option selected value="1">Transaction Only</option>
				<option value="2">Include Beginning Balance | Setup</option>
				<option value="3">Include Running Balance on Previous Transactions</option>				
				<option value="4">Include Beginning and Running Balance</option>					
				<?php }else if($_REQUEST['listing_type'] == '2'){ ?>
				<option disabled>Choose Option</option>
				<option value="1">Transaction Only</option>
				<option selected value="2">Include Beginning Balance | Setup</option>
				<option value="3">Include Running Balance on Previous Transactions</option>				
				<option value="4">Include Beginning and Running Balance</option>						
				<?php }else if($_REQUEST['listing_type'] == '3'){ ?>
				<option disabled>Choose Option</option>
				<option value="1">Transaction Only</option>
				<option value="2">Include Beginning Balance | Setup</option>
				<option selected value="3">Include Running Balance on Previous Transactions</option>				
				<option value="4">Include Beginning and Running Balance</option>						
				<?php }else if($_REQUEST['listing_type'] == '4'){ ?>
				<option disabled>Choose Option</option>
				<option value="1">Transaction Only</option>
				<option value="2">Include Beginning Balance | Setup</option>
				<option value="3">Include Running Balance on Previous Transactions</option>				
				<option selected value="4">Include Beginning and Running Balance</option>						
				<?php }else{ ?>
				<option selected disabled>Choose Option</option>
				<option value="1">Transaction Only</option>
				<option value="2">Include Beginning Balance | Setup</option>
				<option value="3">Include Running Balance on Previous Transactions</option>				
				<option value="4">Include Beginning and Running Balance</option>						
				<?php } ?>
			</select>
		</div>-->
		
        <div class="inline">
            Sorting Type<br />	
			<select name="sorting_type" required >
			<?php if($_REQUEST['sorting_type'] == '1'){ ?>
				<option selected value="1">Project</option>
				<option value="2">Supplier</option>	
			<?php }else if($_REQUEST['sorting_type'] == '2'){ ?>
				<option value="1">Project</option>
				<option selected value="2">Supplier</option>				
			<?php }else{ ?>
				<option selected value="1">Project</option>
				<option value="2">Supplier</option>				
			<?php } ?>
			</select>
		</div>
        
		<br />
        <div class="inline">
            Project<br />
            <?=$options->getTableAssoc($_REQUEST['project_id'],'project_id','Select Project','select * from projects order by project_name asc','project_id','project_name')?>
        </div>
        <div class="inline">
            Supplier<br />
            <?=$options->getTableAssoc($_REQUEST['account_id'],'account_id','Select Supplier','select * from supplier order by account asc','account_id','account')?>
        </div>	

   	</div>
    <div class="module_actions">
                
     	<input type="submit" value="Generate Report"  />
        <input type="button" value="Print" onclick="printIframe('JOframe');" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;" id="content">
     <?php
		//if(!empty($_REQUEST['startingdate']) && !empty($_REQUEST['endingdate']) && !empty($_REQUEST['gchart_id']))
     if((!empty($_REQUEST['startingdate']) && !empty($_REQUEST['endingdate'])) || !empty($_REQUEST['gchart_id']) || !empty($_REQUEST['project_id']))
		{
	?>
   		<iframe id="JOframe" name="JOframe" frameborder="0" src="acctg_print_gl_listings3.php?startingdate=<?=$_REQUEST['startingdate'];?>&endingdate=<?=$_REQUEST['endingdate'];?>&gchart_id=<?=$_REQUEST['gchart_id']?>&project_id=<?=$_REQUEST['project_id']?>&listing_type=2&account_id=<?=$_REQUEST['account_id']?>&sorting_type=<?=$_REQUEST['sorting_type']?>" width="100%" height="500"></iframe>
   		<!--<iframe id="JOframe" name="JOframe" frameborder="0" src="acctg_print_gl_listings.php?startingdate=<?=$_REQUEST['startingdate'];?>&endingdate=<?=$_REQUEST['endingdate'];?>&gchart_id=<?=$_REQUEST['gchart_id']?>&project_id=<?=$_REQUEST['project_id']?>&sorting_type=<?=$_REQUEST['sorting_type']?>" width="100%" height="500"></iframe>-->
    </div>
    <?php }?>
    </div>
</div>
</form>