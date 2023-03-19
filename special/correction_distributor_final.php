<?php
include('download_final.php');
class Correction_distributor{
    private $con, $name;
    function __construct($connection,$name) {
        $this->con = $connection;
        $this->name = $name;
        echo("<h2>Здравствуйте</h2>");
        echo("<h3>Вы зашли как корректор ".$this->name."</h3>");
       
    }
    
    function check_if_it_belongs($connection, $id_ver, $id_cor){
    
    $connection->exec('LOCK TABLES version_corrector WRITE');
    $checker = $connection->prepare('SELECT id_ver as id from version_corrector where id_cor=:t and id_ver=:f');
    $checker->bindParam('t', $id_cor,PDO::PARAM_INT);
    $checker->bindParam('f', $id_ver,PDO::PARAM_INT);
    $checker->execute();
    $res_array = $checker->fetchALL(PDO::FETCH_ASSOC);
    $connection->exec('UNLOCK TABLES');

    if(count($res_array)>0){
        return 1;
    }
    else{
        return 0;
    }
    }
    
    function get_corrector_by_name(){
        $this->con->exec('LOCK TABLES corrector WRITE');
        $getter=$this->con->prepare('SELECT id from corrector where username =:f');
        $getter->bindParam('f',$this->name,PDO::PARAM_STR);
        $result = $getter->execute();
        $res_array = $getter->fetchALL(PDO::FETCH_ASSOC);
        $this->con->exec('UNLOCK TABLES');
        return $res_array[0]['id'];
    }
    
    function get_all_id_where_stat_on_delete($connection){
    $connection->exec('LOCK TABLES version WRITE');
    $getter = $connection->prepare('SELECT id FROM version WHERE stat<0');
    $getter->execute();
    $res_array = $getter->fetchALL(PDO::FETCH_ASSOC);
    $connection->exec('UNLOCK TABLES');
    return $res_array;
    }
    
    function get_corrector_id($connection){
    $connection->exec('LOCK TABLES corrector WRITE');
    $getter = $connection->prepare('SELECT id, username FROM corrector where username=:f');
    $getter->bindParam('f',$_SESSION["session_username"],PDO::PARAM_STR);
    $res = $getter->execute();
    $res_array = $getter->fetchALL(PDO::FETCH_ASSOC);
    $connection->exec('UNLOCK TABLES');
    if(count($res_array)==0){
        return -1;
    }
    return $res_array[0]['id'];
    }
    
    function get_last_version_by_name($connection, $name){
    $connection->exec('LOCK TABLES version WRITE');
    $getter=$connection->prepare('SELECT max(id) as id from version where name=:f group by name');
    $getter->bindParam('f',$name,PDO::PARAM_STR);
    $result = $getter->execute();
    $res_array = $getter->fetchALL(PDO::FETCH_ASSOC);
    $connection->exec('UNLOCK TABLES');
    if(count($res_array)==0){
        return -1;
    }
    if($this->check_if_it_belongs($connection, $res_array[0]['id'], $this->get_corrector_id($connection))==0){
        return -1;
    }
    return $res_array[0]['id'];
    }
    
    function get_articles(){
        $this->con->exec('LOCK TABLES version WRITE, version_corrector WRITE');
        $getter = $this->con->prepare('SELECT name, approved, max(version_number) as version_number FROM version INNER JOIN version_corrector ON version_corrector.id_ver=version.id where version_corrector.id_cor =:f and approved is NULL and stat<10 and stat>0 GROUP BY name');
        $getter->bindValue('f',$this->get_corrector_by_name(),PDO::PARAM_INT);
        $result = $getter->execute();
        $res_array = $getter->fetchALL(PDO::FETCH_ASSOC);
        
        if(is_array($res_array)){
            for($i=0;$i<count($res_array);$i++){
                echo('<h3>'.$res_array[$i]['name'].'</h3>');
                echo('<p><a href="download_final.php?path=versions/'.$res_array[$i]['name'].$res_array[$i]['version_number'].$res_array[$i]['version_number'].'">Описание</a></p>');
                echo('</br>');
                
            };
        }
        $this->con->exec('UNLOCK TABLES');
    }
    
    function last_correction(){
        $getter = $this->con->prepare('SELECT max(problem_list_corr.id) as id FROM problem_list_corr INNER JOIN version_corrector ON version_corrector.id_ver=problem_list_corr.id_ver INNER JOIN version ON version.id=version_corrector.id_ver WHERE version_corrector.id_cor=:t GROUP BY version.name');
        $getter->bindValue('t', $this->get_corrector_by_name(),PDO::PARAM_INT);
        $result = $getter->execute();
        $res_array = $getter->fetchALL(PDO::FETCH_ASSOC);
        return $res_array;
    }
    
    function logout_from_session(){
        if(isset($_POST['submit_exit']) or !isset($_COOKIE['corrector'])){
            unset($_SESSION['session_username']);
            unset($_FILES["fileupload"]);
            unset($_COOKIE['corrector']);
            setcookie('corrector', $this->name, time() - 1);
            header('Location: ../startpage_final.php');

        }
    }
    
