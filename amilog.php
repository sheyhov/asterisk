<?php
require_once '/var/lib/asterisk/agi-bin/phpagi-asmanager.php';
$manager_ip = "127.0.0.1";
$username = "******";
$secret = "*********";
$dbhost = "127.0.0.1";
$logfile = "/tmp/asterisk-manager.log";
$reconnect = TRUE;


$manager = new AGI_AsteriskManager();
$con = $manager->connect($manager_ip,$username,$secret);
if (!$con) {
    $date_receive = date("[Y-m-d H:i:s] ");
    echo($date_receive);
    echo "Cann't connect to manager\n";
    logger("Can't connect to manager!", $date_receive);
} else {
    $date_receive = date("[Y-m-d H:i:s] ");
    echo($date_receive);
    echo "Connected to manager\n";
    logger("Connected to manager!", $date_receive);
}

    $manager->add_event_handler('MusicOnHoldStart','dump_to_file'); // handle all MusicOnHoldStart events
    //$manager->add_event_handler('*','dump_to_file'); // handle all events

    $response = $manager->wait_response(TRUE);
    while (!$response) {
    if ($reconnect) {
    sleep("1");
    $con = $manager->connect($manager_ip,$username,$secret);
    while (!$con) {
        sleep("1");
        $con = $manager->connect($manager_ip,$username,$secret);
    }
    $response = $manager->wait_response(TRUE);
    } else {
    exit();
    }
 }

 function dump_to_file($ecode, $data, $server, $port) {      
    $date_receive = date("[Y-m-d H:i:s] ");
    echo($date_receive);
    echo(print_r($data,true));
    logger(print_r($data,true), $date_receive);
}    

function logger($message, $date_receive) {
    global $logfile;

    if (!empty($logfile)) {
        $handle = fopen($logfile,"a");
        fwrite($handle,"$date_receive\n$message");
        fwrite($handle,"\n");
        fclose($handle);
    }
}
?>
