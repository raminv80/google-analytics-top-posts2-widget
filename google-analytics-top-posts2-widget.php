<?php
/*
Plugin Name:  Google Analytics Top Content Widget 2
Description: Enhanced version of Google Analytics Top Content Widget written by Jtsternberg. Widget and shortcode to display top content according to Google Analytics. ("Google Analytics Dashboard" plugin required) and to filter the result based on country ("Visitor Country" plugin required)
Plugin URI: http://j.ustin.co/yWTtmy
Author: Ramin Vakilian
Author URI: http://about.me/jtsternberg
Donate link: http://j.ustin.co/rYL89n
Version: 1.4.3
*/

require_once dirname( __FILE__ ) . '/class-tgm-plugin-activation.php';
require_once dirname( __FILE__ ) . '/country_list.php';
					  
add_action( 'tgmpa_register', 'dsgnwrks_ga_register_required_plugins' );
/**
 * Register the required plugins for The "Google Analytics Top Content" plugin.
 *
 */
function dsgnwrks_ga_register_required_plugins() {

  $plugins = array(

    array(
      'name'    => 'Google Analytics Dashboard',
      'slug'    => 'google-analytics-dashboard',
      'required'  => true,
    ),
	
	array(
      'name'    => 'Visitor Country',
      'slug'    => 'visitor-country',
      'required'  => true,
	  'force_activation' => true,
    ),

  );

  $plugin_text_domain = 'top-google-posts';
  
  //linkage to administration of wodgets
  //get_Admin_url: Retrieve the url to the admin area for a given site. This function is similar to admin_url() but includes additional support for WordPress MS.
  $widgets_url = '<a href="' . get_admin_url( '', 'widgets.php' ) . '" title="' . __( 'Setup Widget', $plugin_text_domain ) . '">' . __( 'Setup Widget', $plugin_text_domain ) . '</a>';


  $config = array(
    'domain'          => $plugin_text_domain,
    'default_path'    => '', // Default absolute path to pre-packaged plugins
    'parent_menu_slug'  => 'plugins.php', // Default parent menu slug
    'parent_url_slug'   => 'plugins.php', // Default parent URL slug
    'menu'            => 'install-required-plugins', // Menu slug
    'has_notices'       => true, // Show admin notices or not
    'is_automatic'      => true, // Automatically activate plugins after installation or not
    'message'       => '', // Message to output right before the plugins table
    'strings'         => array(
      'page_title'                            => __( 'Install Required Plugins', $plugin_text_domain ), 
      'menu_title'                            => __( 'Install Plugins', $plugin_text_domain ),
      'installing'                            => __( 'Installing Plugin: %s', $plugin_text_domain ), // %1$s = plugin name
      'oops'                                  => __( 'Something went wrong with the plugin API.', $plugin_text_domain ),
      'notice_can_install_required'           => _n_noop( 'The "Google Analytics Top Content" plugin requires the following plugin: %1$s.', 'This plugin requires the following plugins: %1$s.' ), // %1$s = plugin name(s)
      'notice_can_install_recommended'      => _n_noop( 'This plugin recommends the following plugin: %1$s.', 'This plugin recommends the following plugins: %1$s.' ), // %1$s = plugin name(s)
      'notice_cannot_install'           => _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.' ), // %1$s = plugin name(s)
      'notice_can_activate_required'          => _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s)
      'notice_can_activate_recommended'     => _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s)
      'notice_cannot_activate'          => _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.' ), // %1$s = plugin name(s)
      'notice_ask_to_update'            => _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this plugin: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this plugin: %1$s.' ), // %1$s = plugin name(s)
      'notice_cannot_update'            => _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.' ), // %1$s = plugin name(s)
      'install_link'                  => _n_noop( 'Begin installing plugin', 'Begin installing plugins' ),
      'activate_link'                 => _n_noop( 'Activate installed plugin', 'Activate installed plugins' ),
      'return'                                => __( 'Return to Required Plugins Installer', $plugin_text_domain ),
      'plugin_activated'                      => __( 'Plugin activated successfully.', $plugin_text_domain ),
      'complete'                  => __( 'All plugins installed and activated successfully. %s', $plugin_text_domain ) // %1$s = dashboard link
    )
  );

  tgmpa( $plugins, $config );

}

/**
* Register Top Content widgets
*/
add_action( 'widgets_init', 'dsgnwrks_register_google_top_posts_widgets' );
function dsgnwrks_register_google_top_posts_widgets() {
  register_widget( 'dsgnwrks_google_top_posts_widgets' );
}

