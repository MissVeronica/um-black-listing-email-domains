<?php
/**
 * Plugin Name:     Ultimate Member - Black listing email domains
 * Description:     Extension to Ultimate Member for additional blocking possibilities like subdomains and top level domains and online updates of disposable email domains.
 * Version:         3.1.0
 * Requires PHP:    7.4
 * Author:          Miss Veronica
 * License:         GPL v2 or later
 * License URI:     https://www.gnu.org/licenses/gpl-2.0.html
 * Author URI:      https://github.com/MissVeronica?tab=repositories
 * Plugin URI:      https://github.com/MissVeronica/um-black-listing-email-domains
 * Update URI:      https://github.com/MissVeronica/um-black-listing-email-domains
 * Text Domain:     black-listing-domains
 * Domain Path:     /languages
 * UM version:      2.9.0
 * Reference:       https://github.com/amieiro/disposable-email-domains
 */

if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! class_exists( 'UM' ) ) return;

class UM_Blocked_Emails_Domains {

    public $user_blocked_emails = array();
    public $option_name         = array( 'allowDomains' => 'um_plugin_bed_allow_domains',
                                         'denyDomains'  => 'um_plugin_bed_deny_domains' );
    public $caching_hours       = 1;
    public $caching_hit         = false;
    public $cache_loaded        = '';
    public $status_message      = array();

    function __construct() {

        define( 'Plugin_Textdomain_BED', 'black-listing-domains' );

        add_action( 'um_submit_form_errors_hook__blockedemails', array( $this, 'um_submit_form_customized_blockedemails' ), 12, 1 );
        add_filter( 'um_custom_error_message_handler',           array( $this, 'um_custom_error_message_handler_blockedemails' ), 10, 2 );
        add_action( 'plugins_loaded',                            array( $this, 'um_blocked_emails_plugin_loaded' ), 0 );

        if ( is_admin()) {

            define( 'Plugin_Basename_BED', plugin_basename( __FILE__ ));

            add_filter( 'um_settings_structure', array( $this, 'um_settings_structure_blockedemails' ), 10, 1 );
            add_filter( 'plugin_action_links_' . Plugin_Basename_BED, array( $this, 'plugin_settings_link' ), 10, 1 );
        }

        $this->caching_hours = UM()->options()->get( 'um_disposable_cache_hours' );

        if ( ! is_numeric( $this->caching_hours )) {
            $this->caching_hours = 1;
        }

        $this->caching_hours = absint( $this->caching_hours );
    }

