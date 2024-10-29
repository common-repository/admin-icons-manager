<?php
class AIManager {
	
	
	//protected $types_by_3rd;
	//protected $types_by_wordpress;	
	
	
	/**
	 * Types by WordPress
	 * @return array
	 */
	public static function get_types_by_wordpress() {
		
		$types_by_wordpress = array();
		
		$wppts = array(
			'post' => array(
				'slug'      => 'post',
				'_buildin'  => 1
			),
			'page' => array(
				'slug'      => 'page',
				'_buildin'  => 1
			),
			'attachment' => array(
				'slug'      => 'attachment',
				'_buildin'  => 1
			),
		);

		foreach ( $wppts  as $wppt ) {
			$args = array(
				'name' => $wppt['slug'],
			);

			$types_by_wordpress[] = get_post_types( $args, 'objects' );
				
		}
		
		return $types_by_wordpress;

	}
	
	
	
	
	/**
	 * Types by WordPress
	 * @return array
	 */
	public function get_posttypes_by_wordpress() {


		$cpts_raw = array(
			'post' => array(
				'slug'      => 'post',
				'_buildin'  => 1
			),
			'page' => array(
				'slug'      => 'page',
				'_buildin'  => 1
			),
			'attachment' => array(
				'slug'      => 'attachment',
				'_buildin'  => 1
			),
		);

		
		
		$cpts = array();
		foreach( $cpts_raw as $cpt_raw ) {
			$post_type = $cpt_raw['slug'];
			// only use active post types
			if( isset( $post_type->name ) )
				$cpts[$cpt_raw['slug']] = $post_type;
		}

		//uasort( $cpts, array( $this, 'sort_post_types_by_name' ) );
		//$this->types_by_wordpress = $cpts;

		return $cpts;
	}	
	


	/**
	 * Types by 3rd (by themes/plugins)
	 * @return array
	 */
	public static function get_posttypes_by_3rd() {
		/*if( $this->types_by_3rd !== null )
			return $this->types_by_3rd;
		*/
		$cpts_raw = get_post_types( array( 'public' => true ) );
		$cpts = array();
		foreach( $cpts_raw as $cpt_slug => $cpt_raw ) {
			$post_type = $cpt_slug;
			// only use active post types
			if( isset( $post_type->name ) )
				$cpts[$cpt_slug] = $post_type;
		}

		$cpts = array_diff_key( $cpts, self::get_posttypes_by_wordpress() );

		//uasort( $cpts, array( $this, 'sort_post_types_by_name' ) );
		//$this->types_by_3rd = $cpts;

		return $cpts;
	}


	public static function normaliza ($cadena){
		$originales = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿŔŕ';
		$modificadas = 'aaaaaaaceeeeiiiidnoooooouuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr';
		$cadena = utf8_decode($cadena);
		$cadena = strtr($cadena, utf8_decode($originales), $modificadas);
		$cadena = strtolower($cadena);
		return utf8_encode($cadena);
	}

	
	
	public static function validate_input_link ($post){


		// validate icons fonts		
		//if (preg_match('/^(dashicons[ -]|fa[ -]|linecons[ -]|entypo[ -]|lnr[ -]|typcn[ -]|unycon[ -]|pe-7s[ -]|iconmoon[ -]|fontello[ -])/', $post)) {
		if (preg_match('/^(dashicons[ -]|fas[ -]|far[ -]|fab[ -])/', $post)) {	
			
			$classsanitized = explode(" ", $post);
			
			switch ($classsanitized[0]) {
				
				case "dashicons":
					$font = "dashicons";
					$style = "Free";
					break;				
				case "fas":
					$font = "fa";
					$style = "Free";
					break;
				case "far":
					$font = "fa";
					$style = "Free";
					break;
				case "fab":
					$font = "fa";
					$style = "Brands";
					break;					
			
			}
			
			$validate_type = array(
				'found'   => true,
				'type'    => 'icon',
				'font'    => $font,
				'style'   => $style
			);	
		
		}
		else{
				
			// validate images
			if (preg_match("/\b(?:(?:https?|http):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|](\.jpg|\.jpeg|\.png|\.gif|\.bmp)$/i",$post)) {
				
				$validate_type = array(
					'found'   => true,
					'type'    => 'image'
				);

			}
			// Not icon or image
			else{ 
			
				$validate_type = array(
					'found'   => false,
					'type'    => 'unknown'
				);
			
			}
					
		}
		
		return $validate_type;
	
	}
	
	
	
	
	public static function dashicons_unicode ($class){
		

		//Read JSON dashicons
		$datadashicons = file_get_contents(AIMANAGER_PLUGIN_URL . "fonts/dashicons/codepoints.json");
		$json_dashicons = json_decode($datadashicons, true);
		
		
		$classsanitized = explode(" ", $class);
		$classsanitized2 = explode("dashicons-", $classsanitized[1]);
		$dec = $json_dashicons["{$classsanitized2[1]}"];
		return dechex($dec);
		
	}
	
	
	
	public static function fa_unicode ($class){
		

		//Read JSON awesome
		$datafaicons = file_get_contents(AIMANAGER_PLUGIN_URL . "fonts/fontawesome/icons.json");
		$json_faicons = json_decode($datafaicons, true);
		

		$classsanitized = explode(" ", $class);
		$classsanitized2 = explode("fa-", $classsanitized[1]);
		
		return $json_faicons["{$classsanitized2[1]}"]["unicode"];
		
	}	
	
	
}