/**
 * Top Content widget
 */
class dsgnwrks_google_top_posts_widgets extends WP_Widget {

    //process the new widget
    function dsgnwrks_google_top_posts_widgets() {
        $widget_ops = array(
      'classname' => 'google_top_posts',
      'description' => 'Show top posts from Google Analytics'
      );
        $this->WP_Widget( 'dsgnwrks_google_top_posts_widgets', 'Google Analytics Top Content', $widget_ops );
    }

     //build the widget settings form
    function form($instance) {
		
		//get google analytics auth domain from option list of google analytics dashboard in DB
        $gad_auth_token = get_option( 'gad_auth_token' );
		
		//make sure google analytics dashboard is ready to be used
        if ( isset( $gad_auth_token ) && $gad_auth_token != '' && class_exists( 'GADWidgetData' ) ) {

          $defaults = array(
            'title' => 'Top Viewed Content',
            'pageviews' => 20,
            'number' => 5,
            'timeval' => '1',
            'time' => '2628000',
            'showhome' => 0,
			'country' => 'all',
            'titleremove' => '',
            'contentfilter' => '',
            'catlimit' => '',
            'catfilter' => '',
            'postfilter' => ''
          );
          $instance = wp_parse_args( (array) $instance, $defaults );
          extract( $instance, EXTR_SKIP );

          ?>
              <p><label><b>Title:</b><input class="widefat" name="<?php echo $this->get_field_name( 'title' ); ?>"  type="text" value="<?php echo esc_attr( $title ); ?>" /></label></p>

              <p><label><b>Show pages with at least __ number of page views:</b> <input class="widefat" name="<?php echo $this->get_field_name( 'pageviews' ); ?>"  type="text" value="<?php echo absint( $pageviews ); ?>" /></label></p>

              <p><label><b>Number to Show:</b> <input class="widefat" name="<?php echo $this->get_field_name( 'number' ); ?>"  type="text" value="<?php echo absint( $number ); ?>" /></label></p>

              <p><label><b>Select how far back you would like analytics to pull from:</b>

                  <div class="timestamp-wrap">
                    <?php

                    echo '<select style="margin-right: 5px;" id="'. $this->get_field_name( 'timeval' ) .'" name="'. $this->get_field_name( 'timeval' ) .'">';

                      for ( $i = 1; $i <= 30; $i = $i +1 ) {
                          echo '<option value="'. $i .'"';
                          echo selected( $i, $instance['timeval'], false );
                          echo '>' . $i;
                          echo '</option>';
                      }

                    echo '</select>';

                    echo '<select style="width: 50%;" id="'. $this->get_field_name( 'time' ) .'" name="'. $this->get_field_name( 'time' ) .'">';

                      echo '<option value="3600"'. selected( '3600', $time, false ). '>hour(s)</option>';
                      echo '<option value="86400"'. selected( '86400', $time, false ). '>day(s)</option>';
                      echo '<option value="2628000"'. selected( '2628000', $time, false ). '>month(s)</option>';
                      echo '<option value="31536000"'. selected( '31536000', $time, false ). '>year(s)</option>';

                    echo '</select>';
                    ?>
                  </div>
              </label></p>
              
              <p><label><b>Limit the result to selected country</b>

                  <div class="timestamp-wrap">
                    <?php
                    echo '<select style="width: 70%;" id="'. $this->get_field_name( 'country' ) .'" name="'. $this->get_field_name( 'country' ) .'">';
                      echo '<option value="all"'. selected( 'all', $country, false ). '>- no limit -</option>';
					  echo '<option value="user"'. selected( 'user', $country, false ). '>- Viewer\'s country -</option>';
					  $c = new country_list;
					  $countries = $c->get_countries();
					  foreach($countries as $country_code => $country_name):
                      echo '<option value="'.$country_code.'"'. selected( $country_code, $country, false ). '>'.$country_name.'</option>';
					  endforeach;					  

                    echo '</select>';
                    ?>
                  </div>
              </label></p>

              <p><label>
                <span style="width: 80%; float: left; margin-right: 10px;"><b>Remove home page from list:</b> (usually "<i>yoursite.com</i>" is the highest viewed page)<br/></span>
                <input style="margin-top: 15px;" id="<?php echo $this->get_field_id( 'showhome' ); ?>" type="checkbox" name="<?php echo $this->get_field_name( 'showhome' ); ?>" value="1" <?php checked(1, $showhome); ?>/>
              </label></p>
              

              <p style="clear: both; padding-top: 15px;"><label><b>Remove Site Title From Listings:</b><br/>Your listings will usually be returned with the page/post name as well as the site title. To remove the site title from the listings, place the exact text you would like removed (i.e. <i>- Site Title</i>) here. If there is more than one phrase to be removed, separate them by commas (i.e. <i>- Site Title, | Site Title</i>). <b>Unless your site doesn't output the site titles, then you will need to add this in order for the filter settings below to work.</b> <input class="widefat" style="margin-top:2px;" name="<?php echo $this->get_field_name( 'titleremove' ); ?>"  type="text" value="<?php echo esc_attr( $titleremove ); ?>" /></label></p>

              <p><label>
              <b>Limit Listings To:</b>
              <select name="<?php echo $this->get_field_name( 'contentfilter' ); ?>">
              <?php
              echo '<option value="allcontent" '. selected( esc_attr( $instance['contentfilter'] ), '' ) .'>Not Limited</option>';

              $content_types = get_post_types( array( 'public' => true ) );
              foreach( $content_types as $key => $value ) {
                if ( $value == 'attachment' ) continue;
                $selected_value = esc_attr( $instance['contentfilter'] ) == $key ? 'selected' : '';
                echo "<option value='$key' $selected_value>$value</option>";
              }
              ?>
              </select>

              </label>
              </p>

              <?php if ( $instance['contentfilter'] == 'allcontent' || $instance['contentfilter'] == 'post' ) { ?>

                <p><label><b>Limit Listings To Category:</b><br/>To limit to specific categories, place comma separated category ID's.<input class="widefat" style="margin-top:2px;" name="<?php echo $this->get_field_name( 'catlimit' ); ?>"  type="text" value="<?php echo esc_attr( $catlimit ); ?>" /></label></p>

                <p><label><b>Filter Out Category:</b><br/>To remove specific categories, place comma separated category ID's.<input class="widefat" style="margin-top:2px;" name="<?php echo $this->get_field_name( 'catfilter' ); ?>"  type="text" value="<?php echo esc_attr( $catfilter ); ?>" /></label></p>

              <?php } ?>

              <p><label><b>Filter Out Post/Page IDs:</b><br/>To remove specific posts/pages, place comma separated post/page ID's.<input class="widefat" style="margin-top:2px;" name="<?php echo $this->get_field_name( 'postfilter' ); ?>"  type="text" value="<?php echo esc_attr( $postfilter ); ?>" /></label></p>
          <?php

        } elseif ( isset( $gad_auth_token ) && $gad_auth_token != '' && !class_exists( 'GADWidgetData' ) ) {
            echo dsgnwrks_gtc_widget_message_one();
            echo '<style type="text/css"> #widget-'. $this->id .'-savewidget { display: none !important; } </style>';
        } elseif ( class_exists( 'GADWidgetData' ) ) {
            if ( !isset( $gad_auth_token ) || $gad_auth_token == '' ) {
                echo dsgnwrks_gtc_widget_message_two();
                echo '<style type="text/css"> #widget-'. $this->id .'-savewidget { display: none !important; } </style>';
            }
        } else {
            echo dsgnwrks_gtc_widget_message_one();
            echo '<style type="text/css"> #widget-'. $this->id .'-savewidget { display: none !important; } </style>';
        }

    }

