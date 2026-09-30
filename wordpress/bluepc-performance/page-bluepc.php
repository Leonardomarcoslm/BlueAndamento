<?php
 /* Template Name: Página BluePC */
 get_header();$key=bp_current_key();if(!$key){foreach(bp_pages() as $k=>$p){if(get_post_field('post_name',get_the_ID())===$p['slug']){$key=$k;break;}}}
 if($key==='home')get_template_part('templates/home');elseif(in_array($key,['gamer','corporativo','workstation'],true))include get_template_directory().'/templates/category.php';elseif(in_array($key,['fire','ludic','aura','pulse','frag'],true))include get_template_directory().'/templates/line.php';elseif(in_array($key,['contato','sobre','loja'],true))include get_template_directory().'/templates/info.php';else{echo '<section class="wrap prose">';while(have_posts()){the_post();the_title('<h1>','</h1>');the_content();}echo '</section>';}get_footer();
