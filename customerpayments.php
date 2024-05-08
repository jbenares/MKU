<script type="text/javascript" src="scripts/jquery.validate.js"></script>
<script language="JavaScript" src="scripts/calendar/calendar_us.js"></script>
<link rel="stylesheet" href="scripts/calendar/calendar.css"></link>
<script language="JavaScript" src="scripts/cwcalendar/calendar.js"></script>
<link rel="stylesheet" href="scripts/cwcalendar/cwcalendar.css"></link>
<style type="text/css">
.search_table tr:hover td{
	background-color:#B5E2FE;
}
label.error { float: none; display:block; color: red; padding-left: .5em; vertical-align: top; }
.alignLeft
{
	text-align:left;	
}
#messageError{
	padding:0px 5px;
}

ul{
	list-style:square;	
	margin-left:20px;
}
</style>

<?php
	$b = $_REQUEST[b];
	$status="S";
	$date=explode("/",$_REQUEST['date']);
	$date="$date[2]-$date[0]-$date[1]";		
	$pay_header_id=$_REQUEST[pay_header_id];
	
	if($_REQUEST[b]=="Finish"){
		
		$journal_id=$options->getJournalID("CR");
		$generalreference=$options->generateJournalReference($journal_id);
		$account_id="c-$_REQUEST[account_id]";
		
		$result=mysql_query("
			insert into
				gltran_header
			set
				date='$date',
				generalreference='$generalreference',
				account_id='$account_id',
				journal_id='$journal_id',
				status='S'
		") or die(mysql_error());	
		
		$last_id=mysql_insert_id();
		
		$options->insertToGLCustomerPayment($last_id,$pay_header_id);
	}
	
	if($_REQUEST[b]=="Submit"){
			
		/*Insert into Header*/	
		
		// format jobdate
		
			
		$audit="Inserted by: ".$options->getUserName($_SESSION[userID]);
			
		$query="
			insert into
				pay_header
			set
				date='$date',
				account_id='$_REQUEST[account_id]',
				reference='$_REQUEST[reference]',
				user_id='$_SESSION[userID]',
				audit='$audit',
				status='S',
				remarks='$_REQUEST[remarks]',
				locale_id='$_REQUEST[locale_id]'
		";	
		mysql_query($query) or die(mysql_error());
		
		$pay_header_id=$last_id=mysql_insert_id();	
	}if(($b=="Update") || ($b=="Finish")){
		
		$audit=$options->getAuditOfCustomerPayment($pay_header_id)." Updated by: ".$options->getUserName($_SESSION[userID]);
			
		$status=($b=="Finish")?"F":"S";
		
		$query="
			update
				pay_header
			set
				date='$date',
				account_id='$_REQUEST[account_id]',
				reference='$_REQUEST[reference]',
				user_id='$_SESSION[userID]',
				audit='$audit',
				status='$status',
				locale_id='$_REQUEST[locale_id]'
			where	
				pay_header_id='$pay_header_id'
		";	
		mysql_query($query) or die(mysql_error());
		
	}
?>

<div class=form_layout>
	<div class="module_title"><img src='images/user_orange.png'><?=$transac->getMname($view);?></div>
    <div class="module_actions">
    <form name="header_form" id="header_form" action="" method="post">
    <input type="hidden" name="pay_header_id" id="pay_header_id" value="<?=$pay_header_id?>" />
    <div id="messageError">
    	<ul>
        </ul>
    </div>
		<div class='inline'>
        	<div>Date: </div>        
            <div>
            	<input type="text" name="date" id="drdate" class="textbox3 required" readonly="readonly" value="<?=$_REQUEST['date']?>" title="Please Enter Date"/>
					<script language='JavaScript'>
                        new tcal ({
                            // form name
                            'formname': 'header_form',
                            // input name
                            'controlname': 'date'
                        });
                    </script>			
         	</div>
        </div>    	
        <div class='inline'>
        	<div>Account : </div>        
            <div>
                <?php
					echo $options->getSpecificAccountOptions($_REQUEST[account_id]);
				?> 
            </div>
        </div>   
                       
        <div class='inline'>
        	<div>Reference : </div>        
            <div>
				<input type="text" class="textbox3" name="reference" id="reference" value="<?=$_REQUEST[reference]?>" />
            </div>
        </div> 
        
        <div class='inline'>
        	<div>Location : </div>        
            <div>
				<?=$options->getAllLocationOptions($_REQUEST[locale_id])?>
            </div>
        </div> 
                
        <div>
        	<div>Remarks : </div>        
            <div>
				<input type="text" class="textbox2" name="remarks" id="remarks" value="<?=$_REQUEST[remarks]?>" />
            </div>
        </div>
         
       <!--<div class='inline'>
        	<div>Status : </div>        
            <div>
				<input type="text" readonly="readonly" value="<?=$options->getTransactionStatusName($r[status]);?>" />
            </div>
        </div> -->
        <?php
        if($b):
		?>
        <div>
        	<div>Account Balance : </div>        
            <div>
				<input type="text" class="textbox2" id="remarks" value="<?=number_format($options->getAccountBalanceForAR($_REQUEST[account_id],$date),2,'.',',');?>" readonly="readonly" />
            </div>
        </div>
        <?php
        endif;
		?>
        
        <?php if($status=="S"){ ?>
		<?php if(empty($_REQUEST[b])){ ?>
       	<input type="submit" name="b" id="b" value="Submit" />
        <?php }else{ ?>        
        <input type="submit" name="b" id="b" value="Update" />
        <input type="submit" name="b" value="Finish" />
        <?php } ?>
            
        <br />
        <?php
		if(!empty($_REQUEST[b])):
		?>
        <hr />
                   
        <div style="display:inline-block; margin-right:20px;">
        	<div>Delivery : </div>        
            <div id="dr_div"><?php echo $options->getDeliveryOptions(); ?></div>
        </div>  
        <div style="display:inline-block; margin-right:20px;">
        	<div>Amount : </div>        
            <div><input type="text" size="12" name="amount" id="amount"/></div>
        </div> 
  		
        <input type="button" name="addButton" id="addButton" value="Add"  />
        
        <hr />
        
                   
        <div style="display:inline-block; margin-right:20px;">
        	<div>Bank : </div>        
            <div><input type="text" name="bank" id="bank" class="textbox3" /></div>
        </div>  
        
        <div style="display:inline-block; margin-right:20px;">
        	<div>Check # : </div>        
            <div><input type="text" name="checkno" id="checkno" class="textbox3" /></div>
        </div>  
        
        <div style="display:inline-block; margin-right:20px;">
        	<div>Date: </div>        
            <div>
            	<input type="text" name="datecheck" id="datecheck" class="textbox3" readonly="readonly"/>
					<script language='JavaScript'>
                        new tcal ({
                            // form name
                            'formname': 'header_form',
                            // input name
                            'controlname': 'datecheck'
                        });
                    </script>			
         	</div>
        </div>    	
        
        <div style="display:inline-block; margin-right:20px;">
        	<div>Check Amount : </div>        
            <div><input type="text" name="checkamount" id="checkamount" class="textbox3" /></div>
        </div> 
        
        <div style="display:inline-block; margin-right:20px;">
        	<div>Check Status : </div>        
            <div><?=$options->getCheckStatusOptions()?></div>
        </div> 
  		
        <input type="button" name="addCheckButton" id="addCheckButton" value="Add Check"  />
        
        <?php
		endif;
		?>
        <?php } ?>
    </form>
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="float:left; z-index:10000; width:100%; text-align:center;" id="table_container" >
        <table cellspacing="2" cellpadding="5" width="100%"  class="search_table" id="search_table" style="float:left; width:50%;">
            <tr bgcolor="#C0C0C0">				
              <th width="20"><b>#</b></th>
              <th width="20" align="center"></th>
              <th><b>DR #</b></th>
              <th><b>Amount</b></th>
            </tr>   	
        </table>
        
        <table cellspacing="2" cellpadding="5" width="100%"  class="search_table" id="search_table" style="float:right; width:50%;">
            <tr bgcolor="#C0C0C0">				
              <th width="20"><b>#</b></th>
              <th width="20" align="center"></th>
              <th><b>Bank</b></th>
              <th><b>Check #</b></th>
              <th><b>Check Date</b></th>
              <th><b>Check Amount</b></th>
              <th><b>Check Status</b></th>
            </tr>   	
        </table>
    </div>
   
</div>


<script type="text/javascript" src="scripts/script_customerpayment.js"></script>
<?php if($b){ ?>
<script type="text/javascript">
j(function(){
	updateCustomerPaymentTable();
});
</script>
<?php } ?>

