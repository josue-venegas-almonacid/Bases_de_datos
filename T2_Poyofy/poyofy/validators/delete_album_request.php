<?php
    include("database_connection.php");
    require "../components/head.php";

    $username = $_SESSION["userName"];
    $id_album = $_GET["id"];

    $query = "SELECT * FROM albums WHERE id_album = '$id_album'";
    $result_select = mysqli_query($conn, $query);

    //Si es el creador del album, lo borro
    if ($row = mysqli_fetch_array($result_select)){
        if ($row["autor"] == $username){
            $sql = "DELETE FROM albums WHERE id_album = ?";
            $stmt = mysqli_stmt_init($conn);

            if (!mysqli_stmt_prepare($stmt, $sql)){
                header("Location: ../albums.php?error=sqlerror");
                exit();
            }
        
            else{
                mysqli_stmt_bind_param($stmt, "s", $id_album);
                $result = mysqli_stmt_execute($stmt);

                if ($result){
                    header("Location: ../albums.php?success=delete-album");
                    exit();
                }
                header("Location: ../albums.php?error=sqlerror");
                exit();
            }
        }
        header("Location: ../albums.php?error=operacioninvalida");
        exit();
    }
    
    header("Location: ../albums.php?error=noalbum");
    exit();
    
?>