# PlanHome — Site (Laravel)

Plataforma web de móveis do TCC PlanHome. Este projeto está com o **novo visual**,
igual ao do app mobile (marrom + bege, cantos arredondados, bordas finas).
Todas as funcionalidades continuam as mesmas (login, cadastro, catálogo, carrinho,
pedidos e painel da empresa).

## Como rodar (passo a passo)

Pré-requisitos: PHP 8.2+, Composer.

1. Abra o terminal dentro da pasta do projeto.
2. Instale as dependências do PHP:
   ```
   composer install
   ```
3. Crie o arquivo de configuração:
   ```
   cp .env.example .env
   php artisan key:generate
   ```
4. Crie o banco (o projeto usa SQLite, que é só um arquivo):
   ```
   touch database/database.sqlite
   ```
   No Windows (PowerShell): `New-Item database/database.sqlite -ItemType File`
5. Crie as tabelas e os dados de exemplo:
   ```
   # Defina ADMIN_EMAIL e ADMIN_PASSWORD no .env antes de semear o banco.
   php artisan migrate:fresh --seed
   ```
   (`migrate:fresh` **apaga tudo** do banco e recria as tabelas; o `--seed` cria só o
   administrador. Use esse comando também para limpar o banco atual.)
6. Inicie o servidor:
   ```
   php artisan serve
   ```
7. Abra http://127.0.0.1:8000 no navegador.

O banco já nasce **limpo**: sem produtos e sem usuários, só o administrador configurado
com `ADMIN_EMAIL` e `ADMIN_PASSWORD` no `.env`.

Clientes e empresas são criados em **Criar conta** (menu do site). Para trocar a senha do
administrador, atualize `ADMIN_PASSWORD` no `.env` e rode `php artisan migrate:fresh --seed`.

> Não precisa rodar `npm install` nem `npm run dev`: o visual usa só o
> `public/css/styles.css` (Bootstrap) + `public/css/planhome.css` (tema novo).

## Administrador (novo)

Entre com os valores de `ADMIN_EMAIL` e `ADMIN_PASSWORD` do `.env`. O login leva direto para **/admin**, uma tela
única com duas tabelas (usuários e produtos), cada linha com o botão **Excluir**.

- **A exclusão é definitiva**: as linhas são apagadas do banco de dados (com confirmação antes).
- **Excluir produto:** apaga o produto, a imagem enviada, os itens de carrinho e os pedidos
  que usavam esse produto.
- **Excluir conta de empresa:** apaga a empresa, todos os produtos dela e o que dependia deles.
- **Excluir conta de cliente:** apaga a conta, o carrinho, os pedidos, os tokens do app e as sessões.
- O administrador não pode excluir a si mesmo nem outro administrador, e só ele acessa `/admin`
  (qualquer outro usuário recebe erro 403). O cadastro público só cria cliente e empresa.
- **Avisos:** quando um produto é excluído, quem tinha ele no carrinho ou em pedidos (e a empresa dona)
  vê, na próxima página que abrir, a mensagem *"O produto "X" ... foi excluído pelo administrador"*.
  O aviso fica na tabela `avisos` e some depois de mostrado (só fecha no X).
- A conta de administrador não aparece como cliente/empresa: não tem carrinho nem pedidos
  e não entra pelo app mobile (a API responde 403).

Arquivos: `app/Http/Controllers/AdminController.php`, `app/Http/Middleware/EhAdmin.php` (apelido `admin`
em `bootstrap/app.php`), `app/Models/Aviso.php`, `resources/views/admin/index.blade.php`,
`database/seeders/AdminSeeder.php`, migration `2026_10_21_000000_create_avisos_table.php`
e o valor `admin` no campo `tipousuario` (migration `0001_01_01_000000_create_users_table.php`).

> Como o campo `tipousuario` mudou na migration original, é preciso rodar
> `php artisan migrate:fresh --seed` (também na hospedagem) em vez de só `migrate`.

## Novidades desta versão

### 1. Avisos de sucesso/erro não quebram mais o visual
Antes, os avisos ("Produto adicionado!", "Carrinho vazio"...) eram desenhados **no meio da
página**, entre o menu e o título marrom, e empurravam todo o conteúdo para baixo.
Agora eles aparecem como um **aviso flutuante no canto superior direito** (`position: fixed`),
somem sozinhos depois de 5 segundos e têm um botão **×** para fechar.
- `resources/views/layouts/app.blade.php`: bloco `ph-toast-area` + um pequeno script
- `public/css/planhome.css`: classes `ph-toast*`
- Os erros de formulário (lista vermelha dentro do cartão) continuam como estavam.

