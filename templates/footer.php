</div>
<footer>
<div class="container">
    <div class="row">
        <div class="col-sm-3">
            <img src="/image/online.png" width="250px"/>
        </div>
        <div class="col-sm-9 text-center">
            <h6>Ce site internet et ses webmasters ne sont en aucun cas affiliés avec NIVEAU99, Blablaland ou leurs
                partenaires. L'ensemble du contenu flash présent sur ce site appartient à NIVEAU99.<br>Projet à but non
                lucratif proposé par Y0zox et Sorrow à des fins éducatives.</h6>
            <a href="regles.php" style="color:#BD54DB;font-weight:bold;">Règlement</a> | 
            <a href="equipe.php" style="color:#BD54DB;font-weight:bold;">Liste de l'équipe</a> |
            <a href="jouer.php" style="color:#BD54DB;font-weight:bold;">Jouer</a> |
            <a href="https://discord.gg/XHNEXSFkcC" style="color:#BD54DB;font-weight:bold;">Discord</a>
        </div>
    </div>
</div>
</footer>
    <script src="js/bootstrap.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
</body>
</html>
<?php 
if(isset($_SESSION['theme']) && $_SESSION['theme']) {
    echo "<script>
    (function () {
        var c = 'd';
        var e = document.createElement('link');
        e.id = c;
        e.rel = 'stylesheet';
        e.type = 'text/css';
        e.href = '/css/bootstrap-dark.min.css';
        e.media = 'all';
        var f = document.getElementsByTagName('head')[0];
        f.appendChild(e);
        is = true;
        var g = document.getElementById('switch');
        g.innerHTML = 'rallumer la lumière';
    })();
    </script>";
}
?>