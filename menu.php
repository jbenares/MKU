<span class="preload1"></span>
<span class="preload2"></span>

<ul id="nav">
    <li class="top"><a href="admin.php?view=home" class="top_link"><span><img src="images/house.png" border=0 /> Home</span></a></li>
    
	<?php
		if($registered_access==1) {
			$getLevel1 = mysql_query("select M_id, Mname, icon_filename from menu where level='1' order by placement");
		}
		else {
       		$getLevel1 = mysql_query("select M_id, Mname, icon_filename from menu where level='1' and enable='1' order by placement");
		}
		
		while($r=mysql_fetch_array($getLevel1)) {	
			
			$checkif_children_exists = mysql_query("select
														m.Mname, 
														p.view_keyword
													from
														menu as m,
														programs as p,
														my_privileges as pp
													where
														m.level='2' and
														m.parent='$r[M_id]' and													
														m.enable='1' and
														pp.access_type_ID='$registered_access' and
														pp.PCode=p.PCode and
														m.PCode=p.PCode
													order by
														m.placement");
														
			$get_All_level2Parent = mysql_query("select M_id, Mname from menu where PCode='0' and level='2' and parent='$r[M_id]'");
			
			while($r_All_level2Parent=mysql_fetch_array($get_All_level2Parent)) {
				//print_r($r_All_level2Parent);
				//echo '<br>';
				$check_level3Children = mysql_query("select
														m.Mname, 
														p.view_keyword
													from
														menu as m,
														programs as p,
														my_privileges as pp
													where
														m.parent='$r_All_level2Parent[M_id]' and
														m.PCode!='0' and
														m.PCode=p.PCode and
														pp.PCode=p.PCode and
														pp.access_type_ID='$registered_access'");
													
				$level3_children = false;	
				if(mysql_num_rows($check_level3Children)>0) {					
					$level3_children = true;
					break;
				}
			}
												
												
			if(mysql_num_rows($checkif_children_exists)>0 || $registered_access==1 || $level3_children)					
				echo '<li class="top"><a href="#" class="top_link"><span class="down"><img src="images/'.$r[icon_filename].'" border=0 /> '.$r[Mname].'</a></span>';
			else continue;
			
			if($registered_access==1) {
				$getLevel2 = mysql_query("select
											M_id,
											Mname,
											PCode
										from
											menu
										where
											level='2' and
											parent='$r[M_id]'
										order by
											placement");
			}
			else {
				$getLevel2 = mysql_query("select
											M_id,
											Mname,
											PCode
										from
											menu
										where
											level='2' and
											parent='$r[M_id]' and
											enable='1'
										order by
											placement");
			}
			
			if(mysql_num_rows($getLevel2)>0) {		
						
				//echo mysql_num_rows($getLevel2);	
						
				echo '<ul class="sub">';
				
				while($r2=mysql_fetch_array($getLevel2)) {
					if($registered_access==1) {
						$getLevel3 = mysql_query("select
													m.Mname, 
													p.view_keyword
												from
													menu as m,
													programs as p
												where
													m.level='3' and
													m.parent='$r2[M_id]' and
													m.PCode=p.PCode
												order by
													m.placement");
					}
					else {
						$getLevel3 = mysql_query("select
													m.Mname, 
													p.view_keyword
												from
													menu as m,
													programs as p,
													my_privileges as pp
												where
													m.level='3' and
													m.parent='$r2[M_id]' and													
													m.enable='1' and
													pp.access_type_ID='$registered_access' and
													pp.PCode=p.PCode and
													m.PCode=p.PCode
												order by
													m.placement");
					}
													
					if(mysql_num_rows($getLevel3)>0) {	
						echo '<li><a href="#" class="fly">'.$r2[Mname].'</a>';
							echo '<ul>';
												
							while($r3=mysql_fetch_array($getLevel3)) {
								echo '<li><a href="admin.php?view='.$r3[view_keyword].'">'.$r3[Mname].'</a></li>';					
							}
						
							echo '</ul>';
						echo '</li>';
					}
					else {
						if($registered_access==1) {
							$getviewKey = mysql_query("select view_keyword from programs where PCode='$r2[PCode]'");
						}
						else {
							$getviewKey = mysql_query("select
														p.view_keyword
													from
														programs as p,
														my_privileges as pp
													where
														p.PCode='$r2[PCode]' and
														pp.access_type_ID='$registered_access' and
														pp.PCode=p.PCode");
						}
						
						if(mysql_num_rows($getviewKey)>0) {							
							$rKey = mysql_fetch_array($getviewKey);						
							echo '<li><a href="admin.php?view='.$rKey[view_keyword].'">'.$r2[Mname].'</a></li>';
						}
					}
				}
				
				echo '</ul>';
			}
			
			echo '</li>';
		}
    ?>
    
<!--    <li class="top"><a href="logout.php" class="top_link"><span><img src="images/key_go.png" border=0 /> Logout</span></a></li>	 -->
</ul>