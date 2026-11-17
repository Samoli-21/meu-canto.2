# Meu Canto — versão pronta para Render + TiDB Cloud

Plataforma em PHP 8.2 com arquitectura MVC, PDO e TiDB Cloud/MySQL. O produto é uma plataforma informativa de bem-estar e saúde mental, pensada para Angola. Não faz diagnósticos, não prescreve tratamentos e não substitui profissionais ou serviços de urgência.

## Arquitectura

- PHP 8.2 + Apache em Docker (Render Web Service).
- TiDB Cloud como base de dados, com ligação TLS.
- Três entidades funcionais: `usuarios`, `profissionais_saude` e `conteudos_site`.
- `usuarios.tipo`: `admin` ou `utilizador`.
- Candidaturas profissionais permanecem pendentes até decisão administrativa. Apenas `aprovado` aparece no directório público.
- O chat da Canto guarda o contexto apenas em memória no navegador. Não existe tabela de mensagens/conversas.
- A integração de IA usa uma API compatível com OpenAI Chat Completions e só é activada quando `AI_API_KEY` é configurada.

Modelo de dados e campos: [docs/ESQUEMA_DADOS.md](docs/ESQUEMA_DADOS.md). Guia de deploy: [docs/RENDER_TIDB.md](docs/RENDER_TIDB.md).

## Funcionalidades incluídas

- Páginas públicas originais com identidade Meu Canto, responsivas e com transição entre páginas.
- Registo, login, logout e área pessoal.
- Papéis de conta: administrador e utilizador comum.
- Candidatura de profissionais de saúde e painel para aprovar, rejeitar ou suspender perfis.
- Directório público que apresenta exclusivamente profissionais aprovados.
- CMS simples para criar, editar, publicar, arquivar e remover artigos, recursos, páginas e destaques.
- Página de conversa com a Canto, sugestões rápidas, histórico temporário em memória do browser e integração opcional com IA.
- Dockerfile e arranque automático no Render; o arranque verifica/cria as três tabelas e cria o primeiro administrador se as variáveis correspondentes estiverem definidas.

## Publicar no Render

1. Envia a pasta do projecto para um repositório GitHub.
2. No Render, cria **New → Web Service** e liga o repositório.
3. Escolhe **Docker**. O Render detecta o `Dockerfile` da raiz.
4. Adiciona as variáveis de ambiente descritas em `docs/RENDER_TIDB.md`.
5. Define `DB_SSL=true` e adiciona os dados exactos de ligação mostrados no botão **Connect** da tua instância TiDB Cloud. O container escuta na variável `PORT` (por defeito `10000`, a porta esperada pelo Render).
6. Em Render, configura os IPs de saída do serviço na lista de redes autorizadas do TiDB Cloud.
7. Faz o deploy. O `docker/entrypoint.sh` executa `database/migrate.php`, cria o administrador inicial se configurado e inicia o Apache.

## Administração inicial

No Render, define `ADMIN_NAME`, `ADMIN_EMAIL` e `ADMIN_PASSWORD` (mínimo 12 caracteres). No primeiro arranque, será criada uma conta com papel `admin`. Depois da criação, a rotina não altera nem repõe a palavra-passe desse administrador.

A área administrativa fica em `/admin`.

## Desenvolvimento local com XAMPP

1. Cria uma base de dados MySQL/MariaDB vazia chamada `meu_canto`.
2. Copia `.env.example` para `.env` e usa `DB_HOST=127.0.0.1`, `DB_PORT=3306`, `DB_USER=root`, `DB_SSL=false`.
3. Executa `php database/migrate.php` na raiz do projecto.
4. Define temporariamente `ADMIN_NAME`, `ADMIN_EMAIL` e `ADMIN_PASSWORD` no `.env` e executa `php database/create-admin.php`.
5. Aponta o DocumentRoot do Apache para a pasta `public/` ou acede ao `public/` no URL local.

## Base de dados e migração destrutiva

O esquema actual contém apenas as três tabelas funcionais indicadas. Se a base já tiver as tabelas antigas da versão anterior e pretenderes substituí-las, faz primeiro uma cópia de segurança e executa:

```bash
php database/migrate.php --reset
```

**Atenção:** `--reset` apaga todas as tabelas da base definida em `.env` ou no ambiente do Render. Não é necessário numa base TiDB nova. Nunca o executes numa base com dados que precisas de conservar.

## Chat e privacidade

Sem `AI_API_KEY`, a Canto responde em modo de orientação básica para demonstrar a experiência. Para activar a IA, configura `AI_API_URL`, `AI_API_KEY` e `AI_MODEL` no Render. As mensagens não são inseridas na base de dados do Meu Canto; quando a IA está ligada, o texto enviado é transmitido ao fornecedor indicado em `AI_API_URL` e fica sujeito às condições e políticas desse fornecedor. Não solicites nem introduzas dados de identificação ou informações clínicas sensíveis de terceiros.

## Verificações

- Página inicial: `/`
- Conversa Canto: `/canto`
- Directório: `/profissionais`
- Registo/login: `/registar` e `/entrar`
- Área de utilizador: `/conta`
- Administração: `/admin`
- Estado técnico (apenas quando `APP_DEBUG=true`): `/estado`

## Limites conhecidos

- A verificação das cédulas profissionais é um processo administrativo; o sistema não consulta automaticamente uma API oficial de registo profissional. O administrador deve confirmar a credencial junto da entidade competente antes de aprovar.
- Upload de fotografias/documentos não está activado nesta versão; as fotografias dos profissionais podem ser indicadas por URL HTTPS.
- O Render filesystem é efémero por defeito. Para uploads persistentes, configura um serviço de object storage externo ou um disco persistente compatível com o plano escolhido.
- A política de privacidade, textos de saúde e procedimentos de encaminhamento precisam de revisão jurídica e clínica local antes da utilização pública.
