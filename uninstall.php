<?php
/*
======================
Uninstaller
======================
*/

if( ! defined( 'WP_UNINSTALL_PLUGIN' ) ){
    die;
}

global $wpdb;

$wpdb->query( "DELETE FROM {$wpdb->posts} WHERE post_type = 'tag_mgr'" );
$wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE post_id NOT IN (SELECT id FROM {$wpdb->posts})" );
$wpdb->query( "DELETE FROM {$wpdb->term_relationships} WHERE object_id NOT IN (SELECT id FROM {$wpdb->posts})" );
$wpdb->query( "DELETE FROM {$wpdb->terms} WHERE term_id NOT IN (SELECT term_taxonomy_id FROM {$wpdb->term_relationships})" );