    public function um_blocked_emails_plugin_loaded() {

        $locale = ( get_locale() != '' ) ? get_locale() : 'en_US';
        load_textdomain( Plugin_Textdomain_BED, WP_LANG_DIR . '/plugins/' . Plugin_Textdomain_BED . '-' . $locale . '.mo' );
        load_plugin_textdomain( Plugin_Textdomain_BED, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }

    public function plugin_settings_link( $links ) {

        $url = get_admin_url() . 'admin.php?page=um_options&tab=access&section=other';
        $links[] = '<a href="' . esc_url( $url ) . '">' . esc_html__( 'Settings' ) . '</a>';

        return $links;
    }

    public function um_submit_form_customized_blockedemails( $args ) {

        $blocked_emails = UM()->options()->get( 'blocked_emails' );

        if ( ! empty( $blocked_emails )) {
            $this->user_blocked_emails = array_map( 'trim', explode( "\n", sanitize_text_field( strtolower( $blocked_emails ))));
        }

        if ( isset( $args['user_email'] ) && is_email( $args['user_email'] )) {

            $this->validate_email( strtolower( $args['user_email'] ));
        }

        if ( isset( $args['username'] ) && is_email( $args['username'] )) {

            $this->validate_email( strtolower( $args['username'] ));
        }
    }

    public function validate_email( $email_address ) {

        $domain = array_map( 'trim', explode( '@', $email_address ));
        $emailDomain = $domain[1];

        if ( ! empty( $this->user_blocked_emails )) {

            $tld = array_map( 'trim', explode( '.', $emailDomain ));
            $domain_index = count( $tld ) - 1;
            $check_tld = '*.' . $tld[$domain_index];

            if ( in_array( $check_tld, $this->user_blocked_emails )) {

                $err_msg = ( UM()->options()->get( 'um_disposable_err_msg' ) == 1 ) ? 'blocked_domain' : 'blocked_top_level_domain';
                exit( wp_redirect( esc_url( add_query_arg( 'err', $err_msg ))));
            }

            while( count( $tld ) > 2 ) {

                array_shift( $tld );
                $subdomain = '*.' . implode( '.', $tld );

                if ( in_array( $subdomain, $this->user_blocked_emails )) {

                    $err_msg = ( UM()->options()->get( 'um_disposable_err_msg' ) == 1 ) ? 'blocked_domain' : 'blocked_subdomain';
                    exit( wp_redirect( esc_url( add_query_arg( 'err', $err_msg ))));
                }
            }
        }

        if ( in_array( $emailDomain, $this->get_contents_github( 'allowDomains' )) === false ) {

            if ( in_array( $emailDomain, $this->get_contents_github( 'denyDomains' )) !== false ) {

                $err_msg = ( UM()->options()->get( 'um_disposable_err_msg' ) == 1 ) ? 'blocked_domain' : 'blocked_disposable_domain';
                exit( wp_redirect( esc_url( add_query_arg( 'err', $err_msg ))));
            }
        }
    }

    public function custom_insert_option( $option_name, $option ) {

        global $wpdb;

        $number = $wpdb->insert( $wpdb->options, array( 'option_name'  => $option_name,
                                                        'option_value' => serialize( $option ),
                                                        'autoload'     => 'no'
                                                      ),
                                                 array( '%s', '%s', '%s' )
                                );

        $this->status_message[] = ( $number === false ) ? esc_html__( 'Insert cache error', 'black-listing-domains' ) :
                                                          esc_html__( 'Cache updated',   'black-listing-domains' );
    }

    public function custom_update_option( $option_name, $option ) {

        global $wpdb;

        $number = $wpdb->delete( $wpdb->options, array( 'option_name' => $option_name ),
                                                 array( '%s' ));

        $this->status_message[] = ( $number === false ) ? esc_html__( 'Delete cache error', 'black-listing-domains' ) : '';

        $this->custom_insert_option( $option_name, $option );
    }

    public function custom_get_cache_option( $option_name ) {

        global $wpdb;

        $option = array();

        if ( $this->caching_hours == 0 ) {

            $number = $wpdb->delete( $wpdb->options, array( 'option_name' => $option_name ),
                                                     array( '%s' ));

            return $option;
        }

        $result = $wpdb->get_results( $wpdb->prepare( "SELECT `option_value` FROM $wpdb->options WHERE `option_name` LIKE %s", $option_name ));

        if ( ! empty( $result ) && is_array( $result ) && isset( $result[0] )) {
            $option = unserialize( $result[0]->option_value );

        } else {
            $this->status_message[] = esc_html__( 'Empty cache', 'black-listing-domains' );
        }

        return $option;
    }

    public function renew_option_cache( $option ) {

        $this->caching_hit = false;

        if ( $this->caching_hours == 0 ) {
            return 'disabled';
        }

        if ( $option === false || ! isset( $option['time'] ) || ! isset( $option['cache'] )) {
            return 'cache_empty';
        }

        if (( current_time( 'timestamp' ) - $option['time'] ) > $this->caching_hours * HOUR_IN_SECONDS ) {
            return 'cache_old';
        }

        $this->caching_hit = true;

        return 'cache_hit';
    }

    public function um_additional_email_domains( $setting_option ) {

        $um_additional_email_domains = array();

        $local_email_domains = UM()->options()->get( $setting_option );
        if ( ! empty( $local_email_domains )) {
            $um_additional_email_domains = array_map( 'sanitize_text_field', array_map( 'trim', explode( "\n", $local_email_domains )));
        }

        return $um_additional_email_domains;
    }

    public function get_contents_github( $type = '' ) {

        $option = $this->custom_get_cache_option( $this->option_name[$type] );
        $status = $this->renew_option_cache( $option );

        if ( $status !== 'cache_hit' ) {

            switch ( $type ) {

                case 'allowDomains':    $json = file_get_contents( 'https://raw.githubusercontent.com/amieiro/disposable-email-domains/master/allowDomains.json' );
                                        $um_additional_email_domains = $this->um_additional_email_domains( 'um_disposable_local_valid' );
                                        break;

                case 'denyDomains':     $json = file_get_contents( 'https://raw.githubusercontent.com/amieiro/disposable-email-domains/master/denyDomains.json' );
                                        $um_additional_email_domains = $this->um_additional_email_domains( 'um_disposable_local_invalid' );
                                        break;

                default:                $json = false;
                                        $um_additional_email_domains = array();
                                        break;
            }

            if ( ! empty( $json )) {

                $option = array(
                                    'time'  => current_time( 'timestamp' ),
                                    'cache' => array_merge( $um_additional_email_domains, array_map( 'sanitize_text_field', json_decode( $json, true )))
                                );

                $this->cache_loaded = $option['time'];

                if ( $this->caching_hours == 0 ) {
                    return $option['cache'];
                }

                if ( $status === 'cache_empty' ) {
                    $this->custom_insert_option( $this->option_name[$type], $option );
                }

                if ( $status === 'cache_old' ) {
                    $this->custom_update_option( $this->option_name[$type], $option );
                }

                return $option['cache'];
            }

            if ( $status === 'cache_old' ) {
                $this->caching_hit = true;
                $this->cache_loaded = $option['time'];
                $this->status_message[] = esc_html__( 'Unable to read update from GitHub', 'black-listing-domains' );

                return $option['cache'];
            }

            $this->cache_loaded = '';
            $this->status_message[] = esc_html__( 'Unable to read from GitHub', 'black-listing-domains' );

            return $um_additional_email_domains;
        }

        $this->caching_hit = true;
        $this->cache_loaded = $option['time'];

        return $option['cache'];
    }

    public function um_custom_error_message_handler_blockedemails( $err, $request_err ) {

        switch ( $request_err ) {

            case 'blocked_top_level_domain':    $err = esc_html__( 'We do not accept registrations from this top level email domain.', 'black-listing-domains' ); break;
            case 'blocked_subdomain':           $err = esc_html__( 'We do not accept registrations from this email subdomain.',        'black-listing-domains' ); break;
            case 'blocked_disposable_domain':   $err = esc_html__( 'We do not accept registrations from this temporary email domain.', 'black-listing-domains' ); break;
            default: break;
        }

        return $err;
    }

    public function um_settings_structure_blockedemails( $settings_structure ) {

        if ( isset( $_REQUEST['page'] ) && $_REQUEST['page'] == 'um_options' ) {
            if ( isset( $_REQUEST['tab'] ) && $_REQUEST['tab'] == 'access' ) {
                if ( isset( $_REQUEST['section'] ) && $_REQUEST['section'] == 'other' ) {

                    if ( ! isset( $_REQUEST['submit'] ) && ! isset( $settings_structure['access']['sections']['other']['form_sections']['disposable']['fields'] )) {

                        $source_url = 'https://github.com/amieiro/disposable-email-domains';
                        $download_source = '<a href="' . esc_url( $source_url ) . '">' . esc_html__( 'Download source "Disposable Email Domains"', 'black-listing-domains' ) . '</a>';

                        $plugin_data = get_plugin_data( __FILE__ );
                        $prefix = '&nbsp; * &nbsp;';
                        $settings = array();

                        $settings[] = array(
                                                'id'             => 'um_disposable_cache_hours',
                                                'type'           => 'number',
                                                'size'           => 'small',
                                                'default'        => $this->caching_hours,
                                                'label'          => $prefix . esc_html__( 'Number of hours to keep cached email domains', 'black-listing-domains' ),
                                                'description'    => esc_html__( 'Enter the number of hours until refreshing current downloaded GitHub allowed and disposable email domains. Hours set to 0 disables caching.', 'black-listing-domains' ) . '<br />' .
                                                                    $this->create_caching_load_stats(),
                                            );

                        $settings[] = array(
                                                'id'             => 'um_disposable_cache_listing',
                                                'type'           => 'checkbox',
                                                'label'          => $prefix . esc_html__( 'Enable listing of Plugin current caches', 'black-listing-domains' ),
                                                'checkbox_label' => esc_html__( 'Tick to create HTML pages with listings of the Plugin\'s current caches with allowed and disposable email domains.', 'black-listing-domains' ),
                                                'description'    => $this->disposable_cache_listing(),
                                            );

                        $settings[] = array(
                                                'id'             => 'um_disposable_local_valid',
                                                'type'           => 'textarea',
                                                'size'           => 'small',
                                                'label'          => $prefix . esc_html__( 'WHITE list: Additional allowed local email domains', 'black-listing-domains' ),
                                                'description'    => esc_html__( "Enter allowed local email domains (one per line) to be used together with the GitHub allowed emails during email validation. The update will be made at the next cache refresh. Example 'mydomain.com'", 'black-listing-domains' ),
                                            );

                        $settings[] = array(
                                                'id'             => 'um_disposable_local_invalid',
                                                'type'           => 'textarea',
                                                'size'           => 'small',
                                                'label'          => $prefix . esc_html__( 'BLACK list: Additional blocked local email domains', 'black-listing-domains' ),
                                                'description'    => esc_html__( "Enter blocked local email domains (one per line) to be used together with the GitHub disposable emails during email validation. The update will be made at the next cache refresh. Example 'notdomain.com'", 'black-listing-domains' ),
                                            );

                        $settings[] = array(
                                                'id'             => 'um_disposable_err_msg',
                                                'type'           => 'checkbox',
                                                'label'          => $prefix . esc_html__( 'Enable UM error message for all blocked domains', 'black-listing-domains' ),
                                                'checkbox_label' => esc_html__( 'Tick to use the UM "blocked_domain" error message also for all Plugin invalid email domains.', 'black-listing-domains' ),
                                                'description'    => 'UM: ' . esc_html__( 'We do not accept registrations from that domain.', 'ultimate-member' ),
                                            );

                        $settings_structure['access']['sections']['other']['form_sections']['disposable']['title']       = esc_html__( 'Black listing email domains', 'black-listing-domains' );
                        $settings_structure['access']['sections']['other']['form_sections']['disposable']['description'] = sprintf( esc_html__( 'Plugin version %s - tested with UM %s', 'black-listing-domains' ), $plugin_data['Version'], '2.9.0' ) . '<br />' . $download_source;
                        $settings_structure['access']['sections']['other']['form_sections']['disposable']['fields']      = $settings;

                        $settings_structure['access']['sections']['other']['form_sections']['blocked']['fields'][0]['description'] .= '<br />' . esc_html__( "Additional settings by plugin: Top level blocking '*.xyz' and subdomain blocking '*.company.com'", 'black-listing-domains' );
                    }
                }
            }
        }

        return $settings_structure;
    }

    public function create_caching_load_stats() {

        $format = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );

        $description = array();
        $description[] = esc_html__( 'Number of email domains', 'black-listing-domains' );
        $description[] = $this->cache_load_details( 'Allowed', 'allowDomains', $format );
        $this->status_message = array();
        $description[] = $this->cache_load_details( 'Disposable', 'denyDomains', $format );

        return implode( '<br />', $description );
    }

