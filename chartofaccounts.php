<script language="JavaScript" src="scripts/cwcalendar/calendar.js"></script>
 <link rel="stylesheet" href="scripts/cwcalendar/cwcalendar.css"></link>


<?php

	$b = $_REQUEST['b'];
	$checkList = $_REQUEST['checkList'];
	$keyword = $_REQUEST['keyword'];
	
	if($b=='Delete') {
	  if(!empty($checkList)) {
		foreach($checkList as $ch) {	
			mysql_query("update gchart set gchart_void = '1' where gchart_id='$ch'");
		}
	  }
	}
	
	/*if($b=='Archive Beginning Balance') {
		$sqla = mysql_query("Select * from gchart where beg_debit != '0' or beg_credit != '0'") or die (mysql_error());
		while($ra = mysql_fetch_assoc($sqla)){
			$new_gchart = $ra['gchart_id'];
			$new_debit = $ra['beg_debit'];
			$new_credit = $ra['beg_credit'];
			mysql_query("Insert into gchart_beginning set gchart_id = '$new_gchart', date = NOW(), beg_debit = '$new_debit', beg_credit = '$new_credit'") or die (mysql_error());
		}
	}*/
	
?>

<form name="_form" action="" method="post">
<div class=form_layout>
	<div class="module_title"><img src='images/user_orange.png'><?=$transac->getMname($view);?></div>
    <div class="module_actions">
    	<input type="text" name="keyword" class="textbox" value="<?=$keyword;?>" />
        <input type="submit" name="b" value="Search" class="buttons" />
      <!--  <input type="submit" name="b" value="Display All" class="buttons" />-->
        <input type="button" name="b" value="Add" onclick="xajax_new_chartofaccountsform();toggleBox('demodiv',1);" class="buttons" />
        <input type="submit" name="b" value="Delete" onclick="return approve_confirm();" class="buttons" />
        <!--<input type="submit" name="b" value="Archive Beginning Balance" onclick="return approve_confirm();" class="buttons" />-->
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
					 	  gchart
					 where
					 	  gchart like '%$keyword%' 
					and
						  gchart_void = '0'
					 order by acode asc";
			
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
          <td><b>Account Code</b></td>
          <td><b>Parent Account</b></td>
          <td><b>Description</b></td>
          <td><b>Classification</b></td>
          <td><b>SubClassification</b></td>
          <td style="text-align:right;"><b>Beginning Debit</b></td>
          <td style="text-align:right;"><b>Beginning Credit</b></td>
          <td><b>Enabled</b></td>
        </tr>        
		<?php								
			while($r=mysql_fetch_assoc($rs)) {
				$id = $r['gchart_id'];
				$sqlbb = mysql_query("Select beg_debit, beg_credit from gchart_beginning where gchart_id = '$id' order by date DESC limit 1") or die (mysql_error());
				$rbb = mysql_fetch_assoc($sqlbb);
				$enable=($r[enable]=="Y")?"Yes":"No";
				
				echo '<tr bgcolor="'.$transac->row_color($i++).'">';
				echo '<td width="20">'.$i.'</td>';
				echo '<td><input type="checkbox" name="checkList[]" value="'.$r[gchart_id].'" onclick="document._form.checkAll.checked=false"></td>';
				echo '<td width="15"><a href="#" onclick="xajax_edit_chartofaccountsform(\''.$r[gchart_id].'\');" title="Edit Entry"><img src="images/edit.gif" border="0"></a></td>';
				echo '<td>'.$r[acode].'</td>';	
				echo '<td>'.$options->getAttribute('gchart','gchart_id',$r[parent_gchart_id],'gchart').'</td>';	
				echo '<td>'.$r[gchart].'</td>';	
				echo '<td>'.$options->getClassificationName($r[mclass]).'</td>';	
				echo '<td>'.$options->getAttribute('sub_gchart','sub_gchart_id',$r[sub_mclass],'sub_gchart').'</td>';	
				echo '<td style="text-align:right;">'.number_format($rbb['beg_debit'],2).'</td>';	
				echo '<td style="text-align:right;">'.number_format($rbb['beg_credit'],2).'</td>';	
				echo '<td>'.$enable.'</td>';	
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