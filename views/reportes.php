<?php
session_start();
if (!isset($_SESSION['user_id'])) header("Location:index.php");
?>
<!doctype html><html lang="es"><head><meta charset="utf-8"><title>Reportes - HandyHub</title><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="assets/css/bootstrap.min.css"><link rel="stylesheet" href="assets/css/style.css"></head><body>
<?php include 'partials/nav.php'; ?>
<main class="container-fluid p-4">
  <h3>Reportes</h3>
  <div class="panel">
    <div class="panel-header"><h4>Exportar préstamos (CSV)</h4></div>
    <div class="panel-body" style="padding:12px">
      <form id="form-report">
        <div class="form-grid">
          <div class="form-row"><label>Desde</label><input type="date" id="r_from"></div>
          <div class="form-row"><label>Hasta</label><input type="date" id="r_to"></div>
        </div>
        <div class="form-actions" style="margin-top:10px"><button class="btn btn-primary" id="btnExport">Generar CSV</button></div>
      </form>
    </div>
  </div>
  <div id="reportResult" style="margin-top:14px"></div>
</main>
  </div>
</div>

<script src="assets/js/fetch-helpers.js"></script>
<script>
document.getElementById('form-report').addEventListener('submit', async function(e){
  e.preventDefault();
  const from = document.getElementById('r_from').value;
  const to = document.getElementById('r_to').value;
  if (!from || !to){ alert('Selecciona rango'); return; }
  const url = `exports_prestamos.php?from=${encodeURIComponent(from)}&to=${encodeURIComponent(to)}`;
  try{
    const res = await fetch(url, {credentials:'include'});
    if (!res.ok) { alert('No se pudo generar CSV'); return; }
    const blob = await res.blob();
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = `prestamos_${from}_a_${to}.csv`;
    document.body.appendChild(link); link.click(); link.remove();
  }catch(err){ console.error(err); alert('Error al generar'); }
});
</script>

<!-- Bootstrap + panel toggle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script>document.querySelectorAll('.panel-header').forEach(h=>h.addEventListener('click', ()=>h.closest('.panel').classList.toggle('open')));</script>

  </div> <!-- /.content-area -->
</div>   <!-- /.app-shell -->


</body>
</html>
