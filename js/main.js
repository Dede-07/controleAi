const form = document.getElementById("formRegistro");
const tabela = document.getElementById("tabela");
const resumo = document.getElementById("resumo");
const ctx = document.getElementById("grafico").getContext("2d");

let registros = [];
let grafico;

// ✅ Carregar registros ao iniciar
window.onload = listar;


// ✅ Salvar novo registro
form.addEventListener("submit", function (e) {
    e.preventDefault();

    const data = document.getElementById("data").value;
    const horario = document.getElementById("horario").value;
    const valor = document.getElementById("valor").value;

    fetch("backend/adicionar.php", {
        method: "POST",
        body: new URLSearchParams({
            data,
            horario,
            valor
        })
    })
        .then(res => res.json())
        .then(() => {
            form.reset();
            listar();
        });
});

// ✅ Listar registros
function listar() {
    fetch("backend/listar.php")
        .then(res => res.json())
        .then(dados => {
            registros = dados;
            preencherTabela(dados);
            atualizarGrafico(dados);
            atualizarResumo(dados);
        });
}

// ✅ Preencher tabela
function preencherTabela(dados) {
    tabela.innerHTML = "";

    if (dados.length === 0) {
        tabela.innerHTML = `<tr><td colspan="4" class="text-center">Nenhum registro encontrado.</td></tr>`;
        return;
    }

    dados.forEach(item => {
        const tr = document.createElement("tr");
        tr.innerHTML = `
            <td>${formatarData(item.data)}</td>
            <td>${item.horario}</td>
            <td>${item.valor} mg/dL</td>
            <td>
                <button class="btn btn-sm btn-danger" onclick="deletar(${item.id})">Excluir</button>
            </td>
        `;
        tabela.appendChild(tr);
    });
}

// ✅ Deletar registro
function deletar(id) {
    if (!confirm("Tem certeza que deseja excluir este registro?")) return;

    fetch("backend/deletar.php", {
        method: "POST",
        body: new URLSearchParams({ id })
    })
        .then(res => res.json())
        .then(() => listar());
}

// ✅ Limpar todos os dados
function limparDados() {
    if (!confirm("Tem certeza que deseja apagar TODOS os registros?")) return;

    fetch("backend/limpar.php")
        .then(res => res.json())
        .then(() => listar());
}

// ✅ Atualizar gráfico
function atualizarGrafico(dados) {
    const agrupados = dados.reduce((acc, item) => {
        acc[item.data] = acc[item.data] || [];
        acc[item.data].push(Number(item.valor));
        return acc;
    }, {});

    const labels = Object.keys(agrupados).sort();
    const valores = labels.map(data => {
        const valoresDia = agrupados[data];
        const media = valoresDia.reduce((a, b) => a + b, 0) / valoresDia.length;
        return media.toFixed(1);
    });

    if (grafico) grafico.destroy();

    grafico = new Chart(ctx, {
        type: "bar",
        data: {
            labels,
            datasets: [{
                label: "Média de Glicemia",
                data: valores,
                backgroundColor: "#2f4858"
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    suggestedMax: 250
                }
            }
        }
    });
}

// ✅ Atualizar card de resumo (média)
function atualizarResumo(dados) {
    const resumoDiv = document.getElementById("resumo");
    const cardResumo = document.getElementById("cardResumo");

    if (dados.length === 0) {
        resumoDiv.innerText = "Sem dados";
        cardResumo.classList.remove('bg-success', 'bg-warning', 'bg-danger', 'text-white');
        cardResumo.setAttribute('title', 'Nenhum dado registrado');
        bootstrap.Tooltip.getOrCreateInstance(cardResumo).setContent({ '.tooltip-inner': 'Nenhum dado registrado' });
        return;
    }

    const media = (
        dados.reduce((acc, item) => acc + Number(item.valor), 0) / dados.length
    ).toFixed(1);

    resumoDiv.innerText = `${media} mg/dL`;

    // Remove classes anteriores
    cardResumo.classList.remove('bg-success', 'bg-warning', 'bg-danger', 'text-white');

    let mensagemTooltip = '';

    // Verifica faixa da média e aplica cor + mensagem
    if (media < 110) {
        cardResumo.classList.add('bg-success', 'text-white');
        mensagemTooltip = 'Está boa, mantenha!';
    } else if (media >= 120 && media <= 180) {
        cardResumo.classList.add('bg-warning');
        mensagemTooltip = 'Cuidado! Média alterada.';
    } else {
        cardResumo.classList.add('bg-danger', 'text-white');
        mensagemTooltip = 'Atenção! Média muito alta!';
    }

    // ✅ Atualiza tooltip
    cardResumo.setAttribute('title', mensagemTooltip);
    const tooltip = bootstrap.Tooltip.getOrCreateInstance(cardResumo);
    tooltip.setContent({ '.tooltip-inner': mensagemTooltip });
}




