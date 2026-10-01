<?php
defined('ABSPATH') || exit;
function bpe_save_document($id,$blocks,$media){
 $elements=[];$catalog=bpe_catalog();
 foreach($blocks as $key){$settings=[];foreach($catalog['blocks'][$key]['fields'] as $field_key=>$f){$settings[$field_key]=$f['type']==='image'?($media[$f['value']]??bpe_default($f)):bpe_default($f);}
  $elements[]=['id'=>substr(md5($id.$key),0,7),'elType'=>'widget','widgetType'=>'bpe-'.$key,'settings'=>$settings,'elements'=>[]];
 }
 $data=[['id'=>substr(md5('section'.$id),0,7),'elType'=>'section','settings'=>['layout'=>'full_width','gap'=>'no'],'elements'=>[['id'=>substr(md5('column'.$id),0,7),'elType'=>'column','settings'=>['_column_size'=>100],'elements'=>$elements]]]];
 delete_post_meta($id,'_elementor_element_cache');update_post_meta($id,'_elementor_data',wp_slash(wp_json_encode($data)));update_post_meta($id,'_elementor_edit_mode','builder');update_post_meta($id,'_elementor_template_type',get_post_type($id)==='elementor_library'?'section':'wp-page');update_post_meta($id,'_elementor_version',ELEMENTOR_VERSION);update_post_meta($id,'_bpe_enabled',1);update_post_meta($id,'_elementor_page_settings',['hide_title'=>'yes']);
}
function bpe_import(){
 if(!did_action('elementor/loaded'))wp_die('Ative o Elementor.');
 require_once ABSPATH.'wp-admin/includes/image.php';
 $catalog=bpe_catalog();$backup=get_option('bpe_backup',[]);if(!isset($backup['front']))$backup['front']=['show_on_front'=>get_option('show_on_front'),'page_on_front'=>get_option('page_on_front')];$routes=get_option('bpe_routes',[]);$media=get_option('bpe_media',[]);
 foreach(glob(__DIR__.'/assets/*.webp') as $path){$relative='assets/'.basename($path);if(!empty($media[$relative]['id'])&&get_post($media[$relative]['id']))continue;
  $file=wp_upload_bits('bluepc-'.basename($path),null,file_get_contents($path));if($file['error'])wp_die(esc_html($file['error']));
  $id=wp_insert_attachment(['post_mime_type'=>'image/webp','post_title'=>'BluePC '.pathinfo($path,PATHINFO_FILENAME),'post_status'=>'inherit'],$file['file']);if(is_wp_error($id)||!$id)wp_die('Não foi possível importar a mídia.');wp_update_attachment_metadata($id,wp_generate_attachment_metadata($id,$file['file']));$media[$relative]=['id'=>$id,'url'=>$file['url']];update_option('bpe_media',$media,false);
 }
 foreach($catalog['pages'] as $key=>$page){
  $id=$key==='home'?(int)get_option('page_on_front'):0;
  if(!$id&&function_exists('bp_source_post')){$post=bp_source_post($key);$id=$post?$post->ID:0;}
  if(!$id){$post=get_page_by_path($page['file']);$id=$post?$post->ID:0;}
  $created=false;if(!$id){$id=wp_insert_post(['post_type'=>'page','post_status'=>'publish','post_title'=>preg_replace('/[–—].*/u','',$page['title']),'post_name'=>$page['file']]);$created=true;}
  if(!$id||is_wp_error($id))wp_die('Não foi possível preparar a página.');
  if(!isset($backup['posts'][$id])){$backup['posts'][$id]=['created'=>$created,'meta'=>get_post_meta($id),'content'=>get_post_field('post_content',$id)];update_option('bpe_backup',$backup,false);}
  $routes[$page['file'].'.html']=$id;
 }
 update_option('bpe_routes',$routes,false);update_option('show_on_front','page');update_option('page_on_front',$routes['index.html']);
 foreach($catalog['pages'] as $page){$id=$routes[$page['file'].'.html'];if(!bpe_enabled($id))bpe_save_document($id,$page['blocks'],$media);}
 foreach(['header'=>'Cabeçalho BluePC','footer'=>'Rodapé BluePC'] as $kind=>$title){$id=(int)get_option('bpe_'.$kind);if(!$id||!get_post($id)){$id=wp_insert_post(['post_type'=>'elementor_library','post_status'=>'publish','post_title'=>$title]);update_option('bpe_'.$kind,$id);update_post_meta($id,'_bpe_global',1);bpe_save_document($id,['global-'.$kind],$media);}}
 \Elementor\Plugin::instance()->files_manager->clear_cache();
}
function bpe_restore(){
 $backup=get_option('bpe_backup',[]);foreach($backup['posts']??[] as $id=>$state){
  foreach(['_elementor_data','_elementor_edit_mode','_elementor_template_type','_elementor_version','_elementor_page_settings','_bpe_enabled'] as $key){delete_post_meta($id,$key);foreach($state['meta'][$key]??[] as $v)add_post_meta($id,$key,wp_slash(maybe_unserialize($v)));}
  wp_update_post(['ID'=>$id,'post_content'=>$state['content']]);if($state['created'])wp_update_post(['ID'=>$id,'post_status'=>'draft']);
 }
 foreach($backup['front']??[] as $k=>$v)update_option($k,$v);delete_option('bpe_routes');delete_option('bpe_backup');if(class_exists('Elementor\\Plugin'))\Elementor\Plugin::instance()->files_manager->clear_cache();
}
