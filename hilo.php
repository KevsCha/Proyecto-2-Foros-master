<?php
$title = "foro";
$css = "css/hiloo.css";
$idHilo = $_GET["id"];

include 'components/header.php';

if (isset($signedInError)) {
    header('location: index.php');
}

//Recuperar la publicación original con los datos del usuario
$threadArray = [];
$threadQuery = "SELECT publicaciones.publi_titulo AS postTitle, publicaciones.publi_descri AS postDescription, publicaciones.publi_date AS postDate, publicaciones.publi_tema AS idTema, publicaciones.publi_est AS postStatus, usuarios.user_id AS user_id, usuarios.user_nombre AS userName, usuarios.user_img AS userImg
FROM publicaciones
JOIN usuarios ON usuarios.user_id = publicaciones.publi_user
WHERE publi_id = $idHilo";
try {
    $threadResult = $mysqli->query($threadQuery);
    while ($row = $threadResult->fetch_assoc()) {
        $threadArray[] = $row;
    }
} catch (\Exception $e) {
    die($e->getMessage());
}
// Recuperar los comentarios asociados a la publicación
$commentQuery = "SELECT comentarios.com_id, comentarios.com_coment, comentarios.com_date, usuarios.user_nombre, usuarios.user_img, comentarios.com_publi
FROM comentarios
JOIN usuarios
ON comentarios.com_user = usuarios.user_id
WHERE com_publi = $idHilo";
$commentResult = $mysqli->query($commentQuery);
$commentArray = [];
while ($row = $commentResult->fetch_assoc()) {
    $commentArray[] = $row;
}
//Guardar comentario en base de datos
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['comment'])) {
        $sql = "INSERT INTO comentarios (com_coment, com_user, com_publi) 
        VALUES ('$_POST[comment]', '$id', '$idHilo')";
        try {
            $result = $mysqli->query($sql);
            if (!$result) {
                $error = 'No se ha podido publicar tu comentario';
            } else {
                unset($_POST);
                header('Location: ' . $_SERVER["PHP_SELF"] . '?id=' . $idHilo . '');
                die;
            }
        } catch (Exception $e) {
            $error = "Algo ha salido mal. " . $e->getMessage();
        }
    }
}

?>

<div class="paginaEntera">
    <div class="temasLista">
        <h4>Temas</h4>
        <ul>
            <?php
            for ($i = 0; $i < count($categoryArray); $i++) {
                echo '<a href="categoria.php?id=' . $categoryArray[$i]["tema_id"] . '"' . ($categoryArray[$i]["tema_id"] == $threadArray[0]["idTema"] ? 'class="selected"' : '') . '>
                <li><img src="./img/icons/' . ($categoryArray[$i]['tema_id'] == $threadArray[0]["idTema"] ? 'wavyquestion.svg"' : 'compass.svg"') . 'class="listIcon">' . $categoryArray[$i]["tema_nombre"] . '</li></a>';
            } 
           
            ?>
        </ul>
    </div>

    <div class="listaHilos">
        <div class="hilo">
            <div class="hiloFoto">
                <img src=<?php echo $threadArray[0]["userImg"]; ?> alt="">
                <h5><?php echo $threadArray[0]["userName"]; ?></h5>
            </div>
            <div class="hiloTexto">
                <h3 class="hiloTitulo"><?php echo $threadArray[0]["postTitle"]; ?></h3>
                <h6 class="hiloTime">Fecha de publicación: <?php $dateHilo = strtotime($threadArray[0]["postDate"]);
                                                            setlocale(LC_ALL, "es-ES");
                                                            //$nuevoDateHilo = date("l, d M Y", strftime($dateHilo))

                                                            echo strftime("%a, %d de %B del %Y, %H:%M:%S", $dateHilo); ?></h6>
                <div class="hiloDesc">
                    <?php echo $threadArray[0]["postDescription"]; ?>
                </div>
            </div>
        </div>

        <div class="comentario" <?php echo ($threadArray[0]["postStatus"] == "Cerrado" ? "style='display: none'" : "style='display: flex'"); ?>>
            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="comment">
                <textarea name="comment" id="comment" placeholder="Introduzca aquí su comentario" rows=5 cols=40></textarea>
                <div id="postButtons">
                    <button type="reset" class="button cancel" id="cancelPost" onclick="closeModal()">Cancelar</button>
                    <button type="submit" class="button">Guardar</button>
                </div>
            </form>
        </div>

        <div id="closedPost" <?php echo ($threadArray[0]["postStatus"] == "Cerrado" ? "style='display: block'" : "style='display: none'"); ?>>
            <p>Publicación cerrada por el autor. No admite más respuestas.</p>
        </div>

        <div class="respuestas" <?php echo (empty($commentArray) ? 'style="display:none"' : 'style='); ?>>
            <?php for ($i = 0; $i < count($commentArray); $i++) {
                print('<div class="reply">
                <div class="replyFoto">
                    <img src="' . $commentArray[$i]['user_img'] . '" alt="">
                    <div class="userData">
                    <a href=""><h5>@' . $commentArray[$i]['user_nombre'] . '</h5></a>
                    <h6 class="replyTime">' . $commentArray[$i]['com_date'] . '</h6>
                    </div>
                </div>
                <div class="replyTexto">
                    ' . $commentArray[$i]['com_coment'] . '
                </div>
            </div>');
            } ?>
        </div>
        <div class="emptyComments" <?php echo (empty($commentArray) ? 'style="display:flex"' : 'style="display:none;"'); ?>>
            <img src="img/web/surprise.svg" width="25%">
            <p style="cursor:default">Todavía no hay respusetas, ¡sé el primero en contestar!</u></p>
        </div>
    </div>

</div>
<script>
    tinymce.init({
        selector: 'textarea#comment',
        height: 250,
        plugins: 'code lists',
        mobile: {
            menubar: true,
            plugins: 'autosave lists autolink',
            toolbar: 'undo bold italic styles'
        }
    });
</script>
<?php include 'components/footer.php'; ?>