    //save the widget settings
    function update($new_instance, $old_instance) {
        $instance = $old_instance;

        $instance['title'] = esc_attr( $new_instance['title'] );
		$instance['country'] = esc_attr( $new_instance['country'] );
        $instance['pageviews'] = absint( $new_instance['pageviews'] );
        $instance['number'] = absint( $new_instance['number'] );
        $instance['showhome'] = absint( $new_instance['showhome'] );
        $instance['time'] = esc_attr( $new_instance['time'] );
        $instance['timeval'] = absint( $new_instance['timeval'] );
        $instance['titleremove'] = sanitize_text_field( $new_instance['titleremove'] );
        $instance['contentfilter'] = esc_attr( $new_instance['contentfilter'] );
        $instance['catlimit'] = esc_attr( $new_instance['catlimit'] );
        $instance['catfilter'] = esc_attr( $new_instance['catfilter'] );
        $instance['postfilter'] = esc_attr( $new_instance['postfilter'] );
        delete_transient( 'dw-gtc-list' );

        return $instance;
    }

    //display the widget
    function widget($args, $instance) {

        extract($args);

        echo $before_widget;
        $title = apply_filters( 'widget_title', $instance['title'] );
        if ( !empty( $title ) ) { echo $before_title . $title . $after_title; };

        echo dsgnwrks_gtc_top_content_shortcode( $instance, 'widget' );

        echo $after_widget;

    }

}

