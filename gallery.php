<?php
$dir = __DIR__.'/qrs';

$files = [];

if (is_dir($dir)) {
    if ($dh = opendir($dir)) {
        while (($file = readdir($dh)) !== false) {
            if ($file != '.' && $file != '..' && !is_dir($dir . '/' . $file)) {
                // solo png
                if (pathinfo($file, PATHINFO_EXTENSION) === 'png') {
                    $files[] = $file;
                }
            }
        }
        closedir($dh);
    }
}
// Ordenar por fecha de modificación, más reciente primero
usort($files,function ($a,$b) use ($dir) {
    return filemtime($dir.'/'.$b) <=> filemtime($dir.'/'.$a);
});

// cargar la informacion de los metadatos
$metaFile = __DIR__.'/qrs/metadata.json';
$meta = [];
    // validacion de seguridad basicas
if (file_exists($metaFile)) {
    // pedimos el contenido del arhivo
    $metaContent = file_get_contents($metaFile);
    // formatear el contenido
    $meta = json_decode($metaContent, true) ?? [];
    if (!is_array($meta)) {
        $meta = [];
    }
}

// variables dinamica hacia el header
$pageTittle = "Galería de QR Guardados";
$nombre ="Galería";
include __DIR__.'/includes/header.php';
?>

<main class="wrap">
    <div class="card">
      <h1>QR guardados</h1>
      <p class="lead">Aquí puedes ver los QR que has generado anteriormente.</p>
      <div style="display:flex;gap:8px;margin-top:10px">
        <a class="btn" href="index.php">Generar QR</a>
      </div>

      <?php if (empty($files)): ?>
        <p class="empty">No se encontraron QR en la carpeta <code>/qrs/</code>.</p>
      <?php else: ?>
        <div class="grid">
          <?php foreach ($files as $f):
            $path = 'qrs/' . $f;
            $full = $dir . '/' . $f;
            $mtime = date('Y-m-d H:i', filemtime($full));
            $size = filesize($full);
            $sizeKb = round($size/1024, 1);
          ?>
            <div class="tile">
              <a href="#" class="preview" data-src="<?= htmlspecialchars($path) ?>" data-fname="<?= htmlspecialchars($f) ?>" onclick="openQrModal(event)">
                <div class="thumb">
                  <img src="<?= htmlspecialchars($path) ?>" alt="QR">
                  <div class="thumb-overlay" aria-hidden="true">
                    <span class="zoom">🔍</span>
                  </div>
                </div>
              </a>
              <div class="meta-title" title="<?= htmlspecialchars($f) ?>"><?= htmlspecialchars($f) ?></div>
              <?php if (isset($metaMap[$f])):
                  $orig = $metaMap[$f]['text'];
              ?>
                <?php if (preg_match('/^https?:\/\//i', $orig)): ?>
                  <div class="meta-url"><a href="<?= htmlspecialchars($orig) ?>" target="_blank" title="<?= htmlspecialchars($orig) ?>"><?= htmlspecialchars($orig) ?></a></div>
                <?php else: ?>
                  <div class="meta-url" title="<?= htmlspecialchars($orig) ?>"><?= htmlspecialchars($orig) ?></div>
                <?php endif; ?>
              <?php endif; ?>
              <div class="meta-row"><?= $mtime ?> • <?= $sizeKb ?> KB</div>
              <div class="actions">
                <a class="btn small alt" href="<?= htmlspecialchars($path) ?>" target="_blank">Abrir</a>
                <form method="post" action="delete.php" onsubmit="return confirm('¿Eliminar este QR? Esta acción no se puede deshacer.');" style="display:inline">
                  <input type="hidden" name="file" value="<?= htmlspecialchars($f) ?>">
                  <button type="submit" class="btn small danger">Eliminar</button>
                </form>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

    </div>
  </main>

  
  <!-- Modal de vista previa del QR -->
  <div id="qr-modal" class="qr-modal" aria-hidden="true">
    <div class="qr-modal-backdrop" onclick="closeQrModal()"></div>
    <div class="qr-modal-content" role="dialog" aria-modal="true">
      <button class="qr-modal-close" onclick="closeQrModal()" aria-label="Cerrar">×</button>
      <img id="qr-modal-img" src="" alt="QR grande">
      <div class="qr-modal-info">
        <div id="qr-modal-fname" class="qr-modal-fname"></div>
        <div class="qr-modal-actions">
          <a id="qr-modal-download" class="btn small" href="#" download>Descargar</a>
          <a id="qr-modal-open" class="btn small alt" href="#" target="_blank">Abrir en nueva pestaña</a>
        </div>
      </div>
    </div>
  </div>

  <script>
  function openQrModal(e){
    // prevenir el comportamiento por defecto
    e.preventDefault();
    var t = e.currentTarget;
    var src = t.getAttribute('data-src');
    var fname = t.getAttribute('data-fname') || 'qr.png';
    document.getElementById('qr-modal-img').src = src;
    document.getElementById('qr-modal-fname').textContent = fname;
    var dl = document.getElementById('qr-modal-download');
    dl.href = src; dl.setAttribute('download', fname);
    document.getElementById('qr-modal-open').href = src;
    document.getElementById('qr-modal').classList.add('open');
    document.body.style.overflow = 'hidden';
  }
  function closeQrModal(){
    document.getElementById('qr-modal').classList.remove('open');
    document.body.style.overflow = '';
  }
  document.addEventListener('keydown', function(e){ if(e.key === 'Escape') closeQrModal(); });
  </script>

<?php
    include __DIR__.'/includes/footer.php';
?> 