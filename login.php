<?php
require 'components/config.php';
$css = 'css/loginn.css';
$title = 'foro';
include 'components/header.php';
if (isset($_SESSION['signed_in']) && $_SESSION['signed_in'] == true) {
    header('Location: index.php');
}

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['pass'];
    try {
        $sql = mysqli_query($connect, ("SELECT * FROM usuarios where 
                                                        user_correo ='$email'"));
        $ver = mysqli_num_rows($sql);
        if ($ver == 1) {
            $row = mysqli_fetch_array($sql);
            $id = $row['user_id'];
            $row['user_status']="active";
            if ($row['user_status'] == "active") {
                if ($email == $row['user_correo']) {
                    if ($password == $row['user_cont']) {
                        $_SESSION['id'] = $id;
                        $_SESSION['signed_in'] = true;
                        $acceso = date("Y-m-d H:i:s");
                        $int = mysqli_query($connect, "UPDATE `usuarios` SET `user_time`='$acceso' WHERE `user_id`='$id';");
                        header('Location: index.php');
                    } else {
                        $message = "Contraseña incorrecta";
                    }
                }
            } else {
                $message = "Usuario desactivado.\nPara iniciar sesión contacte con un administrador";
            }
        } else {
            $message = "No se encontró nigún usuario registrado con este correo electrónico";
        }
    } catch (\Throwable $th) {
        die($th->getMessage());
    }
}



?>

<div id="login" class="caja">
    <div class="cont_form log">
        <div class="title_log">
            <h2>Accede a Foro</h2>
            <h4>La Mayor comunidad de foros</h4>
        </div>
        <form method="post" action="#">
            <label class="img_group img_log_email"><img src="img/icon_input_nom.png" alt=""></label>
            <input class="inp_log" type="text" name="email" placeholder="Email" required />
            <label class="img_group img_log_pass"><img src="img/icon_input_pass.png" alt=""></label>
            <label class="img_group img_log_vitxt pass"><img src="img/vector_eye.png" alt=""></label>
            <label class="img_group img_log_vipass"><img src="img/icon_input_pass_text.png" alt=""></label>
            <input id="log_pass" class="inp_log" type="password" name="pass" placeholder="Contraseña" required />
            <a class="form_te" href="">¿Has olvidado tu contraseña?</a>
            <button class="button btn_log" type="submit" name="login">Login</button>
        </form>
        <p id="message"><?php echo $message?? "" ?></p>
    </div>
    <div class="cont_img">
        <img class="img" src="img/web/login.svg" alt="">
    </div>
</div>

<div id="registro" class="caja registro">
    <div class="cont_img">
        <img src="img/web/register.svg" alt=""></svg>
    </div>
    <div class="cont_form reg">
        <div class="title_reg">
            <h2>Únete a Foro</h2>
            <h4>La Mayor comunidad de foros</h4>
        </div>
        <form method="post" action="#" enctype='multipart/form-data'>
            <label class="img_group img_reg_name"><img src="img/icon_input_name.png" alt=""></label>
            <input class="inp_reg" type="text" name="name" placeholder="Nombre de usuario">
            <label class="img_group img_reg_email"><img src="img/icon_input_nom.png" alt=""></label>
            <input class="inp_reg" type="text" name="email" placeholder="Email" required>
            <label class="img_group img_reg_pass"><img src="img/icon_input_pass.png" alt=""></label>
            <label class="img_group img_reg_vitxt pass"><img src="img/vector_eye.png" alt=""></label>
            <label class="img_group img_reg_vipass"><img src="img/icon_input_pass_text.png" alt=""></label>
            <input id="pass" class="inp_reg input_pass" type="password" name="pass" placeholder="Contraseña">
            <label class="img_group img_reg_pass1"><img src="img/icon_input_pass.png" alt=""></label>
            <label class="img_group img_reg_vitxt pass"><img src="img/vector_eye.png" alt=""></label>
            <input id="valid_pass" class="inp_reg" type="password" name="valid_pass" placeholder="Repite tu Contraseña">
            <button class=" button btn_reg" type="submit" name="registrar" id="registerButton">Registrate</button>
            <span>¿Ya tienes Cuenta? <a class="form_et" href="">Inicia Sesion</a></span>

            <?php
                    
            if (isset($_POST['registrar'])) {
                if ($_POST['pass'] == $_POST['valid_pass']) {
                
                $name = $_POST['name'];
                $email = $_POST['email'];
                $password = $_POST['pass'];

                $rev = mysqli_num_rows(mysqli_query($connect, "SELECT user_correo from usuarios where user_correo = '$email' "));
                    if ($rev == 0) {
                        $sql = mysqli_query($connect, "INSERT INTO usuarios (user_nombre,
                                                                                        user_correo,
                                                                                        user_cont)
                                                                                VALUES ('$name',
                                                                                        '$email',
                                                                                        '$password')");
                    } else {$error = "El correo ya se encuentra registrado";?>
                        <script> alert("<?php echo $error;?>");</script>
                    <?php }
                }else {
                   ?><script> alert("Las contraseñas no coinciden");</script><?php 
                }   
            }
            ?>
        </form>

    </div>
</div>
<script src="js\loginn.js"></script>
<?php include 'components/footer.php'; ?>