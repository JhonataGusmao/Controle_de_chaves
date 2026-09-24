<?php

session_start();

require '../includes/conexao.php';
require '../valida_secao_usuario.php';

// BLOQUEIA SE NÃO FOR USUÁRIO
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['nivel'] !== 'usuario') {
    header("Location: ../index.php");
    exit;
}

$usuario_nome = $_SESSION['usuario']['nome'];


/* ------------------------------------------------
   CHAVES DISPONÍVEIS PARA RETIRAR
--------------------------------------------------*/

$chaves_disponiveis = $pdo->query("
    SELECT * FROM chaves 
    WHERE status = 'disponivel' 
    ORDER BY numero_sala
")->fetchAll();


/* ------------------------------------------------
   CHAVES PARA DEVOLVER
--------------------------------------------------*/

$chaves_para_devolver = $pdo->query("
    SELECT h.id_chave, c.descricao, c.numero_sala
    FROM historico_chaves h
    JOIN chaves c ON c.id = h.id_chave
    WHERE h.devolvido_por IS NULL
")->fetchAll();

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <title>Painel do Usuário</title>

    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap">

    <style>

        /* =====================================================
           GERAL
        ===================================================== */

        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: #0A1A2F;
            color: #fff;
        }


        /* =====================================================
           SIDEBAR
        ===================================================== */

        .sidebar {
            width: 240px;
            height: 100%;
            background: #1B3B5F;
            position: fixed;
            padding: 20px;
            box-sizing: border-box;
        }

        .sidebar h2 {
            text-align: center;
        }

        .sidebar a {
            display: block;
            color: #fff;
            padding: 12px;
            margin-top: 12px;
            background: rgba(255,255,255,0.1);
            text-decoration: none;
            border-radius: 6px;
        }


        /* =====================================================
           CONTEÚDO
        ===================================================== */

        .content {
            margin-left: 260px;
            padding: 30px;
        }

        .card {
            background: #11263E;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
        }


        /* =====================================================
           BOTÕES
        ===================================================== */

        button {
            margin-top: 15px;
            width: 100%;
            padding: 12px;
            border-radius: 6px;
            cursor: pointer;
            background: #C9A86A;
            color: #000;
            font-weight: bold;
            border: none;
        }

        button:hover {
            opacity: 0.9;
        }


        /* =====================================================
           OBSERVAÇÃO
        ===================================================== */

        .obs {
            display: none;
        }

        .obs input {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            border-radius: 6px;
            background: #1B3B5F;
            color: #fff;
            border: none;
            box-sizing: border-box;
        }


        /* =====================================================
           CAMPO PESQUISÁVEL
        ===================================================== */

        .campo-select {
            position: relative;
            width: 100%;
            margin-top: 6px;
        }


        .campo-select-input {
            width: 100%;
            padding: 12px;
            border-radius: 6px;
            background: #1B3B5F;
            color: #fff;
            border: 1px solid transparent;
            box-sizing: border-box;
            outline: none;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
        }


        .campo-select-input::placeholder {
            color: rgba(255,255,255,0.65);
        }


        .campo-select-input:focus {
            border-color: #C9A86A;
            box-shadow: 0 0 0 2px rgba(201,168,106,0.12);
        }


        /* =====================================================
           LISTA DO DROPDOWN
        ===================================================== */

        .dropdown-opcoes {
            position: absolute;
            top: calc(100% + 4px);
            left: 0;
            width: 100%;
            max-height: 220px;
            overflow-y: auto;

            background: #163653;

            border: 1px solid rgba(201,168,106,0.35);
            border-radius: 6px;

            box-shadow: 0 10px 30px rgba(0,0,0,0.35);

            z-index: 9999;

            display: none;
        }


        .dropdown-opcoes.aberto {
            display: block;
        }


        .dropdown-opcao {
            padding: 11px 12px;
            cursor: pointer;
            transition: background 0.15s ease;
            font-size: 14px;
        }


        .dropdown-opcao:hover {
            background: rgba(201,168,106,0.18);
        }


        .dropdown-vazio {
            padding: 12px;
            color: rgba(255,255,255,0.65);
            font-size: 14px;
        }


        /* =====================================================
           SETA DO CAMPO
        ===================================================== */

        .campo-select::after {
            content: "⌄";
            position: absolute;
            right: 13px;
            top: 9px;
            color: #C9A86A;
            font-size: 20px;
            pointer-events: none;
        }


        /* =====================================================
           RESPONSIVO
        ===================================================== */

        @media (max-width: 700px) {

            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }

            .content {
                margin-left: 0;
                padding: 20px;
            }

        }

    </style>

</head>


<body>


<!-- =========================================================
     SIDEBAR
========================================================= -->

<div class="sidebar">

    <h2>Usuário</h2>

    <a href="../index.php">
        🚪 Sair
    </a>

