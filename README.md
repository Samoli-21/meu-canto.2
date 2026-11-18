# Meu Canto — projecto completo para Render + TiDB Cloud

Aplicação PHP 8.2 com arquitectura MVC, Apache, PDO MySQL/TLS e interface em português de Angola. O projecto foi organizado para ficar **directamente na raiz do repositório GitHub**: `Dockerfile`, `app/`, `config/`, `database/`, `docker/`, `public/`, `routes/` e `storage/`.

## Página inicial de referência

A página `app/Views/public/home.php` preserva o conteúdo e a organização do ficheiro indicado pelo utilizador: [home.php no repositório](https://github.com/Samoli-21/meu-canto.2/blob/main/app/Views/public/home.php). Inclui o hero “Há espaço para aquilo que sentes”, pilares de confiança, temas emocionais, áreas do dia a dia, recursos em destaque, pré-visualização da Canto, pedido de apoio, linguagem clara e chamada final.

O `PublicController` fornece os arrays `$topics`, `$resources` e `$featuredContents` esperados pelo template. Os destaques editoriais publicados são consultados em `conteudos_site`; caso essa consulta esteja temporariamente indisponível, a página inicial continua a apresentar os conteúdos estáticos e regista o problema nos logs.

## Funcionalidades

- Página inicial baseada no `home.php` indicado, com os textos, secções e ligações originais.
- Páginas próprias para temas, biblioteca de recursos, escola e trabalho, profissionais, informação, SOS, sobre, privacidade e acessibilidade.
- Layout adaptável para computador, tablet e telemóvel, menu móvel, formulários responsivos e tabelas administrativas com deslocamento horizontal.
- Transições curtas entre páginas, animação de entrada dos blocos e revelação tipográfica letra a letra. Respeita `prefers-reduced-motion`.
- Registo e autenticação de utilizadores, perfil de administrador, sessões seguras, protecção CSRF e passwords com `password_hash`.
- Candidaturas de profissionais ficam pendentes; só perfis aprovados pela administração aparecem no directório público.
- Área administrativa para aprovação/rejeição/suspensão de profissionais e gestão de conteúdos do site.
- Chat com a Canto: orientação básica local sem chave de IA; integração opcional com API compatível com OpenAI Chat Completions. O histórico do chat não é gravado no banco de dados.
- Migração idempotente e não destrutiva, com exactamente três tabelas de negócio: `usuarios`, `profissionais_saude`, `conteudos_site`.
- Dockerfile PHP 8.2 + Apache, entrypoint Render e endpoint `/healthz` independente do TiDB.

> O Meu Canto é uma plataforma informativa. A Canto não diagnostica, não prescreve e não substitui profissionais de saúde ou serviços de urgência.

## Estrutura

```text
Dockerfile
.env.example
app/
  Controllers/
  Core/
  Middleware/
  Views/
config/
database/
  migrations/001_schema.sql
docker/entrypoint.sh
docs/
public/
  assets/css/
  assets/images/
  assets/js/
  index.php
routes/web.php
storage/
```

## Render — configuração do Web Service

O Dockerfile está na raiz. Configura o serviço Render assim:

| Campo | Valor |
|---|---|
| Runtime | Docker |
| Root Directory | vazio |
| Docker Build Context Directory | `.` |
| Dockerfile Path | `Dockerfile` |
| Branch | `main` |
| Health Check Path | `/healthz` |

O container usa `PORT` fornecida pelo Render (valor local padrão: `10000`). O script `docker/entrypoint.sh` configura o Apache nessa porta, executa a migração, tenta criar o administrador inicial e inicia o Apache em foreground.

## Variáveis de ambiente no Render

Define-as em **Render → serviço → Environment**. Mantém as credenciais privadas fora do GitHub.

| Variável | Valor |
|---|---|
| `APP_NAME` | `Meu Canto` |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_TIMEZONE` | `Africa/Luanda` |
| `DB_HOST` | hostname exacto de Connect no TiDB |
| `DB_PORT` | `4000` (confirmar no Connect) |
| `DB_NAME` | `meu_canto` |
| `DB_USER` | username completo de Connect (inclui prefixo quando fornecido) |
| `DB_PASS` | password do TiDB |
| `DB_SSL` | `true` |
| `DB_SSL_CA` | `/etc/ssl/certs/ca-certificates.crt` |
| `ADMIN_NAME` | nome para o primeiro administrador |
| `ADMIN_EMAIL` | email válido para o primeiro administrador |
| `ADMIN_PASSWORD` | password inicial com pelo menos 12 caracteres |
| `AI_API_URL` | endpoint HTTPS compatível (opcional) |
| `AI_API_KEY` | chave privada do fornecedor de IA (opcional) |
| `AI_MODEL` | modelo autorizado pelo fornecedor (opcional) |

Se já existir uma conta `admin` em `usuarios`, o script de bootstrap não a substitui nem altera a password. As variáveis `ADMIN_*` só criam o primeiro administrador quando não existe nenhum.

## TiDB Cloud

Cria a base `meu_canto` no SQL Editor antes do primeiro deploy:

```sql
CREATE DATABASE IF NOT EXISTS meu_canto
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;
```

Usa o endpoint público e TLS. Autoriza no TiDB os intervalos de saída indicados em **Render → Connect → Outbound**. Não uses `sys` como base de dados da aplicação e não deixes `Allow_all_public_connections` activo permanentemente.

A migração executa `CREATE TABLE IF NOT EXISTS`; não usa `DROP TABLE`, não apaga registos e não redefine o administrador existente.

## Desenvolvimento local

1. Copia `.env.example` para `.env` e define as credenciais locais MySQL.
2. Cria a base `meu_canto`.
3. Executa `php database/migrate.php`.
4. Define `ADMIN_NAME`, `ADMIN_EMAIL` e `ADMIN_PASSWORD` e executa `php database/create-admin.php` para criar o primeiro administrador.
5. Inicia um servidor web apontando o document root para `public/`.

## Guia de actualização no GitHub/Windows

Segue `docs/ATUALIZAR_GITHUB.md`. O guia usa a pasta local `meu-canto-publicar-2`, preserva `.git`, substitui os ficheiros do projecto e envia para a branch `main`. Não copies `.env` nem credenciais.

## Verificações realizadas neste pacote

- `php -l` em todos os ficheiros PHP.
- `node --check` nos ficheiros JavaScript.
- `sh -n` no entrypoint.
- Verificação de 3 instruções `CREATE TABLE` e ausência de `DROP TABLE`.
- Teste local de `/healthz`, `HEAD /healthz`, rotas públicas estáticas e respostas sem base de dados local.
- ZIP com Dockerfile na raiz.

Não foi executado um build Docker nem uma ligação à instância TiDB remota nesta sessão. Esses testes dependem do serviço Render e das credenciais da tua conta.
