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
    

		<div style="display:inline-block;">
			Customer: <br />
			<?=$options->getTableAssoc($_REQUEST['customer_id'],'customer_id','Select Customer',"select * from customer order by customer_first_name asc",'customer_id','customer_last_name', array("customer_first_name",'customer_middle_name','customer_last_name'))?>
		</div>
        
        <div style="display:inline-block;">
            Date<br />
            <input type="text" class="textbox3 datepicker" name="date" value='<?=$_REQUEST['date'];?>'  readonly='readonly'  />
        </div>
                
     	<input type="submit" value="Generate Report"  />
        <input type="button" value="Print" onclick="printIframe('JOframe');" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;" id="content">
     <?php
		if(!empty($_REQUEST['date']))
		{
	?>
   		<iframe id="JOframe" name="JOframe" frameborder="0" src="dprc/print_dprc_water_billing.php?date=<?=$_REQUEST['date']?>&customer_id=<?=$_REQUEST['customer_id']?>" width="100%" height="500">
        </iframe>
    </div>
    <?php }?>
    </div>
</div>
</form>