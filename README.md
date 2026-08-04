# Topic Translate Single

Extensão para phpBB 3.3 que traduz individualmente o conteúdo de um post com uma integração baseada no widget gratuito do GTranslate e no mecanismo do Google Translate. A interface do fórum, os demais posts, assinaturas e anexos permanecem no idioma original.

## Principais recursos

- Tradução individual por post.
- O post original nunca é substituído ou reescrito no DOM.
- Um único seletor de idiomas compartilhado por toda a página.
- Bandeiras locais ao lado dos idiomas no botão e no menu suspenso, sem dependência de CDN.
- Carregamento do serviço externo somente após o clique, por padrão.
- Botões para restaurar o conteúdo original e repetir o último idioma.
- Idioma de origem separado dos idiomas de destino.
- Seleção de fóruns pelo nome, nos modos inclusão ou exclusão.
- Idioma original global com substituição opcional por fórum ou subfórum.
- Pesquisa e seleção rápida de idiomas no ACP.
- Menus recolhíveis de múltipla escolha para manter o ACP compacto.
- Opção para lembrar o último idioma no navegador.
- Blocos técnicos, como `code`, `pre` e `.codebox`, marcados para não serem traduzidos.
- Interface responsiva, compatível com teclado, leitores de tela e páginas RTL.
- Paletas automática, clara e escura configuráveis no ACP.
- Proteção contra conflito com outro widget GTranslate na mesma página.
- Traduções em inglês, francês e português do Brasil.

## Requisitos

- phpBB 3.3.0 ou superior.
- PHP 7.4 ou superior.
- Estilo prosilver ou derivado.
- Acesso do navegador aos serviços externos do Google Translate.

## Instalação

O ZIP de distribuição contém a estrutura:

```text
mundophpbb/topictranslatesingle/
```

1. Extraia o ZIP.
2. Envie `mundophpbb/topictranslatesingle` para a pasta `ext/` do phpBB.
3. No ACP, acesse **Personalizar → Gerenciar extensões**.
4. Ative **Topic Translate Single**.
5. Configure a extensão em **Extensões → Tradução de Tópicos**.

Ao atualizar uma instalação existente, desative a extensão sem excluir os dados, substitua os arquivos, reative-a e limpe o cache do phpBB.

## Configuração

### Idiomas

- **Idioma original do fórum:** idioma de origem predominante dos posts.
- **Idiomas de destino:** idiomas oferecidos aos visitantes.
- **Nomes nativos:** apresenta os idiomas na escrita correspondente quando suportado.
- **Detectar idioma do navegador:** traduz automaticamente o post aberto para o idioma detectado pelo widget; fica desativado por padrão.

### Fóruns

- **Ativar somente nos selecionados:** a tradução aparece apenas nos fóruns marcados.
- **Ativar em todos, exceto nos selecionados:** os fóruns marcados ficam bloqueados.
- Sem fóruns selecionados, o recurso permanece disponível em todos os tópicos.
- **Idioma original por fórum:** use somente nas seções cujo idioma predominante seja diferente do idioma global.
- Quando houver uma substituição, o idioma global será incluído automaticamente como destino. Exemplo: fórum global em português e seção em inglês permitem traduzir essa seção para português.

### Aparência

- **Automática:** analisa a cor de fundo do estilo e, quando necessário, usa a preferência clara ou escura do navegador.
- **Clara ou escura:** força a paleta escolhida em todos os estilos.

### Privacidade e desempenho

- **Carregar somente após o clique:** recomendado e ativado por padrão.
- **Lembrar último idioma:** utiliza `localStorage` no navegador do visitante.

A integração é baseada no widget do GTranslate e utiliza recursos externos do Google Translate. Ao usar a tradução, conteúdo visível e dados técnicos da requisição podem ser processados por terceiros. Avalie sua política de privacidade antes de habilitar o recurso em fóruns privados.

## Funcionamento e limitações

- Somente um post permanece traduzido por vez. Ao traduzir outro post, o anterior volta ao conteúdo original.
- A tradução é automática e pode conter erros.
- A versão gratuita funciona no navegador, usa cookies ou sessão e não cria URLs traduzidas indexáveis.
- As traduções da versão gratuita não são armazenadas e não oferecem benefício de SEO.
- Bloqueadores de conteúdo, políticas CSP ou filtros de rede ainda podem impedir o serviço de tradução, mas o seletor local continuará visível e exibirá um aviso claro.
- Quando outro GTranslate já está presente, a tradução individual é desativada com um aviso para evitar disputa por `window.gtranslateSettings`.
- A interface não depende mais do script remoto `widgets/latest/dwf.js`; os idiomas configurados são renderizados pela própria extensão.

## Compatibilidade com CSP

O código próprio da extensão não depende de JavaScript inline. A política CSP do fórum precisa permitir, em `script-src`, `style-src` e `connect-src` conforme aplicável, os recursos oficiais usados pelo Google Translate, incluindo `translate.googleapis.com`, `translate-pa.googleapis.com`, `translate.google.com` e `www.gstatic.com`.

## Atualização para 1.6.5

Ao atualizar da série 1.5.x, as migrações criam automaticamente as opções de carregamento sob demanda, memorização do idioma, modo da lista de fóruns, paleta e idiomas específicos por fórum. A antiga opção “Modo de compatibilidade” deixa de ser utilizada e é substituída pela opção mais clara “Carregar somente após o clique”. A atualização para 1.6.5 preserva as configurações existentes; todos os fóruns começam usando o idioma global até que uma substituição seja escolhida. Limpe o cache do phpBB e o cache do navegador para substituir o JavaScript e o CSS das versões anteriores.

## Licença

GPL-2.0-or-later.

As bandeiras SVG são provenientes do projeto Flag Icons 7.5.0. A licença MIT correspondente está incluída em `styles/prosilver/theme/images/flags/LICENSE.flag-icons`.

## Suporte

- Projeto: https://github.com/mundophpbb/topictranslatesingle
- Autor: Chico Gois / Mundo phpBB
