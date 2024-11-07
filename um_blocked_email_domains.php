<?php
/**
 * Plugin Name:     Ultimate Member - Blocked Emails and Domains
 * Description:     Extension to Ultimate Member for additional blocking possibilities like subdomains and top level domains and online updates of disposable email domains.
 * Version:         3.0.0
 * Requires PHP:    7.4
 * Author:          Miss Veronica
 * License:         GPL v2 or later
 * License URI:     https://www.gnu.org/licenses/gpl-2.0.html
 * Author URI:      https://github.com/MissVeronica?tab=repositories
 * Plugin URI:      https://github.com/MissVeronica/um-black-listing-email-domains
 * Update URI:      https://github.com/MissVeronica/um-black-listing-email-domains
 * Text Domain:     ultimate-member
 * Domain Path:     /languages
 * UM version:      2.8.9
 */

if ( ! defined( 'ABSPATH' ) ) exit; 
if ( ! class_exists( 'UM' ) ) return;

class UM_Blocked_Emails_Domains {

    public $user_blocked_emails = array();

    function __construct() {

        add_action(    'um_submit_form_errors_hook__blockedemails', array( $this, 'um_submit_form_customized_blockedemails' ), 10, 1 );
        remove_action( 'um_submit_form_errors_hook__blockedemails',               'um_submit_form_errors_hook__blockedemails' );

        add_filter( 'um_custom_error_message_handler',              array( $this, 'um_custom_error_message_handler_blockedemails' ), 10, 2 );
    }

    public function um_submit_form_customized_blockedemails( $args ) {

        $blocked_emails = UM()->options()->get( 'blocked_emails' );

        if ( ! empty( $blocked_emails )) {
            $this->user_blocked_emails = array_map( 'rtrim', explode( "\n", sanitize_text_field( strtolower( $blocked_emails ))));
        }

        if ( isset( $args['user_email'] ) && is_email( $args['user_email'] )) {

            $this->validate_email( strtolower( $args['user_email'] ));
        }

        if ( isset( $args['username'] ) && is_email( $args['username'] )) {

            $this->validate_email( strtolower( $args['username'] ));
        }
    }

    public function validate_email( $args ) {

        $domain = explode( '@', $args );
        $emailDomain = $domain[1];

        if ( ! empty( $this->user_blocked_emails )) {

            if ( in_array( $args, $this->user_blocked_emails )) {
                exit( wp_redirect( esc_url( add_query_arg( 'err', 'blocked_email' ))));
            }

            $check_domain = str_replace( $domain[0], '*', $args );

            if ( in_array( $check_domain, $this->user_blocked_emails )) {
                exit( wp_redirect( esc_url( add_query_arg( 'err', 'blocked_domain' ))));
            }

            $tld = explode( '.', $emailDomain );
            $domain_index = count( $tld ) - 1;
            $check_tld = '*.' . $tld[$domain_index]; 

            if ( in_array( $check_tld, $this->user_blocked_emails )) {
                exit( wp_redirect( esc_url( add_query_arg( 'err', 'blocked_top_level_domain' ) ) ) );
            }

            while( count( $tld ) > 2 ) {

                array_shift( $tld );
                $subdomain = '*.' . implode( '.', $tld );

                if ( in_array( $subdomain, $this->user_blocked_emails )) {
                    exit( wp_redirect( esc_url( add_query_arg( 'err', 'blocked_subdomain' ) ) ) );
                }
            }
        }

        $allowDomains = file_get_contents( 'https://raw.githubusercontent.com/amieiro/disposable-email-domains/master/allowDomains.json' );
        $allowDomains = json_decode( $allowDomains, true );

        if ( ! in_array( $emailDomain, $allowDomains )) {

            $denyDomains = file_get_contents( 'https://raw.githubusercontent.com/amieiro/disposable-email-domains/master/denyDomains.json' );
            $denyDomains = json_decode( $denyDomains, true );

            if ( in_array( $emailDomain, $denyDomains )) {
                exit( wp_redirect( esc_url( add_query_arg( 'err', 'blocked_disposable_domain' ))));
            }
        }
    }

    public function um_custom_error_message_handler_blockedemails( $err, $request_err ) {

        switch ( $request_err ) {

            case 'blocked_top_level_domain':    $err = esc_html__( 'We do not accept registrations from this top level email domain.', 'ultimate-member' ); break;
            case 'blocked_subdomain':           $err = esc_html__( 'We do not accept registrations from this email subdomain.',        'ultimate-member' ); break;
            case 'blocked_disposable_domain':   $err = esc_html__( 'We do not accept registrations from this temporary email domain.', 'ultimate-member' ); break;
            default: break;
        }

        return $err;
    }

}

new UM_Blocked_Emails_Domains();
