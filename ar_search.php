<script type="text/javascript">
	<!--
	//var TSort_Data = new Array ('my_table', '','','','','','s','s', 's','s','s','s');
	//tsRegister();
	// -->
</script>


<?php

	$b 					= $_REQUEST['b'];
	$keyword 			= $_REQUEST['keyword'];
	$checkList 			= $_REQUEST['checkList'];
	$supplier_id		= $_REQUEST['supplier_id'];
	
	$project_name		= $_REQUEST['project_name'];
	$project_id			= $_REQUEST['project_id'];
	
	$contractor_name	= $_REQUEST['contractor_name'];
	$contractor_id		= $_REQUEST['contractor_id'];
	
	$account			= $_REQUEST['account'];
	
	$account_id			= ($account=="p")?$project_id:$contractor_id;
	if($b=='Generate GL') {
	  if(!empty($checkList)) {
		postAP($supplier_id,$checkList);
	  }
	}
	
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
<div id="status_update" class="ui-state-highlight ui-corner-all" style="padding: 0px 0.7em; text-align:left;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right:.3em;"></span>Select Issuance # To Accept Payments</p></div>
<div class=form_layout>
	<div class="module_title"><img src='images/user_orange.png'><?=$transac->getMname($view);?></div>
    <div class="module_actions">       
        <div style="display:inline-block;">
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
            <input type="button" name="b" value="Pay" onclick="xajax_ar_form(xajax.getFormValues('_form'),'<?=$account?>','<?=$account_id?>')" />
        </div>
      	<input type="button" value="Print" onclick="printIframe('JOframe');" />
        
        <input type="hidden" id="ap_total_amount" name='ap_total_amount' />
        <input type="hidden" name='view' value="<?=$view?>" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;" id="content">
    <table id="my_table" cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table">
    	<?php
			$page = $_REQUEST['page'];
			if(empty($page)) $page = 1;
			 
			$limitvalue = $page * $limit - ($limit);
		
			$sql = "
				select
					*
				from
					accounts_receivable
				where
					ar_id not in
				(
					select
						ar_id
					from
						ar_detail	
				)
			";
				
			if($account == "p"){
				$sql.="
				and
					account = 'project_id'
				and
					account_id = '$project_id'
				";
			}else if($account=="c"){
				$sql.="
				and
					account = 'contractor_id'
				and
					account_id = '$contractor_id'
				";
			}
			
			$sql.=	"
				order 
					by date desc
				";
						  
				
			$pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
                    
			$i=$limitvalue;
			$rs = $pager->paginate();
		?>
        <tr>				
            <th width="20">#</th>
            <th width="20" align="center"><input type="checkbox"  name="checkAll" value="Comfortable with" onclick="javascript:check_all('_form', this)" title="Check/Uncheck All" /></th>
            <th>Issue #</th>
            <th>Date</th>
            <th>Payer</th>      
            <th>Total Amount</th>         
            <th>Status</th>
        </tr>  
		<?php								
			while($r=mysql_fetch_assoc($rs)) {
					$ar_id				= $r['ar_id'];
					$header				= $r['header'];
					$header_id			= $r['header_id'];
					$header_id_pad 		= str_pad($header_id,7,0,STR_PAD_LEFT);
					$total_amount		= $r['total_amount'];
					$date				= $r['date'];
					$status				= $r['status'];
					
					$account	= $r['account'];
					$account_id	= $r['account_id'];
					
					$account_display = ($account == "project_id")?"Project":"Subcontractor";
					$account_id_display = ($account == "project_id")?$options->attr_Project($account_id,'project_name'):$options->attr_Contractor($account_id,'contractor');
			if(!empty($contractor_id) || !empty($project_id)):
		?>
        	<tr>
                <td width="20"><?=++$i?></td>
                <td><input type="checkbox" name="checkList[]" value="<?=$ar_id?>" onclick="document._form.checkAll.checked=false" class="check_box" rel="<?=$total_amount?>" ></td>
                <td><?=$header_id_pad?></td>	
                <td><?=date("F j, Y", strtotime($date))?></td>		
                <td><?=$account_id_display?></td>	
                <td class="align-right"><?=number_format($total_amount,2,'.',',')?></td>	
                <td><?=$options->getTransactionStatusName($status)?></td>	
            </tr>
                
      	<?php
			endif;
			}
        ?>
        </table>
        <table cellspacing="2" cellpadding="5" width="100%" class="search_table">
        <tr>
            <td colspan="5" align="left">
                <?php
                    echo $pager->renderFullNav("$view");
                ?>                
            </td>
      	</tr>
    </table>
    </div>
</div>
<div id="ap_dialog" style="padding:0px;">
    <div id="ap_dialog_content">
    	
    </div>
</div>

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
		
		
		j("#ap_dialog").dialog({autoOpen: false , modal:true , show: 'slide' , hide : 'slide' , width : 'auto', resizable : false, minHeight : 'auto'});
		
		j(":checkbox").change(function(){
			var total_amount = 0;
			j(".check_box").each(function(){
				if( j(this).is(":checked") ){
					total_amount += parseFloat(j(this).attr("rel"));
				}
			});		
			j("#ap_total_amount").val(total_amount);
		});
		
	});
</script>
</form>