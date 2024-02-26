<?php
    if (isset($_POST['update-song'])){
        include("database_connection.php");
        $id_cancion = $_GET['id_cancion'];

        $query = "SELECT * FROM canciones WHERE id_cancion = $id_cancion";  //SQL INJECTION ALERT
        $result = mysqli_query($conn, $query);
        $row = mysqli_fetch_array($result);

        $old_nombre_cancion = $row["nombre_cancion"];
        $old_genero = $row["genero"];
        $old_duracion = $row["duracion"];
        $old_anno_publicacion = $row["anno_publicacion"];

        $new_nombre_cancion = $_POST["nombre_cancion"];
        $new_genero = $_POST["genero"];
        $new_duracion = $_POST["duracion"];
        $new_anno_publicacion = $_POST["anno_publicacion"];

        if (empty($new_nombre_cancion)) $new_nombre_cancion = $old_nombre_cancion;
        if (empty($new_genero)) $new_genero = $old_genero;
        if (empty($new_duracion)) $new_duracion = $old_duracion;
        if (empty($new_anno_publicacion)) $new_anno_publicacion = $old_anno_publicacion;

        $sql = "UPDATE canciones SET nombre_cancion = ?, genero = ?, duracion = ?, anno_publicacion = ? WHERE id_cancion = ?";
        $stmt = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($stmt, $sql)){
            header("Location: ../songs.php?error=sqlerror");
            exit();
        }

        else{
            mysqli_stmt_bind_param($stmt, "sssss", $new_nombre_cancion, $new_genero, $new_duracion, $new_anno_publicacion, $id_cancion);
            $result = mysqli_stmt_execute($stmt);

            if ($result){
                header("Location: ../songs.php?success=update-song");
                exit();
            }
        header("Location: ../songs.php?error=sqlerror");
        exit();
        }
    }

    else{
        header("Location: ../songs.php?error=operacioninvalida");
        exit();
    }
?>