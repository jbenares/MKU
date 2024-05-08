<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
	
	$production_id = $_REQUEST['id'];
	
	$result=mysql_query("
		select
			*
		from
			production
		where
			production_id = '$production_id'
	") or die(mysql_error());
	
	$r = mysql_fetch_assoc($result);
	$date				= $r['date'];
	$stock_id			= $r['stock_id'];
	$stock 				= $options->attr_stock($stock_id,'stock');	
	$required			= $r['required'];
	$actual				= $r['actual'];
	$buffer				= $r['buffer'];
	$orders				= $r['orders'];
	$beginning_balance	= $r['beginning_balance'];
	
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>JOB ORDER</title>
<script>
function printPage() { print(); } //Must be present for Iframe printing
</script>
<link rel="stylesheet" type="text/css" href="css/print.css"/>
<style type="text/css">
	table td{
		border:1px solid #000;
		
	}

</style>
</head>
<body>
<div class="container">
	
     <div><!--Start of Form-->
     	
     	<div align="right" style="font-size:18pt; font-weight:bolder;">
        	PRODUCTION BY PIECE
        </div>
        
        <div align="right" style="margin:20px 0px;">	
        	Date: <div align="center" style="display:inline-block; border-bottom:1px dashed #000; width:150px; height:1em;"><?php echo date("m/d/Y")?></div>
        </div>
        
        <div class="content">
        	<table style="margin-bottom:10px;">
            	<tr>
                    <td width="27%" class="align-right">Date : </td>
                    <td width="29%"><?=date("F j, Y",strtotime($date))?></td>
                    
                    <td width="24%" class="align-right">Buffer : </td>
                    <td width="20%" class="align-right"><?=number_format($buffer,2,'.',',')?></td>
                </tr>
                <tr>
                	<td class="align-right">Production # : </td>
                    <td><?=str_pad($production_id,7,0,STR_PAD_LEFT)?></td>
                	
                    <td class="align-right">Order : </td>
                    <td class="align-right"><?=number_format($order,2,'.',',')?></td>
                </tr>
                <tr>
                	<td class="align-right">Stock : </td>
                    <td><?=$stock?></td>
                    
                    <td class="align-right">Required : </td>
                    <td class="align-right"><?=number_format($required,2,'.',',')?></td>
                </tr>
                <tr>
                	<td class="align-right">Beginning Balance : </td>
                    <td class="align-right"><?=number_format($beginning_balance,2,'.',',')?></td>
                    
                    <td class="align-right">Actual Output : </td>
                    <td class="align-right"><?=number_format($actual,2,'.',',')?></td>
                </tr>  
         	</table>
            Formulations : 
            <table style="margin-top:10px;">
                <?php
				$result=mysql_query("
					select
						*
					from 
						production_formulations as p, formulation_header as f
					where
						p.formulation_header_id = f.formulation_header_id
					and
						production_id = '$production_id'
				") or die(mysql_error());
				while($r = mysql_fetch_assoc($result)){
				$formulation_code	= $r['formulation_code'];
				$description		= $r['description'];
				$output				= $r['output'];
                ?>
                <tr>
                	<td width="27%" class="align-right">Formulation Code : </td>
                    <td width="73%"><?=$formulation_code?></td>
                </tr>
                <tr>
                	<td class="align-right">Description : </td>
                    <td><?=$description?></td>
                </tr>
                <tr>	
                	<td class="align-right">Output : </td>
                    <td><?=number_format($output,2,'.',',')?></td>
                </tr>
                <tr>
                	<td colspan="2"></td>
                </tr>
                <?php
				}
                ?>
            </table>     
        </div><!--End of content-->
    </div><!--End of Form-->
</div>
</body>
</html>