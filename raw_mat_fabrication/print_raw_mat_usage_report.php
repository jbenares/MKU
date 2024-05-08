<?php
include_once("../conf/ucs.conf.php");
include_once("../library/lib.php");
require_once(dirname(__FILE__).'/../library/Fabrication.php');

define('TITLE', "RAW MATS USAGE REPORT");

$from_date  = $_REQUEST['from_date'];
$to_date    = $_REQUEST['to_date'];
$project_id = $_REQUEST['project_id'];
$mat_type = $_REQUEST['mat_type'];

$arr = Fabrication::getRawMaterialsUsed($from_date,$to_date,$project_id,$mat_type);
  
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
table  tr td:nth-child(n+4){
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
              echo "As of ".lib::ymd2mdy($date);
            }
              
              ?>
            </p>
        </div>
        <div class="content" >
          <table cellspacing="0">
              <thead>
                    <tr>
                        <td>DATE</td>                        
                        <td>PROJECT</td>                        
                        <td>RAW MATERIAL</td>
                        <td>USAGE</td>
                    </tr>
                </thead>
                <tbody>                  
                    <?php                   
                    if(count($arr))
                      foreach( $arr as $r ){                        
                        $aTotal['raw_mat_quantity'] += $r['raw_mat_quantity'];                        

                        echo "
                            <tr>
                              <td>".lib::ymd2mdy($r['date'])."</td>
                              <td>$r[project_name]</td>
                              <td>$r[stock]</td>
                              <td>".number_format($r['raw_mat_quantity'],4)."</td>                              
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
                  <td><?=number_format($aTotal['raw_mat_quantity'],4)?></td>
                </tr>
              </tfoot>
            </table>            
        </div><!--End of content-->
    </div><!--End of Form-->

</div>
</body>
</html>

