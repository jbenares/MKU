<?php
	$now = date("m/d/Y");

?>
<script language="JavaScript" src="scripts/calendar/calendar_us.js"></script>
<link rel="stylesheet" href="scripts/calendar/calendar.css"></link>
<form name=wallform id=wallform action=javascript:void(null); onsubmit=xajax_postWall(xajax.getFormValues('wallform'));toggleBox('demodiv',1);>
<div class="home_module_title"><img src="images/script.png" /> ANNOUNCEMENTS</div>
<div style="min-height:400px;width:60%;float:left;text-align:left;background:#EEEEEE;border-left:#C0C0C0 1px solid;border-right:#C0C0C0 3px solid;border-bottom:#C0C0C0 1px solid;">
	<div class="home_module_actions">
        <input name="Ndate" id="Ndate" class="textbox" readonly="readonly" value="<?=$now;?>" type="text" onmouseover="Tip('Choose a date for listings of posts, then click Display Posts button.');" />
        <script language="JavaScript">
        new tcal ({
            // form name
            'formname': 'wallform',
            // input name
            'controlname': 'Ndate'
        });

        </script>
        <input type="button" value="Display Posts" onclick="xajax_update_wall(document.getElementById('Ndate').value);" class="buttons" />
    </div>
    <div class="home_module_actions">
        Write Your Message Here :<br />
        <textarea name="wallmsg" class="textbox2"><?=$r['c']?></textarea>
        <input type="hidden" name="userID" value="<?=$registered_userID;?>" /><br />
        <input type="submit" name="b" value="Post Your Message" class="buttons" />
    </div>
    <div id="wall_div" style="background:#FFFFFF;"></div>
</div>
<div style="width:40%;text-align:left;display:inline;">
    <div id="important_div" style="height:424px;border-right:#C0C0C0 1px solid;border-bottom:#C0C0C0 1px solid;overflow-y:scroll;overflow-x:hidden;"></div>
</div>
</form>

<script style="text/JavaScript">
xajax_update_wall(document.getElementById('Ndate').value);
</script>
