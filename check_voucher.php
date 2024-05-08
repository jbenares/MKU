<?php

if( $_REQUEST['ajax'] ){
	require_once(dirname(__FILE__).'/library/lib.php');
	require_once(dirname(__FILE__).'/conf/ucs.conf.php');


	if( $_REQUEST['print'] ){
		$id = $_REQUEST['id'];

		$is_printed = lib::getAttribute("cv_header",'cv_header_id',$id,'printed');
        if( $is_printed ){
            echo 0; #DO NOT PRINT
            exit();
        } else {
            #update database after
            mysql_query("update cv_header set printed = '0' where cv_header_id = '$id'") or die(mysql_error());
            echo 1; #print
            exit();
        }
	}

	exit();
}



function getGLLink($cv_header_id) {
	$result = mysql_query("
		select * from gltran_header where header = 'cv_header_id'  and header_id = '$cv_header_id' and status != 'C' order by gltran_header_id desc
	") or die(mysql_error());
	
	$r = mysql_fetch_assoc($result);
	return $r['gltran_header_id'];
}

/*
function getNextCV(){
	$result = mysql_query("
		select * from cv_header where status != 'C' order by cv_no desc
	") or die(mysql_error());
	$r = mysql_fetch_assoc($result);
	return ($r['cv_no'] + 1);
}*/

?>
<style type="text/css">
.input-table tr td:nth-child(1),.input-table tr td:nth-child(3){	
	text-align:right; font-weight:bold;
}
</style>

<script type="text/javascript">
function printIframe(id)
{
	var iframe = document.frames ? document.frames[id] : document.getElementById(id);    
	var ifWin = iframe.contentWindow || iframe;
	iframe.focus();

	jQuery.post("check_voucher.php", { ajax : 1 , print : 1, id : '<?=$_REQUEST['cv_header_id']?>' }, function(data){
	    //actions
	    if( data == 1 ){            
            ifWin.printPage();            
            jQuery("#printed_status").html("PRINTED");
            jQuery("#printed_status").css('color',"#F00");
        } else{
            alert("Unable to Print Check Voucher, Voucher already Printed");
        }
        return false;
	});

   /* var iframe = document.frames ? document.frames[id] : document.getElementById(id);
    var ifWin = iframe.contentWindow || iframe;
    iframe.focus();
    ifWin.printPage();
    return false;*/
}
</script>
<?php
	$b						= $_REQUEST['b'];
	
	$update_project_id 		= $_REQUEST['update_project_id'];
	$update_cv_detail_id	= $_REQUEST['update_cv_detail_id'];
	$update_gchart_id		= $_REQUEST['update_gchart_id'];
	$update_amount			= $_REQUEST['update_amount'];
	
	#HEADER
	
	$cv_header_id			= $_REQUEST['cv_header_id'];
	$percent		= $_REQUEST['percent'];
	$cv_date		= $_REQUEST['cv_date'];
	$check_date		= $_REQUEST['check_date'];
	$check_no		= $_REQUEST['check_no'];
	$supplier_id	= $_REQUEST['supplier_id'];
	$cash_gchart_id	= $_REQUEST['cash_gchart_id'];
	$ap_gchart_id	= $_REQUEST['ap_gchart_id'];
	$particulars	= $_REQUEST['particulars'];
	$project_id		= $_REQUEST['project_id'];
	$sub_apv_header_id	= $_REQUEST['sub_apv_header_id'];
	
	$wtax	= $_REQUEST['wtax'];
	$vat	= $_REQUEST['vat'];
	$wtax_gchart_id = $_REQUEST['wtax_gchart_id'];
	$vat_gchart_id	= $_REQUEST['vat_gchart_id'];
	
	$retention_gchart_id	= $_REQUEST['retention_gchart_id'];
	$retention_project_id	= $_REQUEST['retention_project_id'];
	
	$chargable_gchart_id	= $_REQUEST['chargable_gchart_id'];
	$retention_amount		= $_REQUEST['retention_amount'];
	$chargable_amount		= $_REQUEST['chargable_amount'];
	
	$rmy_gchart_id			= $_REQUEST['rmy_gchart_id'];
	$rmy_amount				= $_REQUEST['rmy_amount'];
	
	$cv_no = $_REQUEST['cv_no'];

	$bdo	= ($_REQUEST['bdo']) ? 1 : 0;
	
	#OTHERS
	$user_id			= $_SESSION['userID'];	
	$checkList			= $_REQUEST['checkList'];
	
	function updateCashAmount($cv_header_id){
		$options = new options();
		$cash_amount = round($options->getCashAmount($cv_header_id),2);
		mysql_query("
			update
				cv_header
			set
				cash_amount = '$cash_amount'
			where
				cv_header_id = '$cv_header_id'
		") or die(mysql_error());	
	}
	
	if($b == "Update Details"){
		if(!empty($update_cv_detail_id)){
			$x = 0;
			foreach($update_cv_detail_id as $_id){
				mysql_query("
					update 
						cv_detail 
					set 
						project_id = '$update_project_id[$x]' ,
						gchart_id = '$update_gchart_id[$x]',
						amount = '$update_amount[$x]'
					where 
						cv_detail_id = '$_id'
				") or die(mysql_error());
				$x++;	
			}
			
			$msg = "Transaction Details Updated SUCCESSFULLY";
		}
		
	}
	
	if($b == "Unfinish"){
		#CHANGE STATUS TO SAVED
		mysql_query("	
			update cv_header set status = 'S' where cv_header_id = '$cv_header_id'
		") or die(mysql_error());	
		
		#CHANGE STATUS OF GENERATED GL TO CANCELLED
		mysql_query("
			update gltran_header set status = 'C' where header = 'cv_header_id' and header_id = '$cv_header_id'
		") or die(mysql_error());		
		
		$msg = "Transaction unfinished";
	}
	
	if($b=="Update"){
		
		$vatable = ($vatable == "on")?1:0;
		
		$query="
			update
				cv_header
			set
				cv_date = '$cv_date',
				check_no = '$check_no',
				check_date = '$check_date',
				cash_gchart_id = '$cash_gchart_id',
				ap_gchart_id = '$ap_gchart_id',
				wtax = '$wtax',
				vat = '$vat',
				wtax_gchart_id = '$wtax_gchart_id',
				vat_gchart_id = '$vat_gchart_id',
				particulars = '$particulars',
				cv_no = '$cv_no',
				retention_gchart_id = '$retention_gchart_id',
				chargable_gchart_id = '$chargable_gchart_id',
				retention_amount = '$retention_amount',
				chargable_amount = '$chargable_amount',
				sub_apv_header_id	= '$sub_apv_header_id',
				rmy_gchart_id = '$rmy_gchart_id',
				rmy_amount  = '$rmy_amount',
				retention_project_id = '$retention_project_id',
                printing_type = '$_REQUEST[printing_type]'
			";

		if( !empty($_REQUEST['first_pdc_date']) && !empty($_REQUEST['no_of_payments'])){
		$query .= "
				,first_pdc_date = '$_REQUEST[first_pdc_date]',
				no_of_payments = '$_REQUEST[no_of_payments]'
		";
		}

		$query .= "
			where
				cv_header_id='$cv_header_id'
		";	
		
		mysql_query($query) or die(mysql_error());
		$options->insertAudit($cv_header_id,'cv_header_id','U');		
		
		$msg = "Transaction Updated";
		
		updateCashAmount($cv_header_id);
		
	}else if($b=="Cancel"){
		$query="
			update
				cv_header
			set
				status='C'
			where
				cv_header_id='$cv_header_id'
		";	
		mysql_query($query) or die(mysql_error());
		$options->insertAudit($cv_header_id,'cv_header_id','C');
		
		mysql_query("
			update gltran_header set status = 'C' where header = 'cv_header_id' and header_id = '$cv_header_id'
		") or die(mysql_error());
		
		$msg = "Transaction Cancelled";
		
	}else if($b == "Finish"){
		$query="
			update
				cv_header
			set
				status='F'
			where
				cv_header_id='$cv_header_id'
		";	
		mysql_query($query) or die(mysql_error());
		$options->insertAudit($cv_header_id,'cv_header_id','F');
		
		
		if($options->checkGLEntry($cv_header_id,"cv_header_id") == 0){
			$gltran_header_id  = $options->postCV($cv_header_id);
			$msg = "Transaction Finished and Posted. Click <a href='admin.php?view=1da21dd42f2e46c2d13e&gltran_header_id=$gltran_header_id'>me</a> to Preview";
		}
		
		updateCashAmount($cv_header_id);
	}

	$query="
		select
			*
		from
			cv_header
		where
			cv_header_id ='$cv_header_id'
	";
	
	$result=mysql_query($query);
	$r = $aVal = mysql_fetch_assoc($result);
	
	$cv_header_id			= $r['cv_header_id'];
	$cv_header_id_pad		= str_pad($cv_header_id,7,0,STR_PAD_LEFT);
	$percent		= $r['percent'];
	$cv_date		= $r['cv_date'];
	$check_date		= $r['check_date'];
	$check_no		= $r['check_no'];
	$supplier_id	= $r['supplier_id'];
	$supplier 		= $options->getAttribute('supplier','account_id',$supplier_id,'account');
	$cash_gchart_id	= $r['cash_gchart_id'];
	$ap_gchart_id	= $r['ap_gchart_id'];
	$status 		= $r['status'];
	$user_id		= $r['user_id'];
	$type			= $r['type'];
	$particulars  	= $r['particulars'];
	$sub_apv_header_id	= $r['sub_apv_header_id'];
	
	$wtax 			= $r['wtax'];
	$vat			= $r['vat'];
	$wtax_gchart_id	= $r['wtax_gchart_id'];
	$vat_gchart_id	= $r['vat_gchart_id'];
	$cv_no			= $r['cv_no'];
	
	$project_id			= $r['project_id'];
	$project_name		= $options->attr_Project($project_id,'project_name');
	$project_code		= $options->attr_Project($project_id,'project_code');
	$project_name_code	= ($project_id)?"$project_name - $project_code":"";
	
	$retention_project_id	= $r['retention_project_id'];
	$retention_gchart_id	= $r['retention_gchart_id'];
	$chargable_gchart_id	= $r['chargable_gchart_id'];
	$retention_amount		= $r['retention_amount'];
	$chargable_amount		= $r['chargable_amount'];
	
	$rmy_gchart_id			= $r['rmy_gchart_id'];
	$rmy_amount				= $r['rmy_amount'];
	
	
?>
<form name="header_form" id="header_form" action="" method="post">
<div class=form_layout>
	<?php if(!empty($msg)) echo '<div id="status_update" class="ui-state-highlight ui-corner-all" style="padding: 0px 0.7em; text-align:left;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right:.3em;"></span> '.$msg.'</p></div>'; ?>
	<div class="module_title"><img src='images/user_orange.png'>CHECK VOUCHER</div>
    
    <div style="width:100%;">
        <div class="module_actions" style="position:relative;">
            <input type="hidden" name="cv_header_id" id="cv_header_id" value="<?=$cv_header_id?>" />
            <input type="hidden" name="view" value="<?=$view?>" />
            
            <div id="messageError">
                <ul>
                </ul>
            </div> 
            
            
            <table style="display:inline-table;" class="input-table">
            	<tr>
                	<td>Supplier :</td>
                    <td><input type="text" class="textbox" id="supplier_name" value="<?=$supplier?>" readonly="readonly" /></td>
               	</tr>
                <tr>
                    <td>Voucher Date:</td>
                    <td><input type="text" class="datepicker required textbox" title="Please enter date"  name="cv_date" readonly='readonly'  value="<?=$cv_date?>"> </td>
               	</tr>
                <tr>
                    <td>Check No :</td>
					<td><input type="text" class="textbox" value="<?=$check_no?>" name="check_no"/> </td>
               	</tr>
                <tr>
                    <td> Check Date : </td>
                    <td><input type="text" class="textbox datepicker" name="check_date" value="<?=$check_date?>" readonly="readonly"/></td>
               	</tr>
                <tr>
                    <td> CV # : </td>
                    <td><input type="text" class="textbox" name="cv_no" value="<?=$cv_no?>" /></td>
                </tr>           
                <?php if($status == "F"){ ?>
                <tr>
                    <td> Link to GL : </td>
                    <td><a target="_blank" href="admin.php?view=1da21dd42f2e46c2d13e&gltran_header_id=<?=getGLLink($cv_header_id)?>">Click Me to go to GL</a></td>
               	</tr>
                <?php }  ?>
            </table>
            <table style="display:inline-table;" class="input-table">
            	<tr>
                	<td>Witholding Tax Payable Account : </td>
                    <td><?=$options->option_chart_of_accounts($wtax_gchart_id,'wtax_gchart_id')?></td>
                	<td>Witholding Tax (%)</td>
                   	<td><input type="text" class="textbox3" name="wtax" value="<?=$wtax?>" /></td>
                </tr>
                <tr>
                	<td>Vat Account : </td>
                    <td><?=$options->option_chart_of_accounts($vat_gchart_id,'vat_gchart_id')?></td>
                    <td>Vat (%) :</td>
                    <td><input type="text" class="textbox3" name="vat" value="<?=$vat?>" /></td>
                </tr>
                <tr>
                	<td>Cash/PDC Account :</td>
                    <td><?=$options->option_chart_of_accounts($cash_gchart_id,'cash_gchart_id')?></td>
                </tr>
               
                <tr>
                	<td>A/P Account : </td>
					<td><?=$options->option_chart_of_accounts($ap_gchart_id,'ap_gchart_id')?></td>
                </tr>            
    
            </table>
            
            <div>
                Particulars<br />
                <input type="text" class="textbox2" name="particulars" value="<?=$particulars?>"  />
                
                <div style="margin-top:10px;">
                	<?php
					$checked_bdo = ($bdo) ? "checked='checked'" : "";
                    ?>
                	<input type="checkbox" name="bdo" value="1" <?=$checked_bdo?> />	Check to display other references in print out
                </div>
           	</div>

            <!--<div class='inline'>
                <div>% Payment: </div>        
                <div>
                    <input type="text" class="textbox3" value="<?=$percent?>" readonly="readonly"/>
                </div>
            </div>   --> 	
    	</div>
    	<div style="background-color:#000; padding:5px; color:#FFF; font-weight:bold;">
    		PDC MODULE
    	</div>
    	<div class="module_actions" style="background-color:#FFF;">

    		<div style="display:inline-block;">
    			First PDC Date: <br>
    			<input type='text' class='textbox datepicker' name='first_pdc_date' value='<?=$aVal['first_pdc_date']?>' readonly >
    		</div>

    		<div style="display:inline-block;">
    			No of Payments: <br>
    			<input type='text' class='textbox3' name='no_of_payments' value='<?=$aVal['no_of_payments']?>' >
    		</div>
            <div style="display:inline-block;">
    			Printing Type: <br>
    			<?=$options->getBANK($aVal['printing_type']);?>
    		</div>
    	</div>
        <?php if(!empty($status)){ ?>
        <div class="module_actions" style="background-color:#CCC;"> 
            
            <div class="inline">
                Status :
                <input type="text" class="textbox3" name="status" id="status" value="<?=$options->getTransactionStatusName($status)?>" readonly="readonly"/>
            </div>             
            <div class='inline'>
                Prepared by :
                <input type="text" class="textbox" name="status" id="status" value="<?=$options->getUserName($user_id)?>" readonly="readonly"/>
            </div> 
            <div class="inline">
            	Print Status: <br>
            	<?php if ($r['printed']) echo "<b style='color:#F00; font-size:15px;' id='printed_status'>PRINTED</b>"; else echo "<b style='color:#0F0; font-size:15px;' id='printed_status'>PENDING</b>"; ?>
            </div>
        </div>
        <?php } ?>
        <div class="module_actions">
            <!--<input type="submit" name="b" value="New" /> -->
            <?php if($status=="S"){ ?>
            <input type="submit" name="b" id="b" value="Update" />
            <input type="submit" name="b" id="b" value="Finish" onclick="return approve_confirm();"/>
            
            <?php }else if($status!="F" && $status!="C"){ ?>
	            <input type="submit" name="b" id="b" value="Submit" />
            <?php } if($b!="Print Preview" && !empty($status)){ ?>
            	<input type="submit" name="b" id="b" value="Print Preview" />
            <?php } ?>
            <?php  if($b!="Print Preview PDC" && !empty($status)){ ?>
                    <?php if($aVal[printing_type] == 1){ ?> <!-- BDO -->
            	            <a href="transactions/print_check_commercial.php?id=<?=$cv_header_id?>" target="_blank" ><input type="button" value="Print PDC" /></a>
                     <?php } else if($aVal[printing_type] == 2){ ?><!-- CHINA BANK -->
            	            <a href="transactions/print_check_commercial2.php?id=<?=$cv_header_id?>" target="_blank" ><input type="button" value="Print PDC" /></a>
                     <?php}  else if($aVal[printing_type] == 3){ ?><!-- METROBANK -->
            	            <a href="transactions/print_check_commercial3.php?id=<?=$cv_header_id?>" target="_blank" ><input type="button" value="Print PDC" /></a>
                    <?php }  else if($aVal[printing_type] == 4){ ?><!-- PNB -->
            	            <a href="transactions/print_check_commercial4.php?id=<?=$cv_header_id?>" target="_blank" ><input type="button" value="Print PDC" /></a>
                    <?php } ?>
            <?php } ?>
            <?php if($b == "Print Preview"){ ?>	
            	<?php if( $r['printed']  == 0 ){ ?>
                <input type="button" value="Print" onclick="printIframe('JOframe');" />
                <?php } ?>
            <?php } ?>

            <?php if($status!="C" && !empty($status)){ ?>
            <input type="submit" name="b" id="b" value="Cancel" onclick="return approve_confirm();" />
            <?php } ?>
            <?php if($status == "F") { 
				//if($registered_access == '' or $registered_access == ''){
			?>
				<input type="submit" name="b"  value="Unfinish" onclick="return approve_confirm();" />				
            <?php 
				//} 
			}
			?>
        </div>
    </div>
    <div style="width:100%; ">
        <div class="module_title"><img src='images/book_open.png'>VOUCHER DETAILS:  </div>
        <?php if($type == "M"){ ?>
            <table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table" id="search_table">
                <tr bgcolor="#C0C0C0">				
                    <th width="20"><b>#</b></th>
                    <th>APV #</th>
                    <th width="15%">AMOUNT</th>
                </tr> 
                <?php
                $result=mysql_query("
                    select
                        *
                    from
                        cv_detail
                    where
                        cv_header_id = '$cv_header_id'
                ") or die(mysql_error());
                
                $i=1;
                $total_amount = 0;
                while($r=mysql_fetch_assoc($result)){
                    $apv_header_id 	= $r['apv_header_id'];
                    $amount			= $r['amount'];
                    
                    $total_amount += $amount;
                    
                ?>
                <tr>
                    <td><?=$i++?></td>
                    <td><?=str_pad($apv_header_id,7,0,STR_PAD_LEFT)?></td>
                    <td class="align-right"><?=number_format($amount,2,'.',',')?></td>
                </tr>
                <?php
                }
                ?>
            </table>
       	<?php } else { ?>
        	<table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table" id="search_table">
            	<?php if($status == "S"){ ?>
            	<caption style="text-align:left; padding:5px;">
                	<input type="submit" name="b" value="Update Details" />
                </caption>
                <?php } ?>
                <tr bgcolor="#C0C0C0">				
                    <th width="20"><b>#</b></th>
                    <th>ACCOUNT</th>
                    <th>PROJECT</th>
                    <th>EMPLOYEE</th>
                    <th width="15%">AMOUNT</th>
                </tr> 
                <?php
                $result=mysql_query("
                    select
                        gchart,amount,project_id,cv_detail_id,d.gchart_id,d.account_id
                    from
                        cv_detail as d, gchart as g
                    where
                        cv_header_id = '$cv_header_id'
					and
						d.gchart_id = g.gchart_id
                ") or die(mysql_error());
                
                $i=1;
                $total_amount = 0;
                while($r=mysql_fetch_assoc($result)){
                    $gchart			= $r['gchart'];
					$gchart_id		= $r['gchart_id'];
                    $amount			= $r['amount'];
                    $project_id	 	= $r['project_id'];
					$cv_detail_id	= $r['cv_detail_id'];
					$account_id		= $r['account_id'];
					
                    $total_amount += $amount;
					
					$account_select		= $options->getTableAssoc($gchart_id,'update_gchart_id[]',"Select Account","select * from gchart order by gchart asc","gchart_id","gchart");
					$employee_select	= $options->getTableAssoc($account_id,'update_account_id[]',"Select Employee","select * from account order by account asc","account_id","account");
					$project_select 	= $options->getTableAssoc($project_id,'update_project_id[]',"Select Project","select * from projects order by project_name asc","project_id","project_name");
                    
                ?>
                <tr>
                    <td><?=$i++?></td>
                    <td><?=$account_select?></td>
                    <td><?=$project_select?></td>
                    <td><?=$employee_select?></td>
                    <td class="align-right"><input type="text" class="textbox" name="update_amount[]" value="<?=$amount?>" /></td>
                    <!--<td class="align-right"><?=number_format($amount,2,'.',',')?></td> -->
                    <input type="hidden" name="update_cv_detail_id[]" value="<?=$cv_detail_id?>" />
                </tr>
                <?php
                }
                ?>
            </table>
        <?php } ?>
   	</div>
    <div style="clear:both;">
		<?php
        /*if(($b == "Print Preview") && !empty($cv_header_id) && ($aVal[printing_type]==3)){
            echo "	<iframe id='JOframe' name='JOframe' frameborder='0' src='print_cv.php?id=$cv_header_id&bdo=$bdo' width='100%' height='500'>
                    </iframe>";
        }else if(($b == "Print Preview") && !empty($cv_header_id) && ($aVal[printing_type]==1)){         
            echo "	<iframe id='JOframe' name='JOframe' frameborder='0' src='print_cv2.php?id=$cv_header_id&bdo=$bdo' width='100%' height='500'>
                    </iframe>";
        }else if(($b == "Print Preview") && !empty($cv_header_id) && ($aVal[printing_type]==2)){            
            echo "	<iframe id='JOframe' name='JOframe' frameborder='0' src='print_cv3.php?id=$cv_header_id&bdo=$bdo' width='100%' height='500'>
					</iframe>";
        }else{
			 echo "	<iframe id='JOframe' name='JOframe' frameborder='0' src='print_cv.php?id=$cv_header_id&bdo=$bdo' width='100%' height='500'>
                    </iframe>";
		}*/
		
		if($b == "Print Preview"){
			 echo "	<iframe id='JOframe' name='JOframe' frameborder='0' src='print_cv4.php?id=$cv_header_id&bdo=$bdo' width='100%' height='500'>
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
	