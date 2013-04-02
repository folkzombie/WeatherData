Weather Data README

Get weather information from http://www.weather.unisys.com.

This class return current weather information, daycast information, and forecast information (forecast info includes a more detailed description). It also returns radar imagery and weather icons for the current, forecast and daycast info. 

There are 3 example icon sets provided. 

Usage:


//INCLUDE CLASS

include($_SERVER["DOCUMENT_ROOT"] . '/weather/WeatherData.class.php');


//SET ZIP CODE AND IMAGE PATH (image path optional)

$weatherData = new WeatherData($zip, 'http://'.$_SERVER["HTTP_HOST"].'/weather/icon-sets/set2');


//GET WEATHER (returns an array)

$weatherData = $weatherData->getWeather();