<?php
include('special/config_final.php');
$getter = $connection->prepare('SELECT  corrector.username, COUNT(version_corrector.id_ver) AS ct
FROM corrector
LEFT JOIN version_corrector 
ON corrector.id = version_corrector.id_cor
GROUP BY corrector.id');
$getter->execute();
$res_array = $getter->fetchALL(PDO::FETCH_ASSOC);
for($i = 0; $i< count($res_array); $i++){
    echo('<h3>'.$res_array[$i]['username'].': '.$res_array[$i]['ct'].' заявок </h3>');
}
?>