<?php
$b				= $_REQUEST['b'];
$project_name	= $_REQUEST['project_name'];
$project_id		= $_REQUEST['project_id'];
$from_date		= $_REQUEST['from_date'];
$to_date		= $_REQUEST['to_date'];
$categ_id		= $_REQUEST['categ_id'];
$date_option	= $_REQUEST['date_option'];
$supplier_name	= $_REQUEST['supplier_name'];
$supplier_id	= (!empty($supplier_name)) ? $_REQUEST['supplier_id'] : "";
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

<form name="_form" action="" method="post">
<div class=form_layout>
	<div class="module_title"><img src='images/user_orange.png'><?=$transac->getMname($view);?></div>
    <div class="module_actions">       
    
    	<div class="inline">
        	Project <br>
            <input type="text" class="textbox project" name="project_name" value="<?=$_REQUEST['project_name']?>" onclick="this.select();" />
            <input type="hidden" name="project_id" value="<?=($_REQUEST['project_name']) ? $_REQUEST['project_id'] : ""?>" >
        </div>
        
        <div class="inline">
        	Supplier <br />
            <input type="text" class="textbox supplier" name="supplier_name" value="<?=$supplier_name?>" onclick="this.select();" />
            <input type="hidden" name="supplier_id" value="<?=$supplier_id?>" />
        </div>
             
        <div class="inline">
            From Date<br />
            <input type="text" class="textbox3 datepicker" name="from_date" value='<?=$from_date?>' readonly='readonly' >
        </div>    
        
        <div class="inline">
            To Date<br />
            <input type="text" class="textbox3 datepicker" name="to_date" value='<?=$to_date?>' readonly='readonly' >
        </div>    

        <div class="inline">
            From Cleared Date<br />
            <input type="text" class="textbox3 datepicker" name="from_cleared_date" value='<?=$_REQUEST['from_cleared_date']?>' readonly='readonly' >
        </div>    
        

        <div class="inline">
            To Cleared Date<br />
            <input type="text" class="textbox3 datepicker" name="to_cleared_date" value='<?=$_REQUEST['to_cleared_date']?>' readonly='readonly' >
        </div>

		<div class="inline">
            From Released Date<br />
            <input type="text" class="textbox3 datepicker" name="from_released_date" value='<?=$_REQUEST['from_released_date']?>' readonly='readonly' >
        </div>    
        

        <div class="inline">
            To Released Date<br />
            <input type="text" class="textbox3 datepicker" name="to_released_date" value='<?=$_REQUEST['to_released_date']?>' readonly='readonly' >
        </div>         
        
        <div class="inline">
        	Select Bank Account <br />
            <?=$options->getTableAssoc($_REQUEST['cash_gchart_id'],'cash_gchart_id','Select Bank Account',"select * from gchart where sub_mclass = '6' order by gchart asc",'gchart_id','gchart')?>
        </div>
   	</div>
    <div class="module_actions">
        <input type="checkbox" name="date_option" value="1" id="date_option" <?=($date_option)? 'checked="checked"' : '' ?> /><label for="date_option">Check to make date to Check Date as Reference</label>
        
        <input type="submit" name="b" value="View All Checks"  />
      	<input type="submit" name="b" value="View Uncleared Checks"  />
        <input type="submit" name="b" value="View Cleared Checks"  />
		<input type="submit" name="b" value="View Unreleased Checks"  />
        <input type="submit" name="b" value="View Released Checks"  />
        <input type="button" value="Print" onclick="printIframe('JOframe');" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;" id="content">
     <?php	if($from_date && $to_date && ($b == "View Uncleared Checks")){ ?>
   		<iframe id="JOframe" name="JOframe" frameborder="0" src="print_uncleared_checks.php?from_date=<?=$from_date?>&to_date=<?=$to_date?>&date_option=<?=$date_option?>&cash_gchart_id=<?=$_REQUEST['cash_gchart_id']?>&project_id=<?=$_REQUEST['project_id']?>&supplier_id=<?=$supplier_id?>" width="100%" height="500">
        </iframe>
    <?php } else if ( ( $b == "View Cleared Checks") ) {?>
	    <iframe id="JOframe" name="JOframe" frameborder="0" src="print_cleared_checks.php?from_date=<?=$from_date?>&to_date=<?=$to_date?>&date_option=<?=$date_option?>&cash_gchart_id=<?=$_REQUEST['cash_gchart_id']?>&project_id=<?=$_REQUEST['project_id']?>&supplier_id=<?=$supplier_id?>&from_cleared_date=<?=$_REQUEST['from_cleared_date']?>&to_cleared_date=<?=$_REQUEST['to_cleared_date']?>" width="100%" height="500">
        </iframe>
	<?php }else if ($from_date && $to_date && ( $b == "View All Checks") ) {?>
	    <iframe id="JOframe" name="JOframe" frameborder="0" src="print_all_checks.php?from_date=<?=$from_date?>&to_date=<?=$to_date?>&date_option=<?=$date_option?>&cash_gchart_id=<?=$_REQUEST['cash_gchart_id']?>&project_id=<?=$_REQUEST['project_id']?>&supplier_id=<?=$supplier_id?>" width="100%" height="500">
        </iframe>
  
	<?php }else if ($from_date && $to_date && ( $b == "View Released Checks") ) {?>
	    <iframe id="JOframe" name="JOframe" frameborder="0" src="print_released_checks.php?from_date=<?=$from_date?>&to_date=<?=$to_date?>&date_option=<?=$date_option?>&cash_gchart_id=<?=$_REQUEST['cash_gchart_id']?>&project_id=<?=$_REQUEST['project_id']?>&supplier_id=<?=$supplier_id?>&from_released_date=<?=$_REQUEST['from_released_date']?>&to_released_date=<?=$_REQUEST['to_released_date']?>" width="100%" height="500">
        </iframe>
    
	<?php }else if ($from_date && $to_date && ( $b == "View Unreleased Checks") ) {?>
	    <iframe id="JOframe" name="JOframe" frameborder="0" src="print_unreleased_checks.php?from_date=<?=$from_date?>&to_date=<?=$to_date?>&date_option=<?=$date_option?>&cash_gchart_id=<?=$_REQUEST['cash_gchart_id']?>&project_id=<?=$_REQUEST['project_id']?>&supplier_id=<?=$supplier_id?>" width="100%" height="500">
        </iframe>
    <?php } ?>
    </div>
</div>
</form>