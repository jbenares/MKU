<?php
require_once('../my_Classes/options.class.php');
include_once("../conf/ucs.conf.php");

$options		= new options();	
$rtp_header_id	= $_REQUEST['id'];	

$result = mysql_query("
	select
		*
	from
		rtp_header as h,  projects as p
	where
		h.project_id = p.project_id
	and
		rtp_header_id = '$rtp_header_id'
") or die(mysql_error());

$aRtp = mysql_fetch_assoc($result);

$project_id		= $aRtp['project_id'];
$project_name	= $aRtp['project_name'];
$description	= $aRtp['description'];
$status			= $aRtp['status'];
$user_id		= $aRtp['user_id'];
$approved_by	= $aRtp['approved_by'];
$date			= $aRtp['date'];
$date_needed	= $aRtp['date_needed'];
$date_received	= $aRtp['date_received'];
$encoded_datetime	= $aRtp['datetime_encoded'];

$work_category_id 	= $aRtp['work_category_id'];
$sub_work_category_id = $aRtp['sub_work_category_id'];

$work_category = $options->attr_workcategory($work_category_id,'work');
$sub_work_category = $options->attr_workcategory($sub_work_category_id,'work');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>RTP</title>
<script>

function printPage() { print(); } //Must be present for Iframe printing
</script>
<!--<link rel="stylesheet" type="text/css" href="../css/dprc_print.css"/> -->
<link rel="stylesheet" type="text/css" href="../css/print_report2.css" />
<style type="text/css">
.content .rtp_table td{
	border:none;
	border-left:1px solid #000;
	border-right:1px solid #000;	
}
.content .rtp_table tr:last-child td{
	border-bottom:1px solid #000;	
}
body *{
	font-size:15px;	
}
</style>
</head>
<body>
<div class="container">
	<?php require("form_heading.php"); ?>
     <div><!--Start of Form-->
     	<div style="text-align:right; font-weight:bolder;">
        <?=$aRtp['reference']?><br />
    </div>       
        <div style="text-align:center; font-size:12px;">
            
            <?php 
            if( $aRtp['type'] == "RTP" ){
                echo "REQUEST to PURCHASE LOG";
            } else {
                echo "TRANSMITTAL LOG";
            }
            ?>
        </div>
        <div class="header" style="">
            <table style="width:100%;">
                <tr>
                    <td width="13%">Project / Section:</td>
                    <td width="43%" style="border-bottom:1px solid #000;"><?=$project_name?></td>
                    
                    <td width="16%">Date Requested</td>
                    <td width="28%" style="border-bottom:1px solid #000;"><?=date("F j, Y",strtotime($date))?></td>
                </tr>
                <tr>
                  <td>Scope of Work:</td>
                  <td style="border-bottom:1px solid #000;"><?=$work_category." | ".$sub_work_category?></td>
                  
                  <td>Date Needed:</td>
                  <td style="border-bottom:1px solid #000;"><?=date("F j, Y",strtotime($date_needed))?></td>
               	</tr>
                <tr>
                    <td>Description :</td>
                    <td style="border-bottom:1px solid #000;"><?=$aRtp['remarks']?></td>
                    <td>Date Received:</td>
                    <td style="border-bottom:1px solid #000;"><?=date("F j, Y",strtotime($date_received))?></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td &nbsp;</td>
                    <td>Date Encoded:</td>
                    <td style="border-bottom:1px solid #000;"><?=date("F j, Y h:i:s",strtotime($encoded_datetime))?></td>
                </tr>
            </table>
        </div><!--End of header--><br />
     	
        
        <div class="content" style="">
        	<table class="rtp_table">
            	<tr>
                	<th width="40">Qty</th>
                    <th width="40">Unit</th>
                    <th>Item Description </th>
                    <th width="40">c/o MCD</th>
                    <th width="40" >In-House Budget</th>
                    <th width="40">Actual Received</th>
                    <th width="40">Balance</th>
                </tr>
                <?php
				$sql = "
					select * from rtp_detail where rtp_header_id = '$rtp_header_id' and rtp_void = '0'
				";
				
				$result = mysql_query($sql) or die(mysql_error());
				$i = 1;
				$t_qty = 0;
				while($r = mysql_fetch_assoc($result)){
					$t_qty += $r['quantity'];
					echo "
						<tr>
							<td style='text-align:right;'>".number_format($r['quantity'],4)."</td>
							<td>$r[unit]</td>
							<td>$r[description]</td>
							<td style='text-align:right;'>".number_format($r['mcd_qty'],2)."</td>
							<td style='text-align:right;'>".number_format($r['budget_qty'],2)."</td>
							<td style='text-align:right;'>".number_format($r['actual_qty'],2)."</td>
							<td style='text-align:right;'>".number_format($r['balance_qty'],2)."</td>
						</tr>
					";
					$i++;
				}
               	?>
                 <?php
                echo '<tr>';
				echo '<td>&nbsp;</td>';
				echo '<td>&nbsp;</td>';
				echo '<td>********** Nothing Follows **********</td>';
				echo '<td>&nbsp;</td>';	
				echo '<td>&nbsp;</td>';	
				echo '<td>&nbsp;</td>';
				echo '<td>&nbsp;</td>';	
				echo '</tr>';
				?>
                
                <?php
				if($i<20) {
					for($newi=$i;$newi<=26;$newi++) {
						echo '<tr>';
						echo '<td>&nbsp;</td>';
						echo '<td>&nbsp;</td>';
						echo '<td>&nbsp;</td>';
						echo '<td>&nbsp;</td>';
						echo '<td>&nbsp;</td>';	
						echo '<td>&nbsp;</td>';
						echo '<td>&nbsp;</td>';	
						echo '</tr>';
					}
				}
                ?>
            </table>
        </div><!--End of content-->
        <table cellspacing="0" cellpadding="5" align="center" width="98%" style="border:none; text-align:center; margin-top:10px;" class="summary">
            <tr>
                <td>Prepared By:<p>
                    <input type="text" class="line_bottom" /><br>
                        <input type="text" style="border:none; text-align:center; font-size:12px;" value="<?=$options->getUserName($user_id);?>" /> <br>
                        <span style='font-size:10px;'><?=$aRtp['datetime_encoded']?></span>
                    </p></td>
                <td>Checked By:<p>
                    <input type="text" class="line_bottom" /><br>&nbsp;</p></td>
              	<td>Noted By:<p>
                    <input type="text" class="line_bottom" /><br>E. Montinola</p></td>
               	<td>Received By:<p>
                    <input type="text" class="line_bottom" /><br>J. Roque</p></td>
               	<td>Recm'g Approval:<p>
                    <input type="text" class="line_bottom" /><br>J.E.T Cruz</p></td>
                <td>Approved By:<p>
                    <input type="text" class="line_bottom" /><br>R. Yanson Jr. </p></td>
            </tr>
        </table>
       
    </div><!--End of Form-->
</div>
</body>
</html>