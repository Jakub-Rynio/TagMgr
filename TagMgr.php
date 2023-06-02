<?php
/**
 * @package TagMgr   
 * @version 1.0.0
 */
/*
Plugin Name: TagMgr
Description: Tag Menager for HTML and JS code in footer
Author: Jakub Rynio 
Version: 1.0.0
Author URI: https://github.com/Jakub-Rynio
*/
/*
=========================
register custom post type
=========================
*/

function tag_menager_reg_cus_post_type(){
	
	$labels = array(
		'name' => 'TagMgr',
		'singular_name' => 'TagMgr',
		'add_new' => 'Dodaj Tag',
		'all_items' => 'Wszystkie Tagi',
		'add_new_item' => 'Dodaj Tag',
		'edit_item' => 'Edytuj Tag',
		'new_item' => 'Nowy Tag',
		'view_item' => 'Zobacz Tag',
		'search_item' => 'Wyszukaj Tag',
		'not_found' => 'Nie znaleziono found',
		'not_found_in_trash' => 'Nie znalesiono w koszu',
	);
	$args = array(
		'labels' => $labels,
		'public' => true,
		'query_var' => false,
		'rewrite' => false,
		'capability_type' => 'post',
		'hierarchical' => false,
		'supports' => array(
			'title',
			'editor',
			'revisions',
		),
		'taxonomies' => array( 'strony' ),
		'menu_position' => 5,
		'exclude_from_search' => true,
	);
	register_post_type( 'tag_mgr', $args );
}
add_action( 'init', 'tag_menager_reg_cus_post_type' );

/*
=========================
register taxonomy
=========================
*/

function tag_menager_reg_taxonomy(){

	register_taxonomy( 'strony', 'tag_mgr',
		 array(
			'label' => 'Strony',
			'rewrite' => array( 'slug' => 'strony' ),
			'hierarchical' => false,
			'show_admin_column' => true
			)
		 );
}
add_action( 'init' , 'tag_menager_reg_taxonomy' );


function tag_mgr_term_strony_generator(){

	$args = array(
		'post_type' => 'page', 
		'post_status' => 'publish', 
		'posts_per_page' => -1, 
	);

	$term_data = array('description' => 'Nazwy stron twojego szablonu. Dodaj je do tagow HTML aby byly widoczne na stronie');
	$pages = get_pages( $args );

	foreach( $pages as $page ){
		$term_data['term'] = get_the_title( $page->ID );
		wp_insert_term( $term_data['term'], 'strony', $term_data );
	}

	$term_data['description'] = 'Osobny znacznik. Po jego dodaniu tag bedzie widoczny na wszystkich stronach';
	wp_insert_term('All', 'strony', $term_data );
}
add_action( 'init', 'tag_mgr_term_strony_generator' );

/*
================================================================
checking if term is assigned to the page and returning the page title
================================================================
*/
function tag_mgr_is_term( $title ){

    $terms = get_terms( array( 'taxonomy' => 'strony' ) );
	$term_names = wp_list_pluck($terms, 'name');

    foreach( $term_names as $term ){
        if( $title == $term ){ return array( $term ); }
    }
}    

function tag_mgr_show_tags(){

	global $post;
    $title = get_the_title( $post );
    $term = tag_mgr_is_term( $title );
	$term[] = 'All';

	$args = array(
        'post_type' => 'tag_mgr',
        'posts_per_page' => -1 ,
        'tax_query' => array(
			array(
				'taxonomy' => 'strony',
				'field' => 'slug',
				'terms' => $term,
            )));

	$loop = new WP_Query( $args );
		if( $loop->have_posts() ):
			
			while( $loop->have_posts() ): $loop->the_post(); 

				$id = get_the_ID();
				$content = get_post_field( 'post_content', $id );
				echo $content;

			endwhile;
			
		endif;
		
	wp_reset_postdata();
}
add_action( 'wp_footer', 'tag_mgr_show_tags' );