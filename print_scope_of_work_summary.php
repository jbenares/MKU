<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>JOB ORDER</title>
<script>
function printPage() { print(); } //Must be present for Iframe printing
print();
</script>
<link rel="stylesheet" type="text/css" href="css/print.css"/>
</head>
<body>
<div class="container">
	
     <div><!--Start of Form-->
     	
     	<div style="font-weight:bolder;">
        	SCOPE OF WORK SUMMARY REPORT <br />
        </div>           
        <div class="content" style="">        	    
			<?php
                $query = "
                    select
                        *
                    from
                        work_category
                    where
                        work like '%$keyword%'
                    and 
                        level = '1'
                ";
                $result=mysql_query($query) or die(mysql_error());
				echo "<ol>";
                while($r=mysql_fetch_assoc($result)){
					
					echo "<li>$r[work]</li>";
					echo "<ul>";
                    $list = $options->list_work_sub_category($work_category_id);
                    foreach($list as $list_item){
                     	echo "<li>$list_item[work]</li>";   
                    }
					echo "</ul>";
                }
				echo "</ol>";
            ?>
        </div><!--End of content-->
    </div><!--End of Form-->
</div>
</body>
</html>