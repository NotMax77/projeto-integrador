<?php

declare(strict_types=1);
require_once __DIR__ . '/include/auth.php';
exigirTipo('cliente');

$busca = trim($_GET['busca'] ?? '');
$cidade = trim($_GET['cidade'] ?? '');
$estado = trim($_GET['estado'] ?? '');

$sql = "SELECT 
            b.id_baba,
            b.nome,
            b.sobrenome,
            b.foto,
            b.experiencia,
            e.cidade,
            e.estado,

            ROUND(AVG(a.nota), 1) AS media,
            COUNT(a.id_avaliacao) AS total_avaliacoes,

            GROUP_CONCAT(
                DISTINCT p.descricao
                ORDER BY p.descricao
                SEPARATOR '||'
            ) AS preferencias,

            GROUP_CONCAT(
                DISTINCT d.horario
                ORDER BY d.horario
                SEPARATOR '||'
            ) AS disponibilidades

        FROM BABA b

        INNER JOIN ENDERECO e
            ON e.id_endereco = b.id_endereco

        LEFT JOIN AVALIACAO a
            ON a.id_baba = b.id_baba

        LEFT JOIN BABA_PREFERENCIA bp
            ON bp.id_baba = b.id_baba

        LEFT JOIN PREFERENCIA p
            ON p.id_preferencia = bp.id_preferencia

        LEFT JOIN DISPONIBILIDADE d
            ON d.id_baba = b.id_baba

        WHERE 1=1";

$types = '';
$params = [];


// PESQUISA
if ($busca !== '') {

    $sql .= "
        AND (
            CONCAT(b.nome, ' ', b.sobrenome) LIKE ?
            OR e.cidade LIKE ?
        )
    ";

    $like = "%{$busca}%";

    $params[] = $like;
    $params[] = $like;

    $types .= 'ss';
}


// CIDADE
if ($cidade !== '') {

    $sql .= " AND e.cidade = ?";

    $params[] = $cidade;

    $types .= 's';
}


// ESTADO
if ($estado !== '') {

    $sql .= " AND e.estado = ?";

    $params[] = $estado;

    $types .= 's';
}


// AGRUPAMENTO
$sql .= "
    GROUP BY
        b.id_baba,
        b.nome,
        b.sobrenome,
        b.foto,
        b.experiencia,
        e.cidade,
        e.estado

    ORDER BY
        media DESC,
        b.nome ASC
";


$stmt = $conexao->prepare($sql);

if ($types !== '') {

    $stmt->bind_param(
        $types,
        ...$params
    );
}

$stmt->execute();

$babas = $stmt
    ->get_result()
    ->fetch_all(MYSQLI_ASSOC);


// CIDADES
$cidades = $conexao->query(
    "SELECT DISTINCT e.cidade
     FROM ENDERECO e
     INNER JOIN BABA b
        ON b.id_endereco = e.id_endereco
     ORDER BY e.cidade"
)->fetch_all(MYSQLI_ASSOC);


