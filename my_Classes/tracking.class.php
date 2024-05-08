<?php

	function tracking_updates() {
		$objResponse = new xajaxResponse();
		include_once("my_Classes/query.class.php");
				
		$options = new options();
		$transac = new query();
		
		$getR = mysql_query("select
								IMEI,
								plate_num,
								make,
								description
							from
								vehicles
							where
								plate_num like '%$plate_num%'
							limit 0,50");
		
		$row = '<table cellspacing="1" cellpadding="5" width="98%" align="center" class="search_table">';
		
		while($r=mysql_fetch_array($getR)) {
			$row .= '<tr bgcolor="'.$transac->row_color($i++).'">';
			
			$row .= '<td width="15">'.$i.'</td>';
			$row .= '<td width=250><b>'.$r[make].'</b><br>Plate # : '.$r[plate_num].'<br>IMEI : '.$r[IMEI].'</td>';
			
			$igps = $transac->getLatestIGPS($r[IMEI]);
			
			$row .= '<td width="100">'.$igps[LATITUDE].'<br>'.$igps[LONGITUDE].'</td>';
			$row .= '<td width="150">'.$igps[VAL].'<br>Event ID : '.$igps[EVENTID].'<br> Speed : '.$igps[SPEED].'</td>';
			$row .= '<td>'.$options->convert_sysdate($igps[DATETIME]).'</td>';
			$row .= '<td width="20" title="Show on Map"><a href="javascript:void(0);" style="cursor:pointer;" onclick="xajax_ShowOnMap(\''.$igps[LATITUDE].'\',\''.$igps[LONGITUDE].'\',\'http://google-maps-icons.googlecode.com/files/truck.png\');toggleBox(\'demodiv\',1);"><img src="images/map.png" border="0"></a></td>';
						
			$row .= '</tr>';
		}
		
		$row .= '</table>';
		
		$objResponse->addAssign("tracking_div","innerHTML", $row);
		$objResponse->addScript("setTimeout(\"xajax_tracking_updates()\",1000)");
			
		return $objResponse->getXML();
	}
	
	function ShowOnMap($lat, $long, $icon) {
		$objResponse = new xajaxResponse();
		
		$newContent = '<img src="http://maps.google.com/maps/api/staticmap?center='.$lat.','.$long.'&zoom=15&size=500x500
					&markers=icon:'.$icon.'|'.$lat.','.$long.'&sensor=false">';
					
		$objResponse->addAssign("Rdiv","innerHTML", $newContent);						
		$objResponse->addScript("setTimeout(\"showBox();toggleBox('demodiv',0);\",1500)");
		
		return $objResponse->getXML();
	}
	
	function ShowResultsOnMap($IMEI, $Ndate) {
		include_once("my_Classes/query.class.php");
			
		$transac = new query();
		$objResponse = new xajaxResponse();
		
		$query = mysql_query("select
										IMEI,
										plate_num,
										make,
										description
									from
										vehicles
									where
										IMEI='$plate_num'");
							
		$r = mysql_fetch_array($query);
		
		$IGPS = $transac->getIGPS($IMEI, $Ndate);
		
		if(empty($IGPS)) {
			$objResponse->addAlert("Nothing to show on map!");
			
			$objResponse->addScript("toggleBox('demodiv',0)");
			return $objResponse->getXML();
		}
		
		$path = "&path=color:red|weight:5";
		
		$i = 0;
		foreach($IGPS as $igps) {
			$markers .= '&markers=size:small|color:orange|label:'.$i.'|'.$igps[LATITUDE].','.$igps[LONGITUDE];
			$path .= '|'.$igps[LATITUDE].','.$igps[LONGITUDE];
			
			$latTotal += $igps[LATITUDE];
			$longTotal += $igps[LONGITUDE];
			
			$i++;
		}
		
		$latCenter = $latTotal/$i;
		$longCenter = $longTotal/$i;
			
		$newContent = '<img src="http://maps.google.com/maps/api/staticmap?center='.$latTotal.','.$longTotal.'&zoom=15&size=640x640'.$path.'&sensor=true">';		
					
		$objResponse->addAssign("Rdiv","innerHTML", $newContent);						
		$objResponse->addScript("setTimeout(\"showBox();toggleBox('demodiv',0);\",1500)");
		
		return $objResponse->getXML();
	}

?>