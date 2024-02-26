<!--
  Archivo: signup_request.php
  Función: Validar el registro de usuario
-->

<?php
//Si el usuario apretó el botón para registrarse se ejecuta el proceso
if (isset($_POST['signup-submit'])){
  require "database_connection.php";

  $username = $_POST['username'];
  $name = $_POST['name'];
  $email = $_POST['email'];
  $password = $_POST['password'];
  $user_or_artist = ($_POST['user_or_artist']);

  //Si hay campos vacíos
  if ( empty($username) || empty($name) || empty($email) || empty($password) ){
    header("Location: ../signup.php?error=emptyfields");
    exit();
  }

  //Si todo esta correcto, compruebo que el usuario no exista
  else{
    $sql = "SELECT * FROM personas WHERE username=?";
    $stmt = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt, $sql)){
      header("Location: ../signup.php?error=sqlerror");
      exit();
    }

    else{
      mysqli_stmt_bind_param($stmt, "s", $username);
      mysqli_stmt_execute($stmt);
      mysqli_stmt_store_result($stmt);
      $resultCheck = mysqli_stmt_num_rows($stmt);

      //Si el usuario existe, entrega un error
      if ($resultCheck>0){
        header("Location: ../signup.php?error=usernamealreadyexists");
        exit();
      }

      //Si no existe, se registra
      else{
        $sql = "INSERT INTO personas(username, nombre, email, contrasena) VALUES (?,?,?,?)";
        $stmt = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($stmt, $sql)){
          header("Location: ../signup.php?error=sqlerror");
          exit();
        }

        else{
          $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
          mysqli_stmt_bind_param($stmt, "ssss", $username, $name, $email, $hashedPassword);
          mysqli_stmt_execute($stmt);

          //En caso de ser usuario, lo agrega a la tabla usuarios
          if ($user_or_artist == 'is_user'){
            $sql = "INSERT INTO usuarios(username) VALUES (?)";
          }

          //En caso de ser artista, lo agrega a la tabla artistas
          else{
            $sql = "INSERT INTO artistas(username) VALUES (?)";
          }

          $stmt = mysqli_stmt_init($conn);

          if (!mysqli_stmt_prepare($stmt, $sql)){
            header("Location: ../signup.php?error=sqlerror");
            exit();
          }

          else{
            mysqli_stmt_bind_param($stmt, "s", $username);
            $result = mysqli_stmt_execute($stmt);

            if ($result){
              header("Location: ../signup.php?success=signup");
              exit();
            }
            header("Location: ../signup.php?error=sqlerror");
            exit();
          }
        }
      }
    }
  }
  mysqli_stmt_close($stmt);
  mysqli_close($conn);
}

//Si el usuario no apretó el botón para registrarse, no puede entrar en esta página
else{
  header("Location: ../signup.php");
  exit();
}
