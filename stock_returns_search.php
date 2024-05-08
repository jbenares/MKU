<script type="text/javascript">
	<!--
	//var TSort_Data = new Array ('my_table', '','','','','','s','s', 's','s','s','s');
	//tsRegister();
	// -->
</script>

<?php

	$b = $_REQUEST['b'];
	
	$return_header_id		= $_REQUEST['return_header_id'];
	$return_header_id_pad	= str_pad($return_header_id,7,0,STR_PAD_LEFT);
	
	$project_id			= $_REQUEST['project_id'];
	$project_name		= $options->attr_Project($project_id,'project_name');
	$project_code		= $options->attr_Project($project_id,'project_code');
	$project_name_code	= ($project_id)?"$project_name - $project_code":"";
	
	$keyword = $_REQUEST['keyword'];
	$checkList = $_REQUEST['checkList'];
	$field		= $_REQUEST['field'];
	
	if($b=='Cancel') {
	  if(!empty($checkList)) {
		foreach($checkList as $ch) {	

			$query="
				update
					return_header
				set
					status='C'
				where
					return_header_id = '$ch'
			";
			mysql_query($query);
			
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
	            <input type='text' name='keyword' class='textbox3' value='<?=$keyword?>'>
           	</div>
            <select class="select" name="field">
                	<option value="return_header_id" <?php if($field=="return_header_id") echo "selected='selected'"; ?>>Return #</option>
                    <option value='h.project_id' <?php if($field=="h.project_id") echo "selected='selected'"; ?>>Project #</option>
                    <option value='project_name' <?php if($field=="project_name") echo "selected='selected'"; ?>>Project Name</option>
	        </select>
            
            <input type="submit" name="b" value="Search" />
            <input type="submit" name="b" value="Cancel" onclick="return approve_confirm();" />
        </div>
      	<input type="button" value="Print" onclick="printIframe('JOframe');" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;" id="content">
    <?php
	if($b!="Print"){
    ?>
    <table id="my_table" cellspacing="2" cellpadding="5" width="100%" align="center" class="search_table">
    	<?php
			$page = $_REQUEST['page'];
			if(empty($page)) $page = 1;
			 
			$limitvalue = $page * $limit - ($limit);
		
			$sql = "
				select
					*
				from
					return_header as h, projects as p
				where
					h.project_id = p.project_id
			";
			
			if(!empty($field)){
			$sql.="
			and
				$field like '%$keyword%'	
				
			";	
			}
						  
				
			$pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
                    
			$i=$limitvalue;
			$rs = $pager->paginate();
		?>
        <thead>
    	<tr bgcolor="#C0C0C0">				
            <th width="20">#</th>
            <th width="20" align="center"><input type="checkbox"  name="checkAll" value="Comfortable with" onclick="javascript:check_all('_form', this)" title="Check/Uncheck All" /></th>
            <th width="20"></th>
            <th width="20"></th>    
            <th>Return #</th>
            <th>Date</th>
            <th>Project</th>
            <th>Scope of Work</th>
            <th>Work Category</b></th>
            <th>Sub Work Category</b></th>
            <th>Remarks</th>
            <th>Status</th>
        </tr>  
        </thead>      
		<?php		
		$i=1;						
		while($r=mysql_fetch_assoc($rs)) {
			$return_header_id	= $r['return_header_id'];
			$return_header_id_pad	= str_pad($return_header_id,7,0,STR_PAD_LEFT);
			$project_id			= $r['project_id'];
			$project_name		= $options->attr_Project($project_id,'project_name');
			$project_code		= $options->attr_Project($project_id,'project_code');
			$project_name_code	= ($project_id)?"$project_name - $project_code":"";
			$date				= $r['date'];
			
			$remarks			= $r['remarks'];
			$status				= $r['status'];
			
			$scope_of_work		= $r['scope_of_work'];
			$work_category_id 	= $r['work_category_id'];
			$work_category  = $options->attr_workcategory($work_category_id,'work');
			$sub_work_category_id = $r['sub_work_category_id'];
			$sub_work_category  = $options->attr_workcategory($sub_work_category_id,'work');
		?>
            <tr bgcolor="<?=$transac->row_color($i)?>">
            <td width="20"><?=$i++?></td>
            <td><input type="checkbox" name="checkList[]" value="<?=$return_header_id?>" onclick="document._form.checkAll.checked=false"></td>
            <td width="15"><a href="admin.php?view=d6d21a3f95099992ff29&return_header_id=<?=$return_header_id?>" title="Show Details"><img src="images/edit.gif" border="0"></a></td>
            <td width="15"><a href="admin.php?view=<?=$view?>&return_header_id=<?=$return_header_id?>&b=Print"><img src="images/action_print.gif" border="0"></a></td>
            <td><?=$return_header_id_pad?></td>
            <td><?=date("F j, Y",strtotime($date))?></td>
          	<td><?=$project_name_code?></td>	
            <td><?=$scope_of_work?></td>
            <td><?=$work_category?></td>	
            <td><?=$sub_work_category?></td>	
            <td><?=$remarks?></td>	
            <td><?=$options->getTransactionStatusName($status)?></td>	
            </tr>
       	<?php
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
	}else{	
    ?>
    	<iframe id='JOframe' name='JOframe' frameborder='0' src='print_report_stocks_return.php?id=<?=$return_header_id?>' width='100%' height='500'>
       	</iframe>
    <?php
	}
    ?>
    </div>
</div>
</form>