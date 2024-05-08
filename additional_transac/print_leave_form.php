<?php	
require_once(dirname(__FILE__).'/../library/lib.php');
require_once(dirname(__FILE__).'/../conf/ucs.conf.php');


$leave_id = $_REQUEST['leave_id'];

$aStatus = array(
    'F' => 'Finished',
    'S' => 'Saved',
    'C' => 'Cancelled'
);


$sql = "
	select
        h.*, concat(employee_fname,' ',employee_lname) as name
    from
        leave_info as h
        left join employee as e on h.employee_id = e.employeeID         
     where leave_id = '$leave_id'
";

$aTrans = lib::getTableAttributes($sql);


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>JOB ORDER</title>
<script>
function printPage() { print(); } //Must be present for Iframe printing
</script>
<style type="text/css">
body
{
	size: legal portrait;
	padding:0px;
	font-family:Arial, Helvetica, sans-serif;
	font-size:14px;
}
.container{
	margin:0px;
	padding:0.1in;
}

.header
{
	text-align:left;	
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
	margin:10px 0px;	
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
.line_bottom {
    border-bottom-width: 1px;
    border-bottom-style: solid;
    border-bottom-color: #000000;
    border-left: 0px;
    border-right: 0px;
    border-top: 0px;
    width:250px;
    font-size: 14px;
    text-align: left;
	font-family:Arial, Helvetica, sans-serif;
}
.line_bottom2 {
    border-bottom-width: 1px;
    border-bottom-style: solid;
    border-bottom-color: #000000;
    border-left: 0px;
    border-right: 0px;
    border-top: 0px;
    width:180px;
    font-size: 14px;
    text-align: left;
	font-family:Arial, Helvetica, sans-serif;
}

</style>
</head>
<body>
<div class="container">	
<div>    
        <p style="text-align:center; font-size:14px; font-weight:bold;width:90%;">
            <?=$title?> <br><br>
            REQUEST FOR OFFICIAL LEAVE
        </p>  
       <div style="text-align:right; font-weight:bolder;width:80%;">
           	<span style="font-size:18px;">LF#.<?=$aTrans['lf_num']?></span><br />
        </div>       		

        <div class="header" style="text_align-left;">
        	<table style="display:inline-table;">
                
                    <tr>
                        <td>Date of Request:</td>
                        <td style="border-bottom:1px solid #000;"><?=lib::ymd2mdy($aTrans['date_requested'])?></td>
                    </tr>  
						<tr>
                        <td>Inclusive Date:</td>
                       <td style="border-bottom:1px solid #000;"> <?=lib::ymd2mdy($aTrans['inclusive_date'])?> - <?=lib::ymd2mdy($aTrans['inclusive_date_to'])?></td>
                    </tr>
					<tr>
					<td style="height:5px">
					</td>
					</tr>
					   <tr>
					<td> Particulars :</td>
					<td style="border-bottom:1px solid #000;"><?=$aTrans['particular']?></td>
				</tr>
			</div>	
			</table>
			<table style="margin-top:10px;">
                    <tr>
                        <td>Requested by:
						 &nbsp; &nbsp;<input type="text" class="line_bottom" value="<?=$aTrans['name']?>"><br>
						 &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp;
						 &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp;
						 &nbsp; &nbsp; &nbsp;Name & Signature</td>
						                       
                    </tr> 
            </table>
     	</div><!--End of header-->
		</div>
		<p>
		<table>
		<tr>
		<td><b><u>
		Remarks: To be mark by P.I.C. or Dept. Head
		<b></u></td>
		<tr>
		</table>
		</p>
        

		<table style="width:40%">
		<tr>
	     <td>(  ) Vacation Leave</td>
		 <td>(  ) Sick Leave</td>
			</tr>
			<tr></tr>
			<tr>
         <td>(  ) With Pay</td>
		 <td>(  ) Without Pay</td>
			</tr>

        <table cellspacing="0" cellpadding="5" align="left" width="60%" style="border:none; text-align:center; margin-top:10px;" class="summary">
            <tr>
                <td style="text-align:left;">Noted by:<p>
                    <input type="text" class="line_bottom2" /></td>
                <td style="text-align:left;">Approved by:<p>
                    <input type="text" class="line_bottom2"/></td>
            </tr>
        </table>
</div>
</body>
</html>
<script>
    printPage();
</script>