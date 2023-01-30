<?php
if (isset($_GET['p'])) {
    $id = trim(htmlspecialchars($_GET['p']));
}
header("location: /members.php?id={$id}");
