<?php
$authorized = false;
$plugin = new Libsyn\Service();
$sanitize = new Libsyn\Service\Sanitize();
$api = $plugin->getApis();
$render = true;
$error = false;

if(isset($_GET)&&is_array($_GET)) parse_str(http_build_query($_GET));

if(!isset($_POST['redirect_url']))
	if(isset($_GET)) $redirectUri = admin_url().'?'.http_build_query($_GET);
		else $redirectUri = admin_url();
else { $redirectUri = $_POST['redirect_url']; }

/* Handle saved api */
if ($api instanceof Libsyn\Api && !$api->isRefreshExpired()){
	$refreshApi = $api->refreshToken(); 
	if($refreshApi) { //successfully refreshed
		$api = $api->retrieveApiById($api->getPluginApiId()); 
	} else { //in case of a api call error...
		$handleApi = true; 
		$clientId = (!isset($clientId))?$api->getClientId():$clientId; 
		$clientSecret = (!isset($clientSecret))?$api->getClientSecret():$clientSecret; 
		$api = false;
		if(isset($showSelect)) unset($showSelect);
	}
}

if(isset($_POST['msg'])) $msg = $_POST['msg'];

/* Handle Form Submit */
if (isset($_POST['submit'])||isset($_POST['libsyn_settings_submit'])) { //has showSelect on form.
	if($api instanceof Libsyn\Api) { //Brand new setup or changes?
		if(isset($_POST['submit']) && $_POST['submit']==='Save Player Settings') { //has Player Settings Update
			if(isset($_POST['clear-settings-data'])) {
				$check = $sanitize->clear_settings($_POST['clear-settings-data']);
				if($check === true) {
					$plugin->removeSettings($api);
					$msg = "Settings Cleared";
					echo $plugin->redirectUrlScript(admin_url().'?page='.LIBSYN_DIR.'/admin/settings.php&msg='.$msg); 
				} else {
					$msg = "There was a problem when trying to remove settings.";
					$error = true;
				}
			}
			//sanitize player_settings
			$playerSettings = array();
			if(!isset($_POST['player_use_thumbnail'])) $playerSettings['player_use_thumbnail'] = '';
				else $playerSettings['player_use_thumbnail'] = $_POST['player_use_thumbnail'];
			$playerSettings['player_use_theme'] = $_POST['player_use_theme'];
			$playerSettings['player_height'] = $_POST['player_height'];
			$playerSettings['player_width'] = $_POST['player_width'];
			$playerSettings['player_placement'] = $_POST['player_placement'];
			$playerSettings['player_custom_color'] = $_POST['player_custom_color'];
			
			if(!isset($_POST['player_use_download_link'])) $playerSettings['player_use_download_link'] = '';
				else $playerSettings['player_use_download_link'] = $_POST['player_use_download_link'];
			$playerSettings['player_use_download_link_text'] = $_POST['player_use_download_link_text'];
			$playerSettings_clean = $sanitize->player_settings($playerSettings);

			if(!$playerSettings_clean||empty($playerSettings_clean)) { //malformed data
				$error =  true; $msg = __('Something wrong with player input settings, please try again.', $plugin->getTextDom());
			} elseif(is_array($playerSettings_clean)) { //looks good update options
				foreach ($playerSettings_clean as $key => $val) {
					update_option('libsyn-podcasting-'.$key, $val);
				}
			}
			
		} elseif ((isset($_POST['submit']) && $_POST['submit']==='Save Changes') || ($_POST['libsyn_settings_submit']==='Save Changes')) { //has config changes or update
			if(!is_null($api->getClientId())) { //check for cleared data
				if (isset($_POST['showSelect'])) $api->setShowId($_POST['showSelect']);
				if($api->getClientSecret()!==$sanitize->clientSecret($_POST['clientSecret'])) $api->setClientSecret($sanitize->clientSecret($_POST['clientSecret']));
				if($api->getClientId()!==$sanitize->clientId($_POST['clientId'])) $api->setClientId($sanitize->clientId($_POST['clientId']));
				if(!isset($_POST['feed_redirect_url'])) $_POST['feed_redirect_url'] = '';
				if($api->getFeedRedirectUrl()!==$_POST['feed_redirect_url']) $api->setFeedRedirectUrl($_POST['feed_redirect_url']);
				$update = $plugin->updateSettings($api);
				if($update!==false) $msg = __('Settings Updated',$plugin->getTextDom());
				
				//do feed import
				$show_id = $api->getShowId();
				if($api->getFeedRedirectUrl()!==$_POST['feed_redirect_url']&&!empty($_POST['feed_redirect_url'])&&!empty($show_id)) {
					$feedImport = $plugin->feedImport($api);
					if(!$feedImport) { $msg = __('Feed Import failed, check data or try again later.', $plugin->getTextDom()); $error = true; }
					$importer = new LIbsyn\Service\Importer();
					$importer->setFeedRedirect($api);
				}
			} else { //doesn't have client id data saved (must be cleared data update)
				if(isset($_POST['clientId'])&&isset($_POST['clientSecret'])) { 
					update_option('libsyn-podcasting-client', array('id' => $sanitize->clientId($_POST['clientId']), 'secret' => $sanitize->clientSecret($_POST['clientSecret']))); 
					$clientId = $_POST['clientId']; 
				}
			}
		}
	} else { // for brand new setup just store in session through redirects.
		if(isset($_POST['clientId'])&&isset($_POST['clientSecret'])) {
			update_option('libsyn-podcasting-client', array('id' => $sanitize->clientId($_POST['clientId']), 'secret' => $sanitize->clientSecret($_POST['clientSecret']))); 
			$clientId = $_POST['clientId']; 
		}
	}
}

