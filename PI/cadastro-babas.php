<?php

include 'include/conexao.php';

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome = trim($_POST['nome']);
    $sobrenome = trim($_POST['sobrenome']);
    $cpf = trim($_POST['cpf']);
    $data_nascimento = $_POST['data_nascimento'];
    $sexo = $_POST['sexo'];
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];
    $telefone = trim($_POST['telefone']);

    // ENDEREÇO
    $cep = trim($_POST['cep']);
    $estado = trim($_POST['estado']);
    $cidade = trim($_POST['cidade']);
    $bairro = trim($_POST['bairro']);
    $rua = trim($_POST['rua']);
    $numero = trim($_POST['numero']);
    $complemento = trim($_POST['complemento']);

    // EXPERIÊNCIA
    $experiencia = trim($_POST['experiencia']);

    // DISPONIBILIDADE
    $disponibilidades = $_POST['disponibilidade'] ?? [];

    // PREFERÊNCIAS
    $preferencias = $_POST['preferencias'] ?? [];

    // FOTO
    $foto = $_FILES['foto'] ?? null;


    // =========================
    // VALIDAÇÕES
    // =========================

    if ($senha !== $confirmar_senha) {

        $erro = 'As senhas não coincidem.';
    } elseif (!$foto || $foto['error'] !== UPLOAD_ERR_OK) {

        $erro = 'A foto de perfil é obrigatória.';
    } elseif (empty($disponibilidades)) {

        $erro = 'Selecione pelo menos um período de disponibilidade.';
    } elseif (empty($preferencias)) {

        $erro = 'Selecione pelo menos uma preferência.';
    } else {

        // Verifica se CPF ou e-mail já existem

        $stmt = $conexao->prepare(
            "SELECT id_baba 
             FROM BABA 
             WHERE cpf = ? OR email = ?"
        );

        $stmt->bind_param("ss", $cpf, $email);
        $stmt->execute();

        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {

            $erro = 'CPF ou e-mail já cadastrado.';
        } else {

            try {

                $conexao->begin_transaction();


                // =========================
                // FOTO
                // =========================

                $pasta = 'uploads/babas/';

                if (!is_dir($pasta)) {
                    mkdir($pasta, 0777, true);
                }

                $extensao = strtolower(
                    pathinfo($foto['name'], PATHINFO_EXTENSION)
                );

                $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'webp'];

                if (!in_array($extensao, $extensoesPermitidas)) {
                    throw new Exception('Formato de imagem inválido.');
                }

                $nomeFoto = uniqid('baba_') . '.' . $extensao;

                $caminhoFoto = $pasta . $nomeFoto;

                if (!move_uploaded_file($foto['tmp_name'], $caminhoFoto)) {
                    throw new Exception('Erro ao salvar a foto.');
                }


                // =========================
                // ENDEREÇO
                // =========================

                $stmtEndereco = $conexao->prepare(
                    "INSERT INTO ENDERECO
                    (cep, estado, cidade, bairro, rua, numero, complemento)
                    VALUES (?, ?, ?, ?, ?, ?, ?)"
                );

                $stmtEndereco->bind_param(
                    "sssssss",
                    $cep,
                    $estado,
                    $cidade,
                    $bairro,
                    $rua,
                    $numero,
                    $complemento
                );

                $stmtEndereco->execute();

                $id_endereco = $conexao->insert_id;


                // =========================
                // BABA
                // =========================

                $senhaHash = password_hash(
                    $senha,
                    PASSWORD_DEFAULT
                );

                $stmtBaba = $conexao->prepare(
                    "INSERT INTO BABA
                    (
                        nome,
                        sobrenome,
                        cpf,
                        data_nascimento,
                        sexo,
                        email,
                        senha,
                        telefone,
                        foto,
                        experiencia,
                        id_endereco
                    )
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
                );

                $stmtBaba->bind_param(
                    "ssssssssssi",
                    $nome,
                    $sobrenome,
                    $cpf,
                    $data_nascimento,
                    $sexo,
                    $email,
                    $senhaHash,
                    $telefone,
                    $caminhoFoto,
                    $experiencia,
                    $id_endereco
                );

                $stmtBaba->execute();

                $id_baba = $conexao->insert_id;


                // =========================
                // DISPONIBILIDADE
                // =========================

                $stmtDisponibilidade = $conexao->prepare(
                    "INSERT INTO DISPONIBILIDADE
                    (id_baba, horario)
                    VALUES (?, ?)"
                );

                foreach ($disponibilidades as $horario) {

                    $stmtDisponibilidade->bind_param(
                        "is",
                        $id_baba,
                        $horario
                    );

                    $stmtDisponibilidade->execute();
                }


                // =========================
                // PREFERÊNCIAS
                // =========================

                $stmtPreferencia = $conexao->prepare(
                    "INSERT INTO BABA_PREFERENCIA
                    (id_baba, id_preferencia)
                    VALUES (?, ?)"
                );

                foreach ($preferencias as $id_preferencia) {

                    $id_preferencia = (int) $id_preferencia;

                    $stmtPreferencia->bind_param(
                        "ii",
                        $id_baba,
                        $id_preferencia
                    );

                    $stmtPreferencia->execute();
                }


                // =========================
                // FINALIZA
                // =========================

                $conexao->commit();

                $sucesso = 'Cadastro realizado com sucesso!';
            } catch (Exception $e) {

                $conexao->rollback();

                $erro = 'Não foi possível realizar o cadastro.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Cadastro de Babá</title>

    <link rel="stylesheet" href="CSS/cadastro_babas.css">

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>

<body>


    <!-- NAVBAR -->

    <nav class="nav_inteira">

        <div class="minha_navbar">

            <div class="logo">

                <a href="index.html">

                    <img src="./img/teste.png" alt="Logo">

                </a>

            </div>

            <div class="menu_direito">

                <div class="icone">

                    <a href="login.html" title="Entrar como responsável">
                        <i class="fa-solid fa-user"></i>
                    </a>

                    <a href="login-baba.php" title="Entrar como babá">
                        <i class="fa-solid fa-user-nurse"></i>
                    </a>

                </div>

                <div class="hamburguer" onclick="abrirMenu()">

                    <i class="fa-solid fa-bars"></i>

                </div>

            </div>

        </div>


        <div class="mini_nav">

            <ul class="menu_mini">

                <li>
                    <a href="guias.html">Guia para bebês</a>
                </li>

                <li>
                    <a href="favoritos.html">Favoritos</a>
                </li>

                <li>
                    <a href="babas.html">Babás</a>
                </li>

                <li>
                    <a href="ajuda.html">Ajuda</a>
                </li>

                <li>
                    <a href="sua_localizacao.html">Sua localização</a>
                </li>

                <li>
                    <a href="historico_contrato.html">Histórico</a>
                </li>

                <li>
                    <a href="sobre_nos.htm">Sobre Nós</a>
                </li>

            </ul>

        </div>

    </nav>


    <!-- CADASTRO -->

    <div class="container">

        <div class="card">

            <h2>Criar Conta</h2>

            <p class="subtitulo">
                Cadastre-se como babá para oferecer seus serviços.
            </p>


            <?php if ($erro): ?>

                <div class="mensagem erro">
                    <?= htmlspecialchars($erro) ?>
                </div>

            <?php endif; ?>


            <?php if ($sucesso): ?>

                <div class="mensagem sucesso">
                    <?= htmlspecialchars($sucesso) ?>
                </div>

            <?php endif; ?>


            <form method="POST"
                enctype="multipart/form-data">


                <!-- DADOS PESSOAIS -->

                <h3>Dados pessoais</h3>

                <div class="form-grid">

                    <div class="campo">

                        <label>Nome</label>

                        <input
                            type="text"
                            name="nome"
                            placeholder="Digite seu nome"
                            required>

                    </div>


                    <div class="campo">

                        <label>Sobrenome</label>

                        <input
                            type="text"
                            name="sobrenome"
                            placeholder="Digite seu sobrenome"
                            required>

                    </div>


                    <div class="campo">

                        <label>CPF</label>

                        <input
                            type="text"
                            name="cpf"
                            placeholder="000.000.000-00"
                            required>

                    </div>


                    <div class="campo">

                        <label>Data de nascimento</label>

                        <input
                            type="date"
                            name="data_nascimento"
                            required>

                    </div>


                    <div class="campo">

                        <label>Sexo</label>

                        <select name="sexo" required>

                            <option value="">
                                Selecione
                            </option>

                            <option value="masculino">
                                Masculino
                            </option>

                            <option value="feminino">
                                Feminino
                            </option>

                            <option value="outro">
                                Outro
                            </option>

                        </select>

                    </div>


                    <div class="campo">

                        <label>Telefone</label>

                        <input
                            type="tel"
                            name="telefone"
                            placeholder="(11) 99999-9999"
                            required>

                    </div>


                    <div class="campo campo-full">

                        <label>E-mail</label>

                        <input
                            type="email"
                            name="email"
                            placeholder="email@exemplo.com"
                            required>

                    </div>


                    <div class="campo">

                        <label>Senha</label>

                        <input
                            type="password"
                            name="senha"
                            placeholder="Digite sua senha"
                            required>

                    </div>


                    <div class="campo">

                        <label>Confirmar senha</label>

                        <input
                            type="password"
                            name="confirmar_senha"
                            placeholder="Digite novamente"
                            required>

                    </div>


                    <!-- FOTO -->

                    <div class="campo campo-full">

                        <label>Foto de perfil</label>

                        <input
                            type="file"
                            name="foto"
                            accept=".jpg,.jpeg,.png,.webp"
                            required>

                    </div>

                </div>


                <!-- ENDEREÇO -->

                <h3>Endereço</h3>

                <div class="form-grid">

                    <div class="campo">

                        <label>CEP</label>

                        <input
                            type="text"
                            name="cep"
                            placeholder="00000-000"
                            required>

                    </div>


                    <div class="campo">

                        <label>Estado</label>

                        <input
                            type="text"
                            name="estado"
                            placeholder="São Paulo"
                            required>

                    </div>


                    <div class="campo">

                        <label>Cidade</label>

                        <input
                            type="text"
                            name="cidade"
                            placeholder="Sua cidade"
                            required>

                    </div>


                    <div class="campo">

                        <label>Bairro</label>

                        <input
                            type="text"
                            name="bairro"
                            placeholder="Seu bairro"
                            required>

                    </div>


                    <div class="campo campo-full">

                        <label>Rua</label>

                        <input
                            type="text"
                            name="rua"
                            placeholder="Nome da rua"
                            required>

                    </div>


                    <div class="campo">

                        <label>Número</label>

                        <input
                            type="text"
                            name="numero"
                            placeholder="Número">

                    </div>


                    <div class="campo">

                        <label>Complemento</label>

                        <input
                            type="text"
                            name="complemento"
                            placeholder="Apartamento, bloco...">

                    </div>

                </div>


                <!-- EXPERIÊNCIA -->

                <h3>Experiência</h3>

                <div class="campo">

                    <label>
                        Conte um pouco sobre sua experiência
                    </label>

                    <textarea
                        name="experiencia"
                        placeholder="Conte sobre sua experiência como babá..."
                        rows="5"></textarea>

                </div>


                <!-- DISPONIBILIDADE -->

                <h3>Disponibilidade</h3>

                <p>
                    Selecione os períodos em que você trabalha:
                </p>

                <div class="opcoes">

                    <label>

                        <input
                            type="checkbox"
                            name="disponibilidade[]"
                            value="manha">

                        Manhã

                    </label>


                    <label>

                        <input
                            type="checkbox"
                            name="disponibilidade[]"
                            value="tarde">

                        Tarde

                    </label>


                    <label>

                        <input
                            type="checkbox"
                            name="disponibilidade[]"
                            value="noite">

                        Noite

                    </label>

                </div>


                <!-- PREFERÊNCIAS -->

                <h3>Preferências</h3>

                <p>
                    Selecione as opções que correspondem aos serviços que você oferece:
                </p>


                <div class="opcoes">

                    <?php

                    $consultaPreferencias = $conexao->query(
                        "SELECT id_preferencia, descricao
                     FROM PREFERENCIA
                     ORDER BY descricao"
                    );

                    while ($preferencia = $consultaPreferencias->fetch_assoc()):

                    ?>

                        <label>

                            <input
                                type="checkbox"
                                name="preferencias[]"
                                value="<?= $preferencia['id_preferencia'] ?>">

                            <?= htmlspecialchars($preferencia['descricao']) ?>

                        </label>

                    <?php endwhile; ?>

                </div>


                <!-- TERMOS -->

                <div class="termos">

                    <input
                        type="checkbox"
                        required>

                    <label>

                        Li e aceito os Termos de Uso
                        e a Política de Privacidade.

                    </label>

                </div>


                <button type="submit">

                    Criar Conta

                </button>

            </form>


            <div class="login">

                Já possui uma conta?

                <br><br>

                <a href="login-baba.php">
                    Entrar
                </a>

            </div>

        </div>

    </div>


    <script src="js/index.js"></script>

</body>

</html>