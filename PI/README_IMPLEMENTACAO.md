# Babá Amiga — Projeto Integrador

## O que foi implementado

- Login único para **babás e clientes**, identificando automaticamente o tipo de conta pelo e-mail.
- Logout com encerramento da sessão.
- Sessão PHP com `session_regenerate_id()` e proteção de páginas por tipo de usuário.
- Perfil completo da babá em `perfil-baba.php`.
  - Se acessado pela própria babá: permite atualizar telefone e experiência.
  - Se acessado por um cliente com `?id=ID`: exibe o perfil da babá selecionada.
- Perfil do cliente em `perfil-cliente.php`, com edição do telefone.
- Perfil público do cliente para babás em `perfil-cliente-publico.php?id=ID`.
- Navbar adaptativa: mostra menus, perfil e logout de acordo com o tipo de usuário.
- Página de babás (`babas.php`) ligada ao banco, com busca e filtros de cidade/estado.
- Área inicial da babá (`dashboard.php`) protegida.
- Páginas de favoritos/pagamentos/histórico/ganhos convertidas para PHP e protegidas por perfil quando aplicável.
- Arquivos `.html` antigos das páginas convertidas foram mantidos como redirecionamentos para não quebrar links antigos.

## Como executar

1. Instale/abra XAMPP (Apache + MySQL/MariaDB).
2. Copie a pasta `PI` para `htdocs`.
3. Crie o banco `baba_amiga`.
4. Importe o arquivo `baba_amiga 1.sql`.
5. Confira as credenciais em `PI/include/conexao.php`.
6. Acesse `http://localhost/PI/`.

A conexão original do projeto usa:
- servidor: `localhost`
- usuário: `root`
- senha: vazia
- banco: `baba_amiga`

## Fluxo

- Cliente: `cadastro-pais.html` → cadastro → `login.php` → `babas.php` → perfil da babá.
- Babá: `cadastro-babas.php` → cadastro → `login.php` → `dashboard.php` → `perfil-baba.php`.
- Em qualquer área autenticada, o botão de saída chama `logout.php`.

O banco de dados continua sendo o arquivo SQL entregue no projeto; não foi necessário criar uma tabela separada de usuários porque as tabelas `BABA` e `CLIENTE` já possuem e-mail, senha e dados suficientes para a autenticação.
