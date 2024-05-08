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
<script type="text/javascript" src="scripts/jquery.validate.js"></script>
<script language="JavaScript" src="scripts/calendar/calendar_us.js"></script>
<link rel="stylesheet" href="scripts/calendar/calendar.css"></link>
<script language="JavaScript" src="scripts/cwcalendar/calendar.js"></script>
<link rel="stylesheet" href="scripts/cwcalendar/cwcalendar.css"></link>
<style type="text/css">
.header-table tr td:nth-child(1){
	font-weight:bold;	
	text-align:right;
	padding-right:10px;
}

.search_table tr:hover td{
	background-color:#B5E2FE;
}
.search_table tr:nth-child(even){
	background-color:#FFFFCC;	
}
.search_table tr:nth-child(odd){
	background-color:#EEE8AA;	
}
.search_table tr:nth-child(1){
	background-color:#C0C0C0;	
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

.inline{
	display:inline-block;	
}

</style>

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
	$user_id			= $_SESSION['userID'];
	$bank				= $_REQUEST['bank'];
	$po_header_id		= $_REQUEST['po_header_id'];
	
	$trans				= $_REQUEST['trans'];
	$trans_no			= $_REQUEST['trans_no'];

	if($_REQUEST[b]=="Submit"){
		$details_display=FALSE;
		$account_id=$_REQUEST[account_id];
		
		$account_id=explode('-',$account_id);
		
		$status="S";
		$details_display=TRUE;
		/*Insert into Header*/	
		
		$datetoday=date("Y-m-d H:i:s");
		$audit="Added by: ".$options->getUserName($_SESSION[userID])."on $datetoday, ";
		
		$general_reference=$options->generateJournalReference($journal_id);
		
			
		$query="
			insert into
				gltran_header
			set
				generalreference='$general_reference',
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
				user_id='$_SESSION[userID]',
				bank = '$bank',
				po_header_id = '$po_header_id',
				trans = '$trans',
				trans_no = '$trans_no'
		";	
		mysql_query($query) or die(mysql_error());
		
		$gltran_header_id=$last_id=mysql_insert_id();
		
		$options->insertAudit($gltran_header_id,'gltran_header_id','I');		
		
	}else if($_REQUEST[b]=="Update"){
		$status="S";
		$details_display=TRUE;
		
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
				status='S',
				user_id='$_SESSION[userID]',
				bank = '$bank',
				po_header_id = '$po_header_id',
				trans = '$trans',
				trans_no = '$trans_no'
			where 
				gltran_header_id='$gltran_header_id'
		";	
		mysql_query($query) or die(mysql_error());
		
		$options->insertAudit($gltran_header_id,'gltran_header_id','U');
		
		
		$d_gltran_detail_id = $_REQUEST['d_gltran_detail_id'];
		$update_project_id 	= $_REQUEST['update_project_id'];
		
		$x = 0;
		
		if(!empty($d_gltran_detail_id)){
			foreach($d_gltran_detail_id as $id){
				
				mysql_query("
					update
						gltran_detail
					set
						project_id = '$update_project_id[$x]'
					where
						gltran_detail_id = '$id'
				") or die(mysql_error());
				
				$x++;
			}
		}
		
		
	}else if($b=="Finish"){
		
		if($options->checkGLIfBalance($gltran_header_id)){
			$status="F";
			$details_display=TRUE;
			$finish = TRUE;
				
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
					status='F',
					user_id='$_SESSION[userID]',
					bank = '$bank',
					po_header_id = '$po_header_id',
					trans = '$trans',
					trans_no = '$trans_no'
				where 
					gltran_header_id='$gltran_header_id'
			";	
			mysql_query($query) or die(mysql_error());
			$options->insertAudit($gltran_header_id,'gltran_header_id','F');
		}else{
			$status="S";
			$msg="DEBIT CREDIT NOT BALANCED.";
		}
		
	}else if($b=="Cancel"){
		mysql_query("
			update
				gltran_header
			set
				status = 'C'
			where
				gltran_header_id = '$gltran_header_id'
		") or die(mysql_error());
		
		$options->insertAudit($gltran_header_id,'gltran_header_id','C');
	}
	else if($b=="Unfinish"){
		mysql_query("
			update
				gltran_header
			set
				status = 'S'
			where
				gltran_header_id = '$gltran_header_id'
		") or die(mysql_error());
		
		$options->insertAudit($gltran_header_id,'gltran_header_id','S');
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
	$date=($r['date']!="0000-00-00")?$r['date']:"";
	$checkdate=($r['checkdate']!="0000-00-00")?$r['checkdate']:"";
	
	$xrefer= $r['xrefer'];
?>
<form name="header_form" id="header_form" action="" method="post">
<div class=form_layout>
	<div class="module_title"><img src='images/user_orange.png'>GENERAL LEDGER</div>
    <div class="module_actions">
        
        	<input type="hidden" name="gltran_header_id" id="gltran_header_id" value="<?=$r[gltran_header_id];?>" />
            <div id="messageError">
                <ul>
                </ul>
            </div>
            <div>
            	<div style="background-color:#999; color:#FFF; font-weight:bolder; padding:5px; margin-bottom:5px;">JOURNAL ENTRY</div>
            	<table style="display:inline-table;" class="header-table">
                	<?php if(!empty($status)){ ?>
                    <tr>
                    	<td> General Reference </td>
                        <td> <input type="text" class="textbox" value="<?=$r[generalreference]?>" readonly="readonly" /> </td>
                   	</tr>
                    <?php } ?>
	                <tr>
                    	<td> Journal </td>
                        <td> <?=$options->getJournalOptions($r[journal_id],'journal_id','required');?> </td>
                    </tr>    
                	<tr>
                    	<td>Date</td>
                        <td> <input type="text" class="required textbox datepicker" title="Please enter date" id='date' name="date" value="<?=$date?>" ></td>
                 	</tr>
                    <tr>
                        <td> Cross Reference </td>
                        <td> <input type="text" class="textbox" name='xrefer' value="<?=$xrefer?>" /> </td>
                    </tr> 
                    <tr>
                    	<td> Account </td>
                        <td> <?=$options->getGLAccountOptions($r[account_id]);?> </td>
                   	</tr>
                   
                    <tr>
                    	<td> Particulars </td>
                        <td> <input type="text" class="textbox2" name='particulars' value="<?=$r[particulars]?>"/> </td>
                    </tr>
                </table>
                <table style="display:inline-table;" class="header-table">
                	<!--<tr>
                        <td> Labor/Mat PO #</td>
                        <td> <input type="text" class="textbox" name='po_header_id' id="po_header_id"  value="<?=$r['po_header_id']?>"/></td>
                    </tr>-->
                	 <tr>
                        <td> Check # </td>
                        <td> <input type="text" class="textbox" name='mcheck' value="<?=$r[mcheck]?>"/></td>
                    </tr>
                    <tr> 
                        <td> Check Date </td>
                        <td> <input type="text" class="textbox datepicker" id='checkdate' name="checkdate" readonly='readonly' value="<?=$checkdate?>"  /></td>
                    </tr>
                	<!--<tr>
                        <td>Bank </td>
                        <td><input type='text' class='textbox' name='bank' value='<?=$r[bank]?>'  /> </td>
                    </tr>
                    <tr>
                    	<td>Transaction #</td>
                        <td>	
                        	<?php
							$mrr_selected 	= ( $r['trans']=="mrr" )? "selected = 'selected'" : "";
							$ris_selected 	= ( $r['trans']=="ris" )? "selected = 'selected'" : "";
							$po_selected 	= ( $r['trans']=="po" )	? "selected = 'selected'" : "";
                            ?>
                        	<select name="trans">
                            	<option value="">Select Trans:</option>
                            	<option value="mrr" <?=$mrr_selected?> >M.R.R #</option>
                                <option value="ris" <?=$ris_selected?> >R.I.S #</option>
                                <option value="po" <?=$po_selected?> >P.O #</option>
                            </select>                            
                            <input type="text" class="textbox" name="trans_no" value="<?=$r['trans_no']?>" />
                        </td>
                    </tr>-->
                </table>
            </div>
   	</div>
    <div class="module_actions">
		<?php if(empty($status)){ ?>
        <input type="submit" name="b" class="buttons" value="Submit" />
        <?php }else if(!empty($status) && $status=="S"){ ?>
        <input type="submit" name="b" class="buttons" value="Update" />
        <?php } ?> 
        <?php if($status=="S"){ ?>
        <input type="submit" name="b" class="buttons" value="Finish" />
        <?php } ?>
        <?php if($status!="C"){ ?>
        <input type="submit" name="b" class="buttons" value="Cancel"  />
        <?php } ?>
        <?php if($status == "F"){ ?>
				<input type="submit" name="b" class="buttons" value="Unfinish" />
		<?php } ?>
         <?php if($b!="Print Preview" && !empty($status)){ ?>
            <input type="submit" name="b" id="b" value="Print Preview" />
        <?php } ?>
    
        <?php if($b=="Print Preview"){ ?>	
            <input type="button" value="Print" onclick="printIframe('JOframe');" />
        <?php } ?>
 	</div>
    <?php if($status=="S"){ ?>
    <div style="background-color:#000; color:#FFF; font-weight:bolder; padding:5px;">	
        TRANSACTION ENTRY DETAILS
    </div>
    <div style="background:#fff; padding:5px;">
        <div class="inline">
            Chart of Accounts:<br />
            <?=$options->option_chart_of_accounts('','gchart_id');?>
        </div>
        <div class="inline">
            Description:<br />
            <input type="text" class="textbox" name="description" id="description"/>
        </div>
        <?php $project_select =  $options->getTableAssoc($r['project_id'],'project_id','Select Project',"select * from projects order by project_name",'project_id','project_name'); ?>
        <div class="inline">
        	Project:<br />
            <?=$project_select?>
        </div>
        <div class="inline">
            Debit:<br />
            <input type="text" class="textbox_short" name="debit"  id="debit"/>
        </div>
        <div class="inline">
            Credit:<br />
            <input type="text" class="textbox_short" name="credit" id="credit"/>
        </div>
        <input type="button" name="c" id="add" class="buttons" value="Add" />
    </div>
    <?php } ?>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <?php if($b != "Print Preview"){ ?>
    <div style="width:100%;text-align:center;" id="table_container" >
    
        <table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table" id="search_table">
            <tr bgcolor="#C0C0C0">				
              <th><b>Account</b></th>
              <th width="60"><b>Description</b></th>
              <th width="60"><b>Project</b></th>
              <th width="60"><b>Debit</b></th>
              <th width="60"><b>Credit</b></th>
            </tr>   	
        </table>
   </div>
   	<?php }else{ 
		echo "<div>";
		echo "	<iframe id='JOframe' name='JOframe' frameborder='0' src='printGeneralLedger.php?id=$gltran_header_id' width='100%' height='500'>
                    </iframe>";
		echo "</div>";
	}?>
    
   
</div>
</form>
<script type="text/javascript">
j(function(){
	j("#po_header_id").change(function(){
		xajax_checkPOAccount(xajax.getFormValues('header_form'));
	});
	
	<?php
	if($gltran_header_id){
	?>
	xajax_refreshGLTable('<?php echo $gltran_header_id?>');
	<?php
	}
	?>
	
	j("#import").click(function(){
		xajax_importTransactions(xajax.getFormValues('header_form'));
	});
	j("#add").click(function(){
		xajax_addTransaction(xajax.getFormValues('header_form'));
		toggleBox('demodiv',1);
	});
	
	j("#header_form").validate({
		errorContainer: "#messageError",
		errorLabelContainer: "#messageError ul",
		wrapper: "li"
	});
	
});
function setSelectedValue(selectObj, valueToSet) {
    for (var i = 0; i < selectObj.options.length; i++) {
        if (selectObj.options[i].value== valueToSet) {
            selectObj.options[i].selected = true;
            return;
        }
    }
}

</script>

