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

            //Verificar si es usuario o artista
            $username = $_SESSION["userName"];
            $query = "SELECT * FROM usuarios WHERE username = '$username'";
            $result = mysqli_query($conn, $query);
            $row = mysqli_fetch_array($result);
            if ($row){
                echo'<section id="content"><h2>Playlists <a class="btn btn-primary" href="create_playlist.php"><i class="fas fa-plus-square"></i></a></h2>';
            }
            else{
                echo'<section id="content"><h2>Playlists</h2>';
            }
        ?>

        
        
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
                <tbody>
                    <?php
                        $query = "SELECT * FROM playlists";
                        $result_select = mysqli_query($conn, $query);
                        
                        while($row = mysqli_fetch_array($result_select)){
                            echo'
                            <tr>
                                <td>'.$row["id_playlist"].'</td>
                                <td><a href="playlist.php?id='.$row["id_playlist"].'">'.$row["nombre_playlist"].'</a></td>
                                <td><a href="profile.php?username='.$row["creador"].'">'.$row["creador"].'</a></td>
                                <td>'.$row["canciones"].'</td>
                                <td>'.$row["seguidores"].'</td>';

                                if ($row["creador"] != $_SESSION['userName']){
                                    if (!is_following_playlist($row["id_playlist"], $_SESSION['userName'])){
                                        echo'<td><a class="btn btn-primary" href="validators/follow_playlist_request.php?playlist='.$row["id_playlist"].'"><i class="fas fa-eye"></i></a></td>';
                                    }
                                    else{
                                        echo'<td><a class="btn btn-primary" href="validators/unfollow_playlist_request.php?playlist='.$row["id_playlist"].'"><i class="fas fa-eye-slash"></i></a></td>';
                                    }
                                }
                                else{
                                    echo'
                                    <td>
                                        <a class="btn btn-primary" href="playlist.php?id='.$row["id_playlist"].'"><i class="fas fa-edit"></i></a>
                                        <a class="btn btn-primary" href="validators/delete_playlist_request.php?playlist='.$row["id_playlist"].'"><i class="fas fa-trash-alt"></i></a>
                                        <a class="btn btn-primary" href="add_song_to_playlist.php?id_playlist='.$row["id_playlist"].'"><i class="fas fa-plus-square"></a></td>
                                    </td>';
                                }
                            echo'
                            </tr>
                            '; 
                        }
                    ?>
                </tbody>
            </table>
        </section>
        
        <?php
            require "components/footer.php";
        ?>
    </body>
</html>