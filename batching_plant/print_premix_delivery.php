<?php
include_once("../conf/ucs.conf.php");
include_once("../library/lib.php");

$premix_delivery_id = $_REQUEST['premix_delivery_id'];
$arr = lib::getTableAttributes("select * from premix_delivery where premix_delivery_id = '$premix_delivery_id'");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>REPORT</title>
<script>
function printPage() { print(); } //Must be present for Iframe printing
</script>
<style type="text/css">

body
{
	size: letter portrait;
	font-family:Arial, Helvetica, sans-serif;
	font-size:11px;
}
.container{
	margin:0px auto;
	padding:0.1in;
}

.clearfix:after {
	content: ".";
	display: block;
	clear: both;
	visibility: hidden;
	line-height: 0;
	height: 0;
}
 
.clearfix {
	display: inline-block;
}

html[xmlns] .clearfix {
	display: block;
}
 
* html .clearfix {
	height: 1%;
}

table{
	width:100%;
	border-collapse:collapse;	
}

table thead tr td{
	border-top:1px solid #000;
	border-bottom:1px solid #000;
	font-weight:bold;
}
/*table  tr td:nth-child(n+2){
	text-align:right;	
}*/
table td{
	padding:3px;	
}
.line_bottom {
	border-bottom-width: 1px;
	border-bottom-style: solid;
	border-bottom-color: #000000;
	border-left: 0px;
	border-right: 0px;
	border-top: 0px;
	width:140px;
	font-size: 11px;
	text-align: center;
}
</style>
<style type="text/css">
	.trans-header{
		width:100%;
		border-collapse: collapse;
	}
	.trans-header tbody td{
		padding:3px 5px 3px 3px;
	}
	.trans-header tbody td:nth-child(even){
		padding-right:50px;		
		border-bottom: 1px solid #000;
	}
	.trans-header tbody td:nth-child(odd){
		text-align: right;
	}

	.jo-detail{
		width:100%;
		border-collapse: collapse;
	}
	.jo-detail tbody td{
		border:1px solid #c0c0c0;
		padding:3px;
	}
	.jo-detail tbody td:nth-child(2),.jo-detail tbody td:nth-child(3){
		width:20%;
	}
</style>
</head>
<body>
<div class="container">
	
     <div><!--Start of Form-->

     	<?php require_once('../transactions/form_heading.php') ?>
     	<div style="text-align:center; font-weight:bolder; margin-bottom:5px;">
            <u>PREMIX DELIVERY</u>
        </div>
        <div class="content" >
        	<table cellspacing="0" class="trans-header">            	
                <tbody> 
                	<tr>
                		<td></td>
                		<td></td>
                		<td>PREMIX DR NO</td>
                		<td><?=str_pad($arr['premix_delivery_id'], 7,0,STR_PAD_LEFT)?></td>
                	</tr>                 
                	<tr>
            			<td>Project</td>
            			<td><?=lib::getAttribute('projects','project_id',$arr['project_id'],'project_name')?></td>

            			<td>Date</td>
            			<td><?=$arr['date']?></td>
            		</tr>
            		<tr>
            			<td>Batch No</td>
            			<td><?=$arr['batch_no']?></td>

            			<td>Equipment</td>
            			<td><?=lib::getAttribute('productmaster','stock_id',$arr['equipment_id'],'stock')?></td>
            		</tr>
                    <tr>
                        <td>Premix</td>
                        <td><?=lib::getAttribute('productmaster','stock_id',$arr['premix_id'],'stock')?></td>
                    </tr>
                    <tr>
                        <td>Price</td>
                        <td><?=$arr['price']?></td>
                    </tr>
                    <tr>
                        <td>Volume</td>
                        <td><?=$arr['volume']?> </td>
                    </tr>

                    <tr>
                        <td style="vertical-align:top;">Remarks</td>
                        <td colspan="3"> <?=$arr['remarks']?></td>
                    </tr>
           		</tbody>
            </table>            

        	<table cellspacing="0" cellpadding="5" align="center" width="98%" style="border:none; text-align:center; margin-top:10px;" class="summary">
		        <tr>
		            <td><p>
		                <input type="text" class="line_bottom" value="<?=lib::getUserFullName($arr['encoded_by_id'])?>"/><br>Prepared By</p></td>
		          	<td><p>
		                <input type="text" class="line_bottom" value="<?=lib::getEmployeeName($arr['driver_id'])?>" /><br>Driver</p></td>
		            <td><p>
		                <input type="text" class="line_bottom" value="<?=lib::getEmployeeName($arr['checked_by_id'])?>" /><br>Check & Verified By</p></td>
		        </tr>
		    </table>
        </div><!--End of content-->
    </div><!--End of Form-->

</div>
</body>
</html>