/* Handle API Creation/Update*/
if((!$api)||($api->isRefreshExpired())) { //does not have $api setup yet in WP
	$render = false;
	/* Handle login and auth. */
	if(!$authorized) {
		if(isset($code)) { //handle auth callback $_POST['code']
			// (THIS FIRES WHEN YOU APPROVE API)
			$url = $redirectUri."&code=".$code."&authorized=true";
			$client = get_option('libsyn-podcasting-client');
			if (isset($client['id'])) {
				$url .= "&clientId=".$sanitize->clientId($sanitize->clientId($client['id']));
			}
			if(isset($client['secret'])) {
				$url .= "&clientSecret=".$sanitize->clientSecret($client['secret']);
			}
			echo $plugin->redirectUrlScript($url);
		} elseif(isset($clientId)) { //doesn't have api yet
			$html = $plugin->oauthAuthorize($clientId, $redirectUri);
			echo '<div id="oauth-dialog">'.$html.'</div>';
		} elseif ($api instanceof Libsyn\Api) { //either update or cleared data
			if(!isset($clientId)||is_null($clientId)) $clientId = $api->getClientId();
			if(is_null($clientId)) {
				$html = $plugin->oauthAuthorize($clientId, $redirectUri);//has api (update)
				$setup_new = true;
				$api = false;
			} else {
				$html = $plugin->oauthAuthorize($clientId, $redirectUri);//has api (update)
				echo '<div id="oauth-dialog">'.$html.'</div>';
			}
		}
	} elseif(($authorized!==false)) { //has auth token
		if ($api instanceof Libsyn\Api) {
			if(!is_null($api->getClientId())) {
				if(!isset($clientId)) $clientId = $api->getClientId();
				if(!isset($clientSecret)) $clientSecret = $api->getClientSecret();
			} else {
				$client = get_option('libsyn-podcasting-client');
				if(!isset($clientId)) $clientId = $sanitize->clientId($client['id']);
				if(!isset($clientSecret)) $clientSecret = $sanitize->clientSecret($client['secret']);					
			}
		} else {
			$client = get_option('libsyn-podcasting-client');
			if(!isset($clientId)) $clientId = $sanitize->clientId($client['id']);
			if(!isset($clientSecret)) $clientSecret = $sanitize->clientSecret($client['secret']);
		}
			
		/* Auth login */
		$json = $plugin->requestBearer(
				$sanitize->clientId($clientId),
				$sanitize->clientSecret($clientSecret),
				$sanitize->text($code),
				$sanitize->url_raw(urldecode($redirectUri))
			);

		$check = $plugin->checkResponse($json);
		$response= (array) json_decode($json->body);
		if(!$check) {
			$implodeResponse = (is_array($json->response))?implode(" ", $json->response):"Failed authentication.";
			echo "<div class\"updated\"><span style=\"font-weight:bold;\">".$implodeResponse."<span>"."\nClientID:\t".$sanitize->clientId($clientId)."\nClientSecret:\t".$sanitize->clientSecret($clientSecret)."\nAuthCode:\t".$sanitize->text($code)."\nRedirectUrl:\t".$sanitize->url_raw(urldecode($redirectUri))."</div>"; 
		
		} elseif($check) {
			$response = $response + array(
				'client_id' => $sanitize->clientId($clientId),
				'client_secret' => $sanitize->clientSecret($clientSecret),
			);
			if($api instanceof Libsyn\Api && $api->isRefreshExpired() && !is_null($api->getClientId())) {
				$api = $api->update($response);
			} else {
				$libsyn_api = $plugin->createLibsynApi($response);
				$libsyn_api->refreshToken();
				$api = $libsyn_api->retrieveApiById($libsyn_api->getPluginApiId());
				$url = admin_url().'?page='.LIBSYN_DIR.'/admin/settings.php';
				echo "<script type=\"text/javascript\">
						if (typeof window.top.location.href == 'string') window.top.location.href = \"".$url."\";
							else if(typeof document.location.href == 'string') document.location.href = \"".$url."\";
								else if(typeof window.location.href == 'string') window.location.href = \"".$url."\";
									else alert('Unknown javascript error 1022.  Please report this error to support@libsyn.com and help us improve this plugin!');
					 </script>";
			}
			if(!$api) {echo "<div class\"updated\"><span style=\"font-weight:bold;\">Problem with the API connection, please check settings or try again.<span></div>"; }
		}
	}
}

/* Form Stuff */
if($api instanceof Libsyn\Api && ($api->getShowId()===null||$api->getShowId()==='')) {
	$msg = "You must select a show to publish to.";
	$requireShowSelect = true;
	$error = true;
} elseif ($api===false&&!isset($clientId)) { $render = true; }

