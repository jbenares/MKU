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
            From:<br />
            <input type="text" class="textbox3 datepicker" id='from' name="from" value='<?php echo $_REQUEST['from'];?>' readonly='readonly' >
        </div>
		
        <div class="inline">
            To:<br />
            <input type="text" class="textbox3 datepicker" id='to' name="to" value='<?php echo $_REQUEST['to'];?>' readonly='readonly' >
        </div>
        
        <div>
        	Subcontractor<br />
            <?=$options->getTableAssoc($_REQUEST['supplier_id'],'supplier_id',"Select Subcon","select * from supplier where subcon = '1' order by account asc",'account_id','account')?>
        </div>
		
		<div>
			Project<br />
			<input type="text" class="textbox project" value="<?=lib::getAttribute('projects','project_id',$_REQUEST['project_id'],'project_name')?>">
            <input type="hidden" name="project_id" value="" >
		</div>
        <div>
            	Work Category<br />
                <?=$options->option_workcategory($_REQUEST['work_category_id'],'work_category_id','Select Work Category')?>
        </div>
           <div>
            	Sub Work Category<br />
                	<div id="subworkcategory">
                        <select>
                            <option>Select Sub Work Category</option> 
                        </select>	
                    </div>
          </div>
        <br/>
     	<input type="submit" value="Generate Report"  />
		<!--<input type="reset" value="Reset"  />-->
        <input type="button" value="Print" onclick="printIframe('JOframe');" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;" id="content">
     <?php if(!empty($_REQUEST['from']) && !empty($_REQUEST['to'])){ ?>
   		<iframe id="JOframe" name="JOframe" frameborder="0" src="print_ap_subcon_balance_report.php?sub_work_category_id=<?=$_REQUEST['sub_work_category_id']?>&work_category_id=<?=$_REQUEST['work_category_id']?>&from=<?=$_REQUEST['from']?>&to=<?=$_REQUEST['to']?>&supplier_id=<?=$_REQUEST['supplier_id']?>&project_id=<?=$_REQUEST[project_id]?>" width="100%" height="500">
        </iframe>
    <?php }?>
    </div>
</div>
</form>
<script type="text/javascript">
    jQuery(function () {
        j("#work_category_id").change(function () {
            xajax_display_subworkcategory(this.value);
        });

        xajax_display_subworkcategory('<?=$_REQUEST['work_category_id']?>', '<?=$_REQUEST['sub_work_category_id']?>');
});
</script>