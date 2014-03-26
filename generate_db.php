<?php

//phpinfo();

date_default_timezone_set('UTC');
$file_db = new PDO('sqlite:log.sqlite3');
$file_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$file_db->exec("CREATE TABLE IF NOT EXISTS accesses (
                    id INTEGER PRIMARY KEY, 
                    sunetid TEXT, 
                    time TEXT)");

$insert = "INSERT INTO accesses (sunetid, time) 
                VALUES (:sunetid, datetime('now'))";
$stmt = $file_db->prepare($insert);
$stmt->bindParam(':sunetid', $sunetid);

$sunetid = "helloworld";

$stmt->execute();

$file_db = null;

?>