// ESTADOS
$estados = $conexao->query(
    "SELECT DISTINCT e.estado
     FROM ENDERECO e
     INNER JOIN BABA b
        ON b.id_endereco = e.id_endereco
     ORDER BY e.estado"
)->fetch_all(MYSQLI_ASSOC);

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encontrar babás | Babá Amiga</title>
    <link rel="stylesheet" href="CSS/navbar.css">
    <link rel="stylesheet" href="CSS/footer.css">
    <link rel="stylesheet" href="CSS/babas.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .lista-babas {
            max-width: 1100px;
            margin: 25px auto 60px;
            padding: 0 20px;
            display: grid;
            gap: 16px
        }

        .card-baba {
            background: #fff;
            border-radius: 14px;
            padding: 18px;
            display: flex;
            align-items: center;
            gap: 20px;
            box-shadow: 0 5px 18px #00000010
        }

        .card-baba img {
            width: 110px;
            height: 110px;
            object-fit: cover;
            border-radius: 50%
        }

        .dados {
            flex: 1
        }

        .dados h3 {
            margin: 0 0 8px
        }

        .acoes button {
            border: 0;
            border-radius: 9px;
            padding: 11px 16px;
            background: #4f8f72;
            color: #fff;
            font-weight: 700;
            cursor: pointer
        }

        .filtros {
            max-width: 1100px;
            margin: 30px auto 10px;
            padding: 18px 20px;
            background: #fff;
            border-radius: 14px;
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            align-items: end
        }

        .filtros .filtro {
            display: flex;
            flex-direction: column;
            gap: 5px
        }

        .filtros select,
        .filtros input {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px
        }

        .btn-filtrar {
            padding: 10px 16px;
            border: 0;
            border-radius: 8px;
            background: #4f8f72;
            color: white;
            font-weight: 700
        }

        @media(max-width:650px) {
            .card-baba {
                flex-direction: column;
                text-align: center
            }

            .acoes {
                width: 100%
            }

            .acoes a {
                display: block
            }
        }
    </style>
</head>

<body>
    <?php include __DIR__ . '/include/navbar_publica.php'; ?>

    <section class="container-pesquisa" style="max-width:1100px;margin:30px auto 0;padding:0 20px">
        <form class="form_pesquisa" method="GET" style="display:flex;gap:8px">
            <input type="text" name="busca" value="<?= e($busca) ?>" placeholder="Pesquisar por nome ou cidade..." style="flex:1">
            <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
        </form>
    </section>

    <form class="filtros" method="GET">
        <input type="hidden" name="busca" value="<?= e($busca) ?>">
        <div class="filtro"><label>Estado</label><select name="estado">
                <option value="">Todos</option><?php foreach ($estados as $item): ?><option value="<?= e($item['estado']) ?>" <?= $estado === $item['estado'] ? 'selected' : '' ?>><?= e($item['estado']) ?></option><?php endforeach; ?>
            </select></div>
        <div class="filtro"><label>Cidade</label><select name="cidade">
                <option value="">Todas</option><?php foreach ($cidades as $item): ?><option value="<?= e($item['cidade']) ?>" <?= $cidade === $item['cidade'] ? 'selected' : '' ?>><?= e($item['cidade']) ?></option><?php endforeach; ?>
            </select></div>
        <button class="btn-filtrar" type="submit"><i class="fa-solid fa-filter"></i> Filtrar</button>
    </form>

    <h2 style="text-align:center">Babás disponíveis</h2>
    <section class="lista-babas">
        <?php if (!$babas): ?>
            <div style="background:#fff;padding:30px;border-radius:14px;text-align:center">Nenhuma babá encontrada.</div>
        <?php endif; ?>
        <?php foreach ($babas as $baba): ?>
            <article class="card-baba">
                <img src="<?= e($baba['foto'] ?: 'img/baba.jpg') ?>" alt="Foto de <?= e($baba['nome'] . ' ' . $baba['sobrenome']) ?>">
                <div class="dados">
                    <h3><?= e($baba['nome'] . ' ' . $baba['sobrenome']) ?></h3>
                    <p><i class="fa-solid fa-location-dot"></i> <?= e($baba['cidade'] . ' - ' . $baba['estado']) ?></p>
                    <p><?= $baba['media'] !== null ? '⭐ ' . e((string)$baba['media']) . ' (' . (int)$baba['total_avaliacoes'] . ' avaliações)' : 'Sem avaliações' ?></p>
                    <p><?= e(mb_strimwidth($baba['experiencia'] ?: 'Experiência não informada.', 0, 130, '...', 'UTF-8')) ?></p>
                </div>
                <div class="acoes"><a href="perfil-baba.php?id=<?= (int)$baba['id_baba'] ?>"><button type="button">Ver perfil</button></a></div>
            </article>
        <?php endforeach; ?>
    </section>

    <?php include __DIR__ . '/include/footer.php'; ?>
</body>

</html>