### 2. Envio de imagens dos produtos
Nas telas **Adicionar produto** e **Editar produto** agora existe o campo
**"Imagem do produto"** (arquivo JPG, PNG ou WEBP, até 4 MB) com prévia da imagem.
O campo de link continua existindo como opção. Regras:
- se enviar arquivo **e** link, vale o arquivo;
- na edição, deixar tudo vazio **mantém** a imagem atual; há também a caixa "Remover a imagem";
- ao trocar/remover a imagem ou excluir o produto, o arquivo antigo é apagado.

Os arquivos ficam em `public/uploads/produtos/` (a pasta já vem criada; o conteúdo não
vai para o Git). Foi escolhida essa pasta, e não `storage/`, para **não precisar** do comando
`php artisan storage:link` (que costuma dar problema em hospedagem compartilhada).
O endereço final da imagem é montado pelo atributo `imagem_url` do model `Produto`.

> Atenção na hospedagem: a pasta `public/uploads/produtos` precisa ter permissão de escrita.
> Se o upload der erro, rode `chmod -R 775 public/uploads`. Se aparecer
> "imagem muito grande", aumente `upload_max_filesize` e `post_max_size` no PHP.

### 3. API para o app mobile (`/api/v1`)
Só as telas de **cliente** foram abertas para o app. Empresas continuam só no site.
A API segue o material da aula (Laravel: API): **Sanctum** para os tokens, rotas em
`routes/api.php`, `apiResource`, respostas sempre em JSON e códigos HTTP corretos.

> Analogia: o Sanctum é a **pulseira oficial** da loja. O app faz login, recebe a
> pulseira (token) e a mostra em cada pedido (`Authorization: Bearer <token>`).
> Cada aparelho recebe a sua pulseira, e o logout rasga só a pulseira daquele aparelho.

#### Passo a passo para ativar (faça uma vez, no site local e depois no publicado)
1. Instale o Sanctum (é o passo 2 do "RESUMO" da aula):
   ```
   php artisan install:api
   ```
   Ele baixa o Sanctum e cria a tabela `personal_access_tokens`. Quando perguntar
   se quer rodar as migrations pendentes, responda **yes**. Isso já roda também a
   migration das colunas `telefone` e `endereco` do cliente.
2. Se ele não perguntou (ou você respondeu no), rode: `php artisan migrate`
3. Teste: abra `http://127.0.0.1:8000/api/v1/produtos` — deve aparecer uma lista em JSON.

> Importante: o `User` já usa a trait `HasApiTokens` do Sanctum. Se o código for
> enviado para a hospedagem **sem** rodar o passo 1 lá, o site para de abrir
> ("trait not found"). Na hospedagem rode `composer install` (ou `php artisan install:api`)
> e `php artisan migrate`.

#### Rotas (`php artisan route:list --path=api` mostra todas)
| Método | Rota | Método no controller | Resposta | Login? |
|---|---|---|---|---|
| POST | `/api/v1/register` | `register` | `201` + token e cliente | não |
| POST | `/api/v1/login` | `login` | `200` + token e cliente (`401` senha errada, `403` empresa) | não |
| GET | `/api/v1/produtos` | `index` | `200` produtos ativos (com `imagem_url`). Filtros opcionais: `?busca=sofa` (nome) e `?categoria=mesa` | não |
| GET | `/api/v1/produtos/{id}` | `show` | `200` um produto (`404` se não existe ou está inativo) | não |
| POST | `/api/v1/logout` | `logout` | `200` (apaga o token atual) | sim |
| GET | `/api/v1/me` | `me` | `200` dados do cliente | sim |
| PUT | `/api/v1/me` | `update` | `200` (`422` se inválido) | sim |
| DELETE | `/api/v1/me` | `destroy` | `204` (apaga conta, tokens e pedidos) | sim |
| GET | `/api/v1/carrinho` | `index` | `200` `{"itens": [...], "total": 0.0}` do cliente logado | sim |
| POST | `/api/v1/carrinho` | `store` | `201` coloca no carrinho (`200` se já estava e só somou a quantidade) — corpo: `{"produto_id":1,"quantidade":2}` | sim |
| PUT | `/api/v1/carrinho/{produto}` | `update` | `200` troca a quantidade — corpo: `{"quantidade":3}` (`404` se o produto não está no carrinho) | sim |
| DELETE | `/api/v1/carrinho/{produto}` | `destroy` | `204` tira o produto do carrinho (`404` se não estava) | sim |
| DELETE | `/api/v1/carrinho` | `clear` | `204` esvazia o carrinho | sim |
| POST | `/api/v1/carrinho/finalizar` | `finalizar` | `201` transforma o carrinho em pedidos e esvazia (`422` se vazio ou produto indisponível) | sim |
| GET | `/api/v1/pedidos` | `index` | `200` pedidos do cliente | sim |
| POST | `/api/v1/pedidos` | `store` | `201` — corpo: `{"itens":[{"produto_id":1,"quantidade":2}]}` | sim |
| DELETE | `/api/v1/pedidos/{id}` | `destroy` | `204` (`403` se não for seu ou não estiver pendente, `404` se não existe) | sim |

