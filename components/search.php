<?php
include 'conector.php';
$search = [];
if (isset($_GET["term"])) {
    $searchSQL = "SELECT publicaciones.publi_id, publicaciones.publi_titulo, temas.tema_nombre 
        FROM publicaciones
        JOIN temas ON publicaciones.publi_tema = temas.tema_id
        WHERE publi_titulo LIKE ?";

    if ($stmt = $mysqli->prepare($searchSQL)) {
        $stmt->bind_param("s", $parameter);
        $parameter = '%' . $_GET["term"] . '%';

        if ($stmt->execute()) {

            $searchResult = $stmt->get_result();
            if ($searchResult) {
                while ($row = $searchResult->fetch_assoc()) {
                    $search[] = $row;
                }
            } else {
                 echo "<p>No se encontró ningún tema.</p>";
            }
        } else {
            echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
        }
    }

    $stmt->close();
}

$result = json_encode($search);
print_r($result);
         
//     echo "<p href=".$row["publi_id"].">" . $row["publi_titulo"] . "</p>";
// }
