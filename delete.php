<?php
// delete.php: elimina de forma segura un archivo de la carpeta qrs/
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: gallery.php');
    exit;
}
$file = $_POST['file'] ?? '';
$file = trim((string)$file);
if ($file === '') {
    header('Location: gallery.php?error=' . urlencode('Archivo no especificado'));
    exit;
}
// Evitar traversal y validar nombre
$base = basename($file);
if ($base !== $file) {
    header('Location: gallery.php?error=' . urlencode('Nombre de archivo inválido'));
    exit;
}
$allowedExt = ['png','jpg','jpeg','gif'];
$ext = strtolower(pathinfo($base, PATHINFO_EXTENSION));
if (!in_array($ext, $allowedExt)) {
    header('Location: gallery.php?error=' . urlencode('Tipo de archivo no permitido'));
    exit;
}
$full = __DIR__ . '/qrs/' . $base;
if (!file_exists($full)) {
    header('Location: gallery.php?error=' . urlencode('Archivo no encontrado'));
    exit;
}
// Intentar borrar
if (@unlink($full)) {
    header('Location: gallery.php?deleted=' . urlencode('QR eliminado correctamente'));
    exit;
} else {
    header('Location: gallery.php?error=' . urlencode('No se pudo eliminar el archivo (permisos)'));
    exit;
}