function dsgnwrks_gtc_widget_message_one() {
  return '<p><strong>The "Google Analytics Top Content" widget requires the plugin, <em>"Google Analytics Dashboard"</em>, to be installed and activated.</strong></p><p><a href="'. admin_url( 'plugins.php?page=install-required-plugins' ) .'" class="thickbox" title="Install Google Analytics Dashboard">Install plugin</a> | <a href="'. admin_url( 'plugins.php' ) .'" class="thickbox" title="Activate Google Analytics Dashboard">Activate plugin</a>.</p>';
}

function dsgnwrks_gtc_widget_message_two() {
  return '<p>You must first login to Google Analytics in the "Google Analytics Dashboard" settings for this widget to work.</p><p><a href="'. admin_url( 'options-general.php?page=google-analytics-dashboard/gad-admin-options.php' ) .'">Go to plugin settings</a>.</p>';
}

add_filter( 'tgmpa_complete_link_text', 'dsgnwrks_change_link_text' );
function dsgnwrks_change_link_text( $complete_link_text ) {
  return 'Go to "Google Analytics Dashboard" plugin settings';
}

add_filter( 'tgmpa_complete_link_url', 'dsgnwrks_change_link_url' );
function dsgnwrks_change_link_url( $complete_link_url ) {
  return admin_url( 'options-general.php?page=google-analytics-dashboard/gad-admin-options.php' );
}

