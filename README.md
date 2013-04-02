Weather Data README

Get weather information from http://www.weather.unisys.com

Usage:


//INCLUDE CLASS

include($_SERVER["DOCUMENT_ROOT"] . '/weather/WeatherData.class.php');


//SET ZIP CODE AND IMAGE PATH (image path optional)

$weatherData = new WeatherData($zip, 'http://'.$_SERVER["HTTP_HOST"].'/weather/icon-sets/set2');


//GET WEATHER (returns an array)

$weatherData = $weatherData->getWeather();