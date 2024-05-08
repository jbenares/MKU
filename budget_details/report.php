<?php

	$project_name		= $_REQUEST['project_name'];
	$work_category_id		= $_REQUEST['work_category_id'];
	$section		= $_REQUEST['section'];
	
	$project_id		= $_REQUEST['project_id'];
	$sub_work_category_id = $_REQUEST['sub_work_category_id'];
	
	
	
	$b = $_REQUEST['b'];
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
				
			<div style="display:inline-block;">
				Project : <br />
			   <input type="text" class="textbox" name="project_name" id="project_name" value="<?=$project_name?>" onclick="this.select();"  />
			   <input type="hidden" name="project_id"  id="project_id" value="<?=$project_id?>" class="required" title="Please select Project" />
			</div>
			
			<div style="display:inline-block;">
				Work Category : <br />
			   <?=$options->option_workcategory($work_category_id,'work_category_id','Select Work Category')?>
			</div>
			<div id="subworkcategory_div" style="display:none;" class="inline">
					Sub Work Category :
					<div id="subworkcategory">
					
					</div>
				</div>
			<div style="display:inline-block;">
				Section : <br />
				<select name="section">
					<option value="a">Select Section</option>
			   <?php
					$sec = "SELECT * FROM sections WHERE is_deleted !='1'";
					$rs_sec = mysql_query($sec);
					while($rw_sec = mysql_fetch_assoc($rs_sec))
					{
			   ?>
						<option value="<?php echo $rw_sec['section_id']; ?>"><?php echo $rw_sec['section_name']; ?></option>
				<?php
					}
				?>
			   </select>
			</div>
			
					
			<input type="submit" name="b" value="Generate Report"  />
			<input type="submit" name="b" value="Budget Report" class="buttons" />
			<input type="button" value="Print" onclick="printIframe('JOframe');" />
		</div>
		<?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
		<div style="padding:3px; text-align:center;" id="content">
		 <?php  if(!empty($project_id) && !empty($work_category_id) && !empty($sub_work_category_id) && !empty($section) ) { 
				if($b == 'Generate Report'){
					?>
					<iframe id="JOframe" name="JOframe" frameborder="0" src="budget_details/print_report.php?
						project_id=<?=$project_id?>&work_category_id=<?=$work_category_id?>&sub_work_category_id=<?=$sub_work_category_id?>&section=<?=$section?>
						" width="100%" height="500">
					</iframe>
		<?php 
                        }else if($b == 'Budget Report'){
				?>
					<iframe id="JOframe" name="JOframe" frameborder="0" src="budget_details/print_report_budget.php?
						project_id=<?=$project_id?>&work_category_id=<?=$work_category_id?>&sub_work_category_id=<?=$sub_work_category_id?>&section=<?=$section?>
						" width="100%" height="500">
					</iframe>
				<?php
			}
		}
		?>
		</div>
	</div>
</form>
<script type="text/javascript">
j(function(){	

	j("#work_category_id").change(function(){
		xajax_display_subworkcategory(this.value);
	});
	
	<?php
	if(!empty($status)){
	?>
		xajax_display_subworkcategory('<?=$work_category_id?>','<?=$sub_work_category_id?>');
		xajax_update_scope_of_work('<?=$project_id?>','<?=$scope_of_work?>');
	<?php
	}
	?>
});
</script>