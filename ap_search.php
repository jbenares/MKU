<script type="text/javascript">
	<!--
	//var TSort_Data = new Array ('my_table', '','','','','','s','s', 's','s','s','s');
	//tsRegister();
	// -->
</script>


<?php

	$b 				= $_REQUEST['b'];
	$keyword 		= $_REQUEST['keyword'];
	$checkList 		= $_REQUEST['checkList'];
	$supplier_id	= $_REQUEST['supplier_id'];
	$ap_supplier	= $_REQUEST['ap_supplier'];

	$supplier_name	= $_REQUEST['supplier_name'];
	$supplier_id	= $_REQUEST['supplier_id'];

	if($b=='Generate GL') {
	  if(!empty($checkList)) {
		postAP($supplier_id,$checkList);
	  }
	}else if($b == "Generate AP Voucher"){
		if(!empty($checkList)){
			mysql_query("
				insert into
					apv_header
				set
					date = '".date("Y-m-d")."',
					supplier_id = '$ap_supplier'
			") or die(mysql_error());

			$apv_header_id = mysql_insert_id();

			foreach($checkList as $ap_id){
				mysql_query("
					insert into
						apv_detail
					set
						apv_header_id = '$apv_header_id',
						ap_id = '$ap_id'
				");

			}

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
<div id="status_update" class="ui-state-highlight ui-corner-all" style="padding: 0px 0.7em; text-align:left;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right:.3em;"></span>Select Reference To Generate Check Voucher</p></div>
<div class=form_layout>
	<div class="module_title"><img src='images/user_orange.png'><?=$transac->getMname($view);?></div>
    <div class="module_actions">
        <div style="display:inline-block;">
			<div class="inline">
                Supplier : <br />
                <input type="text" class="textbox" name="supplier_name" value="<?=$supplier_name?>" id="supplier_name" onclick="this.select();" />
                <input type="hidden" name="supplier_id" id="account_id" value="<?=$supplier_id?>" title="Please Select Supplier" />
            </div>

            <input type="submit" name="b" value="Search" />
            <input type="submit" name="b" value="Generate AP Voucher" />
            <input type="hidden" name="ap_supplier" value="<?=$supplier_id?>"  />

            <!--<input type="button" name="b" value="Pay" onclick="xajax_ap_form(xajax.getFormValues('_form'),'<?=$supplier_id?>')" />-->
        </div>

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
					accounts_payable
				where
					ap_id not in
				(
					select
						ap_id
					from
						apv_detail
				)
			";

			if(1){
				$sql.="
				and
					supplier_id = '$supplier_id'
				";
			}

			$sql.=	"
				order
					by due_date desc
				";


			$pager = new PS_Pagination($conn,$sql,$limit,$link_limit);

			$i=$limitvalue;
			$rs = $pager->paginate();
		?>
        <tr>
            <th width="20">#</th>
            <th width="20" align="center"><input type="checkbox"  name="checkAll" value="Comfortable with" onclick="javascript:check_all('_form', this)" title="Check/Uncheck All" /></th>
            <th>Reference</th>
            <th>Date</th>
            <th>Total Amount</th>
            <th>Status</th>
        </tr>
		<?php
			while($r=mysql_fetch_assoc($rs)) {
					$ap_id				= $r['ap_id'];
					$header				= $r['header'];
					$header_id			= $r['header_id'];
					$header_id_pad 		= str_pad($header_id,7,0,STR_PAD_LEFT);
					$total_amount		= $r['total_amount'];
					$due_date			= $r['due_date'];
					$rr_header_id_pad 	= str_pad($rr_header_id,7,0,STR_PAD_LEFT);
					$reference			= $r['reference'];
					$status				= $r['status'];
		?>
        	<tr>
                <td width="20"><?=++$i?></td>
                <td><input type="checkbox" name="checkList[]" value="<?=$ap_id?>" onclick="document._form.checkAll.checked=false" class="check_box" rel="<?=$ap_id?>" ></td>
                <td><?=$reference?></td>
                <td><?=date("F j, Y", strtotime($due_date))?></td>
                <td class="align-right"><?=number_format($total_amount,2,'.',',')?></td>
                <td><?=$options->getTransactionStatusName($status)?></td>
            </tr>
      	<?php
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
