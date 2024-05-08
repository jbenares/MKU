<script type="text/javascript">
	<!--
	//var TSort_Data = new Array ('my_table', '','','','','','s','s', 's','s','s','s');
	//tsRegister();
	// -->
</script>
<?php

	$b = $_REQUEST['b'];
	$keyword = $_REQUEST['keyword'];
	$checkList = $_REQUEST['checkList'];
	
	$field	= $_REQUEST['field'];
	$project_id = $_REQUEST['project_id'];
	
	
	
	if($b=="Add Budget"){
		header("Location: admin.php?view=29a6d2e5c71d0ae94395");	
	}
	
	
	if($b=='Cancel') {
	  if(!empty($checkList)) {
		foreach($checkList as $ch) {	

			$query="
				update
					budget_header
				set
					status='C'
				where
					budget_header_id='$ch'
			";
			mysql_query($query) or die(mysql_error());
			$options->insertAudit($ch,'budget_header_id','C');
		}
	  }
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

<form name="_form" action="" method="post">
<div class=form_layout>
	<div class="module_title"><img src='images/user_orange.png'><?=$transac->getMname($view);?></div>
    <div class="module_actions">       
        <div style="display:inline-block;">
        	<div class="inline">
            	Budget # : <br />
                <input type="text" name="budget_header_id" class="textbox3" value="<?=$_REQUEST['budget_header_id']?>"  />
            </div>	
        	<div class='inline'>
                Project : <br />  
                <input type="text" class="textbox" id="project_name" value="<?=$_REQUEST['project_name']?>" onclick="this.select();"  />
                <input type="hidden" name="project_id"  id="project_id" value="" title="Please select Project" />
            </div>
            
            <input type="submit" name="b" value="Search" />
            <!--<input type="submit" name="b" value="Cancel" onclick="return approve_confirm();" />-->
        </div>
      	<input type="button" value="Print" onclick="printIframe('JOframe');" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;" id="content">
		<?php
        if($b!="Print"){
        ?>
    	<?php
		$page = $_REQUEST['page'];
		if(empty($page)) $page = 1;
		 
		$limitvalue = $page * $limit - ($limit);
	
		$sql = "select
					  budget_header_id,
					  h.project_id,
					  project_name,
					  h.description,
					  h.status,
					  work_category_id,
					  sub_work_category_id
				 from
					  budget_header as h,
					  projects as p
				 where
					h.project_id = p.project_id
				
			";
		if($_REQUEST['budget_header_id']){
			$sql.="
					and
					  h.budget_header_id like '$_REQUEST[budget_header_id]%'
			";	
		}
		if(!empty($project_id)){
			$sql.="
					and
					  h.project_id ='$project_id'
			";	
		}
		
		$sql.="
			order by
				project_name asc,
				work_category_id asc, 
				sub_work_category_id asc
				
		";	
			
		$pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
				
		$i=$limitvalue;
		$rs = $pager->paginate();
		?>
        
		<?php		
		if(mysql_num_rows($rs)<=0):
		?>	
			<div id="status_update" class="ui-state-error ui-corner-all" style="padding: 0px 0.7em; text-align:left;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right:.3em;"></span>NO INHOUSE BUDGET &nbsp; <input type="submit" name="b" value="Add Budget"  /></p></div>'			
      	<?php
		else:
		?>
			<table id="my_table" cellspacing="2" cellpadding="5" width="100%" align="left" class="search_table" style="text-align:left;">
            <thead>
            <tr bgcolor="#C0C0C0">				
                <td width="20"><b>#</b></td>
                <td width="20" align="center"><input type="checkbox"  name="checkAll" value="Comfortable with" onclick="javascript:check_all('_form', this)" title="Check/Uncheck All" /></td>
                <td width="20"></td>
                <td width="20"></td>    
                <td><b>Budget #</b></td>
                <td><b>Project</b></td>
                <td><b>Work Category</b></td>
                <td><b>Sub Work Category</b></td>
                <td><b>Status</b></td>
            </tr>  
            </thead>     
			<?php			
			while($r=mysql_fetch_assoc($rs)) {
				$budget_header_id		= $r['budget_header_id'];
				$project_name			= $r['project_name'];
				$status					= $r['status'];
				$work_category_id		= $r['work_category_id'];
				$sub_work_category_id	= $r['sub_work_category_id'];
				
				$work_category = $options->attr_workcategory($work_category_id,'work');
				$sub_work_category = $options->attr_workcategory($sub_work_category_id,'work');
				
				
				echo '<tr bgcolor="'.$transac->row_color($i).'">';
				echo '<td width="20">'.++$i.'</td>';
				echo '<td><input type="checkbox" name="checkList[]" value="'.$budget_header_id.'" onclick="document._form.checkAll.checked=false"></td>';
				echo '<td width="15"><a href="admin.php?view=29a6d2e5c71d0ae94395&budget_header_id='.$budget_header_id.'" title="Show Details"><img src="images/edit.gif" border="0"></a></td>';
				echo '<td width="15"><a href="admin.php?view='.$view.'&budget_header_id='.$budget_header_id.'&b=Print"><img src="images/action_print.gif" border="0"></a></td>';
				echo '<td>'.str_pad($budget_header_id,7,"0",STR_PAD_LEFT).'</td>';
				echo '<td>'.$project_name.'</td>';	
				echo '<td>'.$work_category.'</td>';	
				echo '<td>'.$sub_work_category.'</td>';	
				echo '<td>'.$options->getTransactionStatusName($status).'</td>';	
				echo '</tr>';
			}
       		?>
            </table>
            <table cellspacing="2" cellpadding="5" width="100%" class="search_table">
            <tr>
                <td colspan="5" align="left">
                    <?php
                        echo $pager->renderFullNav("$view");
                    ?>                
                </td>
            </tr>
            </table>
        <?php
		endif;
        ?>
    <?php
	}else{	
    ?>
    	<iframe id='JOframe' name='JOframe' frameborder='0' src='print_report_budget.php?id=<?=$_REQUEST[budget_header_id]?>' width='100%' height='500'>
       	</iframe>
    <?php
	}
    ?>
    </div>
</div>
</form>