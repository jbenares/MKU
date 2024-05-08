<script language="JavaScript" src="scripts/cwcalendar/calendar.js"></script>
<link rel="stylesheet" href="scripts/cwcalendar/cwcalendar.css"></link>
<script type="text/javascript">
	<!--
	//var TSort_Data = new Array ('my_table', '','','','','','s','s', 's','s','s','s');
	//tsRegister();
	// -->
</script>

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
<?php
$eva = $_REQUEST['eva'];
?>
<form name="_form" action="" method="post">
<div class=form_layout>
	<div class="module_title"><img src='images/user_orange.png'><?=$transac->getMname($view);?></div>
    <div class="module_actions">       
    
       
         <div class="inline">
            From<br />
            <input type="text" class="textbox3 datepicker" name="from" value='<?php echo $_REQUEST['from'];?>' readonly='readonly' >
        </div>
        <div class="inline">
            To<br />
            <input type="text" class="textbox3 datepicker" name="to" value='<?php echo $_REQUEST['to'];?>' readonly='readonly' >
        </div>
        <div class="inline">
			<select name="eva">
				<?php if($eva == 1){ ?>
				<option disabled value="" >Select Option:</option>
				<option selected value="1">With Evaluation</option>
				<option value="2">Without Evaluation</option>
				<?php }else if($eva == 2){ ?>
				<option disabled value="" >Select Option:</option>
				<option value="1">With Evaluation</option>
				<option selected value="2">Without Evaluation</option>
				<?php }else{ ?>
				<option selected disabled value="" >Select Option:</option>
				<option value="1">With Evaluation</option>
				<option value="2">Without Evaluation</option>				
				<?php }?>
			</select>
        </div>
		<br />
		<div class="inline">
            Supplier<br />
            <?=$options->option_supplier_accounts($_REQUEST['account_id'])?>
        </div>           
     	<input type="submit" value="Generate Report"  />
        <input type="button" value="Print" onclick="printIframe('JOframe');" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
	<div style="padding:3px; text-align:center;" id="content">
    <?php if(!empty($_REQUEST['from']) && !empty($_REQUEST['to']) && !empty($_REQUEST['account_id']) && !empty($_REQUEST['eva']) && $_REQUEST['eva'] == '1'){ ?>
   		<iframe id="JOframe" name="JOframe" frameborder="0" src="print_rr_evaluation.php?from=<?=$_REQUEST['from'];?>&to=<?=$_REQUEST['to']?>&account_id=<?=$_REQUEST['account_id']?>&eva_check=<?=$eva_check?>" width="100%" height="500"></iframe>
    <?php }else if(!empty($_REQUEST['from']) && !empty($_REQUEST['to']) && empty($_REQUEST['account_id']) && !empty($_REQUEST['eva']) && $_REQUEST['eva'] == '1'){ ?>
   		<iframe id="JOframe" name="JOframe" frameborder="0" src="print_rr_all_evaluation.php?from=<?=$_REQUEST['from'];?>&to=<?=$_REQUEST['to']?>&eva_check=<?=$eva_check?>" width="100%" height="500"></iframe>
    <?php }else if(!empty($_REQUEST['from']) && !empty($_REQUEST['to']) && $_REQUEST['eva'] == '2'){ ?>
		<iframe id="JOframe" name="JOframe" frameborder="0" src="print_rr_no_evaluation.php?from=<?=$_REQUEST['from'];?>&to=<?=$_REQUEST['to']?>&account_id=<?=$_REQUEST['account_id']?>" width="100%" height="500"></iframe>
	<?php } ?>
    </div>
</div>
</form>