</div>


<!-- =========================================================
     CONTEÚDO
========================================================= -->

<div class="content">

    <h1>
        Bem vindo, <?= htmlspecialchars($usuario_nome) ?>
    </h1>


    <!-- =====================================================
         RETIRAR CHAVE
    ====================================================== -->

    <div class="card">

        <h2>Retirar Chave</h2>


        <form action="usuario_retirar.php" method="POST">


            <label>
                Selecione a chave disponível:
            </label>


            <!-- =================================================
                 CHAVE RETIRAR
            ================================================== -->

            <div class="campo-select">

                <input
                    type="text"
                    id="chave_retirar_busca"
                    class="campo-select-input"
                    placeholder="🔎 Selecione ou digite a chave..."
                    autocomplete="off"
                    required
                >


                <div
                    id="chave_retirar_opcoes"
                    class="dropdown-opcoes"
                >

                    <?php foreach ($chaves_disponiveis as $c): ?>

                        <div
                            class="dropdown-opcao"
                            data-value="<?= htmlspecialchars($c['id']) ?>"
                            data-text="<?= htmlspecialchars(
                                $c['descricao'] . ' (Sala ' . $c['numero_sala'] . ')'
                            ) ?>"
                        >

                            <?= htmlspecialchars($c['descricao']) ?>

                            (Sala <?= htmlspecialchars($c['numero_sala']) ?>)

                        </div>

                    <?php endforeach; ?>


                    <?php if (empty($chaves_disponiveis)): ?>

                        <div class="dropdown-vazio">
                            Nenhuma chave disponível.
                        </div>

                    <?php endif; ?>

                </div>

            </div>


            <!-- CAMPO REAL ENVIADO PARA O PHP -->

            <input
                type="hidden"
                name="chave_id"
                id="chave_retirar"
            >


            <label>
                Pessoa Autorizada:
            </label>


            <!-- =================================================
                 AUTORIZADO RETIRAR
            ================================================== -->

            <div class="campo-select">

                <input
                    type="text"
                    id="autorizado_retirar_busca"
                    class="campo-select-input"
                    placeholder="🔎 Selecione ou digite o nome..."
                    autocomplete="off"
                    required
                    disabled
                >


                <div
                    id="autorizado_retirar_opcoes"
                    class="dropdown-opcoes"
                >

                    <div class="dropdown-vazio">
                        Selecione a chave primeiro...
                    </div>

                </div>

            </div>


            <!-- CAMPO REAL ENVIADO PARA O PHP -->

            <input
                type="hidden"
                name="autorizado"
                id="autorizados_retirar"
            >


            <!-- OBSERVAÇÃO -->

            <div class="obs" id="obs_retirar">

                <label>
                    Observação
                </label>

                <input
                    type="text"
                    name="observacao"
                    placeholder="Informe o nome ou observação"
                >

            </div>


            <button type="submit">
                Retirar Chave
            </button>

        </form>

    </div>



    <!-- =====================================================
         DEVOLVER CHAVE
    ====================================================== -->

    <div class="card">

        <h2>Devolver Chave</h2>


        <form action="usuario_devolver.php" method="POST">


            <label>
                Selecione a chave a devolver:
            </label>


            <!-- =================================================
                 CHAVE DEVOLVER
            ================================================== -->

            <div class="campo-select">

                <input
                    type="text"
                    id="chave_devolver_busca"
                    class="campo-select-input"
                    placeholder="🔎 Selecione ou digite a chave..."
                    autocomplete="off"
                    required
                >


                <div
                    id="chave_devolver_opcoes"
                    class="dropdown-opcoes"
                >

                    <?php foreach ($chaves_para_devolver as $e): ?>

                        <div
                            class="dropdown-opcao"
                            data-value="<?= htmlspecialchars($e['id_chave']) ?>"
                            data-text="<?= htmlspecialchars(
                                $e['descricao'] . ' (Sala ' . $e['numero_sala'] . ')'
                            ) ?>"
                        >

                            <?= htmlspecialchars($e['descricao']) ?>

                            (Sala <?= htmlspecialchars($e['numero_sala']) ?>)

                        </div>

                    <?php endforeach; ?>


                    <?php if (empty($chaves_para_devolver)): ?>

                        <div class="dropdown-vazio">
                            Nenhuma chave para devolver.
                        </div>

                    <?php endif; ?>

                </div>

            </div>


            <!-- CAMPO REAL ENVIADO PARA O PHP -->

            <input
                type="hidden"
                name="chave_id"
                id="chave_devolver"
            >


            <label>
                Pessoa Autorizada:
            </label>


            <!-- =================================================
                 AUTORIZADO DEVOLVER
            ================================================== -->

            <div class="campo-select">

                <input
                    type="text"
                    id="autorizado_devolver_busca"
                    class="campo-select-input"
                    placeholder="🔎 Selecione ou digite o nome..."
                    autocomplete="off"
                    required
                    disabled
                >


                <div
                    id="autorizado_devolver_opcoes"
                    class="dropdown-opcoes"
                >

                    <div class="dropdown-vazio">
                        Selecione a chave primeiro...
                    </div>

                </div>

            </div>


            <!-- CAMPO REAL ENVIADO PARA O PHP -->

            <input
                type="hidden"
                name="autorizado"
                id="autorizados_devolver"
            >


            <!-- OBSERVAÇÃO -->

            <div class="obs" id="obs_devolver">

                <label>
                    Observação
                </label>

                <input
                    type="text"
                    name="observacao"
                    placeholder="Informe o nome ou observação"
                >

            </div>


            <button type="submit">
                Devolver
            </button>

        </form>

    </div>