Sem token (ou com token inválido) as rotas protegidas respondem `401`.
Erros em `/api/*` saem sempre em JSON (configurado em `bootstrap/app.php`), mas mande
`Accept: application/json` mesmo assim, como ensina a aula.

#### Carrinho único: site e app conversam
O carrinho **não fica mais na sessão do navegador**: ele fica na tabela `carrinho_item`, e
tanto o `CarrinhoController` do site quanto o `CarrinhoApiController` do app leem e gravam nela.
Então o que o cliente coloca no carrinho no site aparece no app (e vice-versa), com a mesma conta.
As regras são as mesmas nos dois lados: só produtos ativos, máximo de 50 unidades por produto,
e **Finalizar compra** vira um pedido por unidade e esvazia o carrinho.
O carrinho também **não se perde mais ao fazer logout** no site, porque é do cliente, não do navegador.

#### Arquivos da API
`routes/api.php`, `app/Http/Controllers/Api/` (`AuthApiController`, `ProdutoApiController`,
`PedidoApiController`), trait `HasApiTokens` em `app/Models/User.php` e a migration
`2026_10_03_000000_adiciona_campos_api_em_users.php`. O carrinho usa a tabela nova `carrinho_item` (migration `2026_10_05_000000_create_carrinho_item_table.php`, model `CarrinhoItem`). A migration
`2026_10_04_..._remove_api_token_manual_de_users.php` só limpa a coluna `api_token` de uma
versão anterior (se ela existir no banco).
A paginação (`paginate()`) não foi aplicada de propósito: o app espera uma lista simples.

#### Como testar (roteiro da aula)
Importe `docs/PlanHome-API.postman_collection.json` no **Postman**, **Insomnia**, **Bruno**
ou **Thunder Client** (todos importam esse formato). São 34 requisições em ordem:
login → token → catálogo (lista, filtro, um produto) → carrinho (colocar, somar, trocar,
retirar, finalizar) → pedidos → sem token (`401`) → inexistente (`404`) → cancelar (`204`) etc. Cada uma já tem o teste do status esperado.
Variável `base_url` da coleção: `http://127.0.0.1:8000/api/v1` (troque para testar o site publicado).
Rode o site com `php artisan serve` e o banco com `php artisan migrate:fresh --seed`
(com o banco limpo, crie antes um cliente em **Criar conta** ou pelo `POST /api/v1/register`, e uma empresa com ao menos um produto ativo).

## O que mudou no visual

- **`public/css/planhome.css`** (novo): todo o tema em um arquivo só, com a paleta
  do app (`cores_app.dart`) guardada em variáveis no começo do arquivo.
- **`resources/views/*`**: as telas trocaram os estilos antigos (laranja e estilos
  escritos direto no HTML) por classes `ph-*` do tema novo.
  Rotas, formulários, nomes de campos e regras do Blade **não foram alterados**.
- **`resources/views/layouts/app.blade.php`**: menu marrom com o "chip" bege na página
  atual, bolinha com a quantidade do carrinho e rodapé simples.
  Removidos o link da fonte Merriweather e o `scripts.js` (o arquivo não existia em
  `public/js`, então gerava um erro 404 em toda página).

### Paleta (igual ao app)

| Cor            | Código    | Onde é usada                         |
|----------------|-----------|--------------------------------------|
| Marrom escuro  | `#5F3A1A` | menu, botões principais, preços      |
| Marrom médio   | `#8B5E3C` | fim do degradê, hover                |
| Bege           | `#F7F4EE` | fundo das páginas                    |
| Bege claro     | `#F7E9D4` | texto sobre marrom, ícones, etiquetas|
| Cinza da borda | `#D3D1C7` | bordas de cartões e campos           |
| Cinza do texto | `#888780` | textos secundários                   |
| Texto escuro   | `#2C2C2A` | textos principais                    |

### Formas (igual ao app)

Botões grandes com raio 12, botões pequenos 8, campos 10, cartões 12–14,
sem sombras (só borda fina), fonte Georgia.

## Prévia

A pasta `previa/` tem imagens das telas com o visual novo.
