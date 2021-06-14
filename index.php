<?php 

// template used
// https://webdamn.com/create-simple-rest-api-with-php-mysql/

// mysql connection
require_once 'config_mysql.php';

// mysql charset
mysqli_set_charset($mysqli, "utf8");

// init array
$data = [];

// general information
$general = array('description' => 'The Energie Wetter is displaying the C02 production for current supplied energy in Wuppertal.', 'source' => 'The API consists of data from Wuppertaler Stadtwerke. If you would like to add you data please contact hallo@arrenberg.studio.', 'maintained' => 'Studio Arrenberg maintains this API for the usage in the Wuppertal Region.', 'location' => 'Wuppertal, Germany', 'timezone' => 'GMT+2', 'Postcodes' => array(42103, 42105, 42107, 42109, 42111, 42113, 42115, 42117, 42119, 42275, 42277, 42279, 42281, 42283, 42285, 42287, 42289, 42327, 42329, 42349, 42369, 42389, 42399));

// query now
// $now = date('Y-m-d H').":00";
$now = date('Y-m-d H', time() + (60*60*1)).":00"; // adjusted time (why is it behind one hour from gmt?)
// echo $now;
// $now = '2019-05-29 17:00';

$query_now = "SELECT ampel_status.name as name, ampel_status.name_plural as name_plural, ampel_status.color as color, FLOOR( RAND() * (( ampel_status.carbon_factor + 10) - (ampel_status.carbon_factor - 10)) + (ampel_status.carbon_factor - 10)) as gramm  FROM `Ampel` 
  join ampel_status on Ampel.status = ampel_status.id
  WHERE `timestamp` = '$now'
  Limit 0,1";

$result = mysqli_fetch_array(mysqli_query($mysqli, $query_now));
$current = array('color' => $result['color'], 'emissions' => array('amount' => $result['gramm'], 'unit' => 'g C02 / KWh'), 'label' => array('singular' =>  $result['name'], 'plural' =>  $result['name_plural']), 'time' => 'Jetzt');
  

$now = date('Y-m-d H', time() + (60*60*3)).":00";
// query forcast
$query_forecast = "SELECT
  ampel_status.color,
  ampel_status.name as name,
  ampel_status.name_plural as name_plural,
  DATE_FORMAT(Ampel.timestamp, '%H:%i') AS time,
  unix_timestamp(Ampel.timestamp) AS DATE
  FROM
  Ampel
  JOIN ampel_status ON Ampel.status = ampel_status.id
  WHERE
  `timestamp` BETWEEN '".$now."' AND(
      '".$now."' + INTERVAL 48 HOUR
  )
  order by Ampel.timestamp asc
  LIMIT 0, 60";

$forecast = [];

$timeline_r = mysqli_query($mysqli, $query_forecast) or die("could not perform query");
while($row = mysqli_fetch_array($timeline_r)) {

  $forecast[$row['DATE']] = array('color' => $row['color'], 'label' => array('singular' =>  $result['name'], 'plural' =>  $result['name_plural']), 'time' => $row['time'] );

}

// history later
// $query_history = "SELECT ampel_status.color, ampel_status.name, DATE_FORMAT(Ampel.timestamp, '%H:%i') AS time, unix_timestamp(Ampel.timestamp) AS DATE FROM Ampel JOIN ampel_status ON Ampel.status = ampel_status.id WHERE `timestamp` order by Ampel.timestamp desc";

// $history = [];

// $timeline_history = mysqli_query($mysqli, $query_history) or die("could not perform query");
// while($row = mysqli_fetch_array($timeline_history)) {

//   $history[$row['DATE']] = array('color' => $row['color'], 'label' => $row['name'], 'time' => $row['time'], 'timestamp' => $row['DATE'] );

// }

// create array
$data = array('general' => $general, 'current' => $current, 'forecast' => $forecast, 'history' => 'coming soon');

// display data as API in JSON format
header('Content-Type: application/json');
echo json_encode($data, true);
