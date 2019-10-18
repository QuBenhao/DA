<?php
    include "mongodb.php";
    
    $user_from = $_GET['email'];
    $postid = new \MongoDB\BSON\ObjectId($_GET['postid']);
    $comment = $_POST['comment'];
    $time = new MongoDB\BSON\UTCDateTime();
    
    $doc=[
    'postid' => $postid,
    'user' => $user_from,
    'comment' => $comment,
    'time' => $time
    ];

    $manager = new MongoDB\Driver\Manager("mongodb://mongo:27017/MyDB");
    $options = [
        'projection' => ['_id' => 0],
        'sort' => [],
    ];
    $bulk = new MongoDB\Driver\BulkWrite();
    $bulk->insert($doc);

    $writeConcern = new MongoDB\Driver\writeConcern(MongoDB\Driver\WriteConcern::MAJORITY, 100);
    $result = $manager->executeBulkWrite('MyDB.Comments', $bulk);

    if($result)
        echo "<meta http-equiv=\"refresh\" content=\"0;url=facebook.php\">";
?>
