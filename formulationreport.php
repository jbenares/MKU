<script language="JavaScript" src="scripts/calendar/calendar_us.js"></script>
<link rel="stylesheet" href="scripts/calendar/calendar.css"></link>

<?php
	
	$fromDate=$_REQUEST[fromDate];
	$toDate=$_REQUEST[toDate];


	$b = $_REQUEST['b'];
	$checkList = $_REQUEST['checkList'];
	$eq_keyword = $_REQUEST['eq_keyword'];
	
	if($b=='Delete Selected') {
	  if(!empty($checkList)) {
		foreach($checkList as $ch) {	
			mysql_query("delete from clients where eqID='$ch'");
		}
	  }
	}
?>

<form name="_form" action="" method="post">
<div class=form_layout>
	<div class="module_title"><img src='images/user_orange.png'><?=$transac->getMname($view);?></div>
    <div class="module_actions">
    	<div style="display:inline-block; margin-right:10px;">
    	From:
    	<input name="fromDate" id="fromDate" value="<?=$fromDate?>" class="textbox" readonly="readonly" type="text" onmouseover="Tip('Choose a date for listings of posts, then click Display Posts button.');" />	
        <script language="JavaScript">
        new tcal ({
            // form name
            'formname': '_form',
            // input name
            'controlname': 'fromDate'
        });
    
        </script>
        </div>
	   	<div style="display:inline-block; margin-right:10px;">
        To:
    	<input name="toDate" id="toDate" value="<?=$toDate?>" class="textbox" readonly="readonly" type="text" onmouseover="Tip('Choose a date for listings of posts, then click Display Posts button.');" />	
        <script language="JavaScript">
        new tcal ({
            // form name
            'formname': '_form',
            // input name
            'controlname': 'toDate'
        });
    
        </script>
        </div>
        <input type="submit" name="b" value="Generate Equipment Utilization Report" class="buttons" />
      <!--  <input type="submit" name="b" value="Display All" class="buttons" />-->
      <!--  <input type="button" name="b" value="Add Equipment" onclick="xajax_new_equipmentform();toggleBox('demodiv',1);" class="buttons" />
        <input type="submit" name="b" value="Delete Selected" onclick="return approve_confirm();" class="buttons" />-->
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;">
    <?php
		if(!empty($fromDate) && !empty($toDate))
		{
	?>
   		<iframe id="JOframe" name="JOframe" frameborder="0" src="printEUR.php?fromDate=<?=$fromDate;?>&toDate=<?=$toDate?>" width="100%" height="500">
        </iframe>
    </div>
    <?php }?>
</div>
</form>