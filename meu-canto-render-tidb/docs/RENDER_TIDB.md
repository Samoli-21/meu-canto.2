# Publicação do Meu Canto — Render + TiDB Cloud

## 1. Repositório

Confirma que o repositório contém `Dockerfile`, `.dockerignore`, `docker/entrypoint.sh`, `database/migrations/001_schema.sql` e `public/index.php`. Nunca envies `.env`, palavras-passe ou chaves da IA para o GitHub.

## 2. TiDB Cloud

No painel da instância, abre **Connect** e copia o host, a porta, o nome da base, o utilizador e a palavra-passe. Usa o endereço e o formato de utilizador apresentados pelo TiDB; não assumas que o utilizador é simplesmente `root`.

A instância deve aceitar ligações TLS. Em TiDB Cloud Starter/Essential no modelo de endpoint partilhado, o nome de utilizador normalmente inclui o prefixo da instância (ex.: `prefixo.root`); copia o formato exacto de **Connect**, pois o modelo do endpoint pode variar com o plano e a data de criação. Para TiDB Cloud Starter/Essential, o certificado é emitido por uma CA pública e o Dockerfile instala `ca-certificates`; o PHP usa o bundle `/etc/ssl/certs/ca-certificates.crt`. Para TiDB Cloud Premium/BYOC ou planos que disponibilizem um CA próprio, descarrega o certificado no painel **Connect → CA cert**, inclui-o num caminho como `certs/tidb-ca.pem` e define `DB_SSL_CA=/var/www/html/certs/tidb-ca.pem`. Segue sempre o modelo de ligação apresentado para o teu plano.

## 3. Criar Web Service

- Render Dashboard → **New** → **Web Service**.
- Liga o repositório GitHub.
- Runtime: **Docker**.
- Dockerfile: `./Dockerfile`.
- Branch: a branch de produção (por exemplo, `main`).
- Region: escolhe uma região disponível próxima da instância TiDB, quando possível.
- Porta HTTP: o container escuta em `PORT` (por defeito `10000`, que é a porta esperada pelo Render).
- Não é necessário definir Build Command nem Start Command: o Dockerfile define o arranque.

## 4. Variáveis de ambiente no Render

Adiciona em **Environment**:

| Variável | Valor |
|---|---|
| `APP_NAME` | `Meu Canto` |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_TIMEZONE` | `Africa/Luanda` |
| `DB_HOST` | Host exacto do TiDB |
| `DB_PORT` | Porta mostrada no Connect (normalmente `4000`) |
| `DB_NAME` | Base de dados no TiDB |
| `DB_USER` | Utilizador mostrado no Connect |
| `DB_PASS` | Palavra-passe do TiDB |
| `DB_SSL` | `true` |
| `DB_SSL_CA` | `/etc/ssl/certs/ca-certificates.crt` ou caminho do CA dedicado |
| `ADMIN_NAME` | Nome do administrador inicial |
| `ADMIN_EMAIL` | Email do administrador inicial |
| `ADMIN_PASSWORD` | Palavra-passe forte com pelo menos 12 caracteres |
| `AI_API_URL` | Endpoint compatível com Chat Completions |
| `AI_API_KEY` | Chave privada do fornecedor de IA; opcional |
| `AI_MODEL` | Modelo autorizado pelo fornecedor; opcional |

Não coloques a chave da IA no JavaScript, em HTML, nem em ficheiros versionados.

## 5. Firewall / Authorized Networks

No serviço Render, abre **Connect → Outbound** e consulta os intervalos CIDR de saída correspondentes à região. Adiciona esses intervalos à lista **Authorized Networks** da instância TiDB. Não deixes uma regra pública para todos os endereços (`0.0.0.0/0`) como configuração permanente. O Render pode usar intervalos partilhados/regionais; confirma a informação do serviço e a documentação actual antes de alterar o firewall.

## 6. Deploy e inicialização

Ao iniciar o container:

1. `php database/migrate.php` cria as três tabelas se ainda não existirem.
2. `php database/create-admin.php` cria o primeiro administrador se `ADMIN_EMAIL` e `ADMIN_PASSWORD` estiverem definidos e ainda não existir nenhum.
3. Apache inicia na porta `PORT` definida pelo Render (por defeito `10000`).

O deploy é considerado pronto quando o log indicar `Migração concluída` e `Apache` tiver iniciado. Abre o URL `onrender.com` atribuído ao serviço e testa `/`, `/canto`, `/entrar` e `/admin`.

## 7. Já existe uma base com o esquema antigo?

O projecto anterior tinha várias tabelas adicionais. Se queres mesmo ficar apenas com estas três entidades, faz uma cópia de segurança e executa uma única vez `php database/migrate.php --reset` contra a base correcta. Esta opção apaga **todas** as tabelas da base configurada e cria novamente `usuarios`, `profissionais_saude` e `conteudos_site`. Não a uses como comando normal de arranque.

## 8. Problemas frequentes

- **Access denied / erro de autenticação:** volta a copiar o nome de utilizador e palavra-passe apresentados no Connect do TiDB.
- **Connection timed out:** confirma Authorized Networks e os endereços de saída do Render.
- **SSL connection error:** confirma `DB_SSL=true`, a presença do CA e o caminho em `DB_SSL_CA`.
- **Página 404 em rotas MVC:** confirma que o serviço usa o Dockerfile da raiz e que o DocumentRoot do Apache aponta para `/var/www/html/public`.
- **A Canto só responde em modo básico:** configura `AI_API_KEY`, `AI_API_URL` e `AI_MODEL`, depois executa novo deploy.
- **Fotos externas não aparecem:** usa URLs HTTPS; o Content Security Policy permite imagens HTTPS.
