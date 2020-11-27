<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require __DIR__ . '/../vendor/autoload.php';
class MyDB extends SQLite3 {
    function __construct() {
       $this->open('../participants.db');
    }

 }

$app = new \Slim\App;

$app->get(
  '/hello/{name}', 
  function (Request $request, Response $response, array $args) {
  	$db = new MyDB();
 	if(!$db) {
 		echo $db->lastErrorMsg();
    	exit();
 	}
 	$sql = "SELECT id, firstname, lastname FROM participant";
	$ret = $db->query($sql);
	while($row = $ret->fetchArray(SQLITE3_ASSOC) ) {
    	echo "id = ". $row['id'] . ", ";
   		echo "firstname = ". $row['firstname'] . ", ";
   	 echo "lastname = ". $row['lastname'] ."<br>";
	}
	$db->close();
  }
);


/*$app->get(
    '/api/participants',
    function (Request $request, Response $response, array $args) {
        $participants = [
           ['id' => 1, 'firstname' => 'John', 'lastname' => 'Doe'],
           ['id' => 2, 'firstname' => 'Kate', 'lastname' => 'Pig'],
           ['id' => 3, 'firstname' => 'Chris', 'lastname' => 'Lua'],
           ['id' => 300, 'firstname' => 'Natalia', 'lastname' => 'Lua'],
        ];
        return $response->withJson($participants);
    }
);*/

$app->get(
	'/api/participants',
    function (Request $request, Response $response, array $args) {
 		$db = new MyDB();
 		if(!$db) {
 			echo $db->lastErrorMsg();
    		exit();
 		}
 		$participants = [];
 		$sql = "SELECT id, firstname, lastname FROM participant";
		$ret = $db->query($sql);
	
		while($row = $ret->fetchArray(SQLITE3_ASSOC) ) {
    		$participants[]=$row;
		}
		$db->close();
		return $response->withJson($participants);
    }
);

$app->run();