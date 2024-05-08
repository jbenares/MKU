<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
	$sales_invoice_id	= $_REQUEST['id'];
	
	$result = mysql_query("
		select * from sales_invoice where sales_invoice_id = '$sales_invoice_id'
	") or die(mysql_error());
	$r = mysql_fetch_assoc($result);
	
	$project_name 	= $options->getAttribute('projects','project_id',$r['project_id'],'project_name');
$project_address 	= $options->getAttribute('projects','project_id',$r['project_id'],'location');
	$sales_account 	= $options->getAttribute('gchart','gchart_id',$r['sales_gchart_id'],'gchart');
	$ar_account	 	= $options->getAttribute('gchart','gchart_id',$r['ar_gchart_id'],'gchart');
	
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

@media print{
  @page {
  }
  
  .page-break{
		display:block;
		page-break-before:always;  
  }
  
  body *,.header table td,.content table td,.content table th{
		font-size:15px;   
  }
  
}
	
body
{
	padding:0px;
	font-family:Arial, Helvetica, sans-serif;
	font-size:15px;
}
.container{
	width:100%;
}

.header
{
	text-align:center;	
}

.header table, .content table
{
	width:100%;
	text-align:left;
	

}

.issuance_table{
	border-collapse:collapse;
	width:100%;
}
.issuance_table th{
	border:1px solid #000;
}
.issuance_table td{
	border-left:1px solid #000;
	border-right:1px solid #000;	
}
.issuance_table tr:last-child td{
	border-bottom:1px solid #000;	
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

.footer td{
	border:none;
}

.align-right{
	text-align:right;	
}

.inline{
	display:inline-block;	
}

.line_bottom {
	border-bottom-width: 1px;
	border-bottom-style: solid;
	border-bottom-color: #000000;
	border-left: 0px;
	border-right: 0px;
	border-top: 0px;
	width:120px;
}
.kurit td{
	border-top:1px solid #000;
	border-bottom:1px solid #000;	
}
</style>
</head>
<body>
<div class="container">
	
	
	<?php require("form_heading.php") ?>
    <div style="text-align:center; font-weight:bolder;">
        S.I. # : <?=str_pad($sales_invoice_id,7,0,STR_PAD_LEFT)?><br />
    </div>   
    <div style="text-align:left; font-weight:bold; font-size:14px;">
        <u>SALES INVOICE</u>
    </div>
    
    <table style="display:inline-table; margin-right:20px;">
        <tr>
            <td>&nbsp;</td>
            <td style="width:300px;border-bottom:0px solid #000;"> &nbsp;</td>
            <td>Date:</td>
            <td style="width:230px;border-bottom:0px solid #000;"><?=$r[date]?></td>
        </tr>
    	<tr>
        	<td>Billed to:</td>
            <td style="width:300px;border-bottom:1px solid #000;"> <?=$project_name?></td>
            <td>TIN:</td>
            <td style="width:200px;border-bottom:1px solid #000;"></td>
        </tr>
        <tr>
        	<td>Business Style:</td>
            <td style="width:300px;border-bottom:1px solid #000;"></td>
            <td>TERMS:</td>
            <td style="width:200px;border-bottom:1px solid #000;"></td>
        </tr>
        <tr>
        	<td>Address:</td>
            <td style="width:300px;border-bottom:1px solid #000;"> <?=$project_address?></td>
            <td>P.O.No</td>
            <td style="width:200px;border-bottom:1px solid #000;"></td>
        </tr>
        
    </table>
    <?php
        $sql=mysql_query("SELECT * FROM sales_invoice_detail WHERE sales_invoice_id = '$sales_invoice_id'");
$total=0;
?>
    <table border="1" style="width:700px;border-collapse:collapse;margin-top:10px;">
        <tr style="text-align:center;">
            <td style="height:40px;">QTY</td>
            <td>UNIT</td>
            <td>DESCRIPTION</td>
            <td>UNIT PRICE</td>
            <td>AMOUNT</td>
        </tr>
<?php
while($rr=mysql_fetch_assoc($sql)){
    $total+=$rr[amount];
    ?>
      <tr>
          <td align="center"><?=$rr[qty]?></td> 
          <td align="center"><?=$rr[unit]?></td>
          <td align="left"><?=$rr[description]?></td> 
          <td align="right"><?=number_format($rr[unit_price],2)?></td>
          <td align="right"><?=number_format($rr[amount],2)?></td>
       </tr>
    <?php
}
    ?>
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td colspan="2" align="right"><b>TOTAL AMOUNT DUE</b></td>
            <td align="right"><b>P <?=number_format($total,2)?></b></td>
        </tr>
    </table><br/><br/>
    <table border=0 style="margin-left:70px; margin-top:40px;">
        <tr>
            <td style="border-bottom:1px solid #000;width:200px;"></td>
            <td style="width:90px;"></td>
            <td style="border-bottom:1px solid #000;width:300px;"></td>
        </tr>
        <tr>
            <td align="center">Salesman</td>
            <td align="center">&nbsp;</td>
            <td align="center">Customer's Signature Over Printed Name</td>
        </tr>
    </table>
        
    <!--<table cellspacing="0" cellpadding="5" align="center" width="98%" style="border:none; text-align:center; margin-top:10px;" class="summary">
        <tr>
            <td>Requested By:<p>
                <input type="text" class="line_bottom" style="width:200px;" value="" /><br>End User</p></td>
            <td>Approved By:<p>
                <input type="text" class="line_bottom" /><br>P.I.C / Section Head</p></td>
            <td>Issued By:<p>
                <input type="text" class="line_bottom" value="<?=$options->getUserName($user_id);?>" /><br>Warehouseman</p></td>
            <td>Received By:<p>
                <input type="text" class="line_bottom" /><br>&nbsp;</p></td>
        </tr>
    </table> -->
     
    
</div>
</body>
</html>