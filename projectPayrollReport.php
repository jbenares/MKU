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
$query_foreman= mysql_query("select
                 *
             from
                 employee
             where
                 work_category_id='8'");

?>
<form name="_form" action="" method="post">
<div class=form_layout>
	<div class="module_title"><img src='images/user_orange.png'><?=$transac->getMname($view);?></div>
    <div class="module_actions">       
            
        <div class="inline">
             Date From<br />
            <input type="text" class="textbox3 datepicker" id='date_from' name="date_from" va>
        </div>    
           <div class="inline">
             Date To<br />
            <input type="text" class="textbox3 datepicker" id='date_to' name="date_to" va>
        </div>    
        
         <div class='inline'>
            Project : <br />  
            <input type="text" class="textbox" id="project_name" name="project_name" value="<?=$_REQUEST['project_name']?>" onclick="this.select();"  />
            <input type="hidden" name="project_id"  id="project_id" value="<?=$_REQUEST['project_id']?>" class="required" title="Please select Project" />
        </div>  
          <div class='inline'>
            Foreman : <br />  
            <select name='foreman_id'>
                    <option></option>
                    <?php while($fetch_foreman = mysql_fetch_array($query_foreman)){ 
                        $selected=($_REQUEST['foreman_id']==$fetch_foreman[employeeID])?"selected='selected'":"";
                        ?>
                    <option value="<?php echo $fetch_foreman['employeeID']; ?>" <?php echo $selected; ?>><?php echo $fetch_foreman['employee_fname'] . " " . $fetch_foreman['employee_lname']; ?></option>
                    <?php } ?>
                 </select> 
        </div>  
        
       
        
      	<input type="submit" value="Generate Report"  />
        <input type="button" value="Print" onclick="printIframe('JOframe');" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;" id="content">
     <?php
		if(!empty($_REQUEST[project_id]) || !empty($_REQUEST[foreman_id])  || !empty($_REQUEST[date_from])  || !empty($_REQUEST[date_to]))
		{ 
	?>
   		<iframe id="JOframe" name="JOframe" frameborder="0" src="printProjectPayrollReport.php?&datefrom=<?=$_REQUEST['date_from'];?>&date_to=<?=$_REQUEST['date_to'];?>&foreman_id=<?=$_REQUEST['foreman_id']?>&project_id=<?=$_REQUEST['project_id']?>" width="100%" height="500">
        </iframe>
    </div>
    <?php }?>
    </div>
</div>
</form>