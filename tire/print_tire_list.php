<?php
	require_once('../my_Classes/options.class.php');
	include_once("../conf/ucs.conf.php");

	$options=new options();
	$type_id	= $_REQUEST['type_id'];

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
        	TIRE LIST REPORT <br />
            As Of <?=date("F d, Y",strtotime(date("Y-m-d")))?>
        </div>
        <br/>
        <div class="content">
			<?php
				if(!empty($type_id)){
					?>
						<table cellpadding="3" border=1 style="width:900px;">
							 <tr>
								<th style="text-align:center;">NO.</th>
								<th style="text-align:left;">BRANDING #</th>
								<th style="text-align:left;">SIZE</th>
								<th style="text-align:left;">STOCK</th>
								<th style="text-align:left;">BRAND</th>
								<th style="text-align:left;">MANUFACTURER</th>
								
							</tr>
			
							<?php
								$query="SELECT * FROM productmaster WHERE tire_type = '$type_id'
										 AND
											categ_id1 = '10'
										 AND
											categ_id2 = '30'
										 AND
											branding_number !=''
										 ORDER BY
											branding_number ASC
										";
								$result=mysql_query($query) or die(mysql_error());
								$c=1;
								while($r=mysql_fetch_assoc($result)){
										$checking = mysql_query("SELECT * FROM junk_tires where branding_num = '$r[branding_number]'");
										if(!mysql_num_rows($checking)){
											?>
											<tr>
												<td style="text-align:center;"><?=$c?></td>
												<td style="text-align:left;"><?=$r['branding_number']?></td>
												<td><?=$r['size']?></td>
												<td style="text-align:left;"><?=$r['stock']?></td>
												<td style="text-align:left;"><?=$r['brand']?></td>
												<td style="text-align:left;"><?=$r['manufacturer']?></td>
												
											</tr>
											<?php
											$c++;
										}
										
										
								}
							?>
			
							<tr>
								<td>&nbsp;</td>
								<td colspan="1"  style="font-weight:bolder;">Total Result/s: </td>
								<td colspan="1" style="text-align:right;font-weight:bolder;"><?=$c-1?></td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>
						</table>
						<?php
				}else{
					$sql=mysql_query("select * from tire_type order by type_id asc");
					while($rr=mysql_fetch_assoc($sql)){
						?>
							<table cellpadding="3" border=1 style="width:900px;">
								 <tr>
									<th style="text-align:center;">NO.</th>
									<th style="text-align:left;">BRANDING #</th>
									<th style="text-align:left;">SIZE</th>
									<th style="text-align:left;">STOCK</th>
									<th style="text-align:left;">BRAND</th>
									<th style="text-align:left;">MANUFACTURER</th>
									
								</tr>
				
								<?php
									$query="SELECT * FROM productmaster WHERE tire_type = '$rr[type_id]'
											 AND
												categ_id1 = '10'
											 AND
												categ_id2 = '30'
											 AND
												branding_number !=''
										     ORDER BY
												branding_number ASC
											";
									$result=mysql_query($query) or die(mysql_error());
									$c=1;
									while($r=mysql_fetch_assoc($result)){
											$checking = mysql_query("SELECT * FROM junk_tires where branding_num = '$r[branding_number]'");
											if(!mysql_num_rows($checking)){
												?>
												<tr>
													<td style="text-align:center;"><?=$c?></td>
													<td style="text-align:left;"><?=$r['branding_number']?></td>
													<td><?=$r['size']?></td>
													<td style="text-align:left;"><?=$r['stock']?></td>
													<td style="text-align:left;"><?=$r['brand']?></td>
													<td style="text-align:left;"><?=$r['manufacturer']?></td>
													
												</tr>
												<?php
												$c++;
											}	
									}
								?>
				
								<tr>
									<td>&nbsp;</td>
									<td colspan="1"  style="font-weight:bolder;">Total Result/s: </td>
									<td colspan="1" style="text-align:right;font-weight:bolder;"><?=$c-1?></td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
								</tr>
							</table>
							<br/>
						<?php
					}
				}
			?>
        </div><!--End of content-->
    </div><!--End of Form-->
</div>
</body>
</html>
