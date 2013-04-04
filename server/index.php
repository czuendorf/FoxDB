<?php
/**
 * Step 1: Require the Slim Framework
 *
 * If you are not using Composer, you need to require the
 * Slim Framework and register its PSR-0 autoloader.
 *
 * If you are using Composer, you can skip this step.
 */
require 'Slim/Slim.php';
require 'DbConfig.php';

\Slim\Slim::registerAutoloader();

/**
 * Step 2: Instantiate a Slim application
 */
$app = new \Slim\Slim();

/**
 * Step 3: Define the Slim application routes
 */


/**
* Get Database connection.
*/
function getConnection() {
    $dbConfig = DbConfig::getInstance();
    $dbConfigParams = $dbConfig->getParams();

    $dbhost=$dbConfigParams["dbhost"];
    $dbuser=$dbConfigParams["dbuser"];
    $dbpass=$dbConfigParams["dbpass"];
    $dbname=$dbConfigParams["dbname"];

    $dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $dbh;
}


/**
 * Step 3: Define the Slim application routes
 */

function displayError($message) {
    echo '{"error":{"text":'. $message .'}}';
}

// POST route

/**
* Updates a json entity:
* post
* {"car": {
*            "id": "1",
*            "color": "blue"
*        }
* }
*/
$app->post('/', function () {
    $app = \Slim\Slim::getInstance();
    $data = json_decode($app->request()->getBody());
    
    $sql = "";
    $entityId = null;

    foreach ($data as $entity => $entitydata) {
        $sql = "UPDATE $entity SET ";

        foreach ($entitydata as $column => $value) {
            if($column != "id") {
                $sql = $sql . $column."='".$value."',";;
            } else {
                $entityId = $value;
            }
        }

        $sql = substr_replace($sql ,"",-1);
        $sql = $sql . " WHERE id = '".$entityId."' ";

    }

    try {
        $db = getConnection();
        $db->beginTransaction(); 
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $db->commit();
        $db = null;

        $rowCount = $stmt->rowCount();
        echo '{"count": ' . $rowCount . '}';
    } catch(PDOException $e) {
       displayError($e->getMessage());
    }
});

// GET route
$app->get('/get/:entity/id', function ($entity) {
    $db = getConnection();
    $sql = "SELECT MAX(id) as id FROM ".$entity;
    try {
        $db = getConnection();
        $stmt = $db->query($sql);
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        $db = null;

        echo "{\"".$entity."\": " . json_encode($result) . "}";
    } catch(PDOException $e) {
       displayError($e->getMessage());
    }
});

// GET route
$app->get('/get/:entity', function ($entity) {
    $db = getConnection();
    $sql = "SELECT * FROM ".$entity;
    try {
        $db = getConnection();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;

        echo "{\"".$entity."\": " . json_encode($result) . "}";
    } catch(PDOException $e) {
       displayError($e->getMessage());
    }
});

// PUT route
$app->put('/put', function () {
    $app = \Slim\Slim::getInstance();
  
    $data = json_decode($app->request()->getBody(),true);

    $entities = array_keys($data);

    foreach ($data as $entity => $entitydata) {
        $columns = "";
        $values = "";

        foreach ($entitydata as $column => $value) {
            $columns = $columns . $column.",";
            $values = $values ."'". $value."',";
        }

        $columns = substr_replace($columns ,"",-1);
        $values = substr_replace($values ,"",-1);

        $sql = "INSERT INTO $entity ($columns) VALUES ($values)";
    }

    try {
        $db = getConnection();
        $insertStmt = $db->prepare($sql);
        $db->beginTransaction(); 
        $insertStmt->execute();
        $id = $db->lastInsertId("id");
        $db->commit();

        echo '{"id": ' . $id . '}';
    } catch(PDOException $e) {
        $db->rollback(); 
        displayError($e->getMessage());
    }

});

// DELETE route
$app->delete('/delete', function () {
    $app = \Slim\Slim::getInstance();
  
    $data = json_decode($app->request()->getBody(),true);

    $entities = array_keys($data);

    foreach ($data as $entity => $entitydata) {
        $sql = "DELETE FROM $entity ";

        if(sizeof($entitydata) > 0) {
             $sql = $sql . "WHERE ";
        }

        $firstColumnKey = array_shift(array_keys($entitydata));
        $firstValue = $entitydata[$firstColumnKey];
        $sql = $sql . " ". $firstColumnKey. " = '". $firstValue. "'";

        array_shift($entitydata);

        foreach ($entitydata as $column => $value) {
            $sql = $sql . " AND ". $column. " = '". $value. "' ";
        }
    }

    try {
        $db = getConnection();
        $db->beginTransaction(); 
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $db->commit();
        $rowCount = $stmt->rowCount();
        echo '{"count": ' . $rowCount . '}';
    } catch(PDOException $e) {
        $db->rollback(); 
        displayError($e->getMessage());
    }
});

/**
 * Step 4: Run the Slim application
 *
 * This method should be called last. This executes the Slim application
 * and returns the HTTP response to the HTTP client.
 */
$app->run();
