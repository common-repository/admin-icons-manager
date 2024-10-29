<?php
/**
 * Plugin Name:     Admin Icons Manager
 * Plugin URI:      https://cregotech.com/proyect/admin-icons-manager
 * Description:     Manage icons for dashboard menu, you edit its color or change it for another (dashicons or font awesome or image).
 * Author:          Javier Crego
 * Author URI:      https://cregotech.com
 * Text Domain:     ai-manager
 * Domain Path:     /languages
 * License:			GPLv3
 * Version:         1.0.2
 *
 * Copyright (C) 2020 CregoTech
 *
 *
 * @package ai-manager
 * @author Javier Crego <javier@cregotech.com>
 */
 

define( 'AIMANAGER_PLUGIN_NAME', 'Admin Icons Manager' );
define( 'AIMANAGER_VERSION', '1.0.2' );
define( 'AIMANAGER_AUTHOR', 'Javier Crego' );
define( 'AIMANAGER_AUTHOR_URL', 'https://cregotech.com' );
define( 'AIMANAGER_SIDEBAR_ICON', 'dashicons-editor-code' );

define( 'AIMANAGER_FOOTER_COPYRIGHT', date("Y").' ©' );
define( 'AIMANAGER_SUBFOOTER_TEXT', __( 'If you like Admin Icons Manager, please give it <a target="_blank" href="https://wordpress.org/support/view/plugin-reviews/admin-icons-manager?filter=5#postform">★★★★★</a> rating on WordPress.org. Thanks!', 'ai-manager' ) );


if (! defined ( 'AIMANAGER_CORE_DIR' )) {
	define ( 'AIMANAGER_CORE_DIR', WP_PLUGIN_DIR . '/admin-icons-manager' );
}
define ( 'AIMANAGER_FILE', __FILE__ );
define ( 'AIMANAGER_PLUGIN_URL', plugin_dir_url ( AIMANAGER_FILE ) );
define ( 'AIMANAGER_DEFAULT_LOGO', AIMANAGER_PLUGIN_URL . "/images/default_logo.jpg" );
define( 'AIMANAGER_BANNER', AIMANAGER_PLUGIN_URL . '/images/banner.jpg' );


class AIManager_Plugin {
	private static $notices = array ();
	private $errors = 0;
	
	
	public static function init() {
		add_action ( 'init', array (
				__CLASS__,
				'wp_init' 
		) );
		add_action ( 'admin_notices', array (
				__CLASS__,
				'admin_notices' 
		) );
		
		
		add_action ( 'admin_footer', array (
				__CLASS__,
				'insert_styles_scripts'	
		) );


	}
	public static function wp_init() {
		load_plugin_textdomain ( 'ai-manager', null, 'admin-icons-manager/languages' );

		add_action ( 'admin_menu', array (
				__CLASS__,
				'admin_menu' 
		), 40 );

		// extensions
		require_once 'core/class-ai-manager.php';
		
		// styles & javascript
		add_action ( 'admin_enqueue_scripts', array (
				__CLASS__,
				'admin_enqueue_scripts'	
		) );
		
		
	}
	
	

	
	public static function admin_enqueue_scripts($page) {
		
		// css
		wp_register_style ( 'aim-admin-style', AIMANAGER_PLUGIN_URL . 'css/admin-style.css', array (), '1.0' );
		wp_register_style ( 'aim-min', AIMANAGER_PLUGIN_URL . 'css/aim.min.css', array (), '1.0' );	
		wp_register_style ( 'fonticonpicker-corecss', AIMANAGER_PLUGIN_URL . 'css/jquery.fonticonpicker.min.css', array (), '1.0' );
		wp_register_style ( 'fonticonpicker-inverted', AIMANAGER_PLUGIN_URL . 'css/jquery.fonticonpicker.inverted.min.css', array (), '1.0' );

		wp_register_style ( 'dashicons-font', AIMANAGER_PLUGIN_URL . 'fonts/dashicons/css/dashicons.css', array (), '1.0' );
		
		wp_dequeue_style( 'font-awesome' );
        wp_deregister_style( 'font-awesome' );
		wp_register_style ( 'font-awesome', AIMANAGER_PLUGIN_URL . 'fonts/fontawesome/css/all.min.css', array (), '5.12.1' );
		//wp_register_style( 'font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css' );

		
		wp_enqueue_style ( 'aim-admin-style' );
		wp_enqueue_style ( 'aim-min' );
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style ( 'fonticonpicker-corecss' );
		wp_enqueue_style ( 'fonticonpicker-inverted' );

		wp_enqueue_style ( 'dashicons-font' );
		wp_enqueue_style ('font-awesome');
		
		
		
			
		// javascript
		wp_register_script ( 'aim-admin-script', AIMANAGER_PLUGIN_URL . 'js/admin-scripts.js', array (
				'jquery', 
				'wp-color-picker'
		), '1.2', true );
		
		
		wp_enqueue_script ( 'fonticonpicker', AIMANAGER_PLUGIN_URL . 'js/jquery.fonticonpicker.min.js', array () );
		wp_enqueue_script ( 'bootstrap-min', AIMANAGER_PLUGIN_URL . 'js/bootstrap.min.js', array () );
		wp_enqueue_script ( 'bootstrapValidator-min', AIMANAGER_PLUGIN_URL . 'js/bootstrapValidator.min.js', array () );
		
		wp_enqueue_media();
		wp_enqueue_script ( 'aim-admin-script' );

	}
	public static function admin_notices() {
		if (! empty ( self::$notices )) {
			foreach ( self::$notices as $notice ) {
				echo $notice;
			}
		}
	}

