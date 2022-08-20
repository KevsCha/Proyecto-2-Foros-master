<?php
$title = "Mi perfil";
$css = "css/profile.css";
include("components/header.php");
$errorMsg = "";
$userThreads = [];
try {
    $userThreadsQuery = "SELECT * FROM publicaciones WHERE publi_user = $id";
    $userThreadsResult = $mysqli->query($userThreadsQuery);
    while ($row = $userThreadsResult->fetch_assoc()) {
        $userThreads[] = $row;
    }
} catch (Exception $e) {
    echo $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST["deleteProfile"])){
        $deleteProfileQuery = "UPDATE usuarios SET user_status = 'inactive' WHERE user_id = $id";
        try {
            $result= $mysqli->query($deleteProfileQuery);
            if($result){
                header('Location: logout.php');
            }
        } catch (\Exception $e) {
            echo "No se pudo eliminar";
        }
    }

    if (isset($_POST['edit'])) {
        if (!empty($_POST['userName'] && $_POST['userName'] != $userData[0]['user_nombre'])) {
            $newName = $_POST['userName'];
            $editQuery = "UPDATE usuarios SET user_nombre = '$newName' WHERE user_id = $id";
            $result = $mysqli->query($editQuery);
            if ($result) {
                $statusMsg = "Nombre de usuario cambiado correctamente.";
            } else {
                $errorMsg .= "No se pudo cambiar.";
            }
            unset($_POST);
        }

        if (!empty($_POST['actualPass'])) {
            if ($userData[0]['user_cont'] == $_POST['actualPass']) {
                if (!empty($_POST['newPass']) && $_POST['newPass'] !== $_POST['newPassConfirm']) {
                    $errorMsg = "Las contraseñas introducidas no coinciden";
                } else {
                    $newPass = $_POST['newPass'];
                    $editQuery = "UPDATE usuarios SET user_cont = '$newPass' WHERE user_id = $id";
                    $result = $mysqli->query($editQuery);
                    if ($result) {
                        $statusMsg = "Contraseña cambiada correctamente.";
                    } else {
                        $errorMsg = "No se pudo cambiar la contraseña.";
                    }
                }
            } else {
                $errorMsg = "La contraseña introducida no es igual a la contraseña actual";
            }
        }

        if (!empty($_FILES['profilePic'])) {
            $tmpFile = $_FILES['profilePic']['tmp_name'];
            $imagePath = 'img/profile/' . $_FILES['profilePic']['name'];
            if (is_uploaded_file($tmpFile)) {
                if (move_uploaded_file($tmpFile, $imagePath)) {
                    $imageSql = "UPDATE usuarios SET user_img = '$imagePath' WHERE $id = user_id";
                    $result = $mysqli->query($imageSql);
                    if ($result) {
                        unlink($userData[0]['user_img']);
                        header("Location: ".$_SERVER["PHP_SELF"]);
                    } else {
                        $errorMsg = 'No se puede subir el archivo';
                    }
                }
            }
        }
        
    }

}

?>
<div id="container">
    <div id="userProfile">
        <img class="profilePic" id="userProfilePic" src="<?php echo $userData[0]['user_img']; ?>" width="150px" alt="profile picture">
        <form id="updateProfile" method="POST" enctype='multipart/form-data'>
            <input type="hidden" name="edit">
            <div class="image-upload">
                <label for="profilePic">
                    <img src="./img/icons/camera.svg" width="35px" height="35px">
                </label>
                <input type="file" id="profilePic" name="profilePic">
            </div>
            <div class="inputBox">
                <img src="./img/icons/user.svg" class="icon" id="userIcon">
                <img src="./img/icons/edit.svg" class="icon editIcon" id="editUser">
                <input class="input" type="text" name="userName" id="userName" placeholder="Nuevo nombre de usuario" value="<?php echo $userData[0]["user_nombre"]; ?>" placeholder="readonly">
            </div>
            <div class="inputBox">
                <img src="./img/icons/lock.svg" class="icon" id="passIcon">
                <img src="./img/icons/edit.svg" class="icon editIcon" id="editPass">
                <input class="input" type="password" name="actualPass" id="actualPass" placeholder="Contraseña actual" readonly>
            </div>
            <div id="message" style="<?php if ($errorMsg || $statusMsg) echo 'display:block'; ?>">
                <?php if ($errorMsg) echo '<p id="errorMsg">Error: ' . $errorMsg . '</p>' ?>
                <?php if (isset($statusMsg)) echo '<p id="statusMsg">' . $statusMsg . '</p>' ?>
            </div>
            <div id="passContainer" style="display:none">
                <div class="inputBox">
                    <img src="./img/icons/lock.svg" class="icon" id="passIcon">
                    <input class="input" type="password" name="newPass" placeholder="Nueva contraseña">
                </div>
                <div class="inputBox">
                    <img src="./img/icons/lock.svg" class="icon" id="passConfirmIcon">
                    <input class="input" type="password" name="newPassConfirm" placeholder="Confirma la contraseña">
                </div>
            </div>
            <div id="buttonContainer">
                <button type="reset" class="button cancel" id="cancelButton">Cancelar</button>
                <button type="submit" class="button">Guardar</button>
            </div>
        </form>
        <form method="POST">
            <button type="button" class="closeThread button" onclick="deleteProfile(event)">Eliminar Perfil</button>
        </form>
    </div>
    <div id="threadContainer">
        <div id="title">
            <img src="./img/icons/question.svg" class="icon" id="questionIcon">
            <h2>Mis hilos</h2>
        </div>
        <div id="userThread">
            <?php if (count($userThreads) > 0) {
                for ($i=0; $i < count($userThreads); $i++) {
                    echo '
                    <div class="thread '.$userThreads[$i]["publi_est"].'" id="'.$userThreads[$i]["publi_id"].'">
                        <a href="hilo.php?id='.$userThreads[$i]["publi_id"].'" class="threadContent"><div>
                        <h3 style="cursor: pointer">'.$userThreads[$i]["publi_titulo"].'</h3>
                        <div class="threadText">
                        '.$userThreads[$i]["publi_descri"].'
                        </div>
                        <p style="display:none">'.$userThreads[$i]["publi_tema"].'</p>
                        </div></a>
                        <form class="threadButtons" method="POST"'.($userThreads[$i]["publi_est"] == "Cerrado" ? "style='display: none'" : "style='display: flex'").'>
                            <button type="button" class="editThread" name="editId" onclick="edit(event);">Editar</button>
                            <button type="submit" class="closeThread" name="closeId" onclick="closePost(event);">Cerrar</button>
                        </form>
                        <img src="./img/icons/closed.svg" class="lockIcon"'.($userThreads[$i]["publi_est"] == "Cerrado" ? "style='display: block'" : "style='display: none'").'>
                    </div>
                    ';
                } 
            } else {
                echo '
                <div id="noThread">
                    <img src="./img/web/man-thinking.svg" alt="man thinking">
                    <p>¡No seas tímido, publica algo!</p>
                </div>
                ';
            };?>
        </div>
    </div>
</div>

<?php
include 'components/footer.php';
?>
<script src="js/profile.js"></script>