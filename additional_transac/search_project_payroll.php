<script type="text/javascript">
	<!--
	//var TSort_Data = new Array ('my_table', '','','','','','s','s', 's','s','s','s');
	//tsRegister();
	// -->
</script>

<?php

	$b = $_REQUEST['b'];
	
	$project_payroll_header_id		= $_REQUEST['project_payroll_header_id'];
	$project_payroll_header_id_pad	= str_pad($project_payroll_header_id,7,0,STR_PAD_LEFT);
	
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
					project_payroll_header
				set
					status='C'
				where
					project_payroll_header_id = '$ch'
			";
			mysql_query($query);
			$options->insertAudit($ch,'project_payroll_header_id','C');
			
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
                	<option value="project_payroll_header_id" <?php if($field=="project_payroll_header_id") echo "selected='selected'"; ?>>Payroll #</option>
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
					project_payroll_header as h, projects as p
				where
					h.project_id = p.project_id
			";
			
			if(!empty($field)){
			$sql.="
			and
				$field like '%$keyword%'	
				
			";	
			}
			
			$sql.="
				order by date desc
			";
						  
				
			$pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
                    
			$i=$limitvalue;
			$rs = $pager->paginate();
		?>
        <thead>
    	<tr bgcolor="#C0C0C0">				
            <th width="20">#</th>
            <th width="20" align="center"><input type="checkbox"  name="checkAll" value="Comfortable with" onclick="javascript:check_all('_form', this)" title="Check/Uncheck All" /></th>
            <th width="20"></th>
            <!-- <th width="20"></th>     -->
            <th>Payroll #</th>
            <th>Date</th>
            <th>Project</th>
            <th>Foreman</th>
            <th>Total Amount</th>
            <th>Status</th>
        </tr>  
        </thead>      
		<?php		
		$i=1;						
		while($r=mysql_fetch_assoc($rs)) {

			 $sql_detail = mysql_query("select SUM(total_price) AS total from project_payroll_detail where project_payroll_header_id  = '$r[project_payroll_header_id]' and project_payroll_void='0' GROUP BY project_payroll_header_id") or die(mysql_error());
			 $fetch_total = mysql_fetch_assoc($sql_detail);

			  $sql_discount = mysql_query("select SUM(discount_amount) AS total_disc from project_payroll_discount where project_payroll_header_id  = '$r[project_payroll_header_id]' and void='0' GROUP BY project_payroll_header_id") or die(mysql_error());
			 $fetch_discount = mysql_fetch_assoc($sql_discount);

			 $total_amount = $fetch_total['total'] - $fetch_discount['total_disc'];


			$project_payroll_header_id	= $r['project_payroll_header_id'];
			$project_payroll_header_id_pad	= str_pad($project_payroll_header_id,7,0,STR_PAD_LEFT);
			$project_id			= $r['project_id'];
			$project_name		= $options->attr_Project($project_id,'project_name');
			$project_code		= $options->attr_Project($project_id,'project_code');
			$project_name_code	= ($project_id)?"$project_name - $project_code":"";
			$date				= $r['date'];

	        $foreman_id			= $r['foreman_id'];
			$fname		= $options->attr_Employee($foreman_id,'employee_fname');
			$lname		= $options->attr_Employee($foreman_id,'employee_lname');
			$foreman = $fname . " " . $lname;
			
			if($r['status'] == 'C'){
				$status = 'Cancelled';
			}
			else if($r['status'] == 'S'){
				$status = 'Saved';
			}
			else if($r['status'] == 'F'){
				$status = 'Finished';
			}
			// $remarks			= $r['remarks'];
			// $status				= $r['status'];
			
			// $scope_of_work		= $r['scope_of_work'];
			// $work_category_id 	= $r['work_category_id'];
			// $work_category  = $options->attr_workcategory($work_category_id,'work');
			// $sub_work_category_id = $r['sub_work_category_id'];
			// $sub_work_category  = $options->attr_workcategory($sub_work_category_id,'work');
		?>
            <tr bgcolor="<?=$transac->row_color($i)?>">
            <td width="20"><?=$i++?></td>
            <td><input type="checkbox" name="checkList[]" value="<?=$transfer_header_id?>" onclick="document._form.checkAll.checked=false"></td>
            <td width="15"><a href="admin.php?view=f42e3184a2pp1aa6078t&project_payroll_header_id=<?=$project_payroll_header_id?>" title="Show Details"><img src="images/edit.gif" border="0"></a></td>
            <!-- <td width="15"><a href="admin.php?view=<?=$view?>&project_payroll_header_id=<?=$project_payroll_header_id?>&b=Print" title="Print Preview"><img src="images/action_print.gif" border="0"></a></td> -->
            <td><?=$project_payroll_header_id_pad?></td>
            <td><?=date("F j, Y",strtotime($date))?></td>
          	<td><?=$project_name_code?></td>	
          	<td><?=$foreman?></td>	
          	<td><?=number_format( $fetch_total['total'] ,2)?></td>	
          	<td><?=$status?></td>	
           <!--  <td><?=$work_category?></td>	
            <td><?=$sub_work_category?></td>	
            <td><?=$remarks?></td>	
            <td><?=$options->getTransactionStatusName($status)?></td>	 -->
            </tr>
       	<?php
		}
        ?>
        </table>
        <table cellspacing="2" cellpadding="5" width="100%" class="search_table">
        <tr>
            <td colspan="5" align="left">
                <?php
                    echo $pager->renderFullNav("$view&field=$field&keyword=$keyword");
                ?>                
            </td>
      	</tr>
    	</table>
    <?php
	}else{	
    ?>
    	<iframe id='JOframe' name='JOframe' frameborder='0' src='print_report_transmittal.php?id=<?=$project_payroll_header_id?>' width='100%' height='500'>
       	</iframe>
    <?php
	}
    ?>
    </div>
</div>
</form>