<?php
include('download_final.php');
class Author{
    private $con, $name;
    function __construct($connection,$name) {
        $this->con = $connection;
        $this->name = $name;
        
        
        
    }
    function get_author_by_name(){
        $getter_new1 = $this->con->prepare("SELECT id FROM author WHERE username=:name1");
        $getter_new1->bindParam("name1", $this->name, PDO::PARAM_STR);
        $result11 = $getter_new1->execute();
        $result11_ar = $getter_new1->fetchALL(PDO::FETCH_ASSOC);
        $author_id = $result11_ar[0]["id"];
        return $author_id;
    }
    function if_it_belongs($title){
        $author_id = $this->get_author_by_name();
        $checker = $this->con->prepare('SELECT version.id from version INNER JOIN send_recive ON send_recive.id_ver=version.id WHERE send_recive.id_a =:t AND version.name =:f');
        $checker->bindValue('t',$author_id,PDO::PARAM_INT);
        $checker->bindValue('f',$title,PDO::PARAM_STR);
        $checker->execute();
        $res_array = $checker->fetchALL(PDO::FETCH_ASSOC);
        if(count($res_array)>0){
            return 1;
        }
        else{
            return 0;
        }


    }

    function if_name_is_uniq($title){
        $checker = $this->con->prepare('SELECT id from version where name =:t');
        $checker->bindValue('t',$title,PDO::PARAM_STR);
        $checker->execute();
        $res_array = $checker->fetchALL(PDO::FETCH_ASSOC);
        if(count($res_array)>0){
            return 1;
        }
        else{
            return 0;
        }
    }

