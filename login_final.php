<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<?php
session_start();
?>
<a class="me-3 py-2 text-dark text-decoration-none" href="/startpage_final.php">Назад</a>
<h1>Страница для входа</h1>
<style>
    h1 {
    color : #000000;
    text-align : center;
    font-family: "SIMPSON";
}
    form {
    width: 300px;
    margin: 0 auto;
}
</style>

<form method="post" id ="login" name="signup-form">
<div class="form-element">
<label>Имя пользователя</label>
<input type="text" name="username" pattern="[a-zA-Z0-9\s]+" required />
</div>
<div class="form-element">
<label>Почта</label>
<input type="email" name="email" required />
</div>
<div class="form-element">
<label>Пароль</label>
<input type="text" name="password" pattern="[a-zA-Z0-9\s]+" required />
</div>
<button type="submit" name="auth" value="auth">Авторизация</button>
</form>
<?php

if(isset($_COOKIE['remember_u'])){
    echo('<h3>В прошлый раз вы заходили с такими логином</h3>');
    echo('<h3>'.$_COOKIE['remember_u'].'</h3>');
    echo('<h3>зайти так же?<h3>');
}
?>
<form method = "post" id = "login_cookie" name ="login_cookie">
    <button type="submit2" name="auth2" value="auth2">Авторизация по сохраненным данным</button>
</form>
<?php

if(isset($_COOKIE['remember_u']) and isset($_POST['auth2'])){
    
    $username = $_COOKIE['remember_u'];
    $_SESSION['session_username']=$_COOKIE['remember_u'];
    $email = $_COOKIE['remember_e'];
    $password = $_COOKIE['remember_p'];
    $_SESSION["session_username"]=$username;
    //print_r($_SESSION);
    if($_COOKIE['remember_w']=='aut'){
        $lifetime = 120;
        setcookie('author', $username, time()+$lifetime,'/');
        header("Location:page_auth_final.php");
    }
    if($_COOKIE['remember_w']=='red'){
        $lifetime = 120;
        setcookie('redactor', $username, time()+$lifetime,'/');
        header("Location:page_red_final.php");
    }
    if($_COOKIE['remember_w']=='cor'){
        $lifetime = 120;
        setcookie('corrector', $username, time()+$lifetime,'/');
        header("Location:page_corr_final.php");
    }
    if($_COOKIE['remember_w']=='rev'){
        $lifetime = 120;
        setcookie('reviwer', $username, time()+$lifetime,'/');
        header("Location:page_rev_final.php");
    }
    if($_COOKIE['remember_w']=='admin'){
        $lifetime = 120;
        setcookie('admin', $username, time()+$lifetime,'/');
        header("Location:page_admin_final.php");
    }
    
}
?>
<?php
    
    
    //print_r($_COOKIE);
    if(isset($_POST['auth'])){
        include('special/config_final.php');
        //$ercounter = 0;
        if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            //Email not valid, 
            //echo("Invalid email");
            //$ercounter++;
            die("Invalid email");
        };
        if(!preg_match("/^[a-zA-Z0-9\s]/", $_POST['username'])){
            //echo("Invalid username");
            //$ercounter++;
            die("Invalid username");
        }
        if(!preg_match("/^[a-zA-Z0-9\s]/", $_POST['password'])){
            //echo("Invalid pwd");
            //$ercounter++;
            die("Invalid pwd");
        }
        if($_POST['username']=='admin' and $_POST['email']=='admin@mail.ru' and $_POST['password']=='adminpassword'){
            
            $username = $_POST['username'];
            $_SESSION['session_username']=$username;
            
            $lifetime = 120;
            setcookie('admin', $username, time()+$lifetime,'/');
            setcookie('remember_u','admin',time()+360000,'/');
            setcookie('remember_e','admin@mail.ru',time()+360000,'/');
            setcookie('remember_p','adminpassword',time()+360000,'/');
            setcookie('remember_w','admin',time()+360000,'/');
            header("Location:page_admin_final.php");
            
        }
        else{
            $connection->exec('LOCK TABLES author WRITE, redactor WRITE, corrector WRITE');
            $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $existigs_auth = $connection->prepare("SELECT * FROM author WHERE pas=:password and username=:u");
        $existigs_auth->bindParam("password", $password, PDO::PARAM_STR);
        $existigs_auth->bindParam("u", $username, PDO::PARAM_STR);
        $existigs_auth->execute();
        $existigs_red = $connection->prepare("SELECT * FROM redactor WHERE passwd=:password and username=:u");
        $existigs_red->bindParam("password", $password, PDO::PARAM_STR);
        $existigs_red->bindParam("u", $username, PDO::PARAM_STR);
        
        $existigs_red->execute();
        $existigs_corr = $connection->prepare("SELECT * FROM corrector WHERE passwd=:password and username=:u");
        $existigs_corr->bindParam("password", $password, PDO::PARAM_STR);
        $existigs_corr->bindParam("u", $username, PDO::PARAM_STR);
        $existigs_corr->execute();
        $connection->exec('UNLOCK TABLES');
        if($existigs_auth->rowCount()){
            echo '<h1>You are author</h1>';
            $_SESSION['session_username']=$username;
            $lifetime = 120;
            setcookie('author', $username, time()+$lifetime,'/');
            setcookie('remember_u', $username,time()+360000,'/');
            setcookie('remember_e',$email,time()+360000,'/');
            setcookie('remember_p',$password,time()+360000,'/');
            setcookie('remember_w','aut',time()+360000,'/');
            header("Location:page_auth_final.php");
        }
        if($existigs_red->rowCount()){
            $_SESSION['session_username']=$username;
            $lifetime = 120;
            setcookie('redactor', $username, time()+$lifetime,'/');
            
            setcookie('remember_u', $username,time()+360000,'/');
            setcookie('remember_e',$email,time()+360000,'/');
            setcookie('remember_p',$password,time()+360000,'/');
            setcookie('remember_w','red',time()+360000,'/');
            header("Location:page_red_final.php");
        }
        if($existigs_corr->rowCount()){
            $_SESSION['session_username']=$username;
            $lifetime = 120;
            setcookie('corrector', $username, time()+$lifetime,'/');
            
            setcookie('remember_u', $username,time()+360000,'/');
            setcookie('remember_e',$email,time()+360000,'/');
            setcookie('remember_p',$password,time()+360000,'/');
            setcookie('remember_w','cor',time()+360000,'/');
            header("Location:page_corr_final.php");
        }
        }
    }
?>