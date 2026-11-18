# Como substituir o projecto inteiro no GitHub (Windows)

Este guia actualiza o repositório `Samoli-21/meu-canto.2` com o projecto completo do ZIP. A estrutura nova fica directamente na raiz do GitHub — sem a pasta exterior `meu-canto-render-tidb`.

## 1. Fazer cópia de segurança

Antes de substituir qualquer coisa, guarda uma cópia do repositório actual ou descarrega-o como ZIP no GitHub. Não apagues a base de dados TiDB: este processo altera apenas ficheiros do projecto.

## 2. Descarregar e extrair o ZIP

1. Descarrega `meu-canto-novo-render-tidb.zip`.
2. No Explorador do Windows, clica com o botão direito e escolhe **Extrair tudo**.
3. Abre a pasta extraída. Deves ver directamente `Dockerfile`, `README.md`, `.env.example`, `app`, `config`, `database`, `docker`, `docs`, `public`, `routes` e `storage`.
4. Se vires apenas uma pasta `meu-canto-novo` que contém estes elementos, abre-a; são os elementos internos que devem ser copiados para a raiz do repositório.

## 3. Clonar o repositório numa pasta nova

Abre PowerShell numa localização em que tenhas espaço, por exemplo o Desktop, e executa:

```powershell
cd $HOME\Desktop
git clone https://github.com/Samoli-21/meu-canto.2.git meu-canto-2-atualizacao
```

Se o repositório for privado, autentica-te quando o GitHub solicitar.

## 4. Substituir o conteúdo da pasta clonada

Assumindo que extraíste o ZIP para `C:\Users\TEU_UTILIZADOR\Downloads\meu-canto-novo`, ajusta o caminho abaixo para a localização real. O comando remove os ficheiros antigos do clone, mas preserva a pasta `.git` que mantém a ligação ao GitHub.

```powershell
$repo = "$HOME\Desktop\meu-canto-2-atualizacao"
$novo = "$HOME\Downloads\meu-canto-novo"

Get-ChildItem -LiteralPath $repo -Force |
  Where-Object { $_.Name -ne ".git" } |
  Remove-Item -Recurse -Force

Get-ChildItem -LiteralPath $novo -Force |
  Copy-Item -Destination $repo -Recurse -Force
```

Se o conteúdo extraído estiver dentro de uma subpasta, aponta `$novo` para essa subpasta. Confirma que `Dockerfile` está em `$repo\Dockerfile`, e não em `$repo\meu-canto-novo\Dockerfile`.

## 5. Rever e enviar as alterações

```powershell
cd "$HOME\Desktop\meu-canto-2-atualizacao"
git status
git add -A
git status
git commit -m "Recria Meu Canto com layout responsivo e deploy Render"
git push origin main
```

Se a branch de produção não for `main`, substitui `main` pelo nome correcto. O segundo `git status` deve mostrar os ficheiros a serem enviados. Revê antes do commit.

## 6. Corrigir os caminhos no Render para a nova raiz

Como o `Dockerfile` agora está directamente na raiz do repositório, no Render → serviço `meu-canto.2` → Settings → Build & Deploy configura:

| Campo | Valor |
|---|---|
| Root Directory | vazio |
| Docker Build Context Directory | `.` |
| Dockerfile Path | `Dockerfile` |

Guarda e faz **Manual Deploy → Deploy latest commit** (ou deixa o Auto-Deploy iniciar após o push). Não voltes a colocar `meu-canto-render-tidb` nesses campos.

## 7. Preservar as variáveis e a base TiDB

- As variáveis em Render → Environment permanecem guardadas no serviço; não as coloques no GitHub.
- Confirma `DB_HOST`, `DB_PORT`, `DB_NAME=meu_canto`, `DB_USER`, `DB_PASS`, `DB_SSL=true`, `DB_SSL_CA`.
- A base de dados não é substituída pelo upload de ficheiros.
- A migração usa `CREATE TABLE IF NOT EXISTS`; não apaga as três tabelas nem os registos existentes.
- Se já existe administrador, o projecto preserva a conta e a palavra-passe actuais.

## 8. Confirmar o deploy

Nos Logs, espera ver `Migração concluída`, `A iniciar Apache` e Apache a correr. Testa `https://TEU-SERVICO.onrender.com/healthz` e depois as páginas do site.

## Alternativa: GitHub Desktop

1. Clona `Samoli-21/meu-canto.2` para uma pasta nova usando GitHub Desktop.
2. Abre a pasta local do repositório.
3. Faz uma cópia de segurança dos ficheiros actuais.
4. Copia para essa pasta o conteúdo interno do ZIP, substituindo os ficheiros.
5. Confirma que `Dockerfile` fica na raiz, não dentro de outra subpasta.
6. No GitHub Desktop, revê as alterações, escreve uma mensagem de commit e escolhe **Commit to main**.
7. Clica em **Push origin**.

Nunca copies o ficheiro `.env` local para o GitHub. O pacote inclui apenas `.env.example` sem credenciais reais.
