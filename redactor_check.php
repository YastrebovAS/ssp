<?php
include('special/config_final.php');
$getter = $connection->prepare('SELECT  redactor.username, COUNT(version_redactor.id_ver) AS ct
FROM redactor
LEFT JOIN version_redactor
ON redactor.id = version_redactor.id_red
GROUP BY redactor.id');
$getter->execute();
$res_array = $getter->fetchALL(PDO::FETCH_ASSOC);
for($i = 0; $i< count($res_array); $i++){
    echo('<h3>'.$res_array[$i]['username'].': '.$res_array[$i]['ct'].' заявок </h3>');
}
?>