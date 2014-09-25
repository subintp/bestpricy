<?php

  # connect to the database
  class database {

    private $host = "localhost";
    private $dbname = "bestpricy";
    private $user = "rooti";
    private $pass = "root";

    public function connect () {
      try {
        $DBH = new PDO("mysql:host=$this->host;dbname=$this->dbname", $this->user, $this->pass);   
        $DBH->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        return $DBH;         
      }

      catch(PDOException $e) {
          echo "error message =".$e->getMessage();
          //log this error message
      }
    }
  }

  $DBH = new database();
  $status = $DBH->connect();
  if(isset($status)){
    echo "connected";
  }else {
    echo "disconnected";
  }
  
 /* function connect () {

    $host = "localhost";
    $dbname = "bestpricy";
    $user = "root";
    $pass = "root";

    try {

      $DBH = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);   
      $DBH->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
      return true;
       
    }

    catch(PDOException $e) {
        echo "error message =".$e->getMessage();
        #LOG ERROR MESSAGE
        return false;

    }
  }

  function disconnect() {

  }

  $status = connect();
  if ($status) {
    echo "database connected successfully";
  }else {
    echo "connection failed"; 
  }*/
 

  // $STH = $DBH->prepare('INSERT INTO `bestpricy`.catagory (name,url) VALUES (:name,:url)');
  // $STH->execute([:name => "subin",:url => "hello.com"]);

?>