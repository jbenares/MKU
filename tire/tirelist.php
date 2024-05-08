<?php
$b				= $_REQUEST['b'];
$type_id	= $_REQUEST['type_id'];

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
.table-form tr td:nth-child(odd){
	text-align:right;
	font-weight:bold;
}
.table-form td{
	padding:3px;	
}
.table-form{
	display:inline-table;	
	border-collapse:collapse;
}
</style>
<form name="_form" action="" method="post">
<div class=form_layout>
	<div class="module_title"><img src='images/user_orange.png'><?=$transac->getMname($view);?></div>
    <div class="module_actions">           
        <table class="table-form">
            <!--<tr>
            	<td>From Date :</td>
                <td><input type="text" class="textbox datepicker" name="from_date" value='<?=$from_date?>' readonly='readonly' ></td>
            </tr>
            <tr>
            	<td>To Date :</td>
                <td><input type="text" class="textbox datepicker" name="to_date" value='<?=$to_date?>' readonly='readonly' ></td>
            </tr>-->
            <tr>
            	<td>Tire Type:</td>
                <td>
                	<?=$options->getTableAssoc($_REQUEST['type_id'],"type_id","Select Type","select * from tire_type order by type_id asc","type_id","type_name")?>
                </td>
            </tr>
      	</table>
  	</div>
    <div class="module_actions">
      	<input type="submit" name="b" value="Generate Report"  />
        <input type="button" value="Print" onclick="printIframe('JOframe');" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;" id="content">
     <?php	if($b == "Generate Report"){ ?>
   		<iframe id="JOframe" name="JOframe" frameborder="0" src="tire/print_tire_list.php?type_id=<?=$_REQUEST['type_id']?>" width="100%" height="500">
        </iframe>
    <?php } ?>
    </div>
</div>
</form>