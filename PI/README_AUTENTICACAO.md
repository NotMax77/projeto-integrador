# Autenticação e perfis adicionados

Foram adicionados ao projeto:

- `include/auth.php`: controle central de sessão e proteção de páginas.
- `login.php`: autenticação de clientes e babás usando `password_verify`.
- `logout.php`: encerra a sessão.
- `sessao.php`: endpoint JSON para consultar o usuário logado.
- `cliente_perfil.php`: perfil do cliente com edição de nome, sobrenome e telefone.
- `baba/perfil.php`: perfil da babá com edição de nome, sobrenome, telefone e experiência.
- `dashboard.php`: versão protegida do dashboard da babá.
- `CSS/perfil.css`: estilos dos perfis.
- `navbar_publica.php`: navbar adaptada ao tipo de usuário.
- `login.html`: seleção de tipo de conta no login e mensagens de erro/sucesso.

## Banco

O código usa o banco já configurado em `include/conexao.php` (`baba_amiga`) e as tabelas que o cadastro existente já utiliza:

- `CLIENTE`
- `BABA`
- `ENDERECO`
- `DISPONIBILIDADE`
- `BABA_PREFERENCIA`
- `PREFERENCIA`

As senhas continuam sendo gravadas pelo cadastro existente com `password_hash`, e o login valida com `password_verify`.

## Fluxo

1. O usuário escolhe Cliente ou Babá no login.
2. O PHP verifica a conta na tabela correspondente.
3. A sessão guarda `usuario_id` e `tipo_usuario`.
4. Cliente vai para `index.php`.
5. Babá vai para `dashboard.php`.
6. O perfil de cada tipo só pode ser aberto pelo próprio tipo de usuário.
7. `logout.php` encerra a sessão e volta para o login.

## Observação

As páginas HTML antigas de conteúdo continuam funcionando como páginas de conteúdo. As áreas que precisam de autenticação usam PHP e sessão. O `dashboard.html` antigo foi mantido apenas como redirecionamento para o novo `dashboard.php`.
