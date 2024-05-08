<?php
	$b					= $_REQUEST['b'];
	
	$gltran_header_id	= $_REQUEST['gltran_header_id'];
	$date				= $_REQUEST['date'];
	$checkdate			= $_REQUEST['checkdate'];
	$xrefer				= $_REQUEST['xrefer'];
	$particulars 		= $_REQUEST['particulars'];
	$journal_id			= $_REQUEST['journal_id'];
	$details			= $_REQUEST['details'];
	$account			= $_REQUEST['account'];
	$address			= $_REQUEST['address'];
	$account_id 		= $_REQUEST['account_id'];
	$mcheck				= $_REQUEST['mcheck'];
	$admin_id			= $_SESSION['userID'];

	if($_REQUEST[b]=="Submit"){
		$audit=$options->getAuditFromGLTransac($gltran_header_id);	
			
		$datetoday=date("Y-m-d H:i:s");
		$audit.="Edit by: ".$options->getUserName($_SESSION[userID])."on $datetoday, ";
			
		$query="
			update
				gltran_header
			set
				xrefer='$_REQUEST[xrefer]',
				date='$_REQUEST[date]',
				particulars='$_REQUEST[particulars]',
				journal_id='$_REQUEST[journal_id]',
				details='$_REQUEST[details]',
				account='$_REQUEST[account]',
				address='$_REQUEST[address]',
				account_id='$_REQUEST[account_id]',
				checkdate='$_REQUEST[checkdate]',
				mcheck='$_REQUEST[mcheck]',
				audit='$audit',
				status='S',
				admin_id='$_SESSION[userID]'
			where 
				gltran_header_id='$gltran_header_id'
		";	
		mysql_query($query) or die(mysql_error());
		
	}else if($_REQUEST[b]=="Update"){
	
		$gltran_detail_id=$_REQUEST[d_gltran_detail_id];
		$gchart_id=$_REQUEST[d_gchart_id];
		$description=$_REQUEST[d_description];
		$debit=$_REQUEST[d_debit];
		$credit=$_REQUEST[d_credit];
		$enable=$_REQUEST[d_enable];
		
		$x=0;
		if($gltran_detail_id){
			foreach($gltran_detail_id as $key){
				
				$query="
					update
						gltran_detail
					set
						description='$description[$x]',
						debit='$debit[$x]',
						credit='$credit[$x]',
						gchart_id='$gchart_id[$x]'
					where
						gltran_detail_id='$key'
				";
				mysql_query($query);
				
				$x++;
			}
		}
	}else if($b=="Finish"){
		
		if($options->checkGLIfBalance($gltran_header_id)){
			$status="F";
			$details_display=TRUE;
			$finish = TRUE;
			
			$audit=$options->getAuditFromGLTransac($gltran_header_id);	
				
			$datetoday=date("Y-m-d H:i:s");
			$audit.="Edit by: ".$options->getUserName($_SESSION[userID])."on $datetoday, ";
				
			$query="
				update
					gltran_header
				set
					xrefer='$_REQUEST[xrefer]',
					date='$_REQUEST[date]',
					particulars='$_REQUEST[particulars]',
					journal_id='$_REQUEST[journal_id]',
					details='$_REQUEST[details]',
					account='$_REQUEST[account]',
					address='$_REQUEST[address]',
					account_id='$_REQUEST[account_id]',
					checkdate='$_REQUEST[checkdate]',
					mcheck='$_REQUEST[mcheck]',
					audit='$audit',
					status='F',
					admin_id='$_SESSION[userID]'
				where 
					gltran_header_id='$gltran_header_id'
			";	
			mysql_query($query) or die(mysql_error());
		}else{
			$msg="DEBIT CREDIT NOT BALANCED.";
		}
		
	}
	
	
	$query="
		select
			*
		from
			gltran_header
		where
			gltran_header_id='$gltran_header_id'
	";
	
	$result=mysql_query($query) or die(mysql_error());
	$r=mysql_fetch_assoc($result);
	$status=$r[status];
	$date=$r['date'];
