<?php

	function update_map($count) {
		$objResponse = new xajaxResponse();
		
		$filename = "ref_date.txt";
		
		if(file_exists($filename)) {
			$handle = fopen($filename, "r");
			$ref_date = fread($handle, filesize($filename));
			fclose($handle);
		}
		
		$get_latest_Salert = mysql_query("select
												max(ref_date) as maxAdate
											from
												sensor_alerts");
												
		$r_latest_Salert = mysql_fetch_array($get_latest_Salert);
		
		//$objResponse->addAlert($r_latest_Salert[maxAdate]);
		
		if($r_latest_Salert[maxAdate]>$ref_date) {
			$objResponse->addScript("toggleBox('demodiv',1)");
			
			$get_stations = mysql_query("select
										station_name,
										station_code,
										status,
										latitude,
										longitude
									from
										stations
									order by
										id");
				
			$i=1;								
			while($r=mysql_fetch_array($get_stations)) {
				if($r[status]==1) {
					$color = 'green';
					$statP = 'Power Up';
				}
				else {
					$color = 'red';
					$statP = 'Power Down';
				}
				
				$markers .= " var point = new GLatLng($r[latitude],$r[longitude]);
							  var marker = createMarker(point,'<b>".$r[station_name].'</b> @<br>'.$r[latitude].', '.$r[longitude].'<br>Mobile # : '.$r[station_code].'<br>Status : '.$statP."','$color')
									  map.addOverlay(marker);";
									  
				$i++;
			}
			
	
			$filename = "ref_date.txt";
			
			if(file_exists($filename)) {
				$handle = fopen($filename, "w");			
				fwrite($handle, $r_latest_Salert[maxAdate]);
				fclose($handle);
			}
			
			$objResponse->addScript("if (GBrowserIsCompatible()) {     
									  function createMarker(point,html,flag) {
									    var greenIcon = new GIcon();
										greenIcon.image = \"images/flag_green.png\";
										greenIcon.shadow = \"shadow.png\";
										greenIcon.iconSize = new GSize(28, 28);
										greenIcon.shadowSize = new GSize(48, 28);
										greenIcon.iconAnchor = new GPoint(6, 20);
										greenIcon.infoWindowAnchor = new GPoint(5, 1);					    
										
										var redIcon = new GIcon();
										redIcon.image = \"images/flag_red.png\";
										redIcon.shadow = \"shadow.png\";
										redIcon.iconSize = new GSize(28, 28);
										redIcon.shadowSize = new GSize(48, 28);
										redIcon.iconAnchor = new GPoint(6, 20);
										redIcon.infoWindowAnchor = new GPoint(5, 1);
										
										if(flag=='green') 
											markerOptions = { icon:greenIcon };
										else
											markerOptions = { icon:redIcon };
									  
										var marker = new GMarker(point, markerOptions);
										GEvent.addListener(marker, \"click\", function() {
										  marker.openInfoWindowHtml(html);
										});
										return marker;
									  }
								 
									  var map = new GMap2(document.getElementById(\"map_div\"));
									  map.addControl(new GLargeMapControl());
									  map.addControl(new GMapTypeControl());
									  map.setCenter(new GLatLng(10.659469,122.966452),13);
									  
									  ".$markers."
									}
									
									else {
									  alert(\"Sorry, the Google Maps API is not compatible with this browser\");
									}");
			
			$objResponse->addScript("toggleBox('demodiv',0)");		
		}
		$new_count = $count+1;

		$asterisk = '<img src="images/9.gif">';

		
		if($new_count=='10') $new_count = 0;
		
		$objResponse->addAssign("count_","innerHTML", $asterisk.$new_count);
		$objResponse->addScript("xajax_map_info()");
		$objResponse->addScript("setTimeout(\"xajax_update_map($new_count)\",1000)");
			
		return $objResponse->getXML();
	}
	
	function map_info() {
		include_once("my_Classes/query.class.php");
		$objResponse = new xajaxResponse();
				
		$transac = new query();
		
		$getAreas = mysql_query("select status, station_name, station_code, latitude, longitude from stations");
					
		$row .= '<table width="94%" class=search_table>';
		
		while($rA=mysql_fetch_array($getAreas)) {
			$row .= '<tr bgcolor="'.$transac->row_color($i++).'">';
			
			$row .= '<td width="15">'.$i.'</td>';
			
			if($rA[status]==1) {
				$statP = '<font color="green">(Power Up)</font>';
			}
			else {
				$statP = '<font color="red">(Power Down)</font>';
			}
			
			$row .= '<td align="left"><b>'.$rA[station_name].'</b> '.$statP.'<br> Mobile # : '.$rA[station_code].'</td>';

			$row .= '<td width="16" title="Show on Map"><a href="javascript:void(0);" style="cursor:pointer;" onclick="xajax_ShowOnMap(\''.$rA[latitude].'\',\''.$rA[longitude].'\',\'http://google-maps-icons.googlecode.com/files/powersubstation.png\');toggleBox(\'demodiv\',1);"><img src="images/map.png" border="0"></a></td>';
			
			$row .= '</tr>';
		}
		
		$row .= '</table>';
					
		$objResponse->addAssign("map_info","innerHTML", $row);
			
		return $objResponse->getXML();
	}

?>