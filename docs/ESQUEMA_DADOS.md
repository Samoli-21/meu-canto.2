# Modelo de dados — três entidades funcionais

A base do Meu Canto foi reduzida a **três tabelas de negócio**. O papel do administrador e do visitante registado é tratado no campo `usuarios.tipo`; não existe uma tabela de papéis. O directório profissional é separado do utilizador comum e pode ser associado a uma conta, quando a candidatura é submetida por alguém autenticado.

## 1. `usuarios`

| Atributo | Tipo | Obrigatório | Finalidade |
|---|---|---:|---|
| `id` | BIGINT, PK | Sim | Identificador da conta |
| `nome` | VARCHAR(150) | Sim | Nome do utilizador |
| `email` | VARCHAR(190), único | Sim | Identificador de login e contacto |
| `telefone` | VARCHAR(30) | Não | Contacto facultativo |
| `password_hash` | VARCHAR(255) | Sim | Hash criado por `password_hash()`; nunca armazena a palavra-passe em texto |
| `tipo` | ENUM(`admin`,`utilizador`) | Sim | Distingue administração de conta comum |
| `estado` | ENUM(`ativo`,`suspenso`) | Sim | Controla o acesso à conta |
| `ultimo_acesso` | DATETIME | Não | Último login concluído |
| `criado_em` | DATETIME | Sim | Data de criação |
| `actualizado_em` | DATETIME | Sim | Última actualização |

## 2. `profissionais_saude`

| Atributo | Tipo | Obrigatório | Finalidade |
|---|---|---:|---|
| `id` | BIGINT, PK | Sim | Identificador do perfil profissional |
| `usuario_id` | BIGINT, FK → usuarios | Não | Liga a candidatura à conta do profissional, se existir |
| `nome` | VARCHAR(150) | Sim | Nome público/profissional |
| `email` | VARCHAR(190) | Sim | Contacto profissional |
| `telefone` | VARCHAR(30) | Sim | Contacto profissional |
| `cedula_profissional` | VARCHAR(80), único | Sim | Número de registo/cédula a verificar |
| `especialidade` | VARCHAR(140) | Sim | Área de actuação |
| `formacao` | TEXT | Não | Formação académica e profissional |
| `experiencia_anos` | SMALLINT | Não | Anos de experiência declarados |
| `biografia` | TEXT | Sim | Apresentação profissional |
| `foto_url` | VARCHAR(500) | Não | Fotografia por URL HTTPS |
| `municipio` | VARCHAR(100) | Não | Município de atendimento |
| `provincia` | VARCHAR(100) | Sim | Província de atendimento |
| `modalidade` | ENUM(`presencial`,`online`,`ambos`) | Sim | Modalidade de atendimento |
| `morada_consultorio` | VARCHAR(255) | Não | Local de atendimento presencial |
| `estado` | ENUM(`pendente`,`aprovado`,`rejeitado`,`suspenso`) | Sim | Moderação do perfil; apenas `aprovado` é público |
| `nota_revisao` | TEXT | Não | Nota interna da decisão administrativa |
| `revisor_id` | BIGINT, FK → usuarios | Não | Administrador que tomou a decisão |
| `submetido_em` | DATETIME | Sim | Data da candidatura |
| `revisto_em` | DATETIME | Não | Data da última revisão |
| `actualizado_em` | DATETIME | Sim | Última actualização |

## 3. `conteudos_site`

| Atributo | Tipo | Obrigatório | Finalidade |
|---|---|---:|---|
| `id` | BIGINT, PK | Sim | Identificador do conteúdo |
| `slug` | VARCHAR(190), único | Sim | Endereço amigável `/informacao/{slug}` |
| `tipo` | ENUM(`artigo`,`recurso`,`pagina`,`destaque`) | Sim | Classificação editorial |
| `titulo` | VARCHAR(190) | Sim | Título apresentado no site |
| `resumo` | VARCHAR(500) | Não | Texto breve para cartões e listagens |
| `corpo` | MEDIUMTEXT | Sim | Conteúdo em texto simples |
| `imagem_url` | VARCHAR(500) | Não | Imagem por URL HTTPS |
| `estado` | ENUM(`rascunho`,`publicado`,`arquivado`) | Sim | Define se o conteúdo é visível ao público |
| `ordem` | INT | Sim | Prioridade de apresentação nos destaques |
| `autor_id` | BIGINT, FK → usuarios | Não | Administrador que criou o conteúdo |
| `criado_em` | DATETIME | Sim | Data de criação |
| `actualizado_em` | DATETIME | Sim | Última actualização |

## Relações

- `usuarios` 1:N `profissionais_saude` (opcional, através de `usuario_id`).
- `usuarios` 1:N `profissionais_saude` como revisor (através de `revisor_id`).
- `usuarios` 1:N `conteudos_site` como autor (através de `autor_id`).
- Não existe uma quarta tabela de conversas: o histórico de chat permanece apenas na memória do browser e é enviado ao fornecedor de IA apenas durante a conversa.

## Regras de negócio

1. A inscrição pública só pode criar `tipo='utilizador'`.
2. O primeiro administrador é criado por CLI/arranque com `ADMIN_EMAIL` e `ADMIN_PASSWORD`; a inscrição pública não permite escolher o papel admin.
3. Candidaturas profissionais começam como `pendente`.
4. Só o administrador pode aprovar, rejeitar ou suspender profissionais.
5. O directório público consulta somente `estado='aprovado'`.
6. Conteúdos em rascunho ou arquivados não são publicados.
