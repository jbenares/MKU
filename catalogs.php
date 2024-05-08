<?php
	
	$b = $_REQUEST['b'];
	$checkList = $_REQUEST['checkList'];
	$xls = $_FILES['xls'];
	if($b=='Delete Selected') {
	  if(!empty($checkList)) {
		foreach($checkList as $ch) {	
			mysql_query("delete from admin_access where userID='$ch'");
		}
	  }
	}else if($b=="Load File"){
		//$upload = new upload();
		$filename =  $upload->upload_img($xls[size], $xls[type], $xls[tmp_name], $xls[name], "catalogs", "");
		//echo $filename;
	}

?>
<form name="_form" id="_form" action="" method="post" enctype="multipart/form-data">
<div class=form_layout>
	<div class="module_title"><img src='images/user.png'><?=$transac->getMname($view);?></div>
   <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;">
    <table cellspacing="2" cellpadding="5" width="100%" align="center" class="search_table">
       <tr>
		 <td>
			<ul style="padding:5px;">
			<!--<li><b>BACKUP FILES FOR THE MONTH OF <?php echo strtoupper(date("F, Y")); ?></b></li>
			<li><b>FILES LISTED ARE ACCUMULATED DATABASE CONTENTS.</b></li>-->
			</ul>
		 </td>
       </tr>
	   <tr>
			<?php
				if(($registered_access!=24) || ($registered_access!=10) || ($registered_access!=7)){
					?>
						<td colspan=4>
							 <input type="file" name="xls" class="textbox" />
							 <input type="submit" name="b" value="Load File" class="buttons" onclick="return approve_confirm();" />
						</td>
					<?php
				}
			?>
			
	   </tr>
	   <tr>
			<td width="5%" style="text-align:right;"><b>Action</b></td>
			<td width="2%"></td>
			<td width="50%"><b>Filename</b></td>
			<td width="30%"><b>Date Added</b></td>
	   </tr>
    	<?php
		$dir="My_Uploads/catalogs/"; // Directory where files are stored
		//echo $dir;
			
		if ($dir_list = opendir($dir)){
			//echo opendir($dir);
			
		    $i = 0;
			while(($filename = readdir($dir_list)) != false) {
				//echo "";
			      if(strpos($filename, ".pdf") == true) {			       
					$created_month = date("m", filemtime("My_Uploads/catalogs/".$filename));
					
					//if($created_month!=date("m")) continue;	
  
					$file_saved[$i] = $filename;
  
					$i++;
  
					//if($i>50) break;
				  }			     
			}

		       rsort($file_saved);	

		       $i = 1;
		       foreach($file_saved as $fs) {
				$created_date = date("F d Y", filemtime("C:/xampp/htdocs/dbcci/My_Uploads/catalogs/".$fs));
				?>
					 <tr>
						
							<!--<div style="text-align:left;font-size:12px;padding:5px;border-bottom:1px #C0C0C0 dashed;">-->
								<td  width="5%">
									<a target="_blank" href="<?php echo '/dbcci/My_Uploads/catalogs/'.$fs; ?>" style="margin-left:50px;"><img src=images/action_print.gif title="Print File" /></a>
								</td>
								<td  width="2%">
									<span style="margin-right:30px;">
									<?php echo str_pad($i, 2, "0", STR_PAD_LEFT).'.'; ?>
									</span>
								</td>
								<td  width="50%">
								<?php echo $fs; ?>
								</td>
								<td  width="30%">
								<?php echo "<span style='margin-left:50px;'>".$created_date."</span>"; ?>
								</td>
							<!--</div>-->
					</tr>
				<?php
				
				$i++;
		       }	

			closedir($dir_list);
		}
        ?>
		
    </table>
    </div>
</div>
</form>