	/**
	 * Adds the admin section.
	 */
	public static function admin_menu() {
		$admin_page = add_menu_page ( __ ( 'Admin Icons Manager' ), __ ( 'Admin Icons Manager' ), 'manage_options', 'aimanager', array (
				__CLASS__,
				'aimanager_menu_settings' 
		), 'dashicons-images-alt2' );
	}




	public static function insert_styles_scripts() {
		
		
		global $menu;
		
		$outputjquery1 = "<script>jQuery(document).ready(function($) {";
		$outputjquery2 = "<script>jQuery(document).ready(function($) {";
		$outputcss = "<style type=\"text/css\" media='screen'>";
		foreach ( $menu as $menuitem ) {
			$menuname  = $menuitem[0];
			$menunamesanitized = explode(" <", $menuname);
			if (!empty($menunamesanitized[0])){
				
				$i++;
				
				$idmenuitem = $menuitem[5];
				$iconclass = $menuitem[6];
				$cssiconcolor = get_option( "aim_iconcolor_{$idmenuitem}" );
				$cssiconlink = get_option( "aim_iconlink_{$idmenuitem}" );
				
												
				$outputcss .= " #adminmenu #{$idmenuitem} div.wp-menu-image:before {";
				
				if (!empty($cssiconcolor)){$outputcss .= "color: {$cssiconcolor} !important;";}

				
				if (!empty($cssiconlink)){
					
					// check if icon item ends with image extension
					if (preg_match('/(\.jpg|\.jpeg|\.png|\.gif|\.bmp)$/', $cssiconlink)) {
						
						$outputjquery1 .= "$( '#adminmenu #{$idmenuitem} .wp-menu-image' ).removeClass( '{$iconclass}' ).attr('style', 'background-image: url(\"{$cssiconlink}\") !important').css('background-position', 'center').css('background-repeat', 'no-repeat').css('background-size', '20px 20px');";
											
					}
					else{
						
						$outputjquery1 .= "$( '#adminmenu #{$idmenuitem} .wp-menu-image' ).removeClass( '{$iconclass}' ).addClass( '{$cssiconlink}' );";
						$validate_type = AIManager::validate_input_link( $cssiconlink );
																														
						if ( $validate_type['found'] && $validate_type['type'] == "icon" ) {
							switch ($validate_type['font']) {
								
								case "dashicons":
									
									
									$csscontent = AIManager::dashicons_unicode($cssiconlink);
									$outputcss .= "content: '\\".$csscontent."' !important; font-family: dashicons !important;";
									break;
									
								case "fa":
									
									$csscontent = AIManager::fa_unicode($cssiconlink);
									$outputcss .= "content: '\\".$csscontent."' !important; font-family: 'Font Awesome 5 {$validate_type['style']}' !important";
									break;
							}
						
						}	
							
							
							

					}
					
				}
				
				$outputcss .= "}";
				
		
				$outputjquery2 .= "$('body').on('click', '.aim_upload_image_button{$i}', function(e){e.preventDefault();var button = $(this),custom_uploader = wp.media({title: 'Insert image',library : {type : 'image'},button: {text: 'Use this image'},multiple: false}).on('select', function() {var attachment = custom_uploader.state().get('selection').first().toJSON();$(button).removeClass('button').html('<img class=\"true_pre_image\" src=\"' + attachment.url + '\" style=\"max-width:20px;display:block;\" />').next().val(attachment.id).next().show();$('#_aim_iconlink{$i}').val(attachment.url);}).open();});
						$('body').on('click', '.aim_remove_image_button{$i}', function(){
			$(this).hide().prev().val('').prev().addClass('button').html('Upload image');
			$('#_aim_iconlink{$i}').val('');
			return false;
		});";
				
			}

		}
		
		$outputcss .= "</style>";
		$outputjquery1 .= "});</script>";
		$outputjquery2 .= "});</script>";
		
		echo $outputcss . $outputjquery1 . $outputjquery2;
			
	}
	

	
	
