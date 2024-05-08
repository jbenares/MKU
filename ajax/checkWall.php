<?php
  require('../conf/ucs.conf.php');
  require('../my_Classes/options.class.php');

  $wallcountpre = $_REQUEST[data];
  $options = new options();
  $sql = mysql_query("select * from wall order by date_posted desc");
  $r=mysql_fetch_assoc($sql);
  $row = mysql_num_rows($sql);
  #echo 'Message By: '.$options->getUserName($r['posted_by']).'<br/>'.$r['wallmsg'].'-'.$row;
  $data = array();
  if($wallcountpre != $row){
     $string = '<b>Posted By: '.$options->getUserName($r['posted_by']).'</b><br/><br/>'.$r['wallmsg'];
     array_push($data, $string);
     array_push($data, $row);

     echo json_encode($data);
     //var_dump($data);
  }else{
     array_push($data, "0");
     echo json_encode($data);
  }
?>
