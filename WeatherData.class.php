<?php

class WeatherData {
	
	private $zip_code;
	private $image_path;
	
	public function __construct($zip_code, $icon_set='') {          
		$this->zip_code = $zip_code;
		$this->image_path = preg_replace('{/$}', '', $icon_set); 
		
		//we get the region so we can get the radar image from unisys
		$this->region_array = array(
			"mw" => array("WI","IA","IN","MI","MN","NE","SD","ND","KY"),
			"ne" => array("ME","VT","NH","RI","NJ","MD","NY","VA","DE","PA","MA"),
			"at" => array("TN","NC","SC","WV","OH"),
			"se" => array("FL","GA","AL","MS","LA"),
			"cp" => array("KS","MO","IL","AR","OK"),
			"sp" => array("TX","NM"),
			"nw" => array("WA","ID","MT","WY","OR"),
			"sw" => array("CA","NV","UT","AZ")
		);
		
	}
	
	///////////RETURN PATH TO IMAGE IF PROVIDED OTHER WISE JUST RETURN IMAGE NAME (name is "skies" value with ".png" added)
	public function weatherGraphic($skies, $night=0) {
		switch(strtolower($skies)) {
			case "sunny":
				$image = ($night == 1 ? $this->image_path.'/night-clear.png' : $this->image_path.'/sunny.png');
				break;
			case "clear":
				$image = ($night == 1 ? $this->image_path.'/night-clear.png' : $this->image_path.'/clear.png');
				break;
			case "mostly clear":
				$image = ($night == 1 ? $this->image_path.'/night-mostly-clear.png' : $this->image_path.'/mostly-clear.png');
				break;	
			case "overcast":
				$image = ($night == 1 ? $this->image_path.'/night-cloudy.png' : $this->image_path.'/cloudy.png');
				break;
			case "cloudy":
			case "mostly cloudy":
				$image = ($night == 1 ? $this->image_path.'/night-cloudy.png' : $this->image_path.'/cloudy.png');
				break;
			case "partly cloudy":
				$image = ($night == 1 ? $this->image_path.'/night-partly-cloudy.png' : $this->image_path.'/partly-cloudy.png');
				break;
			case "rain":
				$image = ($night == 1 ? $this->image_path.'/night-rain.png' : $this->image_path.'/rain.png');
				break;	
			case "thunderstorms":
				$image = ($night == 1 ? $this->image_path.'/night-thunderstorms.png' : $this->image_path.'/thunderstorms.png');
				break;
			case "snow":
				$image = ($night == 1 ? $this->image_path.'/night-snow.png' : $this->image_path.'/snow.png');
				break;
			case "fog":
 			case "obscured":
				$image = $this->image_path.'/obscured.png';
				break;	
				
		}
		return $image;
	}
	
	///////////CONVERT WEATHER "CODE" TO STRING
	public function code2string($code) {
		switch($code) {
			case "TS": $string = "Thunderstorms"; break;
			case "RA": $string = "Rain"; break;
			case "MC": $string = "Mostly Cloudy"; break;
			case "SU": $string = "Sunny"; break;
			case "MO": $string = "Mostly Clear"; break;
			case "PC": $string = "Partly Cloudy"; break;
			case "SN": $string = "Snow"; break;
			case "CL": $string = "Overcast"; break;
			case "FG": $string = "Fog"; break;
		}
		return $string;
	}
	
