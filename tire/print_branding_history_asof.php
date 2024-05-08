<?php
	require_once('../my_Classes/options.class.php');
	include_once("../conf/ucs.conf.php");

	$options=new options();
	$from_date		= $_REQUEST['from_date'];

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
*{
	font-family:Arial;	
}
 .content table tr td{
 	font-size:11px;
 }
 .content table tr th{
 	font-size:12px;
 }
 .content table{
	border-collapse:collapse;
 }
</style>


<link rel="stylesheet" type="text/css" href="css/print.css"/>
</head>
<body>
<div class="container">

     <div><!--Start of Form-->

     	<div style="font-weight:bolder;">
        	TIRE BRANDING HISTORY REPORT <br />

            AS OF <?=date("m/d/Y",strtotime($from_date))?>
        </div>
        <br/>
        <div class="content">
        	<table cellpadding="3" border=1 style="width:1500px;">
            	<tr>
					<th style="text-align:left;">NO.</th>
                    <th style="text-align:left;">DATE</th>
                    <th style="text-align:left;">BRANDING #</th>
                    <th style="text-align:left;">TRANSFER #</th>
					<th style="text-align:left;">SIZE</th>
                    <th style="text-align:left;">FROM PROJECT</th>
					<th style="text-align:left;">TO PROJECT</th>
                    <th style="text-align:left;">FROM EQUIPMENT</th>
					<th style="text-align:left;">TO EQUIPMENT</th>
                    <th style="text-align:left;">FROM POSITION</th>
                    <th style="text-align:left;">TO POSITION</th>
                </tr>

             	<?php
					$query="SELECT * FROM tiretransfer WHERE date <='$from_date' AND status!='C' AND branding_num != '0' ORDER by branding_num,date DESC";
					$result=mysql_query($query) or die(mysql_error());
					$c=1;
					$branding_num = "";
					while($r=mysql_fetch_assoc($result)){
							if($branding_num == $r['branding_num']){
								continue;
							}else{
								$branding_num = $r['branding_num'];
							}
							?>
							<tr>
								<td style="text-align:center;"><?=$c?></td>
								<td><?=date("m/d/Y",strtotime($r[date]))?></td>
								<td style="text-align:center;"><?=$r[branding_num]?></td>
								<td style="text-align:center;"><?=$r[tiretransfer_header_id]?></td>
								<td><?=$options->getAttribute("productmaster","branding_number",$r[branding_num],"size")?></td>
								<td><?=$options->getAttribute("projects","project_id",$r[from_project_id],"project_name")?></td>
								<td><?=$options->getAttribute("projects","project_id",$r[to_project_id],"project_name")?></td>
								<td><?=$options->getAttribute("productmaster","stock_id",$r[from_eqID],"stock")?></td>
								<td><?=$options->getAttribute("productmaster","stock_id",$r[to_eqID],"stock")?></td>
								<td style="text-align:center;"><?=$options->getTirePositionValue($r[from_position])?></td>
								<td style="text-align:center;"><?=$options->getTirePositionValue($r[to_position])?></td>
							</tr>
							<?php
							$c++;
					}
				?>
			
				<tr style="font-weight:bolder;">
					<td colspan="3">Total Results: </td>
					<td colspan="9" style="text-align:left;"><?=$c-1?></td>
				</tr>
            </table>

        </div><!--End of content-->
    </div><!--End of Form-->
</div>
</body>
</html>
