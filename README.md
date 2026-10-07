# BluePC

Site BluePC com tema WordPress, blocos editáveis no Elementor e prévia estática.

## WordPress + Elementor
- `wordpress/bluepc-performance/`: tema do WordPress local.
- `wordpress/bluepc-elementor/`: plugin de blocos editáveis. Requer Elementor.
- [Guia de instalação e edição](wordpress/GUIA-ELEMENTOR.md).

O plugin prepara 12 páginas com textos, imagens e links editáveis por campos no Elementor, além de cabeçalho e rodapé globais. Os blocos têm estrutura visual própria; não são widgets nativos separados para cada texto. A preparação mantém uma cópia reversível dos dados anteriores e não sobrescreve páginas já migradas.

## Site público
[Acessar a prévia BluePC](https://leonardomarcoslm.github.io/BlueAndamento/).

`preview/` contém a versão estática servida pela branch `gh-pages`. GitHub Pages não executa WordPress, Elementor ou PHP. Edições no WordPress não são publicadas automaticamente no Pages. Para usar o editor no site online, instale o tema e o plugin em hospedagem WordPress.

## Conteúdo do repositório
Não inclui banco de dados, credenciais, backups, núcleo WordPress nem plugins de terceiros. Textos, imagens e modelos iniciais acompanham os blocos do plugin.

Marca BluePC, imagens e logos de terceiros permanecem sujeitos aos direitos dos respectivos titulares.

## Atualização de 7 de outubro de 2026

A versão pública em `preview/` foi atualizada a partir da exportação Simply Static fornecida pelo proprietário. Os caminhos foram adaptados para `/BlueAndamento/`; as URLs antigas em `.html` redirecionam para as páginas atuais. O WordPress em `wordpress/` permanece como código da integração anterior; este ZIP é uma exportação estática, não um backup do banco de dados.
