# Configurar Meu Canto no Render + TiDB Cloud

## A. Requisitos da instância TiDB

A aplicação liga-se pelo protocolo MySQL usando PDO e TLS. Na instância TiDB Cloud:

1. Abre **Connect → Public → General** e copia Host, Port, User e Password.
2. Usa o hostname sem `https://` e sem `:4000` no fim.
3. Cria a base de dados, uma única vez, no SQL Editor:

```sql
CREATE DATABASE IF NOT EXISTS meu_canto
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;
```

A migração do projecto cria as três tabelas dentro desta base de dados. Não uses a base `sys` para a aplicação.

## B. Autorizar o Render no TiDB

1. No serviço Render, consulta **Connect → Outbound** e copia todos os intervalos CIDR de saída indicados para a região do serviço.
2. No TiDB Cloud, abre **Settings → Networking → Authorized Networks**.
3. Adiciona os intervalos de saída do Render, respeitando o formato solicitado pelo painel TiDB.
4. Testa a ligação e só depois remove a regra `Allow_all_public_connections`, caso exista.

Não deixes a instância aberta a todos os IPs como configuração permanente. Os intervalos de saída podem variar com a configuração/região do serviço; confirma os valores directamente no Render.

## C. Estrutura de repositório e Docker

Este pacote coloca `Dockerfile` directamente na raiz do repositório GitHub. Depois de substituir o conteúdo do repositório, configura no Render:

| Campo | Valor |
|---|---|
| Runtime | Docker |
| Root Directory | deixar vazio |
| Docker Build Context Directory | `.` |
| Dockerfile Path | `Dockerfile` |
| Branch | `main` (ou a branch usada pelo serviço) |
| Health Check Path | `/healthz` (opcional) |

Não coloques `meu-canto-render-tidb` nos caminhos. Essa pasta externa deixa de existir nesta organização do projecto.

## D. Environment Variables no Render

Abre **serviço → Environment → Environment Variables**. Cria ou actualiza:

| Key | Valor |
|---|---|
| `APP_NAME` | `Meu Canto` |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_TIMEZONE` | `Africa/Luanda` |
| `DB_HOST` | Host exacto de Connect no TiDB |
| `DB_PORT` | `4000` (ou a porta indicada pelo Connect) |
| `DB_NAME` | `meu_canto` |
| `DB_USER` | Utilizador completo de Connect (pode incluir prefixo, ex. `prefixo.root`) |
| `DB_PASS` | Palavra-passe da instância TiDB |
| `DB_SSL` | `true` |
| `DB_SSL_CA` | `/etc/ssl/certs/ca-certificates.crt` |
| `ADMIN_NAME` | Nome do administrador inicial |
| `ADMIN_EMAIL` | Email válido do primeiro administrador |
| `ADMIN_PASSWORD` | Palavra-passe forte com pelo menos 12 caracteres |
| `AI_API_URL` | `https://api.openai.com/v1/chat/completions` ou endpoint HTTPS compatível |
| `AI_API_KEY` | Chave privada do fornecedor (opcional) |
| `AI_MODEL` | Modelo autorizado pelo fornecedor (opcional) |

Não coloques palavras-passe ou chaves privadas no GitHub. `AI_API_KEY` é opcional: sem ela, o chat funciona em modo de orientação básica e informa o utilizador desse estado.

**Administrador existente:** se a tabela `usuarios` já tiver um registo `tipo='admin'`, o script não altera esse utilizador. As variáveis `ADMIN_EMAIL` e `ADMIN_PASSWORD` são usadas apenas para criar o primeiro administrador numa base sem administrador. A alteração dessas variáveis não redefine credenciais existentes.

## E. Arranque do container

O `docker/entrypoint.sh`:

1. valida `PORT`, `public/index.php` e `routes/web.php`;
2. configura Apache na porta recebida pelo Render (padrão `10000`);
3. executa `php database/migrate.php`;
4. executa `php database/create-admin.php`;
5. inicia Apache em foreground.

A migração é idempotente: cria as três tabelas se não existirem. Não executa `DROP TABLE`.

## F. Confirmar sucesso

Nos Runtime Logs, procura:

```text
Migração concluída. Entidades de negócio: usuarios, profissionais_saude, conteudos_site.
Já existe um administrador; nenhuma alteração foi feita.
[Meu Canto] A iniciar Apache...
Apache/2.4 ... configured -- resuming normal operations
```

Depois testa:

- `/healthz` — deve devolver `ok`;
- `/` — início;
- `/o-que-sinto` — temas;
- `/recursos` — biblioteca;
- `/profissionais` — directório e candidatura;
- `/entrar` — autenticação;
- `/canto` — chat;
- `/admin` — painel, requer perfil admin.

## G. Erros frequentes

- `getaddrinfo for HOST_DO_TIDB`: `DB_HOST` ainda contém o marcador; substitui pelo hostname exacto de Connect.
- `Access denied`: confirma `DB_USER` completo e `DB_PASS`.
- `Unknown database`: cria `meu_canto` no SQL Editor e confirma `DB_NAME`.
- `Connection timed out`: verifica Authorized Networks e intervalos IP do Render.
- `Certificate CA not found`: confirma o caminho `DB_SSL_CA`.
- `ADMIN_EMAIL inválido`: define email válido; se já existe admin, o script não precisa de novas credenciais de bootstrap.
- `404` do Apache em rotas internas: confirma que o Docker usa a raiz do repositório (Root Directory vazio), que `public/.htaccess` está no commit e que `mod_rewrite`/`mod_dir` estão activos no Dockerfile.

## H. Atenção à base antiga

Esta migração cria as tabelas necessárias e não apaga tabelas antigas. Não uses `--reset` no deploy. Se a tua instância contém tabelas antigas além das três, faz primeiro backup e planeia uma limpeza manual separada. Este projecto não apaga dados automaticamente.
