<script language="JavaScript" src="scripts/cwcalendar/calendar.js"></script>
 <link rel="stylesheet" href="scripts/cwcalendar/cwcalendar.css"></link>


<?php

	$b = $_REQUEST['b'];
	$checkList = $_REQUEST['checkList'];
	$eq_keyword = $_REQUEST['eq_keyword'];
	
	if($b=='Delete Selected') {
	  if(!empty($checkList)) {
		foreach($checkList as $ch) {	
			mysql_query("delete from clients where eqID='$ch'");
		}
	  }
	}
?>

<form name="_form" action="" method="post">
<div class=form_layout>
	<div class="module_title"><img src='images/table.png'><?=$transac->getMname($view);?></div>
    <div class="module_actions">
    	<input type="text" name="eq_keyword" class="textbox" value="<?=$eq_keyword;?>" />
        <input type="submit" name="b" value="Search Equipment" class="buttons" />
      <!--  <input type="submit" name="b" value="Display All" class="buttons" />-->
        <input type="button" name="b" value="Add Equipment" onclick="xajax_new_equipmentform();toggleBox('demodiv',1);" class="buttons" />
        <input type="submit" name="b" value="Delete Selected" onclick="return approve_confirm();" class="buttons" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;">
    <table cellspacing="2" cellpadding="5" width="100%" align="center" class="search_table">
    	<?php
			$page = $_REQUEST['page'];
			if(empty($page)) $page = 1;
			 
			$limitvalue = $page * $limit - ($limit);
		
			$sql = "select
						  *
					 from
					 	  equipment as e,
						  equipment_categories as ec
					 where
					 	  (e.eq_name like '%$eq_keyword%' or
						  ec.eq_cat_name like '$eq_keyword%') and
						  e.eq_catID=ec.eq_catID";
			
			$pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
                    
			$i=$limitvalue;
			$rs = $pager->paginate();
		?>
        <tr>
            <td colspan="5" align="left">
                <?php
                    echo $pager->renderFullNav("$view");
                ?>
            </td>
        </tr>
    	<tr bgcolor="#C0C0C0">				
          <td width="20"><b>#</b></td>
          <td width="20" align="center"><input type="checkbox"  name="checkAll" value="Comfortable with" onclick="javascript:check_all('_form', this)" title="Check/Uncheck All" /></td>
          <td width="20"></td>
          <td><b>Equipment Name</b></td>
          <td><b>Equipment Category</b></td>
          <td><b>Equipment Model</b></td>
          <td><b>Plate Number</b></td>
          <td><b>Rate Per Hour</b></td>
        </tr>        
		<?php								
			while($r=mysql_fetch_assoc($rs)) {
				echo '<tr bgcolor="'.$transac->row_color($i++).'">';
				
				echo '<td width="20">'.$i.'</td>';
				echo '<td><input type="checkbox" name="checkList[]" value="'.$r[eqID].'" onclick="document._form.checkAll.checked=false"></td>';
				echo '<td width="15"><a href="#" onclick="xajax_edit_equipmentform(\''.$r[eqID].'\');" title="Edit Entry"><img src="images/edit.gif" border="0"></a></td>';
				echo '<td>'.$r[eq_name].'</td>';	
				echo '<td>'.$r[eq_cat_name].'</td>';	
				echo '<td>'.$r[eqModel].'</td>';	
				echo '<td>'.$r[plateNumber].'</td>';	
				echo '<td> P '.$r[rateperhour].'</td>';	
				echo '</tr>';
			}
        ?>
        <tr>
            <td colspan="5" align="left">
                <?php
                    echo $pager->renderFullNav("$view");
                ?>                
            </td>
      	</tr>
    </table>
    </div>
</div>
</form>