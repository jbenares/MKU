<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	$options				=new options();	
	$project_id 			= $_REQUEST['project_id'];
	$work_category_id 		= $_REQUEST['work_category_id'];
	$sub_work_category_id 	= $_REQUEST['sub_work_category_id'];
	$stock_id			 	= $_REQUEST['stock_id'];
	
	
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>ORDER SHEET</title>
<script>
function printPage() { print(); } //Must be present for Iframe printing
</script>
<link rel="stylesheet" type="text/css" href="css/print_report2.css" />
<style type="text/css">
.table-content{
	border-collapse:collapse;
	width:100%;
}
.table-content th{
	text-align:left;	
	border-top:1px solid #000;
	border-bottom:1px solid #000;
}
.table-content td,.table-content th{
	padding:3px;
}

.table-content tr td:nth-child(3),.table-content tr th:nth-child(3){
	text-align:right;	
}
</style>

</head>
<body>
    <?php require("form_heading.php"); ?>
    
    <table>
        <tr>
            <td>Project / Section:</td>
            <td style="border-bottom:1px solid #000;"><?=$options->getAttribute('projects','project_id',$project_id,'project_name')?></td>
        </tr>
        <tr>
          <td>Work Category:</td>
          <td style="border-bottom:1px solid #000;"><?=$options->getAttribute('work_category','work_category_id',$work_category_id,'work')?></td>
        </tr>
        
        <tr>
          <td>Sub Work Category:</td>
          <td style="border-bottom:1px solid #000;"><?=$options->getAttribute('work_category','work_category_id',$sub_work_category_id,'work')?></td>
        </tr>
    </table>
   
    <table class="table-content">
        <tr>
			<th>RTP #</th>
            <th>ITEM</th>
            <th>QTY</th>
            <th>UNIT</th>
            <th>STATUS</th>
            <th>ENCODED BY</th>
        </tr>
        <?php
		$total_allowed_qty = $total_not_allowed_qty = 0;
		$result=mysql_query("
				select
					h.pr_header_id,user_id,stock,unit,quantity,allowed
				from
					pr_header as h, pr_detail as d,productmaster as p
				where
					h.pr_header_id = d.pr_header_id
				and
					p.stock_id = d.stock_id
				and
					h.status != 'C'
				and
					work_category_id = '$work_category_id'
				and
					sub_work_category_id = '$sub_work_category_id'
				and
					d.stock_id = '$stock_id'
			") or die(mysql_error());
			
        while($r=mysql_fetch_assoc($result)):
			if($r['allowed']){
				$total_allowed_qty += $r['quantity'];	
			}else{
				$total_not_allowed_qty += $r['quantity'];	
			}
        ?>
        <tr>
            <td><?=str_pad($r['pr_header_id'],7,0,STR_PAD_LEFT)?></td>
            <td><?=$r['stock']?></td>
            <td><?=number_format($r['quantity'],4,'.',',')?></td>
            <td><?=$r['unit']?></td>
            <td><?=($r['allowed']) ? "ALLOWED" : "NOT ALLOWED"?></td>
            <td><?=$options->getUserName($r['user_id'])?></td>
        </tr>
        <?php
        endwhile;
        ?>
        <tr>
        	<td style="border-top:1px solid #000; font-weight:bold;"></td>
            <td style="border-top:1px solid #000; font-weight:bold; text-align:right;">TOTAL ALLOWED:</td>
            <td style="border-top:1px solid #000; font-weight:bold;"><?=number_format($total_allowed_qty,4,'.',',')?></td>
            <td style="border-top:1px solid #000; font-weight:bold;"></td>
            <td style="border-top:1px solid #000; font-weight:bold;"></td>
            <td style="border-top:1px solid #000; font-weight:bold;"></td>
        </tr>	
        
        <tr>
        	<td style="font-weight:bold;"></td>
            <td style="font-weight:bold; text-align:right;">TOTAL NOT ALLOWED:</td>
            <td style="font-weight:bold; text-align:right;"><?=number_format($total_not_allowed_qty,4,'.',',')?></td>
            <td style="font-weight:bold;"></td>
            <td style="font-weight:bold;"></td>
            <td style="font-weight:bold;"></td>
        </tr>	
    </table>  
    
    <table cellspacing="0" cellpadding="5" align="center" width="98%" style="border:none; text-align:center; margin-top:10px;" class="summary">
        <tr>
            <td>Prepared By:<p>
                <input type="text" class="line_bottom" /><br><input type="text" style="border:none; text-align:center; font-size:11px;" value="<?=$options->getUserName($user_id);?>" /></p></td>
            <td>Requested By:<p>
                <input type="text" class="line_bottom" /><br>Requisitioner</p></td>
            <td>Checked By:<p>
                <input type="text" class="line_bottom" /><br>Warehouseman</p></td>
            <td>Approved By:<p>
                <input type="text" class="line_bottom" /><br>RJR / DMG </p></td>
        </tr>
    </table>            
	<div class="page-break"></div>
</body>
</html>