/* Check Ajax Urls */
$check_ajax = file_get_contents($sanitize->text(get_site_url().'/?libsyn_check_url=1'));
$check_ajax = ($check_ajax === 'true')?true:false;
if(!$check_ajax) {
	$error = true;
	$msg = __('Something went wrong when trying to load your site\'s base url.
	Please make sure your \"Site Address (URL)\" in Wordpress settings is correct.', LIBSYN_DIR);
}

?>


<?php wp_enqueue_script( 'jquery-ui-dialog', array('jquery-ui')); ?>
<?php wp_enqueue_style( 'wp-jquery-ui-dialog'); ?>
<?php wp_enqueue_script('jquery_validate', plugins_url(LIBSYN_DIR.'/lib/js/jquery.validate.min.js'), array('jquery')); ?>
<?php wp_enqueue_script('libsyn_meta_validation', plugins_url(LIBSYN_DIR.'/lib/js/meta_form.js')); ?>
<?php IF($render): ?>

<?php wp_enqueue_style( 'metaBoxes', plugins_url(LIBSYN_DIR.'/lib/css/libsyn_meta_boxes.css' )); ?>
<?php wp_enqueue_style( 'metaForm', plugins_url(LIBSYN_DIR.'/lib/css/libsyn_meta_form.css' )); ?>
<?php wp_enqueue_script( 'colorPicker', plugins_url(LIBSYN_DIR.'/lib/js/jquery.colorpicker.js' )); ?>
<?php wp_enqueue_style( 'colorPickerStyle', plugins_url(LIBSYN_DIR.'/lib/css/jquery.colorpicker.css' )); ?>

	<style media="screen" type="text/css">
	.code { font-family:'Courier New', Courier, monospace; }
	.code-bold {
		font-family:'Courier New', Courier, monospace; 
		font-weight: bold;
	}
	</style>

	<div class="wrap">
	<?php if (isset($msg)) echo $plugin->createNotification($msg, $error); ?>
	  <h2><?php _e("Libsyn Plugin Settings", $plugin->getTextDom()); ?><span style="float:right"><a href="http://www.libsyn.com/"><img src="<?php _e(plugins_url( LIBSYN_DIR . '/lib/images/libsyn_dark-small.png'), $plugin->getTextDom()); ?>" title="Libsyn Podcasting" height="28px"></a></span></h2>
	  <form name="<?php echo LIBSYN_KEY . "form" ?>" id="<?php echo LIBSYN_KEY . "form" ?>" method="post" action="">
		 <div id="poststuff">
		  <div id="post-body">
			<div id="post-body-content">
			<?php if(isset($api) && ($api !== false)) { $shows = $plugin->getShows($api)->{'user-shows'};?>
			<!-- BOS Existing API -->
			  <div class="stuffbox" style="width:93.5%">
				<h3 class="hndle"><span><?php _e("Modify Api", $plugin->getTextDom()); ?></span></h3>
				<div class="inside" style="margin: 15px;">
				  <p><em><?php _e("Libsyn account application settings can be found <a href=\"https://four.libsyn.com/wordpress-plugins\" target=\"_blank\">here.</a>", $plugin->getTextDom()); ?></em></p>
				  <table class="form-table">
					<tr valign="top">
					  <th><?php _e("Client ID:", $plugin->getTextDom()); ?></th>
					  <td>
						<input id="clientId" type="text" value="<?php _e($api->getClientId(), $plugin->getTextDom()); ?>" name="clientId" maxlength="12" pattern="[a-zA-Z0-9]{12}" <?=(!is_null($api->getClientId()))?'readonly="readonly" ':'';?>required/> 
					  </td>
					</tr>
					<tr valign="top">
					  <th><?php _e("Client Secret:", $plugin->getTextDom()); ?></th>
					  <td>
						<input id="clientSecret" value="<?php _e($api->getClientSecret(), $plugin->getTextDom()); ?>" type="password" name="clientSecret" maxlength="20" pattern="[a-zA-Z0-9]{20}" <?=(!is_null($api->getClientSecret()))?'readonly="readonly" ':'';?>required/>
						<input type="hidden" name="handleApi" id="handleApi" />
					  </td>
					</tr>
					<tr valign="top">
					  <th><?php _e("Select Show:", $plugin->getTextDom()); ?></th>
					  <td>
						<select name="showSelect" autofocus required>
							<?php 
								if(isset($requireShowSelect)&&($requireShowSelect)) echo  "<option value=\"\">None</option>";
								foreach($shows as $show) {
									if($api->getShowId()==$show->{'show_id'}||count($shows)===1)
										echo  "<option value=\"".$sanitize->showId($show->{'show_id'})."\" selected>".$show->{'show_title'}."</option>";
									else
										echo  "<option value=\"".$sanitize->showId($show->{'show_id'})."\">".$show->{'show_title'}."</option>";
								}
							?>
						</select>
					  </td>
					</tr>
					<?php if(is_int($api->getShowId())) { ?>
					<tr valign="top">
						<th></th>
						<td>
							<div class="inside" style="margin: 15px;">Libsyn is connected to your Wordpress account successfully.</div>
						</td>
					</tr>					
					<?php } ?>
					<tr valign="top">
					  <th></th>
					  <td>
						<?php submit_button(__('Save Changes', $plugin->getTextDom()), 'primary', 'libsyn_settings_submit', true, array('id' => 'submit_save', 'onClick' => "document.getElementById('submit_save').value='Save Changes';")); ?>
					  </td>
					</tr>
				  </table>
				</div>
			  </div>
			  <!-- EOS Existing API -->
			<?php } else { //new?>
			<?php $setup_new = true; ?>
			<!-- BOS Add new API -->
			  <div class="stuffbox">
				<h3 class="hndle"><span><?php _e("Add New Api", $plugin->getTextDom()); ?></span></h3>
				<div class="inside" style="margin: 15px;">
				  <p><em><?php _e("Enter settings provided from your Libsyn account application setup <a href=\"http://libsyn.com/developer_api\" target=\"_blank\">here.</a>", $plugin->getTextDom()); ?></em></p>
				  <table class="form-table">
					<tr valign="top">
					  <th><?php _e("Client ID:", $plugin->getTextDom()); ?></th>
					  <td>
						<input id="clientId" type="text" value="" name="clientId" pattern="[a-zA-Z0-9]{12}" required/> 
					  </td>
					</tr>
					<tr valign="top">
					  <th><?php _e("Client Secret:", $plugin->getTextDom()); ?></th>
					  <td>
						<input id="clientSecret" type="text" value="" name="clientSecret" pattern="[a-zA-Z0-9]{20}" required/> 
					  </td>
					</tr>
					<tr valign="top">
					  <th></th>
					  <td>
						<?php submit_button(__('Authorize Plugin', $plugin->getTextDom()), 'primary', 'button', true, array('id' => 'submit_authorization', 'onClick' => "document.getElementById('submit_save').value='Authorize Plugin';")); ?>
					  </td>
					</tr>
				  </table>
				</div>
				<?php //<div id="oauth-dialog"><iframe id="oauthBox" src="" scrolling="no" style="height:498px;display:none;"></iframe></div> ?>
				<div id="oauth-dialog"><div id="oauthBox" style="height:498px;display:none;"></div></div>
			  </div>
			  <!-- EOS Add new API -->
			<?php } ?>
			<?php if(!isset($setup_new)) { ?>
			  <!-- BOS Bottom L/R Boxes -->
			  <div class="box_left_column">
				  <div class="stuffbox box_left_content"></div>
			  </div>
			  <div class="box_right_column">
				  <div class="stuffbox">
					<div class="inside box_right_content_1">
					</div>
				  </div>
				  <div class="stuffbox">
					<div class="inside box_right_content_2">
					</div>
				  </div>
				  <div class="stuffbox">
					<div class="inside box_right_content_3">
					</div>
				  </div>
			  </div>
			  <div id="accept-dialog" class="hidden" title="Confirm Integration">
				<p><span style="color:red;font-weight:600;">Warning!</span> By accepting you will modifying your Libsyn Account & Wordpress Posts for usage with the Podcast Plugin.  We suggest backing up your Wordpress database before proceeding.</p>
				<br>
			  </div>
			  <div id="clear-settings-dialog" class="hidden" title="Confirm Integration">
				<p><span style="color:red;font-weight:600;">Warning!</span> By accepting you will be removing all your libsyn-podcasting plugin settings.  Click yes to continue.</p>
				<br>
			  </div>

			  <!-- EOS Bottom L/R Boxes -->
			<?php } ?>
			</div>
		  </div>
		</div>
	  </form>
	</div>
	<?php $feed_redirect_url = (isset($api)&&$api!==false)?$api->getFeedRedirectUrl():''; ?>	
	<?php IF(isset($json)&&!empty($json)): ?>
	<script type="text/html" id="tmpl-wp-playlist-current-item">
		<# if ( data.image ) { #>
		<img src="{{ data.thumb.src }}"/>
		<# } #>
		<div class="wp-playlist-caption">
			<span class="wp-playlist-item-meta wp-playlist-item-title">&#8220;{{ data.title }}&#8221;</span>
			<# if ( data.meta.album ) { #><span class="wp-playlist-item-meta wp-playlist-item-album">{{ data.meta.album }}</span><# } #>
			<# if ( data.meta.artist ) { #><span class="wp-playlist-item-meta wp-playlist-item-artist">{{ data.meta.artist }}</span><# } #>
		</div>
	</script>
	<script type="text/html" id="tmpl-wp-playlist-item">
		<div class="wp-playlist-item">
			<a class="wp-playlist-caption" href="{{ data.src }}">
				{{ data.index ? ( data.index + '. ' ) : '' }}
				<# if ( data.caption ) { #>
					{{ data.caption }}
				<# } else { #>
					<span class="wp-playlist-item-title">&#8220;{{{ data.title }}}&#8221;</span>
					<# if ( data.artists && data.meta.artist ) { #>
					<span class="wp-playlist-item-artist"> &mdash; {{ data.meta.artist }}</span>
					<# } #>
				<# } #>
			</a>
			<# if ( data.meta.length_formatted ) { #>
			<div class="wp-playlist-item-length">{{ data.meta.length_formatted }}</div>
			<# } #>
		</div>
	</script>
	<?php ENDIF; ?>
	
	<?php //PP check goes here ?>
	
	<?php IF(!ISSET($setup_new)): ?>
	
	<?php //PP box goes here ?>
	
	<!-- BOS Handle PlayerSettings -->
	<?php 
		//handle adding settings fields for player-setings
		register_setting('general', 'libsyn-podcasting-player_use_thumbnail');
		register_setting('general', 'libsyn-podcasting-player_use_theme');
		register_setting('general', 'libsyn-podcasting-player_height');
		register_setting('general', 'libsyn-podcasting-player_width');
		register_setting('general', 'libsyn-podcasting-player_placement');
		register_setting('general', 'libsyn-podcasting-player_custom_color');
	?>
	<script type="text/javascript">
		(function($){
			$(document).ready(function(){
				$(".box_left_content").load("<?php _e(plugins_url( LIBSYN_DIR . '/admin/views/box_playersettings.php'), $plugin->getTextDom()); ?>", function() {
					
						//add stuff to ajax box
						$("#player_use_theme_standard_image").append('<img src="<?php _e(plugins_url( LIBSYN_DIR . '/lib/images/player-preview-standard.jpg'), $plugin->getTextDom()); ?>" />');
						$("#player_use_theme_mini_image").append('<img src="<?php _e(plugins_url( LIBSYN_DIR . '/lib/images/player-preview-standard-mini.jpg'), $plugin->getTextDom()); ?>" />');
						$("#player_use_theme_custom_image").append('<img src="<?php _e(plugins_url( LIBSYN_DIR . '/lib/images/custom-player-preview.jpg'), $plugin->getTextDom()); ?>" />');
						$(".post-position-shape-top").append('<img src="<?php _e(plugins_url( LIBSYN_DIR . '/lib/images/player_position.png'), $plugin->getTextDom()); ?>" style="vertical-align:top;" />');
						$(".post-position-shape-bottom").append('<img src="<?php _e(plugins_url( LIBSYN_DIR . '/lib/images/player_position.png'), $plugin->getTextDom()); ?>" style="vertical-align:top;" />');
					
					//validate button
					$('<a>').text('Validate').attr({
						class: 'button'
					}).click( function() {
						var current_feed_redirect_input = validator_url + encodeURIComponent($("#feed_redirect_input").attr('value'));
						window.open(current_feed_redirect_input);
					}).insertAfter("#feed_redirect_input");
					
					//set default value for player use thumbnail
					<?php $playerUseThumbnail = get_option('libsyn-podcasting-player_use_thumbnail'); ?>
					var playerUseThumbnail = '<?php _e($playerUseThumbnail, $plugin->getTextDom()); ?>';
					if(playerUseThumbnail == 'use_thumbnail') {
						$('#player_use_thumbnail').attr('checked', true);
					}
					
					//set default value of player theme
					<?php $playerTheme = get_option('libsyn-podcasting-player_use_theme'); ?>
					var playerTheme = '<?php _e($playerTheme, $plugin->getTextDom()); ?>';
					if(playerTheme == 'standard') {
						$('#player_use_theme_standard').attr('checked', true);	
						//check if player_use_thumbnail is checked
						if($('#player_use_thumbnail').is(':checked')) {
							if(parseInt($('#player_height').val()) < 200) $('#player_height').val(200);
						} else {
							$('#player_height').attr({"min": "45"});
							if(parseInt($('#player_height').val()) < 45) $('#player_height').val(45);
						}						
					} else if(playerTheme == 'mini') {
						$('#player_use_theme_mini').attr('checked', true);	
						//check if player_use_thumbnail is checked
						if($('#player_use_thumbnail').is(':checked')) {
							if(parseInt($('#player_height').val()) < 200) $('#player_height').val(200);
						} else {
							$('#player_height').attr({"min": "26"});
							if(parseInt($('#player_height').val()) < 26) $('#player_height').val(26);
						}
					} else if(playerTheme == 'custom') {
						$('#player_use_theme_custom').attr('checked', true);
						$('#player_custom_color_picker').fadeIn('normal');
						//check if player_use_thumbnail is checked
						if($('#player_use_thumbnail').is(':checked')) {
							if(parseInt($('#player_height').val()) < 90) $('#player_height').val(90);
							if(parseInt($('#player_width').val()) < 450) $('#player_height').val(450);
						} else {
							$('#player_height').attr({"min": "90"});
							if(parseInt($('#player_height').val()) < 90) $('#player_height').val(90);
						}
					} else { //default: getPlayerTheme is not set
						//set default value of player theme to standard if not saved
						$('#player_use_theme_standard').attr('checked', true);
						
						//check if player_use_thumbnail is checked
						if($('#player_use_thumbnail').is(':checked')) {
							if(parseInt($('#player_height').val()) < 200) $('#player_height').val(200);
						} else {
							$('#player_height').attr({"min": "45"});
							if(parseInt($('#player_height').val()) < 45) $('#player_height').val(45);
						}
					}
					
					//player theme checkbox settings
					$('#player_use_theme_standard').change(function() {
						if($('#player_use_theme_standard').is(':checked')) {
							//check if player_use_thumbnail is checked
							if($('#player_use_thumbnail').is(':checked')) {
								if(parseInt($('#player_height').val()) < 200) $('#player_height').val(200);
							} else {
								$('#player_height').attr({"min": "45"});
								if(parseInt($('#player_height').val()) < 45) $('#player_height').val(45);
							}
							$('#player_custom_color_picker').hide('fast');
						} else if($('#player_use_theme_mini').is(':checked')) {
							//check if player_use_thumbnail is checked
							if($('#player_use_thumbnail').is(':checked')) {
								if(parseInt($('#player_height').val()) < 200) $('#player_height').val(200);
							} else {
								$('#player_height').attr({"min": "26"});
								if(parseInt($('#player_height').val()) < 26) $('#player_height').val(26);
							}
							$('#player_custom_color_picker').hide('fast');
						} else if($('#player_use_theme_custom').is(':checked')) {
							$('#player_height').attr({"min": "90"});
							$('#player_width').attr({"min": "450"});
							if(parseInt($('#player_width').val()) < 450) $('#player_width').val(450);
							if(parseInt($('#player_height').val()) > 90) $('#player_height').val(90);
							$('#player_custom_color_picker').fadeIn('normal');
						}
					});
					$('#player_use_theme_mini').change(function() {
						if($('#player_use_theme_standard').is(':checked')) {
							//check if player_use_thumbnail is checked
							if($('#player_use_thumbnail').is(':checked')) {
								if(parseInt($('#player_height').val()) < 200) $('#player_height').val(200);
							} else {
								$('#player_height').attr({"min": "45"});
								if(parseInt($('#player_height').val()) < 45) $('#player_height').val(45);
							}
							$('#player_custom_color_picker').hide('fast');
						} else if($('#player_use_theme_mini').is(':checked')) {
							//check if player_use_thumbnail is checked
							if($('#player_use_thumbnail').is(':checked')) {
								if(parseInt($('#player_height').val()) < 200) $('#player_height').val(200);
							} else {
								$('#player_height').attr({"min": "26"});
								if(parseInt($('#player_height').val()) < 26) $('#player_height').val(26);
							}
							$('#player_custom_color_picker').hide('fast');
						} else if($('#player_use_theme_custom').is(':checked')) {
							$('#player_height').attr({"min": "90"});
							$('#player_width').attr({"min": "450"});
							if(parseInt($('#player_width').val()) < 450) $('#player_width').val(450);
							if(parseInt($('#player_height').val()) > 90) $('#player_height').val(90);
							$('#player_custom_color_picker').fadeIn('normal');
						}
					});
					$('#player_use_theme_custom').change(function() {
						if($('#player_use_theme_standard').is(':checked')) {
							//check if player_use_thumbnail is checked
							if($('#player_use_thumbnail').is(':checked')) {
								if(parseInt($('#player_height').val()) < 200) $('#player_height').val(200);
							} else {
								$('#player_height').attr({"min": "45"});
								if(parseInt($('#player_height').val()) < 45) $('#player_height').val(45);
							}
							$('#player_custom_color_picker').hide('fast');
						} else if($('#player_use_theme_mini').is(':checked')) {
							//check if player_use_thumbnail is checked
							if($('#player_use_thumbnail').is(':checked')) {
								if(parseInt($('#player_height').val()) < 200) $('#player_height').val(200);
							} else {
								$('#player_height').attr({"min": "26"});
								if(parseInt($('#player_height').val()) < 26) $('#player_height').val(26);
							}
							$('#player_custom_color_picker').hide('fast');
						} else if($('#player_use_theme_custom').is(':checked')) {
							$('#player_height').attr({"min": "90"});
							$('#player_width').attr({"min": "450"});
							if(parseInt($('#player_width').val()) < 450) $('#player_width').val(450);
							if(parseInt($('#player_height').val()) > 90) $('#player_height').val(90);
							$('#player_custom_color_picker').fadeIn('normal');
						}
					});
					
					//player values height & width
					<?php $playerHeight = get_option('libsyn-podcasting-player_height'); ?>
					<?php $playerWidth = get_option('libsyn-podcasting-player_width'); ?>
					var playerHeight = parseInt('<?php _e($playerHeight, $plugin->getTextDom()); ?>');
					var playerWidth = parseInt('<?php _e($playerWidth, $plugin->getTextDom()); ?>');
					
					//height
					if(isNaN(playerHeight)) {
						$('#player_height').val(360);
					} else {
						if($('#player_use_theme_standard').is(':checked')) {
							if(playerHeight >= 45) $('#player_height').val(playerHeight);
								else $('#player_height').val(45);
						} else if($('#player_use_theme_mini').is(':checked')) {
							if(playerHeight >= 26) $('#player_height').val(playerHeight);
								else $('#player_height').val(26);
						} else if($('#player_use_theme_custom').is(':checked')) {
							if(playerHeight >= 90) $('#player_height').val(playerHeight);
								else $('#player_height').val(90);
						} else {
							$('#player_height').val(360);
						}
					}
					
					//width
					if(isNaN(playerWidth)) {
						$('#player_width').val(450);
					} else {
						if($('#player_use_theme_standard').is(':checked')) {
							if(playerWidth >= 200) $('#player_width').val(playerWidth);
								else $('#player_width').val(200);
						} else if($('#player_use_theme_mini').is(':checked')) {
							if(playerWidth >= 100) $('#player_width').val(playerWidth);
								else $('#player_width').val(100);
						} else if($('#player_use_theme_custom').is(':checked')) {
							if(playerWidth >= 450) $('#player_width').val(playerWidth);
								else $('#player_width').val(450);
						} else {
							$('#player_width').val(450);
						}
					}
					
					//player use thumbnail checkbox settings
					$('#player_use_thumbnail').change(function() {
						if($(this).is(':checked')) {
							//TODO: Add playlist support here
							if($('#player_use_theme_custom').is(':checked')) {
								if($('#player_width').val() == '' || parseInt($('#player_width').val()) <= 450) { //below min width
									$('#player_width').val("450");
									$('#player_width').attr({"min": "450"});
								}
							} else {
								if($('#player_height').val() == '' || parseInt($('#player_height').val()) <= 200) { //below min height
									$('#player_height').val("200");
									$('#player_height').attr({"min": "200"});
								}
							}
						} else {
							if($('#player_use_theme_standard').is(':checked')) {
								$('#player_height').attr({"min": "45"});
							} else if($('#player_use_theme_mini').is(':checked')){
								$('#player_height').attr({"min": "26"});
							} else if($('#player_use_theme_custom').is(':checked')){
								$('#player_height').attr({"min": "90"});
								$('#player_width').attr({"min": "450"});
							}
							
						}
					});
					
					//player placement checkbox settings
					<?php $playerPlacement = get_option('libsyn-podcasting-player_placement'); ?>
					var playerPlacement = '<?php _e($playerPlacement, $plugin->getTextDom()); ?>';
					if(playerPlacement == 'top') {
						$('#player_placement_top').attr('checked', true);
					} else if(playerPlacement == 'bottom') {
						$('#player_placement_bottom').attr('checked', true);
					} else { //player placement is not set
						$('#player_placement_top').attr('checked', true);
					}
					
					<?php $playerUseDownloadLink = get_option('libsyn-podcasting-player_use_download_link'); ?>
					var playerUseDownloadLink = '<?php _e($playerUseDownloadLink, $plugin->getTextDom()); ?>';
					<?php $playerUseDownloadLinkText = get_option('libsyn-podcasting-player_use_download_link_text'); ?>
					var playerUseDownloadLinkText = '<?php _e($playerUseDownloadLinkText, $plugin->getTextDom()); ?>';
					if(playerUseDownloadLink == 'use_download_link') {
						$('#player_use_download_link').attr('checked', true);
						if(playerUseDownloadLinkText == '') {
							$('#player_use_download_link_text').val('');
						} else if(playerUseDownloadLinkText.length >= 1) {
							$('#player_use_download_link_text').val(playerUseDownloadLinkText);
						}
						$('#player_use_download_link_text_div').fadeIn('normal');
					}
					
					//player theme checkbox settings
					$('#player_use_download_link').change(function() {
						if($(this).is(':checked')) {
							$('#player_use_download_link_text_div').fadeIn('normal');
						} else {
							$('#player_use_download_link_text_div').hide('fast');
							$('#player_use_download_link_text').val('Download Episode!');
						}
					});
					
					<?php $playerCustomColor = get_option('libsyn-podcasting-player_custom_color'); ?>
					<?php if(empty($playerCustomColor)) { ?>
					var playerCustomColor = '87a93a';
					<?php } else { ?>
					var playerCustomColor = '<?php _e($playerCustomColor, $plugin->getTextDom()); ?>';
					$('#player_custom_color').attr('value', playerCustomColor);
					$('#player_custom_color').css('background-color', "#" + playerCustomColor);
					<?php } ?>
					
					//color picker settings
					$('#player_custom_color_picker_button, #player_custom_color').click(function() {
						if(typeof libsyn_player_color_picker == 'undefined') {
							// css class can be found in the color picker's library: libsyn-colorpicker.css
							var libsyn_player_color_picker = $('#player_custom_color').colorpicker({
								title: "Choose a color "
								//, parts: ["header", "map", "bar", "hex", "preview", "footer"]
								, parts: 'full'
								, modal: true
								, showOn: 'focus'
								, color: ('#' + playerCustomColor)
								, altField: $('#player_custom_color')
								, okOnEnter: true
								//, showCloseButton: false
								, select: function(event, color) {
									$('#player_custom_color').attr('value', color.formatted);
								}
							});
							libsyn_player_color_picker.colorpicker('open');
						} else {
							libsyn_player_color_picker.colorpicker('close');
						}
					});
				});
			});
		})(jQuery);
	</script>
	<!-- EOS Handle PlayerSettings -->
	<!-- BOS Handle About -->
	<script type="text/javascript">
		(function($){
			$(document).ready(function(){
				$(".box_right_content_1").load("<?php _e(plugins_url( LIBSYN_DIR . '/admin/views/box_about.php'), $plugin->getTextDom()); ?>", function() {
					$("#version").text('Version <?php _e($plugin->getPluginVersion(), $plugin->getTextDom()); ?>');
				});
			});
		})(jQuery);
	</script>
	<!-- EOS Handle About -->
	<!-- BOS Handle Support -->
	<script type="text/javascript">
		(function($){
			$(document).ready(function(){
				$(".box_right_content_2").load("<?php _e(plugins_url( LIBSYN_DIR . '/admin/views/box_support.php?libsyn_dir='. LIBSYN_DIR), $plugin->getTextDom()); ?>", function() {
					
				});
			});
		})(jQuery);
	</script>
	<!-- EOS Handle Support -->
	<!-- BOS Handle Clear-Settings -->
	<script type="text/javascript">
		(function($){
			$(document).ready(function(){
				$(".box_right_content_3").load("<?php _e(plugins_url( LIBSYN_DIR . '/admin/views/box_clear-settings.php'), $plugin->getTextDom()); ?>", function() {
					$("#clear-settings-button").click(function() {
						//handle submission & dialog
						$( "#clear-settings-dialog" ).dialog({
							autoOpen: false,
							draggable: false,
							height: 'auto',
							width: 'auto',
							modal: true,
							resizable: false,
							open: function(){
								$('.ui-widget-overlay').bind('click',function(){
									$('#clear-settings-dialog').dialog('close');
								})
							},
							buttons: [
								{
									id: "clear-settings-dialog-button-confirm",
									text: "Delete",
									click: function(){
										$('#<?php echo LIBSYN_KEY . 'form'; ?>').append('<input type="hidden" name="clear-settings-data" value="<?php echo time(); ?>" />');
										$('#clear-settings-dialog').dialog('close');
										$( "#player-settings-submit" ).trigger( "click" );									
									}
								},
								{
									id: "clear-settings-dialog-button-cancel",
									text: "Cancel",
									click: function(){
										$('#clear-settings-dialog').dialog('close');
									}
								}
							]
						});	
						$("#clear-settings-dialog").dialog( "open" );
					});
				});
			});
		})(jQuery);
	</script>
	<!-- EOS Handle Clear-Settings -->
	<?php ELSE: ?>
	<!-- BOS Handle Oauth-Dialog -->
	<script type="text/javascript">
		(function($){
			$(document).ready(function(){
				$("#submit_authorization").click(function(event) {
					
					event.preventDefault(); // cancel default behavior
					var libsyn_redirect_uri = "<?php echo $redirectUri; ?>";
					var libsyn_active_client_id = $("#clientId").val();
					var getLibsynOauthAuthUrl = function(libsyn_active_client_id, libsyn_redirect_uri){
						var oauth_url =  
							"<?php echo $plugin->getApiBaseUri(); ?>/oauth/authorize?" +
							"client_id=" + libsyn_active_client_id + "&" +
							"redirect_uri=" + decodeURIComponent(libsyn_redirect_uri) + "&" + 
							"response_type=code" + "&" + "state=xyz" + "&" + "authorized=true";
						return oauth_url;
					}
					
					//check if input fields are valid
					if($('#clientId').valid() && $('#clientSecret').valid()) {
						if($('#clientId').prop("validity").valid && $('#clientSecret').prop("validity").valid) {
							//run ajax to clear settings meta
							//run ajax to update_option
							$.ajax({
							  type: "POST",
							  url: "<?php echo $sanitize->text(get_site_url().'/?libsyn_update_oauth_settings=1&client_id=') ?>" + $("#clientId").val() + "&client_secret=" + $("#clientSecret").val(),
							  data: {clientId:$("#clientId").val(),clientSecret:$("#clientSecret").val()},
							  success: function(data, textStatus, jqXHR) {
									//Looks good run update
									//run ajax to update_option
									$.ajax({
									  type: "POST",
									  url: "<?php echo $sanitize->text(get_site_url().'/?libsyn_oauth_settings=1') ?>",
									  data: {clientId:$("#clientId").val(),clientSecret:$("#clientSecret").val()},
									  success: function(data, textStatus, jqXHR) {
											//looks good redirect
											if (typeof window.top.location.href == "string") window.top.location.href = getLibsynOauthAuthUrl($("#clientId").val(),libsyn_redirect_uri);
												else if(typeof document.location.href == "string") document.location.href = getLibsynOauthAuthUrl($("#clientId").val(),libsyn_redirect_uri);
													else if(typeof window.location.href == "string") window.location.href = getLibsynOauthAuthUrl($("#clientId").val(),libsyn_redirect_uri);
														else alert("Unknown javascript error 1022.  Please report this error to support@libsyn.com and help us improve this plugin!");
										},
										error: function (jqXHR, textStatus, errorThrown){
											//console.log(errorThrown);
										}
									});
								},
								error: function (jqXHR, textStatus, errorThrown){
									//console.log(errorThrown);
								}
							});							
							
							//handle submission & dialog
							// $( "#oauth-dialog" ).dialog({
								// autoOpen: false,
								// draggable: true,
								// position: ['center', 'middle'],
								// height: $(window).width() * 90 / 100,
								// width: $(window).height() * 90 / 100,
								// modal: true,
								// resizable: true,
								// open: function(){
									// $('.ui-widget-overlay').bind('click',function(){
										// $('#oauth-dialog').dialog('close');
									// });
								// },
								// buttons: [
									// {
										// id: "oauth-dialog-button-cancel",
										// text: "Cancel",
										// click: function(){
											// $('#oauth-dialog').dialog('close');
										// }
									// }
								// ]
							// });
							//var member_guid = $(this).closest("td").data("member_guid");
							// $('#oauth-dialog').load(getLibsynOauthAuthUrl($("#clientId").val(),libsyn_redirect_uri));
							// $("#oauth-dialog").dialog( "open" );
							//$('#oauthBox').fadeIn('normal');
							//redirect to auth
							
						} else {
							if(!$('#clientId').prop("validity").valid){
								$('#clientId').after('<label id="clientId-error" class="error" for="clientId">Client ID is not valid.</label>');
							}
							if(!$('#clientSecret').prop("validity").valid){
								$('#clientSecret').after('<label id="clientSecret-error" class="error" for="clientSecret">Client Secret is not valid.</label>');
							}
							
						}
					}
				});
			});
		})(jQuery);
	</script>
	<!-- EOS Handle Oauth-Dialog -->
	<?php ENDIF; ?>
<?php ENDIF; ?>
