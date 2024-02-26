<?php

    if (isset($_POST['update-album'])){
        include("database_connection.php");

        $id_album = $_GET['id'];

        $query = "SELECT * FROM albums WHERE id_album = '$id_album'";  //SQL INJECTION ALERT
        $result = mysqli_query($conn, $query);
        $row = mysqli_fetch_array($result);

        $old_nombre_album = $row['nombre'];
        $old_anno_publicacion = $row['anno_publicacion'];
        $new_nombre_album = $_POST['name'];
        $new_anno_publicacion = $_POST['anno_publicacion'];
        
        if (empty($new_nombre_album)) $new_nombre_album = $old_nombre_album;
        if (empty($new_anno_publicacion)) $new_anno_publicacion = $old_anno_publicacion;
        
        $sql = "UPDATE albums SET nombre_album = '$new_nombre_album', anno_publicacion = '$new_anno_publicacion' WHERE albums.id_album = '$id_album'";
        $result = mysqli_query($conn, $sql);

        if ($result){
                header("Location: ../albums.php?success=update-playlist");
                exit();
        }
        header("Location: ../albums.php?error=sqlerror");
        exit();
    }

    else{
        header("Location: ../albums.php?error=operacioninvalida");
        exit();
    }
?>