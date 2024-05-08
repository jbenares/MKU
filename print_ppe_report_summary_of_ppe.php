<?php
require_once(dirname(__FILE__).'/library/lib.php');
require_once('my_Classes/options.class.php');
require_once("my_Classes/depreciation.class.php");
include_once("conf/ucs.conf.php");

$options=new options();	
$fdate			= $_REQUEST['fdate'];
$tdate			= $_REQUEST['tdate'];
$rr_detail_id	= $_REQUEST['rr_detail_id'];
$asset_code     = $_REQUEST['asset_code'];
$categ1          = $_REQUEST['categ1'];
$categ2          = $_REQUEST['categ2'];


function getStatusAndLocationOfAsset($rr_detail_id){
    $sql = "
        select 
            item_status, project_name as location, accountable_id,account,remarks
        from
            accountables as a 
        left join projects as p on a.project_id = p.project_id
		left join account as ac on a.account_id = ac.account_id
        where
            rr_detail_id = '$rr_detail_id'
        and accountables_void = '0'
        order by accountable_id desc
        limit 1
    ";

    $arr = lib::getTableAttributes($sql);
    return $arr;
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
<link rel="stylesheet" type="text/css" href="css/print.css"/>
<style type="text/css">
.table-print tr:nth-child(1) td{
	border-top:1px solid #000;
	border-bottom:1px solid #000;
	font-weight:bold;
}
.table-print tr td:nth-child(2),.table-print tr td:nth-child(n+6){
	text-align:right;
}

.tr-summary{
	border-top:1px solid #000;	
}
.summary-highlight{
	border-bottom:3px double #000;	
	font-weight:bold;
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

</style>
</head>
<body>
<div class="container">
	
     <div><!--Start of Form-->
     	
     	<div style="font-weight:bolder;">
        	SUMMARY OF PROPERTY, PLANT & EQUIPMENT REPORT<br />
           	As of <?=date("M/d/Y",strtotime($tdate))?>
        </div>           
        <div class="content" style="">
        	<table cellpadding="3" class="table-print">
            	<tr>
					<td>DATE ACQUIRED</td>
                	<td>MRR #</td>
					<td style="text-align:center;">AR #</td>
					<td>P.O. #</td>
					<!--<td>P.O. DATE</td>-->
                    <td>UNIT</td>                   
                    <td>ASSET NAME</td>                  
                    <td style="text-align:left;">ASSET DETAILS</td>
					<td>ASSET CODE</td>
                    <td style="text-align:left;">SERIAL NO.</td>
                    <td style="text-align:left;">BRAND/MODEL NO.</td>
                    <td>ACQUISITION COST</td>
                    <td>ESTIMATED USEFUL LIFE IN MONTHS</td>
                    <td>MONTHLY DEPRECIATION</td>
                    <td>ACCUMULATED DEPRECIATION</td>
                    <td>NET BOOK VALUE</td>
                    <td style="text-align:center;">STATUS</td>
                    <td style="text-align:center;">LOCATION</td>
					<td style="text-align:center;">EMPLOYEE</td>
					<td style="text-align:center;">REMARKS</td>
                </tr>	
                
				<?php
				
				$sql = "
					select
						h.rr_header_id,rr_detail_id,stock,asset_code,details,date_acquired,d.cost,estimated_life,quantity,d.details,
                        serial_no,model,h.po_header_id
					from
						rr_header as h, rr_detail as d, productmaster as p
					where 
						h.rr_header_id = d.rr_header_id
					and d.stock_id = p.stock_id
					and h.status != 'C'
					and rr_type = 'A'
				";
				if(!empty($rr_detail_id)) $sql .= "and d.rr_detail_id = '$rr_detail_id'";  
                if( !empty($asset_code) ) $sql .= " and asset_code like '%$asset_code%'";
                if( !empty($categ1) ) $sql .= " and p.categ_id1 = '$categ1'";
				if( !empty($categ2) ) $sql .= " and p.categ_id2 = '$categ2'";
				if (!empty ($fdate )&& ($tdate)) $sql .="and date_acquired between '$fdate' and '$tdate'";

                $sql .= " order by stock asc";
#echo $sql;
				$result = mysql_query($sql) or die(mysql_error());
				$total_cost = 0;
				$total_monthly_dep = 0;
				$total_accu_dep = 0;
				$total_net_book = 0;
				while($r = mysql_fetch_assoc($result)){
					$total_cost += $r['cost'];
					$total_monthly_dep += Depreciation::getMonthlyDepreciation($r['cost'],$r['estimated_life']);
					$total_accu_dep += Depreciation::getAccumulatedDepreciation($r['date_acquired'],$tdate,$r['cost'],$r['estimated_life']);
					$total_net_book += Depreciation::getNetBookValue($r['date_acquired'],$tdate,$r['cost'],$r['estimated_life']);

                    $arr_status_loc = getStatusAndLocationOfAsset($r['rr_detail_id']);
                ?>  
<!--<?php
echo $sql;
?>-->
                <tr>
					<td><?=($r['date_acquired'] == "0000-00-00") ? "" :  date("m/d/Y",strtotime($r['date_acquired']))?></td>
                	<td><?=str_pad($r['rr_header_id'],7,0,STR_PAD_LEFT)?></td>
					<td><?=str_pad($arr_status_loc['accountable_id'],7,0,STR_PAD_LEFT)?></td>
					<td><?=str_pad($r['po_header_id'],7,0,STR_PAD_LEFT)?></td>
					<!-- <td><?=date("m/d/Y",strtotime($r['date']))?></td>-->
                	<td><?=$r['quantity']?></td> 
                    <td><?=htmlentities($r['stock'])?></td>
                    <td style="text-align:left;"><?=htmlentities($r['details'])?></td>
					<td><?=htmlentities($r['asset_code'])?></td>
                    <td style="text-align:left;"><?=htmlentities($r['serial_no'])?></td>
                    <td style="text-align:left;"><?=htmlentities($r['model'])?></td>
                    <td><?=number_format($r['cost'],2,'.',',')?></td>
                    <td><?=$r['estimated_life']?> MONTHS</td>
                    <td><?=number_format(Depreciation::getMonthlyDepreciation($r['cost'],$r['estimated_life']),2,'.',',')?></td>
                    <td><?=number_format(Depreciation::getAccumulatedDepreciation($r['date_acquired'],$tdate,$r['cost'],$r['estimated_life']),2,'.',',')?></td>
                    <td><?=number_format(Depreciation::getNetBookValue($r['date_acquired'],$tdate,$r['cost'],$r['estimated_life']),2,'.',',')?></td>
                    <td style="text-align:center;"><?=$arr_status_loc['item_status']?></td>
                    <td style="text-align:center;"><?=$arr_status_loc['location']?></td>
					<td style="text-align:center;"><?=$arr_status_loc['account']?></td>
					<td style="text-align:center;"><?=$arr_status_loc['remarks']?></td>
                </tr>        	
                <?php } ?>
                <tr class="tr-summary">
                	<td></td>
                    <td></td>
				    <td></td>
					<td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><span class="summary-highlight"><?=number_format($total_cost,2,'.',',')?></span></td>
                    <td></td>
                    <td><span class="summary-highlight"><?=number_format($total_monthly_dep,2,'.',',')?></span></td>
                    <td><span class="summary-highlight"><?=number_format($total_accu_dep,2,'.',',')?></span></td>
                    <td><span class="summary-highlight"><?=number_format($total_net_book,2,'.',',')?></span></td>
                    <td></td>
                    <td></td>
                </tr>
            </table>
            
            <table cellspacing="0" cellpadding="5" align="center" width="98%" style="border:none; text-align:center; margin-top:10px;" class="summary">
                <tr>
                    <td>Prepared by:<p>
                        <input type="text" class="line_bottom" /><br>Bookkeeper</p></td>
                    <td>Noted by:<p>
                        <input type="text" class="line_bottom" /><br>Chief Financial Officer</p></td>
                </tr>
           	</table>
        
        </div><!--End of content-->
    </div><!--End of Form-->
</div>
</body>
</html>