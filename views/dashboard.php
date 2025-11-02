<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit; }
?>

<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8"><title>HandyHub - Dashboard</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="assets/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    .stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:14px}
    .stat-card{background:linear-gradient(180deg, rgba(255,255,255,0.01), rgba(255,255,255,0.02));padding:12px;border-radius:10px}
    @media(max-width:900px){ .stats-grid{grid-template-columns:repeat(2,1fr);} }
  </style>
</head>
<body>
<?php include 'partials/nav.php'; ?>
<main class="container-fluid p-4">
  <h2>Dashboard</h2>

  <div class="stats-grid" id="statsGrid">
    <div class="stat-card"><div class="text-muted">Disponibles</div><div id="s_disp" class="h3">0</div></div>
    <div class="stat-card"><div class="text-muted">Prestadas</div><div id="s_prest" class="h3">0</div></div>
    <div class="stat-card"><div class="text-muted">En mantenimiento</div><div id="s_mant" class="h3">0</div></div>
    <div class="stat-card"><div class="text-muted">Dañadas</div><div id="s_dan" class="h3">0</div></div>
  </div>

  <div style="display:grid;grid-template-columns:2fr 1fr;gap:12px">
    <div class="panel open">
      <div class="panel-header"><h4>Uso mensual</h4></div>
      <div class="panel-body" style="max-height:300px; padding:14px 6px">
        <canvas id="chartUsage" height="220"></canvas>
      </div>
    </div>

    <div class="panel">
      <div class="panel-header"><h4>Préstamos recientes</h4></div>
      <div class="panel-body" style="padding:12px">
        <div id="recentLoans"></div>
      </div>
    </div>
  </div>

</main>
  </div> <!-- /.content-area -->
</div>   <!-- /.app-shell -->

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="assets/js/fetch-helpers.js"></script>
<script>
async function loadStats(){
  try {
    console.log('dashboard: solicitando stats...');
    const res = await fetch('api/stats.php', { credentials:'include' });
    // mostrar encabezados y status
    console.log('stats status', res.status, res.statusText);
    const text = await res.text();
    // intento parsear JSON
    try {
      const j = JSON.parse(text);
      console.log('stats parsed json:', j);
      // si viene error: mostrar mensaje
      if (j.error) {
        document.getElementById('recentLoans').innerText = 'Error: ' + (j.error || 'ver consola');
        console.warn('stats returned error:', j);
      }
      // asignar valores si existen
      document.getElementById('s_disp').innerText = (j.disponibles ?? 0);
      document.getElementById('s_prest').innerText = (j.prestadas ?? 0);
      document.getElementById('s_mant').innerText = (j.mantenimiento ?? 0);
      document.getElementById('s_dan').innerText = (j.danadas ?? 0);

      // chart
      const labels = (j.usage && j.usage.labels) ? j.usage.labels : [];
      const values = (j.usage && j.usage.values) ? j.usage.values : [];
      const ctx = document.getElementById('chartUsage').getContext('2d');
      if (window._dashboardChart) window._dashboardChart.destroy();
      window._dashboardChart = new Chart(ctx, {
        type:'line',
        data:{ labels, datasets:[{label:'Usos', data:values, tension:0.2, borderColor:'#38bdf8', fill:false}]},
        options:{ plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true}}}
      });

      const loans = j.recentLoans || [];
      const html = loans.map(l=>`<div style="padding:6px 0;border-bottom:1px solid rgba(255,255,255,0.02)"><strong>${l.herramienta||'—'}</strong><div class="text-muted small">${l.usuario||'—'} • ${l.Fecha_Prestamo||''}</div></div>`).join('');
      document.getElementById('recentLoans').innerHTML = html || '<div class="text-muted">No hay préstamos recientes</div>';
    } catch(jsonErr) {
      // no es JSON: mostrar texto crudo para depuración
      console.error('stats response is not JSON:', text);
      document.getElementById('recentLoans').innerText = 'Error cargando estadísticas (ver consola) — respuesta cruda: ' + text.slice(0,1000);
    }
  } catch (err) {
    console.error('loadStats error', err);
    document.getElementById('recentLoans').innerText = 'Error de conexión al cargar estadísticas (ver consola).';
  }
}
loadStats();
</script>



<!-- Bootstrap + panel toggle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script>
document.querySelectorAll('.panel-header').forEach(h=>{
  h.addEventListener('click', ()=> h.closest('.panel').classList.toggle('open'));
});
</script>


  </div> <!-- /.content-area -->
</div>   <!-- /.app-shell -->


</body>
</html>
