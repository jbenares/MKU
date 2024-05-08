<?php
	include_once("../conf/ucs.conf.php");

	$to = $_REQUEST['to_date'];
	$from= $_REQUEST['from_date'];
	$stock_id = $_REQUEST[stock_id];	

	$select=mysql_query("select * from productmaster where stock_id='$stock_id'");
	$fetch=mysql_fetch_assoc($select);
	extract($fetch);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script>
function printPage() { print(); } //Must be present for Iframe printing
</script>
<style type="text/css">
	body
	{
		size: legal portrait;
		padding:0px;
		font-family:Arial, Helvetica, sans-serif;
		font-size:13px;
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

	table{ border-collapse:collapse; width:100%; }
	table td{ padding:3px; text-align:center;}
	table thead td{ font-weight:bold;  border-top:1px solid #000; border-bottom:1px solid #000; }
	table tfoot td{
		border-top:1px solid #000;
		font-weight:bold;	
	}
	@media print{
		table { page-break-inside:auto }
		tr{ page-break-inside:avoid; page-break-after:auto }
		thead { display:table-header-group }
		tfoot { display:table-footer-group }
		.pb { page-break-after:always }
	}
	
</style>
</head>
<body>
	<h2>Parts File List</h2>
	<div style="display:inline-block;">
		Equipment Name : <?=$stock?>
	</div>
	<br/>
	<div style="display:inline-block;">
		Date Added : <?=$dateadded?>
	</div>
<br/><br/>
	<table border='1' cellpadding="1" cellspacing='0' width='100%'>
		<tr>
			<th style="witdth:10px;">#</th>
			<th style="width:450px;">PART ITEM</th>
			<th>KMS RUN</th>
			<th>DAYS RUN</th>
			<th>DATE INSTALLED</th>
		</tr>

		<?php
			$sql="
					select
						*,jh.date as date_ins
					from
						issuance_detail as i,joborder_header as jh,joborder_detail as jb,
						productmaster as p
					where
					   jh.equipment_id = '$stock_id'
					and
						jh.joborder_header_id = jb.joborder_header_id
					and
						jb.issuance_detail_id = i.issuance_detail_id
					and
						jh.date BETWEEN '$from' and '$to'
					and
						jb.joborder_detail_void !='1'
					and
						p.stock_id=i.stock_id
					and
						(p.categ_id1='6' or p.categ_id1='10')
				 ";
			$query=mysql_query($sql);
			$c=1;
			$km=0;
			while($f=mysql_fetch_assoc($query)){
					extract($f);
					$date_installed=$f['date_ins'];
					$sql2="
							select 
								*
							from
								eur_header as eh,eur_detail as ed
							where
								eh.stock_id='".$_REQUEST[stock_id]."'
							and
								eh.eur_header_id = ed.eur_header_id
							and
								ed.eur_void !='1'
							and
								ed.released_date>='$date_installed'
						  ";
					$query2=mysql_query($sql2);
					while($ff=mysql_fetch_assoc($query2)){
							$km_r=$ff[km];
							$km=$km_r+$km;
					}
					//get days difference
					$start = strtotime($date_installed);
					$end = strtotime($to);

					$days_between = ceil(abs($end - $start) / 86400);
					?>
					<tr>
						<td><?=$c++?></td>
						<td style="text-align:left;border-bottom:0px;"><?=$stock?></td>
						<td style="text-align:right;"><?=number_format($km,2)?></td>
						<td style="text-align:right;"><?=$days_between?></td>
						<td><?=$date_installed?></td>
					</tr>
					<?php
			}
		?>
	</table>
</body>
</html>