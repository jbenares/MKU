<?php

	$b = $_REQUEST['b'];
	$checkList = $_REQUEST['checkList'];
	
	if($b=='Delete Selected') {
	  if(!empty($checkList)) {
		foreach($checkList as $ch) {	
			mysql_query("delete from admin_access where userID='$ch'");
		}
	  }
	}

?>
<form name="_form" id="_form" action="" method="post">
<div class=form_layout>
	<div class="module_title"><img src='images/user.png'><?=$transac->getMname($view);?></div>
   <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;">
    <table cellspacing="2" cellpadding="5" width="100%" align="center" class="search_table">
       <tr>
	 <td>
		<ul style="padding:5px;">
		<li><b>BACKUP FILES FOR THE MONTH OF <?php echo strtoupper(date("F, Y")); ?></b></li>
		<li><b>FILES LISTED ARE ACCUMULATED DATABASE CONTENTS.</b></li>
		</ul>
	 </td>
       </tr>
       </tr>
		<td>
    	<?php
		$dir="/var/www/backup_auto"; // Directory where files are stored

		if ($dir_list = opendir($dir)) {
		      $i = 0;
			while(($filename = readdir($dir_list)) != false) {
				
			      if(strpos($filename, "builders") == true) {			       
			      
			      $created_month = date("m", filemtime("../backup_auto/".$filename));

			      if($created_month!=date("m")) continue;	

			      $file_saved[$i] = $filename;

			      $i++;

			      if($i>50) break;
			      }			     
			}

		       rsort($file_saved);	

		       $i = 1;
		       foreach($file_saved as $fs) {
				$created_date = date("F d Y", filemtime("../backup_auto/".$fs));

				?>
			 	<div style="text-align:left;font-size:12px;padding:5px;border-bottom:1px #C0C0C0 dashed;">
					<span style="margin-right:30px;">
					<?php echo str_pad($i, 2, "0", STR_PAD_LEFT).'.'; ?>
					</span>
			
					<?php echo $fs; ?>

					<?php echo "<span style='margin-left:50px;'>".$created_date."</span>"; ?>

					<a href="<?php echo '../backup_auto/'.$fs; ?>" style="margin-left:50px;"><img src=images/icon_download.gif title="Download File" /></a>
			        </div>
				<?php
				
				$i++;
		       }	

			closedir($dir_list);
		}
        ?>
		</td>
	</tr>
    </table>
    </div>
</div>
</form>