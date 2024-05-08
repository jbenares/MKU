<?php
$sql = mysql_query("Select * from projects where pstatus != 'Fu' order by project_name ASC") or die (mysql_error());
?>
<script type="text/javascript">
    function do_this(){

        var checkboxes = document.getElementsByName('checklist[]');
        var button = document.getElementById('toggle');

        if(button.value == 'SELECT ALL'){
            for (var i in checkboxes){
                checkboxes[i].checked = 'FALSE';
            }
            button.value = 'DESELECT ALL'
        }else{
            for (var i in checkboxes){
                checkboxes[i].checked = '';
            }
            button.value = 'SELECT ALL';
        }
    }
</script>
<form  action="transactions/print_income_statement_project.php" target="_blank" method="post">
<div class=form_layout>
	<div class="module_title"><img src='images/user_orange.png'><?=$transac->getMname($view);?></div>
    <div class="module_actions">
		<div style="display: inline-block; padding-left: 10px; padding-rgith: 10px;width: 420px;">
			<input type="button" id="toggle" value="SELECT ALL" onClick="do_this()" />
		</div>
		<br />
		<?php while($r = mysql_fetch_assoc($sql)){ ?>
		<div style="display: inline-block; padding-left: 10px; padding-rgith: 10px;width: 420px;"><input type="checkbox" id="container" name="checklist[]" value="<?=$r['project_id']?>" /><?=$r['project_name']?></div>
		<?php } ?>
    </div>
    <div class="module_actions">              
        <div class="inline">
            Starting Date<br />
            <input type="text" class="textbox3 datepicker" id='startingdate' name="startingdate" value='<?php echo $_REQUEST[startingdate];?>' readonly='readonly' >
        </div>
        
        <div class="inline">
            Ending Date<br />
            <input type="text" class="textbox3 datepicker" id='endingdate' name="endingdate" value='<?php echo $_REQUEST[endingdate];?>' readonly='readonly' >
        </div>
        
     	<input type="submit" value="Generate Report"  onclick="openinnewTab();" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    </div>
</div>
</form>
