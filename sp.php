<?php
/*
   Plugin Name: Twitter Like Box Lightbox
   Plugin URI: http://arevico.com/kb-twitter-likebox-lightbox-promoter/
   Description:  Twitter Like Box Lightbox
   Version: 1.1
   Author: Arevico
   Author URI: http://arevico.com/kb-twitter-likebox-lightbox-promoter/
   Copyright: 2011, Arevico
*/
require_once(rtrim(dirname(__FILE__), '/\\') . DIRECTORY_SEPARATOR . 'options.php');
$arv_twt_mp=new arv_twt_mp();

class arv_twt_mp{
	private $arv_twt_options;
	private $testendured=false;
	function arv_twt_mp()
	{
		$this->__construct();
	}
	/** Constructor, add all hooks and stuff here!*/
	function __construct()
	{
	$this->arv_twt_options=new arv_lb_options();
		add_filter('the_content', array(&$this,'launch_script'));
		add_action('wp_enqueue_scripts', array(&$this,'append_scripts'));
	}

	function launch_script($content){
		$lr = "<!-- An Arevico Plugin -->";
		$x = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
		$options=$this->arv_twt_options->pl_get_options();
		
		if(	$this->testendured && (!stristr($content,$options['exc']))){
			$lr .= $this->js_localize('twt_lb_ret',$options);
			$lr .= $this->genStyle(array('styles/box.css'));
			$lr .= $this->genScript(array('styles/jtwitter.js','//platform.twitter.com/widgets.js'));
			$lr.='<a id="inline" href="#data" style="display: none;">Show</a>';
			$lr.='<div style="display:none"><div id="data" style="">';
			//===========================================================
			$lr .= '<div style=\'background-image:url("' . $x . 'styles/bg.png' .'");width:400px;height:228px;\'>';
			$lr .= "<div class=\"twitter-friends\" options=\"{username:'" .$options['username'] . "',header:'<a href=\'_tp_\'><img src=\'_ti_\' style=\'width:48px;height:48px;margin:9px;\'/></a>',info:'',users_max:0}\"></div>";
			//===========================================================
			$lr .= '<span class="twt_head">Follow us on Twitter</span>';
			$lr .= '<span><a href="https://twitter.com/'. $options['username'] .'" class="twitter-follow-button" data-show-count="true">Follow @Arevico</a></span>';
			$lr .= "<div  style=\"position:absolute;top:70px;margin-left:5px;\" class=\"twitter-friends\" options=\"{friends:0,username:'" . $options['username'] ."',info:'',users_max:" .$options['follower_count'] ."}\">";
			$lr .='</div></div>';
			//===========================================================
			$lr .= '</div></div>';
			$lr .= $this->genScript(array('scs/launch.js'));
		}

		/* Remove shorttags*/
		$content = str_ireplace($option['exc'],"",$content);
		return $content .$lr;
		}


	function append_scripts() {
		$options=$this->arv_twt_options->pl_get_options();

		if (($options['display_on_page']==1 && is_page()) || ($options['display_on_post']==1 && is_single() ) || ($options['display_on_home']==1 && is_home() ) ){
			$this->testendured=true;
			$x = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
			wp_deregister_script( 'jquery' );
			wp_register_script( 'jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.6/jquery.min.js');
			wp_enqueue_script( 'jquery' );

			if ($options['fancybox']<=0){
				wp_register_style( 'scs_style', $x . "scs/scs.css");
				wp_enqueue_style('scs_style');
				wp_register_script( 'scs', $x . "scs/scs.js");
				wp_enqueue_script('scs');
			}

		}
	}

	/**
*	Generate Script code ()
* 	@param $arr_rel_src array, with relative script source to the plugin directory, no leading slash
* */
	function genScript($arr_rel_src){
	$lret="";
	$x = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));

	foreach ($arr_rel_src as $src){
		if (substr($src,0,2)=="//") {
		$lret .= '<script src="'. $src .'" type="text/javascript"></script>';
		} else {
		$lret .= '<script src="'.$x . $src .'" type="text/javascript"></script>';
		}
	}
	return $lret;
}
/**
 *	Generate style code ()
 * 	@param $arr_rel_src array, with relative script source to the plugin directory, no leading slash
 * */

function genStyle($arr_rel_src){
	$lret="";
	$x = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));

	foreach ($arr_rel_src as $src){
		$lret .= '<link rel="stylesheet" type="text/css" href="'. $x . $src .'"></link>';
	}
	return $lret;
}

/**
 *Custom function to localize js inline
 */

function js_localize($name, $vars) {
$lret="";
$data = "var $name = {";
$arr = array();
foreach ($vars as $key => $value) {
	$arr[count($arr)] = $key . " : '" . esc_js($value) . "'";
}
$data .= implode(",",$arr);
$data .= "};";
$lret .= "<script type='text/javascript'>\n";
//	$lret .= "/* <![CDATA[ */\n";
$lret .= $data;
//	$lret .= "\n/* ]]> */\n";
$lret .= "</script>\n";
return 	print_r($lret,true);
}}
?>