<?php
// las partes dinamicas de la pagina
if(!isset($pageTittle)) {
    $pageTittle = "Titulo por defecto";
}

if(!isset($nombre)) {
    $nombre = "Nombre por defecto";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTittle; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Merriweather:wght@300;400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header class="site-header">
        <div class="wrap header-inner">
            <div class="brand">
                <div class="logo"><?php echo $nombre; ?></div>
                <div class="tag">El sabor de casa</div>
            </div>
            <div class="contact">Reserva: <a href="tel:+573146133941">3146133941</a></div>
        </div>
    </header>