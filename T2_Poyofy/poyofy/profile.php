



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
            <h2>Perfil</h2>
            <?php
                //Si no recibo un username en la barra de direcciones o soy yo, entro a mi perfil
                if ( (!isset($_GET['username'])) || ($_GET['username'] == $_SESSION['userName']) ){
                    $username = $_SESSION['userName'];

                    $query = "SELECT * FROM personas WHERE username = '$username'";  //SQL INJECTION ALERT
                    $result = mysqli_query($conn, $query);
                    $row = mysqli_fetch_array($result);

                    $name = $row['nombre'];
                    $email = $row['email'];
                    $seguidores = $row['seguidores'];
                    $seguidos = $row['seguidos'];

                    //Veo si es artista o usuario
                    $query2 = "SELECT * FROM artistas WHERE username = '$username'";
                    $result2 = mysqli_query($conn, $query2);
                    $row2 = mysqli_fetch_array($result2);

                    if ($row2){
                        $is_artist = 1;
                    }

                    else{
                        $is_artist = 0;
                    }

                    //Si soy usuario
                    if ($is_artist == 0){
                        $query = "SELECT * FROM usuarios WHERE username = '$username'";  //SQL INJECTION ALERT
                        $result = mysqli_query($conn, $query);
                        $row = mysqli_fetch_array($result);

                        $playlists_creadas = $row['playlists_creadas'];
                        $playlists_seguidas = $row['playlists_seguidas'];
                        $genero_favorito = $row['genero_favorito'];

                        echo'
                        <div class="card card-body">
                            <form action="validators/update_profile_request.php?username='.$username.'" method="POST">
                                <div class="form-group"><p><strong>Nombre de usuario: </strong>'.$username.'</p></div>
                                <div class="form-group"><p><strong>Nombre: </strong><input type="text" class="form-control" name="name" value="'.$name.'" placeholder="Nombre"></p></div>
                                <div class="form-group"><p><strong># Seguidores: </strong>'.$seguidores.'</p></div>
                                <div class="form-group"><p><strong># Seguidos: </strong>'.$seguidos.'</p></div>
                                <div class="form-group"><p><strong># Playlists creadas: </strong>'.$playlists_creadas.'</p></div>
                                <div class="form-group"><p><strong># Playlists seguidas: </strong>'.$playlists_seguidas.'</p></div>
                                <div class="form-group"><p><strong>Género favorito: </strong><input type="text" class="form-control" name="genero_favorito" value="'.$genero_favorito.'" placeholder="Género favorito"></p></div>
                                <div class="form-group"><p><strong>Correo: </strong><input type="text" class="form-control" name="email" value="'.$email.'" placeholder="Correo"></p></div>
                                <div class="form-group"><p><strong>Nueva contraseña: </strong><input type="password" class="form-control" name="new_password" placeholder="Ingrese nueva contraseña"></p></div>
                                <button class="btn btn-success" name="update-profile">Actualizar</button>
                            </form>
                        </div>

                        <div class="my_card">
                            <p class="p_index"><a href="validators/delete_profile_request.php"><button type="submit" name="delete">Eliminar cuenta</button></a></p>
                        </div>
                        ';  
                        
                        //Muestro mis playlists creadas
                        echo'
                        <h2>Mis playlists creadas <a class="btn btn-primary" href="create_playlist.php"><i class="fas fa-plus-square"></i></a></h2>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th># Canciones</th>
                                    <th># Seguidores</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>';
                                
                                    $query = "SELECT * FROM playlists WHERE creador = '".$_SESSION["userName"]."'";
                                    $result_select = mysqli_query($conn, $query);
                                    
                                    while($row = mysqli_fetch_array($result_select)){
                                        echo'
                                        <tr>
                                            <td>'.$row["id_playlist"].'</td>
                                            <td><a href="playlist.php?id='.$row["id_playlist"].'">'.$row["nombre_playlist"].'</a></td>
                                            <td>'.$row["canciones"].'</td>
                                            <td>'.$row["seguidores"].'</td>
                                            <td>
                                                <a class="btn btn-primary" href="playlist?id='.$row["id_playlist"].'"><i class="fas fa-edit"></i></a>
                                                <a class="btn btn-primary" href="validators/delete_playlist_request.php?playlist='.$row["id_playlist"].'"><i class="fas fa-trash-alt"></i></a>
                                                <a class="btn btn-primary" href="add_song_to_playlist.php?&id_playlist='.$row["id_playlist"].'"><i class="fas fa-plus-square"></a></td>
                                            </td>
                                        </tr>';
                                    }
                                
                            echo'</tbody>
                        </table>';

                        //Muestro mis canciones gustadas
                        echo'
                        <h2>Mis canciones favoritas</h2>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Autor</th>
                                    <th>Género</th>
                                    <th>Duración</th>
                                    <th>Año de publicación</th>
                                    <th># Likes</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>';
                            
                                    $query = "SELECT P.* FROM canciones P JOIN usuarios_gustando_canciones F ON F.username = '".$_SESSION["userName"]."' AND P.id_cancion = F.id_cancion";
                                    $result_select = mysqli_query($conn, $query);
                                    
                                    while($row = mysqli_fetch_array($result_select)){
                                        echo'
                                        <tr>
                                            <td>'.$row["id_cancion"].'</td>
                                            <td><a href="song.php?id='.$row["id_cancion"].'">'.$row["nombre_cancion"].'</a></td>
                                            <td><a href="profile.php?username='.$row["autor"].'">'.$row["autor"].'</a></td>
                                            <td>'.$row["genero"].'</td>
                                            <td>'.$row["duracion"].'</td>
                                            <td>'.$row["anno_publicacion"].'</td>
                                            <td>'.$row["cantidad_likes"].'</td>';
                                            
                                            //Si soy usuario puedo darle like o agregarla a mi playlist
                                            if (!is_liking_song($row["id_cancion"], $username)){
                                                echo'<td><a class="btn btn-primary" href="validators/like_request.php?song='.$row["id_cancion"].'"><i class="far fa-thumbs-up"></i></a>';
                                            }
                                            else{
                                                echo'<td><a class="btn btn-primary" href="validators/unlike_request.php?song='.$row["id_cancion"].'"><i class="fas fa-thumbs-up"></i></a>';
                                            }
                                            echo'<a class="btn btn-primary" href="add_song_to_playlist.php?id_cancion='.$row["id_cancion"].'"><i class="fas fa-plus-square"></a></td>';
                                        echo'
                                        </tr>';   
                                    }
                            echo'</tbody>
                        </table>';
                    }

                    //Si soy artista
                    else{
                        $query = "SELECT * FROM artistas WHERE username = '$username'";  //SQL INJECTION ALERT
                        $result = mysqli_query($conn, $query);
                        $row = mysqli_fetch_array($result);

                        $canciones = $row['canciones_publicadas'];
                        $albums = $row['albums_publicados'];
                        $playlists_seguidas = $row['playlists_seguidas'];

                        echo'
                        <div class="card card-body">
                            <form action="validators/update_profile_request.php?username='.$username.'" method="POST">
                                <div class="form-group"><p><strong>Nombre de usuario: </strong>'.$username.'</p></div>
                                <div class="form-group"><p><strong>Nombre: </strong><input type="text" class="form-control" name="name" value="'.$name.'" placeholder="Nombre"></p></div>
                                <div class="form-group"><p><strong># Seguidores: </strong>'.$seguidores.'</p></div>
                                <div class="form-group"><p><strong># Seguidos: </strong>'.$seguidos.'</p></div>
                                <div class="form-group"><p><strong># Canciones publicadas: </strong>'.$canciones.'</p></div>
                                <div class="form-group"><p><strong># Álbums publicados: </strong>'.$albums.'</p></div>
                                <div class="form-group"><p><strong># Playlists seguidas: </strong>'.$playlists_seguidas.'</p></div>
                                <div class="form-group"><p><strong>Correo: </strong><input type="text" class="form-control" name="email" value="'.$email.'" placeholder="Correo"></p></div>
                                <div class="form-group"><p><strong>Nueva contraseña: </strong><input type="password" class="form-control" name="new_password" placeholder="Ingrese nueva contraseña"></p></div>
                                <button class="btn btn-success" name="update-profile">Actualizar</button>
                            </form>
                        </div>

                        <div class="my_card">
                            <p class="p_index"><a href="validators/delete_profile_request.php"><button type="submit" name="delete">Eliminar cuenta</button></a></p>
                        </div>
                        ';

                        //Muestro mis canciones
                        echo'
                        <h2>Mis canciones publicadas <a class="btn btn-primary" href="create_song.php"><i class="fas fa-plus-square"></i></a></h2>
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
                                        echo'<td><a class="btn btn-primary" href="song.php?id='.$row["id_cancion"].'"><i class="fas fa-pencil-alt"></i></a>';
                                        echo'<a class="btn btn-primary" href="validators/delete_song_request.php?id='.$row["id_cancion"].'"><i class="fas fa-trash-alt"></i></a>';
                                        echo'<a class="btn btn-primary" href="add_song_to_album.php?id_cancion='.$row["id_cancion"].'"><i class="fas fa-plus-square"></a></td>';
                                    echo'
                                    </tr>
                                    '; 
                                }
                            echo'</tbody>
                        </table>';


                        //Muestro mis albums
                        echo'
                        <h2>Mis álbums publicados <a class="btn btn-primary" href="create_album.php"><i class="fas fa-plus-square"></i></a></h2>
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
                                        echo'<td><a class="btn btn-primary" href="album.php?id='.$row["id_album"].'"><i class="fas fa-pencil-alt"></i></a>';
                                        echo'<a class="btn btn-primary" href="validators/delete_album_request.php?id='.$row["id_album"].'"><i class="fas fa-trash-alt"></i></a>';
                                        echo'<a class="btn btn-primary" href="add_song_to_album.php?id_album='.$row["id_album"].'"><i class="fas fa-plus-square"></a></td>';
                                    echo'
                                    </tr>
                                    '; 
                                }
                            echo'</tbody>
                        </table>';
                    }

                    //En cualquiera de los dos casos, muestro mis personas seguidas
                    echo'
                    <h2>Mis personas seguidas</h2>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Nombre de usuario</th>
                                <th># Seguidores</th>
                                <th># Seguidos</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>'; 
                            $query = "SELECT P.username, P.seguidores, P.seguidos FROM personas P JOIN personas_siguiendo_personas F ON F.username_seguidor = '".$_SESSION["userName"]."' AND P.username = F.username_seguido";
                            $result_select = mysqli_query($conn, $query);
                            
                            while($row = mysqli_fetch_array($result_select)){
                                echo'
                                <tr>                                    
                                    <td><a href="profile.php?username='.$row["username"].'">'.$row["username"].'</a></td>
                                    <td>'.$row["seguidores"].'</td>
                                    <td>'.$row["seguidos"].'</td>';

                                    if (!is_following_person($row["username"], $_SESSION['userName'])){
                                        echo'<td><a class="btn btn-primary" href="validators/follow_request.php?username='.$row["username"].'"><i class="fas fa-eye"></i></a></td>';
                                    }
                                    else{
                                        echo'<td><a class="btn btn-primary" href="validators/unfollow_request.php?username='.$row["username"].'"><i class="fas fa-eye-slash"></i></a></td>';
                                    }
                                echo'
                                </tr>
                                '; 
                            }
                        echo'</tbody>
                    </table>';

                    //En cualquiera de los dos casos, muestro mis playlists seguidas
                    echo'
                    <h2>Mis playlists seguidas</h2>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Creador</th>
                                <th># Canciones</th>
                                <th># Seguidores</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>'; 
                            $query = "SELECT P.* FROM playlists P JOIN personas_siguiendo_playlists F ON F.username = '".$_SESSION["userName"]."' AND P.id_playlist = F.id_playlist";
                            $result_select = mysqli_query($conn, $query);
                            
                            while($row = mysqli_fetch_array($result_select)){
                                echo'
                                <tr>
                                    <td>'.$row["id_playlist"].'</td>
                                    <td><a href="playlist.php?id='.$row["id_playlist"].'">'.$row["nombre_playlist"].'</a></td>
                                    <td><a href="profile.php?username='.$row["creador"].'">'.$row["creador"].'</a></td>
                                    <td>'.$row["canciones"].'</td>
                                    <td>'.$row["seguidores"].'</td>';

                                    if (!is_following_playlist($row["id_playlist"], $_SESSION['userName'])){
                                        echo'<td><a class="btn btn-primary" href="validators/follow_playlist_request.php?playlist='.$row["id_playlist"].'"><i class="fas fa-eye"></i></a></td>';
                                    }
                                    else{
                                        echo'<td><a class="btn btn-primary" href="validators/unfollow_playlist_request.php?playlist='.$row["id_playlist"].'"><i class="fas fa-eye-slash"></i></a></td>';
                                    }
                                echo'
                                </tr>
                                '; 
                            }
                        echo'</tbody>
                    </table>';
                }

                //En caso contrario accedo al perfil de él
                else{
                    $username = $_GET['username'];
                    $my_username = $_SESSION["userName"];

                    $query = "SELECT * FROM personas WHERE username = '$username'";  //SQL INJECTION ALERT
                    $result = mysqli_query($conn, $query);
                    $row = mysqli_fetch_array($result);

                    //Si el usuario no existe, retorno a la página principal
                    if (!$row){
                        header("Location: index.php?error=nouser");
                        exit();
                    }

                    $name = $row['nombre'];
                    $email = $row['email'];
                    $seguidores = $row['seguidores'];
                    $seguidos = $row['seguidos'];

                    //Veo si es artista o usuario
                    $query2 = "SELECT * FROM artistas WHERE username = '$username'";
                    $result2 = mysqli_query($conn, $query2);
                    $row2 = mysqli_fetch_array($result2);

                    if ($row2){
                        $is_artist = 1;
                    }

                    else{
                        $is_artist = 0;
                    }


                    //Si es usuario
                    if ($is_artist == 0){
                        $query = "SELECT * FROM usuarios WHERE username = '$username'";  //SQL INJECTION ALERT
                        $result = mysqli_query($conn, $query);
                        $row = mysqli_fetch_array($result);

                        $playlists_creadas = $row['playlists_creadas'];
                        $playlists_seguidas = $row['playlists_seguidas'];
                        $genero_favorito = $row['genero_favorito'];

                        echo'
                        <div class="card card-body">
                            <div class="form-group"><p><strong>Nombre de usuario: </strong>'.$username.'</p></div>
                            <div class="form-group"><p><strong>Nombre: </strong>'.$name.'</p></div>
                            <div class="form-group"><p><strong># Seguidores: </strong>'.$seguidores.'</p></div>
                            <div class="form-group"><p><strong># Seguidos: </strong>'.$seguidos.'</p></div>
                            <div class="form-group"><p><strong># Playlists creadas: </strong>'.$playlists_creadas.'</p></div>
                            <div class="form-group"><p><strong># Playlists seguidas: </strong>'.$playlists_seguidas.'</p></div>
                            <div class="form-group"><p><strong>Género favorito: </strong>'.$genero_favorito.'</p></div>
                            <div class="form-group"><p><strong>Email: </strong>'.$email.'</p></div>';
                        
                            if (!is_following_person($username, $my_username)){
                                echo'<td><a class="btn btn-primary" href="validators/follow_request.php?username='.$row["username"].'"><i class="fas fa-eye"></i></a></td>';
                            }
                            else{
                                echo'<td><a class="btn btn-primary" href="validators/unfollow_request.php?username='.$row["username"].'"><i class="fas fa-eye-slash"></i></a></td>';
                            }
                        echo'</div>';

                        //Muestro sus playlists creadas
                        echo'<h2>Playlists creadas</h2>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th># Canciones</th>
                                    <th># Seguidores</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>';
                            $query2 = "SELECT * FROM playlists WHERE creador = '".$username."'";
                            $result_select2 = mysqli_query($conn, $query2);
                            
                            while($row2 = mysqli_fetch_array($result_select2)){
                                echo'
                                <tr>
                                    <td>'.$row2["id_playlist"].'</td>
                                    <td><a href="playlist.php?id='.$row2["id_playlist"].'">'.$row2["nombre_playlist"].'</a></td>
                                    <td>'.$row2["canciones"].'</td>
                                    <td>'.$row2["seguidores"].'</td>';

                                    if (!is_following_playlist($row2["id_playlist"], $_SESSION['userName'])){
                                        echo'<td><a class="btn btn-primary" href="validators/follow_playlist_request.php?playlist='.$row2["id_playlist"].'"><i class="fas fa-eye"></i></a></td>';
                                    }
                                    else{
                                        echo'<td><a class="btn btn-primary" href="validators/unfollow_playlist_request.php?playlist='.$row2["id_playlist"].'"><i class="fas fa-eye-slash"></i></a></td>';
                                    }
                                echo'</tr>';
                            }
                            echo'</tbody>
                        </table>';

                        //Muestro sus canciones gustadas
                        $query_user = "SELECT * FROM usuarios WHERE username ='$my_username'";
                        $result = mysqli_query($conn, $query_user);
                        $row_user = mysqli_fetch_array($result);
                        echo'
                        <h2>Canciones favoritas</h2>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Autor</th>
                                    <th>Género</th>
                                    <th>Duración</th>
                                    <th>Año de publicación</th>
                                    <th># Likes</th>';
                                    if ($row_user){
                                    echo'<th>Acciones</th>';}
                                echo'</tr>
                            </thead>
                            <tbody>';
                                
                            $query = "SELECT P.* FROM canciones P JOIN usuarios_gustando_canciones F ON F.username = '".$username."' AND P.id_cancion = F.id_cancion";
                            $result_select = mysqli_query($conn, $query);
                                    
                            while($row = mysqli_fetch_array($result_select)){
                                echo'
                                <tr>
                                    <td>'.$row["id_cancion"].'</td>
                                    <td><a href="song.php?id='.$row["id_cancion"].'">'.$row["nombre_cancion"].'</a></td>
                                    <td><a href="profile.php?username='.$row["autor"].'">'.$row["autor"].'</a></td>
                                    <td>'.$row["genero"].'</td>
                                    <td>'.$row["duracion"].'</td>
                                    <td>'.$row["anno_publicacion"].'</td>
                                    <td>'.$row["cantidad_likes"].'</td>';
                                    
                                    //Si soy usuario puedo darle like o agregarla a mi playlist
                                    

                                    if ($row_user){
                                        if (!is_liking_song($row["id_cancion"], $my_username)){
                                            echo'<td><a class="btn btn-primary" href="validators/like_request.php?song='.$row["id_cancion"].'"><i class="far fa-thumbs-up"></i></a>';
                                        }
                                        else{
                                            echo'<td><a class="btn btn-primary" href="validators/unlike_request.php?song='.$row["id_cancion"].'"><i class="fas fa-thumbs-up"></i></a>';
                                        }
                                        echo'<a class="btn btn-primary" href="add_song_to_playlist.php?id_cancion='.$row["id_cancion"].'"><i class="fas fa-plus-square"></a></td>';
                                    }
                                    
                                    //Si soy artista y soy el creador puedo editar la canción, eliminarla o agregarla a un álbum
                                    else{
                                        if ($row["autor"] == $username){
                                            echo'<td><a class="btn btn-primary" href="song.php?id='.$row["id_cancion"].'"><i class="fas fa-pencil-alt"></i></a>';
                                            echo'<a class="btn btn-primary" href="validators/delete_song_request.php?id='.$row["id_cancion"].'"><i class="fas fa-trash-alt"></i></a>';
                                            echo'<a class="btn btn-primary" href="add_song_to_album.php?id_cancion='.$row["id_cancion"].'"><i class="fas fa-plus-square"></a></td>';
                                        }
                                    }
                                echo'
                                </tr>';   
                            }
                            echo'</tbody>
                        </table>';
                    }

                    //Si es artista
                    else{
                        $query = "SELECT * FROM artistas WHERE username = '$username'";  //SQL INJECTION ALERT
                        $result = mysqli_query($conn, $query);
                        $row = mysqli_fetch_array($result);
                        $me = $_SESSION["userName"];

                        $canciones = $row['canciones_publicadas'];
                        $albums = $row['albums_publicados'];
                        $playlists_seguidas = $row['playlists_seguidas'];

                        echo'
                        <div class="card card-body">
                            <div class="form-group"><p><strong>Nombre de usuario: </strong>'.$username.'</p></div>
                            <div class="form-group"><p><strong>Nombre: </strong>'.$name.'</p></div>
                            <div class="form-group"><p><strong># Seguidores: </strong>'.$seguidores.'</p></div>
                            <div class="form-group"><p><strong># Seguidos: </strong>'.$seguidos.'</p></div>
                            <div class="form-group"><p><strong># Canciones publicadas: </strong>'.$canciones.'</p></div>
                            <div class="form-group"><p><strong># Álbums publicados: </strong>'.$albums.'</p></div>
                            <div class="form-group"><p><strong># Playlists seguidas: </strong>'.$playlists_seguidas.'</p></div>
                            <div class="form-group"><p><strong>Email: </strong>'.$email.'</p></div>';
                            
                            if (!is_following_person($row["username"], $_SESSION['userName'])){
                                echo'<td><a class="btn btn-primary" href="validators/follow_request.php?username='.$row["username"].'"><i class="fas fa-eye"></i></a></td>';
                            }
                            else{
                                echo'<td><a class="btn btn-primary" href="validators/unfollow_request.php?username='.$row["username"].'"><i class="fas fa-eye-slash"></i></a></td>';
                            }
                            
                        echo'</div>';

                        //Muestro sus canciones creadas
                        $im_user = "SELECT * FROM usuarios WHERE username = '$me'";
                        $result_user = mysqli_query($conn, $im_user);
                        $row_user = mysqli_fetch_array($result_user);

                        echo'
                        <h2>Canciones publicadas</h2>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Género</th>
                                    <th>Duración</th>
                                    <th>Año de publicación</th>
                                    <th># Likes</th>';
                                    if ($row_user){
                                    echo'<th>Acciones</th>';}
                                echo'</tr>
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

                                        if ($row_user){
                                            //Si soy usuario puedo darle like o agregarla a mi playlist
                                            if (!is_liking_song($row["id_cancion"], $my_username)){
                                                echo'<td><a class="btn btn-primary" href="validators/like_request.php?song='.$row["id_cancion"].'"><i class="far fa-thumbs-up"></i></a>';
                                            }
                                            else{
                                                echo'<td><a class="btn btn-primary" href="validators/unlike_request.php?song='.$row["id_cancion"].'"><i class="fas fa-thumbs-up"></i></a>';
                                            }
                                            echo'<a class="btn btn-primary" href="add_song_to_playlist.php?id_cancion='.$row["id_cancion"].'"><i class="fas fa-plus-square"></a></td>';
                                        }


                                    echo'
                                    </tr>
                                    '; 
                                }
                            echo'</tbody>
                        </table>';

                        //Muestro sus albums creados
                        echo'
                        <h2>Álbums publicados</i></a></h2>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th># Canciones</th>
                                    <th>Año de publicación</th>
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
                                    echo'
                                    </tr>
                                    '; 
                                }
                            echo'</tbody>
                        </table>';
                    }

                    //En cualquiera de los dos casos, muestro sus personas seguidas
                    echo'
                    <h2>Personas seguidas</h2>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Nombre de usuario</th>
                                <th># Seguidores</th>
                                <th># Seguidos</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>'; 
                            $query = "SELECT P.username, P.seguidores, P.seguidos FROM personas P JOIN personas_siguiendo_personas F ON F.username_seguidor = '".$username."' AND P.username = F.username_seguido";
                            $result_select = mysqli_query($conn, $query);
                            
                            while($row = mysqli_fetch_array($result_select)){
                                echo'
                                <tr>                                    
                                    <td><a href="profile.php?username='.$row["username"].'">'.$row["username"].'</a></td>
                                    <td>'.$row["seguidores"].'</td>
                                    <td>'.$row["seguidos"].'</td>';

                                    if (!is_following_person($row["username"], $_SESSION['userName'])){
                                        echo'<td><a class="btn btn-primary" href="validators/follow_request.php?username='.$row["username"].'"><i class="fas fa-eye"></i></a></td>';
                                    }
                                    else{
                                        echo'<td><a class="btn btn-primary" href="validators/unfollow_request.php?username='.$row["username"].'"><i class="fas fa-eye-slash"></i></a></td>';
                                    }
                                echo'
                                </tr>
                                '; 
                            }
                        echo'</tbody>
                    </table>';

                    //En cualquiera de los dos casos, muestro sus playlists seguidas
                    echo'
                    <h2>Playlists seguidas</h2>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Creador</th>
                                <th># Canciones</th>
                                <th># Seguidores</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>'; 
                            $query = "SELECT P.* FROM playlists P JOIN personas_siguiendo_playlists F ON F.username = '".$username."' AND P.id_playlist = F.id_playlist";
                            $result_select = mysqli_query($conn, $query);
                            
                            while($row = mysqli_fetch_array($result_select)){
                                echo'
                                <tr>
                                    <td>'.$row["id_playlist"].'</td>
                                    <td><a href="playlist.php?id='.$row["id_playlist"].'">'.$row["nombre_playlist"].'</a></td>
                                    <td><a href="profile.php?username='.$row["creador"].'">'.$row["creador"].'</a></td>
                                    <td>'.$row["canciones"].'</td>
                                    <td>'.$row["seguidores"].'</td>';

                                    if ($row['creador'] != $_SESSION['userName']){
                                        if (!is_following_playlist($row["id_playlist"], $_SESSION['userName'])){
                                            echo'<td><a class="btn btn-primary" href="validators/follow_playlist_request.php?playlist='.$row["id_playlist"].'"><i class="fas fa-eye"></i></a></td>';
                                        }
                                        else{
                                            echo'<td><a class="btn btn-primary" href="validators/unfollow_playlist_request.php?playlist='.$row["id_playlist"].'"><i class="fas fa-eye-slash"></i></a></td>';
                                        }
                                    }
                                echo'
                                </tr>
                                '; 
                            }
                        echo'</tbody>
                    </table>';
                }
            ?>
            
        </section>

        <?php
            require "components/footer.php";
        ?>
    </body>
</html>