?>
<form name="header_form" id="header_form" action="" method="post">
<div class=form_layout>
	<div class="module_title"><img src='images/user_orange.png'>GENERAL LEDGER EDIT</div>
    <div class="module_actions">
        
        	<input type="hidden" name="gltran_header_id" id="gltran_header_id" value="<?=$r[gltran_header_id];?>" />
            <div id="messageError">
                <ul>
                </ul>
            </div>
            <div>
            	<div style="background-color:#999; color:#FFF; font-weight:bolder; padding:5px; margin-bottom:5px;">JOURNAL ENTRY</div>
            	<table class="header-table">
                	<tr>
                    	<td>
                        	Journal
                        </td>
                        <td>
                        	<?=$options->getJournalOptions($r[journal_id]);?>
                        </td>
                        <td>
                        	Cross Reference
                        </td>
                        <td>
                        	<input type="text" class="textbox3" name='xrefer' value="<?=$r[xrefer]?>" />
                        </td>
                    </tr>
                    <tr>
                    	<td>
                        	Account
                        </td>
                        <td>
                        	<input type="text" class="textbox2" name="account" value="<?=$r[account]?>" />
                        <!--
                            <?=$options->getGChartOptions($r[account],'account');?>
                        -->
                        <td>
                        	Date
                        </td>
                        <td>
                        	<input type="text" class="required textbox3" title="Please enter date" id='date' name="date" onclick=fPopCalendar("date"); readonly='readonly'  value="<?=$date?>">
                        </td>
                    </tr>
                    
                    <tr>
                    
                    	<td>
                        	Account ID
                        </td>
                        <td>
                        	<?=$options->getGLAccountOptions($r[account_id]);?>
                        </td>
                        
                        <td>
                        	Check #
                        </td>
                        <td>
                        	<input type="text" class="textbox4" name='mcheck' value="<?=$r[mcheck]?>"/>
                        </td>
                    	
                        
                    </tr>
                    <tr>
                        <td>
                        	Address
                        </td>
                        <td>
                        	<input type="text" class="textbox2" name='address' value="<?=$r[address]?>"/>
                        </td>
                        
                        <td>
                        	Check Date
                        </td>
                        <td>
                        	<input type="text" class="textbox3" id='checkdate' name="checkdate" onclick=fPopCalendar("checkdate"); readonly='readonly' value="<?=$checkdate?>" >	
                        </td>
                        
                    </tr>
                    <tr>
                    	<td>
                        	Particulars
                        </td>
                        <td>
                        	<input type="text" class="textbox2" name='particulars' value="<?=$r[particulars]?>"/>
                        </td>
                        
                        <td>
                        	Details
                        </td>
                        <td>
                        	<input type="text" class="textbox4" name='details' value='<?=$r[details]?>'/>
                        </td>    
                    </tr>
                    <tr>
                    	<td>
                        	General Reference
                        </td>
                        <td>
                        	<input type="text" class="textbox4" value="<?=$r[generalreference]?>" readonly="readonly" />
                        </td>
                    </tr>
                </table>
            
            </div>
           <?php if($status=="S"){ ?>
	        <input type="submit" name="b" class="buttons" value="Submit" />
            <input type="submit" name="b" class="buttons" value="Finish" />
			
            <div style="background-color:#000; color:#FFF; font-weight:bolder; padding:5px; margin:5px 0px;">	
                TRANSACTION ENTRY DETAILS
            </div>
            <div style="background:#fff;">
                <div class="inline">
                    Chart of Accounts:<br />
                    <?=$options->getGchartOptions();?>
                </div>
                <div class="inline">
                    Description:<br />
                    <input type="text" class="textbox4" name="description" id="description"/>
                </div>
                <div class="inline">
                    Debit:<br />
                    <input type="text" class="textbox3" name="debit"  id="debit"/>
                </div>
                <div class="inline">
                    Credit:<br />
                    <input type="text" class="textbox3" name="credit" id="credit"/>
                </div>
                <!--<div class="inline">
                    Enable:<br />
                    <input type="checkbox" checked="checked" name="enable" id="enable" value="Yes"/>
                </div>-->
                <input type="button" name="c" id="add" class="buttons" value="Add" />
            </div>
        
            <div><!--Start of Import Details-->
                <div style="background-color:#000; color:#FFF; font-weight:bolder; padding:5px; margin:5px 0px;">	
                    IMPORT 
                </div>
                <div style="background:#fff;">
                    <div class="inline">
                        From Date:<br />
                        <input type="text" class="textbox3" id='fromdate' name="fromdate" onclick=fPopCalendar("fromdate"); readonly='readonly' >
                    </div>
                     <div class="inline">
                        To Date:<br />
                        <input type="text" class="textbox3" id='todate' name="todate" onclick=fPopCalendar("todate"); readonly='readonly' >
                    </div>
                    <input type="button" name="c" id="import" class="buttons" value="Import" />
                    <input type="submit" name="b" value="Update" />
                </div>
            </div><!--End of Import Details-->
            <?php }else if($status == "F"){ ?>
				<input type="submit" name="b" class="buttons" value="Unfinish" />
			<?php } ?>
    	
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="float:left; z-index:10000; width:100%; text-align:center;" id="table_container" >
        <table cellspacing="2" cellpadding="5" width="100%" align="center" class="search_table" id="search_table">
            <tr bgcolor="#C0C0C0">				
              <th width="20"><b>#</b></th>
              <th width="20" align="center"></th>
              <th><b>GChart</b></th>
              <th><b>Description</b></th>
              <th><b>Debit</b></th>
              <th><b>Credit</b></th>
              <th><b>Enable</b></th>
            </tr>   	
        </table>
    </div>
   
</div>
</form>
<script type="text/javascript">
j(function(){
	xajax_refreshGLTable('<?php echo $gltran_header_id?>');
	
	j("#import").click(function(){
		xajax_importTransactions(xajax.getFormValues('header_form'));
	});
	j("#add").click(function(){
		xajax_addTransaction(xajax.getFormValues('header_form'));
		toggleBox('demodiv',1);
	});
});
</script>

