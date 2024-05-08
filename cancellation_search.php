<script language="JavaScript" src="scripts/cwcalendar/calendar.js"></script>
 <link rel="stylesheet" href="scripts/cwcalendar/cwcalendar.css"></link>
<script type="text/javascript">
	<!--
	//var TSort_Data = new Array ('my_table', '','','','','','s','s', 's','s','s','s');
	//tsRegister();
	// -->
</script>
<style type="text/css">
	.cancelled td{
		background-color:#FFA6A6;
	}
	
	.approved td{
		background-color:#A4FFA4;
	}
</style>

<?php

	$b = $_REQUEST['b'];
	$search_date = $_REQUEST['search_date'];
	$search_supplier = $_REQUEST['search_supplier'];
	$keyword = $_REQUEST['keyword'];
	
	function getSupplier($supplier_id){
		$sql = mysql_query("Select account from supplier where account_id = '$supplier_id'") or die (mysql_error());
		$r = mysql_fetch_assoc($sql);
		
		return $r['account'];
	}
	
	function getProject($project_id){
		$sql = mysql_query("Select project_name from projects where project_id = '$project_id'") or die (mysql_error());
		$r = mysql_fetch_assoc($sql);
		
		return $r['project_name'];
	}
	
	function getWork($work_category_id){
		$sql = mysql_query("Select work from work_category where work_category_id = '$work_category_id'") or die (mysql_error());
		$r = mysql_fetch_assoc($sql);
		
		return $r['work'];
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
        	PO # : <br />
            <input type='text' name='keyword' class='textbox3' value='<?=$keyword?>'>
        </div>
      	<div class="inline">
            Date : <br />
            <input type="text" class="textbox3 datepicker" name="search_date" value="<?=$_REQUEST['search_date']?>" />
        </div>
        
        <input type="submit" name="b" value="Search" />
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
		
			$sql = "Select * from po_cancellation where po_header_id like '%$keyword%' and status != 'C'";
						  
			if($search_date){			  
			$sql .= " and date = '$search_date'";
			}
			
			$sql .= " order by date desc";
				
			$pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
                    
			$i=$limitvalue;
			$rs = $pager->paginate();
		?>
    	<tr bgcolor="#C0C0C0">				
            <th width="20">#</th>
            <!--<th></th> -->
            <th width="20"></th>  
            <th>PO #</th>   
            <th>Date</th>    
            <th>Project</th>
            <th>Work Category</th>           
            <th>Supplier</th>
            <th>Status</th>
        </tr>    
		<?php								
			while($r=mysql_fetch_assoc($rs)) {
				
				$cancellation_id 	= $r['cancellation_id'];
				$po_header_id 		= $r['po_header_id'];
				$project_id			= $r['project_id'];
				$project_name		= $options->attr_Project($project_id,'project_name');
				$work_category_id 	= $r['work_category_id'];
				$work_category  = $options->attr_workcategory($work_category_id,'work');	
				$status 			= $r['status'];
				$supplier_id 		= $r['supplier_id'];
				
				echo '<td width="20">'.++$i.'</td>';
				echo '<td width="15"><a href="admin.php?view=5b94ad18bbc6c24c4123&cancellation_id='.$r[cancellation_id].'" title="Show Details"><img src="images/edit.gif" border="0"></a></td>';
				echo '<td>'.str_pad($r['po_header_id'],7,"0",STR_PAD_LEFT).'</td>';		
				echo '<td>'.date("F j, Y",strtotime($r['date'])).'</td>';					
				echo '<td>'.$project_name.'</td>';	
				echo '<td>'.$work_category.'</td>';			
				echo '<td>'.getSupplier($supplier_id).'</td>';	
				echo '<td>'.$options->getTransactionStatusName($r[status]).'</td>';	
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
    }
    ?>
</div>
</form>