	///////////GET WEATHER VALUES
	public function getWeatherValues($xml) {
		
		$observation = $xml->{"observation"};
		$attributes = $observation->attributes();
		
		$weatherData = array();
		$weatherData['city'] = str_replace("_", " ", $attributes["city"]);
		$cityArray = explode(",", $weatherData['city']);
		$weatherData['state'] = $cityArray[1];
		$weatherData['latitude'] = $attributes["latitude"];
		$weatherData['longitude'] = $attributes["longitude"];
		$weatherData['observation_station'] = $attributes["observation_name"];
		$weatherData['night'] = $attributes["night"];
		$weatherData['time'] = $attributes["time"];
		$weatherData['timezone'] = $attributes["timezone"];
		$weatherData['temp_string'] = $attributes["temp.string"];
		$weatherData['temp_f'] = $attributes["temp.F"];
		$weatherData['temp_c'] = $attributes["temp.C"];
		$weatherData['dewpt_str'] = $attributes["dewpt.string"];
		$weatherData['dewpt_f'] = $attributes["dewpt.F"];
		$weatherData['dewpt_c'] = $attributes["dewpt.C"];
		$weatherData['rel_hum_string'] = $attributes["rel_hum.string"];
		$weatherData['rel_hum_percent'] = $attributes["rel_hum.percent"];
		$weatherData['wind_string'] = $attributes["wind.string"];
		$weatherData['wind_direct'] = $attributes["wind_direct"];
		$weatherData['wind_speed_knt'] = $attributes["wind_speed.knt"];
		$weatherData['wind_speed_mph'] = substr($weatherData['wind_speed_knt'] * 1.15077944802354, 0, 4);
		$weatherData['wind_chill_f'] = (!empty($attributes["wind_chill.F"]) ? $attributes["wind_chill.F"] : 'NA');
		$weatherData['pressure_string'] = $attributes["pressure.string"];
		$weatherData['pressure_in'] = $attributes["pressure.in"];
		$weatherData['altimeter_string'] = $attributes["altimeter.string"];
		$weatherData['altimeter_mb'] = $attributes["altimeter.mb"];
		$weatherData['altimeter_in'] = $attributes["altimeter.in"];
		$weatherData['heat_index'] = (!empty($attributes["heat_index.F"]) ? $attributes["heat_index.F"] : 'NA');
		$weatherData['skies'] = $attributes["skies"];
		$weatherData['text_weather'] = (!empty($attributes["text_weather"]) ? $attributes["text_weather"] : 'NA');
		$almanac = $xml->{"almanac"};
		$almanac_attributes = $almanac->attributes();
		$weatherData['sunrise'] = $almanac_attributes["sunrise"];
		$weatherData['sunset'] = $almanac_attributes["sunset"];
		$weatherData['image_weather'] = $this->weatherGraphic($weatherData['skies'], $weatherData['night']);
		
		//get "region"
		foreach($this->region_array as $key => $value) {
			foreach($value as $keySub => $valueSub) {
				if(trim($weatherData['state']) == trim($valueSub)) { $region = $key; break; }
			}
		}
		if(!isset($region)) { $region = 'us'; }
		
		$weatherData['image_radar'] = 'http://weather.unisys.com/radar/rad_'.$region.'_loop.gif';
		$weatherData['image_visible_satellite'] = 'http://www.weather.unisys.com/satellite/sat_vis_'.$region.'_loop.gif';
		$weatherData['image_infrared_satellite'] = 'http://www.weather.unisys.com/satellite/sat_ir_'.$region.'_loop.gif';
		$weatherData['image_enhanced_infrared_satellite'] = 'http://www.weather.unisys.com/satellite/sat_ir_enh_'.$region.'_loop.gif';
		$weatherData['image_surface_data'] = 'http://www.weather.unisys.com/surface/sfc_'.$region.'.gif';
		
		//get forcast information
		$fCount = 0;
		foreach($xml->{"forecast"} as $child) {
			$forecastAttributes = $child->attributes();
			$weatherData['forecast'][$fCount]['day'] = $forecastAttributes['day'];
			$weatherData['forecast'][$fCount]['high'] = $forecastAttributes['high_temp'];
			$weatherData['forecast'][$fCount]['low'] = $forecastAttributes['low_temp'];
			$weatherData['forecast'][$fCount]['description'] = $forecastAttributes['text'];
			$weatherData['forecast'][$fCount]['weather_code'] = $this->code2string($forecastAttributes['weather']);
			$weatherData['forecast'][$fCount]['image_weather'] = $this->weatherGraphic($this->code2string($forecastAttributes['weather']), 0);
			$fCount++;
		}
		
		//get daycast information
		$dCount = 0;
		foreach($xml->{"daycast"} as $child) {
			$daycastAttributes = $child->attributes();
			$weatherData['daycast'][$dCount]['day'] = $daycastAttributes['day'];
			$weatherData['daycast'][$dCount]['high'] = $daycastAttributes['high_temp'];
			$weatherData['daycast'][$dCount]['low'] = $daycastAttributes['low_temp'];
			$weatherData['daycast'][$dCount]['weather'] = $this->code2string($daycastAttributes['weather']);
			$weatherData['daycast'][$dCount]['image_weather'] = $this->weatherGraphic($this->code2string($daycastAttributes['weather']), 0);
			$dCount++;
		}
		
		return $weatherData;
		
	}
	
	///////////GET WEATHER FEED
	public function getWeather() { 
		$source = 'http://www.weather.unisys.com/forexml.cgi?'.$this->zip_code;
		$xml = new SimpleXMLElement($source, null, true);
		if($xml == 'Server Busy') {
			return 'Server Busy... Cannot Get Weather Data for ' . $this->zip_code;
		} else {
			return $this->getWeatherValues($xml);
		}
	}
	
}

?>