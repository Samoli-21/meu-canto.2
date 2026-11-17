# Interface visual e funcionalidades — Meu Canto

## Identidade visual

A base visual mantém o fundo marfim `#F8F5F0`, texto verde profundo `#34463D`, verde suave `#DCE8E1`, verde-floresta `#2F5D50`, terracota `#A9502F` e azuis/lilases desaturados. As imagens são apresentadas com saturação moderada e em superfícies pastel para manter coerência com a identidade.

## Páginas diferenciadas

- Página inicial editorial, com temas, recursos e destaques publicados pelo painel.
- Páginas de temas, biblioteca, escola/trabalho e informação editorial.
- Directório com cartões próprios, estado de aprovação visível e formulário de candidatura.
- Chat da Canto, inspirado na referência visual fornecida: cartão marfim, mensagens verde-sálvia, avatar e sugestões rápidas.
- Login/registo e área pessoal em composição própria.
- Administração com navegação lateral verde-floresta, indicadores, tabelas e formulários de gestão.

A navegação interna tem uma transição curta. A animação respeita `prefers-reduced-motion`.

## Fluxos funcionais

1. Visitante pode criar uma conta `utilizador`.
2. Um profissional envia uma candidatura; o registo entra como `pendente`.
3. O administrador entra em `/admin`, revê os dados e aprova, rejeita ou suspende.
4. Só os profissionais com estado `aprovado` são listados em `/profissionais`.
5. O administrador gere artigos, recursos, páginas e destaques em `/admin/conteudos`.
6. O público pode conversar com a Canto em `/canto`. Sem chave de IA há respostas de demonstração guiadas; com chave, o backend usa o endpoint configurado.

## Modelo de dados

O esquema de negócio tem exactamente três tabelas: `usuarios`, `profissionais_saude` e `conteudos_site`. O tipo de utilizador é `admin` ou `utilizador`. O histórico da Canto não é guardado na base de dados.

## Limites e publicação

A análise de cédulas continua a ser humana e deve ser confirmada junto da entidade competente. A aplicação ainda não faz upload de fotografias nem armazena documentos de credenciação. Antes do lançamento público, rever conteúdo clínico, privacidade, contacto de urgência e legislação aplicável em Angola.
