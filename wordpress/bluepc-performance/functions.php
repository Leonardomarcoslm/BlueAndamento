<?php
 defined('ABSPATH') || exit;
 require get_template_directory().'/inc/content.php';
 require get_template_directory().'/inc/setup.php';
 add_action('after_setup_theme',function(){add_theme_support('title-tag');add_theme_support('post-thumbnails');add_theme_support('custom-logo');add_theme_support('responsive-embeds');add_theme_support('html5',['search-form','gallery','caption','style','script']);add_theme_support('woocommerce');register_nav_menus(['primary'=>'Navegação principal']);});
 add_action('wp_enqueue_scripts',function(){wp_enqueue_style('bluepc',get_template_directory_uri().'/assets/site.css',[],'1.3.0');wp_enqueue_script('bluepc',get_template_directory_uri().'/assets/site.js',[],'1.3.0',['strategy'=>'defer','in_footer'=>true]);});
 function bp_asset($name){return get_template_directory_uri().'/assets/'.$name.'.webp';}
 function bp_source_slugs(){return ['gamer'=>'gamers','corporativo'=>'office','workstation'=>'computadores-office','contato'=>'contatoooo','fire'=>'fire','ludic'=>'ludic','aura'=>'aura','pulse'=>'pulse','frag'=>'frag'];}
 function bp_source_post($key){$slugs=bp_source_slugs();if(!isset($slugs[$key]))return null;$post=get_page_by_path($slugs[$key],OBJECT,'post');return $post&&$post->post_status==='publish'?$post:null;}
 function bp_current_key(){if(is_front_page())return 'home';$key=get_post_meta(get_queried_object_id(),'_bluepc_page',true);if($key)return $key;$slug=get_post_field('post_name',get_queried_object_id());return array_search($slug,bp_source_slugs(),true)?:'';}
 function bp_url($key){$pages=bp_pages();if($key==='home')return home_url('/');$p=bp_source_post($key);if(!$p)$p=get_page_by_path($pages[$key]['slug']??$key);return $p?get_permalink($p):home_url('/'.($pages[$key]['slug']??$key).'/');}

 function bp_setting($key,$default=''){return get_theme_mod('bp_'.$key,$default);}
 function bp_whatsapp($text='Olá! Gostaria de conhecer os computadores BluePC.'){$phone=preg_replace('/\D/','',bp_setting('whatsapp','5531984662481'));return 'https://wa.me/'.$phone.'?text='.rawurlencode($text);}
 function bp_image($name,$alt,$class='',$eager=false){$path=get_template_directory().'/assets/'.$name.'.webp';$size=is_file($path)?getimagesize($path):[800,800];echo '<img class="'.esc_attr($class).'" src="'.esc_url(bp_asset($name)).'" alt="'.esc_attr($alt).'" width="'.esc_attr($size[0]).'" height="'.esc_attr($size[1]).'" '.($eager?'fetchpriority="high"':'loading="lazy"').' decoding="async">';}
 function bp_link($key,$text,$class=''){echo '<a class="'.esc_attr($class).'" href="'.esc_url(bp_url($key)).'">'.esc_html($text).'</a>';}
 add_action('customize_register',function($c){$c->add_section('bp_contact',['title'=>'BluePC: contatos e lojas','priority'=>30]);$settings=['whatsapp'=>['WhatsApp (país + DDD + número)','5531984662481','text'],'email'=>['E-mail','sac@bluepc.com.br','email'],'phone'=>['Telefone (opcional; confirmar número)','','text'],'mercado'=>['Mercado Livre','https://www.mercadolivre.com.br/loja/bluepc','url'],'kabum'=>['KaBuM!','https://www.kabum.com.br/busca/pc-gamer-bluepc','url'],'amazon'=>['Amazon','https://www.amazon.com.br/s?k=bluepc+ludic','url'],'shopee'=>['Shopee','https://shopee.com.br/oficial/search?keyword=computadores&shop=950046979','url'],'magalu'=>['Magalu','https://www.magazineluiza.com.br/busca/computadores/?filters=brand---2eletro','url']];foreach($settings as $id=>$s){$c->add_setting('bp_'.$id,['default'=>$s[1],'sanitize_callback'=>$s[2]==='url'?'esc_url_raw':($s[2]==='email'?'sanitize_email':'sanitize_text_field')]);$c->add_control('bp_'.$id,['label'=>$s[0],'section'=>'bp_contact','type'=>$s[2]]);}});
 add_action('wp_head',function(){if(!has_site_icon())echo '<link rel="icon" href="'.esc_url(bp_asset('favicon')).'">';});

