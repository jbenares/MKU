<?php
include_once("../conf/ucs.conf.php");
include_once("../library/lib.php");

define('TITLE', "FINISHED PRODUCT FABRICATION SUMMARY");

$from_date     = $_REQUEST['from_date'];
$to_date       = $_REQUEST['to_date'];
$to_project_id = $_REQUEST['to_project_id'];


function getReport($from_date,$to_date, $to_project_id ){

  if ( !empty($from_date) && !empty($to_date) ) {
    $sql_date = " and f.date between '$from_date' and '$to_date'";
  } else {
    $sql_date = " and f.date <= '$from_date'";
  }

  $sql="
    select
      d.*, stock, to_proj.project_name as to_project_name,date, stock_length
    from
      fabrication as f 
      inner join fabrication_product as d on f.fabrication_id = d.fabrication_id
      inner join productmaster as p on d.product_stock_id = p.stock_id
      left join projects as to_proj on to_proj.project_id = f.to_project_id
    where
      product_void = '0'
    $sql_date    
  ";

  if( $to_project_id ) $sql .= " and to_project_id = '$to_project_id'";

  $result = mysql_query($sql) or die(mysql_error());
  $a = array();
  while( $r = mysql_fetch_assoc( $result ) ){
    $a['summary']['t_quantity']     += $r['product_quantity'];
    $a['summary']['t_total_weight'] += $r['product_total_weight'];

    $a['details'][] = $r;
  }

  return $a;
  
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
  size: legal portrait;
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
table  tr td:nth-child(n+6){
 text-align:right; 
}
table td{
  padding:3px;  
}
tfoot td{
  font-weight: bold;
  border-top: 1px solid #000;
}
</style>
</head>
<body>
<div class="container">
  
     <div><!--Start of Form-->
      
      <div style="text-align:center; font-weight:bolder; margin-bottom:5px;">
          <?=$title?> <br />
            <u style="text-transform:uppercase;"><?=TITLE?></u>
            <p style="text-align:center;">                          
              <?php
              if( !empty($from_date) && !empty($to_date) ){
               echo "From ".lib::ymd2mdy($from_date)." to ".lib::ymd2mdy($to_date);
            } else {
              echo "As of ".lib::ymd2mdy($from_date);
            }
              
              ?>
            </p>
        </div>
        <div class="content" >
          <table cellspacing="0">
              <thead>
                    <tr>
                        <td>FAB#</td>
                        <td>DATE</td>
                        <td>PROJECT</td>
                        <td>CLR#</td>
                        <td>ITEM</td>
                        <td>LENGTH</td>
                        <td>WEIGHT</td>
                        <td>QUANTITY</td>
                        <td>TOTAL WEIGHT</td>
                    </tr>
                </thead>
                <tbody>                  
                    <?php                               
                    $aReport= getReport($from_date,$to_date,$to_project_id);

                    if(count($aReport['details']))
                      foreach( $aReport['details'] as $r ){
                        echo "
                            <tr>
                              <td>".str_pad($r['fabrication_id'],7,0,STR_PAD_LEFT)."</td>
                              <td>".lib::ymd2mdy($r['date'])."</td>
                              <td>$r[to_project_name]</td>
                              <td>$r[clr_no]</td>
                              <td>$r[stock]</td>
                              <td>".number_format($r['stock_length'],4)."</td>
                              <td>".number_format($r['product_weight_per_unit'],4)."</td>
                              <td>".number_format($r['product_quantity'],4)."</td>
                              <td>".number_format($r['product_total_weight'],4)."</td>
                            </tr>
                        ";    
                    }               
                    ?>
              </tbody>
              <tfoot>
                <tr>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td><?=number_format($aReport['summary']['t_quantity'],4)?></td>
                  <td><?=number_format($aReport['summary']['t_total_weight'],4)?></td>
                </tr>
              </tfoot>
            </table>            
        </div><!--End of content-->
    </div><!--End of Form-->

</div>
</body>
</html>