	public static function aimanager_menu_settings() {
		
		global $menu;
		$menus_err = array();
		
		/*
		$arraymenu = array();
		foreach ( $menu as $item ) {
			
			
		}				
		*/
		

		


		//Read JSON awesome
		$datafaicons = file_get_contents(AIMANAGER_PLUGIN_URL . "fonts/fontawesome/icons.json");
		$json_faicons = json_decode($datafaicons, true);
		
		//Read JSON dashicons
		$datadashicons = file_get_contents(AIMANAGER_PLUGIN_URL . "fonts/dashicons/codepoints.json");
		$json_dashicons = json_decode($datadashicons, true);						

		?>			
		
		<!-- JavaScript -->
		<script type="text/javascript">			
			jQuery(document).ready(function($) {
																						
																					
		
		
		});

		</script>
		

		<div class="aim-options" dir="ltr">
			<form method="post" id="" action="">
				<!-- header -->
				<div class="banner">
					<h1><?php echo esc_html( __( 'ADMIN ICONS MANAGER', 'ai-manager' ) ); ?></h1>
					<p><?php echo esc_html("v." . AIMANAGER_VERSION); ?></p>
					<a href="<?php echo esc_url(AIMANAGER_AUTHOR_URL); ?>"><?php echo esc_html(AIMANAGER_AUTHOR); ?></a>
				</div>
				<div class="main-menu" id="sections-menu">

						<a href="#" id="adminmenuicons_link" class="active">
							<span class="dashicons dashicons-wordpress"></span>
							<?php echo esc_html( __( 'ADMIN MENU ICONS', 'ai-manager' ) ); ?>
						</a>

						<a href="#" id="about_link">
							<span class="dashicons dashicons-info"></span>
							<?php echo esc_html( __( 'ABOUT', 'ai-manager' ) ); ?>
						</a>
				</div>
				


				<!-- sections -->
				<div id="sections" class="sections">
					<div class="section active" id="adminmenuicons_section">
						<div class="section-fields">
							<div class="sub-menu">
							</div>
							<div class="section-notifications">
								
			
								<?php


								?>

					
							</div>	

								<?php 	
																
					
																
								
									foreach ( $menu as $menuitem ) {
									
										$menuname  = $menuitem[0];
										$idmenuitem = $menuitem[5];
										$menunamesanitized = explode(" <", $menuname);

										
										if (!empty($menunamesanitized[0])){
											
											$i++;

											if (wp_verify_nonce ( $_POST ["aim-settings"], "aim-settings" )) {
												
												if (! empty ( $_POST ["_aim_iconlink".$i] ) ) {
													
													$validate_type = AIManager::validate_input_link($_POST ["_aim_iconlink".$i]);
													
													//Good validation
													if ( $validate_type['found'] ) {
														
														$validate_msg = "";
														$validate_class = "";


														add_option ( "aim_iconlink_".$idmenuitem, sanitize_text_field($_POST ["_aim_iconlink".$i])  );
														add_option ( "aim_iconcolor_".$idmenuitem, sanitize_hex_color($_POST ["_aim_iconcolor".$i])  );
														add_option ( "aim_image_".$idmenuitem, sanitize_text_field($_POST ["_aim_image".$i])  );
														update_option ( "aim_iconlink_".$idmenuitem, sanitize_text_field($_POST ["_aim_iconlink".$i]) );
														update_option ( "aim_iconcolor_".$idmenuitem, sanitize_hex_color($_POST ["_aim_iconcolor".$i]) );
														update_option ( "aim_image_".$idmenuitem, sanitize_text_field($_POST ["_aim_image".$i])  );

														
													}
													//Validation error
													else{
														
														$validate_msg = esc_html( __( "Invalid image link or class name", 'ai-manager' ) );
														$validate_class = "alert alert-danger";
														$errors++;													
														$menus_err[] = esc_html( $menunamesanitized[0]);
														
													} 
												}
												//Empty iconlink input 
												else{
														
														$validate_msg = "";
														$validate_class = "";
														add_option ( "aim_iconlink_".$idmenuitem, sanitize_text_field($_POST ["_aim_iconlink".$i])  );
														add_option ( "aim_iconcolor_".$idmenuitem, sanitize_hex_color($_POST ["_aim_iconcolor".$i])  );
														add_option ( "aim_image_".$idmenuitem, sanitize_text_field($_POST ["_aim_image".$i])  );
														update_option ( "aim_iconlink_".$idmenuitem, sanitize_text_field($_POST ["_aim_iconlink".$i]) );
														update_option ( "aim_iconcolor_".$idmenuitem, sanitize_hex_color($_POST ["_aim_iconcolor".$i]) );
														update_option ( "aim_image_".$idmenuitem, sanitize_text_field($_POST ["_aim_image".$i])  );
														
												}
												
											}
											

								?>
											<div class="field-wrapper">
														<div class="field-title">
															<p class="title"><?php echo esc_html($menunamesanitized[0]); ?></p>
														</div>
													<div class="field full-width">
													
														
															<div class="divTable optionsTable">
																<div class="divTableBody">
																	<div class="divTableRow">
																		<div class="divTableCell"><?php echo esc_html( __( "DEFAULT ICON", 'ai-manager' ) );?></div>
																		<div class="divTableCell">
																		<?php
																		// check if icon item ends with image extension
																		if (preg_match('/(\.jpg|\.jpeg|\.png|\.gif|\.bmp)$/', $menuitem[6])) {
																		?>
																			<div class="divicon" style="width: 36px; text-align: center;"><img style="max-width: 20px; max-height: 20px;" src="<?php echo esc_url($menuitem[6]); ?>"></img></div>
																		<?php
																		// check if there is svg in string
																		} elseif (strpos($menuitem[6], "svg") !== false) {
																		?>																			
																			<div class="wp-menu-image svg divicon" style="width: 36px;
																						  height: 34px;
																						  margin: 0;
																						  text-align: center;
																						  background-position: center;
																						  background-repeat: no-repeat;
																						  background-size: 20px auto; background-image: url('<?php echo esc_url($menuitem[6]); ?>')"></div>
																		<?php
																		// font icon
																		} else {
																		?>
																			<div class="divicon" style="width: 36px; text-align: center;"><span class="wp-menu-image dashicons-before <?php echo esc_attr($menuitem[6]); ?>"></span></div>
																		<?php
																		}
																		?>
																		</div>
																	</div>
																	<div class="divTableRow">
																		<div class="divTableCell"><?php echo esc_html( __( "CLASS NAME / IMAGE LINK", 'ai-manager' ) ); ?></div>
																		<div class="divTableCell"><input type="text" id="_aim_iconlink<?php echo esc_attr($i); ?>" name="_aim_iconlink<?php echo esc_attr($i); ?>" value="<?php echo esc_attr( get_option( "aim_iconlink_{$idmenuitem}" ) ); ?>" style="width:100%" readonly /><p><span class="<?php echo esc_attr($validate_class); ?>"><?php echo esc_html($validate_msg); ?></span></p></div>
																	</div>
																	<div class="divTableRow">
																		<div class="divTableCell"><?php echo esc_html( __( "COLOR", 'ai-manager' ) );?></div>
																		<div class="divTableCell"><input type="text" id="_aim_iconcolor<?php echo esc_attr($i); ?>" name="_aim_iconcolor<?php echo esc_attr($i); ?>" class="color-field" data-default-color="#ccc" value="<?php echo esc_attr( get_option( "aim_iconcolor_{$idmenuitem}" ) ); ?>" /></div>
																	</div>
																	<div class="divTableRow">
																		<div class="divTableCell"><?php echo esc_html( __( "UPLOAD IMAGE", 'ai-manager' ) );?></div>
																		<div class="divTableCell"><div><a href="#" class="aim_upload_image_button<?php echo esc_attr($i); ?> button"><?php echo esc_html( __( "Upload Image", 'ai-manager' ) ); ?></a>
																		<input type="hidden" name="_aim_image<?php echo esc_attr($i); ?>" id="_aim_image<?php echo esc_attr($i); ?>" value="<?php echo esc_attr( get_option( "aim_image_{$idmenuitem}" ) ); ?>" />
																		<a href="#" class="aim_remove_image_button<?php echo esc_attr($i); ?>" style="display:inline-block;display: none"><?php echo esc_html( __( "Remove image", 'ai-manager' ) ); ?></a>
																		</div></div>
																	</div>
																	<div class="divTableRow">
																		<div class="divTableCell"><?php echo esc_html( __( "CHANGE ICON", 'ai-manager' ) );?></div>
																		<div class="divTableCell" id="cell<?php echo esc_attr($i); ?>">
																		
																		
																			<div class="iconssel">

																				
																				<input type="text" name="iconsselect<?php echo esc_attr($i); ?>" id="iconsselect<?php echo esc_attr($i); ?>" />
																				<p>
																					<div id="changeiconsset<?php echo esc_attr($i); ?>">
																						<button type="button" class="btn activeset change-icons-all-<?php echo esc_attr($i); ?>"><?php echo esc_html( __( "All Icons", 'ai-manager' ) ); ?></button>
																						<button type="button" class="btn change-icons-di-<?php echo esc_attr($i); ?>"><?php echo esc_html( __( "Set Dashicons", 'ai-manager' ) ); ?></button>
																						<button type="button" class="btn change-icons-fa-<?php echo esc_attr($i); ?>"><?php echo esc_html( __( "Set Font Awesome", 'ai-manager' ) ); ?></button>
																					</div>																			
																				</p>
																				


																				
																				
				
																				
																				<!-- JavaScript -->
																				<script type="text/javascript">																				
																					jQuery(document).ready(function($) {
																						
																						var aim_awesomeicons = [<?php foreach ($json_faicons as $key=>$icon) {
																							
																								
																								if ( in_array("solid", $json_faicons[$key]["styles"]) && in_array("regular", $json_faicons[$key]["styles"]) ) {
																									$class = "fas";
																								}
																								elseif ( in_array("solid", $json_faicons[$key]["styles"]) ){
																									$class = "fas";
																								}
																								elseif ( in_array("regular", $json_faicons[$key]["styles"]) ){
																									$class = "far";
																								}
																								elseif ( in_array("brands", $json_faicons[$key]["styles"]) ){
																									$class = "fab";
																								}
																								
																								echo "'{$class} fa-".esc_js($key)."', ";
																								

																							} 
																						?>];
																						
																						
																						var aim_awesomeicons_search = [<?php foreach ($json_faicons as $key=>$icon) { echo "'".esc_js($json_faicons[$key]["label"])."', "; } ?>];		
																						
																						var aim_dashicons = [<?php foreach ($json_dashicons as $key=>$icono) {
																						
																								$class = "dashicons";									
																								echo "'{$class} dashicons-".esc_js($key)."', ";
																								

																							} 
																						?>];	
																						
																						var aim_dashicons_search = [<?php foreach ($json_dashicons as $key=>$icon) { echo "'".esc_js($key)."', "; } ?>];		

																						var aim_icons = aim_dashicons.concat(aim_awesomeicons);
																						
																						var aim_icons_search = aim_dashicons_search.concat(aim_awesomeicons_search);	


																						// Get the variable to use the public APIs
																						var dynamicIconsElement = $('#iconsselect<?php echo esc_js($i); ?>').fontIconPicker({
																							source: aim_icons,
																							//searchSource: aim_icons_search,
																							//useAttribute: true,
																							//attributeName: 'data-icomoon',
																							theme: 'fip-inverted'
																						});									
																						
																						
																						$('.change-icons-all-<?php echo esc_js($i); ?>').on('click', function(e) {
																							// Prevent default action
																							e.preventDefault();

																							// Set the icon
																							dynamicIconsElement.setIcons(aim_icons, aim_icons_search);

																							// Change the button appearance
																							$('.change-icons-buttons button').removeClass('btn-primary').addClass('btn-default');
																							$(this).removeClass('btn-default').addClass('btn-primary');
																						});
																						// SET DASHICONS
																						$('.change-icons-di-<?php echo esc_js($i); ?>').on('click', function(e) {
																							

																							// Prevent default action
																							e.preventDefault();

																							// Set the icon
																							dynamicIconsElement.setIcons(aim_dashicons, aim_dashicons_search);
																							
																							// Change the button appearance
																							$('.change-icons-buttons button').removeClass('btn-primary').addClass('btn-default');
																							$(this).removeClass('btn-default').addClass('btn-primary');
																						});	
																						// SET AWESOME
																						$('.change-icons-fa-<?php echo esc_js($i); ?>').on('click', function(e) {
																							
																															
																							
																							// Prevent default action
																							e.preventDefault();

																							// Set the icon
																							dynamicIconsElement.setIcons(aim_awesomeicons, aim_awesomeicons_search);
																							
																							// Change the button appearance
																							$('.change-icons-buttons button').removeClass('btn-primary').addClass('btn-default');
																							$(this).removeClass('btn-default').addClass('btn-primary');
																						});																						
																						
																						
																						//$('#_aim_iconlink<?php echo esc_js($i); ?>').prop('readonly', true);
																						
																						
																						$('#iconsselect<?php echo esc_js($i); ?>').change(function(){
																							var selectedIcon = $(this).val();
																							$('#_aim_iconlink<?php echo esc_js($i); ?>').val(selectedIcon);
																							
																						});
																						
																						
																						
																						$('#changeiconsset<?php echo esc_js($i); ?> button').on('click', function(){
																							$('button.activeset').removeClass('activeset');
																							$(this).addClass('activeset');
																						});
																						
																						
																						<?php 
																							$iconlink = get_option( "aim_iconlink_{$idmenuitem}" );
																							if (!empty($iconlink)){
																								$validate_type = AIManager::validate_input_link( $iconlink );
																																																
																								if ( $validate_type['found'] && $validate_type['type'] == "icon" ) {
																									
																						?>
																									$("#cell<?php echo esc_js($i); ?> .selected-icon i").removeClass( 'fip-icon-block' ).addClass( '<?php echo esc_js($iconlink); ?>' );
																						<?php 
																								}
																							}
																						?>
																						
																																									
																					});
																					
																				</script>
																		</div>
																		
																		
																		</div>
																	</div>
																</div>
															</div>
																						
													</div>
											</div>
									<?php 
										}

									} 
									?>
						</div>
						<div class="section-help">
							<h2><span class="dashicons dashicons-wordpress"></span> <?php echo esc_html( __( "ADMIN MENU ICONS", 'ai-manager' ) );?></h2>
							<p><?php //echo esc_html( __( "Description", 'ai-manager' ) ); ?></p>
						</div>
					</div>
					
					<div class="section" id="about_section">
						<div class="section-fields">
							<div class="sub-menu">
							</div>
							<div class="section-notifications"></div>
							<div style="padding: 20px 35px;">
								<h1 style="margin: .2em 0 0 0;
									padding: 0;
									color: #32373c;
									line-height: 1.2em;
									font-size: 2em;
									font-weight: 400;"><?php echo esc_html( __( "Welcome to the new", 'aim-settings' ) ); ?><span style="font-weight: 600;"><?php echo esc_html( " " . AIMANAGER_PLUGIN_NAME . " " ); ?><span style="background: #9C27B0;
														padding: 1px 12px 4px 12px;
														color: #fff;
														border-radius: 50%;"><?php echo esc_html( " " . intval(AIMANAGER_VERSION) ); ?></span></span></h1>	
							</div>
							
							<div class="aim-changelog-wrap" style="padding: 20px 35px;">
							

								<div class="aim-changelog">
									<h2 style="margin: .2em 0 0 0;
										padding: 0 0 25px 0;
										color: #32373c;
										line-height: 1.2em;
										font-size: 2em;
										font-weight: 200;"><?php echo esc_html( __( "Changelog", 'aim-settings' ) ); ?>
									</h2>
									<ul class="aim-changelog-item">
										<li class="aim-changelog-li"><span class="version">1.0.2</span> <span class="date">- 2020-04-23</span></li>
										<li class="aim-changelog-li"><span class="aim--type add"><?php echo esc_html( __( "Newfeatures", 'aim-settings' ) ); ?></span>Now choice between Dashicons and Font Awesome.</li>
										<li class="aim-changelog-li"><span class="aim--type add"><?php echo esc_html( __( "Newfeatures", 'aim-settings' ) ); ?></span>Font Awesome 5 is added.</li>
										<li class="aim-changelog-li"><span class="aim--type update"><?php echo esc_html( __( "Improvements", 'aim-settings' ) ); ?></span>Changes in the code: get icons using JSON and remove unnecessary code.</li>

									</ul>									
									<ul class="aim-changelog-item">
										<li class="aim-changelog-li"><span class="version">1.0.1</span> <span class="date">- 2020-03-09</span></li>

										<li class="aim-changelog-li"><span class="aim--type add"><?php echo esc_html( __( "Newfeatures", 'aim-settings' ) ); ?></span>Changelog section is added.</li>

										<li class="aim-changelog-li"><span class="aim--type fixed"><?php echo esc_html( __( "Bugfixes", 'aim-settings' ) ); ?></span>When we installed other plugins and their icon is placed in a higher menu order, the previously saved icons were broken.</li>
									</ul>
									<ul class="aim-changelog-item">
										<li class="aim-changelog-li"><span class="version">1.0</span> <span class="date">- 2020-03-03</span></li>

										<li class="aim-changelog-li"><span class="aim--type new"><?php echo esc_html( __( "Release", 'aim-settings' ) ); ?></span>Start version of the new Admin Icons Manager 1.</li>
									</ul>
								</div>
								<div class="aim-changelog-side">
								</div>
							</div>
							
						</div>
					</div>	
									
					
				</div>
				<!-- footer -->
				<div class="footer">
					<div class="content">
						<span style="font-family: 'Shadows Into Light',cursive;
							font-size: 1rem;
							color: #383838;
							font-weight: 300;"><?php echo esc_html( __( "A project by", 'aim-settings' ) ); ?></span><img class="icon" src="<?php echo esc_url( AIMANAGER_PLUGIN_URL . 'images/CregoTech.png' ); ?>" height="50" style="display:inline-block; margin-left: 5px"><?php echo esc_html(AIMANAGER_FOOTER_COPYRIGHT); ?>
						
					</div>
					<div id="buttonsubmit" class="form-control">
					<?php wp_nonce_field ( 'aim-settings', 'aim-settings' )?>
						
						
						<input type="submit"
							value="<?php echo esc_html( __( "Save", 'aim-settings' ) );?>"
							class="button button-primary button-large" />

					</div>
				</div>
				<!-- Subfooter -->
				<?php if( '' != AIMANAGER_SUBFOOTER_TEXT ){ ?>
					<div class="ao-subfooter">
						<p><?php echo AIMANAGER_SUBFOOTER_TEXT; ?></p>
				
						
					</div>
				<?php } ?>
				

			</form>

			<?php 
				$msg = sprintf( __( '%u errors found when changing the icon in the following menus:', 'ai-manager' ), $errors );
				foreach($menus_err as $menu_err){$m_names .= '<br>'.esc_js($menu_err);}
			
				if($errors>0){
			?>
					<script type="text/javascript">
						
						jQuery(document).ready(function($) {

							$(".section-notifications").addClass("danger").html("<p><?php echo esc_js($msg); ?><br><?php echo $m_names; ?></p>");
			
						});
																						
					</script>
			<?php		
				}
			?>	
			
		</div>


<?php 

	}
	
}
AIManager_Plugin::init();


