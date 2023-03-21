<?php
session_start();
include('special/config_final.php');
include('special/author_class_final.php');
//print_r($_SESSION);
$dis = new Author($connection,$_SESSION["session_username"]);
?>
<body>
    <t1>
        <td>
        <h1>Добрый день</h1>
            <h3>Вы зашли как пользователь:</h3><?php echo($_SESSION["session_username"]);?>
        </td>
    </t1>
    <t2>
        <td>
        <h3>Если хотите вернуться на начальную страницy, воспользуйтесь кнопкой:</h3>
        <form method = "post" enctype="multipart/form-data">
            <input type = "submit" name = "submit_exit" value = "Выход">
        </form>
        <?php
        $dis->logout_from_session();
        ?>
        </td>
    </t2>


    <h3>В названии изобретений и претензиях используйте только заглавные и прописные английские буквы и пробел</h3>
    <table>

        <tr><th align="left">Если хотите получить авторское свидетельство, приложите название изобретения, его описание и стандартную форму заявки:</th></tr>
        <tr>
            <td>
                <form method = "post" enctype="multipart/form-data">
                    <lable>Название изобретения:</lable>
                    <input type = "text" name = "title1" pattern="[a-zA-Z0-9\s]+" required>
                    <lable>Описание изобретения:</lable>
                    <input type = "file" name = "fileupload1">
                    <lable>Заявка:</lable>
                    <input name = "fileupload2" type = "file">
                    <input type = "submit" name = "submit">
                </form>
                <?php
                if (isset($_POST['title1']) and isset($_POST['submit'])){
                    if(!preg_match("/^[a-zA-Z0-9\s]/", $_POST['title1'])){
                        echo("Invalid username");
                        die("Invalid title");
                    }
                }


                $username = $_SESSION["session_username"];
                $lifetime = 120;
                setcookie('author', $username, time()+$lifetime,'/');
                $dis->upload_to_server();
                ?>
            </td>
        </tr>
        <tr><th align="left">Если хотите отправить претензию корректору, то направьте сюда:</th></tr>
        <tr>
        <td>
            <form method = "post" enctype="multipart/form-data">
            <lable>Название изобретения:</lable>
            <input type = "text" name = "name" pattern="[a-zA-Z0-9\s]+" required>
            <lable>Претензия:</lable>
            <input type = "text" name = "problem" pattern="[a-zA-Z0-9\s]+" required>
            <input type = "submit" name = "submit1">
        </form>
            <?php
                if(isset($_POST['submit1']) and isset($_POST['name']) and isset($_POST['problem'])){
                    if(!preg_match("/^[a-zA-Z0-9\s]/", $_POST['name'])){
                        //echo("Invalid username");
                        die("Invalid title");
                    }
                    if(!preg_match("/^[a-zA-Z0-9\s]/", $_POST['problem'])){
                        //echo("Invalid username");
                        die("Invalid problem");
                    }
                
                    $username = $_SESSION["session_username"];
                    $lifetime = 120;
                    setcookie('author', $username, time()+$lifetime,'/');
                    $dis->send_problems_to_corrector();
            }
            ?>
        </td></tr>
        <tr><th align="left">Если хотите удалить заявку с сайта, то воспользуйтесь этой формой:</th></tr>
        <tr>
            <td>
            <form method = "post" enctype="multipart/form-data">
                <lable>Название изобретения:</lable>
                <input type = "text" name = "name" pattern="[a-zA-Z0-9\s]+" required>
                <input type = "submit" name = "submit_delete">
            </form>
            <?php
            $username = $_SESSION["session_username"];
            $lifetime = 120;
            setcookie('author', $username, time()+$lifetime,'/');
            if(isset($_POST['name'])){
                if(!preg_match("/^[a-zA-Z0-9\s]/", $_POST['name'])){
                    //echo("Invalid username");
                    die("Invalid title");
                }
                $dis->delete_all_about_article();
            }
            ?>
            </td>
        </tr>
    </table>

    <table>
        <tr><th>Ваши заявки</th><th>Предложения корректировки заявок и описаний</th><th>Изобретения, на которые выдано авторское свидетельство</th><th>Отказано в патенте</th></tr>
        <tr>
        <td>
            <?php
            $dis->show_last_version();
            ?>
        </td>
        <td>
            <?php
            $dis->show_all_problems();
            ?>
        </td>
        <td>
            <?php
            $dis->show_approved_versions();
            ?>
        </td>
        <td>
           <?php
           $dis->show_deleted_versions();
           ?>
        </td>
    </tr>
    </table>
    
    
    
</body>

