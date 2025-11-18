<!-- aqui comienza -->
<?php
    // variables dinamica hacia el header
    $pageTittle = "Curso de QR con PHP";
    $nombre ="Pagina Principal";
    include __DIR__.'/includes/header.php';
?> 
<!-- es el contenido -->
<main class="wrap">
    <section class="hero">
      <div class="card">
        <h1>Generador de QR</h1>
        <p class="lead">Introduce la URL de tu menú o cualquier texto y genera un QR listo para imprimir y colocar en mesas.</p>

        <form action="generar.php" method="post" class="form" enctype="multipart/form-data">
          <label for="text" class="label">URL o texto</label>
          <input id="text" name="text" type="text" placeholder="https://mi-restaurante.com/menu" required>

          <fieldset class="qr-customize">
            <legend>🎨 Personalización QR</legend>
            <div class="qr-row">
              <label>
                <span>Color principal:</span>
                <input type="color" name="colorQR" value="#1e1e1e">
              </label>
              <label>
                <span>Color fondo:</span>
                <input type="color" name="colorBG" value="#ffffff">
              </label>
            </div>
            <div class="qr-row">
              <label>
                <span>Logo en el centro:</span>
                <input type="file" name="logo" accept="image/png">
              </label>
              <label>
                <span>Tamaño del logo:</span>
                <input type="number" name="logoSize" min="10" max="50" value="20" style="width:60px;"> %
              </label>
            </div>
            <small class="qr-hint">Puedes elegir colores, subir un logo PNG y ajustar el tamaño del logo (10-50%).</small>
          </fieldset>

          <div class="actions">
            <button type="submit" class="btn">Generar QR</button>
            <a href="gallery.php" class="btn alt">Ver QR guardados</a>
          </div>
        </form>

        <p class="story">Perfecto para restaurantes, cafeterías y food trucks que quieren ofrecer su carta sin contacto.</p>
      </div>
    </section>
  </main>


  <!-- aqui acaba -->
<?php
    include __DIR__.'/includes/footer.php';
?> 