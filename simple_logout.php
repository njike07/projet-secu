<?php
session_start();
session_destroy();
header("Location: simple_login.php?msg=Déconnexion réussie");
exit();
?>
