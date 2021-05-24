<?php 

// mysql connection


// read request (later)

// init REST
$api = new Rest();

$api->displayAPI();


// display api
// forecast, unix - unix, now
public function displayAPI() {
  
  // query now
  
  // query history (later)
  
  // query forcast
  
  // general information
  
  $data = array(0 => 'hello world');
  
  header('Content-Type: application/json');
	echo json_encode($data);
  
}


?>