    public function cache_load_details( $text, $type, $format ) {

        $time_start     = microtime( true );
        $allowDomains   = $this->get_contents_github( $type );
        $execution_time = microtime( true ) - $time_start;
        $reply_time     = number_format((float) $execution_time, 5 );

        $cache_update   = implode( ', ', $this->status_message );
        $cache_status   = ( $this->caching_hit ) ? sprintf( esc_html__( '%s seconds to read from local cache dated %s. %s', 'black-listing-domains' ),
                                                                         $reply_time, date_i18n( $format, $this->cache_loaded ), $cache_update ) :

                                                   sprintf( esc_html__( '%s seconds to download from GitHub. %s', 'black-listing-domains' ),
                                                                         $reply_time, $cache_update );

        return sprintf( esc_html__( '%s: %d domains and %s', 'black-listing-domains' ),
                                    $text, count( $allowDomains ), $cache_status );
    }

    public function disposable_cache_listing() {

        $description = '';

        if ( UM()->options()->get( 'um_disposable_cache_listing' ) == 1 ) {

            $description = $this->create_main_html_page();

            if ( $description === false ) {
                return esc_html__( 'Email domain list HTML files could not be created.', 'black-listing-domains' );
            }

            $this->create_cache_html_page( 'allowDomains' );
            $this->create_cache_html_page( 'denyDomains' );

        } else {

            $path = UM()->uploader()->get_upload_base_dir() . 'disposable_emails' . DIRECTORY_SEPARATOR;

            if ( file_exists( $path . 'main.html' )) {
                wp_delete_file( $path . 'main.html' );
            }

            if ( file_exists( $path . 'allowDomains.html' )) {
                wp_delete_file( $path . 'allowDomains.html' );
            }

            if ( file_exists( $path . 'denyDomains.html' )) {
                wp_delete_file( $path . 'denyDomains.html' );
            }
        }

        return $description;
    }

