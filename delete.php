<?php
    include "mongodb.php";
    
    $user = $_GET['email'];
    $objectid = new \MongoDB\BSON\ObjectId($_GET['postid']);
    
    $mng = new MongoDB\Driver\Manager("mongodb://mongo:27017/MyDB");
    $filter = [];
    $options = [
        'projection' => ['_id' => 0],
        'sort' => [],
    ];
    $query = new MongoDB\Driver\Query($filter, $options);
    $cursor = $mng->executeQuery('MyDB.FriendshipRequest', $query);
    foreach($cursor as $cur){
        if($cur->em)
    }
    
    
    $filter = ['_id'=>$objectid];
    $bulk = new MongoDB\Driver\BulkWrite();
    $bulk->delete($filter);
    $writeConcern = new MongoDB\Driver\writeConcern(MongoDB\Driver\WriteConcern::MAJORITY, 100);

    if($likeresult)
    {
        $result = $mng->executeBulkWrite('MyDB.Post', $bulk);
        echo '<html><head><Script Language="JavaScript">alert("Delete successfully");</Script></head></html>' .
            "<meta http-equiv=\"refresh\" content=\"0;url=logpage.php\">";
        return;
    }

?>
