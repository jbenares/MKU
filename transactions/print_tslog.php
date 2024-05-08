<?php
require_once('../my_Classes/options.class.php');
include_once("../conf/ucs.conf.php");

$options		= new options();	
$translog_header_id	= $_REQUEST['id'];	

$result = mysql_query("
	select
		*
	from
		transfer_log_header as h,  projects as p
	where
		h.project_id = p.project_id
	and
		translog_header_id = '$translog_header_id'
") or die(mysql_error());

$aTslog = mysql_fetch_assoc($result);


$project_id		= $aTslog['project_id'];
$to_project_id	= $aTslog['to_project_id'];
$project_name	= $aTslog['project_name'];
$description	= $aTslog['description'];
$status			= $aTslog['status'];
$user_id		= $aTslog['user_id'];
$approved_by	= $aTslog['approved_by'];
$date			= $aTslog['date'];
$date_needed	= $aTslog['date_needed'];
$date_received	= $aTslog['date_received'];
$datetime_encoded	= $aTslog['datetime_encoded'];

$work_category_id 	= $aTslog['work_category_id'];
$sub_work_category_id = $aTslog['sub_work_category_id'];

$work_category = $options->attr_workcategory($work_category_id,'work');
$sub_work_category = $options->attr_workcategory($sub_work_category_id,'work');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>TRANSMITTAL LOG</title>
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
	<?php require("form_heading.php"); ?>
     <div><!--Start of Form-->
     	<div style="text-align:right; font-weight:bolder;">
        T.S. LOG # :&nbsp&nbsp&nbsp<?=str_pad($translog_header_id,7,0,STR_PAD_LEFT)?><br />
    </div>       
        <div style="text-align:center; font-size:16px;font-weight:bolder;">
					TRANSMITTAL LOG
        </div><p>
        <div class="header" style="">
            <table style="width:100%;">
                <tr>
                   
					<td width="13%">From Project/Section:</td>
                    <td width="43%" style="border-bottom:1px solid #000;"><?=$project_name?></td>&nbsp&nbsp
					<td width="13%">Date:</td>
                    <td width="43%"style="border-bottom:1px solid #000;"><?=date("F j, Y",strtotime($date))?></td>
                    
                </tr>
                <tr>
				    <td>To Project/Section</td>
                    <td style="border-bottom:1px solid #000;"><?=$options->getAttribute('projects','project_id',$to_project_id,'project_name')?></td>
                    <td width="13%">Reference: </td>
                    <td width="43%" style="border-bottom:1px solid #000; margin-right:30px;"><?=$aTslog['reference']?></td>
                 <!-- <td>Date Needed:</td>
                  <td style="border-bottom:1px solid #000;"><?=date("F j, Y",strtotime($date_needed))?></td>-->
               	</tr>
                <tr>
				  
                 <td>Scope of Work:</td>
                  <td style="border-bottom:1px solid #000;"><?=" | ".$work_category." | ".$sub_work_category?></td> 
                   <td>Remarks :</td>
                    <td style="border-bottom:1px solid #000;"><?=$aTslog['remarks']?></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>    
                </tr>
            </table>
        </div><!--End of header--><br />
     	
        
        <div class="content" style="">
        	<table class="rtp_table">
            	<tr>
                	<th width="120" height="30px">Qty</th>
                    <th width="120">Unit</th>
                    <th>Item Description </th>
                    <!--<th width="40">c/o MCD</th>
                    <th width="40" >In-House Budget</th>
                    <th width="40">Actual Received</th>
                    <th width="40">Balance</th>-->
                </tr>
                <?php
				$sql = "
					select * from transfer_log_detail where translog_header_id = '$translog_header_id' and tslog_void = '0'
				";
				
				$result = mysql_query($sql) or die(mysql_error());
				$i = 1;
				$t_qty = 0;
				while($r = mysql_fetch_assoc($result)){
					$t_qty += $r['quantity'];
					echo "
						<tr>
							<td style='text-align:right;'>".number_format($r['quantity'],2)."</td>
							<td style='text-align:center;'>$r[unit]</td>
							<td>$r[description]</td>
						</tr>
							<!--<td style='text-align:right;'>".number_format($r['mcd_qty'],2)."</td>
							<td style='text-align:right;'>".number_format($r['budget_qty'],2)."</td>
							<td style='text-align:right;'>".number_format($r['actual_qty'],2)."</td>
							<td style='text-align:right;'>".number_format($r['balance_qty'],2)."</td>
						</tr>-->
					";
					$i++;
				}
               	?>
                 <?php
                echo '<tr>';
				echo '<td>&nbsp;</td>';
				echo '<td>&nbsp;</td>';
				echo '<td>****************** Nothing Follows ********************</td>';
				/*echo '<td>&nbsp;</td>';	
				echo '<td>&nbsp;</td>';	
				echo '<td>&nbsp;</td>';
				echo '<td>&nbsp;</td>';*/	
				echo '</tr>';
				?>
                
               <?php
				if($i<15) {
					for($newi=$i;$newi<=20;$newi++) {
						echo '<tr>';
						echo '<td>&nbsp;</td>';
						echo '<td>&nbsp;</td>';
						echo '<td>&nbsp;</td>';
						/*echo '<td>&nbsp;</td>';
						echo '<td>&nbsp;</td>';	
						echo '<td>&nbsp;</td>';
						echo '<td>&nbsp;</td>';	
						echo '</tr>';*/
					}
				}
                ?>
            </table>
        </div><!--End of content-->
        <table cellspacing="0" cellpadding="5px" align="center"  width="100%" style="border:none; text-align:center; margin-top:40px;" class="summary">
            <tr>
                <td>Encoded By:<p>
                    <input type="text" class="line_bottom" /><br>
					 <input type="text" style="border:none; text-align:center; font-size:12px;" value="<?=$options->getUserName($user_id);?>" /> <br>
                        <span style='font-size:10px;'><?=$aTslog['datetime_encoded']?></span>
                    </p></td>
					
				<!--<td>Recm'g Approval:<p>
                    <input type="text" class="line_bottom" /><br>J.E.T Cruz</p></td>-->
                <td>Approved By:<p>
                    <input type="text" class="line_bottom" /><br>Dept. Head / P.I.C.</p></td>
					
              	<td>Transmitted By:<p>
                    <input type="text" class="line_bottom" /><br>Driver / Bearer</p></td>
					
				<!--<td>Approved By:<p>
                    <input type="text" class="line_bottom" /><br>R. Yanson Jr. </p></td>-->
     
               	<td>Received By:<p>
                    <input type="text" class="line_bottom" /><br>Signature over Printed Name</p></td>
               	
              </tr> 
        </table>
       
    </div><!--End of Form-->
</div>
<div class="divFooter">
    F-WHS-014<br>
    Rev. 0 03/12/16
</div>
</body>
</html>