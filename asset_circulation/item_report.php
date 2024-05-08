<?php
	$options = new ac_options();
	$from_date 		= $_REQUEST['from_date'];
	$to_date		= $_REQUEST['to_date'];
	$stock_id		= $_REQUEST['stock_id'];

	$sql = "SELECT * FROM productmaster p, asset_circulation_header h, asset_circulation_detail d
				WHERE h.ach_id = d.ach_id AND d.stock_id = p.stock_id AND h.is_deleted != '1'
					GROUP BY d.stock_id";
	$rs_sql = mysql_query($sql);
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
	<div class="module_title"><img src='images/user_orange.png'>Generate Report - Item</div>
    <div class="module_actions">       
            
        <div style="display:inline-block;">
            From Date : <br />
            <input type="text" class="datepicker required textbox3" title="Please enter date"  name="from_date" readonly='readonly'  value="<?=(!empty($from_date))?$from_date:date("Y-m-d")?>">
        </div>
        
        <div style="display:inline-block;">
            To Date : <br />
            <input type="text" class="datepicker required textbox3" title="Please enter date"  name="to_date" readonly='readonly'  value="<?=(!empty($to_date))?$to_date:date("Y-m-d")?>">
        </div>
		
		<div style="display:inline-block;">
            Select Item : <br />
            <!--<select name="stock_id">
				<?php
				/*	while($rw_sql = mysql_fetch_assoc($rs_sql))
					{
						extract($rw_sql);
				?>
						<option value="<?=(!empty($stock_id))?$stock_id:""?>"><?php echo $stock; ?></option>
				<?php
					} // End While*/
				?>
			</select>!-->	
			<?=$options->option_stock_list2($_REQUEST['stock_id'],'stock_id','Select Item')?>
        </div>
                
      	<input type="submit" value="Generate Item Report"  />
        <input type="button" value="Print" onclick="printIframe('JOframe');" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;" id="content">
     <?php  if(!empty($from_date) && !empty($to_date) ) { ?>
   		<iframe id="JOframe" name="JOframe" frameborder="0" src="asset_circulation/print_item_report.php?
            from_date=<?=$from_date?>&
            to_date=<?=$to_date?>&
			stock_id=<?=$_REQUEST['stock_id']?>
            " width="100%" height="500">
        </iframe>
    <?php }?>
    </div>
</div>
</form>