    function remove($connection){
    if(isset($_POST["submit_bad"])){
        if(!preg_match("/^[a-zA-Z0-9\s]/", $_POST['name'])){
            //echo("Invalid username");
            die("Invalid title");
        }
        $name = $_POST['name'];
        $warning = $connection->prepare("INSERT INTO deletion VALUES(:t, date('y-m-d',(strtotime( date('y-m-d').' + 14 days')))");
        $warning = $connection->bindValue('t',$name,PDO::PARAM_STR);
        $warning ->execute();
        $updater = $connection->prepare('UPDATE version SET stat = -2 WHERE name =:t');
        $updater->bindValue('t',$name,PDO::PARAM_STR);
        $updater->execute();
        $result = $this->get_all_id_where_stat_on_delete($connection);
        for($i=0; $i<count($result);$i++){
            $connection->exec('LOCK TABLES version WRITE, prev_next_ver WRITE, problem_list WRITE, problem_list_corr WRITE, send_recive WRITE, version_corrector WRITE, version_redactor WRITE');
            $deleter = $connection->prepare('DELETE FROM version WHERE id=:t');
            $deleter->bindValue('t',$result[$i]['id'],PDO::PARAM_INT);
            $deleter->execute();
            $deleter = $connection->prepare('DELETE FROM prev_next_ver WHERE id_prev=:t OR id_next=:f');
            $deleter->bindValue('t',$result[$i]['id'],PDO::PARAM_INT);
            $deleter->bindValue('f',$result[$i]['id'],PDO::PARAM_INT);
            $deleter->execute();
            $deleter = $connection->prepare('DELETE FROM  problem_list WHERE id_ver=:t');
            $deleter->bindValue('t',$result[$i]['id'],PDO::PARAM_INT);
            $deleter->execute();
            $deleter = $connection->prepare('DELETE FROM  problem_list_corr WHERE id_ver=:t');
            $deleter->bindValue('t',$result[$i]['id'],PDO::PARAM_INT);
            $deleter->execute();
            $deleter = $connection->prepare('DELETE FROM  send_recive WHERE id_ver=:t');
            $deleter->bindValue('t',$result[$i]['id'],PDO::PARAM_INT);
            $deleter->execute();
            $deleter = $connection->prepare('DELETE FROM  version_corrector WHERE id_ver=:t');
            $deleter->bindValue('t',$result[$i]['id'],PDO::PARAM_INT);
            $deleter->execute();
            $deleter = $connection->prepare('DELETE FROM  version_redactor WHERE id_ver=:t');
            $deleter->bindValue('t',$result[$i]['id'],PDO::PARAM_INT);
            $deleter->execute();
            
            $connection->exec('UNLOCK TABLES');
        }
    }
    }
    
    function send_problems_aut($connection){
    if (isset($_POST["submit1"]) and isset($_SESSION["session_username"])){
        if(!preg_match("/^[a-zA-Z0-9\s]/", $_POST['title1'])){
            //echo("Invalid username");
            die("Invalid title");
        }
        if(!preg_match("/^[a-zA-Z0-9\s]/", $_POST['problem'])){
            //echo("Invalid username");
            die("Invalid title");
        }
        echo($_POST['title1']);
        $id = $this->get_last_version_by_name($connection,$_POST["title1"]);
        if($id == -1){
            echo('<h3>Такой статьи не существует</h3>');
            return 0;
        }
        $connection->exec('LOCK TABLE problem_list WRITE');
        $inserter = $connection->prepare('INSERT INTO problem_list(txt, id_ver) VALUES (:f,:t)');
        $inserter->bindParam('f',$_POST["problem"],PDO::PARAM_STR);
        $inserter->bindParam('t',$id,PDO::PARAM_INT);
        $result = $inserter->execute();
        $connection->exec('UNLOCK TABLE');
        if($result){
            echo('<h3>Проблема успешно зарегистрирована и отправлена автору</h3>');
        }
    }
    }

    function show_problems(){
        $our_ids = $this->last_correction();
        for($i=0;$i<count($our_ids);$i++){
            $printer = $this->con->prepare('SELECT problem_list_corr.txt as txt, version.name as name from problem_list_corr INNER JOIN version on problem_list_corr.id_ver=version.id where problem_list_corr.id=:t');
            $printer->bindValue('t',$our_ids[$i]['id'],PDO::PARAM_INT);
            $printer->execute();
            $res_array = $printer->fetchALL(PDO::FETCH_ASSOC);
            echo('<h4>'.$res_array[$i]['name'].': '.$res_array[$i]['txt'].'</h4>');
        }
        
    }
    
    function send_approval_aut($connection){
    if (isset($_POST["submit2"]) and isset($_SESSION["session_username"])){
        if(!preg_match("/^[a-zA-Z0-9\s]/", $_POST['title2'])){
            //echo("Invalid username");
            die("Invalid title");
        }
        echo($_POST['title2']);
        $id = $this->get_last_version_by_name($connection,$_POST["title2"]);
        if($id == -1){
        echo($_POST['title2']);
            echo('<h3>Такой статьи не существует</h3>');
            return 0;
        }
        $tname1 = $_FILES["patent"]["tmp_name"];
        $title = $_POST["title2"];
        move_uploaded_file($tname1,'D:\Myssp\approvals'.'/'.$title);
        $connection->exec('LOCK TABLE version WRITE');
        $inserter = $connection->prepare('UPDATE version SET approved = TRUE WHERE id =:t');
        $inserter->bindParam('t',$id,PDO::PARAM_INT);
        $result = $inserter->execute();
        $connection->exec('UNLOCK TABLE');
        if($result){
            echo('<h3>Авторское свидетельство успешно зарегистрировано, автор уведомлен.</h3>');
        }
    }
    }

    
    
}
?>