// ✅ Gerar relatório em PDF
async function gerarPDF() {
    // Buscar nome do usuário
    let nomeUsuario = "Usuário";
    try {
        const res = await fetch("backend/usuario.php");
        const dados = await res.json();
        nomeUsuario = dados.nome || "Usuário";
    } catch (e) {
        // Se der erro, usa o padrão
    }

    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    // Inserir logo
    const img = new Image();
    img.src = "LogoCA.png";

    img.onload = function () {
        doc.addImage(img, 'PNG', 10, 10, 30, 30);

        // Título centralizado
        doc.setFontSize(18);
        doc.setFont('helvetica', 'bold');
        doc.text("Relatório de Glicemia", 105, 20, { align: 'center' });

        // Nome do usuário logo abaixo do título
        doc.setFontSize(12);
        doc.setFont('helvetica', 'normal');
        doc.text(`De: ${nomeUsuario}`, 105, 28, { align: 'center' });

        // Data de geração
        const dataAtual = new Date().toLocaleDateString();
        doc.setFontSize(10);
        doc.text(`Data: ${dataAtual}`, 105, 34, { align: 'center' });

        // Dados para a tabela
        const linhas = registros.map(item => [
            formatarData(item.data),
            item.horario,
            `${item.valor} mg/dL`
        ]);

        // Gerar a tabela
        doc.autoTable({
            startY: 44, // Ajuste para não sobrepor o cabeçalho
            head: [["Data", "Horário", "Valor"]],
            body: linhas,
            theme: 'grid',
            styles: {
                lineWidth: 0.2,
                lineColor: [0, 0, 0],
                fontSize: 10,
                halign: 'center'
            },
            headStyles: {
                fillColor: [47, 72, 88],
                textColor: [255, 255, 255],
                fontSize: 11,
                halign: 'center'
            },
            alternateRowStyles: {
                fillColor: [240, 240, 240]
            },
            margin: { top: 44 }
        });

        // Rodapé com número de páginas
        const pageCount = doc.getNumberOfPages();
        for (let i = 1; i <= pageCount; i++) {
            doc.setPage(i);
            doc.setFontSize(8);
            doc.text(`Página ${i} de ${pageCount}`, 105, 290, { align: 'center' });
        }

        // Salvar o PDF
        let nomeArquivo = "relatorio_glicemia_" +
            nomeUsuario
                .normalize("NFD") // separa acentos
                .replace(/[\u0300-\u036f]/g, "") // remove acentos
                .replace(/\s+/g, "_") // troca espaços por _
                .replace(/[^\w\-À-ÿ_]/g, "") // permite letras acentuadas
            + ".pdf";
        doc.save(nomeArquivo);

    };
}
// ...existing code...

// ✅ Filtrar por data
function filtrar() {
    const inicio = document.getElementById("dataInicio").value;
    const fim = document.getElementById("dataFim").value;

    if (!inicio || !fim) {
        alert("Selecione as duas datas para filtrar.");
        return;
    }

    const filtrados = registros.filter(item => {
        return item.data >= inicio && item.data <= fim;
    });

    preencherTabela(filtrados);
    atualizarGrafico(filtrados);
    atualizarResumo(filtrados);
}

// ✅ Limpar filtros
function limparFiltro() {
    document.getElementById("dataInicio").value = "";
    document.getElementById("dataFim").value = "";
    listar();
}

// ✅ Utilitário para formatar data
function formatarData(data) {
    const partes = data.split("-");
    return `${partes[2]}/${partes[1]}/${partes[0]}`;
}
