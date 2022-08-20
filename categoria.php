<?php

$title = "Categorías";
$css = "css/hilo.css";

require 'components/config.php';
include 'components/header.php';

$cod = $_GET['id'];

for ($i = 0; $i < count($categoryArray); $i++) {
    if ($categoryArray[$i]['tema_id'] == $cod) {
        $cat = $categoryArray[$i];
    }
}

$categoryConsulta = "SELECT publicaciones.publi_id, publicaciones.publi_titulo, publicaciones.publi_descri, publicaciones.publi_date, publicaciones.publi_tema, publicaciones.publi_user, usuarios.user_nombre, usuarios.user_img FROM publicaciones JOIN usuarios ON user_id = publi_user WHERE publi_tema = $cod";
$categoryHilos = mysqli_query($connect, $categoryConsulta);
$selecHilos = [];
while ($cont = mysqli_fetch_assoc($categoryHilos)) {
    $selecHilos[] = $cont;
}

?>

<div class="paginaEntera">
    <div class="temasLista">
        <h4>Temas</h4>
        <ul>
            <?php
            for ($i = 0; $i < count($categoryArray); $i++) {
                echo '<a href="categoria.php?id=' . $categoryArray[$i]["tema_id"] . '"' . ($categoryArray[$i]["tema_id"] == $cod ? 'class="selected"' : '') . '>
                <li><img src="./img/icons/' . ($categoryArray[$i]['tema_id'] == $cod ? 'wavyquestion.svg"' : 'compass.svg"') . 'class="listIcon">' . $categoryArray[$i]["tema_nombre"] . '</li></a>';
            } ?>
        </ul>
    </div>

    <div class="listaHilos">
        <h2><?php echo ucwords($cat['tema_nombre']); ?></h2>
        <?php
        for ($i = 0; $i < count($selecHilos); $i++) {
            if ($selecHilos[$i]['publi_tema'] == $cat['tema_id']) {
        ?>
                <a href="hilo.php?id=<?php echo $selecHilos[$i]["publi_id"] ?>">
                    <div class="hilo<?php
                                    if (isset($userData[0]) && strtotime($userData[0]['user_time']) < strtotime($selecHilos[$i]['publi_date'])) {
                                        echo " bord";
                                        $dateTwo['user_time'] = gmdate("d-F-Y H:i:s ", time() + 3600 * (1 + date("I")));
                                        $_SESSION['date'] = $dateTwo;
                                        //es una manera muy obligada de que muestre los mensajes nuevos hay q seguir buscando...
                                    } ?>">


                        <div class="hiloFoto">
                            <img src="<?php echo $selecHilos[$i]['user_img'] ?>" alt="">
                            <h5><?php echo $selecHilos[$i]['user_nombre']; ?></h5>
                        </div>
                        <div class="hiloTexto">
                            <div class="hiloTitulo">
                                <h3> <?php echo ucwords($selecHilos[$i]['publi_titulo']); ?></h3>
                            </div>
                            <div class="hiloTime">

                                <h6><?php $dateHilo = strtotime($selecHilos[$i]['publi_date']);
                                    setlocale(LC_ALL, "es-ES");
                                    //$nuevoDateHilo = date("l, d M Y", strftime($dateHilo))

                                    echo strftime("%a, %d de %B del %Y, %H:%M:%S", $dateHilo);
                                    ?></h6>
                            </div>
                            <div class="hiloDesc">
                                <p><?php echo $selecHilos[$i]['publi_descri']; ?></p>
                            </div>
                        </div>
                    </div>
                </a>
        <?php }
        } ?>

        <div class="respuestas" <?php echo (empty($selecHilos) ? 'style="display:flex; flex-direction:column; align-items:center"' : 'style="display:none;"'); ?>>
            <img src="img/web/nodata.svg" width="40%">
            <p onclick=openModal() style="cursor:default" ;>Todavía no hay ningún mensaje, <u style="cursor:pointer">¡Escribe algo!</u></p>
        </div>
    </div>
</div>
<?php include 'components/footer.php'; ?>