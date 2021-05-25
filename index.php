<?php 

// template used
// https://webdamn.com/create-simple-rest-api-with-php-mysql/

// require 'vendor/autoload.php';
// $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
// $dotenv->load();

// echo $_ENV["HOST"];
// echo getenv('HOST')."-";
// $ip = getenv('HOST', true) ?: getenv('HOST');
// echo $ip;
// echo $_SERVER['HOST'];
// var_dump(getenv('host'));

// mysql connection
require_once 'config_mysql.php';

// display api
// forecast, unix - unix, now


// init array
$data = [];

// general information
$general = array('general' => array('description' => 'hello World', 'source' => 'The API consists of data from Wuppertaler Stadtwerke. If you would like to add you data please contact hallo@arrenberg.studio.', 'maintained' => 'Studio Arrenberg maintains this API for the usage in the Wuppertal Region.'));

// query now
$now = date('Y-m-d H').":00";
// $now = '2019-05-29 17:00';

$query_now = "SELECT ampel_status.color as color, FLOOR( RAND() * (( ampel_status.carbon_factor + 10) - (ampel_status.carbon_factor - 10)) + (ampel_status.carbon_factor - 10)) as gramm  FROM `Ampel` 
join ampel_status on Ampel.status = ampel_status.id
WHERE `timestamp` = '$now'
Limit 0,1";

$result = mysqli_fetch_array(mysqli_query($mysqli, $query_now));
$current = array('color' => $result['color'], 'emissions' => array('amount' => $result['gramm'], 'unit' => 'g C02 / KWh'), 'lable' => array('singular' =>  $result['color'], 'plural' =>  $result['color']));
  
// query forcast
$query_forecast = "SELECT
ampel_status.color,
ampel_status.name,
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

  $forecast[$row['DATE']] = array('color' => $row['color'], 'lable' => $row['name'], 'time' => $row['time'], 'timestamp' => $row['DATE'] );

}

// history later
// $query_history = "SELECT ampel_status.color, ampel_status.name, DATE_FORMAT(Ampel.timestamp, '%H:%i') AS time, unix_timestamp(Ampel.timestamp) AS DATE FROM Ampel JOIN ampel_status ON Ampel.status = ampel_status.id WHERE `timestamp` order by Ampel.timestamp desc";

// $history = [];

// $timeline_history = mysqli_query($mysqli, $query_history) or die("could not perform query");
// while($row = mysqli_fetch_array($timeline_history)) {

//   $history[$row['DATE']] = array('color' => $row['color'], 'lable' => $row['name'], 'time' => $row['time'], 'timestamp' => $row['DATE'] );

// }

// create array
$data = array('general' => $general, 'current' => $current, 'forecast' => $forecast, 'history' => 'coming soon');

// display data as API in JSON format
header('Content-Type: application/json');
echo json_encode($data, true);
  


?>
