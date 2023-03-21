<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package Paineau
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function paineau_body_classes( $classes ) {
	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	// Adds a class of no-sidebar when there is no sidebar present.
	if ( ! is_active_sidebar( 'sidebar-1' ) ) {
		$classes[] = 'no-sidebar';
	}

	return $classes;
}
add_filter( 'body_class', 'paineau_body_classes' );

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function paineau_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'paineau_pingback_header' );

//Create Post Type Events
function custom_post_type() {
  $labels = array(
    'name'               => 'Événements',
    'singular_name'      => 'Événement',
    'menu_name'          => 'Événements',
    'name_admin_bar'     => 'Événement',
    'add_new'            => 'Ajouter un nouvel événement',
    'add_new_item'       => 'Ajouter un nouvel événement',
    'new_item'           => 'Nouvel événement',
    'edit_item'          => 'Modifier l\'événement',
    'view_item'          => 'Voir l\'événement',
    'all_items'          => 'Tous les événements',
    'search_items'       => 'Rechercher des événements',
    'parent_item_colon'  => 'Événement parent :',
    'not_found'          => 'Aucun événement trouvé.',
    'not_found_in_trash' => 'Aucun événement trouvé dans la corbeille.'
  );

  $args = array(
    'labels'             => $labels,
    'public'             => true,
    'publicly_queryable' => true,
    'show_ui'            => true,
    'show_in_menu'       => true,
    'query_var'          => true,
    'rewrite'            => array( 'slug' => 'evenements' ),
    'capability_type'    => 'post',
    'has_archive'        => true,
    'hierarchical'       => false,
    'menu_position'      => null,
    'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' ),
	'show_in_rest'       => true,
	'rest_base'          => 'evenements',
	'rest_controller_class' => 'WP_REST_Posts_Controller',
  );

  register_post_type( 'evenements', $args );
}

add_filter( 'rest_authentication_errors', function( $result ) {
    if ( ! is_user_logged_in() ) {
        $error = new WP_Error(
            'rest_not_logged_in',
            __( 'You are not currently logged in.' ),
            array( 'status' => 401 )
        );
        return $error;
    }
    return $result;
});

add_action( 'init', 'custom_post_type' );

function custom_meta_boxes() {
  add_meta_box( 'start_date', 'Date de début', 'start_date_callback', 'evenements', 'normal', 'high' );
  add_meta_box( 'end_date', 'Date de fin', 'end_date_callback', 'evenements', 'normal', 'high' );
  add_meta_box( 'event_category', 'Catégorie de l\'événement', 'event_category_callback', 'evenements', 'normal', 'core' );
  add_meta_box( 'event_status', 'Statut de l\'événement', 'event_status_callback', 'evenements', 'normal', 'high' );
  add_meta_box( 'event_documents', 'Documents de l\'événement', 'event_documents_callback', 'evenements', 'normal', 'high' );
  add_meta_box( 'signature', 'Signature', 'signature_callback', 'evenements', 'normal', 'high' );
  add_meta_box( 'ouvrier', 'Ouvrier', 'ouvrier_callback', 'evenements', 'normal', 'high' );
  add_meta_box( 'client', 'Client', 'client_callback', 'evenements', 'normal', 'high' );
  add_meta_box( 'notes_chantier', 'Notes de chantier', 'notes_chantier_callback', 'evenements', 'normal', 'high' );
}

function start_date_callback( $post ) {
  wp_nonce_field( basename( __FILE__ ), 'start_date_nonce' );
  $value = get_post_meta( $post->ID, 'start_date', true );
  $value2 = get_post_meta( $post->ID, 'start_time', true );
  echo '<input type="date" id="start_date" name="start_date" value="' . esc_attr( $value ) . '">';
  echo '<input type="time" id="start_time" name="start_time" value="' . esc_attr( $value2 ) . '">';
}

function end_date_callback( $post ) {
	wp_nonce_field( basename( __FILE__ ), 'end_date_nonce' );
	$value = get_post_meta( $post->ID, 'end_date', true );
	$value2 = get_post_meta( $post->ID, 'end_time', true );
	echo '<input type="date" id="end_date" name="end_date" value="' . esc_attr( $value ) . '">';
	echo '<input type="time" id="end_time" name="end_time" value="' . esc_attr( $value2 ) . '">';
}

function event_category_callback( $post ) {
	wp_nonce_field( basename( __FILE__ ), 'event_category_nonce' );
	$value = get_post_meta( $post->ID, 'event_category', true );
	//select event category
	echo '<select id="event_category" name="event_category">';
	echo '<option value="1" ' . selected( $value, '1', false ) . '>Chantier</option>';
	echo '<option value="2" ' . selected( $value, '2', false ) . '>Plomberie</option>';
	echo '<option value="3" ' . selected( $value, '3', false ) . '>Chauffagerie</option>';
	echo '<option value="4" ' . selected( $value, '4', false ) . '>Electricité</option>';
	echo '<option value="4" ' . selected( $value, '4', false ) . '>Repas</option>';
	echo '</select>';
}

