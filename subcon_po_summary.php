<?php
	$b				= $_REQUEST['b'];
	$budget_header_id	= $_REQUEST['budget_header_id'];
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
            	<td>BUDGET # :</td>
                <td>
                   <input type=text name=budget_header_id class=textbox3 value="<?=$budget_header_id?>"/>
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
    <?php if ($budget_header_id && $b == "View Summary") {?>
	    <iframe id="JOframe" name="JOframe" frameborder="0" src="print_subcon_po_summary.php?budget_header_id=<?=$budget_header_id?>" width="100%" height="500">
        </iframe>
    <?php } ?>
    </div>
</div>
</form>
<script type="text/javascript">
jQuery(function(){
	j("#work_category_id").change(function(){
		xajax_display_subworkcategory(this.value);
	});
	
	xajax_display_subworkcategory('<?=$_REQUEST['work_category_id']?>','<?=$_REQUEST['sub_work_category_id']?>');
});
</script>