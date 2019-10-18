<?php
    include "mongodb.php";
    
    $user = $_POST['user'];
    $content = $_POST['content'];
    $doc = [
    'email' => $user,
    'content' => $content,
    'time' => new MongoDB\BSON\UTCDateTime(),
    ];
    
    $mng = new MongoDB\Driver\Manager("mongodb://mongo:27017/MyDB");
    
    $bulk = new MongoDB\Driver\BulkWrite();
    $bulk->insert($doc);
    $writeConcern = new MongoDB\Driver\writeConcern(MongoDB\Driver\WriteConcern::MAJORITY, 100);
    $result = $mng->executeBulkWrite('MyDB.Post', $bulk);
    echo "<meta http-equiv=\"refresh\" content=\"0;url=facebook.php\">";
?>
