<?php
    if (isset($_POST['update-playlist'])){
        include("database_connection.php");

        $id_playlist = $_GET['id'];

        $query = "SELECT * FROM playlists WHERE id_playlist = '$id_playlist'";  //SQL INJECTION ALERT
        $result = mysqli_query($conn, $query);
        $row = mysqli_fetch_array($result);

        $old_nombre_playlist = $row['nombre'];
        $new_nombre_playlist = $_POST['name'];
        
        

        if (empty($new_nombre_playlist)) $new_nombre_playlist = $old_nombre_playlist;
        
        $sql = "UPDATE playlists SET nombre_playlist = '$new_nombre_playlist' WHERE playlists.id_playlist = '$id_playlist'";
        $result = mysqli_query($conn, $sql);

        if ($result){
                header("Location: ../playlists.php?success=update-playlist");
                exit();
        }
        header("Location: ../playlists.php?error=sqlerror");
        exit();
    }

    else{
        header("Location: ../playlists.php?error=operacioninvalida");
        exit();
    }
?>