// Writing Prompts Calendar Shortcode
add_shortcode( 'google_top_content', 'dsgnwrks_gtc_top_content_shortcode' );
function dsgnwrks_gtc_top_content_shortcode( $atts, $context ) {

  $defaults = array(
    'title' => 'Top Viewed Content',
    'pageviews' => 20,
    'number' => 5,
    'timeval' => '1',
    'time' => '2628000',
    'showhome' => 0,
	'country' => 'all',
    'titleremove' => '',
    'contentfilter' => 'allcontent',
    'catlimit' => '',
    'catfilter' => '',
    'postfilter' => ''
  );
  $atts = shortcode_atts( $defaults, $atts );
  $atts = apply_filters( 'gtc_atts_filter', $atts );

  $gad_auth_token = get_option( 'gad_auth_token' );
  if ( isset( $gad_auth_token ) && $gad_auth_token != '' && class_exists( 'GADWidgetData' ) ) {

      $trans = '';
      $atts['update'] = true;
      if ( !empty( $atts['update'] ) && !empty( $atts['default_trans'] ) ) delete_transient( 'dw-gtc-list' );
      elseif ( !empty( $atts['default_trans'] ) ) {
        $trans = get_transient( 'dw-gtc-list' );
        $transuse = "\n<!-- using transient -->\n";
      }

      if ( empty( $trans ) ) {
        $transuse = "\n<!-- not using transient -->\n";

        $login = new GADWidgetData();
        $ga = new GALib( $login->auth_type, NULL, $login->oauth_token, $login->oauth_secret, $login->account_id);

        $time = ( $atts['timeval'] * $atts['time'] );
        $time_diff = abs( time() - $time );

        if ( strpos( $atts['time'], 'month' ) ) {
          $time = str_replace( '-month', '', $atts['time'] );
          $month = $time * 60 * 60 * 24 * 30.416666667;
          $time_diff = abs( time() - $month );
        }
		
		//google analytics query
        $pages = $ga->complex_report_query(
            date( 'Y-m-d', $time_diff ),
            date( 'Y-m-d' ),
            array( 'ga:pagePath', 'ga:pageTitle', 'ga:country' ),
            array( 'ga:pageviews' ),
            array( '-ga:pageviews' ),
            array( 'ga:pageviews>' . $atts['pageviews'] )
          );
		  
        $atts['context'] = ( $context ) ? $context : 'shortcode';
        $pages = apply_filters( 'gtc_pages_filter', $pages, $atts );

        $list = '';
        if ( $pages ) {
          $urlarray = array();
          $list .= '<ol>';
          $counter = 1;
          foreach( $pages as $page ) {
            $url = $page['value'];
            if ( $url == '/' && $atts['showhome'] != '0' ) {
              continue;
            }
            $url = apply_filters( 'gtc_page_url', $url );
            if ( in_array( $url, $urlarray ) ) continue;
            $urlarray[] = $url;

            if ( $atts['contentfilter'] != 'allcontent' || $atts['catlimit'] != '' || $atts['catfilter'] != '' || $atts['postfilter'] != '' ||  ($atts['country'] != '' && $atts['country'] != 'all')) {

              $path = pathinfo( $url );
              $post = null;
              $content_types = get_post_types( array( 'public' => true ) );
			  
			  //apply country filter
			  global $VisitorCountry;
			  if($page['children']['children']['value']!=$atts['country'] && $atts['country']!='user' && $atts['country']!='all' && trim($atts['country'])!='' ) continue;
			  //apply country filter based on user's location (requires visitor's country plugin)
			  if($atts['country']=='user' && isset($VisitorCountry) && $page['children']['children']['value']!=$VisitorCountry->GetCode()) continue; 
			  
			  //apply content type filter
			  //find content type of url
              foreach( $content_types as $type ) {

                if ( $type == 'attachment' ) continue;
                if ( !empty( $post ) ) break;
                $post = get_page_by_path( $path['filename'], OBJECT, $type );
              }
              if ( $atts['contentfilter'] != 'allcontent' ) {
                if ( empty( $post ) ) continue;
                if ( $post->post_type != $atts['contentfilter'] ) continue;
              }

              if ( $atts['contentfilter'] == 'allcontent' || $atts['contentfilter'] == 'post' ) {

                if ( $atts['catlimit'] != '' ) {
                  $limit_array = array();
                  $catlimits = esc_attr( $atts['catlimit'] );
                  $catlimits = explode( ', ', $catlimits );
                  foreach ( $catlimits as $catlimit ) {
                    // if ( is_user_logged_in() ) $list .= '<pre>'. htmlentities( print_r( $post->post_name, true ) ) .'</pre>';
                    if ( in_category( $catlimit, $post ) ) $limit_array[] = $post->ID;
                  }
                  if ( !in_array( $post->ID, $limit_array ) ) continue;

                }

                if ( $atts['catfilter'] != '' ) {
                  $filter_array = array();
                  $catfilters = esc_attr( $atts['catfilter'] );
                  $catfilters = explode( ', ', $catfilters );
                  foreach ( $catfilters as $catfilter ) {
                    if ( in_category( $catfilter, $post ) ) $filter_array[] = $post->ID;
                  }
                  if ( in_array( $post->ID, $filter_array ) ) continue;
                }
              }

              if ( $atts['postfilter'] != '' ) {
                $postfilter_array = array();
                $postfilters = esc_attr( $atts['postfilter'] );
                $postfilters = explode( ', ', $postfilters );
                foreach ( $postfilters as $postfilter ) {
                  // if ( is_user_logged_in() ) $list .= '<pre>'. htmlentities( print_r( $post->post_name, true ) ) .'</pre>';
                  if ( $postfilter == $post->ID ) $postfilter_array[] = $post->ID;
                }
                if ( in_array( $post->ID, $postfilter_array ) ) continue;
              }
            }

            $title = stripslashes( wp_filter_post_kses( apply_filters( 'gtc_page_title', $page['children']['value'] ) ) );

            if ( !empty( $atts['titleremove'] ) ) {
              $removes = explode( ',', sanitize_text_field( $atts['titleremove'] ) );
              foreach ( $removes as $remove ) {
                $title = str_ireplace( trim( $remove ), '', $title );
              }
            }

            $list .= '<li><a href="' . $url . '">' . $title . '</a></li>';
            $counter++;
            if ( $counter > $atts['number'] ) break;
          }
          $list .= '</ol>';

        }
        if ( empty( $atts['update'] ) && !empty( $atts['default_trans'] ) )set_transient( 'dw-gtc-list', $list, 86400 );
        return $transuse . $list . $transuse;
      }
      return $transuse . $trans . $transuse;

  } elseif ( isset( $gad_auth_token ) && $gad_auth_token != '' && !class_exists( 'GADWidgetData' ) ) {
      $list = dsgnwrks_gtc_widget_message_one();
  } elseif ( class_exists( 'GADWidgetData' ) ) {
      if ( !isset( $gad_auth_token ) || $gad_auth_token == '' ) {
          $list = dsgnwrks_gtc_widget_message_two();
      }
  } else {
      $list = dsgnwrks_gtc_widget_message_one();
  }

  return $list;

}
