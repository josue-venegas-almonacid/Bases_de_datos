<?php
    include("database_connection.php");
    require "../components/head.php";

    $username = $_SESSION["userName"];
    $id_cancion = $_GET["id"];

    $query = "SELECT * FROM canciones WHERE id_cancion = '$id_cancion'";
    $result_select = mysqli_query($conn, $query);

    //Si es el creador del album, lo borro
    if ($row = mysqli_fetch_array($result_select)){
        if ($row["autor"] == $username){
            $sql = "DELETE FROM canciones WHERE id_cancion = ?";
            $stmt = mysqli_stmt_init($conn);

            if (!mysqli_stmt_prepare($stmt, $sql)){
                header("Location: ../songs.php?error=sqlerror");
                exit();
            }
        
            else{
                mysqli_stmt_bind_param($stmt, "s", $id_cancion);
                $result = mysqli_stmt_execute($stmt);

                if ($result){
                    header("Location: ../songs.php?success=delete-song");
                    exit();
                }
                header("Location: ../songs.php?error=delete-song");
                exit();
            }
        }
        header("Location: ../songs.php?error=operacioninvalida");
        exit();
    }
    
    header("Location: ../songs.php?error=nosong");
    exit();
    
?>