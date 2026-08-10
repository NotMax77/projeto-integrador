<?php
require_once __DIR__ . "/include/conexao.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: cadastro-pais.html");
    exit;
}

// =========================
// DADOS DO CLIENTE
// =========================
$nome = trim($_POST["nome"] ?? "");
$sobrenome = trim($_POST["sobrenome"] ?? "");
$cpf = trim($_POST["cpf"] ?? "");
$data_nascimento = $_POST["data_nascimento"] ?? "";
$sexo = $_POST["sexo"] ?? "";
$telefone = trim($_POST["telefone"] ?? "");
$email = trim($_POST["email"] ?? "");
$senha = $_POST["senha"] ?? "";
$confirmar_senha = $_POST["confirmar_senha"] ?? "";

// =========================
// DADOS DO ENDEREÇO
// =========================
$cep = trim($_POST["cep"] ?? "");
$estado = trim($_POST["estado"] ?? "");
$cidade = trim($_POST["cidade"] ?? "");
$bairro = trim($_POST["bairro"] ?? "");
$rua = trim($_POST["rua"] ?? "");
$numero = trim($_POST["numero"] ?? "");
$complemento = trim($_POST["complemento"] ?? "");

if (
    $nome === "" || $sobrenome === "" || $cpf === "" ||
    $data_nascimento === "" || $sexo === "" || $telefone === "" ||
    $email === "" || $senha === "" || $confirmar_senha === "" ||
    $cep === "" || $estado === "" || $cidade === "" ||
    $bairro === "" || $rua === ""
) {
    die("Preencha todos os campos obrigatórios.");
}

if ($senha !== $confirmar_senha) {
    die("As senhas não conferem.");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("E-mail inválido.");
}

if (!in_array($sexo, ["masculino", "feminino", "outro"], true)) {
    die("Sexo inválido.");
}

// A tabela atual possui CPF e e-mail únicos.
$verifica = $conexao->prepare(
    "SELECT id_cliente FROM cliente WHERE cpf = ? OR email = ? LIMIT 1"
);
$verifica->bind_param("ss", $cpf, $email);
$verifica->execute();
$resultado = $verifica->get_result();

if ($resultado->num_rows > 0) {
    $verifica->close();
    die("Já existe um cliente cadastrado com este CPF ou e-mail.");
}
$verifica->close();

// =========================
// FOTO
// =========================
// A foto é opcional no formulário.
// Se nenhuma for enviada, gravamos uma string vazia.
$foto = "";

if (isset($_FILES["foto"]) && $_FILES["foto"]["error"] !== UPLOAD_ERR_NO_FILE) {
    if ($_FILES["foto"]["error"] !== UPLOAD_ERR_OK) {
        die("Erro ao enviar a foto.");
    }

    $permitidos = ["jpg", "jpeg", "png", "gif", "webp"];
    $extensao = strtolower(pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION));

    if (!in_array($extensao, $permitidos, true)) {
        die("Formato de foto não permitido.");
    }

    $diretorio = __DIR__ . "/img/uploads/clientes";

    if (!is_dir($diretorio) && !mkdir($diretorio, 0775, true)) {
        die("Não foi possível criar a pasta para as fotos.");
    }

    $nomeArquivo = uniqid("cliente_", true) . "." . $extensao;
    $destino = $diretorio . "/" . $nomeArquivo;

    if (!move_uploaded_file($_FILES["foto"]["tmp_name"], $destino)) {
        die("Não foi possível salvar a foto.");
    }

    $foto = "img/uploads/clientes/" . $nomeArquivo;
}

// =========================
// TRANSAÇÃO
// =========================
// Primeiro salva o endereço.
// Depois usa o id_endereco gerado para salvar o cliente.
// Se algo falhar, nenhuma das duas inserções fica gravada.
$conexao->begin_transaction();

try {
    $sqlEndereco = "
        INSERT INTO endereco
        (cep, estado, cidade, bairro, rua, numero, complemento)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ";

    $stmtEndereco = $conexao->prepare($sqlEndereco);
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
    $stmtEndereco->close();

    // A senha é armazenada com hash.
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    $sqlCliente = "
        INSERT INTO cliente
        (nome, sobrenome, cpf, data_nascimento, sexo, email, senha, telefone, foto, id_endereco)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ";

    $stmtCliente = $conexao->prepare($sqlCliente);
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
        $foto,
        $id_endereco
    );
    $stmtCliente->execute();
    $stmtCliente->close();

    $conexao->commit();

    echo "<script>
        alert('Cadastro realizado com sucesso!');
        window.location.href = 'login.html';
    </script>";
} catch (Throwable $erro) {
    $conexao->rollback();

    // Se a foto já foi salva e o banco falhou, remove o arquivo.
    if ($foto !== "") {
        $arquivoFoto = __DIR__ . "/" . $foto;
        if (is_file($arquivoFoto)) {
            unlink($arquivoFoto);
        }
    }

    die("Erro ao cadastrar: " . $erro->getMessage());
}

$conexao->close();
?>
