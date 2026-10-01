<?php
defined('ABSPATH') || exit;
class BPE_Widget extends \Elementor\Widget_Base {
 private $blueprint_key;private $blueprint;
 public function __construct($data=[],$args=null){$key=$args['blueprint_key']??substr($data['widgetType']??'',4);$this->blueprint_key=$key;$this->blueprint=bpe_catalog()['blocks'][$key]??['label'=>'BluePC','html'=>'','fields'=>[]];parent::__construct($data,empty($data)?null:$args);}
 public function get_name(){return 'bpe-'.$this->blueprint_key;}
 public function get_title(){return $this->blueprint['label'];}
 public function get_icon(){return 'eicon-edit';}
 public function get_categories(){return ['bluepc'];}
 protected function register_controls(){
  foreach(['text'=>'Textos','image'=>'Imagens e logos','url'=>'Links','attribute'=>'Acessibilidade e rótulos'] as $group=>$title){
   $fields=array_filter($this->blueprint['fields'],fn($f)=>$f['type']===$group||($group==='image'&&$f['type']==='logo'));if(!$fields)continue;
   $this->start_controls_section('section_'.$group,['label'=>$title]);
   foreach($fields as $key=>$field){$type=match($field['type']){'image','logo'=>\Elementor\Controls_Manager::MEDIA,'url'=>\Elementor\Controls_Manager::URL,default=>\Elementor\Controls_Manager::TEXTAREA};$this->add_control($key,['label'=>$field['label'],'type'=>$type,'default'=>bpe_default($field),'label_block'=>true,'rows'=>3]);}
   $this->end_controls_section();
  }
 }
 protected function render(){
  $settings=$this->get_settings_for_display();$replace=[];
  foreach($this->blueprint['fields'] as $key=>$field){$v=$settings[$key]??bpe_default($field);
   $replace['{{'.$key.'}}']=match($field['type']){
    'image','url'=>esc_url(is_array($v)?($v['url']??''):''),
    'attribute'=>esc_attr($v),
    'logo'=>!empty($v['url'])?'<img class="market-logo" src="'.esc_url($v['url']).'" alt="'.esc_attr($field['label']).'">':str_replace('assets/marketplace-logos.png',esc_url(plugins_url('assets/marketplace-logos.png',__FILE__)),$field['value']),
    default=>nl2br(esc_html($v))
   };
  }
  // Markup is a bundled, trusted blueprint; all editor-provided values are escaped above.
  $html=preg_replace('/%7B%7B(f[0-9]+)%7D%7D/i','{{$1}}',$this->blueprint['html']);
  echo strtr($html,$replace);
 }
}
