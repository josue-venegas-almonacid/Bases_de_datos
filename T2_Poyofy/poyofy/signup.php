<!--
  Archivo: signup.php
  Función: Mostrar la página de registro
  Accesible si está:
    No logeado: Sí
    Logeado: No
  Visible para: No Personas
-->

<!doctype html>
<html lang="en">

<head>
  <?php
    require "components/head.php";
    //En caso de haber una sesión iniciada, no puedo entrar en esta página
    if (isset($_SESSION["userName"])){
      header("Location: index.php");
      exit();
    }
  ?>
</head>

<!--En caso de no haber una sesión iniciada, puedo entrar en esta página-->
<body>
  <?php
    require "components/body.php";
  ?>

  <section id="content">
    <h2>Registrarse</h2>

    <div class="card card-body">
    <form action="validators/signup_request.php" method="POST">
      <p class="p_index">Eres:</p>
      <div class="form-check">
        <p class="p_index"><input class="form-check-input" type="radio" name="user_or_artist" id="first_option" value="is_user" checked>
          <label class="form-check-label" for="first_option">
              Usuario
          </label>
      </div></p>
      <div class="form-check">
        <p class="p_index"><input class="form-check-input" type="radio" name="user_or_artist" id="second_option" value="is_artist">
          <label class="form-check-label" for="first_option">
              Artista
          </label>
      </div></p>
      
      <div class="form-group"><p><strong>Nombre completo: </strong><input type="text" class="form-control" name="name" placeholder="Nombre"></p></div>
      <div class="form-group"><p><strong>Nombre de usuario: </strong><input type="text" class="form-control" name="username" placeholder="Nombre de usuario"></p></div>
      <div class="form-group"><p><strong>Correo: </strong><input type="text" class="form-control" name="email" placeholder="Correo"></p></div>
      <div class="form-group"><p><strong>Contraseña: </strong><input type="password" class="form-control" name="password" placeholder="Contraseña"></p></div>
      <p class="p_index"><button type="submit" name="signup-submit">Registrarme</button>
    </form>
    </div>

    <div class="my_card">
      <p class="p_index"><a href="index.php"><button type="submit" name="back">Volver</button></a></p>
    </div>
    
  </section>

  <?php
    require "components/footer.php";
  ?>
</body>
