<?php
include('weather.php');
$weatherData = new WeatherData(62234, 'http://'.$_SERVER["HTTP_HOST"].'/'.basename(getcwd()).'/icon-sets/set2');
$weatherData = $weatherData->getWeather();

echo '<h1>Current Weather</h1>';
foreach($weatherData as $key => $value) {
	if(!is_array($value)) {
		echo $key . ': ' . $value . '<br>';
		if($key == 'image_weather' || $key == 'image_radar') {
			echo '<img src="'.$value.'" /><br>';
		}
	}
}

echo '<h1>Forecast Data</h1>';
foreach($weatherData['forecast'] as $key => $value) {
	if(is_array($value)) {
		foreach($value as $subKey2 => $subValue2) {
			echo $subKey2 . ': ' . $subValue2 . '<br>';
			if($subKey2 == 'image_weather') {
				echo '<img src="'.$subValue2.'" width="80" /><br>';
			}
		}
	}
	echo '<br>';
}

echo '<h1>Daycast Data</h1>';
foreach($weatherData['daycast'] as $key => $value) {
	if(is_array($value)) {
		echo '<div style="width: 130px;  float: left; text-align: center; padding: 4px; border: 1px solid white;">';
		foreach($value as $subKey2 => $subValue2) {
			if($subKey2 == 'image_weather') {
				echo '<img src="'.$subValue2.'" width="80"/>';
			}
			echo $subKey2 . ': ' . $subValue2 . '<br>';
		}
		echo '</div>';
	}
}


?>