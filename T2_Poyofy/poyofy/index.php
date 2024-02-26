<!--
  Archivo: index.php
  Función: Mostrar la página principal
  Accesible si está:
    No logeado: Sí
    Logeado: Sí
  Visible para: No Personas, Personas
-->

<!DOCTYPE html>
<html>
    <head>
        <?php
            require "components/head.php";
        ?>
    </head>

    <body>
        <?php
            require "components/body.php";
        ?>

        <section id="content">
            <h2>¡Bienvenido a Poyofy!</h2>
            <p>El sitio web de música más popular de Dream Land</p><br>
            <h2>Regístrate, ¡Es gratis!</h2>
            <p>Escucha a tus artistas y tu música favorita ¡Sin costo!</p><br>
            <h2>¡Síguenos en nuestras redes sociales!</h2>
            <a href="https://www.facebook.com"><img src="resources/img/rrss-facebook.png" alt="RRSS Facebook" width="80" height="80"></a>
            <a href="https://www.instagram.com"><img src="resources/img/rrss-instagram.png" alt="RRSS Instagram" width="80" height="80"></a>
            <a href="https://www.spotify.com"><img src="resources/img/rrss-spotify.png" alt="RRSS Spotify" width="80" height="80"></a>
        </section>

        <?php
            require "components/footer.php";
        ?>
    </body>
</html>