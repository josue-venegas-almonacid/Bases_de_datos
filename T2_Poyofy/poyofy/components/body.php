<!--
  Archivo: body.php
  Función: Definir el encabezado de todas las páginas
-->

<!doctype html>
<html lang="en">

<div id="container">
    <header>
        <a href="index.php"><img src="resources/img/banner.png" alt="Logo Poyofy" class="center" width="220" height="180"></a>
    </header>

    <nav>
        <ul>
        <?php
            echo'<li><a href="index.php">Página principal</a></li>';
            if (isset($_SESSION["userName"])){
                //SI ESTÁ LOGEADO MOSTRAR index, comunidad, mi_perfil, canciones_totales, playlists_totales, albums_totales
                echo'
                <li><a href="community.php">Comunidad</a></li>
                <li><a href="profile.php">Mi perfil</a></li>
                <li><a href="songs.php">Canciones</a></li>
                <li><a href="playlists.php">Playlists</a></li>
                <li><a href="albums.php">Albums</a></li>
                ';
            }
            else{
                //SI NO ESTÁ LOGEADO MOSTRAR index, signup
                echo'
                <li><a href="signup.php">Registrarse</a></li>
                ';
            }
            ?>
        </ul>

        <aside>
            <!--Login-logout buttons-->
            <?php
                //Si no hay una sesión iniciada, muestro los botones para inciar sesión
                if (!isset($_SESSION["userName"])){
                    echo '
                    <form action="validators/login_request.php" method="post">
                        <input type="text" name="username" placeholder="Nombre de usuario">
                        <input type="password" name="password" placeholder="Contraseña">
                        <button type="submit" name="login-submit">Iniciar sesión</button>
                    </form>
                    ';
                }

                //Si hay sesión iniciada, sólo muestro el botón para cerrar sesión
                else{
                    echo '
                    Usuario: '.$_SESSION["userName"].'
                    <form action="validators/logout_request.php" method="post" style="display: inline;">
                        <button type="submit" name="logout-submit">Cerrar sesión</button>
                    </form>
                    ';
                }
            ?>
        </aside>
    </nav>

    <div class="clearfix"></div>
    
    <?php
        if (isset($_GET["error"])){
            echo'<section id="error_alert">';
            if ($_GET["error"] == "emptyfields"){
                echo 'Error: Complete todos los campos';
            }
            else if ($_GET["error"] == "wrongpassword"){
                echo 'Error: Contraseña incorrecta';
            }
            else if ($_GET["error"] == "nouser"){
                echo 'Error: El usuario no existe';
            }
            else if ($_GET["error"] == "usernamealreadyexists"){
                echo 'Error: El usuario ya existe';
            }
            else if ($_GET["error"] == "useralreadyfollowed"){
                echo 'Error: El usuario ya ha sido seguido';
            }
            else if ($_GET["error"] == "useralreadyunfollowed"){
                echo 'Error: El usuario no ha sido seguido';
            }
            else if ($_GET["error"] == "noplaylist"){
                echo 'Error: La playlist no existe';
            }
            else if ($_GET["error"] == "playlistalreadyfollowed"){
                echo 'Error: La playlist ya ha sido seguida';
            }
            else if ($_GET["error"] == "playlistalreadyunfollowed"){
                echo 'Error: La playlist no ha sido seguida';
            }
            else if ($_GET["error"] == "nosong"){
                echo 'Error: La canción no existe';
            }
            else if ($_GET["error"] == "noalbum"){
                echo 'Error: El álbum no existe';
            }
            else if ($_GET["error"] == "isnotowner"){
                echo 'Error: La canción o álbum no pertenece al artista';
            }
            else if ($_GET["error"] == "operacioninvalida"){
                echo 'Error: Operación inválida';
            }
            else if ($_GET["error"] == "sqlerror"){
                echo 'Error de SQL';
            }
            else{
                echo 'Error desconocido';
            }
            echo'</section>';
        }

        else if (isset($_GET["success"])){
            echo'<section id="success_alert">';
            if ($_GET["success"] == "login"){
                echo 'Sesión iniciada correctamente';
            }
            if ($_GET["success"] == "logout"){
                echo 'Sesión cerrada correctamente';
            }
            else if ($_GET["success"] == "signup"){
                echo 'Registro realizado correctamente';
            }
            else if ($_GET["success"] == "follow"){
                echo 'Usuario seguido correctamente';
            }
            else if ($_GET["success"] == "unfollow"){
                echo 'Usuario sin seguir correctamente';
            }
            else if ($_GET["success"] == "update-profile"){
                echo 'Perfil actualizado correctamente';
            }
            else if ($_GET["success"] == "delete-profile"){
                echo 'Perfil eliminado correctamente';
            }
            else if ($_GET["success"] == "follow-playlist"){
                echo 'Playlist seguida correctamente';
            }
            else if ($_GET["success"] == "unfollow-playlist"){
                echo 'Playlist sin seguir correctamente';
            }
            else if ($_GET["success"] == "create-playlist"){
                echo 'Playlist creada correctamente';
            }
            else if ($_GET["success"] == "update-playlist"){
                echo 'Playlist actualizada correctamente';
            }
            else if ($_GET["success"] == "delete-playlist"){
                echo 'Playlist eliminada correctamente';
            }
            else if ($_GET["success"] == "create-song"){
                echo 'Canción creada correctamente';
            }
            else if ($_GET["success"] == "update-song"){
                echo 'Canción creada correctamente';
            }
            else if ($_GET["success"] == "delete-song"){
                echo 'Canción eliminada correctamente';
            }
            else if ($_GET["success"] == "create-album"){
                echo 'Álbum creado correctamente';
            }
            else if ($_GET["success"] == "update-album"){
                echo 'Álbum actualizado correctamente';
            }
            else if ($_GET["success"] == "delete-album"){
                echo 'Álbum eliminado correctamente';
            }
            else if ($_GET["success"] == "like-song"){
                echo 'Me gusta añadido correctamente';
            }
            else if ($_GET["success"] == "unlike-song"){
                echo 'Me gusta eliminado correctamente';
            }
            else if ($_GET["success"] == "add-song-to-playlist"){
                echo 'Canción añadida correctamente';
            }
            else if ($_GET["success"] == "add-song-to-album"){
                echo 'Canción añadida correctamente';
            }
            else if ($_GET["success"] == "remove-song-from-playlist"){
                echo 'Canción removida correctamente';
            }
            else if ($_GET["success"] == "remove-song-from-album"){
                echo 'Canción removida correctamente';
            }
            echo'</section>';
        }
    ?>
</div>

