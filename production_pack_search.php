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
	
	if($b=='Cancel') {
	  if(!empty($checkList)) {
		foreach($checkList as $ch) {	

			$query="
				update
					packaging
				set
					status='C'
				where
					packaging_id = '$ch'
			";
			mysql_query($query) or die(mysql_error());
			
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
            <input type='text' name='keyword' class='textbox3' value='<?=$keyword?>'>
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
		
			$sql = "select
						  *
					 from
					 	  packaging
					 where
					 	  packaging_id like '%$keyword%'
					order 
						by date desc
				";
						  
				
			$pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
                    
			$i=$limitvalue;
			$rs = $pager->paginate();
		?>
        <thead>
    	<tr bgcolor="#C0C0C0">				
            <td width="20"><b>#</b></td>
            <td width="20" align="center"><input type="checkbox"  name="checkAll" value="Comfortable with" onclick="javascript:check_all('_form', this)" title="Check/Uncheck All" /></td>
            <td width="20"></td>
            <td width="20"></td>    
            <td><b>Packaging #</b></td>
            <td><b>Date</b></td>
            <td><b>Piece Product</b></td>
            <td><b>Piece Quantity</b></td>
            <td><b>Pack Product</b></td>
            <td><b>Pack Quantity</b></td>
            <td><b>Status</b></td>
        </tr>  
        </thead>      
		<?php		
			$i=1;						
			while($r=mysql_fetch_assoc($rs)) {
				$date				= $r['date'];
				$packaging_id		= $r['packaging_id'];
				$piece_stock_id		= $r['piece_stock_id'];
				$piece_stock		= $options->attr_stock($piece_stock_id,'stock');
				$piece_quantity		= $r['piece_quantity'];
				$pack_stock_id		= $r['pack_stock_id'];
				$pack_stock			= $options->attr_stock($pack_stock_id,'stock');
				$pack_quantity		= $r['pack_quantity'];
				$status				= $r['status'];
				
				echo '<tr bgcolor="'.$transac->row_color($i).'">';
				echo '<td width="20">'.$i++.'</td>';
				echo '<td><input type="checkbox" name="checkList[]" value="'.$packaging_id.'" onclick="document._form.checkAll.checked=false"></td>';
				echo '<td width="15"><a href="admin.php?view=b28e2bf669cfd225bb74&packaging_id='.$packaging_id.'" title="Show Details"><img src="images/edit.gif" border="0"></a></td>';
				echo '<td width="15"><a href="admin.php?view=1fd51d23ed1b7615432b&packaging_id='.$packaging_id.'&b=Print"><img src="images/action_print.gif" border="0"></a></td>';
				echo '<td>'.str_pad($packaging_id,7,"0",STR_PAD_LEFT).'</td>';	
				echo '<td>'.$date.'</td>';	
				echo '<td>'.$piece_stock.'</td>';
                echo '<td class="align-right">'.number_format($piece_quantity,2,'.',',').'</td>';
				echo '<td>'.$pack_stock.'</td>';
                echo '<td class="align-right">'.number_format($pack_quantity,2,'.',',').'</td>';				
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
	}else{	
    ?>
    	<iframe id='JOframe' name='JOframe' frameborder='0' src='print_report_productionByPack.php?id=<?=$_REQUEST[packaging_id]?>' width='100%' height='500'>
       	</iframe>
    <?php
	}
    ?>
    </div>
</div>
</form>