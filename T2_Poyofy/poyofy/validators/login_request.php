<!--
  Archivo: login_request.php
  Función: Validar el inicio de sesión del usuario
-->

<?php

  //Si el usuario apretó el botón para registrarse se ejecuta el proceso
  if (isset($_POST['login-submit'])){
    require "database_connection.php";

    
    $username = $_POST['username'];
    $password = $_POST['password'];

    //Si hay un campo vacío
    if ( empty($username) || empty($password) ){
      header("Location: ../index.php?error=emptyfields");
      exit();
    }

    //En caso contrario, compruebo que los datos sean correctos
    else{
      $sql = "SELECT * FROM personas WHERE username=?";
      $stmt = mysqli_stmt_init($conn);

      if (!mysqli_stmt_prepare($stmt, $sql)){
        header("Location: ../index.php?error=sqlerror");
        exit();
      }

      else{
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)){
          $pwdCheck = password_verify($password, $row["contrasena"]);

          if ($pwdCheck == false){
            header("Location: ../index.php?error=wrongpassword");
            exit();
          }
          else if ($pwdCheck == true){
            session_start();
            $_SESSION["userName"] = $row["username"];
            header("Location: ../index.php?success=login");
            exit();
          }

        }
        else{
          header("Location: ../index.php?error=nouser");
          exit();
        }
      }
    }
  }

  //Si el usuario no apretó el botón para iniciar sesión, no puede entrar en esta página
  else{
    header("Location: ../index.php");
    exit();
  }
?>
