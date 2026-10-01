# BluePC + Elementor

## Instalação
1. Instale e ative o Elementor oficial (gratuito). O Elementor Pro não é obrigatório para estes blocos.
2. Instale e ative o tema `bluepc-performance.zip` atualizado.
3. Instale e ative `bluepc-elementor.zip` em Plugins → Adicionar plugin → Enviar plugin.
4. Abra **BluePC Elementor → Preparar páginas editáveis**. Isso migra as 12 páginas e importa as imagens para a Biblioteca de mídia.
5. Use os links da tela **BluePC Elementor** para abrir cada página no editor.

## Como editar
- Selecione um bloco BluePC no Navegador do Elementor. Os campos estão nas abas **Textos**, **Imagens e logos**, **Links** e **Acessibilidade e rótulos**.
- Cada seção é um widget BluePC personalizado. Seu conteúdo é editável por campos; a estrutura interna e o visual do bloco são definidos pelo plugin. Não se trata de uma conversão de cada parágrafo em widget nativo separado.
- É possível reordenar, duplicar e remover as seções, e adicionar widgets nativos do Elementor ao lado delas.
- Edite cabeçalho e rodapé pelos atalhos globais da tela BluePC Elementor. O rodapé inclui o botão de WhatsApp.
- Os três banners aparecem empilhados no editor para facilitar a edição. No site, continuam no slider automático.
- Textos desenhados dentro dos banners são parte da imagem: substitua a arte pela Biblioteca de mídia para alterá-los.
- As logos dos marketplaces têm sua arte original branca como padrão. Selecione outra imagem para substituir uma logo.
- Publique/atualize no Elementor para salvar. Não execute a antiga configuração de páginas do tema depois da migração.

## Segurança e restauração
A preparação guarda os metadados e o conteúdo anteriores, não altera produtos/pedidos e preserva as URLs existentes. Repetir a preparação não sobrescreve documentos já migrados. **Restaurar versão anterior** recupera os documentos anteriores; mídias e modelos globais ficam disponíveis. Faça também um backup completo antes de usar em produção.

## Hospedagem
O endereço github.io continua sendo uma cópia estática e não executa WordPress nem Elementor. Alterações feitas no Elementor não são sincronizadas automaticamente para o GitHub Pages. Para publicar a versão editável, use hospedagem WordPress com PHP e banco de dados.

## Validação
Testado com a versão do Elementor instalada no ambiente de trabalho: renderização de 12 páginas; edição de texto, imagem e link; disponibilidade dos controles; migração repetida sem sobrescrita; restauração e nova migração. O plugin não inclui nem distribui Elementor Pro ou plugins de terceiros.
