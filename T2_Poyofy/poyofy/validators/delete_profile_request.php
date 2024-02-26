<?php
    include("database_connection.php");
    require "../components/head.php";

    $username = $_SESSION["userName"];

    $query = "SELECT * FROM personas WHERE username = '.$username'";
    $result = $result_select = mysqli_query($conn, $query);
    $row = mysqli_fetch_array($result_select);


    //Si es usuario, lo borro de tabla usuarios
    if ($row['is_artist'] == 0){
        $sql = "DELETE FROM usuarios WHERE username = ?";
        $stmt = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($stmt, $sql)){
            header("Location: ../community.php?error=sqlerror");
            exit();
        }

        else{
            mysqli_stmt_bind_param($stmt, "s", $username);
            $result = mysqli_stmt_execute($stmt);
        
            if (!$result){
                header("Location: ../community.php?error=delete-user");
                exit();
            }
        }
    }

    //Si es artista, lo borro de tabla artistas
    else{
        $sql = "DELETE FROM artistas WHERE username = ?";
        $stmt = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($stmt, $sql)){
            header("Location: ../community.php?error=sqlerror");
            exit();
        }

        else{
            mysqli_stmt_bind_param($stmt, "s", $username);
            $result = mysqli_stmt_execute($stmt);
        
            if (!$result){
                header("Location: ../community.php?error=delete-artist");
                exit();
            }
        }
    }

    //Si fue borrado de forma exitosa, lo borro en la tabla personas
    $sql = "DELETE FROM personas WHERE username = ?";
    $stmt = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt, $sql)){
        header("Location: ../community.php?error=sqlerror");
        exit();
    }

    else{
        mysqli_stmt_bind_param($stmt, "s", $username);
        $result = mysqli_stmt_execute($stmt);
    
        if ($result){
            session_unset();
            session_destroy();
            header("Location: ../index.php?success=delete-profile");
            exit();
        }
        header("Location: ../community.php?error=sqlerror");
        exit();
    }
    
?>