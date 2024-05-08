<script type="text/javascript">
	<!--
	//var TSort_Data = new Array ('my_table', '','','','','','s','s', 's','s','s','s');
	//tsRegister();
	// -->
</script>
<style type="text/css">
	.border-bottom td{
		border-bottom:1px solid #000;
	}
	
	.border-top-double td{
		border-top:3px double #000;
	}
</style>


<?php

	$b 				= $_REQUEST['b'];
	$keyword 		= $_REQUEST['keyword'];
	$ar_payment_id	= $_REQUEST['ar_payment_id'];
	$checkList 		= $_REQUEST['checkList'];
	
	$project_name		= $_REQUEST['project_name'];
	$project_id			= $_REQUEST['project_id'];
	
	$contractor_name	= $_REQUEST['contractor_name'];
	$contractor_id		= $_REQUEST['contractor_id'];
	
	$account			= $_REQUEST['account'];
	
	$account_id = $id	= ($account=="p")?$project_id:$contractor_id;
	$header				= ($account=="p")?"project_id":"contractor_id";
	
	
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

<form name="_form" id="_form" action="" method="post">

<div class=form_layout>
	<div class="module_title"><img src='images/user_orange.png'><?=$transac->getMname($view);?></div>
    <div class="module_actions">       

        <div class="inline">
            	Account : <br />
                <select name="account" id="account">
               		<option value="" >Select Account : </option> 
                    <option value="p" <?=($account=="p")?"selected='selected'":""?> >Project</option>
                    <option value="c" <?=($account=="c")?"selected='selected'":""?>>Subcontractor</option>
                </select>
            </div>
            <?php
			if($account == "p"){
				$style="style='display:inline-block;'";	
			}else{
				$style="style='display:none;'";	
			}
            ?>
			<div class="inline" <?=$style?> id="div_project">
                Project : <br />
                <input type="text" class="textbox" name="project_name" value="<?=$project_name?>" id="project_name" onclick="this.select();" />
                <input type="hidden" name="project_id" id="project_id" value="<?=$project_id?>" title="Please Select Project" />
            </div>
            
            <?php
			if($account == "c"){
				$style="style='display:inline-block;'";	
			}else{
				$style="style='display:none;'";	
			}
            ?>
            <div class="inline" id="div_contractor" <?=$style?>>
                Subcontractor: <br />
                <input type="text" class="textbox" name="contractor_name" value="<?=$contractor_name?>" id="contractor_name" onclick="this.select();" />
                <input type="hidden" name="contractor_id" id="contractor_id" value="<?=$contractor_id?>" title="Please Select Contractor" />
            </div>
         
        <input type="submit" name="b" value="Search" />            
      	<input type="button" value="Print" onclick="printIframe('JOframe');" />
        <input type="hidden" id="ap_total_amount" name='ap_total_amount' />
        <input type="hidden" name='view' value="<?=$view?>" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    
   	<?php
	if($b=="Search" || empty($b) ){
    ?> 
    
    <?php
		$page = $_REQUEST['page'];
		if(empty($page)) $page = 1;
		 
		$limitvalue = $page * $limit - ($limit);
		
		$sql = "
			select
				*
			from
				ar_payment
			
		";
		if(!empty($account)){
		$sql.="
		where
			account = '$header'
		and
			account_id = '$account_id'
		";
		}
		$sql.="
		order 
			by date desc
		";
					  
			
		$pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
				
		$i=$limitvalue;
		$rs = $pager->paginate();
	?>
    <div class="pagination">
   		<?=$pager->renderFullNav("$view")?>                
	</div>
    <table id="my_table" cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table">
        
        <tr>				
            <th width="20">#</th>
            <th width="20" align="center"><input type="checkbox"  name="checkAll" value="Comfortable with" onclick="javascript:check_all('_form', this)" title="Check/Uncheck All" /></th>
            <th width="20"></th>
            <th width="20"></th>
            <th>AR #</th>
            <th>DATE</th>         
            <th>BANK</th>         
            <th>CHECK NO.</th>         
            <th>CHECK DATE</th>         
            <th>AMOUNT</th>         
            <th>PAYER</th>         
            <th>STATUS</th>
        </tr>  
        <?php								
            while($r=mysql_fetch_assoc($rs)) {
                    $ar_payment_id		= $r['ar_payment_id'];
                    $ar_payment_id_pad	= str_pad($ar_payment_id,7,0,STR_PAD_LEFT);
                    $date		= $r['date'];
                    
                    $bank		= $r['bank'];
                    $checkno	= $r['checkno'];
                    $checkdate	= $r['checkdate'];
                    $amount		= $r['amount'];
                    
                    $supplier_id	= $r['supplier_id'];
                    $status			= $r['status'];
					$ap_header_id	 =$r['ap_header_id'];
					
					$account	= $r['account'];
					$account_id	= $r['account_id'];
					
					$account_display = ($account == "project_id")?"Project":"Subcontractor";
					$account_id_display = ($account == "project_id")?$options->attr_Project($account_id,'project_name'):$options->attr_Contractor($account_id,'contractor');
        ?>
            <tr>
                <td width="20"><?=++$i?></td>
                <td><input type="checkbox" name="checkList[]" value="<?=$ar_payment_id?>" onclick="document._form.checkAll.checked=false" class="check_box" rel="<?=$total_amount?>" ></td>
                <td><a href="admin.php?view=<?=$view?>&b=d&ar_payment_id=<?=$ar_payment_id?>"><img src="images/edit.gif" /></a></td>
                <td><a href="admin.php?view=<?=$view?>&b=print&ar_payment_id=<?=$ar_payment_id?>"><img src="images/action_print.gif" /></a></td>
                <td><?=$ar_payment_id_pad?></td>	
                <td><?=date("F j, Y", strtotime($date))?></td>		
                <td><?=$bank?></td>	
                <td><?=$checkno?></td>	
                <td><?=date("F j, Y", strtotime($checkdate))?></td>		
                <td class="align-right"><?=number_format($amount,2,'.',',')?></td>	
                <td><?=$account_id_display?></td>	
                <td><?=$options->getTransactionStatusName($status)?></td>	
            </tr>
                
        <?php
            }
        ?>
    </table>
    
    <div class="module_title">
        <img src='images/user_orange.png'> ACCOUNTS RECIEVABLES
    </div>
    <?php
	$headers = array();
	$query="
		select
			*
		from
			ar_header
	";
	if(!empty($id) && !empty($header)){
	$query.="
		where 
			account = '$header'
		and
			account_id = '$id'
	";	
	}
	$result=mysql_query($query) or die(mysql_error());
	
	while($r = mysql_fetch_assoc($result)){
		$array = array();
		$ar_header_id 	= $r['ar_header_id'];	
		
		$account		= $r['account'];
		$account_id		= $r['account_id'];
		
		$array['ar_header_id'] 	= $ar_header_id;
		$array['account']	 	= $account;
		$array['account_id']	= $account_id;
		array_push($headers, $array);
	}
    ?>
    <?php
	foreach($headers as $item){
		$ar_header_id 	= $item['ar_header_id'];
		$account_id		= $item['account_id'];
		$account		= $item['account'];

		$balance = $options->getARBalance($ar_header_id);
		$account_display = ($account == "project_id")?"Project":"Subcontractor";
		$account_id_display = ($account == "project_id")?$options->attr_Project($account_id,'project_name'):$options->attr_Contractor($account_id,'contractor');
		
    ?>
   	<table cellspacing="2" cellpadding="5" style="border-collapse:collapse; display:inline-table; margin:10px;" >
    	<caption><?=$account_id_display?></caption>
    	<tr>
        	<td><img src="images/add.png" style="cursor:pointer;" onmouseover="Tip('Add Payment');" onclick="xajax_ar_form_add('<?=$ar_header_id?>','<?=$account?>','<?=$account_id?>')"></td>
            <td><em>Balance : </em><b><?=number_format($balance,2,'.',',')?></b></td>
        </tr>
        <?php
		$result= mysql_query("
			select
				p.header_id,
				p.total_amount
			from
				ar_detail as d, accounts_receivable as p
			where
				d.ar_id = p.ar_id
			and
				ar_header_id = '$ar_header_id'
		");
        ?>
        <tr class="border-bottom">
            <td>ISSUANCE #</td>
            <td>AMOUNT</td>
        </tr>
		<?php
		$total = 0;
		while($r=mysql_fetch_assoc($result)){
			$total_amount = $r['total_amount'];
			$header_id = $r['header_id'];
			$header_id_pad = str_pad($header_id,7,0,STR_PAD_LEFT);
			$total += $total_amount;
        ?>        
        <tr>
            <td><?=$header_id_pad?></td>
            <td class="align-right"><?=number_format($total_amount,2,'.',',')?></td>
        </tr>
        <?php
		}
        ?>
        <tr class="border-top-double">
        	<td></td>
        	<td class="align-right" style="border-bottom:3px double #000; font-weight:bold;"><?=number_format($total,2,'.',',')?></td>
        </tr>
   	<?php
	}
    ?>
    </table>
    <?php
	}else if($b=='d'){
    ?>
    <?php
		$result = mysql_query("
			select
				*
			from
				ar_payment
			where
				ar_payment_id = '$ar_payment_id'
		
		") or die(mysql_error());
		
		
		$r = mysql_fetch_assoc($result);
		
		$ar_payment_id		= $r['ar_payment_id'];
		$ar_payment_id_pad	= str_pad($ar_payment_id,7,0,STR_PAD_LEFT);
		$date		= $r['date'];
		
		$bank		= $r['bank'];
		$checkno	= $r['checkno'];
		$checkdate	= $r['checkdate'];
		$amount		= $r['amount'];
		$supplier		= $options->attr_Supplier($supplier_id,'account');
		$status			= $r['status'];
		
		$account	= $r['account'];
		$account_id	= $r['account_id'];
		
		$account_display = ($account == "project_id")?"Project":"Subcontractor";
		$account_id_display = ($account == "project_id")?$options->attr_Project($account_id,'project_name'):$options->attr_Contractor($account_id,'contractor');
		
		$ar_header_id	= $r['ar_header_id'];
    ?>
    <div>
		<div class="module_actions">
        	<div class="module_title"><img src='images/money.png'>PAYMENT DETAILS : </div>
            <div class="inline">
            	Date :<br />
				<input type="text" class="textbox" value="<?=$date?>"  readonly="readonly"/>
            </div>	
            
            <div class="inline">
            	PAYER :<br />
				<input type="text" class="textbox" value="<?=$account_id_display?>"  readonly="readonly" />
            </div>	
            
            <br />
            
            <div class="inline">
            	Bank :<br />
				<input type="text" class="textbox" value="<?=$bank?>" readonly="readonly" />
            </div>	
            <div class="inline">
            	Check No. :<br />
				<input type="text" class="textbox" value="<?=$checkno?>" readonly="readonly" />
            </div>	
            <div class="inline">
            	Check Date. :<br />
				<input type="text" class="textbox" value="<?=$checkdate?>" readonly="readonly" />
            </div>	
            <div class="inline">
            	Check Amount. :<br />
				<input type="text" class="textbox" value="<?=$amount?>" readonly="readonly" />
            </div>	
            
            <?php
			if(!empty($status)){
			?>
            <br />
            <div class='inline'>AR # : <br />
            <input type='text' readonly="readonly" class='textbox' value="<?=$ar_payment_id_pad?>"></div>
        
            <div class='inline'>Status : <br />
            <input type="text" class="textbox" value="<?=$options->getTransactionStatusName($status)?>" readonly="readonly" /></div>

			<?php
			}
			?>
        </div>
        <?php
		$result=mysql_query("
			select
				p.header_id,
				p.total_amount
			from
				ar_detail as d, accounts_receivable as p
			where
				d.ar_id = p.ar_id
			and
				ar_header_id = '$ar_header_id'
		") or die(mysql_error());
        ?>
        <div class="module_title">
        	<img src="images/money.png" /> PAYABLE DETAILS
        </div>
        <table cellspacing="2" cellpadding="5"  >
        	<tr>
            	<th width="20">#</th>
            	<th width="100">ISSUANCE #</th>
                <th>TOTAL AMOUNT</th>
            </tr>
            <?php
			$i=1;
			$balance = 0;
			while($r=mysql_fetch_assoc($result)){
				$header_id = $r['header_id'];
				$header_id_pad = str_pad($header_id,7,0,STR_PAD_LEFT);
				$total_amount = $r['total_amount'];
				$balance += $total_amount;
            ?>
            <tr>
            	<td><?=$i++?></td>
            	<td><?=$header_id_pad?></td>
                <td class="align-right"><?=number_format($total_amount,2,'.',',')?></td>
            </tr>
            <?php
			}
            ?>
            <tr>
            	<td colspan="3" style="border-top:1px solid #000; font-style:italic;">Previous Payments :</td>
            </tr>
            <?php
			$result = mysql_query("
				select
					*
				from
					ar_payment
				where
					ar_header_id = '$ar_header_id'
			") or die(mysql_error());
            ?>
            
            <?php
			while($r=mysql_fetch_assoc($result)){
			$ar_payment_id		= $r['ar_payment_id'];
			$ar_payment_id_pad	= str_pad($ar_payment_id,7,0,STR_PAD_LEFT);
			$date		= $r['date'];
			
			$bank		= $r['bank'];
			$checkno	= $r['checkno'];
			$checkdate	= $r['checkdate'];
			$amount		= $r['amount'];
			
			$account	= $r['account'];
			$account_id	= $r['account_id'];
			
			$account_display = ($account == "project_id")?"Project":"Subcontractor";
			$account_id_display = ($account == "project_id")?$options->attr_Project($account_id,'project_name'):$options->attr_Contractor($account_id,'contractor');
			$status			= $r['status'];
			
			$ar_header_id	= $r['ar_header_id'];
			
			$balance -= $amount;
            ?>
            <tr>
				<td colspan="2"><?=date("F j, Y",strtotime($date))?></td>
                <td class="align-right"><?=number_format($amount,2,'.',',')?></td>
            </tr>
            <?php
			}
            ?>
            <tr>
            	<td colspan="2" style="font-style:italic;">Balance :</td>
            	<td class="align-right" style="border-top:1px solid #000; border-bottom:3px double #000; font-weight:bold;"><?=number_format($balance,2,'.',',')?></td>
            </tr>
            
        </table>
    </div>
    <?php
	}else if($b=="print"){
    ?>
    <iframe id='JOframe' name='JOframe' frameborder='0' src='print_ap_payments.php?id=<?=$ap_payment_header_id?>' width='100%' height='500'></iframe>
    <?php
	}
    ?>
</div>
</form>
<script type="text/javascript">
j(function(){
	j("#account").change(function(){
		var account = j(this).val();
		if(account == "p"){
			j("#div_project").show(500);		
			j("#div_contractor").hide(500);		
		}else{
			j("#div_project").hide(500);		
			j("#div_contractor").show(500);		
		}
	});
});
</script>