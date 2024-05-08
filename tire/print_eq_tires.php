<?php
	require_once('../my_Classes/options.class.php');
	include_once("../conf/ucs.conf.php");

	$options=new options();
	$from_date		= $_REQUEST['from_date'];
	$to_date		= $_REQUEST['to_date'];
	$categ_id2		= $_REQUEST['categ_id2'];
	$eqID			= $_REQUEST['eqID'];

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
        	EQUIPMENT TIRE LIST REPORT <br />
			<?php
				if(!empty($categ_id2)){
					echo $options->getAttribute("categories","categ_id",$categ_id2,"category");
				}else{
					echo "ALL EQUIPMENTS";
				}
			?>
			</br>
            From <?=date("m/d/Y",strtotime($from_date))?> to <?=date("m/d/Y",strtotime($to_date))?>
        </div>
        <br/>
        <div class="content">
        	
				<?php
					if(!empty($eqID)){
									$query="SELECT * FROM tiretransfer WHERE to_eqID = '$eqID' AND date BETWEEN '$from_date'
											   AND '$to_date' AND status!='C'";
									 $result=mysql_query($query) or die(mysql_error());
									 if(mysql_num_rows($result)){
										
											?>
											<table><tr><td colpsan="2">&nbsp;</td></tr></table>
											<table><tr><th colspan="4" style="text-align:left;"><b><?=$options->getAttribute("productmaster","stock_id",$eqID,"stock")?></b></th></tr></table>
											<table cellpadding="3" border=1 style="width:800px;">
													
													<tr>
														<th style="text-align:left;border-bottom:1px solid #000;width:30px;">NO.</th>
														<th style="text-align:left;border-bottom:1px solid #000;width:400px;">STOCK</th>
														<th style="text-align:left;border-bottom:1px solid #000;width:150px;">BRANDING #</th>
														<th style="text-align:left;border-bottom:1px solid #000;width:100px;">SIZE</th>
														<th style="text-align:left;border-bottom:1px solid #000;width:200px;">BRAND</th>
														<th style="text-align:left;border-bottom:1px solid #000;width:300px;">MANUFACTURER</th>
													</tr>
													<?php
														$c=1;
														while($r=mysql_fetch_assoc($result)){
																?>
																<tr>
																	<td  style="text-align:right;width:30px;"><?=$c?></td>
																	<td  style="text-align:left;width:400px;"><?=$options->getAttribute("productmaster","branding_number",$r[branding_num],"stock")?></td>
																	
																	<td  style="text-align:left;width:150px;"><?=$r[branding_num]?></td>
																	<td  style="text-align:left;width:100px;"><?=$options->getAttribute("productmaster","branding_number",$r[branding_num],"size")?></td>
																	<td  style="text-align:left;width:300px;"><?=$options->getAttribute("productmaster","branding_number",$r[branding_num],"brand")?></td>
																	<td  style="text-align:left;width:300px;"><?=$options->getAttribute("productmaster","branding_number",$r[branding_num],"manufacturer")?></td>
																</tr>
																<?php
																$c++;
														}
														?>
														</table>
														<?php
											 }
					}else{
						if(!empty($categ_id2)){
							$sql=mysql_query("SELECT * FROM productmaster WHERE categ_id2 = '$categ_id2' AND categ_id1 = '25' order by stock asc");
								while($rr=mysql_fetch_assoc($sql)){
									 $query="SELECT * FROM tiretransfer WHERE to_eqID = '$rr[stock_id]' AND date BETWEEN '$from_date'
											   AND '$to_date' AND status!='C'";
									 $result=mysql_query($query) or die(mysql_error());
									 if(mysql_num_rows($result)){
										
											?>
											<table><tr><td colpsan="2">&nbsp;</td></tr></table>
											<table><tr><th colspan="4" style="text-align:left;"><b><?=$rr['stock']?></b></th></tr></table>
											<table cellpadding="3" border=1 style="width:800px;">
													
													<tr>
														<th style="text-align:left;border-bottom:1px solid #000;width:30px;">NO.</th>
														<th style="text-align:left;border-bottom:1px solid #000;width:400px;">STOCK</th>
														<th style="text-align:left;border-bottom:1px solid #000;width:150px;">BRANDING #</th>
														<th style="text-align:left;border-bottom:1px solid #000;width:100px;">SIZE</th>
														<th style="text-align:left;border-bottom:1px solid #000;width:200px;">BRAND</th>
														<th style="text-align:left;border-bottom:1px solid #000;width:300px;">MANUFACTURER</th>
													</tr>
													<?php
														$c=1;
														while($r=mysql_fetch_assoc($result)){
																?>
																<tr>
																	<td  style="text-align:right;width:30px;"><?=$c?></td>
																	<td  style="text-align:left;width:400px;"><?=$options->getAttribute("productmaster","branding_number",$r[branding_num],"stock")?></td>
																	
																	<td  style="text-align:left;width:150px;"><?=$r[branding_num]?></td>
																	<td  style="text-align:left;width:100px;"><?=$options->getAttribute("productmaster","branding_number",$r[branding_num],"size")?></td>
																	<td  style="text-align:left;width:300px;"><?=$options->getAttribute("productmaster","branding_number",$r[branding_num],"brand")?></td>
																	<td  style="text-align:left;width:300px;"><?=$options->getAttribute("productmaster","branding_number",$r[branding_num],"manufacturer")?></td>
																</tr>
																<?php
																$c++;
														}
														?>
														</table>
														<?php
											 }
								
								}
						}else{
							$sql2=mysql_query("select * from categories where level = '2' and subcateg_id = '25'");
							while($rrr=mysql_fetch_assoc($sql2)){
								?>
									<table cellpadding="3"  border=0>
									<tr>
										<td style="font-size:12px;"><b><u><?=$rrr['category']?></u></b></td>	
									</tr>
									</table>
								<?php
								$sql=mysql_query("SELECT * FROM productmaster WHERE categ_id2 = '$rrr[categ_id]' AND categ_id1 = '25' order by stock asc");
								while($rr=mysql_fetch_assoc($sql)){
									 $query="SELECT * FROM tiretransfer WHERE to_eqID = '$rr[stock_id]' AND date BETWEEN '$from_date'
											   AND '$to_date' AND status!='C'";
									 $result=mysql_query($query) or die(mysql_error());
									 if(mysql_num_rows($result)){
										
											?>
													<table border=1 cellpadding=3  style="width:800px;">
													<tr><th colspan="4" style="text-align:left;border:1px solid #fff;border-bottom: 1px solid #000;"><b><?=$rr['stock']?></b></th></tr>
													<tr>
														<th style="text-align:left;border-bottom:1px solid #000;width:30px;">NO.</th>
														<th style="text-align:left;border-bottom:1px solid #000;width:400px;">STOCK</th>
														<th style="text-align:left;border-bottom:1px solid #000;width:150px;">BRANDING #</th>
														<th style="text-align:left;border-bottom:1px solid #000;width:100px;">SIZE</th>
														<th style="text-align:left;border-bottom:1px solid #000;width:200px;">BRAND</th>
														<th style="text-align:left;border-bottom:1px solid #000;width:300px;">MANUFACTURER</th>
													</tr>
													<?php
														$c=1;
														while($r=mysql_fetch_assoc($result)){
																?>
																<tr>
																	<td  style="text-align:right;width:30px;"><?=$c?></td>
																	<td  style="text-align:left;width:400px;"><?=$options->getAttribute("productmaster","branding_number",$r[branding_num],"stock")?></td>
																	
																	<td  style="text-align:left;width:150px;"><?=$r[branding_num]?></td>
																	<td  style="text-align:left;width:100px;"><?=$options->getAttribute("productmaster","branding_number",$r[branding_num],"size")?></td>
																	<td  style="text-align:left;width:300px;"><?=$options->getAttribute("productmaster","branding_number",$r[branding_num],"brand")?></td>
																	<td  style="text-align:left;width:300px;"><?=$options->getAttribute("productmaster","branding_number",$r[branding_num],"manufacturer")?></td>
																</tr>
																<?php
																$c++;
														}
														?>
														</table>
														<br/>
														<?php
									}
								
								}
								
								?>
								
								
								<?php
							}
						}
					}
				?>
           
        </div><!--End of content-->
    </div><!--End of Form-->
</div>
</body>
</html>