</div>



<script>


/* ============================================================
   NORMALIZAR TEXTO
   Ignora acentos e diferença entre maiúsculas/minúsculas
============================================================ */

function normalizarTexto(texto) {

    return texto
        .toLowerCase()
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "");

}



/* ============================================================
   DROPDOWN DE CHAVES
============================================================ */

function criarPesquisaChave(inputId, dropdownId, hiddenId) {

    const input = document.getElementById(inputId);
    const dropdown = document.getElementById(dropdownId);
    const hidden = document.getElementById(hiddenId);


    function abrir() {

        dropdown.classList.add("aberto");

        filtrar();

    }


    function fechar() {

        dropdown.classList.remove("aberto");

    }


    function filtrar() {

        const texto = normalizarTexto(input.value.trim());

        const opcoes = dropdown.querySelectorAll(".dropdown-opcao");

        let encontrou = false;


        opcoes.forEach(opcao => {

            const textoOpcao = normalizarTexto(
                opcao.dataset.text || opcao.textContent
            );


            if (texto === "" || textoOpcao.includes(texto)) {

                opcao.style.display = "block";

                encontrou = true;

            } else {

                opcao.style.display = "none";

            }

        });


        let vazio = dropdown.querySelector(".dropdown-vazio-filtro");


        if (!encontrou) {

            if (!vazio) {

                vazio = document.createElement("div");

                vazio.className = "dropdown-vazio dropdown-vazio-filtro";

                vazio.textContent = "Nenhuma chave encontrada.";

                dropdown.appendChild(vazio);

            }

        } else if (vazio) {

            vazio.remove();

        }

    }


    input.addEventListener("focus", abrir);

    input.addEventListener("input", function() {

        /*
         * Quando o usuário começa a digitar uma nova busca,
         * o valor anterior deixa de ser considerado selecionado.
         */

        hidden.value = "";

        abrir();

    });


    dropdown.addEventListener("click", function(event) {

        const opcao = event.target.closest(".dropdown-opcao");

        if (!opcao) {
            return;
        }


        input.value = opcao.dataset.text;

        hidden.value = opcao.dataset.value;

        fechar();


        /*
         * Dispara o evento para carregar os autorizados.
         */

        hidden.dispatchEvent(new Event("change"));

    });


    document.addEventListener("click", function(event) {

        if (!event.target.closest(".campo-select")) {

            fechar();

        }

    });

}



/* ============================================================
   DROPDOWN DE AUTORIZADOS
============================================================ */

function criarPesquisaAutorizado(
    inputId,
    dropdownId,
    hiddenId,
    obsId
) {

    const input = document.getElementById(inputId);
    const dropdown = document.getElementById(dropdownId);
    const hidden = document.getElementById(hiddenId);


    function abrir() {

        if (input.disabled) {
            return;
        }

        dropdown.classList.add("aberto");

        filtrar();

    }


    function fechar() {

        dropdown.classList.remove("aberto");

    }


    function filtrar() {

        const texto = normalizarTexto(input.value.trim());

        const opcoes = dropdown.querySelectorAll(".dropdown-opcao");

        let encontrou = false;


        opcoes.forEach(opcao => {

            const textoOpcao = normalizarTexto(
                opcao.dataset.text || opcao.textContent
            );


            if (
                texto === "" ||
                textoOpcao.includes(texto) ||
                opcao.dataset.value === "OUTROS"
            ) {

                opcao.style.display = "block";

                encontrou = true;

            } else {

                opcao.style.display = "none";

            }

        });


        let vazio = dropdown.querySelector(".dropdown-vazio-filtro");


        if (!encontrou) {

            if (!vazio) {

                vazio = document.createElement("div");

                vazio.className = "dropdown-vazio dropdown-vazio-filtro";

                vazio.textContent = "Nenhuma pessoa encontrada.";

                dropdown.appendChild(vazio);

            }

        } else if (vazio) {

            vazio.remove();

        }

    }


    input.addEventListener("focus", abrir);


    input.addEventListener("input", function() {

        hidden.value = "";

        abrir();

    });


    dropdown.addEventListener("click", function(event) {

        const opcao = event.target.closest(".dropdown-opcao");

        if (!opcao) {
            return;
        }


        input.value = opcao.dataset.text;

        hidden.value = opcao.dataset.value;

        fechar();


        /*
         * Controla o campo de observação quando for OUTROS.
         */

        controlarObservacao(
            hidden,
            obsId
        );

    });


    document.addEventListener("click", function(event) {

        if (!event.target.closest(".campo-select")) {

            fechar();

        }

    });

}



