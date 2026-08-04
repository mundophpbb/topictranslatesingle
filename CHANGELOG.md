# Changelog

## 1.6.5

### Seletor de idiomas com bandeiras

- Restauradas as bandeiras ao lado dos nomes dos idiomas no botão e nas opções do menu suspenso.
- Adicionado um menu personalizado e acessível, com suporte a mouse, toque, teclado, foco, `aria-expanded` e `aria-selected`.
- O seletor nativo permanece sincronizado internamente para preservar a integração com a tradução, detecção do navegador e repetição do último idioma.
- As bandeiras SVG são fornecidas localmente pela extensão e não dependem do CDN do GTranslate, mantendo a correção de compatibilidade com Brave da versão 1.6.4.
- Adicionado suporte visual completo para as paletas clara e escura e para dispositivos móveis.
- Os recursos de bandeiras utilizam o projeto Flag Icons 7.5.0, distribuído sob a licença MIT incluída no pacote.

## 1.6.4

### Compatibilidade com Brave e bloqueadores

- O menu de idiomas agora é renderizado localmente pela extensão e aparece imediatamente, sem depender da criação da interface por um script de terceiros.
- Removida a dependência de execução do `widgets/latest/dwf.js`, que podia ser bloqueado pelo Brave Shields e cuja marcação deixou de ser compatível com o seletor esperado pela extensão.
- O carregamento usa primeiro o endpoint `translate.googleapis.com`, reconhecido pelo mecanismo de tradução do Brave, com uma segunda origem oficial como alternativa.
- Quando todas as origens são bloqueadas, o post original é restaurado e o seletor permanece visível com uma mensagem de erro clara.
- A integração continua carregada somente sob demanda por padrão e mantém a detecção de conflito com outros widgets GTranslate/Google Translate.

### Interface e robustez

- O seletor local preserva os idiomas configurados no ACP, o idioma original específico do fórum, nomes nativos, detecção do idioma do navegador e repetição do último idioma.
- Corrigida a compatibilidade com a marcação atual do widget GTranslate, que passou a usar um menu baseado em elementos `div` em vez do `select` anteriormente esperado.
- Adicionados cancelamento seguro de traduções pendentes, tentativa automática por uma origem alternativa e restauração do conteúdo original em falhas de rede.

## 1.6.3

### Idioma por fórum

- Adicionada configuração de idioma original por fórum e subfórum, mantendo um idioma global como padrão.
- Quando uma seção usa outro idioma de origem, o idioma global do fórum é incluído automaticamente como destino da tradução.
- Configurações inválidas ou referentes a idiomas removidos voltam com segurança ao idioma global.
- O mapa de fóruns utiliza o armazenamento de texto longo do phpBB para não ser truncado em instalações com muitas seções.

### ACP compacto

- Idiomas de destino e fóruns habilitados agora usam menus recolhíveis com múltipla escolha.
- Adicionados contadores dinâmicos, pesquisa de fóruns e fechamento automático dos demais menus ao abrir outro.
- A configuração de idioma por fórum fica em um painel compacto, pesquisável e com uma escolha individual por fórum.

## 1.6.2

### Compatibilidade

- Detecta outro widget GTranslate, script externo ou substituição de `window.gtranslateSettings` na mesma página.
- Em caso de conflito, preserva a configuração existente, não carrega um segundo widget e mostra uma mensagem clara ao visitante.

### Aparência

- Adicionada paleta escura completa para o popover, seletor, estados, botões e mensagens.
- Novo modo automático que analisa o fundo do estilo e usa a preferência de cor do navegador como alternativa.
- O ACP agora permite escolher entre paleta automática, clara ou escura.

## 1.6.1

### Correções

- A detecção do idioma do navegador agora inicia a tradução do post ativo mesmo quando o widget pré-seleciona o idioma sem emitir uma troca manual.
- A inicialização passa a usar o evento real de visualização de tópico do phpBB e o `forum_id` fornecido pelo próprio evento.
- URLs reescritas por extensões de SEO deixam de depender de `SCRIPT_NAME`, `PHP_SELF` ou do parâmetro `f` para ativar a tradução.

## 1.6.0

### Segurança e privacidade

- O script externo agora é carregado somente após o clique por padrão.
- Removido JavaScript inline da página pública para melhorar a compatibilidade com CSP.
- Adicionado aviso no ACP sobre processamento por serviços externos.
- Corrigida a remoção de cookies em domínios como `example.com.br`, usando o domínio e caminho configurados no phpBB.
- Removida a limpeza genérica de todos os elementos `.skiptranslate`, evitando interferência em outras extensões.

### Correções

- Corrigida a detecção de páginas: rotas genéricas em `app.php` não são mais tratadas como tópicos.
- Corrigida a identificação do fórum quando a URL possui apenas o parâmetro `t`.
- A restrição configurada por fórum não pode mais ser ignorada quando o parâmetro `f` não está presente.
- O contêiner `notranslate` agora é inserido somente quando a extensão está ativa naquele fórum.
- Corrigidas as chaves de idioma ausentes na tradução francesa.
- Corrigida a ausência do botão para restaurar o original no prosilver.

### Arquitetura e desempenho

- O conteúdo original dos posts não é mais substituído por clones.
- A tradução é renderizada em uma cópia isolada somente do post escolhido.
- Eventos, players, spoilers e estados do post original são preservados.
- Removida a clonagem antecipada de todos os posts da página.
- Implementado um único widget compartilhado, em vez de um widget por post.
- Removida a execução duplicada do listener no cabeçalho e rodapé.
- Unificados os códigos claro e escuro; não existe mais uma cópia divergente do JavaScript.

### ACP e experiência do usuário

- Idioma original separado dos idiomas de destino.
- Fóruns selecionados pelo nome em vez de IDs digitados manualmente.
- Modos de inclusão e exclusão de fóruns.
- Pesquisa de idiomas e botões para selecionar populares, todos ou nenhum.
- Opção para lembrar o último idioma.
- Melhorias de teclado, foco, `aria-live`, `aria-controls`, RTL e dispositivos móveis.
- Blocos de código e outros conteúdos técnicos ficam fora da tradução.

### Distribuição

- Versão atualizada para 1.6.0.
- Dependência de desenvolvimento alinhada à série estável do phpBB 3.3.
- README revisado com limitações reais da versão gratuita e instruções de atualização.
