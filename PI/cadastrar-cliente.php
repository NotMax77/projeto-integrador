<?php

include 'include/conexao.php';
include 'include/navbar_publica.php';

$erro = '';
$sucesso = '';


// ========================================
// VALIDAR CPF
// ========================================

function validarCPF($cpf)
{
    $cpf = preg_replace('/\D/', '', $cpf);

    if (strlen($cpf) !== 11) {
        return false;
    }

    // Impede números repetidos
    if (preg_match('/^(\d)\1{10}$/', $cpf)) {
        return false;
    }

    // Primeiro dígito
    $soma = 0;

    for ($i = 0; $i < 9; $i++) {
        $soma += (int)$cpf[$i] * (10 - $i);
    }

    $resto = $soma % 11;

    $digito1 = ($resto < 2) ? 0 : 11 - $resto;

    if ($digito1 != (int)$cpf[9]) {
        return false;
    }

    // Segundo dígito
    $soma = 0;

    for ($i = 0; $i < 10; $i++) {
        $soma += (int)$cpf[$i] * (11 - $i);
    }

    $resto = $soma % 11;

    $digito2 = ($resto < 2) ? 0 : 11 - $resto;

    if ($digito2 != (int)$cpf[10]) {
        return false;
    }

    return true;
}


// ========================================
// VALIDAR IDADE
// ========================================

function validarIdade($data)
{
    if (empty($data)) {
        return false;
    }

    $nascimento = new DateTime($data);
    $hoje = new DateTime();

    $idade = $hoje->diff($nascimento)->y;

    return $idade >= 18;
}


