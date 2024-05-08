<?php
include_once("../conf/ucs.conf.php");
include_once("../library/lib.php");

$premix_quotation_header_id = $_REQUEST['premix_quotation_header_id'];
$aTrans = lib::getTableAttributes("select * from premix_quotation_header where premix_quotation_header_id = '$premix_quotation_header_id'");

function getDetails($premix_quotation_header_id){
    $sql = "
        select 
        	d.*, p.stock
        from 
        	premix_quotation_detail  as d
        left join productmaster as p on d.stock_id = p.stock_id
        where 
        	premix_quotation_header_id = '$premix_quotation_header_id' 
        and premix_quotation_void = '0'
    ";

    return lib::getArrayDetails($sql);
}

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

	.details-table tbody td:nth-child(n+2),.details-table thead td:nth-child(n+2),.details-table tfoot td:nth-child(n+2){
		text-align: right;
	}

	.details-table tfoot td{
		border-top: 1px solid #000;
	}

	li {
		font-size: 13px;
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


</style>
</head>
<body>
<div class="container">
	
     <div><!--Start of Form-->

     	<?php require_once('../transactions/form_heading.php') ?>
     	<div style="text-align:center; font-weight:bolder; margin-bottom:5px;">
            <u>QUOTATION</u>
        </div>
             
        <div class="content">
        	<div class="clearfix">
        		<div style="float:left;">
        			DYNAMIC Concrete Mix <br>
        			<?=$company_address?><br>
        			<?=$company_tel_no?>
        		</div>

        		<div style="float:right;">
        			Date : <?=lib::ymd2mdy($aTrans['date'])?> <br>
        			Quotation No. <?=$aTrans['premix_quotation_header_id']?>

        		</div>
        	</div>

        	<p>
        		Client: <?=$aTrans['client_info']?><br>
        		<?=$aTrans['client_address']?>
        	</p>

        	<p style="font-style:italic; font-weight:bold; text-align:center;">
        		Thank you for your patronage. We are happy to quote our PREMIX for your requirements
        	</p>

        	<p>
        		<table class="details-table" border=1>
		            <thead>
		                <tr>	                    
		                    <td>Specification/Strength</td>

		                    <td>Quantity (CU.M)</td>
		                    <td>Premix Cost/CU.M</td>		                  
		                    <td>Pumpcrete Cost/CU.M</td>
		                    <td>Total Cost/CU.M</td>
		                    <td>Total Amount</td>                  
		                </tr>
		            </thead>
		            <tbody>
		            <?php
		            $arr = getDetails($aTrans['premix_quotation_header_id']);
		            if( count($arr) ){                
		            	$g_amount = 0;
		                foreach( $arr as $r ){
		                	$g_amount += $r['total_amount'];

		                	$premix_cost_non_vat = $r['premix_cost'] / 1.12;
		                	$total_cost_non_vat = $premix_cost_non_vat + $r['pumpcrete_cost'];
		                	$total_amt_non_vat = $r['quantity'] * $total_cost_non_vat;
		            ?>

		            	<!-- W/ VAT -->
		                <tr>
		                	<td><?=$r['stock']?><br/>12% VAT Inclusive</td>	                    
		                    <td><?=number_format($r['quantity'],2)?></td>
		                    <td><?=number_format($r['premix_cost'],2)?></td>
							<td><?=number_format($r['pumpcrete_cost'],2)?></td>
							<td><?=number_format($r['pumpcrete_cost']+$r['premix_cost'],2)?></td>
							<td><?=number_format($r['premix_amount']+$r['pumpcrete_amount'],2)?></td>	                    
		                </tr>

		                <!-- W/O VAT -->
		               <!-- <tr>
		                	<td><?=$r['stock']?><br/>NON VAT</td>	                    
		                    <td><?=number_format($r['quantity'],2)?></td>
		                    <td><?=number_format($premix_cost_non_vat,2)?></td>
							<td><?=number_format($r['pumpcrete_cost'],2)?></td>
							<td><?=number_format($total_cost_non_vat,2)?></td>
							<td><?=number_format($total_amt_non_vat,2)?></td>
		                </tr>-->
		                
		                <tr style="border-right:1px solid #fff;">
		                	<td style="border-left:1px solid #fff;border-right:1px solid #fff;">&nbsp;</td>
		                </tr>
		            <?php
		                }#end foreach
		            }#end if
		            ?>	            
		            </tbody>
		            <!-- <tfoot>
		            	<tr>
		            		<td></td>
		            		<td></td>
		            		<td></td>
		            		<td><span style="font-weight:bold; border-bottom:3px double #000;"><?=number_format($g_amount,2)?></span></td>
		            	</tr>
		            </tfoot> -->
		        </table>  
        	</p>

        	<p>
        		<u style="font-weight:bold;">Terms and Conditions</u>
        		<ol>
					<li>This quotation is inclusive of 12% VAT and other government taxes required by the Philippine Government.</li>
					<li>Other related pouring tools/equipment such as stick vibrator, etc. must be provided by the clients.</li>
					<li>An additional charge of PhP. 10,000.00 for Mobilization if the client cannot attain our minimum volume requirement of 14 cu.m. for Direct Pouring and 50 cu.m. for Pumpcrete Design</li>
					<li>Deliver site must have good accessibility and free from any obstruction.</li>
					<li>Notification of pouring schedule shall be at least 5 days prior.</li>
					<li>Client may request a random sampling from the plant/site but DBCCI personnel will perform the actual sampling and curing to a maximum of (3) Three Pieces for FREE.</li>
					<li>Due to pouring requirement which require DBCCI to do overtime: the CLIENT shall pay for the total overtime cost plus service charge (10%).</li>
					<li>DBCCI personnel will handle, facilitate, transport, and cured concrete sample in project/Plant to the laboratory testing center.</li>
					<li>Prices are subject to change upon written notification.</li>
					<li>Terms of Payment 50% Down Payment and 50% 7 days PDC.</li>
					<li>In the event that the Client cancels or re-schedules the day of pouring without prior notice, he/she/it shall not be refunded of his/her downpayment; likewise, he/she/it will pay DBCCI the amount equivalent to incurred expenses plus fifteen (15%) percent service charge preparation.</li>
					<li>This Quotation excludes Degree of Difficulty cost and other additional.</li>
					<li>Cost Per Cu.m Varies due to Distance.</li>
					<li>This Quotation is valid only for 5 days upon receipt.</li>
        		</ol>
        	</p>
        	<p style="text-align:center;">
        		Thank you for considering our product as a choice for your requirement
        	</p>

        	<table cellspacing="0" cellpadding="5" align="center" width="98%" style="border:none; text-align:center; margin-top:100px;" class="summary">
		        <tr>
		            <td><p>
		                <input type="text" class="line_bottom" value="<?=lib::getUserFullName($aTrans['prepared_by'])?>"/><br>Prepared By</p></td>
		          	<td><p>
		                <input type="text" class="line_bottom" value="<?=lib::getEmployeeName($aTrans['noted_by'])?>" /><br>Noted by</p></td>
		            <td><p>
		                <input type="text" class="line_bottom" value="Ricardo V. Yanson, Jr." /><br>Approved by</p></td>
		        </tr>
		    </table>
        </div><!--End of content-->
    </div><!--End of Form-->
	<div class="divFooter">
        F-BAT-003<br>
        Rev. 1 06/25/14
    </div>

</div>
</body>
</html>