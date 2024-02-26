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
    //Si no es artista no puedo entrar en esta página
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

    $id_album = "";
    $id_cancion = "";
  
    if (isset($_GET["id_album"])) $id_album = $_GET["id_album"];
    if (isset($_GET["id_cancion"])) $id_cancion = $_GET["id_cancion"];

    echo'<section id="content">
      <h2>Agregar canción a un álbum</h2>
      <div class="card card-body">
        <form action="validators/add_song_to_album_request.php" method="POST">
          <div class="form-group"><p><strong>ID del álbum: </strong><input type="text" class="form-control" name="id_album" value="'.$id_album.'" placeholder="ID del álbum"></p></div>
          <div class="form-group"><p><strong>ID de la canción: </strong><input type="text" class="form-control" name="id_cancion" value="'.$id_cancion.'" placeholder="ID de la canción"></p></div>
          <button class="btn btn-success" name="add-song-to-album">Agregar</button>
        </form>
      </div>
      
      <div class="my_card">
        <p class="p_index"><a href="songs.php"><button type="submit" name="back">Volver</button></a></p>
      </div>';

      if (isset($_GET["id_album"])){
      //Muestro mis canciones
      echo'
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
                  <th>Acciones</th>
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

                      //Si soy el creador puedo editar la canción                                        
                      echo'<td><a class="btn btn-primary" href="add_song_to_album.php?id_cancion='.$row["id_cancion"].'&id_album='.$id_album.'"><i class="fas fa-plus-square"></a></td>';
                  echo'
                  </tr>
                  '; 
              }
          echo'</tbody>
      </table>';}

      if (isset($_GET["id_cancion"])){
      //Muestro mis albums
      echo'
      <h2>Mis álbums publicados</h2>
      <table class="table table-bordered">
          <thead>
              <tr>
                  <th>ID</th>
                  <th>Nombre</th>
                  <th># Canciones</th>
                  <th>Año de publicación</th>
                  <th>Acciones</th>
              </tr>
          </thead>
          <tbody>'; 
              $query = "SELECT * FROM albums WHERE autor = '$username'";
              $result_select = mysqli_query($conn, $query);
              
              while($row = mysqli_fetch_array($result_select)){
                  echo'
                  <tr>
                      <td>'.$row["id_album"].'</td>
                      <td><a href="album.php?id='.$row["id_album"].'">'.$row["nombre_album"].'</a></td>
                      <td>'.$row["canciones"].'</td>
                      <td>'.$row["anno_publicacion"].'</td>';

                      //Si soy el creador puedo editar el album
                      echo'<td><a class="btn btn-primary" href="add_song_to_album.php?id_album='.$row["id_album"].'&id_cancion='.$id_cancion.'"><i class="fas fa-plus-square"></a></td>';
                  echo'
                  </tr>
                  '; 
              }
          echo'</tbody>
      </table>';}
    echo'</section>';


    require "components/footer.php";
  ?>
</body>
