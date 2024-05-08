<script type="text/javascript">
	<!--
	//var TSort_Data = new Array ('my_table', '','','','','','s','s', 's','s','s','s');
	//tsRegister();
	// -->
</script>

<?php

	$b = $_REQUEST['b'];
	
	$production_header_id		= $_REQUEST['production_header_id'];
	$production_header_id_pad	= str_pad($production_header_id,7,0,STR_PAD_LEFT);
		
	$keyword = $_REQUEST['keyword'];
	$checkList = $_REQUEST['checkList'];
	$field		= $_REQUEST['field'];
	
	if($b=='Cancel') {
	  if(!empty($checkList)) {
		foreach($checkList as $ch) {	

			$query="
				update
					production_header
				set
					status='C'
				where
					production_header_id = '$ch'
			";
			mysql_query($query);
			
			$options->insertAudit($ch,'production_header_id','C');
			
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
                	<option value="production_header_id" <?php if($field=="production_header_id") echo "selected='selected'"; ?>>Production #</option>
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
					production_header
				
			";
			
			if(!empty($field)){
			$sql.="
			where
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
            <th>Production #</th>
            <th>Date</th>
            <th>Item</th>
            <th>Formulation</th>
            <th>Actual Output</th>
            <th>Status</th>
        </tr>  
        </thead>      
		<?php		
		$i=1;						
		while($r=mysql_fetch_assoc($rs)) {
			$production_header_id	= $r['production_header_id'];
			$production_header_id_pad	= str_pad($production_header_id,7,0,STR_PAD_LEFT);
			$date				= $r['date'];			
			$stock_id			= $r['stock_id'];
			$stock				= $options->attr_stock($stock_id,'stock');
			$stockcode			= $options->attr_stock($stock_id,'stockcode');
			$stock_display	 	= "$stock - $stockcode";
			$formulation_header_id = $r['formulation_header_id'];
			$formulation_code	= $options->formulationAttr($formulation_header_id,'formulation_code');
			$actualoutput		= $r['actualoutput'];
			
			$remarks			= $r['remarks'];
			$status				= $r['status'];
		?>
            <tr bgcolor="<?=$transac->row_color($i)?>">
            <td width="20"><?=$i++?></td>
            <td><input type="checkbox" name="checkList[]" value="<?=$production_header_id?>" onclick="document._form.checkAll.checked=false"></td>
            <td width="15"><a href="admin.php?view=c46704ff5879e490212a&production_header_id=<?=$production_header_id?>" title="Show Details"><img src="images/edit.gif" border="0"></a></td>
            <td width="15"><a href="admin.php?view=<?=$view?>&production_header_id=<?=$production_header_id?>&b=Print"><img src="images/action_print.gif" border="0"></a></td>
            <td><?=$production_header_id_pad?></td>
            <td><?=date("F j, Y",strtotime($date))?></td>
            <td><?=$stock_display?></td>	
            <td><?=$formulation_code?></td>	
            <td class="align-right"><?=number_format($actualoutput,2,'.',',')?></td>	
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
    	<iframe id='JOframe' name='JOframe' frameborder='0' src='print_report_purchase_request.php?id=<?=$production_header_id?>' width='100%' height='500'>
       	</iframe>
    <?php
	}
    ?>
    </div>
</div>
</form>