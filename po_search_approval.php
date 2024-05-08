<script language="JavaScript" src="scripts/cwcalendar/calendar.js"></script>
 <link rel="stylesheet" href="scripts/cwcalendar/cwcalendar.css"></link>
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

<style type="text/css">
	.pending td{
		background-color:#FFFFA4;
	}
	
	
	.approved td{
		background-color:#A4FFA4;
	}
</style>

<form name="_form" action="" method="post">
<div class=form_layout>
	<div class="module_title"><img src='images/user_orange.png'><?=$transac->getMname($view);?></div>
    <div class="module_actions">       
        <div style="display:inline-block;">
            <input type='text' name='keyword' class='textbox3' value='<?=$keyword?>'>
            <input type="submit" name="b" value="Search" class="buttons" />
        </div>
      	<input type="button" value="Print" onclick="printIframe('JOframe');" />
        <input type="hidden" id="po_header_id" value="" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <?php
	if($b!="Print"){
    ?>
    <div style="padding:3px; text-align:center;" id="content">
    <table id="my_table" cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table">
    	<?php
			$page = $_REQUEST['page'];
			if(empty($page)) $page = 1;
			 
			$limitvalue = $page * $limit - ($limit);
		
			$sql = "select
						  *
					 from
					 	  po_header
					 where
					 	  po_header_id like '%$keyword%'
					and
						status = 'F'
					order 
						by date desc
				";
						  
				
			$pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
                    
			$i=$limitvalue;
			$rs = $pager->paginate();
		?>
    	<tr bgcolor="#C0C0C0">				
            <th width="20">#</th>
            <th width="20"></th>
            <th width="20"></th>    
            <th>PO #</th>     
            <th>Date</th>
            
            <th>Project</th>
            <th>Scope of Work</th>
            <th>Work Category</th>
            <th>Sub Work Category</th>
            
            <th>Status</th>
            <th>Approval Status</th>
        </tr>        
		<?php								
			while($r=mysql_fetch_assoc($rs)) {
				$project_id			= $r['project_id'];
				$project_name		= $options->attr_Project($project_id,'project_name');
				$scope_of_work		= $r['scope_of_work'];
				$work_category_id 	= $r['work_category_id'];
				$work_category  = $options->attr_workcategory($work_category_id,'work');
				$sub_work_category_id = $r['sub_work_category_id'];
				$sub_work_category  = $options->attr_workcategory($sub_work_category_id,'work');
				$approval_status	= $r['approval_status'];
				
				if( $approval_status == "P" ) {
					echo '<tr class="pending">';
				} else if ( $approval_status  == "A" ) {
					echo '<tr class="approved">';
				} else {
					echo '<tr>';
				}
				echo '<td width="20">'.++$i.'</td>';
				echo '<td width="15"><a href="admin.php?view=6463c8fe450f52cf5906&po_header_id='.$r[po_header_id].'" title="Show Details"><img src="images/edit.gif" border="0"></a></td>';
				echo '<td width="15"><a href="admin.php?view='.$view.'&b=Print&po_header_id='.$r[po_header_id].'" title="Print"><img src="images/action_print.gif" border="0"></a></td>';
				echo '<td>'.str_pad($r['po_header_id'],7,"0",STR_PAD_LEFT).'</td>';	
				echo '<td>'.date("F j, Y",strtotime($r['date'])).'</td>';	
				
				echo '<td>'.$project_name.'</td>';	
				echo '<td>'.$scope_of_work.'</td>';	
				echo '<td>'.$work_category.'</td>';	
				echo '<td>'.$sub_work_category.'</td>';	
				
				
				echo '<td>'.$options->getTransactionStatusName($r[status]).'</td>';	
				echo '<td>'.$options->getApprovalStatus($r[approval_status]).'</td>';	
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
    </div>
    <?php
    }else{
    ?>
    <iframe id='JOframe' name='JOframe' frameborder='0' src='print_report_po.php?id=<?=$_REQUEST['po_header_id']?>' width='100%' height='500'></iframe>
    <?php
	}
    ?>
</div>
</form>