/* ============================================================
   OBSERVAÇÃO
============================================================ */

function controlarObservacao(hidden, obsId) {

    const obs = document.getElementById(obsId);

    if (!obs) {
        return;
    }


    const input = obs.querySelector("input");


    if (hidden.value === "OUTROS") {

        obs.style.display = "block";

        input.required = true;

    } else {

        obs.style.display = "none";

        input.required = false;

        input.value = "";

    }

}



/* ============================================================
   CARREGAR AUTORIZADOS
============================================================ */

function carregarAutorizados(
    chaveHiddenId,
    autorizadoInputId,
    autorizadoDropdownId,
    autorizadoHiddenId,
    obsId
) {

    const chaveHidden = document.getElementById(chaveHiddenId);

    const autorizadoInput = document.getElementById(
        autorizadoInputId
    );

    const autorizadoDropdown = document.getElementById(
        autorizadoDropdownId
    );

    const autorizadoHidden = document.getElementById(
        autorizadoHiddenId
    );


    chaveHidden.addEventListener("change", function() {

        const chaveId = this.value;


        // Limpa autorização anterior

        autorizadoInput.value = "";

        autorizadoHidden.value = "";

        autorizadoInput.disabled = true;

        autorizadoDropdown.innerHTML =
            '<div class="dropdown-vazio">Carregando...</div>';


        if (!chaveId) {

            autorizadoDropdown.innerHTML =
                '<div class="dropdown-vazio">Selecione a chave primeiro...</div>';

            return;

        }


        fetch("buscar_autorizados.php?id=" + encodeURIComponent(chaveId))

            .then(res => res.json())

            .then(lista => {

                autorizadoDropdown.innerHTML = "";


                if (!Array.isArray(lista) || lista.length === 0) {

                    autorizadoDropdown.innerHTML =
                        '<div class="dropdown-vazio">Nenhuma pessoa autorizada encontrada.</div>';

                }


                lista.forEach(nome => {

                    const opcao = document.createElement("div");

                    opcao.className = "dropdown-opcao";

                    opcao.dataset.value = nome;

                    opcao.dataset.text = nome;

                    opcao.textContent = nome;

                    autorizadoDropdown.appendChild(opcao);

                });


                // Mantém a opção OUTROS

                const outros = document.createElement("div");

                outros.className = "dropdown-opcao";

                outros.dataset.value = "OUTROS";

                outros.dataset.text = "OUTROS";

                outros.textContent = "OUTROS";

                autorizadoDropdown.appendChild(outros);


                autorizadoInput.disabled = false;

            })

            .catch(() => {

                autorizadoDropdown.innerHTML =
                    '<div class="dropdown-vazio">Erro ao carregar autorizados.</div>';

            });

    });

}



/* ============================================================
   INICIALIZAÇÃO - CHAVES
============================================================ */

criarPesquisaChave(
    "chave_retirar_busca",
    "chave_retirar_opcoes",
    "chave_retirar"
);


criarPesquisaChave(
    "chave_devolver_busca",
    "chave_devolver_opcoes",
    "chave_devolver"
);



/* ============================================================
   INICIALIZAÇÃO - AUTORIZADOS
============================================================ */

criarPesquisaAutorizado(
    "autorizado_retirar_busca",
    "autorizado_retirar_opcoes",
    "autorizados_retirar",
    "obs_retirar"
);


criarPesquisaAutorizado(
    "autorizado_devolver_busca",
    "autorizado_devolver_opcoes",
    "autorizados_devolver",
    "obs_devolver"
);



/* ============================================================
   CARREGAR AUTORIZADOS - RETIRAR
============================================================ */

carregarAutorizados(
    "chave_retirar",
    "autorizado_retirar_busca",
    "autorizado_retirar_opcoes",
    "autorizados_retirar",
    "obs_retirar"
);



/* ============================================================
   CARREGAR AUTORIZADOS - DEVOLVER
============================================================ */

carregarAutorizados(
    "chave_devolver",
    "autorizado_devolver_busca",
    "autorizado_devolver_opcoes",
    "autorizados_devolver",
    "obs_devolver"
);

</script>


</body>

</html>