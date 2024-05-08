<?php
include_once("../conf/ucs.conf.php");
require_once("../my_Classes/options.class.php");
$options = new options();
$id  = $_REQUEST['id'];

$query="
	select
		concat(employee_lname,', ',employee_fname,' ',employee_mname) as name,o.notes,o.user_id,o.official_logbook_id,
		date,
		p1.project_name as from_project,
		p2.project_name as to_project
	from 
		official_logbook as o left join employee as e on o.employee_id = e.employeeID
		left join projects as p1 on o.from_project_id = p1.project_id
		left join projects as p2 on o.to_project_id = p2.project_id
	where
		o.official_logbook_id = '$id'
";

$result=mysql_query($query);
$aTrans=mysql_fetch_assoc($result);

	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>ORDER SHEET</title>
<script>
function printPage() { print(); } //Must be present for Iframe printing
</script>
<style type="text/css">
	
body
{
	size: legal portrait;
	padding:0px;
	font-family:Arial, Helvetica, sans-serif;
	font-size:16px;
}
.container{
	margin:0px auto;
	padding:0.1in;
}

.header
{
	text-align:center;	
	margin-top:20px;
}

.header table, .content table
{
	text-align:left;
	

}
.header table td, .content table td
{
	padding:3px;
	
}

hr
{
	margin:40px 0px;	
	border:1px dashed #999;

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

.line_bottom{
	border:none;
	border-bottom:1px solid #000;
	background-color:none;
	width:220px;
}

.table-print-css{
	border-collapse:collapse;
}
.table-print-css td, table-print-css th{
	padding:3px;
}

.table-print-css thead td{
	border-top:1px solid #000;
	border-bottom:1px solid #000;	
}

.table-print-css tfoot tr:first-child td{
	border-top:1px solid #000;	
}
.table-print-css tfoot td{
	font-weight:bold;	
}


</style>
</head>
<body>
<div class="container">
	
    <div><!--Start of Form-->
        <div style="text-align:center; font-weight:bold;width:900px;">
        	<?=$title?><br />
            <u>OFFICIAL BUSINESS LOG BOOK</u>
        </div>
        <div style="text-align:right; font-weight:bolder;width:900px;">
           	<span style="font-size:18px;">OB#.<?=str_pad($aTrans['official_logbook_id'],7,0,STR_PAD_LEFT)?></span><br />
            <?=date("m/d/Y",strtotime($aTrans['date']))?>
        </div>       
        <div class="header" style="text-align:left;">
        	<table style="display:inline-table;">
                <tr>
                	<td>DATE:</td>
                    <td style="border-bottom:1px solid #000;"><?=$aTrans['date']?></td>
               	</tr>
                <tr>
                	<td>EMPLOYEE:</td>
                    <td style="border-bottom:1px solid #000;"><?=$aTrans['name']?></td>
               	</tr>
                <tr>
                	<td>FROM PROJECT:</td>
                    <td style="border-bottom:1px solid #000;"><?=$aTrans['from_project']?></td>
               	</tr>
                <tr>
                	<td>TO PROJECT:</td>
                    <td style="border-bottom:1px solid #000;"><?=$aTrans['to_project']?></td>
               	</tr>
                <tr>
                	<td>NOTES:</td>
                    <td style="border-bottom:1px solid #000;"><?=$aTrans['notes']?></td>
                </tr>
            </table>
     	</div><!--End of header--><br />

    </div><!--End of Form-->
	<table cellspacing="0" cellpadding="5" align="center" width="100%" style="border:none; text-align:center; margin-top:50px;" class="summary">
        <tr>
            
            <td>Prepared By:<p>
               <!--<input type="text" class="line_bottom" /><br><?=$options->getUserName($aTrans['user_id']);?><br>-->
				 <input type="text" class="line_bottom" /><br>HR/Admin & Legal Department</p></td>
            <td>Noted By:<p>
            <input type="text" class="line_bottom" /><br>Department Head</p></td> 
			 <td>Approved By:<p>
				 <input type="text" class="line_bottom" /><br>President / G.M.</p></td>
            <td>Acknowledged by:<p>
            <input type="text" class="line_bottom" /><br>&nbsp;</p></td> 
        </tr>
    </table>
    
    <?php 
	if($b == "Print Preview" && $preturn_header_id){ 
		echo "	<iframe id='JOframe' name='JOframe' frameborder='0' src='inventory_transactions/print_purchase_return.php?id=$preturn_header_id' width='100%' height='500'>
		</iframe>";    
	} 
	?>
</div>
<div class="page-break"></div>
</body>
</html>
