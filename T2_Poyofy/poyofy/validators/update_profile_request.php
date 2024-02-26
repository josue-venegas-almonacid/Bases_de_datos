<?php
    //Zona de actualización
    if (isset($_POST['update-profile'])){
        include("database_connection.php");
        $username = $_GET['username'];

        $query = "SELECT * FROM personas WHERE username = '$username'";  //SQL INJECTION ALERT
        $result = mysqli_query($conn, $query);
        $row = mysqli_fetch_array($result);

        $current_name = $row['nombre'];
        $current_email = $row['email'];
        $current_password = $row['contrasena'];

        $new_name = $_POST['name'];
        $new_email = $_POST['email'];
        $new_password = $_POST['new_password'];
        

        if (empty($new_name)) $new_name = $current_name;
        if (empty($new_email)) $new_email = $current_email;
        if (empty($new_password)){
            $new_password = $current_password;
        }
        else{
            $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        }        

        $sql = "UPDATE personas SET nombre = '$new_name', email = '$new_email', contrasena = '$new_password' WHERE personas.username = '$username'";
        $result = mysqli_query($conn, $sql);

        if ($result){
            if ($row['is_artist'] == 0){
                $query = "SELECT * FROM usuarios WHERE username = '$username'";  //SQL INJECTION ALERT
                $result = mysqli_query($conn, $query);
                $row = mysqli_fetch_array($result);

                $current_genero_favorito = $row['genero_favorito'];
                $new_genero_favorito = $_POST['genero_favorito'];

                if (empty($new_genero_favorito)) $new_genero_favorito = $current_genero_favorito;
                $sql = "UPDATE usuarios SET genero_favorito = '$new_genero_favorito' WHERE usuarios.username = '$username'";
                $result = mysqli_query($conn, $sql);

                if ($result){
                    header("Location: ../profile.php?success=update-profile");
                    exit();
                }
                header("Location: ../profile.php?error=sqlerror");
                exit();
            }
        }
        header("Location: ../profile.php?error=sqlerror");
        exit();
    }

    else{
        header("Location: ../profile.php?error=operacioninvalida");
        exit();
    }
?>