    public function create_main_html_page() {

        $html = '<!DOCTYPE html><html lang="en-US">
<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><title>' . esc_html__( 'Black listing email domains', 'black-listing-domains' ) . '</title></head><body>';

        $url = get_bloginfo( 'url' ) . '/wp-content/uploads/ultimatemember/disposable_emails/';

        $html .= '<div style="margin-left: 80px;">
                    <h3>Black listing email domains - current cache</h3>
                    <div><a href="' . esc_url( $url . 'allowDomains.html' ) . '">White list</a> - allowDomains</div>
                    <div><a href="' . esc_url( $url . 'denyDomains.html' ) . '">Black list</a> - denyDomains</div>
                  </div>
                </body>';

        $html_file = UM()->uploader()->get_upload_base_dir() . 'disposable_emails' . DIRECTORY_SEPARATOR . 'main.html';

        wp_mkdir_p( dirname( $html_file ));
        file_put_contents( $html_file, $html );

        if ( file_exists( $html_file )) {

            $msg    = esc_html__( 'Cached email domain lists', 'black-listing-domains' );
            $untick = esc_html__( 'Untick and Save to remove the HTML files', 'black-listing-domains' );

            return '<a href="' . esc_url( $url . 'main.html' ) . '" target="_blank">' . $msg . '</a> ' . $untick;

        } else {

            return false;
        }
    }

    public function create_cache_html_page( $type ) {

        $html = '<!DOCTYPE html><html lang="en-US">
<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><title>' . $type . '</title></head><body>'. "\n";
 
        $html .= '<div style="margin-left: 80px;">
                    <h3>' . $type . '</h3>
                    <div>' . implode( "</div>\n<div>", array_map( 'esc_html', $this->get_contents_github( $type ))) . '</div>
                  </div>
                </body>';

        $html_file = UM()->uploader()->get_upload_base_dir() . 'disposable_emails' . DIRECTORY_SEPARATOR . $type . '.html';

        wp_mkdir_p( dirname( $html_file ));
        file_put_contents( $html_file, $html );
    }
}

new UM_Blocked_Emails_Domains();
