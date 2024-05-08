<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");

	$options=new options();

	$ca_header_id=$_REQUEST[ca_header_id];


	$query="
		select * from 
		cash_advance_header as c,
		employee as e
		where
		e.employeeID = c.employee_id and
		c.ca_header_id = '$ca_header_id'
	";
	$result=mysql_query($query);
	$r=mysql_fetch_assoc($result);
	$ca_header_id	= $r['ca_header_id'];
	$status		= $r['status'];
	$created	= $r['created'];
	$employee_lname	= $r['employee_lname'];
	$employee_fname	= $r['employee_fname'];
	$employee_mname	= $r['employee_mname'];
	$ca_header_id_pad	= str_pad($ca_header_id,7,0,STR_PAD_LEFT);


	//$due_date = date("F j, Y",strtotime("+$terms days",strtotime($date)));
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>JOB ORDER</title>
<script>
function printPage() { print(); } //Must be present for Iframe printing
</script>
<link rel="stylesheet" type="text/css" href="css/print_report_rr.css"/>
<style type="text/css">
	*{
		font-family: "Times New Roman";
	}
	@media screen {
	    div.divFooter {
	        display: none;
	    }
	}
	@media print {
	    div.divFooter {
	        position: fixed;
	        bottom: 0;

	        font-family: "Times New Roman";
	        font-size: 11px;
	    }
	}
	.square {
		width: 13px;
		height: 13px;
		background: #FFF;
		border:1px solid #000;
		display:inline-block;
	}
	.eva_td{
		text-align: center;
	}
</style>
</head>
<body>
<div class="container">


    <?php
	$c = 0;
	$page_beak_content = "<div style='page-break-after:always;'></div>";
	$exit = 0;
	$page_break = 0;
	$items = 17;
	$totalamount=0;
	$total_quantity = 0;
	$total_items = 0;

    ?>

    <div style="text-align:right; font-weight:bolder;">
        M.R.R #. : <?=str_pad($rr_header_id,7,0,STR_PAD_LEFT)?><br />
    </div>
    <div style="text-align:center; font-size:14px;">
        Material Receiving Report
    </div>
    <div class="header" style="margin-bottom:10px;">
        <table style="width:100%;">
            <tr>
                <td width="19%">Supplier:</td>
                <td width="47%" style="border-bottom:1px solid #000;"><?=$supplier?></td>

                <td width="7%">Date:</td>
                <td width="27%" style="border-bottom:1px solid #000;"><?=date("F j, Y",strtotime($date))?></td>
            </tr>
            <tr>
              <td>Project / Section:</td>
              <td style="border-bottom:1px solid #000;"><?=$project_name?></td>

              <td>PO #:</td>
              <td style="border-bottom:1px solid #000;"><?=$po_header_id_pad?></td>
            </tr>

            <tr>
            	<td>Terms</td>
                <td style="border-bottom:1px solid #000;"><?=$terms?></td>

                <td>Due Date</td>
                <td style="border-bottom:1px solid #000;"><?=$due_date?></td>
            </tr>

        </table>
    </div><!--End of header-->

    <table cellspacing="0" cellpadding="5" align="center" width="98%" style="border:none; text-align:center; margin-top:10px;" class="summary">
        <tr>
            <td>Received & Checked by:<p>
                <input type="text" class="line_bottom" /><br>Warehouseman</p></td>
            <td>Noted by:<p>
                <input type="text" class="line_bottom" /><br>P.I.C / MCD Head / Finance</p></td>
            <td>Encoded by:<p>
                <input type="text" class="line_bottom" /><br><?=$options->getUserName($user_id);?></p></td>
        </tr>
    </table>
</div>
</body>
</html>
