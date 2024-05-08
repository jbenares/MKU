<script type="text/javascript">
	<!--
	//var TSort_Data = new Array ('my_table', '','','','','','s','s', 's','s','s','s');
	//tsRegister();
	// -->
</script>
<style>
	.approved td{
		background-color:#9BF999;
	}
	.disapproved td{
		background-color:#F00;
	}
	.pending td{
		background-color:#FF0;
	}
</style>

<?php

	$b = $_REQUEST['b'];

	$pr_header_id		= $_REQUEST['pr_header_id'];
	$pr_header_id_pad	= str_pad($pr_header_id,7,0,STR_PAD_LEFT);
	$project_id			= $_REQUEST['project_id'];
	$project_name		= $options->attr_Project($project_id,'project_name');
	$project_code		= $options->attr_Project($project_id,'project_code');
	$project_name_code	= ($project_id)?"$project_name - $project_code":"";
	$description		= $_REQUEST['description'];

	$keyword = $_REQUEST['keyword'];
	$checkList = $_REQUEST['checkList'];

	if($b=='Approve') {
	  if(!empty($checkList)) {
		foreach($checkList as $ch) {

			$query="
				update
					pr_header
				set
					approval_status='A'
				where
					pr_header_id='$ch'
			";
			mysql_query($query);

			$options->insertAudit($ch,'pr_header_id','A');

		}
	  }
	}else if($b=='Disapprove') {
	  if(!empty($checkList)) {
		foreach($checkList as $ch) {

			$query="
				update
					pr_header
				set
					approval_status='D'
				where
					pr_header_id='$ch'
			";
			mysql_query($query);

			$options->insertAudit($ch,'pr_header_id','D');

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
            	PR # : <br />
	            <input type='text' name='keyword' class='textbox3' value='<?=$keyword?>'>
           	</div>
            <input type="submit" name="b" value="Search" />
            <!--
            <input type="submit" name="b" value="Approve" onclick="return approve_confirm();" />
            <input type="submit" name="b" value="Disapprove" onclick="return approve_confirm();" />
           	-->
        </div>
      	<input type="button" value="Print" onclick="printIframe('JOframe');" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;" id="content">
    <?php
	if($b!="Print"){
    ?>
    <table id="my_table" cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table">
    	<?php
			$page = $_REQUEST['page'];
			if(empty($page)) $page = 1;

			$limitvalue = $page * $limit - ($limit);

			$sql = "
				select
					  *
				from
					pr_header as h
				where
					pr_header_id like '%$keyword%'
				and
					status='F'
				order by
					date desc
			";


			$pager = new PS_Pagination($conn,$sql,$limit,$link_limit);

			$i=$limitvalue;
			$rs = $pager->paginate();
		?>
    	<tr>
            <th width="20">#</th>
            <th width="20" align="center"><input type="checkbox"  name="checkAll" value="Comfortable with" onclick="javascript:check_all('_form', this)" title="Check/Uncheck All" /></th>
            <th width="20"></th>
            <th width="20"></th>
            <th>PR #</th>
            <th>Date Requested</th>
            <th>Date Needed</th>
            <th>Project</b></th>
            <th>Scope of Work</th>
            <th>Work Category</b></th>
            <th>Sub Work Category</b></th>
            <th>Description</th>
            <th>Status</th>
            <th>Approval Status</th>
        </tr>
		<?php
		$i=1;
		while($r=mysql_fetch_assoc($rs)) {
			$pr_header_id		= $r['pr_header_id'];
			$pr_header_id_pad	= str_pad($pr_header_id,7,0,STR_PAD_LEFT);
			$project_id			= $r['project_id'];
			$project_name		= $options->attr_Project($project_id,'project_name');
			$project_code		= $options->attr_Project($project_id,'project_code');
			$project_name_code	= ($project_id)?"$project_name - $project_code":"";
			$description		= $r['description'];
			$status				= $r['status'];
			$approval_status	= $r['approval_status'];

			$date				= $r['date'];
			$date_needed		= $r['date_needed'];

			$scope_of_work		= $r['scope_of_work'];
			$work_category_id 	= $r['work_category_id'];
			$work_category  = $options->attr_workcategory($work_category_id,'work');
			$sub_work_category_id = $r['sub_work_category_id'];
			$sub_work_category  = $options->attr_workcategory($sub_work_category_id,'work');
		?>
            <tr
            <?php
				if($approval_status == "P"){
					echo "class='pending'";
				}else if($approval_status == "A"){
					echo "class='approved'"	;
				}else if($approval_status == "D"){
					echo "class='disapproved'";
				}
            ?>
            >
                <td width="20"><?=$i++?></td>
                <td><input type="checkbox" name="checkList[]" value="<?=$pr_header_id?>" onclick="document._form.checkAll.checked=false"></td>
                <td width="15"><a href="admin.php?view=5f32fa918fa844c4fbe6&pr_header_id=<?=$pr_header_id?>" title="Show Details"><img src="images/edit.gif" border="0"></a></td>
                <td width="15"><a href="admin.php?view=<?=$view?>&pr_header_id=<?=$pr_header_id?>&b=Print"><img src="images/action_print.gif" border="0"></a></td>
                <td><?=$pr_header_id_pad?></td>
                <td><?=date("F j, Y",strtotime($date))?></td>
                <td><?=date("F j, Y",strtotime($date_needed))?></td>
                <td><?=$project_name_code?></td>
                <td><?=$scope_of_work?></td>
                <td><?=$work_category?></td>
                <td><?=$sub_work_category?></td>
                <td><?=$description?></td>
                <td><?=$options->getTransactionStatusName($status)?></td>
                <td><?=$options->getApprovalStatus($approval_status)?></td>
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
    	<iframe id='JOframe' name='JOframe' frameborder='0' src='print_report_purchase_request.php?id=<?=$_REQUEST[pr_header_id]?>' width='100%' height='500'>
       	</iframe>
    <?php
	}
    ?>
    </div>
</div>
</form>
