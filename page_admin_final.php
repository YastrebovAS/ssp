<?php
session_start();
include('special/config_final.php');
include('special/admin_class_final.php');
$dis = new Admin($connection,'admin');
?>
<script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
<body>
<h3>Если хотите выйти, воспользуйтесь кнопкой:</h3>
<form method = "post" enctype="multipart/form-data">
    <input type = "submit" name = "submit_exit" value = "выйти">
</form>
<?php
$dis->logout_from_session();
?>
<h3>Поступившие изобретения</h3>
<?php
$lifetime =120;
setcookie('admin', 'admin', time()+$lifetime,'/');
$dis->show_all_zero_version_of_article();
?>
<table>
    <tr><th><h3>Отправьте нулевую версию редактору</h3></th></tr>
    <tr>
        <td>
            <form method = "post" enctype="multipart/form-data">
                <lable>Название изобретения</lable>
                <input type = "text" name = "name" pattern="[a-zA-Z0-9\s]+" required>
                <lable>Имя редактора</lable>
                <input type = "text" name = "redactor" pattern="[a-zA-Z0-9\s]+" required>
                <lable>File upload</lable>
                <input name = "fileupload" type = "file" required>
                <input type = "submit" name = "submit1">
            </form>
            <?php
            //print_r($_POST);
            if(isset($_POST['submit1'])){
                if(!preg_match("/^[a-zA-Z0-9\s]/", $_POST["name"])){
                    //echo("Invalid name");
                    //$ercounter++;
                    die("Invalid name");
                }
                if(!preg_match("/^[a-zA-Z0-9\s]/", $_POST["redactor"])){
                    //echo("Invalid name");
                    //$ercounter++;
                    die("Invalid redactor name");
                }
            }
            $lifetime =120;
            setcookie('admin', 'admin', time()+$lifetime,'/');
            $dis->send_zero_version_to_redactor();
            ?>
        </td>
    </tr>

</table>


<h3>Занятость редакторов</h3>
<form id="see_red_workload" method="post">
    <button type="submit" name="submit_new" value ="проверить">Проверить</button>
</form>
<div id="result"></div>

<script type ="text/javascript">
    $(document).ready(function() {
        //alert('I am here');
        $('#see_red_workload').on('submit',function(e)
        {
            //alert('new');
            e.preventDefault();
            $.ajax({

                    type: "POST",
                    url: "redactor_check.php",
                    success: function(data){
                        $('#result').append(data);
                    }

                }

            )
        })

    })
</script>
</body>