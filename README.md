# Topic Translate Single

[![phpBB 3.3](https://img.shields.io/badge/phpBB-3.3-brightgreen.svg)](https://www.phpbb.com/)
[![License: GPL v2](https://img.shields.io/badge/License-GPL%20v2-blue.svg)](https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html)
[![GitHub release](https://img.shields.io/github/release/mundophpbb/topictranslatesingle.svg)](https://github.com/mundophpbb/topictranslatesingle/releases)
[![GitHub stars](https://img.shields.io/github/stars/mundophpbb/topictranslatesingle.svg?style=social)](https://github.com/mundophpbb/topictranslatesingle/stargazers)

Extensão phpBB que permite **tradução individual de tópicos/posts** usando o widget gratuito do GTranslate (baseado no Google Translate).  
O usuário clica em um botão discreto no post e escolhe o idioma — a tradução acontece apenas naquele post, sem afetar o resto do fórum.

Ideal para fóruns multilíngues ou com público internacional, sem precisar de tradução manual ou plugins pesados.

## Recursos principais

- Tradução **por post individual** (não traduz o fórum inteiro).
- Botão simples no postbody com dropdown inline de idiomas.
- Configuração fácil no ACP:
  - Idioma padrão.
  - Seleção de idiomas disponíveis (mais de 100 suportados, com os mais comuns pré-selecionados).
- Layout limpo e moderno no ACP (fonte Bai Jamjuree, design flat 2026).
- Correção automática de layout da tradução (evita quebras em BBCode).
- Crédito obrigatório ao GTranslate (conforme termos free).
- Compatível com prosilver e estilos derivados.

## Screenshots

![Configurações no ACP](screenshots/acp_settings.png)
*Painel de configuração no ACP — idiomas populares no topo*

![Botão no post](screenshots/button_post.png)
*Botão discreto no canto do post*

![Dropdown de idiomas](screenshots/dropdown_open.png)
*Dropdown inline com bandeiras 3D*

![Tradução em ação](screenshots/translation_example.png)
*Exemplo de tradução para espanhol*

## Instalação

1. Baixe a última versão em [Releases](https://github.com/mundophpbb/topictranslatesingle/releases).
2. Descompacte e envie a pasta `mundophpbb/topictranslatesingle` para `/ext/` do seu phpBB.
3. Vá ao ACP → Personalizar → Gerenciar extensões.
4. Ative **Topic Translate Single**.

Pronto! A extensão já vem com idiomas comuns pré-configurados.

## Configuração

No ACP → Extensões → Topic Translate Single → Configurações:

- **Idioma Padrão**: Inglês (base do GTranslate).
- **Idiomas Disponíveis**: Selecione os que quiser no menu (Ctrl+clique). Os mais usados já vêm marcados.

> Dica: A maioria dos fóruns usa menos de 15 idiomas. Idiomas raros estão no final da lista.

## Requisitos

- phpBB 3.3.x ou superior
- Estilo baseado em prosilver (testado em prosilver e derivados)
- Conexão à internet (para o widget GTranslate)

## Limitações (versão free do GTranslate)

- Quota diária/mensal gratuita (pode falhar em fóruns muito grandes).
- Para uso intenso, considere a versão paid do GTranslate.

## Licença

GPL-2.0 — livre para usar, modificar e distribuir.

## Suporte e Feedback

- Tópico de discussão: [Link pro seu fórum ou phpBB.com quando criar]
- Issues aqui no GitHub: [Reportar bug ou sugestão](https://github.com/mundophpbb/topictranslatesingle/issues)

Feito com ❤️ por Chico Gois (@ChicoGois2)

---

### English Description

**Topic Translate Single** is a phpBB extension that allows **per-post/topic translation** using the free GTranslate widget (powered by Google Translate).  
Users click a discreet button on the post to select a language — translation applies only to that post.

Perfect for multilingual or international forums without manual translations.

- Download: [Latest release](https://github.com/mundophpbb/topictranslatesingle/releases)
- Support: Open an issue or contact @ChicoGois2
