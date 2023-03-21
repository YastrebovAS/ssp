<?php
include('special/config.php');
$getter = $connection->prepare('SELECT * from plan_num');
$getter->execute();
$res_array = $getter->fetchALL(PDO::FETCH_ASSOC);
for($i = 0; $i< count($res_array); $i++){
    echo('<h3>'.$res_array[$i]['name'].' Заполнена?(0-нет 1 - да) '.$res_array[$i]['fulled'].'</h3>');
}
?>