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

$app->post(
	'/api/participants',
    function (Request $request, Response $response, array $args) {
 		$db = new MyDB();
 		if(!$db) {
 			echo $db->lastErrorMsg();
    		exit();
		 }
		 $requestData = $request->getParsedBody();
		if (!isset($requestData['firstname']) || !isset($requestData['lastname'])) {
			return $response->withStatus(418)->withJason(['message'=> 'Lastname and firstname are required']);
		 }else
		 $sql =  "INSERT INTO participant (firstname, lastname) VALUES('$requestData[firstname]', '$requestData[lastname]');";
		 $db->query($sql);
		return $response->withStatus(201);
    }
);
$app->get(
    '/api/participants/{id}',
    function (Request $request, Response $response, array $args) {
		$db = new MyDB();
		if(!$db) {
			echo $db->lastErrorMsg();
		   exit();
		}
        $sql = "SELECT * FROM participant WHERE id = $args[id]"; // beware! SQL Injection Attack
        $ret = $db->query($sql);
        $participant = $ret->fetchArray(SQLITE3_ASSOC);
        if ($participant) {
            return $response->withJson($participant);
        } else {
            return $response->withStatus(404)->withJson(['error' => 'Such participant does not exist.']);
        }
    }
);
/*$app->post(
	'/api/participants',
    function (Request $request, Response $response, array $args) {
 		$db = new MyDB();
 		if(!$db) {
 			echo $db->lastErrorMsg();
    		exit();
		 }
		 $requestData = $request->getParsedBody();
		 $sql =  "DELETE FROM participant WHERE id=2;";
		 $db->query($sql);
		return $response->withStatus(201);
    }
);*/

$app->run();