// ========================================
// CADASTRO
// ========================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ====================================
    // DADOS PESSOAIS
    // ====================================

    $nome = trim($_POST['nome'] ?? '');

    $sobrenome = trim(
        $_POST['sobrenome'] ?? ''
    );

    $cpf = preg_replace(
        '/\D/',
        '',
        $_POST['cpf'] ?? ''
    );

    $data_nascimento =
        $_POST['data_nascimento'] ?? '';

    $sexo =
        $_POST['sexo'] ?? '';

    $email = trim(
        $_POST['email'] ?? ''
    );

    $senha =
        $_POST['senha'] ?? '';

    $confirmar_senha =
        $_POST['confirmar_senha'] ?? '';

    $telefone = trim(
        $_POST['telefone'] ?? ''
    );


    // ====================================
    // ENDEREÇO
    // ====================================

    $cep = trim(
        $_POST['cep'] ?? ''
    );

    $estado = trim(
        $_POST['estado'] ?? ''
    );

    $cidade = trim(
        $_POST['cidade'] ?? ''
    );

    $bairro = trim(
        $_POST['bairro'] ?? ''
    );

    $rua = trim(
        $_POST['rua'] ?? ''
    );

    $numero = trim(
        $_POST['numero'] ?? ''
    );

    $complemento = trim(
        $_POST['complemento'] ?? ''
    );


    // ====================================
    // FOTO
    // ====================================

    $foto = $_FILES['foto'] ?? null;


    // ====================================
    // VALIDAÇÕES
    // ====================================

    if (
        empty($nome) ||
        empty($sobrenome) ||
        empty($cpf) ||
        empty($data_nascimento) ||
        empty($sexo) ||
        empty($email) ||
        empty($senha) ||
        empty($confirmar_senha) ||
        empty($telefone)
    ) {

        $erro =
            'Preencha todos os dados pessoais obrigatórios.';
    } elseif (!validarCPF($cpf)) {

        $erro =
            'O CPF informado é inválido.';
    } elseif (!validarIdade($data_nascimento)) {

        $erro =
            'É necessário ter pelo menos 18 anos.';
    } elseif (strlen($senha) < 6) {

        $erro =
            'A senha deve ter no mínimo 6 caracteres.';
    } elseif (
        !preg_match(
            '/[^A-Za-z0-9]/',
            $senha
        )
    ) {

        $erro =
            'A senha deve possuir pelo menos 1 caractere especial.';
    } elseif ($senha !== $confirmar_senha) {

        $erro =
            'As senhas não coincidem.';
    } elseif (
        empty($cep) ||
        empty($estado) ||
        empty($cidade) ||
        empty($bairro) ||
        empty($rua)
    ) {

        $erro =
            'Preencha todos os dados obrigatórios do endereço.';
    } elseif (
        !$foto ||
        $foto['error'] !== UPLOAD_ERR_OK
    ) {

        $erro =
            'A foto de perfil é obrigatória.';
    } else {

        // ====================================
        // VERIFICAR CPF / EMAIL
        // ====================================

        $stmt = $conexao->prepare(
            "SELECT id_cliente
             FROM CLIENTE
             WHERE cpf = ? OR email = ?"
        );

        $stmt->bind_param(
            "ss",
            $cpf,
            $email
        );

        $stmt->execute();

        $resultado =
            $stmt->get_result();


        if ($resultado->num_rows > 0) {

            $erro =
                'CPF ou e-mail já cadastrado.';
        } else {

            try {

                // ====================================
                // INICIAR TRANSAÇÃO
                // ====================================

                $conexao->begin_transaction();


                // ====================================
                // FOTO
                // ====================================

                $pasta =
                    'uploads/clientes/';

                if (!is_dir($pasta)) {

                    mkdir(
                        $pasta,
                        0777,
                        true
                    );
                }


                $extensao = strtolower(
                    pathinfo(
                        $foto['name'],
                        PATHINFO_EXTENSION
                    )
                );


                $extensoesPermitidas = [
                    'jpg',
                    'jpeg',
                    'png',
                    'webp'
                ];


                if (
                    !in_array(
                        $extensao,
                        $extensoesPermitidas
                    )
                ) {

                    throw new Exception(
                        'Formato de imagem inválido.'
                    );
                }


                // Máximo de 5 MB

                if (
                    $foto['size'] >
                    5 * 1024 * 1024
                ) {

                    throw new Exception(
                        'A foto deve ter no máximo 5 MB.'
                    );
                }


                $nomeFoto =
                    uniqid('cliente_') .
                    '.' .
                    $extensao;


                $caminhoFoto =
                    $pasta .
                    $nomeFoto;


                if (
                    !move_uploaded_file(
                        $foto['tmp_name'],
                        $caminhoFoto
                    )
                ) {

                    throw new Exception(
                        'Erro ao salvar a foto.'
                    );
                }


                // ====================================
                // ENDEREÇO
                // ====================================

                $stmtEndereco =
                    $conexao->prepare(
                        "INSERT INTO ENDERECO
                        (
                            cep,
                            estado,
                            cidade,
                            bairro,
                            rua,
                            numero,
                            complemento
                        )
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


                if (
                    !$stmtEndereco->execute()
                ) {

                    throw new Exception(
                        'Erro ao cadastrar o endereço.'
                    );
                }


                $id_endereco =
                    $conexao->insert_id;


                // ====================================
                // SENHA
                // ====================================

                $senhaHash =
                    password_hash(
                        $senha,
                        PASSWORD_DEFAULT
                    );


                // ====================================
                // CLIENTE
                // ====================================

                $stmtCliente =
                    $conexao->prepare(
                        "INSERT INTO CLIENTE
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
                            id_endereco
                        )
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
                    );


                $stmtCliente->bind_param(
                    "sssssssssi",
                    $nome,
                    $sobrenome,
                    $cpf,
                    $data_nascimento,
                    $sexo,
                    $email,
                    $senhaHash,
                    $telefone,
                    $caminhoFoto,
                    $id_endereco
                );


                if (
                    !$stmtCliente->execute()
                ) {

                    throw new Exception(
                        'Erro ao cadastrar o cliente.'
                    );
                }


                // ====================================
                // FINALIZAR
                // ====================================

                $conexao->commit();

                $sucesso =
                    'Cadastro realizado com sucesso!';
            } catch (Exception $e) {

                // Desfaz os INSERTs
                $conexao->rollback();


                // Remove a foto se o cadastro falhar
                if (
                    isset($caminhoFoto) &&
                    file_exists($caminhoFoto)
                ) {

                    unlink($caminhoFoto);
                }


                $erro =
                    $e->getMessage();
            }
        }
    }
}

?>

<!DOCTYPE html>

<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Cadastro de Cliente</title>


    <!-- MESMO CSS DAS BABÁS -->

    <link
        rel="stylesheet"
        href="css/cadastro_babas.css">


    <link
        rel="stylesheet"
        href="css/navbar.css">


    <link
        rel="stylesheet"
        href="CSS/footer.css">


    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>


