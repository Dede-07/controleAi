<?php
include 'backend/login/auth.php'; // Inclui a verificação de autenticação
// Pega o nome do usuário da sessão para exibição (com tratamento básico)
$nome_usuario = isset($_SESSION['usuario_nome']) ? htmlspecialchars($_SESSION['usuario_nome']) : 'Usuário';
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Controle de Glicemia</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="css/style.css" /> <!-- Corrigido caminho do CSS -->
  <link rel="site icon" href="LogoCA.png" type="image/png" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>

<body class="bg-light">

  <nav class="navbar navbar-expand-lg bg-white shadow-sm mb-4">
    <div class="container d-flex justify-content-between align-items-center">
      <a class="navbar-brand fw-semibold d-flex align-items-center" href="index.php"> 
        <img src="LogoCA.png" class="img-fluid" style="max-height: 70px;" alt="logo">
        <span class="ms-2" style="color: #2f4858; font-weight: bold; font-size: 2rem;">Controle Aí</span>
      </a>
      <div> <!-- Div para agrupar usuário e botão Sair -->
        <span class="navbar-text me-3">
          Olá, <?php echo $nome_usuario; ?>!
        </span>
        <a href="backend/login/logout.php" class="btn btn-outline-danger btn-sm">Sair &nbsp<i class="bi bi-box-arrow-right"></i></a>
      </div>
    </div>
  </nav>

  <main class="container my-5">

    <div class="row g-4">

      <!-- Histórico + Gráfico + Média -->
      <div class="col-md-8">
        <div class="row g-4">

          <!-- Histórico -->
          <div class="col-md-7">
            <div class="card p-4">
              <div class="d-flex justify-content-between mb-3">
                <h4 class="fw-semibold">Histórico de Registros</h4>
                <div>
                  <button class="btn btn-success btn-sm me-2" onclick="gerarPDF()">Relatório <i class="bi bi-clipboard-check"></i></button>
                  <button class="btn btn-danger btn-sm" onclick="limparDados()">Limpar <i class="bi bi-trash3"></i></button>
                </div>
              </div>

              <div class="row mb-3">
                <div class="col">
                  <label class="form-label">Data inicial</label>
                  <input type="date" id="dataInicio" class="form-control">
                </div>
                <div class="col">
                  <label class="form-label">Data final</label>
                  <input type="date" id="dataFim" class="form-control">
                </div>
                <div class="col d-flex align-items-end mt-3">
                  <button style="background-color: #2f4858; color: white;" class="btn me-2" onclick="filtrar()">Filtrar <i class="bi bi-funnel"></i></button>
                  <button class="btn btn-secondary" onclick="limparFiltro()">Limpar <i class="bi bi-x-lg"></i></button>
                </div>
              </div>

              <div class="table-responsive">
                <table class="table table-bordered align-middle rounded">
                  <thead class="thead-azul">
                    <tr>
                      <th>Data</th>
                      <th>Horário</th>
                      <th>Glicemia</th>
                      <th>Ações</th>
                    </tr>
                  </thead>
                  <tbody id="tabela"></tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- Gráfico Pequeno -->
          <div class="col-md-5">
            <div class="card p-4 mb-3">
              <h5 class="text-center mb-3 fw-semibold">Visão Geral</h5>
              <canvas id="grafico"></canvas>
            </div>

            <!-- Card de Média -->
            <div class="card p-4" id="cardResumo">
              <h5 class="text-center fw-semibold mb-2">Média de Glicemia</h5>
              <div id="resumo" class="fs-4 fw-bold text-center"></div>
            </div>
          </div>

        </div>
      </div>

      <!-- Novo Registro -->
      <div class="col-md-4">
        <div class="card p-4">
          <h4 class="text-center mb-3 fw-semibold">Novo Registro</h4>
          <form id="formRegistro">
            <div class="mb-3">
              <label class="form-label">Data</label>
              <input type="date" id="data" class="form-control" required />
            </div>
            <div class="mb-3">
              <label class="form-label">Horário</label>
              <select id="horario" class="form-select" required>
                <option value="">Selecione</option>
                <option>Antes do Café (Em jejum)</option>
                <option>Depois do Café (Após comer)</option>
                <option>Antes do Almoço (Em jejum)</option>
                <option>Depois do Almoço (Após comer)</option>
                <option>Antes do Jantar (Em jejum)</option>
                <option>Depois do Jantar (Após comer)</option>
                <option>Antes de Dormir</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Valor (mg/dL)</label>
              <input type="number" id="valor" class="form-control" min="20" max="600" required />
            </div>
            <button type="submit" style="background-color: #2f4858; color: white;" class="btn w-100">Salvar <i class="bi bi-save"></i></button>
            <div id="alerta" class="alert alert-success mt-3 d-none" role="alert">
              Registro adicionado com sucesso!
            </div>
          </form>
        </div>
      </div>

    </div>

  </main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>
<script src="js/main.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>

</body>

</html>
