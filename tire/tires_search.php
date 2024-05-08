<script type="text/javascript">
	<!--
	//var TSort_Data = new Array ('my_table', '','','','','','s','s', 's','s','s','s');
	//tsRegister();
	// -->
</script>
<style type="text/css">
	.search_table td.approved{
		color:#090;	
		font-weight:bolder;
	}
	
	.search_table td.pending{
		color:#FF0;
		font-weight:bolder;
	}
</style>	
<?php

	$b = $_REQUEST['b'];
	
	$keyword = $_REQUEST['keyword'];
	$checkList = $_REQUEST['checkList'];
	
	if($b=='Junk') {
	  if(!empty($checkList)) {
		foreach($checkList as $ch) {	

			$query="
				insert into
					junk_tires
				set
					branding_num = '$ch',
					date_junked = NOW()
			";
			mysql_query($query);
			//$options->insertAudit($ch,'pr_header_id','C');
		}
	  }
	}else if($b=='Update Type') {
	  if(!empty($checkList)) {
		foreach($checkList as $ch) {	

			$query="
				update productmaster set tire_type = '$_REQUEST[type_id]' where branding_number = '$ch'
			";
			mysql_query($query);
			//$options->insertAudit($ch,'pr_header_id','C');
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
        	<img src="images/find.png" />
        	<div class="inline">
            	Branding #: <br />
	            <input type='text' name='keyword' class='textbox3' value='<?=$keyword?>'>
           	</div>
			<div class="inline">
            	Tire Type: <br />
	            <?=$options->getTableAssoc($_REQUEST['type_id'],"type_id","Select Type","select * from tire_type order by type_id asc","type_id","type_name")?>
           	</div>
        </div>
            <input type="submit" name="b" value="Search" />
            <input type="submit" name="b" value="Junk" onclick="return confirm('Are you sure you want to do this?')"/>
			<input type="submit" name="b" value="Update Type" onclick="return confirm('Are you sure you want to do this?')"/>
        </div>
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;" id="content">
    <?php
		$page = $_REQUEST['page'];
		if(empty($page)) $page = 1;
		 
		$limitvalue = $page * $limit - ($limit);
		
		$sql = "
			 select
				*
			 from
				productmaster
			 where
			 	branding_number 
			 like 
			 	'%$keyword%'
			 AND
			 	categ_id1 = '10'
			 AND
			 	categ_id2 = '30'
			 AND
			 	branding_number !=''

		";
		if(!empty($_REQUEST['type_id']) && $b!="Update Type"){
			$sql.=" and tire_type='$_REQUEST[type_id]'";
		}
		// /echo $sql;		
		$pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
				
		$i=$limitvalue;
		$rs = $pager->paginate();
		
		
		$links = "$view";
		$links.= ( $keyword ) ? "&keyword=$keyword" : "";
		$links.= ( $_REQUEST[type_id] ) ? "&type_id=$_REQUEST[type_id]" : "";
		$page = $pager->renderFullNav($links)
	?>
    <div class="pagination">
		<?=$page?>                      
    </div>
    <table id="my_table" cellspacing="2" cellpadding="5" width="100%" align="center" class="search_table">
        <thead>
            <tr bgcolor="#C0C0C0">				
                <th width="20">#</th>
                <th width="20" align="center"><input type="checkbox"  name="checkAll" value="Comfortable with" onclick="javascript:check_all('_form', this)" title="Check/Uncheck All" /></th>  
                <th>Branding #</th>
                <th>Stock</th>
				<th>Tire Type</th>
               	<th>Status</th>
            </tr>  
        </thead>      
		<?php							
		while($r=mysql_fetch_assoc($rs)) {
			$query=mysql_query("SELECT * FROM junk_tires WHERE branding_num = '$r[branding_number]'");
			$display="";
			if(mysql_num_rows($query)){
				$display="disabled=disabled";
			}
		?>
            <tr bgcolor="<?=$transac->row_color(++$i)?>">
            <td width="20"><?=$i?></td>
            <td><input type="checkbox" <?=$display?> name="checkList[]" value="<?=$r[branding_number]?>" onclick="document._form.checkAll.checked=false"></td>
			<td style="font-weight:bolder;text-align:center;"><?=$r[branding_number]?></td>
			<td style="font-weight:bolder;text-align:center;"><?=$r[stock]?></td>
			<td style="font-weight:bolder;text-align:center;"><?=$options->getAttribute("tire_type","type_id",$r['tire_type'],"type_name")?></td>
			<?php
			if(mysql_num_rows($query)){
				echo '<td style="text-align:center;font-weight:bolder;color:red;">JUNKED</td>';
			}else{
				echo '<td style="text-align:center;font-weight:bolder;color:green;">SAVED</td>';
			}
			?>
			
			<?php
		}
        ?>
			
			</tr>
        </table>
        <div class="pagination">
	        <?=$page?>                
        </div>
    <?php
	
    ?>
    </div>
</div>
</form>