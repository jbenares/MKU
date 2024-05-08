<?php
$from_date = $_REQUEST['from_date'];
$to_date	= $_REQUEST['to_date'];
$check="";
if(isset($_REQUEST[subtotal])){
	$check="checked=checked";
}
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
<?php
$b = $_REQUEST['b'];
?>
<form name="_form" action="" method="post">
<div class=form_layout>
	<div class="module_title"><img src='images/user_orange.png'><?=$transac->getMname($view);?></div>
    <div class="module_actions">       
        
		<table>
        	<tr>
            	<td>Project</td>
                <td>
                	<input type="text" class="textbox" id="project_name" name="project_name" value="<?=$_REQUEST['project_name']?>" onclick="this.select();"  />
		            <input type="hidden" name="project_id"  id="project_id" value="<?=$_REQUEST['project_id']?>" class="required" title="Please select Project" />
               	</td>
            </tr>
        	<tr>
            	<td>Work Category</td>
                <td> <?=$options->option_workcategory($_REQUEST['work_category_id'],'work_category_id','Select Work Category')?></td>
            </tr>
            <tr>
            	<td>Sub Work Category</td>
                <td>
                	<div id="subworkcategory">
                        <select>
                            <option>Select Sub Work Category</option> 
                        </select>	
                    </div>
                </td>
            </tr>
            <tr>
            	<td>From Date :</td>
                <td><input type="text" class="textbox datepicker" name="from_date" value='<?=$from_date?>' readonly='readonly' ></td>
            </tr>
            <tr>
            	<td>To Date :</td>
                <td><input type="text" class="textbox datepicker" name="to_date" value='<?=$to_date?>' readonly='readonly' ></td>
            </tr>
            <tr>
            	<td align="right"><input type="checkbox" name="subtotal" <?=$check?> value="1" /></td>
                <td>Check if you want to view the Subtotal Only </td>
            </tr>
        </table>        	
           
      	<input type="submit" name="b" value="Generate Report"  />
        <input type="submit" name="b" value="Generate Budget Summary Report"  />
        <input type="submit" name="b" value="Generate Budget Summary per Category Report" />
        <input type="button" value="Print" onclick="printIframe('JOframe');" />
    </div>

     <?php if(!empty($_REQUEST[project_id]) && $b == "Generate Report" ) { ?>
   		<iframe id="JOframe" name="JOframe" frameborder="0" src="budget_reports/print_budget_monitoring_report.php?&id=<?=$_REQUEST['project_id']?>&work_category_id=<?=$_REQUEST['work_category_id']?>&sub_work_category_id=<?=$_REQUEST['sub_work_category_id']?>&subtotal=<?=$_REQUEST[subtotal]?>" width="100%" height="500">
        </iframe>
   	 <?php } else if(!empty($_REQUEST[project_id]) && $b == "Generate Budget Summary Report"){ ?>
     	<iframe id="JOframe" name="JOframe" frameborder="0" src="print_budget_summary.php?&id=<?=$_REQUEST['project_id']?>&work_category_id=<?=$_REQUEST['work_category_id']?>&sub_work_category_id=<?=$_REQUEST['sub_work_category_id']?>" width="100%" height="500">
        </iframe>
     <?php } else if(!empty($_REQUEST[project_id]) && $b == "Generate Budget Summary per Category Report"){ ?>
     	<iframe id="JOframe" name="JOframe" frameborder="0" src="print_budget_summary_per_category.php?&id=<?=$_REQUEST['project_id']?>&work_category_id=<?=$_REQUEST['work_category_id']?>&sub_work_category_id=<?=$_REQUEST['sub_work_category_id']?>&from_date=<?=$from_date?>&to_date=<?=$to_date?>" width="100%" height="500">
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