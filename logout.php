<?php
session_start();
session_destroy();
header("Location: pageLogin.php?msg=Déconnexion réussie");
exit();
?>