<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
	
	$gltran_header_id=$_REQUEST[id];
	
	$query="
		select
			*
		from 
			gltran_header
		where
			gltran_header_id='$gltran_header_id'
	";
	$result=mysql_query($query);
	$r=mysql_fetch_assoc($result);
	
	$admin_id=$r[admin_id];
	$journal_code=$options->getJournalCode($r[journal_id]);
	
	if($journal_code=="DV"){
		$admin=$options->getUserName($admin_id);
	}
	
	
	
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
	/*margin:0px;*/
	font-family:Arial, Helvetica, sans-serif;
	font-size:11px;
}
.container{
	margin:0px auto;
	padding:0.1in;
}

.header
{
	text-align:center;	
	margin-top:50px;
}

.header table, .content table
{
	width:100%;
	text-align:left;
	

}
.header table td, .content table td
{
	padding:3px;
	
}

.content table{
	border-collapse:collapse;
}
.content table th{
	/*border:1px solid #000;
	*/
	padding:10px;
	border-top:1px solid #000;
	border-bottom:1px solid #000;
	
}
.table-summary td{
	border-top:1px solid #000;
	border-bottom:1px solid #000;
	padding:10px;
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
.line_bottom {
    border-bottom-width: 1px;
    border-bottom-style: solid;
    border-bottom-color: #000000;
    border-left: 0px;
    border-right: 0px;
    border-top: 0px;
    width:150px;
    font-size: 11px;
    text-align: center;
}


</style>
</head>
<body>
<div class="container">
	
     <div><!--Start of Form-->
         <?php
			require("form_heading.php");
        ?>

        <h2><?=$options->getJournalName($r[journal_id]);?></h2>

        <div class="header" style="">
        	<table style="width:100%;">
                <tr>
                	<td width="19%">Name of Payee:</td>
                    <td width="47%"><?=$options->getGLAccountName($r[account_id])?></td>
                    <td width="12%">Check  #: </td>
                    <td width="22%"><?=$r[mcheck]?></td>
               	</tr>
                <tr>
					<td>Address:</td>
                    <td><?=$r[address]?></td>
                    <td>Check Date:</td>
                    <td><?=($r['checkdate']=="0000-00-00")?"":$r['checkdate']?></td>
               	</tr>
                <tr>
                  <td>Particulars:</td>
                  <td><?=$r[particulars]." ".$r[details]?></td>
                  <td>Date:</td>
                  <td><?=$r['date']?></td>
                </tr>
                <tr>
                	<td>Reference:</td>
                    <td><?=$r['xrefer']?></td>
                    <td>General Reference:</td>
                    <td><?=$r['generalreference']?></td>
                </tr>
               
            </table>
   	   </div><!--End of header-->
        <?php
	
			$query="
				select
					*
				from
					gltran_detail
				where
					gltran_header_id='$gltran_header_id'
				and
					enable='Y'
			";
			
			$result=mysql_query($query) or die(mysql_error());		
		?>
        <div class="content" >
        	<table cellspacing="0">
            	<tr>
                	<th>Account Description</th>
                    <th>Description</th>
                    <th>Acode</th>
                    <th>Debit</th>
                    <th>Credit</th>
                </tr>
           		<?php
				$debit_total=0;
				$credit_total=0;
				while($r=mysql_fetch_assoc($result)):
					$debit_total+=$r[debit];
					$credit_total+=$r[credit];
				?>
                    <tr>
                        <td><?php echo $options->getGchartName($r[gchart_id]);?></td>
                        <?php if(empty($r['header_id'])){ ?>
                        <td><?=$options->getAttribute('projects','project_id',$r['project_id'],'project_name')?></td>
                        <?php } else { ?>
                        <td><?=$r['description']?></td>
                        <?php } ?>
                        <td><?=$options->getACodeFromGChartID($r[gchart_id])?></td>
                        <td><div align="right"><?=number_format($r[debit],2,'.',',')?></div></td>
                        <td><div align="right"><?=number_format($r[credit],2,'.',',')?></div></td>
                    </tr>
                    
                <?php
				endwhile;
				?>
                <tr class="table-summary" style="font-weight:bolder;">
                	<td colspan="3"><div align="right">Total</div></td>
                    <td><div align="right">P <?=number_format($debit_total,2,'.',',')?></div></td>
                    <td><div align="right">P <?=number_format($credit_total,2,'.',',')?></div></td>
                </tr>
            </table>
            <table width="100%" align="center" style="margin-top:50px;text-align:center;">
            	<tr>
                	<td>Prepared by:</p>
					<input type="text" class="line_bottom" /><br>Bookkeepper</p></td>
                    <td>Checked by:</p>
					<input type="text" class="line_bottom" /><br>Accounting Head</p></td>
                    <td>Approved by:</p>
					<input type="text" class="line_bottom" /><br>Finance Manager</p></td>
                   <!-- <td width="20%">Released by:</td>
                    <td width="20%">Received by:</td>-->
              	</tr>
                <!--<tr>
                    <td width="20%"><?=$admin;?></td>
                    <td>Bookkeepper</td>
                    <td>Accounting Head</td>
                    <td> Finance Manager</td>
                   <td width="20%"></td>
                </tr>-->
            </table>
        </div><!--End of content-->
    </div><!--End of Form-->
    

  


</div>
</body>
</html>