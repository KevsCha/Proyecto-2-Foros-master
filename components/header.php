<?php
session_start();
$id = $_SESSION['id'] ?? null; // Variable inventada, a user_id cuando haya usuario con sesión iniciada
include 'components/conector.php';

// Obtiene los datos del usuario que inicia sesión
$userData = [];
if (isset($id)) {
  try {
    $editQuery = "SELECT * FROM usuarios WHERE $id = user_id";
    $editResult = $mysqli->query($editQuery);
    while ($row = $editResult->fetch_assoc()) {
      $userData[] = $row;
    }
  } catch (Exception $e) {
    echo "Error: " . $e->getMessage();
  }
}else{
  $userData[0]['user_img']="https://upload.wikimedia.org/wikipedia/commons/8/89/Portrait_Placeholder.png";
}

// Obtiene las categorías para el select de nuevo post y para la página de temas
$categoryArray = [];
try {
  $categoryQuery = "SELECT tema_id, tema_nombre, tema_img FROM temas ORDER BY tema_nombre ASC";
  $categoryResult = $mysqli->query($categoryQuery);
  while ($row = $categoryResult->fetch_assoc()) {
    $categoryArray[] = $row;
  }
} catch (Exception $e) {
  echo $e->getMessage();
}
// Publicar nuevo post y guardarlo en la base de datos
$error = "";

if (isset($_SESSION['signed_in']) == false) {
  $signedInError = true;
} else {
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['newPost'])) {
      $title = $_POST['postTitle'];
      $description = $_POST['postDescription'] ?? "";
      $category = $_POST['postCategory'];
      $sql = "INSERT INTO publicaciones (publi_titulo, publi_descri, publi_date, publi_tema, publi_user) 
        VALUES ('$title', '$description', now(), '$category','$id')";
      try {
        $result = $mysqli->query($sql);
        if (!$result) {
          $error = 'Algo no ha ido bien, por favor inténtalo de nuevo más tarde.';
        } else {
          $newId = $mysqli->insert_id;
          header('Location: hilo.php?id=' . $newId);
        }
      } catch (Exception $e) {
        $error = "Algo ha salido mal. " . $e->getMessage();
      }
    }
    if (isset($_POST['editPost'])) {
      $title = $_POST['postTitle'];
      $description = $_POST['postDescription'] ?? "";
      $postId = $_POST['editPost'];
      $sql = "UPDATE publicaciones SET publi_titulo = '$title', publi_descri = '$description' WHERE publi_id = $postId";
      try {
        $result = $mysqli->query($sql);
        if (!$result) {
          $error = 'Algo no ha ido bien, por favor inténtalo de nuevo más tarde.';
        } else {
          header('Location: hilo.php?id=' . $postId);
        }
      } catch (\Exception $e) {
        echo $e->getMessage();
      }
    }

    if (isset($_POST['closeId'])) {
      $closeSQL = "UPDATE publicaciones SET publi_est = 'Cerrado' WHERE publi_id = $_POST[closeId]";
      try {
        $result = $mysqli->query($closeSQL);
        if ($result) {
          header('Location: ' . $_SERVER['PHP_SELF']);
        }
      } catch (\Exception $e) {
        echo $e->getMessage();
      }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo $title; ?></title>
  <link rel="stylesheet" href="css/header.css">
  <link rel="stylesheet" href="css/footer.css">
  <link rel="stylesheet" href=<?php echo $css ?>>
  <script src="https://cdn.tiny.cloud/1/wyr78gq4bxmh08sv63gfilc0rvzydpgjc3knjw3k6t1xpcev/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
</head>

<body>
  <header>
    <div class="header" id="logoContainer">
      <a href="index.php" class="header_logo">
        <img src="img/icons/logo.svg" alt="foro">
        <h1>foro</h1>
      </a>
    </div>
    <?php if (str_contains('css/loginn.css', $css)) : ?>
      <div class="header loginButtons">
        <button id="is" class="button is">
          Iniciar Sesion
        </button>
        <button id="reg" class="button">
          Registrarse
        </button>
      </div>
    <?php else : ?>
      <div class="header perfil_hilo">
        <div class="header_search">
          <img src="img/icons/search.svg" class="icon" id="searchIcon">
          <input type="text" class="input" autocomplete="off" id="searchBox" placeholder="Buscar Temas...">
          <div class="result" id="searchResultContainer">
          </div>
          <button id="searchButton" class="button">search</button>
        </div>
        <div class="nuevoHilo" onclick="openModal(event)">
          <img src="img/icons/post.svg" class="icon" id="postIcon">
          <button class="button">Nuevo Hilo</button>
        </div>
       
      <?php
        if (!isset($signedInError)) {?>
         <div id="profileMenuIcon" onclick="showMenu()">
            <img class="headerProfile" src="<?php echo $userData[0]["user_img"] ?? "" ;?>" alt="" srcset="">
            <img src="img/icons/down.svg" class="icon" id="profileMore">
            <div id="profileMenu">
              <a href="profile.php?=<?php echo $id ;?>">
                <img src="img/icons/user-edit.svg" class="icon" id="profileIcon">
                Mi Perfil
              </a>
              <span class="separator"></span>
              <a href="logout.php">
                <img src="img/icons/logout.svg" class="icon" id="logoutIcon">
                Cerrar sesión
              </a>
            </div>
         </div> 
          <?php
        }?>
      <?php endif; ?>
      </div>
      <script src="js/header.js"></script>
  </header>
  <main id="main">

    <div class="modal" id="newPostModal">
      <div id="newPost" <?php if (isset($signedInError)) echo 'style="display:none"'; ?>>
        <img src="img/icons/x.svg" class="icon" id="xIcon" onclick="closeModal()" alt="Cerrar">
        <form id="postForm" method="POST" enctype='multipart/form-data'>
          <input type="hidden" name="newPost" id="newPostInput">
          <input type="text" class="input" name="postTitle" id="postTitle" placeholder="Título" required>
          <select name="postCategory" id="postCategory" required>
            <option selected="true" disabled="disabled">Selecciona un tema</option>
            <?php
            for ($i = 0; $i < count($categoryArray); $i++) {
              echo '<option value="' . $categoryArray[$i]["tema_id"] . '">' . $categoryArray[$i]["tema_nombre"] . '</option>"';
            } ?>
          </select>
          <textarea name="postDescription" id="description"></textarea>

          <div id="postButtons">
            <button type="reset" class="button cancel" id="cancelPost" onclick="closeModal()">Cancelar</button>
            <button type="submit" class="button" id="publishButton">Publicar</button>
          </div>

          <script>
            tinymce.init({
              selector: 'textarea#description',
              height: 350,
              plugins: 'code lists',
              mobile: {
                menubar: true,
                plugins: 'autosave lists autolink',
                toolbar: 'undo bold italic styles'
              }
            });
          </script>
        </form>
      </div>
      <div id="modalError" <?php if (isset($signedInError)) echo 'style="display:block"'; ?>>
        <img src="img/icons/x.svg" class="icon" onclick="closeModal()" alt="Cerrar">
        <img src="img/web/signup.svg" id="errorImg">
        <?php if (isset($signedInError)) echo '<p>¡Únete a nosotros! Para publicar <a href="login.php">regístrate</a> o <a href="login.php">inicia sesión</a>.</p>' ?>
      </div>
    </div>