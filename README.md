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
   php artisan migrate --seed
   ```
6. Inicie o servidor:
   ```
   php artisan serve
   ```
7. Abra http://127.0.0.1:8000 no navegador.

Contas de teste (criadas pelo seeder), senha `123456`:

| Tipo    | E-mail            |
|---------|-------------------|
| Empresa | admin@email.com   |
| Cliente | cliente@email.com |

> Não precisa rodar `npm install` nem `npm run dev`: o visual usa só o
> `public/css/styles.css` (Bootstrap) + `public/css/planhome.css` (tema novo).

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
