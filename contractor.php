<?php
$b = $_REQUEST['b'];
$checkList = $_REQUEST['checkList'];
$keyword = $_REQUEST['keyword'];

if($b=='Delete Selected') {
  if(!empty($checkList)) {
	foreach($checkList as $ch) {	
		mysql_query("delete from contractor where contractor_id='$ch'") or die (mysql_error());
	}
  }
}
?>

<form name="_form" action="" method="post">
<div class=form_layout>
	<div class="module_title"><img src='images/user_orange.png'><?=$transac->getMname($view);?></div>
    <div class="module_actions">
    	<input type="text" name="keyword" class="textbox" value="<?=$keyword;?>" />
        <input type="submit" name="b" value="Search Contractor" class="buttons" />
        <input type="button" name="b" value="Add Contractor" onclick="xajax_new_contractorform();" class="buttons" />
        <input type="submit" name="b" value="Delete Selected" onclick="return approve_confirm();" class="buttons" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;">
    <table cellspacing="2" cellpadding="5" width="100%" align="center" class="search_table" style="text-align:left;">
    	<?php
			$page = $_REQUEST['page'];
			if(empty($page)) $page = 1;
			 
			$limitvalue = $page * $limit - ($limit);
		
			$sql = "
				select
					*
				from
					contractor
				where
					contractor like '%$keyword%'
				or
					contractor_code like '%$keyword%' 
			";
			
			$pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
                    
			$i=$limitvalue;
			$rs = $pager->paginate();
		?>
        <tr>
            <td colspan="5" align="left">
                <?php
                    echo $pager->renderFullNav($view);
                ?>
            </td>
        </tr>
    	<tr bgcolor="#C0C0C0">				
          <td width="20"><b>#</b></td>
          <td width="20" align="center"><input type="checkbox"  name="checkAll" value="Comfortable with" onclick="javascript:check_all('_form', this)" title="Check/Uncheck All" /></td>
          <td width="20"></td>
          <td><b>Contractor</b></td>
          <td><b>Contractor Code</b></td>
          <td><b>Address</b></td>
          <td><b>Contact #</b></td>
          <td><b>Contact Person</b></td>
         
        </tr>        
		<?php								
			while($r=mysql_fetch_assoc($rs)) {
				
				echo '<tr bgcolor="'.$transac->row_color($i++).'">';
				echo '<td width="20">'.$i.'</td>';
				echo '<td><input type="checkbox" name="checkList[]" value="'.$r[contractor_id].'" onclick="document._form.checkAll.checked=false"></td>';
				echo '<td width="15"><a href="#" onclick="xajax_edit_contractorform(\''.$r[contractor_id].'\');" title="Edit Entry"><img src="images/edit.gif" border="0"></a></td>';
				echo '<td>'.$r[contractor].'</td>';	
				echo '<td>'.$r[contractor_code].'</td>';	
				echo '<td>'.$r[address].'</td>';	
				echo '<td>'.$r[contactno].'</td>';	
				echo '<td>'.$r[contactperson].'</td>';	
				echo '</tr>';
			}
        ?>
        <tr>
            <td colspan="5" align="left">
                <?php
                    echo $pager->renderFullNav($view);
                ?>                
            </td>
      	</tr>
    </table>
    </div>
</div>
</form>