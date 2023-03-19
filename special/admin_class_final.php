<?php
include('download_final.php');
class Admin{
    private $con, $name;
    function __construct($connection,$name) {
        $this->con = $connection;
        $this->name = $name;
        echo("<h1>Добрый день</h1>");
        echo("<h3>Вы зашли как распределитель</h3>");
        
    }
    function if_it_is_zero_version($name){
        $getter = $this->con->prepare("SELECT max(version_number) as ver from version where name =:t GROUP BY version_number");
        $getter->bindParam('t',$name, PDO::PARAM_STR);
        $getter->execute();
        $res_array = $getter->fetchALL(PDO::FETCH_ASSOC);
        if(count($res_array)>0){
            //print_r($res_array);
            if($res_array[0]['ver']==0){
                return 1;
            }
            else{
                return 0;
            }
        }
        else{
            return 0;
        };
        
    }
    function if_there_is_a_redactor($name){
        $getter = $this->con->prepare("SELECT id from redactor where username =:t");
        $getter->bindParam('t',$name, PDO::PARAM_STR);
        $getter->execute();
        $res_array = $getter->fetchALL(PDO::FETCH_ASSOC);
        if(count($res_array)>0){
            return 1;
        }
        else{
            return 0;
        }
    }
    function show_all_zero_version_of_article(){
        $getter = $this->con->prepare("SELECT name FROM version WHERE version_number=0");
        $getter->execute();
        $res_array = $getter->fetchALL(PDO::FETCH_ASSOC);
        for($i =0;$i<count($res_array);$i++){
            echo('<h3>'.$res_array[$i]['name'].'</h3>');
            echo('<p><a href="download_final.php?path=versions/'.$res_array[$i]['name'].'00">Описание</a></p>');
            echo('<p><a href="download_final.php?path=request/'.$res_array[$i]['name'].'00">Заявка</a></p>');

        }
    }
    function get_max_id_by_name($name){
        $getter = $this->con->prepare('SELECT MAX(id) as id FROM version WHERE name =:t');
        $getter->bindValue('t',$name,PDO::PARAM_STR);
        $result = $getter->execute();
        $res_array = $getter->fetchALL(PDO::FETCH_ASSOC);
        if(count($res_array)==1){
            return $res_array[0]['id'];
        }
        else{
            return -1;
        }
    }
    function get_redactor_id($name){
        $getter = $this->con->prepare('SELECT id, username FROM redactor where username=:f');
        $getter->bindParam('f',$name,PDO::PARAM_STR);
        $res = $getter->execute();
        $res_array = $getter->fetchALL(PDO::FETCH_ASSOC);
        if(count($res_array)==1){
            return $res_array[0]['id'];
        }
        else{
            return -1;
        }
    }
    function version_updater($ver_id,$name){
        $updater = $this->con->prepare('UPDATE version SET stat = 2, version_number=1 WHERE id=:t');
        $updater->bindParam('t',$ver_id,PDO::PARAM_INT);
        $updater->execute();
        $version = 1;
        $tname = $_FILES["fileupload"]["tmp_name"];
        $pnam = $name.$version;
        move_uploaded_file($tname,'D:\Myssp\versions'.'/'.$pnam.$version);
    }
    function ver_redactor_inserter($ver_id, $redactor_id){
        $deleter = $this->con->prepare("DELETE from version_redactor WHERE id_ver = :t");
        $deleter->bindParam('t',$ver_id,PDO::PARAM_INT);
        $deleter->execute();
        $inserter = $this->con->prepare('INSERT INTO version_redactor(id_ver, id_red) VALUES (:t,:f)');
        $inserter->bindParam('t',$ver_id,PDO::PARAM_INT);
        $inserter->bindParam('f',$redactor_id,PDO::PARAM_INT);
        $inserter->execute();
    }
    function send_zero_version_to_redactor(){
        if(isset($_POST["submit1"]) and isset($_POST["name"])){
            $this->con->exec('LOCK TABLES version WRITE, version_redactor WRITE, redactor WRITE');
            if($this->if_it_is_zero_version($_POST["name"])==1 and $this->if_there_is_a_redactor($_POST["redactor"])==1){
                $ver_id = $this->get_max_id_by_name($_POST["name"]);
                $red_id = $this->get_redactor_id($_POST["redactor"]);
                if($ver_id==0 or $red_id == 0){
                    echo('<h3>Нет такой статьи или редактора</h3>');
                }
                $this->version_updater($ver_id,$_POST["name"]);
                $this->ver_redactor_inserter($ver_id, $red_id);
            }
            $this->con->exec('UNLOCK TABLES');
            echo('<h3>Отправлено</h3>');
        }

    }
    function if_there_are_free(){
        $getter = $this->con->prepare('SELECT * from version where stat =10');
        $getter->execute();
        $res_array = $getter->fetchALL(PDO::FETCH_ASSOC);
        $getter = $this->con->prepare('SELECT * from plan_num where fulled = 0');
        $getter->execute();
        $res_array2 = $getter->fetchALL(PDO::FETCH_ASSOC);
        for($i=0;$i<$res_array2[0]['number_of_articles'];$i++){
            if(count($res_array)>$i){
                $inserter = $this->con->prepare('INSERT into fills(id_ver, id_plan) VALUES(:t,:f)');
                $inserter->bindParam('t',$res_array[$i]['id'],PDO::PARAM_INT);
                $inserter->bindParam('f',$res_array2[$i]['id'],PDO::PARAM_INT);
                $inserter->execute();
                $updater = $this->con->prepare('UPDATE version SET stat=11 WHERE id=:t');
                $updater->bindValue('t',$res_array[$i]['id'],PDO::PARAM_INT);
                $updater->execute();
            }
        }

    }
    function if_you_can_create_number(){
        $getter = $this->con->prepare('SELECT min(fulled) as f from plan_num');
        $getter->execute();
        $res_array = $getter->fetchALL(PDO::FETCH_ASSOC);
        $getter = $this->con->prepare('SELECT count(id) as c from plan_num');
        $getter->execute();
        $res_array2 = $getter->fetchALL(PDO::FETCH_ASSOC);
        if($res_array2[0]['c']==0){
            return 1;
        }
        if($res_array[0]['f']==0){
            return 0;
        }
        return 1;
    }
    function create_new_number(){
        if(isset($_POST["submit2"]) and $_POST["number"]>0){
            $this->con->exec('LOCK TABLES plan_num WRITE, version WRITE, fills WRITE');
            //echo('i am here');
            if($this->if_you_can_create_number()==0){
                echo('<h3>создавать новый номер нет нужды<h3>');
                return 0;
            }
            $inserter = $this->con->prepare("INSERT INTO plan_num(name, number_of_articles) VALUES(:t,:f)");
            $inserter->bindParam('t',$_POST["name"],PDO::PARAM_STR);
            $inserter->bindParam('f',$_POST["number"],PDO::PARAM_INT);
            $inserter->execute();
            $this->if_there_are_free();
            $this->con->exec('UNLOCK TABLES');

        }

    }
    function logout_from_session(){
        if(isset($_POST['submit_exit']) or !isset($_COOKIE['admin'])){
            //print_r($_COOKIE);
            //sleep(50);
            unset($_SESSION['session_username']);
            unset($_FILES["fileupload"]);
            unset($_COOKIE['admin']);
            setcookie('admin', $this->name, time() - 1);
            header('Location: ../startpage_final.php');
        }

    }
    function show_numbers_and_articles_in_them(){
        $this->con->exec('LOCK TABLES fills WRITE, plan_num WRITE, version WRITE');
        $getter = $this->con->prepare('SELECT plan_num.name as n1, version.name as n2 from plan_num INNER JOIN fills ON plan_num.id = fills.id_plan INNER JOIN version ON fills.id_ver=version.id');
        $res = $getter->execute();
        $res_array = $getter->fetchALL(PDO::FETCH_ASSOC);
        echo('<table>');
        echo('<tr><th>Название номера</th><th>Название статьи</th><tr>');
        for($i=0;$i<count($res_array);$i++){
            echo('<tr><td>'.$res_array[$i]['n1'].'</td><td>'.$res_array[$i]['n2'].'</td></tr>');
            
        }
        echo('</table>');
        $this->con->exec('UNLOCK TABLES');
    }
    function show_meta(){
        $getter= $this->con->prepare('SELECT * FROM allinformation');
        $getter->execute();
        $res_array = $getter->fetchALL(PDO::FETCH_ASSOC);
        echo('<table>');
        echo('<tr><th>Статьи в процессе</th><th>Статьи, законченные</th><th>существующие планы</th><tr>');
        for($i=0;$i<count($res_array);$i++){
            echo('<tr><td>'.$res_array[$i]['active_articles'].'</td><td>'.$res_array[$i]['written_articles'].'</td><td>'.$res_array[$i]['plans'].'</td></tr>');
            
        }
        echo('</table>');

    }
}
?>