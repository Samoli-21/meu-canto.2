# Instalação local — XAMPP

1. Copia a pasta para `C:\xampp\htdocs\meu-canto`.
2. No phpMyAdmin, cria uma base de dados vazia chamada `meu_canto` com `utf8mb4_unicode_ci`.
3. Copia `.env.example` para `.env` e configura `DB_HOST=127.0.0.1`, `DB_PORT=3306`, `DB_NAME=meu_canto`, `DB_USER=root`, `DB_PASS=` e `DB_SSL=false`.
4. Abre o terminal na raiz do projecto e executa `C:\xampp\php\php.exe database\migrate.php`.
5. Define `ADMIN_NAME`, `ADMIN_EMAIL` e `ADMIN_PASSWORD` no `.env` e executa `C:\xampp\php\php.exe database\create-admin.php`.
6. Abre `http://localhost/meu-canto/public`.

O DocumentRoot recomendado é a pasta `public/`. Para converter uma base antiga para as três tabelas novas, faz uma cópia de segurança e usa `database/migrate.php --reset`. Este comando elimina todas as tabelas existentes na base seleccionada.

Extensões PHP necessárias: `pdo_mysql`, `mbstring`, `fileinfo` e `openssl`.
