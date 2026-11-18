# Meu Canto — versão responsiva para Render + TiDB Cloud

Plataforma de informação, bem-estar e orientação inicial sobre saúde emocional, preparada para a realidade angolana. Interface em português (pt-AO), PHP 8.2, MVC, Apache, PDO MySQL e TiDB Cloud.

> O Meu Canto é informativo. Não faz diagnósticos, não prescreve tratamentos e não substitui profissionais de saúde ou serviços de urgência.

## O que está incluído

- Página inicial e páginas editoriais diferenciadas: temas, recursos, escola e trabalho, profissionais, ajuda, sobre, privacidade e acessibilidade.
- Navegação móvel com menu acessível, grelhas responsivas, formulários adaptados a telemóveis e tabelas administrativas com deslocamento horizontal.
- Transições curtas entre páginas, animação de entrada dos blocos e revelação letra a letra nos títulos. Respeita `prefers-reduced-motion`.
- Contas de utilizadores e administradores, autenticação, sessões seguras, CSRF e palavras-passe com `password_hash`.
- Directório de profissionais: candidaturas pendentes, aprovação/rejeição/suspensão no painel e publicação pública apenas dos aprovados.
- Painel administrativo para gerir conteúdos, artigos, recursos, páginas e destaques.
- Chat com a Canto. Sem `AI_API_KEY`, usa respostas orientativas locais; com uma chave e endpoint compatível, encaminha pedidos ao fornecedor configurado. As conversas não são gravadas nas três tabelas.
- Esquema de dados reduzido a exactamente três tabelas de negócio: `usuarios`, `profissionais_saude`, `conteudos_site`.
- Dockerfile PHP 8.2 + Apache, migração idempotente, health check `/healthz` e configuração para Render.

## Estrutura do projecto

O ZIP tem os ficheiros directamente na raiz. Depois de o extrair, a raiz deve conter `Dockerfile`, `docker/`, `app/`, `public/`, `routes/`, `database/` e `config/`. **Não carregues a pasta exterior do ZIP como uma pasta adicional dentro do repositório.**

## Publicar no Render

1. Confirma que a base `meu_canto` já existe na tua instância TiDB Cloud.
2. No Render, usa o Web Service Docker ligado ao repositório GitHub.
3. Como o Dockerfile está na raiz: `Root Directory` vazio; `Docker Build Context Directory` = `.`; `Dockerfile Path` = `Dockerfile`.
4. Configura as variáveis descritas em `docs/RENDER_TIDB.md`.
5. Configura o firewall do TiDB para aceitar os intervalos de saída do Render (não deixes `Allow_all_public_connections` activo permanentemente).
6. Publica e confirma os logs `Migração concluída` e `A iniciar Apache`.
7. Opcionalmente, configura o health check path do Render como `/healthz`.

## Actualizar o GitHub

Segue `docs/ATUALIZAR_GITHUB.md`. Esse guia explica como fazer cópia de segurança, substituir o conteúdo completo do repositório e enviar a nova versão sem colocar `.env` ou credenciais no GitHub.

## Variáveis principais

Consulta `.env.example` para desenvolvimento local e `docs/RENDER_TIDB.md` para produção. As credenciais reais devem ficar apenas em Render → serviço → Environment.

## Base de dados existente

A migração usa `CREATE TABLE IF NOT EXISTS`, não executa `DROP TABLE` e não apaga dados. A criação inicial do administrador não altera uma conta administrativa já existente. Se já existe um administrador no TiDB, continua a usar as credenciais dessa conta; mudar `ADMIN_EMAIL`/`ADMIN_PASSWORD` no Render não redefine automaticamente a palavra-passe existente.

## Testes realizados neste pacote

- Verificação sintáctica de todos os ficheiros PHP com `php -l`.
- Verificação sintáctica do JavaScript com `node --check`.
- Verificação de que o esquema define apenas as três tabelas de negócio e de que a migração não contém instruções destrutivas.
- Teste local do roteador e do endpoint `/healthz` via servidor PHP integrado.
- Verificação do conteúdo do ZIP e de que o Dockerfile está na raiz.

O build de imagem e a ligação real ao TiDB têm de ser confirmados no teu serviço Render, porque dependem da conta, região, firewall e credenciais privadas.
