



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
            <h2>Playlist</h2>
            <?php
                if (isset($_GET['id'])){
                    $id_playlist = $_GET['id'];

                    $query = "SELECT * FROM playlists WHERE id_playlist = '$id_playlist'";  //SQL INJECTION ALERT
                    $result = mysqli_query($conn, $query);
                    $row = mysqli_fetch_array($result);

                    //Si no existe playlist, retorno a la página principal
                    if (!$row){
                        header("Location: playlists.php?error=noplaylist");
                        exit();
                    }

                    $nombre_playlist = $row['nombre_playlist'];
                    $creador = $row['creador'];
                    $canciones = $row['canciones'];
                    $seguidores = $row['seguidores'];
                    $username = $_SESSION["userName"];

                    //Si yo soy el creador, puedo editar los campos de la playlist
                    if ($creador == $username){
                        echo'
                        <div class="card card-body">
                            <form action="validators/update_playlist_request.php?id='.$id_playlist.'" method="POST">
                                <div class="form-group"><p><strong>ID de la playlist: </strong>'.$id_playlist.'</p></div>
                                <div class="form-group"><p><strong>Nombre de la playlist: </strong><input type="text" class="form-control" name="name" value="'.$nombre_playlist.'" placeholder="Nombre de la playlist"></p></div>
                                <div class="form-group"><p><strong>Creador: </strong><a href="profile.php?username='.$creador.'">'.$creador.'</a></p></div>
                                <div class="form-group"><p><strong># Canciones: </strong>'.$canciones.'</p></div>
                                <div class="form-group"><p><strong># Seguidores: </strong>'.$seguidores.'</p></div>
                                <button class="btn btn-success" name="update-playlist">Actualizar</button>
                            </form>
                        </div>

                        <div class="my_card">
                            <p class="p_index"><a href="validators/delete_playlist_request.php?id='.$id_playlist.'"><button type="submit" name="delete">Eliminar playlist</button></a></p>
                        </div>
                        ';  
                    }


                    //En caso contrario, sólo veo los datos y puedo seguir la lista
                    else{
                        echo'
                        <div class="card card-body">
                            <div class="form-group"><p><strong>ID de la playlist: </strong>'.$id_playlist.'</p></div>
                            <div class="form-group"><p><strong>Nombre de la playlist: </strong>'.$nombre_playlist.'</p></div>
                            <div class="form-group"><p><strong>Creador: </strong><a href="profile.php?username='.$creador.'">'.$creador.'</a></p></div>
                            <div class="form-group"><p><strong># Canciones: </strong>'.$canciones.'</p></div>
                            <div class="form-group"><p><strong># Seguidores: </strong>'.$seguidores.'</p></div>';

                            if (!is_following_playlist($id_playlist, $_SESSION['userName'])){
                                echo'<td><a class="btn btn-primary" href="validators/follow_playlist_request.php?playlist='.$id_playlist.'"><i class="fas fa-eye"></i></a></td>';
                            }
                            else{
                                echo'<td><a class="btn btn-primary" href="validators/unfollow_playlist_request.php?playlist='.$id_playlist.'"><i class="fas fa-eye-slash"></i></a></td>';
                            }

                        echo'</div>';
                    }

                    //Muestro las canciones. Si soy creador puedo añadir canciones
                    if ($creador == $username){
                        echo'<h2>Canciones <a class="btn btn-primary" href="add_song_to_playlist.php?id_playlist='.$id_playlist.'"><i class="fas fa-plus-square"></i></a></h2>';
                    }
                    else{
                        echo'<h2>Canciones</h2>';
                    }

                    $username = $_SESSION['userName'];
                    $query2 = "SELECT * FROM usuarios WHERE username = '$username'";  //SQL INJECTION ALERT
                    $result2 = mysqli_query($conn, $query2);
                    $row2 = mysqli_fetch_array($result2);

                    //Si soy usuario
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
                                            <th>Cantidad de likes</th>                                    
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>';
                                        $query = "SELECT C.* FROM canciones C JOIN playlists_conteniendo_canciones P ON C.id_cancion = P.id_cancion AND P.id_playlist = '.$id_playlist.'";
                                        $result_select = mysqli_query($conn, $query);
                                        
                                        while($row = mysqli_fetch_array($result_select)){
                                            echo'
                                            <tr>
                                                <td>'.$row["id_cancion"].'</td>
                                                <td><a href="song.php?id='.$row["id_cancion"].'">'.$row["nombre_cancion"].'</td>
                                                <td><a href="profile.php?username='.$row["autor"].'">'.$row["autor"].'</td>
                                                <td>'.$row["genero"].'</td>
                                                <td>'.$row["duracion"].'</td>
                                                <td>'.$row["anno_publicacion"].'</td>
                                                <td>'.$row["cantidad_likes"].'</td>';
                                                
                                                //Si soy el creador de la playlist (luego soy usuario) puedo quitar la canción de la playlist
                                                if ($creador == $username){
                                                    echo'<td><a class="btn btn-primary" href="validators/remove_song_from_playlist_request.php?playlist='.$id_playlist.'&song='.$row["id_cancion"].'"><i class="fas fa-trash-alt"></i></a>';
                                                    
                                                }
                                                //Si soy usuario puedo darle like
                                                else{
                                                    if (!is_liking_song($row["id_cancion"], $username)){
                                                    echo'<td><a class="btn btn-primary" href="validators/like_request.php?song='.$row["id_cancion"].'"><i class="far fa-thumbs-up"></i></a></td>';
                                                    }
                                                    else{
                                                        echo'<td><a class="btn btn-primary" href="validators/unlike_request.php?song='.$row["id_cancion"].'"><i class="fas fa-thumbs-up"></i></a></td>';
                                                    }
                                                }
                                            echo'
                                            </tr>';
                                        }
                                    echo'</tbody>
                                </table>
                            </div>
                        ';
                    }

                    //Si soy artista
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
                                        </tr>
                                    </thead>
                                    <tbody>';
                                        $query = "SELECT C.* FROM canciones C JOIN playlists_conteniendo_canciones P ON P.id_cancion = C.id_cancion";
                                        $result_select = mysqli_query($conn, $query);
                                        
                                        while($row = mysqli_fetch_array($result_select)){
                                            echo'
                                            <tr>
                                                <td>'.$row["id_cancion"].'</td>
                                                <td><a href="song.php?id='.$row["id_cancion"].'">'.$row["nombre_cancion"].'</td>
                                                <td><a href="profile.php?username='.$row["autor"].'">'.$row["autor"].'</td>
                                                <td>'.$row["genero"].'</td>
                                                <td>'.$row["duracion"].'</td>
                                                <td>'.$row["anno_publicacion"].'</td>
                                                <td>'.$row["cantidad_likes"].'</td>';
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
                    header("Location: playlists.php?error=operacioninvalida");
                }
            ?>
            
        </section>

        <?php
            require "components/footer.php";
        ?>
    </body>
</html>