<?php
$title = "foro: Categorías";
$css = "css/index.css";
include 'components/header.php';
?>

<div class="pageContent">
    <h2>¿Qué categoría quieres explorar?</h2>
    <div id="categoryContainer">
    <?php
    for ($i=0; $i < count($categoryArray); $i++) {?>
        <a href="hilo.php?id=<?php echo $categoryArray[$i]["tema_id"]; ?>">
            <div class="category">
                <?php //echo $categoryArray[$i]["tema_img"];?>
                <h3><?php echo $categoryArray[$i]["tema_nombre"];?></h3>
            </div>
        </a>
        <?php
    }?>
    </div>
</div>
<?php if (isset($signedInError)):?>
    <script>openModal();</script>
<?php endif; ?>
<?php
include 'components/footer.php';
?>