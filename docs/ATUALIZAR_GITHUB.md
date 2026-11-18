# Actualizar o repositório `Samoli-21/meu-canto.2` no Windows

Este pacote contém um projecto completo e independente. A página inicial está em `app/Views/public/home.php` e foi mantida com base na [versão do ficheiro no GitHub](https://github.com/Samoli-21/meu-canto.2/blob/main/app/Views/public/home.php). Não copies apenas essa view: ela depende do layout, helpers, controladores, rotas, CSS, imagens e JavaScript incluídos no pacote.

## 1. Extrair o ZIP

1. Descarrega o ZIP `meu-canto-render-tidb-completo.zip`.
2. Clica com o botão direito → **Extrair tudo**.
3. Abre a pasta extraída. Confirma que vês directamente `Dockerfile`, `app`, `config`, `database`, `docker`, `docs`, `public`, `routes` e `storage`.
4. Não copies uma pasta exterior adicional. O `Dockerfile` tem de ficar na raiz do repositório.

## 2. Fazer cópia de segurança do repositório local

O guia assume que já tens uma cópia clonada em:

```text
C:\Users\joaquim.samoli\Desktop\meu-canto-publicar-2
```

Se essa pasta for diferente, altera `$repo` no comando abaixo. Antes de substituir, cria uma cópia de segurança no Desktop:

```powershell
$repo = "C:\Users\joaquim.samoli\Desktop\meu-canto-publicar-2"
$novo = "C:\Users\joaquim.samoli\Downloads\meu-canto-render-tidb-completo"
$backup = "$repo-backup-$(Get-Date -Format yyyyMMdd-HHmm)"

if (!(Test-Path "$repo\.git")) { throw "Não encontrei .git em $repo" }
if (!(Test-Path "$novo\Dockerfile")) { throw "Não encontrei Dockerfile em $novo" }

Copy-Item -LiteralPath $repo -Destination $backup -Recurse
Write-Host "Cópia de segurança criada em: $backup"
```

Altera `$novo` para a pasta real onde extraíste o ZIP.

## 3. Substituir o código antigo

Este comando remove o conteúdo antigo do clone, mas **preserva `.git`**, que mantém a ligação e o histórico do GitHub. Preserva também um `.env` local, se existir, porque contém configurações privadas de desenvolvimento.

```powershell
Get-ChildItem -LiteralPath $repo -Force |
  Where-Object { $_.Name -notin @('.git', '.env') } |
  Remove-Item -Recurse -Force

Get-ChildItem -LiteralPath $novo -Force |
  Where-Object { $_.Name -ne '.env' } |
  Copy-Item -Destination $repo -Recurse -Force
```

## 4. Confirmar os caminhos essenciais

```powershell
Set-Location $repo

Test-Path .\Dockerfile
Test-Path .\public\index.php
Test-Path .\public\.htaccess
Test-Path .\app\Views\public\home.php
Test-Path .\routes\web.php
Test-Path .\docker\entrypoint.sh
Test-Path .\.git
```

Todos devem devolver `True`. Confirma também que `.env` não aparece como ficheiro a enviar:

```powershell
git status --short
```

O `.gitignore` exclui `.env`. Nunca adiciones credenciais, passwords ou chaves de IA ao GitHub.

## 5. Criar commit e enviar

```powershell
git add -A
git status
git commit -m "Actualiza Meu Canto com projecto completo e configuracao Render"
```

Se a ligação HTTPS do Git no Windows voltar a apresentar o erro Schannel, usa o OpenSSL sem desactivar validação de certificados:

```powershell
git -c http.sslBackend=openssl -c http.version=HTTP/1.1 -c core.compression=0 push origin main
```

Confirma que o GitHub recebeu exactamente o commit local:

```powershell
git rev-parse HEAD
git -c http.sslBackend=openssl -c http.version=HTTP/1.1 ls-remote origin refs/heads/main
```

Os dois hashes devem ser iguais. Se o push falhar com HTTP 408/reset, não apagues a pasta nem cries outro commit. Mantém o commit local e tenta novamente noutra rede, por exemplo hotspot, ou mais tarde.

## 6. Configurar o Render para a nova raiz

O novo pacote coloca o Dockerfile na raiz. No Render → `meu-canto.2` → Settings → Build & Deploy:

| Campo | Valor |
|---|---|
| Root Directory | vazio |
| Docker Build Context Directory | `.` |
| Dockerfile Path | `Dockerfile` |
| Branch | `main` |

Guarda. O Auto-Deploy pode iniciar; caso contrário, usa **Manual Deploy → Deploy latest**.

## 7. Preservar TiDB e variáveis

- Não cries outra instância TiDB.
- Não apagues a base `meu_canto` nem as tabelas existentes.
- As variáveis guardadas em Render → Environment permanecem no serviço. Confirma `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS`, `DB_SSL=true`, `DB_SSL_CA`.
- A migração usa `CREATE TABLE IF NOT EXISTS` para `usuarios`, `profissionais_saude` e `conteudos_site`. Não contém `DROP TABLE`.
- O script de administrador preserva a conta admin já existente.

## 8. Confirmar o deploy

Nos Runtime Logs, procura:

```text
Migração concluída. Entidades de negócio: usuarios, profissionais_saude, conteudos_site.
Já existe um administrador; nenhuma alteração foi feita.
[Meu Canto] A iniciar Apache...
Apache/2.4 ... configured -- resuming normal operations
```

Testa primeiro `https://TEU-SERVICO.onrender.com/healthz` (deve devolver `ok`), depois `/`, `/o-que-sinto`, `/recursos`, `/profissionais`, `/entrar` e `/canto`.
