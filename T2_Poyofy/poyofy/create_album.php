<!doctype html>
<html lang="en">

<head>
  <?php
    require "components/head.php";
    //En caso de no haber una sesión iniciada, no puedo entrar en esta página
    if (!isset($_SESSION["userName"])){
      header("Location: index.php");
      exit();
    }
    //En caso de no ser artista, no puedo entrar en esta página
    include("validators/database_connection.php");
    $username = $_SESSION["userName"];
    $sql = "SELECT * FROM artistas WHERE username = '$username'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_array($result);

    if (!$row){
      header("Location: index.php?error=operacioninvalida");
      exit();
    }
  ?>
</head>

<!--En caso de haber una sesión iniciada, puedo entrar en esta página-->
<body>
  <?php
    require "components/body.php"; 

  echo'<section id="content">
    <h2>Crear álbum</h2>
    <div class="card card-body">
      <form action="validators/create_album_request.php" method="POST">
        <div class="form-group"><p><strong>Nombre del álbum: </strong><input type="text" class="form-control" name="nombre_album" placeholder="Nombre del álbum"></p></div>
        <div class="form-group"><p><strong>Año de publicación: </strong><input type="text" class="form-control" name="fecha" placeholder="Año de publicación"></p></div>
        <div class="form-group"><p><strong>ID de canción: </strong><input type="text" class="form-control" name="id_cancion" placeholder="ID de la canción"></p></div>
        <button class="btn btn-success" name="create-album">Crear</button>
      </form>
    </div>
    
    <div class="my_card">
        <p class="p_index"><a href="albums.php"><button type="submit" name="back">Volver</button></a></p>
      </div>

      
      <h2>Mis canciones publicadas</h2>
      <table class="table table-bordered">
          <thead>
              <tr>
                  <th>ID</th>
                  <th>Nombre</th>
                  <th>Género</th>
                  <th>Duración</th>
                  <th>Año de publicación</th>
                  <th># Likes</th>
              </tr>
          </thead>
          <tbody>'; 
              $query = "SELECT * FROM canciones WHERE autor = '$username'";
              $result_select = mysqli_query($conn, $query);
              
              while($row = mysqli_fetch_array($result_select)){
                  echo'
                  <tr>
                      <td>'.$row["id_cancion"].'</td>
                      <td><a href="song.php?id='.$row["id_cancion"].'">'.$row["nombre_cancion"].'</a></td>
                      <td>'.$row["genero"].'</td>
                      <td>'.$row["duracion"].'</td>
                      <td>'.$row["anno_publicacion"].'</td>
                      <td>'.$row["cantidad_likes"].'</td>';
                  echo'
                  </tr>
                  '; 
              }
          echo'</tbody>
      </table>
  </section>';

    require "components/footer.php";
  ?>
</body>
