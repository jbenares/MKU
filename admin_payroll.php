<?php
	$b				= $_REQUEST['b'];

  $supplier_id          = $_REQUEST['supplier_id'];
  $supplier_name        = (!empty($supplier_id))?$options->getSupplierName($supplier_id):"";
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
<style type="text/css">
.table-form tr td:nth-child(odd){
	text-align:right;
	font-weight:bold;
}
.table-form td{
	padding:3px;
}
.table-form{
	display:inline-table;
	border-collapse:collapse;
}
</style>

<form name="_form" action="" method="post">
<div class=form_layout>
	<div class="module_title"><img src='images/user_orange.png'><?=$transac->getMname($view);?></div>
    <div class="module_actions">
    	<table class="table-form">
        	<tr>
                <td>
                   <div id="supplier_div">
                      Supplier :
                      <input type="text" class="textbox" name="supplier_id_display" value="<?=$supplier_name?>" id="supplier_name2" onclick="this.select();" />
                      <input type="hidden" name="supplier_id" id="account_id" value="<?=$supplier_id?>" title="Please Select Supplier" />
                  </div>
               	</td>
				<td>
					 <div class="module_actions">
						<input type="submit" name="b" value="View Summary"/>
						<input type="button" value="Print" onclick="printIframe('JOframe');" />
					</div>
				</td>
           	</tr>
      	</table>
  	</div>
    <?php
	 #echo "STOCK ID : $stock_id";
    ?>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <?php if ($supplier_id && $b == "View Summary") {?>
	    <iframe id="JOframe" name="JOframe" frameborder="0" src="print_admin_payroll_summary.php?supplier_id=<?=$supplier_id?>" width="100%" height="500">
        </iframe>
    <?php } ?>
    </div>
</div>
</form>

