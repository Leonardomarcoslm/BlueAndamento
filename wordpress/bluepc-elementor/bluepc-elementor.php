<?php
/**
 * Plugin Name: BluePC Elementor
 * Description: Blocos visuais editáveis, migração reversível e modelos globais para o site BluePC.
 * Version: 1.0.0
 * Requires Plugins: elementor
 */
defined('ABSPATH') || exit;
function bpe_catalog(){static $data;return $data??=json_decode(file_get_contents(__DIR__.'/catalog.json'),true);}
function bpe_enabled($id=0){return (bool)get_post_meta($id?:get_queried_object_id(),'_bpe_enabled',true);}
add_action('elementor/widgets/register',function($manager){require_once __DIR__.'/widget.php';foreach(bpe_catalog()['blocks'] as $key=>$block)$manager->register(new BPE_Widget([],['blueprint_key'=>$key]));});
add_action('elementor/elements/categories_registered',function($manager){$manager->add_category('bluepc',['title'=>'BluePC — blocos editáveis','icon'=>'eicon-desktop']);});
add_action('wp_enqueue_scripts',function(){if(!bpe_enabled()&&!isset($_GET['elementor-preview']))return;wp_dequeue_style('bluepc');wp_dequeue_script('bluepc');wp_enqueue_style('bpe-site',plugins_url('assets/site.css',__FILE__),[],'1.0.0');wp_enqueue_style('bpe-editor',plugins_url('compat.css',__FILE__),['bpe-site'],'1.0.0');wp_enqueue_script('bpe-site',plugins_url('assets/site.js',__FILE__),[],'1.0.0',true);},999);
add_filter('template_include',function($template){return bpe_enabled()?__DIR__.'/page.php':$template;},2000);
function bpe_global($kind){$id=(int)get_option('bpe_'.$kind);if($id&&class_exists('Elementor\\Plugin'))echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display($id);}
function bpe_default($field){
 $value=$field['value'];
 if($field['type']==='image')return ['url'=>plugins_url($value,__FILE__),'id'=>0];
 if($field['type']==='logo')return ['url'=>'','id'=>0];
 if($field['type']==='url'){
  $parts=explode('#',$value,2);$map=get_option('bpe_routes',[]);
  if(isset($map[$parts[0]]))$value=get_permalink($map[$parts[0]]).(isset($parts[1])?'#'.$parts[1]:'');
  return ['url'=>$value];
 }
 return $value;
}
require_once __DIR__.'/import.php';
add_action('admin_menu',function(){add_menu_page('BluePC Elementor','BluePC Elementor','manage_options','bluepc-elementor','bpe_screen','dashicons-edit-page',59);});
function bpe_screen(){
 if(!current_user_can('manage_options'))return;
 echo '<div class="wrap"><h1>BluePC — edição no Elementor</h1><p>Edite os textos, imagens, links e textos alternativos de cada bloco. Os blocos também podem ser reordenados, duplicados e removidos no Navegador do Elementor.</p>';
 if(!did_action('elementor/loaded')){echo '<p>Ative o plugin Elementor antes de migrar.</p></div>';return;}
 if(isset($_GET['done']))echo '<div class="notice notice-success"><p>Operação concluída.</p></div>';
 echo '<h2>Modelos globais</h2><p>O cabeçalho e o rodapé são compartilhados entre todas as páginas. As alterações abaixo valem para o site inteiro.</p>';
 foreach(['header'=>'Cabeçalho e navegação','footer'=>'Rodapé e WhatsApp'] as $k=>$label){$id=get_option('bpe_'.$k);if($id)echo '<p><a class="button" href="'.esc_url(admin_url('post.php?post='.(int)$id.'&action=elementor')).'">Editar '.$label.'</a></p>';}
 echo '<h2>Páginas</h2><ul>';
 foreach((array)get_option('bpe_routes',[]) as $file=>$id)echo '<li><a href="'.esc_url(admin_url('post.php?post='.(int)$id.'&action=elementor')).'">'.esc_html(get_the_title($id)).'</a></li>';
 echo '</ul><p>A migração preserva os endereços existentes e salva uma cópia dos dados anteriores. Executar novamente não sobrescreve páginas já migradas. Imagens são importadas para a Biblioteca de mídia.</p><form action="'.esc_url(admin_url('admin-post.php')).'" method="post">';wp_nonce_field('bpe_import');echo '<input type="hidden" name="action" value="bpe_import">';submit_button('Preparar páginas editáveis');echo '</form>';
 if(get_option('bpe_backup')){echo '<h2>Restaurar</h2><p>Retorna o conteúdo e os modelos anteriores. As mídias importadas permanecem na biblioteca.</p><form action="'.esc_url(admin_url('admin-post.php')).'" method="post">';wp_nonce_field('bpe_restore');echo '<input type="hidden" name="action" value="bpe_restore">';submit_button('Restaurar versão anterior','secondary');echo '</form>';}
 echo '</div>';
}
add_action('admin_post_bpe_import',function(){if(!current_user_can('manage_options'))wp_die('Acesso negado');check_admin_referer('bpe_import');bpe_import();wp_safe_redirect(admin_url('admin.php?page=bluepc-elementor&done=1'));exit;});
add_action('admin_post_bpe_restore',function(){if(!current_user_can('manage_options'))wp_die('Acesso negado');check_admin_referer('bpe_restore');bpe_restore();wp_safe_redirect(admin_url('admin.php?page=bluepc-elementor&done=1'));exit;});
