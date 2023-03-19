<?php
session_start();
include('special/config_final.php');
include('special/article_distributor_final.php');
echo("<h1>Добрый день</h1>");
echo("<h3>Вы зашли как пользователь - редактор ".$_SESSION["session_username"]."</h3>");

$lifetime = 120;
setcookie('redactor', $_SESSION["session_username"], time()+$lifetime,'/');
$username = $_SESSION["session_username"];
?>
<h3>Если хотите выйти, воспользуйтесь кнопкой:</h3>
    <form method = "post" enctype="multipart/form-data">
        <input type = "submit" name = "submit_exit" value = "Выйти">
    </form>
    <?php
    setcookie('redactor', $username, time()+$lifetime,'/');
    logout($connection);
    ?>
<script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
<?php
echo("<h3>Напрваленные вам заявки на регистрацию изобретений:</h3>");
article_redactor_show($connection);

?>
<html>
 <head>
  <meta charset="utf-8">
  <script>
   function VAKappear() {
    var str = document.getElementById("email").value;
    var status = document.getElementById("status");
    var re = /^[^\s()<>@,;:\/]+@\w[\w\.-]+\.[a-z]{2,}$/i;
    if (re.test(str)) status.innerHTML = "Адрес правильный";
      else status.innerHTML = "Адрес неверный";
    if(isEmpty(str)) status.innerHTML = "Поле пустое";
   }
   function isEmpty(str){
    return (str == null) || (str.length == 0);
   }
  </script>
 </head> 
<body>
    <h3>В замечаниях используйте только заглавные и прописные Английские и Русские буквы и пробел</h3>
    <table>
        <tr>
            <th><h3>Замечание автору</h3></th>
            <th><h3>Перенаправьте заявку корректору</h3></th>
            
        </tr>
        <tr>
            <td>
    <form method = "post" enctype="multipart/form-data">
        <lable>Title</lable>
        <input type = "text" name = "title" pattern="[a-zA-Z0-9\s]+" required>
        <lable>Problem</lable>
        <input type = "text" name = "problem" pattern="[a-zA-Z0-9\s]+" required>
        <input type = "submit" name = "submit">
    </form>
    <?php
    setcookie('redactor', $username, time()+$lifetime,'/');
    send_problems_aut($connection)
    ?>
    </td>
    <td>
    <form method = "post" enctype="multipart/form-data">
        <lable>Имя пользователя корректора</lable>
        <input type = "text" id ="corrector" name = "corrector">
        <lable>Название статьи</lable>
        <input type = "text" name = "name" pattern="[a-zA-Z0-9\s]+">
        <input type = "submit" name = "submit3">
    </form>
    <?php
    setcookie('redactor', $username, time()+$lifetime,'/');
    send_article_cor($connection);
    ?>
    </td>
    
    
        </tr>
    </table>

    <h3>Увидеть заполненность номеров номер</h3>
    <form id="see_current_number" method="post">
        <button type="submit" name="submit_new" value ="увидеть"></button>
    </form>
    <div id="res"></div>
    
    <script type ="text/javascript">
        $(document).ready(function() {
            //alert('I am here');
            $('#see_current_number').on('submit',function(e)
            {
                //alert('new');
                e.preventDefault();
                $.ajax({
                    
                    type: "POST",
                    url: "youdidit_final.php",
                    
                    success: function(data){
                        $('#res').append(data);
                    }

                }

                )
            })
            
        })
    </script>
</body>