function event_status_callback( $post ) {
	wp_nonce_field( basename( __FILE__ ), 'event_status_nonce' );
	$value = get_post_meta( $post->ID, 'event_status', true );
	//select event status
	echo '<select id="event_status" name="event_status">';
	echo '<option value="1" ' . selected( $value, '1', false ) . '>A réaliser</option>';
	echo '<option value="2" ' . selected( $value, '2', false ) . '>En cours</option>';
	echo '<option value="3" ' . selected( $value, '3', false ) . '>Terminé</option>';
	echo '</select>';
}

function event_documents_callback( $post ) {
	wp_nonce_field( basename( __FILE__ ), 'event_documents_nonce' );
	$value = get_post_meta( $post->ID, 'event_documents', true );
	//Input file for event documents
	echo '<input type="file" id="event_documents" name="event_documents" value="' . esc_attr( $value ) . '">';
}

function signature_callback( $post ) {
	wp_nonce_field( basename( __FILE__ ), 'signature_nonce' );
	$value = get_post_meta( $post->ID, 'signature', true );
	//Input file for event documents
	echo '<input type="file" id="signature" name="signature" value="' . esc_attr( $value ) . '">';
}

function ouvrier_callback( $post ) {
	wp_nonce_field( basename( __FILE__ ), 'ouvrier_nonce' );
	$value = get_post_meta( $post->ID, 'ouvrier', true );
	//Select with all users with role ouvrier
	echo '<select id="ouvrier" name="ouvrier">';
	$args = array(
		'role'    => 'Ouvrier',
		'orderby' => 'user_nicename',
		'order'   => 'ASC'
	);
	$users = get_users( $args );
	foreach ( $users as $user ) {
		echo '<option value="' . $user->ID . '" ' . selected( $value, $user->ID, false ) . '>' . $user->display_name . '</option>';
	}
	echo '</select>';
}

function client_callback( $post ) {
	wp_nonce_field( basename( __FILE__ ), 'client_nonce' );
	$value = get_post_meta( $post->ID, 'client', true );
	//Input email for client
	echo '<input type="email" id="client" name="client" value="' . esc_attr( $value ) . '">';
}

function notes_chantier_callback( $post ) {
	wp_nonce_field( basename( __FILE__ ), 'notes_chantier_nonce' );
	$value = get_post_meta( $post->ID, 'notes_chantier', true );
	//Input email for client
	echo '<textarea id="notes_chantier" name="notes_chantier" rows="5" cols="50">' . esc_attr( $value ) . '</textarea>';
}

function save_start_date_meta_box( $post_id ) {
  if ( ! isset( $_POST['start_date_nonce'] ) || ! wp_verify_nonce( $_POST['start_date_nonce'], basename( __FILE__ ) ) ) {
    return $post_id;
  }

  if ( ! isset( $_POST['end_date_nonce'] ) || ! wp_verify_nonce( $_POST['end_date_nonce'], basename( __FILE__ ) ) ) {
    return $post_id;
  }

  if ( ! isset( $_POST['event_category_nonce'] ) || ! wp_verify_nonce( $_POST['event_category_nonce'], basename( __FILE__ ) ) ) {
    return $post_id;
  }

  if ( ! isset( $_POST['event_status_nonce'] ) || ! wp_verify_nonce( $_POST['event_status_nonce'], basename( __FILE__ ) ) ) {
	return $post_id;
  }

  if ( ! isset( $_POST['event_documents_nonce'] ) || ! wp_verify_nonce( $_POST['event_documents_nonce'], basename( __FILE__ ) ) ) {
	return $post_id;
  }

  if ( ! isset( $_POST['signature_nonce'] ) || ! wp_verify_nonce( $_POST['signature_nonce'], basename( __FILE__ ) ) ) {
	return $post_id;
  }

  if ( ! isset( $_POST['ouvrier_nonce'] ) || ! wp_verify_nonce( $_POST['ouvrier_nonce'], basename( __FILE__ ) ) ) {
	return $post_id;
  }

  if ( ! isset( $_POST['client_nonce'] ) || ! wp_verify_nonce( $_POST['client_nonce'], basename( __FILE__ ) ) ) {
	return $post_id;
  }

  if ( ! isset( $_POST['notes_chantier_nonce'] ) || ! wp_verify_nonce( $_POST['notes_chantier_nonce'], basename( __FILE__ ) ) ) {
	return $post_id;
  }


  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
    return $post_id;
  }

  if ( isset( $_POST['post_type'] ) && 'evenements' == $_POST['post_type'] ) {
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
	  return $post_id;
	}
	if ( ! isset( $_POST['start_date'] ) ) {
		return $post_id;
	}
	if( ! isset( $_POST['start_time'] ) ) {
		return $post_id;
	}
	if ( ! isset( $_POST['end_date'] ) ) {
		return $post_id;
	}
	if( ! isset( $_POST['end_time'] ) ) {
		return $post_id;
	}
	if ( ! isset( $_POST['event_category'] ) ) {
		return $post_id;
	}
	if ( ! isset( $_POST['event_status'] ) ) {
		return $post_id;
	}
	if ( ! isset( $_POST['event_documents'] ) ) {
		return $post_id;
	}
	if( ! isset( $_POST['signature'] ) ) {
		return $post_id;
	}
	if( ! isset( $_POST['ouvrier'] ) ) {
		return $post_id;
	}
	if( ! isset( $_POST['client'] ) ) {
		return $post_id;
	}
	if( ! isset( $_POST['notes_chantier'] ) ) {
		return $post_id;
	}


	$start_date = sanitize_text_field( $_POST['start_date'] );
	$start_time = sanitize_text_field( $_POST['start_time'] );
	$end_date = sanitize_text_field( $_POST['end_date'] );
	$end_time = sanitize_text_field( $_POST['end_time'] );
	$event_category = sanitize_text_field( $_POST['event_category'] );
	$event_status = sanitize_text_field( $_POST['event_status'] );
	$event_documents = sanitize_text_field( $_POST['event_documents'] );
	$signature = sanitize_text_field( $_POST['signature'] );
	$ouvrier = sanitize_text_field( $_POST['ouvrier'] );
	$client = sanitize_text_field( $_POST['client'] );
	$notes_chantier = sanitize_text_field( $_POST['notes_chantier'] );

	update_post_meta( $post_id, 'start_date', $start_date );
	update_post_meta( $post_id, 'start_time', $start_time );
	update_post_meta( $post_id, 'end_date', $end_date );
	update_post_meta( $post_id, 'end_time', $end_time );
	update_post_meta( $post_id, 'event_category', $event_category );
	update_post_meta( $post_id, 'event_status', $event_status );
	update_post_meta( $post_id, 'event_documents', $event_documents );
	update_post_meta( $post_id, 'signature', $signature );
	update_post_meta( $post_id, 'ouvrier', $ouvrier );
	update_post_meta( $post_id, 'client', $client );
	update_post_meta( $post_id, 'notes_chantier', $notes_chantier );

  }
}