    function get_max_id_by_name($name){
        $getter = $this->con->prepare('SELECT MAX(id) as id FROM version INNER JOIN send_recive ON send_recive.id_ver = version.id WHERE name=:t AND send_recive.id_a =:f');
        $getter->bindValue('t',$name,PDO::PARAM_STR);
        $getter->bindValue('f',$this->get_author_by_name(),PDO::PARAM_STR);
        $result = $getter->execute();
        $res_array = $getter->fetchALL(PDO::FETCH_ASSOC);
        if(count($res_array)==1){
            return $res_array[0]['id'];
        }
        else{
            return -1;
        }
    }
    function upload_zero_version($title, $date){
        $version = 0;
        $tname1 = $_FILES["fileupload1"]["tmp_name"];
        $tname2 = $_FILES["fileupload2"]["tmp_name"];
        $pnam = $title.$version;
        move_uploaded_file($tname1,'D:\Myssp\versions'.'/'.$pnam.$version);
        move_uploaded_file($tname2,'D:\Myssp\request'.'/'.$pnam.$version);
        // Вставка 0 версии в таблицу со всеми версиями
        $inserter = $this->con->prepare("INSERT INTO version(name,stat,dat,version_number) VALUES (:name1, :stat,:dat, :version1)");
        $inserter->bindParam(':name1', $title,PDO::PARAM_STR);
        $inserter->bindValue(':stat', 1,PDO::PARAM_INT);
        $inserter->bindValue(':dat', $date,PDO::PARAM_STR);
        $inserter->bindValue(':version1', $version,PDO::PARAM_INT);
        $result = $inserter->execute();
        //Получение id этой нулевой версии
        $getter_new = $this->con->prepare("SELECT id FROM version WHERE name=:name1");
        $getter_new->bindParam("name1", $title, PDO::PARAM_STR);
        $result1 = $getter_new->execute();
        $result1_ar = $getter_new->fetchALL(PDO::FETCH_ASSOC);
        $version_id = $result1_ar[0]["id"];
        //Получение id автора
        $getter_new1 = $this->con->prepare("SELECT id FROM author WHERE username=:name1");
        $getter_new1->bindParam("name1",$this->name, PDO::PARAM_STR);
        $result11 = $getter_new1->execute();
        $result11_ar = $getter_new1->fetchALL(PDO::FETCH_ASSOC);
        $author_id = $result11_ar[0]["id"];
        //Запись в таблицу отправки и получений версий автором
        $inserter1 = $this->con->prepare("INSERT INTO send_recive(id_a, id_ver, sends) VALUES (:author_id, :ver_id,:sends)");
        $inserter1->bindParam(':author_id', $author_id,PDO::PARAM_INT);
        $inserter1->bindParam(':ver_id', $version_id,PDO::PARAM_INT);
        $inserter1->bindValue(':sends', TRUE,PDO::PARAM_STR);
        $result = $inserter1->execute();
        //Запись в таблицу следующих и предыдущих версий
        $inserter2 = $this->con->prepare("INSERT INTO prev_next_ver(id_prev,id_next) VALUES(DEFAULT,:id1)");
        $inserter2->bindParam("id1",$version_id,PDO::PARAM_INT);
        $result12=$inserter2->execute();
        if($result12){
            echo('<h3>0 версия статьи успешно загружена и отправлена на рассмотрение случайному редактору</h3>');
        }
        else{
            echo('<h3>Произошла ошибка при загрузке.</h3>');
        }
    }
    function get_redactor_by_version($id){
        $getter = $this->con->prepare('SELECT id_red FROM version_redactor WHERE id_ver=:t');
        $getter->bindValue('t',$id,PDO::PARAM_INT);
        $getter->execute();
        $res_array = $getter->fetchALL(PDO::FETCH_ASSOC);
        return $res_array[0]['id_red'];
        

    }
    function insert_to_redactor($name){
        $getter = $this->con->prepare('SELECT max(id_ver) FROM version_redactor INNER JOIN version ON version.id = version_redactor.id_ver WHERE version.name =:t');
        $getter->bindValue('t',$name,PDO::PARAM_STR);
        $getter->execute();
        $result = $getter->fetchALL(PDO::FETCH_ASSOC);
        if(count($result)>0){
            $id = $this->get_redactor_by_version($result[0]['max(id_ver)']);
            $getter = $this->con->prepare('SELECT max(id_next) FROM prev_next_ver WHERE id_prev =:t');
            $getter->bindValue('t',$result[0]['max(id_ver)'],PDO::PARAM_INT);
            $getter->execute();
            $result = $getter->fetchALL(PDO::FETCH_ASSOC);
            $inserter = $this->con->prepare('INSERT INTO version_redactor(id_ver,id_red) VALUES (:t,:f)');
            $inserter->bindValue('t',$result[0]['max(id_next)'],PDO::PARAM_INT);
            $inserter->bindValue('f',$id,PDO::PARAM_INT);
            $inserter->execute();

        }
    }
    function upload_new_version($title, $date){
        $getter = $this->con->prepare("SELECT id, version_number, stat FROM version WHERE name=:name1 ORDER BY version_number DESC LIMIT 1");
        $getter->bindParam("name1", $title, PDO::PARAM_STR);
        $result = $getter->execute();
        $res_array = $getter->fetchALL(PDO::FETCH_ASSOC);
        $prev_id = $res_array[0]["id"];
        $version = $res_array[0]["version_number"];
        $stat = $res_array[0]["stat"];
        echo("<h2> Последняя версия документа в базе ".$version."</h2>");
        if($stat==1){
            echo("<h3>Просим прощения, но вы не можете отправить новую версию статьи, если 0 версия еще не была утверждена.</h3>");
            echo("<h3>Дождитесь пока ваша статья или исчезнет, или появится версия 1</h3>");
        }
        else{
            $version = $version + 1;
            $tname1 = $_FILES["fileupload1 "]["tmp_name"];
            $tname2 = $_FILES["fileupload2 "]["tmp_name "];
            $pnam = $title.$version;
            move_uploaded_file($tname1,'D:\Myssp\versions'.'/'.$pnam.$version);
            move_uploaded_file($tname2,'D:\Myssp\request'.'/'.$pnam.$version);
            //Вставка в таблицу с версиями
            $inserter = $this->con->prepare("INSERT INTO version(name,stat, dat,version_number) VALUES (:name1, :stat,:dat,:version1)");
            $inserter->bindParam(':name1', $title,PDO::PARAM_STR);
            $inserter->bindValue(':stat', 2,PDO::PARAM_INT);
            $inserter->bindValue(':dat', $date,PDO::PARAM_STR);
            $inserter->bindValue(':version1', $version,PDO::PARAM_INT);
            $result = $inserter->execute();
            //Поиск id текущей последней версии
            $getter_new = $this->con->prepare("SELECT id FROM version WHERE name=:name1 ORDER BY version_number DESC LIMIT 1");
            $getter_new->bindParam("name1", $title, PDO::PARAM_STR);
            $result1 = $getter_new->execute();
            $result1_ar = $getter_new->fetchALL(PDO::FETCH_ASSOC);
            $version_id = $result1_ar[0]["id"];
            //Вставка в prev_next
            $inserter2 = $this->con->prepare("INSERT INTO prev_next_ver(id_prev, id_next) VALUES(:id1,:id2)");
            $inserter2->bindParam("id1",$prev_id,PDO::PARAM_INT);
            $inserter2->bindParam("id2",$version_id,PDO::PARAM_INT);
            $result12=$inserter2->execute();
            //Получение id автора по имени
            $getter_new1 = $this->con->prepare("SELECT id FROM author WHERE username=:name1");
            $getter_new1->bindParam("name1", $this->name, PDO::PARAM_STR);
            $result11 = $getter_new1->execute();
            $result11_ar = $getter_new1->fetchALL(PDO::FETCH_ASSOC);
            $author_id = $result11_ar[0]["id"];
            //
            $inserter1 = $this->con->prepare("INSERT INTO send_recive(id_a, id_ver, sends) VALUES (:author_id, :ver_id,:sends)");
            $inserter1->bindParam(':author_id', $author_id,PDO::PARAM_INT);
            $inserter1->bindParam(':ver_id', $version_id,PDO::PARAM_INT);
            $inserter1->bindValue(':sends', TRUE,PDO::PARAM_STR);
            $result = $inserter1->execute();
            $this->insert_to_redactor($title);
            echo("<h2> Новая версия документа в базе".$version."</h2>");
            if($result){
                echo("<h2>Загрузка произведена успешно</h2>");
            }
            


        }
        
    }
    function if_final($name){
        $getter = $this->con->prepare('SELECT MAX(stat) as st FROM version WHERE name=:t GROUP BY name');
        $getter->bindValue('t',$_POST["name"],PDO::PARAM_STR);
        $getter->execute();
        $res_array = $getter->fetchALL(PDO::FETCH_ASSOC);
        if(count($res_array)>0){
            if($res_array[0]['st']<10){
                return 1;
            }
        }
        return 0;
    }
    function stater($name){
        
        $finder = $this->con->prepare('SELECT max(stat) as st FROM version WHERE name=:t LIMIT 1');
        $finder->bindValue('t',$name,PDO::PARAM_STR);
        $finder->execute();
        $res_array = $finder->fetchALL(PDO::FETCH_ASSOC);
        return $res_array[0]['st'];
    }
    
