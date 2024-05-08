<?php
	$b = $_REQUEST['b'];
	$checkList = $_REQUEST['checkList'];
	$keyword = $_REQUEST['keyword'];
	
	if($b=='Delete Selected') {
	  if(!empty($checkList)) {
		foreach($checkList as $ch) {	
			echo $ch;
			mysql_query("UPDATE part_file_list SET is_deleted = '1' WHERE id='$ch'");
			//$options->insertAudit($ch,'petty_cash_id','D');
		}
	  }
	}else if($b=="Add"){
		$query="
				insert into
					part_file_list
				set
					stock_id = '".$_REQUEST['supplier_id']."',
					kms_run = '".$_REQUEST['kms']."',
					dys_run = '".$_REQUEST['dys']."',
					date_added=NOW()
					";
		mysql_query($query) or die(mysql_error());
		$msg="Entry Added";
	}else if($b=="edit"){
		$q="
			SELECT 
				* 
			FROM 
				part_file_list as pf,productmaster as p
					WHERE pf.id='".$_REQUEST['id']."'
				AND pf.is_deleted !='1'
				AND	p.stock_id=pf.stock_id
				order by 
					p.stock ASC
					";
		$l=mysql_query($q);
		$fe=mysql_fetch_assoc($l);
		
		if(empty($fe['stock'])){
			$stock=$fe['stockcode'];
		}else{
			$stock=$fe['stock'];
		}
		$stock_id=$fe['stock_id'];
		$kms=$fe['kms_run'];
		$dys=$fe['dys_run'];
		$da=explode(" ",$fe['date_added']);
		$date=$da[0];
		
	}else if($b=="Update"){
		$query="
				update
					part_file_list
				set
					stock_id = '".$_REQUEST['supplier_id']."',
					kms_run = '".$_REQUEST['kms']."',
					dys_run = '".$_REQUEST['dys']."',
					date_modified=NOW()
					";
		mysql_query($query) or die(mysql_error());
		header("location: admin.php?view=98b1ba711c97b55b6151");
	}
?>
<script>
xajax.callback.global.onRequest = function(){toggleBox('demodiv',1);}
xajax.callback.global.beforeResponseProcessing = function(){toggleBox('demodiv',0);}
</script>
<!-- Modified by roljhon starts here !-->
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
<!-- Modified by roljhon ends here !-->
<form name="_form" action="" method="post">
<div class=form_layout>
	<div class="module_title"><img src='images/user_orange.png'><?=$transac->getMname($view);?></div>
    <div class="module_actions">
    	<input type="text" name="keyword" class="textbox" value="<?=$keyword;?>" />
        <input type="submit" name="b" value="Search" class="buttons"/>
		<input type="submit" name="b" value="Delete Selected" onclick="return approve_confirm();" class="buttons" />
    </div>
   <?php if(!empty($msg)) echo '<div id="status_update" class="ui-state-highlight ui-corner-all" style="padding: 0px 0.7em; text-align:left;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right:.3em;"></span> '.$msg.'</p></div>'; ?>
    <div style="padding:3px; text-align:center;">
    <?php
		$page = $_REQUEST['page'];
		if(empty($page)) $page = 1;
		 
		$limitvalue = $page * $limit - ($limit);
	
		$sql = "
				select
					*
				from
					part_file_list as pf,productmaster as p
				where
					(p.stock like '%$keyword%'  or
						  p.stockcode like '%$keyword%') 
					AND pf.is_deleted != '1'
					AND p.stock_id=pf.stock_id
				order by 
					p.stock DESC
				";
		
		$pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
				
		$i=$limitvalue;
		$rs = $pager->paginate();
	?>
	<div class="module_actions">
		<div class='inline'>
            Date : <br />
            <input type="text" name="date" id="date" class="textbox3 datepicker" readonly="readonly" value="<?=$date;?>" title="Please Enter Date" />
        </div>    	
        <div class="inline" id="supplier_div">
            Product : <br />
            <input type="text" class="textbox" name="product" value="<?=$stock?>" id="supplier_name1" onclick="this.select();" />
            <input type="hidden" name="supplier_id" id="account_id" value="<?=$stock_id?>" title="Please Select Product" />
        </div>
        <div class="inline">
            Kms Run:<br/>
                <input type="text" name="kms" class="textbox" value="<?=$kms?>"/>
        </div>
		<div class="inline">
				Dys Run :<br/>
				<input type="text" name="dys" class="textbox" value="<?=$dys?>"/>
		</div>
		<br/>
		<?php
			if($b=="edit"){
		?>
			<input type="submit" name="b" id="b" value="Update"/>
		<?php
			}else{
		?>
			<input type="submit" name="b" id="b" value="Add"/>
		<?php
			}
		?>
	</div>
    <table cellspacing="2" cellpadding="5" width="100%" align="left" class="search_table">
    	<tr bgcolor="#C0C0C0">				
          <td width="20"><b>#</b></td>
          <td width="20" align="center"><input type="checkbox"  name="checkAll" value="Comfortable with" onclick="javascript:check_all('_form', this)" title="Check/Uncheck All" /></td>
		  <td width="20"></td>
		  <td style="text-align:center;">STOCK/STOCKCODE</td>
		  <td style="text-align:center;">KMS RUN</td>
		  <td style="text-align:center;">DYS RUN</td>
		  <td style="text-align:center;">Date Added</td>  
         </tr>        
		<?php					
			$i=1;			
			while($r=mysql_fetch_assoc($rs)) {
				$id		= $r['id'];
				$stock = $r['stock'];
				$kms = $r['kms_run'];
				$dys = $r['dys_run'];
				#Date Added
				$date_added	= $r['date_added'];

			echo '<tr bgcolor="'.$transac->row_color($i).'">';
		?>
                    <td width="20"><?=$i++?></td>
                    <td><input type="checkbox" name="checkList[]" value="<?=$id?>" onclick="document._form.checkAll.checked=false"></td>
                    <td width="15"><a href="admin.php?view=98b1ba711c97b55b6151&id=<?=$id?>&b=edit" title="Edit Entry"><img src="images/edit.gif" border="0"></a></td>
					<!--<td width="15"><a href="admin.php?view=a2e67ff3af23b37db350&agent_id=<?=$agent_id?>&reports=get" title="Reports"><img src="images/report.png" border="0"></a></td>	!-->				
					<td style="text-align:center;">
					<?php
						if(empty($stock)){
							echo $r['stockcode'];
						}else{
							echo $stock;
						}
					?></td>   
					<td style="text-align:center;"><?=$kms?></td>
					<td style="text-align:center;"><?=$dys?></td>
					<td style="text-align:center;"><?=date("F m,Y",strtotime($date_added))?></td>					
				</tr>
      	<?php
			}
        ?>
    </table>
    <div class="pagination">
	<?php
        echo $pager->renderFullNav($view);
    ?>
    </div>
    </div>
</div>
</form>
