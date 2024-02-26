



<!DOCTYPE html>

<html>
    <head>
    <?php
        require "components/head.php";
        //En caso de no haber una sesión iniciada, no puedo entrar en esta página
        if (!isset($_SESSION["userName"])){
            header("Location: index.php");
            exit();
        }
    ?>
    </head>

    <body>
        <?php
            require "components/body.php";
            include("validators/database_connection.php");
            include("validators/is_following.php");
        ?>

        <section id="content">
            <h2>Álbum</h2>
            <?php
                if (isset($_GET['id'])){
                    $id_album = $_GET['id'];

                    $query = "SELECT * FROM albums WHERE id_album = '$id_album'";  //SQL INJECTION ALERT
                    $result = mysqli_query($conn, $query);
                    $row = mysqli_fetch_array($result);

                    //Si no existe album, retorno a la página principal
                    if (!$row){
                        header("Location: albums.php?error=noalbum");
                        exit();
                    }

                    $nombre_album = $row['nombre_album'];
                    $autor = $row['autor'];
                    $canciones = $row['canciones'];
                    $anno_publicacion = $row['anno_publicacion'];
                    $username = $_SESSION["userName"];

                    //Si yo soy el creador, puedo editar los campos del album
                    if ($autor == $username){
                        echo'
                        <div class="card card-body">
                            <form action="validators/update_album_request.php?id='.$id_album.'" method="POST">
                                <div class="form-group"><p><strong>ID del álbum: </strong>'.$id_album.'</p></div>
                                <div class="form-group"><p><strong>Nombre del álbum: </strong><input type="text" class="form-control" name="name" value="'.$nombre_album.'" placeholder="Nombre del álbum"></p></div>
                                <div class="form-group"><p><strong>Autor: </strong><a href="profile.php?username='.$autor.'">'.$autor.'</a></p></div>
                                <div class="form-group"><p><strong># Canciones: </strong>'.$canciones.'</p></div>
                                <div class="form-group"><p><strong>Año de publicación: </strong><input type="text" class="form-control" name="anno_publicacion" value="'.$anno_publicacion.'" placeholder="Año de publicación"></p></div>
                                <button class="btn btn-success" name="update-album">Actualizar</button>
                            </form>
                        </div>

                        <div class="my_card">
                            <p class="p_index"><a href="validators/delete_album_request.php?id='.$id_album.'"><button type="submit" name="delete">Eliminar álbum</button></a></p>
                        </div>
                        ';  
                    }


                    //En caso contrario, sólo veo los datos
                    else{
                        echo'
                        <div class="card card-body">
                            <div class="form-group"><p><strong>ID del álbum: </strong>'.$id_album.'</p></div>
                            <div class="form-group"><p><strong>Nombre del álbum: </strong>'.$nombre_album.'</p></div>
                            <div class="form-group"><p><strong>Autor: </strong><a href="profile.php?username='.$autor.'">'.$autor.'</a></p></div>
                            <div class="form-group"><p><strong># Canciones: </strong>'.$canciones.'</p></div>
                            <div class="form-group"><p><strong>Año de publicación: </strong>'.$anno_publicacion.'</p></div>';
                        echo'</div>';
                    }

                    //Muestro las canciones. Si soy creador puedo añadir canciones
                    if ($autor == $username){
                        echo'<h2>Canciones <a class="btn btn-primary" href="add_song_to_album.php?id_album='.$id_album.'"><i class="fas fa-plus-square"></i></a></h2>';
                    }
                    else{
                        echo'<h2>Canciones</h2>';
                    }

                    $username = $_SESSION['userName'];
                    $query2 = "SELECT * FROM artistas WHERE username = '$username'";  //SQL INJECTION ALERT
                    $result2 = mysqli_query($conn, $query2);
                    $row2 = mysqli_fetch_array($result2);

                    //Si soy artista
                    if ($row2){
                        echo'
                            <div class="my_card">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th>Autor</th>
                                            <th>Género</th>
                                            <th>Duración</th>
                                            <th>Año de publicación</th>
                                            <th>Cantidad de likes</th>';
                                            if ($autor == $username){                                    
                                            echo'<th>Acciones</th>';}
                                        echo'</tr>
                                    </thead>
                                    <tbody>';
                                        $query = "SELECT C.* FROM canciones C JOIN albums_incluyendo_canciones P ON C.id_cancion = P.id_cancion AND P.id_album = '.$id_album.'";
                                        $result_select = mysqli_query($conn, $query);
                                        
                                        while($row = mysqli_fetch_array($result_select)){
                                            $id_cancion = $row["id_cancion"];
                                            echo'
                                            <tr>
                                                <td>'.$row["id_cancion"].'</td>
                                                <td><a href="song.php?id='.$row["id_cancion"].'">'.$row["nombre_cancion"].'</td>
                                                <td><a href="profile.php?username='.$row["autor"].'">'.$row["autor"].'</td>
                                                <td>'.$row["genero"].'</td>
                                                <td>'.$row["duracion"].'</td>
                                                <td>'.$row["anno_publicacion"].'</td>
                                                <td>'.$row["cantidad_likes"].'</td>';
                                                
                                                //Si soy el creador (luego soy artista) puedo quitar la canción del album
                                                if ($autor == $username){
                                                    echo'<td><a class="btn btn-primary" href="validators/remove_song_from_album_request.php?id_cancion='.$id_cancion.'&id_album='.$id_album.'"><i class="fas fa-trash-alt"></i></a>';
                                                }
                                            echo'
                                            </tr>';
                                        }
                                    echo'</tbody>
                                </table>
                            </div>
                        ';
                    }

                    //Si soy usuario
                    else{
                        echo'
                            <div class="my_card">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th>Autor</th>
                                            <th>Género</th>
                                            <th>Duración</th>
                                            <th>Año de publicación</th>
                                            <th>Cantidad de likes</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>';
                                        $query = "SELECT C.* FROM canciones C JOIN albums_incluyendo_canciones P ON C.id_cancion = P.id_cancion AND P.id_album = '.$id_album.'";
                                        $result_select = mysqli_query($conn, $query);
                                    
                                        while($row = mysqli_fetch_array($result_select)){
                                            $id_cancion = $row["id_cancion"];
                                            echo'
                                            <tr>
                                                <td>'.$row["id_cancion"].'</td>
                                                <td><a href="song.php?id='.$row["id_cancion"].'">'.$row["nombre_cancion"].'</td>
                                                <td><a href="profile.php?username='.$row["autor"].'">'.$row["autor"].'</td>
                                                <td>'.$row["genero"].'</td>
                                                <td>'.$row["duracion"].'</td>
                                                <td>'.$row["anno_publicacion"].'</td>
                                                <td>'.$row["cantidad_likes"].'</td>';

                                                //Si soy usuario puedo darle like
                                                if (!is_liking_song($row["id_cancion"], $username)){
                                                    echo'<td><a class="btn btn-primary" href="validators/like_request.php?song='.$row["id_cancion"].'"><i class="far fa-thumbs-up"></i></a></td>';
                                                }
                                                else{
                                                    echo'<td><a class="btn btn-primary" href="validators/unlike_request.php?song='.$row["id_cancion"].'"><i class="fas fa-thumbs-up"></i></a></td>';
                                                }

                                            echo'
                                            </tr>';
                                            
                                        }
                                    echo'</tbody>
                                </table>
                            </div>
                        ';

                    }
                }

                else{
                    header("Location: albums.php?error=operacioninvalida");
                }
            ?>
            
        </section>

        <?php
            require "components/footer.php";
        ?>
    </body>
</html>