    function upload_to_server(){
        if (isset($_POST["submit"]) and isset($this->name)){
            //sleep(10);
            if($this->stater($_POST["title1"])>=10){
                echo('<h3>Невозможно получить патент на уже одобренное изобретение<h3>');
                return 0;
            }
            $this->con->exec('Start transaction');
            $this->con->exec('LOCK TABLES version WRITE, author WRITE, send_recive WRITE, prev_next_ver WRITE, version_redactor WRITE');
            
            $date = date('Y-m-d H:i:s');
            $title = $_POST["title1"];
            
            $getter = $this->con->prepare('SELECT * FROM version WHERE name=:name1 ORDER BY version_number DESC');
            $getter->bindValue('name1',$title,PDO::PARAM_STR);
            $result = $getter->execute();
            
            $res_array = $getter->fetchALL(PDO::FETCH_ASSOC);
            //print_r($res_array);
            
            if(count($res_array)>0){
                if($this->if_it_belongs($_POST["title1"])==0){
                    echo('<h3>Вы не можете запросить патент на чужое изобретение</h3>');
                    return 0;
                }
                $this->upload_new_version($title, $date);
            }
            else{
                if($this->if_name_is_uniq($title)==1){
                    echo('<h3>Изобретение с таким названием уже существует<h3>');
                    return 0;
                }
                $this->upload_zero_version($title, $date);
            }
            $this->con->exec('UNLOCK TABLES');
            $this->con->exec('commit');
        

        }
    }
    function show_last_version(){
        if (isset($this->name)){
            $getter = $this->con->prepare('SELECT name, approved, max(version_number) as version_number FROM version
INNER JOIN send_recive ON send_recive.id_ver = version.id WHERE send_recive.sends = 1 AND approved IS NULL AND send_recive.id_a=:t GROUP BY name');
            $getter->bindValue('t', $this->get_author_by_name(), PDO::PARAM_INT);
            $result = $getter->execute();
            $res_array = $getter->fetchALL(PDO::FETCH_ASSOC);
            if(is_array($res_array)){
                for($i =0;$i<count($res_array);$i++){
                    $st = $this->stater($res_array[$i]['name']);
                    echo('<h3> ======================= </h3>');
                    echo('<h3>'.$res_array[$i]['name']." Версия номер ".$res_array[$i]['version_number'].'</h3>');
                    echo('<p><a href="download_final.php?path=versions/'.$res_array[$i]['name'].$res_array[$i]['version_number'].$res_array[$i]['version_number'].'">Описание</a></p>');
                    echo('<p><a href="download_final.php?path=request/'.$res_array[$i]['name'].$res_array[$i]['version_number'].$res_array[$i]['version_number'].'">Заявка</a></p>');
                    if($st>=10){
                        echo('<h3>Статья будет отправлена в номер</h3>');

                    }
                    else{
                        echo('<h3>Статью пока еще рассматривают</h3>');
                    } 

                }
            }
        }

    }
    function show_all_problems(){
        $this->con->exec('LOCK TABLES problem_list WRITE, version WRITE, author WRITE, send_recive WRITE');
        $getter = $this->con->prepare('SELECT problem_list.txt, version.name FROM problem_list INNER JOIN version ON version.id =  problem_list.id_ver WHERE problem_list.id_ver IN (SELECT id_ver as ver_id FROM send_recive WHERE id_a =:f AND sends=1)');
        $getter->bindValue('f',$this->get_author_by_name(),PDO::PARAM_INT);
        $result = $getter->execute();
        $res_array = $getter->fetchALL(PDO::FETCH_ASSOC);
        //echo('<table>');
        //echo('<tr><th>Название</th><th>Проблема</th></tr>');
        for($i =0;$i<count($res_array);$i++){
            echo("<h3>".$res_array[$i]["name"].": ".$res_array[$i]["txt"]."</h3>");
        }
        $this->con->exec('UNLOCK TABLES');
    }
    function count_sended_version($name){
        $this->con->exec('LOCK TABLES version WRITE, send_recive WRITE');
        $getter=$this->con->prepare('SELECT count(version.id) as num from version INNER JOIN send_recive on send_recive.id_ver=version.id WHERE send_recive.sends=0 AND version.name=:t GROUP BY version.name');
        $getter->bindValue('t', $name, PDO::PARAM_STR);
        $result = $getter->execute();
        $res_array =$getter->fetchALL(PDO::FETCH_ASSOC);
        $this->con->exec('UNLOCK TABLES');
        return $res_array[0]['num'];
    }
    
    function show_approved_versions(){
        $this->con->exec('LOCK TABLES send_recive WRITE, version WRITE, author WRITE');
        $getter = $this->con->prepare('SELECT name FROM version WHERE approved = TRUE AND id IN (SELECT id_ver FROM send_recive WHERE id_a = :f)');
        $getter->bindValue('f',$this->get_author_by_name(),PDO::PARAM_INT);
        $result = $getter->execute();
        $res_array = $getter->fetchALL(PDO::FETCH_ASSOC);
        //echo('<table>');
        //echo('<tr><th>Название</th><th>Проблема</th></tr>');
        for($i =0;$i<count($res_array);$i++){
            echo("<h3>".$res_array[$i]["name"]."</h3>");
                echo('<p><a href="download_final.php?path=approvals/'.$res_array[$i]['name'].'">Авторское свидетельство</a></p>');
        }
        $this->con->exec('UNLOCK TABLES');
    }
    
    function send_problems_to_corrector(){
        if (isset($_POST["submit1"]) and isset($_SESSION["session_username"])){
            //$this->con->beginTransaction();
            $name = $_POST["name"];
            $id = $this->get_max_id_by_name($name);
            //echo("<h3>".$id."</h3>");
            if($id == -1){
                echo("<h3>Статья не найдена или вам не пренадлежит</h3>");
                return 0;
            }
            
            $this->con->exec('LOCK TABLES problem_list_corr WRITE');
            $inserter = $this->con->prepare('INSERT INTO problem_list_corr(txt,id_ver) VALUES (:t,:f)');
            $inserter->bindValue(':t',$_POST["problem"],PDO::PARAM_STR);
            $inserter->bindValue(':f',$id,PDO::PARAM_INT);
            $result = $inserter->execute();
            if($result){
                echo("<h3>Обращение зарегистировано</h3>");
            }
            $this->con->exec('UNLOCK TABLES');
            //$this->con->commit();
        }        
    }
    function logout_from_session(){
        if(isset($_POST['submit_exit']) or !isset($_COOKIE['author'])){
            
            unset($_SESSION['session_username']);
            unset($_FILES["fileupload"]);
            unset($_COOKIE['author']);
            setcookie('author', $this->name, time() - 1);
            header('Location: ../startpage_final.php');

        }
    }
    function if_you_can_delete(){
        $getter = $this->con->prepare('SELECT MAX(stat) as st FROM version WHERE name=:t GROUP BY name');
        $getter->bindValue('t',$_POST["name"],PDO::PARAM_STR);
        $getter->execute();
        $res_array = $getter->fetchALL(PDO::FETCH_ASSOC);
        if(count($res_array)>0){
            if($res_array[0]['st']<10){
                return 1;
            }
        }
        return 0;
        
    }
    function delete_all_about_article(){
        if(isset($_POST["submit_delete"])){
            //echo($this->if_it_belongs($_POST["name"]));
            if(!preg_match("/^[a-zA-Z0-9\s]/", $_POST['name'])){
                //echo("Invalid username");
                die("Invalid title");
            }
            if($this->if_it_belongs($_POST["name"])){

                //$home = $_SERVER['DOCUMENT_ROOT']."/";
                //$unlink = unlink($home.'versions/'.$_POST["name"].'*');
                //if($unlink == true){ echo "получилось удалить";} else{ echo "не получилось удалить";}

                if($this->if_you_can_delete() == 0){
                    echo('<h3>Данную статью невозможно удалить<h3>');
                    return 0;
                }
                $getter = $this->con->prepare('SELECT id from version where name =:t and stat<10');
                $getter->bindValue('t', $_POST["name"], PDO::PARAM_STR);
                $getter->execute();
                $res_array = $getter->fetchALL(PDO::FETCH_ASSOC);
                //echo(count($res_array));
                for($i=0;$i<count($res_array);$i++){
                    $deleter = $this->con->prepare('DELETE from prev_next_ver WHERE id_prev =:t OR id_next=:f');
                    $deleter->bindValue('t',$res_array[$i]['id'],PDO::PARAM_INT);
                    $deleter->bindValue('f',$res_array[$i]['id'],PDO::PARAM_INT);
                    $deleter->execute();
                    $deleter = $this->con->prepare('DELETE from problem_list WHERE ver_id=:t');
                    $deleter->bindValue('t',$res_array[$i]['id'],PDO::PARAM_INT);
                    $deleter->execute();
                    $deleter = $this->con->prepare('DELETE from problem_list_corr WHERE id_ver=:t');
                    $deleter->bindValue('t',$res_array[$i]['id'],PDO::PARAM_INT);
                    $deleter->execute();
                    $deleter = $this->con->prepare('DELETE from review WHERE ver_id=:t');
                    $deleter->bindValue('t',$res_array[$i]['id'],PDO::PARAM_INT);
                    $deleter->execute();
                    $deleter = $this->con->prepare('DELETE from send_recive WHERE id_ver=:t');
                    $deleter->bindValue('t',$res_array[$i]['id'],PDO::PARAM_INT);
                    $deleter->execute();
                    $deleter = $this->con->prepare('DELETE from version WHERE id=:t');
                    $deleter->bindValue('t',$res_array[$i]['id'],PDO::PARAM_INT);
                    $deleter->execute();
                    $deleter = $this->con->prepare('DELETE from version_corrector WHERE id_ver=:t');
                    $deleter->bindValue('t',$res_array[$i]['id'],PDO::PARAM_INT);
                    $deleter->execute();
                    $deleter = $this->con->prepare('DELETE from version_redactor WHERE id_ver=:t');
                    $deleter->bindValue('t',$res_array[$i]['id'],PDO::PARAM_INT);
                    $deleter->execute();
                    $deleter = $this->con->prepare('DELETE from version_reviewer WHERE ver_id=:t');
                    $deleter->bindValue('t',$res_array[$i]['id'],PDO::PARAM_INT);
                    $deleter->execute();
                    $deleter = $this->con->prepare('DELETE from version WHERE id=:t');
                    $deleter->bindValue('t',$res_array[$i]['id'],PDO::PARAM_INT);
                    $deleter->execute();
                    
                }
                $this->con->exec('UNLOCK TABLES');
            }
        }
    }
}
?>