<?php
    function findEmail($email){
        // within php use mongo:27017 as the mongo server:port, not
        // localhost:27020 that's for accessing from host computer
        $mng = new MongoDB\Driver\Manager("mongodb://mongo:27017/MyDB");
        
        $filter = ['email' => $email];
        $options = [
          //  'projection' => ['_id' => 0],
            'sort' => [],
        ];

        $query = new MongoDB\Driver\Query($filter, $options);
        $cursor = $mng->executeQuery('MyDB.Members', $query);

        foreach($cursor as $document){
            return $document;
        }
    }
    
    function findUsers($screenname){
        $mng = new MongoDB\Driver\Manager("mongodb://mongo:27017/MyDB");
        
        $filter = ['screenname' => $screenname];
        $options = [
          //  'projection' => ['_id' => 0],
            'sort' => [],
        ];

        $query = new MongoDB\Driver\Query($filter, $options);
        $cursor = $mng->executeQuery('MyDB.Members', $query);
        return $cursor;
    }
    
    function register($doc){
        $manager = new MongoDB\Driver\Manager("mongodb://mongo:27017/MyDB");

        $bulk = new MongoDB\Driver\BulkWrite();
        $bulk->insert($doc);

        $writeConcern = new MongoDB\Driver\writeConcern(MongoDB\Driver\WriteConcern::MAJORITY, 100);
        $result = $manager->executeBulkWrite('MyDB.Members', $bulk);
        return $result;
    }
    
    function addFriend($user_from,$user_to){
        $mng = new MongoDB\Driver\Manager("mongodb://mongo:27017/MyDB");
        $filter = [];
        $options = [
             'projection' => ['_id' => 0],
             'sort' => [],
        ];
        $query = new MongoDB\Driver\Query($filter, $options);
        $rows = $mng->executeQuery('MyDB.FriendshipRequest', $query);
        foreach($rows as $row)
        {
            if(($row->requester_email==$user_from && $row->recepient_email==$user_to)||($row->requester_email==$user_to && $row->recepient_email==$user_from))
                return false;
        }
        $doc = [
        'requester_email' => $user_from,
        'recepient_email' => $user_to,
        'request_date' => new MongoDB\BSON\UTCDateTime(),
        'request_status' => "pending"
        ];
        
        $bulk = new MongoDB\Driver\BulkWrite();
        $bulk->insert($doc);
        
        $writeConcern = new MongoDB\Driver\writeConcern(MongoDB\Driver\WriteConcern::MAJORITY, 100);
        $result = $mng->executeBulkWrite('MyDB.FriendshipRequest', $bulk);
        return $result;
     }
    
    function getRequest($user_to){
        $mng = new MongoDB\Driver\Manager("mongodb://mongo:27017/MyDB");
        $filter = [];
        $options = [
            'projection' => ['_id' => 0],
            'sort' => [],
        ];
        $query = new MongoDB\Driver\Query($filter, $options);
        $rows = $mng->executeQuery('MyDB.FriendshipRequest', $query);
        
        $str = "";
        
        foreach($rows as $row)
        {
            if($row->recepient_email==$user_to && $row->request_status=="pending")
                $str .= $row->requester_email . "<button name=\"accept\" type=\"submit\" value=\"1\">accept</button>" . "<button name=\"reject\" type=\"submit\" value=\"2\">reject</button>" . "<br>";
        }
        
        return $str;
    }
        
?>
