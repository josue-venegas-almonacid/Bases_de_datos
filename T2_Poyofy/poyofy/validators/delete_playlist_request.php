<?php
    include("database_connection.php");
    require "../components/head.php";

    $username = $_SESSION["userName"];
    $id_playlist = $_GET["playlist"];

    $query = "SELECT * FROM playlists WHERE id_playlist = '$id_playlist'";
    $result_select = mysqli_query($conn, $query);

    //Si es el creador del album, lo borro
    if ($row = mysqli_fetch_array($result_select)){
        if ($row["creador"] == $username){
            $sql = "DELETE FROM playlists WHERE id_playlist = ?";
            $stmt = mysqli_stmt_init($conn);

            if (!mysqli_stmt_prepare($stmt, $sql)){
                header("Location: ../playlists.php?error=sqlerror");
                exit();
            }
        
            else{
                mysqli_stmt_bind_param($stmt, "s", $id_playlist);
                $result = mysqli_stmt_execute($stmt);

                if ($result){
                    header("Location: ../playlists.php?success=delete-playlist");
                    exit();
                }
                header("Location: ../playlists.php?error=sqlerror");
                exit();
            }
        }
    }
    
    header("Location: ../playlists.php?error=operacioninvalida");
    exit();
    
?>