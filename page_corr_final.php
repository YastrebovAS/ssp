<?php
session_start();
include('special/config_final.php');
include('special/correction_distributor_final.php');
$dis = new Correction_distributor($connection,$_SESSION["session_username"]);


$lifetime = 120;
setcookie('redactor', $_SESSION["session_username"], time()+$lifetime,'/');
$username = $_SESSION["session_username"];
?>
<body>
<h3>Если хотите выйти, воспользуйтесь кнопкой:</h3>
    <form method = "post" enctype="multipart/form-data">
        <input type = "submit" name = "submit_exit" value = "Выход">
    </form>
    <?php
    $dis->logout_from_session();
    ?>
<?php
echo('<h3>Изобретения на проверку:<h3>');
$dis->get_articles();
?>
    <table>
        <tr>
            <th><h3>Отправить автору замечание по поводу описания изобретения</h3></th>
            <th><h3>Отказать в регистрации</h3></th>
            <th><h3>Выдать авторское свидетельство</h3></th>
            
        </tr>
        <tr>
            <td>
    <form method = "post" enctype="multipart/form-data">
        <lable>Название изобретения:</lable>
        <input type = "text" name = "title1" pattern="[a-zA-Z0-9\s]+" required><br/>
        <lable>Замечание:</lable>
        <input type = "text" name = "problem" pattern="[a-zA-Z0-9\s]+" required>
        <br/>
        <br/>
        <input type = "submit" name = "submit1">
    </form>
    <?php
    setcookie('corrector', $username, time()+$lifetime,'/');
    $dis->send_problems_aut($connection)
    ?>
    </td>
    <td>
    <script type="text/javascript">
    function showVak() {
    var x = document.getElementById("fn");
    x.style.display = "inline";
    }
    function hideVak() {
    var x = document.getElementById("fn");
    x.style.display = "none";
    }
    </script>
    <form method = "post" id = "send_to_number" enctype="multipart/form-data">
        <lable>Название изобретения:</lable>
        <input type = "text" id="name" name = "name" pattern="[a-zA-Z0-9\s]+"><br/>
        <lable>Причина:</lable>
        <input type = "radio" value="significance" name = "denial" onclick="hideVak()">Недостаточная значимость</input>
        <input type = "radio" value = "plagiarism" name = "denial" onclick="showVak()">Плагиат</input>
        <br/>
        <div id="fn" style="display: none;" hidden="hidden">Сообщить в ВАК?<input type="checkbox" name="vak" value= "rep"></input></div>
        <br/>
        <input type = "submit" name = "submit_bad">
    </form>
    <?php
    setcookie('corrector', $username, time()+$lifetime,'/');
    $dis->remove($connection);
    ?>
    </td>
    <td>
    <form method = "post" enctype="multipart/form-data">
        <lable>Название изобретения:</lable>
        <input type = "text" name = "title2" pattern="[a-zA-Z0-9\s]+" required>
        <lable>Патент:</lable>
        <input name = "patent" type = "file" required>
        <br/>
        <br/>
        <input type = "submit" name = "submit2">
    </form>
    <?php
    setcookie('corrector', $username, time()+$lifetime,'/');
    $dis->send_approval_aut($connection);
    ?>
    </td>
    </tr>
    </table>

</body>