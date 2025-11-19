<?php
    // variables dinamica hacia el header
    $pageTittle = "Tu qr esta listo";
    $nombre ="Aca sale el qr generado";
    include __DIR__.'/includes/header.php';


    // recibir la informacion del formulario
    // text https://new-portoflio.onrender.com/
    $text = trim((string)($_POST['text'] ?? ''));
    // colorQR #ccc
    $colorQR_hex = isset($_POST['colorQR']) ? $_POST['colorQR'] : '#1e1e1e';
    // colorBG #ffffff
    $colorBG_hex = isset($_POST['colorBG']) ? $_POST['colorBG'] : '#ffffff';
    // logoSize 20
    $logoSizePercent = isset($_POST['logoSize']) ? max(10, min(50, (int)$_POST['logoSize'])) : 20;
    
    
    // convierte el color de hexadecimal a rgb
    function hex2rgb($hex){
        $hex = ltrim($hex, '#');
        if (strlen($hex) == 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }
        $int = hexdec($hex);
        return [
            ($int >> 16) & 255,
            ($int >> 8) & 255,
            $int & 255
        ];
    }

    // validaciones de seguridad basicas
    if($text === ''){
        header('Location: index.php');
        exit;
    }

    // inclusion de la libreria
    $libPath = __DIR__ .'/phpqrcode/qrlib.php';

    if(!file_exists($libPath)){
        die('Error: No se encontro la libreria QR.');
    }

    require_once $libPath;

    $dir = __DIR__.'/qrs';
    // validacion de existencia de la carpeta y creacion si no existe
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    // generar el nombre de archivo unico y seguro
    $hash = substr(md5($text . microtime(true)), 0, 10);
    $filename = 'qr_' . $hash .'_'.time().'.png';
    $filepath = $dir . '/' . $filename;


    // parametro : ECC tamaño y margen
    $ecc = 'H'; // Nivel de corrección de errores: L, M, Q, H
    $size = 8; // Tamaño del QR
    $magin = 2; // Margen

    QRcode::png($text,$filepath,$ecc,$size,$magin);

    // --- Personalización: color y logo ---
    $qr = imagecreatefrompng($filepath);
    $width = imagesx($qr);
    $height = imagesy($qr);
    $rgbQR = hex2rgb($colorQR_hex);
    $rgbBG = hex2rgb($colorBG_hex);
    $colorQR = imagecolorallocate($qr, $rgbQR[0], $rgbQR[1], $rgbQR[2]);
    $colorBG = imagecolorallocate($qr, $rgbBG[0], $rgbBG[1], $rgbBG[2]);

    for ($x = 0; $x < $width; $x++) {
        for ($y = 0; $y < $height; $y++) {
            $rgb = imagecolorat($qr, $x, $y);
            // Negro (píxel del QR)
            if ($rgb == 0) {
            imagesetpixel($qr, $x, $y, $colorQR);
            } else {
            imagesetpixel($qr, $x, $y, $colorBG);
            }
        }
    }

    // --- Insertar logo en el centro ---
    $logoTempPath = null;
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $logoTempPath = $_FILES['logo']['tmp_name'];
    }
    if ($logoTempPath && file_exists($logoTempPath)) {
    $logo = imagecreatefrompng($logoTempPath);
    $logoWidth = imagesx($logo);
    $logoHeight = imagesy($logo);

    // Tamaño del logo (% del QR)
    $logoSize = (int)($width * ($logoSizePercent / 100));

    // Redimensionar logo
    $logoResized = imagecreatetruecolor($logoSize, $logoSize);
    imagealphablending($logoResized, false);
    imagesavealpha($logoResized, true);
    imagecopyresampled($logoResized, $logo, 0, 0, 0, 0, $logoSize, $logoSize, $logoWidth, $logoHeight);

    // Borde redondeado al logo
    $mask = imagecreatetruecolor($logoSize, $logoSize);
    imagesavealpha($mask, true);
    $trans = imagecolorallocatealpha($mask, 0, 0, 0, 127);
    imagefill($mask, 0, 0, $trans);
    $circleColor = imagecolorallocatealpha($mask, 0, 0, 0, 0);
    imagefilledellipse($mask, $logoSize/2, $logoSize/2, $logoSize, $logoSize, $circleColor);
    // Aplicar máscara circular
    for ($x = 0; $x < $logoSize; $x++) {
        for ($y = 0; $y < $logoSize; $y++) {
        $alpha = (imagecolorat($mask, $x, $y) >> 24) & 0x7F;
        if ($alpha == 127) {
            imagesetpixel($logoResized, $x, $y, $trans);
        }
        }
    }

    // Posición centrada
    $posX = (int)(($width - $logoSize) / 2);
    $posY = (int)(($height - $logoSize) / 2);

    // Combinar
    imagecopy($qr, $logoResized, $posX, $posY, 0, 0, $logoSize, $logoSize);
    imagedestroy($logo);
    imagedestroy($logoResized);
    imagedestroy($mask);
    }


    imagepng($qr, $filepath);
    imagedestroy($qr);

    $metaFile = __DIR__.'/qrs/metadata.json';
    $meta = [];
    // validacion de seguridad basicas
    if (file_exists($metaFile)) {
        $metaContent = file_get_contents($metaFile);
        $meta = json_decode($metaContent, true) ?? [];
        if (!is_array($meta)) {
            $meta = [];
        }
    }

    $meta[] = [
        'file' => $filename,
        'text' => $text,
        'colorQR' => $colorQR_hex,
        'colorBG' => $colorBG_hex,
        'logoSizePercent' => $logoSizePercent,
        'createdAt' => date('Y-m-d H:i:s'),
    ];

    @file_put_contents($metaFile, json_encode($meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),LOCK_EX);

    // mostrar el resultado
    $escapedText = htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE);
    $webPath = 'qrs/' . $filename;
?> 

    <main class="wrap">
    <div class="card result">
      <h1>QR generado</h1>
      <div class="qr-box">
        <img src="<?= htmlspecialchars($webPath) ?>" alt="QR" />
      </div>
      <p class="meta">Texto / URL: <strong><?= $escapedText ?></strong></p>
      <div class="actions">
        <a class="btn" href="<?= htmlspecialchars($webPath) ?>" download="<?= htmlspecialchars($filename) ?>">Descargar QR</a>
        <a class="btn alt" href="index.php">Generar otro</a>
      </div>
      <p class="note">El archivo fue guardado en <code>/qrs/<?= htmlspecialchars($filename) ?></code></p>
    </div>
  </main>



<?php
    include __DIR__.'/includes/footer.php';
?> 