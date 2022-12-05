<?php
/**
 * Plugin Name:     Ultimate Member - Blocked Email Domains
 * Description:     Extension to Ultimate Member for additional blocking possibilities like subdomains and top level domains.
 * Version:         1.0.0
 * Requires PHP:    7.4
 * Author:          Miss Veronica
 * License:         GPL v2 or later
 * License URI:     https://www.gnu.org/licenses/gpl-2.0.html
 * Author URI:      https://github.com/MissVeronica
 * Text Domain:     ultimate-member
 * Domain Path:     /languages
 * UM version:      2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; 
if ( ! class_exists( 'UM' ) ) return;


add_action( 'um_submit_form_errors_hook__blockedemails', 'um_submit_form_customized_blockedemails', 10, 1 );
remove_action( 'um_submit_form_errors_hook__blockedemails', 'um_submit_form_errors_hook__blockedemails', 10 );

add_filter( 'um_custom_error_message_handler', 'um_custom_error_message_handler_blockedemails', 10, 2 );


function um_submit_form_customized_blockedemails( $args ) {

    $emails = UM()->options()->get( 'blocked_emails' );
    if ( ! $emails ) {
        return;
    }

    $emails = strtolower( $emails );
    $emails = array_map( 'rtrim', explode( "\n", $emails ) );

    if ( isset( $args['user_email'] ) && is_email( $args['user_email'] ) ) {

        if ( in_array( strtolower( $args['user_email'] ), $emails ) ) {
            exit( wp_redirect( esc_url( add_query_arg( 'err', 'blocked_email' ) ) ) );
        }

        $domain = explode( '@', $args['user_email'] );
        $check_domain = str_replace( $domain[0], '*', $args['user_email'] );

        if ( in_array( strtolower( $check_domain ), $emails ) ) {
            exit( wp_redirect( esc_url( add_query_arg( 'err', 'blocked_domain' ) ) ) );
        }

        $tld = explode( '.', strtolower( $domain[1] ));
        $domain_index = count( $tld ) - 1;
        $check_tld = '*.' . $tld[$domain_index]; 

        if ( in_array( $check_tld, $emails ) ) {
            exit( wp_redirect( esc_url( add_query_arg( 'err', 'blocked_top_level_domain' ) ) ) );
        }

        while( count( $tld ) > 2 ) {
            array_shift( $tld );
            $subdomain = '*.' . implode( '.', $tld );
 
            if ( in_array( $subdomain, $emails ) ) {
                exit( wp_redirect( esc_url( add_query_arg( 'err', 'blocked_subdomain' ) ) ) );
            }
        }
    }

    if ( isset( $args['username'] ) && is_email( $args['username'] ) ) {

        if ( in_array( strtolower( $args['username'] ), $emails ) ) {
            exit( wp_redirect( esc_url( add_query_arg( 'err', 'blocked_email' ) ) ) );
        }
        
        $domain = explode( '@', $args['username'] );
        $check_domain = str_replace( $domain[0], '*', $args['username'] );

        if ( in_array( strtolower( $check_domain ), $emails ) ) {
            exit( wp_redirect( esc_url( add_query_arg( 'err', 'blocked_domain' ) ) ) );
        }

        $tld = explode( '.', strtolower( $domain[1] ));
        $domain_index = count( $tld ) - 1;
        $check_tld = '*.' . $tld[$domain_index]; 

        if ( in_array( $check_tld, $emails ) ) {
            exit( wp_redirect( esc_url( add_query_arg( 'err', 'blocked_top_level_domain' ) ) ) );
        }
        
        while( count( $tld ) > 2 ) {
            array_shift( $tld );
            $subdomain = '*.' . implode( '.', $tld );
             
            if ( in_array( $subdomain, $emails ) ) {
                exit( wp_redirect( esc_url( add_query_arg( 'err', 'blocked_subdomain' ) ) ) );
            }
        }
    }
}

function um_custom_error_message_handler_blockedemails( $err, $request_err ) {

    if( $request_err == 'blocked_top_level_domain' ) {

        $err =  __( 'We do not accept registrations from this top level email domain.', 'ultimate-member' );
    }    
    if( $request_err == 'blocked_subdomain' ) {

        $err =  __( 'We do not accept registrations from this email subdomain.', 'ultimate-member' );
    }

    return $err;
}