add_action('wp_head',function(){if(defined('WPSEO_VERSION')||defined('RANK_MATH_VERSION'))return;$description=is_front_page()?'Conheça os computadores BluePC Gamer, Corporativos e Workstations. Encontre sua configuração e fale com nossa equipe.':(is_page()?get_the_title().' BluePC. Conheça nossas soluções e fale com a equipe para escolher seu computador.':'');if($description)echo '<meta name="description" content="'.esc_attr($description).'">';});

// Preserve known incoming links from the recovered Elementor homepage.
add_action('template_redirect',function(){if(!is_404())return;$path=trim((string)parse_url(wp_unslash($_SERVER['REQUEST_URI']??'/'),PHP_URL_PATH),'/');$base=trim((string)parse_url(home_url('/'),PHP_URL_PATH),'/');if($base&&str_starts_with($path,$base.'/'))$path=substr($path,strlen($base)+1);$aliases=['402-2'=>'gamer','office'=>'corporativo','computadores-office'=>'workstation','contatoooo'=>'contato'];if(isset($aliases[$path])){wp_safe_redirect(bp_url($aliases[$path]),301);exit;}});
// Explicitly own the theme header/footer instead of Elementor's legacy replacement.
add_action('elementor/theme/register_locations',function($manager){$manager->register_core_location('header');$manager->register_core_location('footer');});
function bp_is_native_page(){return !is_admin()&&!isset($_GET['elementor-preview'])&&(is_front_page()||is_page_template('page-bluepc.php')||bp_current_key()!=='');}
add_action('wp',function(){
 if(!bp_is_native_page())return;
 $class='ElementorPro\Modules\FloatingButtons\Module';
 if(class_exists($class))remove_action('wp_footer',[$class::instance(),'print_floating_buttons']);
});
function bp_trim_legacy_assets(){
 if(!bp_is_native_page())return;
 foreach(['style','script'] as $kind){$registry=$kind==='style'?wp_styles():wp_scripts();foreach($registry->queue as $handle){$src=(string)($registry->registered[$handle]->src??'');if(str_starts_with($handle,'elementor')||str_starts_with($handle,'e-animation')||str_contains($src,'/plugins/elementor/')||str_contains($src,'/plugins/pro-elements/')||str_contains($src,'/uploads/elementor/')||str_contains($src,'fonts.googleapis.com')){if($kind==='style')wp_dequeue_style($handle);else wp_dequeue_script($handle);}}}
}
add_action('wp_enqueue_scripts','bp_trim_legacy_assets',PHP_INT_MAX);
add_action('wp_print_footer_scripts','bp_trim_legacy_assets',0);
add_filter('elementor/frontend/print_google_fonts',function($print){return bp_is_native_page()?false:$print;});
add_filter('document_title_parts',function($parts){if(is_front_page())$parts['title']='BluePC — Performance e produtividade';elseif(empty($parts['site']))$parts['site']='BluePC';return $parts;});

// Keep the recovered post URLs and data; only replace their frontend template.
add_filter('template_include',function($template){$key=bp_current_key();return $key&&$key!=='home'?get_template_directory().'/page-bluepc.php':$template;},1000);
add_action('template_redirect',function(){if(!is_page()||is_front_page())return;$key=get_post_meta(get_queried_object_id(),'_bluepc_page',true);$source=$key?bp_source_post($key):null;if($source){wp_safe_redirect(get_permalink($source),301);exit;}},1);
