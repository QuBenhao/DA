<?php
    include "mongodb.php";
    
    $user_from = $_GET['from'];
    $user_to = $_GET['to'];
    
    $manager = new MongoDB\Driver\Manager("mongodb://mongo:27017/MyDB");
    $options = [
        'projection' => ['_id' => 0],
        'sort' => [],
    ];
    $bulk = new MongoDB\Driver\BulkWrite();
    $bulk->update(array('requester_email'=>$user_from,'recepient_email'=>$user_to),['$set' => ['request_status' => 'reject']]);
    
    $writeConcern = new MongoDB\Driver\writeConcern(MongoDB\Driver\WriteConcern::MAJORITY, 100);
    $result = $manager->executeBulkWrite('MyDB.FriendshipRequest', $bulk);
    
    if($result)
        echo "<meta http-equiv=\"refresh\" content=\"0;url=facebook.php\">";
?>