<body>


    <div class="container">

        <div class="card">


            <h2>Criar Conta</h2>


            <p class="subtitulo">

                Cadastre-se para encontrar
                as melhores babás.

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


            <form
                method="POST"
                enctype="multipart/form-data"
                id="formCadastroCliente">


                <!-- =================================
                 DADOS PESSOAIS
            ================================== -->

                <h3>Dados pessoais</h3>


                <div class="form-grid">


                    <div class="campo">

                        <label>Nome</label>

                        <input
                            type="text"
                            name="nome"
                            placeholder="Digite seu nome"
                            value="<?= htmlspecialchars(
                                        $_POST['nome'] ?? ''
                                    ) ?>"
                            required>

                    </div>


                    <div class="campo">

                        <label>Sobrenome</label>

                        <input
                            type="text"
                            name="sobrenome"
                            placeholder="Digite seu sobrenome"
                            value="<?= htmlspecialchars(
                                        $_POST['sobrenome'] ?? ''
                                    ) ?>"
                            required>

                    </div>


                    <div class="campo">

                        <label>CPF</label>

                        <input
                            type="text"
                            name="cpf"
                            id="cpf"
                            placeholder="000.000.000-00"
                            maxlength="14"
                            value="<?= htmlspecialchars(
                                        $_POST['cpf'] ?? ''
                                    ) ?>"
                            required>

                    </div>


                    <div class="campo">

                        <label>
                            Data de nascimento
                        </label>

                        <input
                            type="date"
                            name="data_nascimento"
                            id="data_nascimento"
                            value="<?= htmlspecialchars(
                                        $_POST['data_nascimento'] ?? ''
                                    ) ?>"
                            required>

                    </div>


                    <div class="campo">

                        <label>Sexo</label>

                        <select
                            name="sexo"
                            required>

                            <option value="">
                                Selecione
                            </option>

                            <option
                                value="masculino"
                                <?= (
                                    ($_POST['sexo'] ?? '') ===
                                    'masculino'
                                ) ? 'selected' : '' ?>>
                                Masculino
                            </option>

                            <option
                                value="feminino"
                                <?= (
                                    ($_POST['sexo'] ?? '') ===
                                    'feminino'
                                ) ? 'selected' : '' ?>>
                                Feminino
                            </option>

                            <option
                                value="outro"
                                <?= (
                                    ($_POST['sexo'] ?? '') ===
                                    'outro'
                                ) ? 'selected' : '' ?>>
                                Outro
                            </option>

                        </select>

                    </div>


                    <div class="campo">

                        <label>Telefone</label>

                        <input
                            type="tel"
                            name="telefone"
                            id="telefone"
                            placeholder="(11) 99999-9999"
                            maxlength="15"
                            value="<?= htmlspecialchars(
                                        $_POST['telefone'] ?? ''
                                    ) ?>"
                            required>

                    </div>


                    <div class="campo campo-full">

                        <label>E-mail</label>

                        <input
                            type="email"
                            name="email"
                            placeholder="email@exemplo.com"
                            value="<?= htmlspecialchars(
                                        $_POST['email'] ?? ''
                                    ) ?>"
                            required>

                    </div>


                    <div class="campo">

                        <label>Senha</label>

                        <input
                            type="password"
                            name="senha"
                            id="senha"
                            placeholder="Digite sua senha"
                            minlength="6"
                            required>

                        <small id="requisitosSenha">

                            Mínimo de 6 caracteres
                            e pelo menos 1 caractere especial.

                        </small>

                    </div>


                    <div class="campo">

                        <label>
                            Confirmar senha
                        </label>

                        <input
                            type="password"
                            name="confirmar_senha"
                            id="confirmar_senha"
                            placeholder="Digite novamente"
                            required>

                    </div>


                    <!-- FOTO -->

                    <div class="campo campo-full">

                        <label>
                            Foto de perfil
                        </label>

                        <input
                            type="file"
                            name="foto"
                            id="foto"
                            accept=".jpg,.jpeg,.png,.webp"
                            required>

                    </div>


                </div>


                <!-- =================================
                 ENDEREÇO
            ================================== -->

                <h3>Endereço</h3>


                <p>

                    Digite seu CEP para preencher
                    automaticamente os dados do endereço.

                </p>


                <div class="form-grid">


                    <div class="campo">

                        <label>CEP</label>

                        <input
                            type="text"
                            name="cep"
                            id="cep"
                            placeholder="00000-000"
                            maxlength="9"
                            value="<?= htmlspecialchars(
                                        $_POST['cep'] ?? ''
                                    ) ?>"
                            required>

                    </div>


                    <div class="campo">

                        <label>Estado</label>

                        <select
                            name="estado"
                            id="estado"
                            required>

                            <option value="">
                                Selecione o estado
                            </option>

                            <option value="AC">
                                Acre
                            </option>

                            <option value="AL">
                                Alagoas
                            </option>

                            <option value="AP">
                                Amapá
                            </option>

                            <option value="AM">
                                Amazonas
                            </option>

                            <option value="BA">
                                Bahia
                            </option>

                            <option value="CE">
                                Ceará
                            </option>

                            <option value="DF">
                                Distrito Federal
                            </option>

                            <option value="ES">
                                Espírito Santo
                            </option>

                            <option value="GO">
                                Goiás
                            </option>

                            <option value="MA">
                                Maranhão
                            </option>

                            <option value="MT">
                                Mato Grosso
                            </option>

                            <option value="MS">
                                Mato Grosso do Sul
                            </option>

                            <option value="MG">
                                Minas Gerais
                            </option>

                            <option value="PA">
                                Pará
                            </option>

                            <option value="PB">
                                Paraíba
                            </option>

                            <option value="PR">
                                Paraná
                            </option>

                            <option value="PE">
                                Pernambuco
                            </option>

                            <option value="PI">
                                Piauí
                            </option>

                            <option value="RJ">
                                Rio de Janeiro
                            </option>

                            <option value="RN">
                                Rio Grande do Norte
                            </option>

                            <option value="RS">
                                Rio Grande do Sul
                            </option>

                            <option value="RO">
                                Rondônia
                            </option>

                            <option value="RR">
                                Roraima
                            </option>

                            <option value="SC">
                                Santa Catarina
                            </option>

                            <option value="SP">
                                São Paulo
                            </option>

                            <option value="SE">
                                Sergipe
                            </option>

                            <option value="TO">
                                Tocantins
                            </option>

                        </select>

                    </div>


                    <div class="campo">

                        <label>Cidade</label>

                        <input
                            type="text"
                            name="cidade"
                            id="cidade"
                            placeholder="Cidade"
                            value="<?= htmlspecialchars(
                                        $_POST['cidade'] ?? ''
                                    ) ?>"
                            required>

                    </div>


                    <div class="campo">

                        <label>Bairro</label>

                        <input
                            type="text"
                            name="bairro"
                            id="bairro"
                            placeholder="Bairro"
                            value="<?= htmlspecialchars(
                                        $_POST['bairro'] ?? ''
                                    ) ?>"
                            required>

                    </div>


                    <div class="campo campo-full">

                        <label>Rua</label>

                        <input
                            type="text"
                            name="rua"
                            id="rua"
                            placeholder="Rua"
                            value="<?= htmlspecialchars(
                                        $_POST['rua'] ?? ''
                                    ) ?>"
                            required>

                    </div>


                    <div class="campo">

                        <label>Número</label>

                        <input
                            type="text"
                            name="numero"
                            id="numero"
                            placeholder="Número"
                            maxlength="10"
                            value="<?= htmlspecialchars(
                                        $_POST['numero'] ?? ''
                                    ) ?>">

                    </div>


                    <div class="campo">

                        <label>Complemento</label>

                        <input
                            type="text"
                            name="complemento"
                            id="complemento"
                            placeholder="Apartamento, casa, etc."
                            maxlength="150"
                            value="<?= htmlspecialchars(
                                        $_POST['complemento'] ?? ''
                                    ) ?>">

                    </div>


                </div>


                <!-- =================================
                 TERMOS
            ================================== -->

                <div class="termos">

                    <input
                        type="checkbox"
                        id="termos"
                        required>

                    <label for="termos">

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

                <a href="login-cliente.php">

                    Entrar

                </a>

            </div>


        </div>

    </div>


    <script src="js/cadastro_clientes.js"></script>


</body>

</html>


<?php include 'include/footer.php'; ?>