add_action( 'add_meta_boxes', 'custom_meta_boxes' );
add_action( 'save_post', 'save_start_date_meta_box' );

//remove discussion, excerpt author and comments from events
function remove_meta_boxes() {
  remove_meta_box( 'commentstatusdiv', 'evenements', 'normal' );
  remove_meta_box( 'commentsdiv', 'evenements', 'normal' );
  remove_meta_box( 'authordiv', 'evenements', 'normal' );
  remove_meta_box( 'postexcerpt', 'evenements', 'normal' );
  remove_post_type_support( 'evenements', 'editor' );
}

add_action( 'admin_menu', 'remove_meta_boxes' );

//show meta boxes in api rest of events custom post type
function add_meta_to_api( $data, $post, $context ) {
	$_data = $data->data;
	$custom_fields = get_post_custom( $post->ID );
	foreach ( $custom_fields as $key => $value ) {
		if ( $key == 'start_date' ) {
			$_data[$key] = $value[0];
		}
		if ( $key == 'start_time' ) {
			$_data[$key] = $value[0];
		}
		if ( $key == 'end_date' ) {
			$_data[$key] = $value[0];
		}
		if ( $key == 'end_time' ) {
			$_data[$key] = $value[0];
		}
		if ( $key == 'event_category' ) {
			$_data[$key] = $value[0];
		}
		if ( $key == 'event_status' ) {
			$_data[$key] = $value[0];
		}
		if ( $key == 'event_documents' ) {
			$_data[$key] = $value[0];
		}
		if ( $key == 'signature' ) {
			$_data[$key] = $value[0];
		}
		if ( $key == 'ouvrier' ) {
			$_data[$key] = $value[0];
		}
		if ( $key == 'client' ) {
			$_data[$key] = $value[0];
		}
		if ( $key == 'notes_chantier' ) {
			$_data[$key] = $value[0];
		}
	}
	$data->data = $_data;
	return $data;
}

add_filter( 'rest_prepare_evenements', 'add_meta_to_api', 10, 3 );

//create role for user
function add_custom_role() {
    $capabilities = array(
        'read' => true,
		'edit_posts' => true,
		'edit_published_posts' => true,
		'edit_others_posts' => true,
		'edit_private_posts' => true,
		'edit_others_posts' => true,
		'edit_others_pages' => true,
		'edit_published_pages' => true,
		'edit_private_pages' => true,
		'edit_published_posts' => true,
		'edit_private_posts' => true,
		'edit_published_posts' => true,
		'publish_posts' => true,
		'publish_pages' => true,
    );
    
    add_role( 'ouvrier', 'Ouvrier', $capabilities );
}
add_action( 'init', 'add_custom_role' );