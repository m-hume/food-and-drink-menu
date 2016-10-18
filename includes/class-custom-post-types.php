<?php
/**
 * Class to handle all custom post type definitions for Food and Drink Menu
 */

if ( !defined( 'ABSPATH' ) )
	exit;

class fdmCustomPostTypes {

	public function __construct() {

		// Call when plugin is initialized on every page load
		add_action( 'init', array( $this, 'load_cpts' ) );

		// Handle metaboxes
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_meta' ) );

		// Add columns and filters to the admin list of menu items
		add_filter( 'manage_fdm-menu-item_posts_columns', array( $this, 'menu_item_posts_columns' ) );
		add_filter( 'manage_edit-fdm-menu-item_sortable_columns', array( $this, 'menu_item_posts_sortable_columns' ) );
		add_action( 'pre_get_posts', array( $this, 'menu_item_posts_orderby' ) );
		add_action( 'manage_fdm-menu-item_posts_custom_column', array( $this, 'menu_item_posts_columns_content' ), 10, 2 );
		add_action( 'restrict_manage_posts', array( $this, 'menu_item_posts_filters' ) );
		add_filter( 'parse_query', array( $this, 'menu_item_posts_filter_query' ) );

		// Add columns and filters to the admin list of menus
		add_filter( 'manage_fdm-menu_posts_columns', array( $this, 'menu_posts_columns' ) );
		add_action( 'manage_fdm-menu_posts_custom_column', array( $this, 'menu_posts_columns_content' ), 10, 2 );

	}

	/**
	 * Initialize custom post types
	 * @since 1.1
	 */
	public function load_cpts() {

		// Define the menu taxonomies
		$menu_taxonomies = array();

		// Create filter so addons can modify the taxonomies
		$menu_taxonomies = apply_filters( 'fdm_menu_taxonomies', $menu_taxonomies );

		// Define the menu custom post type
		$args = array(
			'labels' => array(
				'name' => __( 'Menus', 'food-and-drink-menu' ),
				'singular_name' => __( 'Menu', 'food-and-drink-menu' ),
				'add_new' => __( 'Add Menu', 'food-and-drink-menu' ),
				'add_new_item' => __( 'Add New Menu', 'food-and-drink-menu' ),
				'edit' => __( 'Edit', 'food-and-drink-menu' ),
				'edit_item' => __( 'Edit Menu', 'food-and-drink-menu' ),
				'new_item' => __( 'New Menu', 'food-and-drink-menu' ),
				'view' => __( 'View', 'food-and-drink-menu' ),
				'view_item' => __( 'View Menu', 'food-and-drink-menu' ),
				'search_items' => __( 'Search Menus', 'food-and-drink-menu' ),
				'not_found' => __( 'No Menu found', 'food-and-drink-menu' ),
				'not_found_in_trash' => __( 'No Menu found in Trash', 'food-and-drink-menu' ),
				'parent' => __( 'Parent Menu', 'food-and-drink-menu' )
			),
			'menu_position' => 15,
			'public' => true,
			'rewrite' => array( 'slug' => 'menu' ),
			'supports' => array(
				'title',
				'editor',
				'revisions'
			),
			'taxonomies' => array_keys( $menu_taxonomies )
		);

		// Create filter so addons can modify the arguments
		$args = apply_filters( 'fdm_menu_args', $args );

		// Add an action so addons can hook in before the menu is registered
		do_action( 'fdm_menu_pre_register' );

		// Register the menu
		register_post_type( 'fdm-menu', $args );

		// Add an action so addons can hook in after the menu is registered
		do_action( 'fdm_menu_post_register' );

		// Define the menu item taxonomies
		$menu_item_taxonomies = array(

			// Create menu sections (desserts, entrees, etc)
			'fdm-menu-section'	=> array(
				'labels' 	=> array(
					'name' => __( 'Menu Sections', 'food-and-drink-menu' ),
					'singular_name' => __( 'Menu Section', 'food-and-drink-menu' ),
					'search_items' => __( 'Search Menu Sections', 'food-and-drink-menu' ),
					'all_items' => __( 'All Menu Sections', 'food-and-drink-menu' ),
					'parent_item' => __( 'Menu Section', 'food-and-drink-menu' ),
					'parent_item_colon' => __( 'Menu Section:', 'food-and-drink-menu' ),
					'edit_item' => __( 'Edit Menu Section', 'food-and-drink-menu' ),
					'update_item' => __( 'Update Menu Section', 'food-and-drink-menu' ),
					'add_new_item' => __( 'Add New Menu Section', 'food-and-drink-menu' ),
					'new_item_name' => __( 'Menu Section', 'food-and-drink-menu' ),
					'no_terms' => __( 'No menu sections', 'food-and-drink-menu' ),
					'items_list_navigation' => __( 'Menu sections list navigation', 'food-and-drink-menu' ),
					'items_list' => __( 'Menu sections list', 'food-and-drink-menu' ),
					'archives' => __( 'Menu Archives', 'food-and-drink-menu' ),
					'insert_into_item' => __( 'Insert into menu', 'food-and-drink-menu' ),
					'uploaded_to_this_item' => __( 'Uploaded to this menu', 'food-and-drink-menu' ),
					'filter_items_list' => __( 'Filter menu list', 'food-and-drink-menu' ),
					'item_list_navigation' => __( 'Menu list navigation', 'food-and-drink-menu' ),
					'items_list' => __( 'Menu list', 'food-and-drink-menu' ),
				)
			)

		);

		// Create filter so addons can modify the taxonomies
		$menu_item_taxonomies = apply_filters( 'fdm_menu_item_taxonomies', $menu_item_taxonomies );

		// Register taxonomies
		foreach( $menu_item_taxonomies as $id => $taxonomy ) {
			register_taxonomy(
				$id,
				'',
				$taxonomy
			);
		}

		// Define the Menu Item custom post type
		$args = array(
			'labels' => array(
				'name' => __( 'Menu Items', 'food-and-drink-menu' ),
				'singular_name' => __( 'Menu Item', 'food-and-drink-menu' ),
				'add_new' => __( 'Add Menu Item', 'food-and-drink-menu' ),
				'add_new_item' => __( 'Add New Menu Item', 'food-and-drink-menu' ),
				'edit' => __( 'Edit', 'food-and-drink-menu' ),
				'edit_item' => __( 'Edit Menu Item', 'food-and-drink-menu' ),
				'new_item' => __( 'New Menu Item', 'food-and-drink-menu' ),
				'view' => __( 'View', 'food-and-drink-menu' ),
				'view_item' => __( 'View Menu Item', 'food-and-drink-menu' ),
				'search_items' => __( 'Search Menu Items', 'food-and-drink-menu' ),
				'not_found' => __( 'No Menu Item found', 'food-and-drink-menu' ),
				'not_found_in_trash' => __( 'No Menu Item found in Trash', 'food-and-drink-menu' ),
				'parent' => __( 'Parent Menu Item', 'food-and-drink-menu' ),
				'featured_image' => __( 'Item Photo', 'food-and-drink-menu' ),
				'set_featured_image' => __( 'Set item photo', 'food-and-drink-menu' ),
				'remove_featured_image' => __( 'Remove item photo', 'food-and-drink-menu' ),
				'use_featured_image' => __( 'Use as item photo', 'food-and-drink-menu' ),
				'archives' => __( 'Menu Item Archives', 'food-and-drink-menu' ),
				'insert_into_item' => __( 'Insert into menu item', 'food-and-drink-menu' ),
				'uploaded_to_this_item' => __( 'Uploaded to this menu item', 'food-and-drink-menu' ),
				'filter_items_list' => __( 'Filter menu items list', 'food-and-drink-menu' ),
				'item_list_navigation' => __( 'Menu items list navigation', 'food-and-drink-menu' ),
				'items_list' => __( 'Menu items list', 'food-and-drink-menu' ),
			),
			'menu_position' => 15,
			'public' => true,
			'rewrite' => array( 'slug' => 'menu-item' ),
			'supports' => array(
				'title',
				'editor',
				'thumbnail',
				'revisions',
				'page-attributes'
			),
			'taxonomies' => array_keys( $menu_item_taxonomies )
		);

		// Create filter so addons can modify the arguments
		$args = apply_filters( 'fdm_menu_item_args', $args );

		// Add an action so addons can hook in before the menu is registered
		do_action( 'fdm_menu_item_pre_register' );

		// Register the menu item post type
		register_post_type( 'fdm-menu-item', $args );

		// Add an action so addons can hook in after the menu is registered
		do_action( 'fdm_menu_item_post_register' );

	}

	/**
	 * Add metaboxes to specify custom post type data
	 * @since 1.0
	 */
	public function add_meta_boxes() {

		$meta_boxes = array(

			// Add a menu footer WYSIWYG editor
			'fdm_menu_footer' => array (
				'id'		=>	'fdm_menu_footer',
				'title'		=> __( 'Menu Footer', 'food-and-drink-menu' ),
				'callback'	=> array( $this, 'show_menu_footer' ),
				'post_type'	=> 'fdm-menu',
				'context'	=> 'normal',
				'priority'	=> 'core'
			),

			// Add a menu organizer
			'fdm_menu_layout' => array (
				'id'		=>	'fdm_menu_layout',
				'title'		=> __( 'Menu Layout', 'food-and-drink-menu' ),
				'callback'	=> array( $this, 'show_menu_organizer' ),
				'post_type'	=> 'fdm-menu',
				'context'	=> 'normal',
				'priority'	=> 'default'
			),

			// Add a box that shows menu shortcode
			'fdm_menu_shortcode' => array (
				'id'		=>	'fdm_menu_shortcode',
				'title'		=> __( 'Menu Shortcode', 'food-and-drink-menu' ),
				'callback'	=> array( $this, 'show_menu_shortcode' ),
				'post_type'	=> 'fdm-menu',
				'context'	=> 'side',
				'priority'	=> 'default'
			),

			// Add a box that shows menu item shortcode
			'fdm_menu_item_shortcode' => array (
				'id'		=>	'fdm_menu_item_shortcode',
				'title'		=> __( 'Menu Item Shortcode', 'food-and-drink-menu' ),
				'callback'	=> array( $this, 'show_menu_item_shortcode' ),
				'post_type'	=> 'fdm-menu-item',
				'context'	=> 'side',
				'priority'	=> 'default'
			),

		);

		// Add menu item price metabox
		$settings = get_option( 'food-and-drink-menu-settings' );
		if ( !$settings['fdm-disable-price'] ) {
			$meta_boxes['fdm_menu_item_price'] = array (
				'id'		=>	'fdm_item_price',
				'title'		=> __( 'Price', 'food-and-drink-menu' ),
				'callback'	=> array( $this, 'show_item_price' ),
				'post_type'	=> 'fdm-menu-item',
				'context'	=> 'side',
				'priority'	=> 'default'
			);
		}

		// Create filter so addons can modify the metaboxes
		$meta_boxes = apply_filters( 'fdm_meta_boxes', $meta_boxes );

		// Create the metaboxes
		foreach ( $meta_boxes as $meta_box ) {
			add_meta_box(
				$meta_box['id'],
				$meta_box['title'],
				$meta_box['callback'],
				$meta_box['post_type'],
				$meta_box['context'],
				$meta_box['priority']
			);
		}

		// Remove Attributes metabox from menu organizer
		remove_meta_box( 'pageparentdiv', 'fdm-menu', 'side' );
	}


	/**
	 * Print the Menu Item price metabox HTML
	 * @since 1.0
	 */
	public function show_item_price() {

		// Retrieve values for this if it exists
		global $post;
		$prices = get_post_meta( $post->ID, 'fdm_item_price' );

		// Always add at least one price input field
		if ( empty( $prices ) ) {
			$prices = array( '' );
		}

		?>

			<div class="fdm-input-controls fdm-input-side-panel">
				<div id="fdm-input-prices" class="fdm-input-group">
					<?php foreach( $prices as $key => $price ) : ?>
						<div class="fdm-input-control">
							<label for="fdm_item_price" class="screen-reader-text"><?php echo __( 'Price', 'food-and-drink-menu' ); ?></label>
							<input type="text" name="fdm_item_price[]" value="<?php echo esc_attr( $price ); ?>">
							<a href="#" class="fdm-input-delete">
								<?php esc_html_e( 'Remove this price' ); ?>
							</a>
						</div>
					<?php endforeach; ?>
					<div class="fdm-input-group-add">
						<a href="#" id="fdm-price-add">
							<?php esc_html_e( 'Add Price' ); ?>
						</a>
					</div>
				</div>

				<?php do_action( 'fdm_show_item_price' ); ?>

			</div>

		<?php
	}

	/**
	 * Print the Menu footer HTML
	 * @since 1.0
	 */
	public function show_menu_footer() {

		// Retrieve existing settings
		global $post;
		$footer = get_post_meta( $post->ID, 'fdm_menu_footer_content', true );

		wp_editor(
			$footer,
			'fdm_menu_footer_content',
			array(
				'textarea_rows' => 5
			)
		);
	}

	/**
	 * Print the Menu organizer HTML
	 * @since 1.0
	 */
	public function show_menu_organizer() {

		// Retrieve existing settings
		global $post;
		$column_one = get_post_meta( $post->ID, 'fdm_menu_column_one', true );
		$column_two = get_post_meta( $post->ID, 'fdm_menu_column_two', true );

		// Retrieve sections and store in HTML lists
		$terms = get_terms( 'fdm-menu-section', array( 'hide_empty' => false ) );
		$sections_list = '';
		foreach ( $terms as $term ) {
			$sections_list .= '<li><a href="#" data-termid="' . $term->term_id . '">' . $term->name . ' (' . $term->count . ')</a></li>';
		}
		?>

			<input type="hidden" id="fdm_menu_column_one" name="fdm_menu_column_one" value="<?php echo $column_one; ?>">
			<input type="hidden" id="fdm_menu_column_two" name="fdm_menu_column_two" value="<?php echo $column_two; ?>">

			<p><?php echo __( 'Click on a Menu Section to add it to this menu.', 'food-and-drink-menu' ); ?></p>

			<div id="fdm-menu-organizer">

				<div id="fdm-menu-column-one">
					<div class="fdm-column fdm-options">
						<h4><?php echo __( 'Menu Sections', 'food-and-drink-menu' ); ?></h4>
						<ul>
							<?php echo $sections_list; ?>
						</ul>
					</div>
					<div class="fdm-column fdm-added">
						<h4><?php echo __( 'First Column', 'food-and-drink-menu' ); ?></h4>
						<ul>
						<!-- List filled with values on page load. see admin.js -->
						</ul>
					</div>
				</div>

				<div id="fdm-menu-column-two">
					<div class="fdm-column fdm-added">
						<h4><?php echo __( 'Second Column', 'food-and-drink-menu' ); ?></h4>
						<ul>
						<!-- List filled with values on page load. see admin.js -->
						</ul>
					</div>
					<div class="fdm-column fdm-options">
						<h4><?php echo __( 'Menu Sections', 'food-and-drink-menu' ); ?></h4>
						<ul>
							<?php echo $sections_list; ?>
						</ul>
					</div>
				</div>

				<div class="clearfix"></div>

			</div>

			<p><?php echo __( 'Hint: Leave the second column empty to display the menu in a single column.', 'food-and-drink-menu' ); ?></p>

		<?php

	}

	/**
	 * Print the menu shortcode HTML on the edit page for easy reference
	 * @since 1.0
	 * @note We also add the fdm_nonce field here for security
	 */
	public function show_menu_shortcode() {

		// Retrieve post ID
		global $post;

		// Check if menu is ready to be displayed
		$status = get_post_status( $post->ID );

		// Show advisory note when shortcode isn't ready
		if ( $status != 'publish' ) {

			?>

			<p><?php echo __( 'Once this menu is published, look here to find the shortcode you will use to display this menu in any post or page.', 'food-and-drink-menu' ); ?></p>

			<?php

		// Show the shortcode
		} else {

			?>

				<p><?php echo __( 'Copy and paste the snippet below into any post or page in order to display this menu.', 'food-and-drink-menu' ); ?></p>
				<div class="fdm-menu-shortcode">[fdm-menu id=<?php echo $post->ID; ?>]</div>

			<?php

		}

		// Add an nonce here for security
		?>
		<input type="hidden" name="fdm_nonce" value="<?php echo wp_create_nonce( basename( __FILE__ ) ); ?>">
		<?php
	}

	/**
	 * Print the menu shortcode HTML on the edit page for easy reference
	 * @since 1.0
	 * @note We also add the fdm_nonce field here for security
	 */
	public function show_menu_item_shortcode() {

		// Retrieve post ID
		global $post;

		// Check if menu is ready to be displayed
		$status = get_post_status( $post->ID );

		// Show advisory note when shortcode isn't ready
		if ( $status != 'publish' ) {

			?>

			<p><?php echo __( 'Once this menu is published, look here to find the shortcode you will use to display this menu item in any post or page.', 'food-and-drink-menu' ); ?></p>

			<?php

		// Show the shortcode
		} else {

			?>

				<p><?php echo __( 'Copy and paste the snippet below into any post or page in order to display this menu.', 'food-and-drink-menu' ); ?></p>
				<div class="fdm-menu-shortcode">[fdm-menu-item id=<?php echo $post->ID; ?>]</div>

			<?php

		}

		// Add an nonce here for security
		?>
		<input type="hidden" name="fdm_nonce" value="<?php echo wp_create_nonce( basename( __FILE__ ) ); ?>">
		<?php
	}

	/**
	 * Save the metabox data from menu items and menus
	 * @since 1.0
	 */
	public function save_meta( $post_id ) {

		// Verify nonce
		if ( !isset( $_POST['fdm_nonce'] ) || !wp_verify_nonce( $_POST['fdm_nonce'], basename( __FILE__ ) ) ) {
			return $post_id;
		}

		// Check autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		// Check permissions
		if ( !current_user_can( 'edit_page', $post_id ) ) {
			return $post_id;
		} elseif ( !current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		// Array of values to fetch and store
		$meta_ids = array();

		global $post;

		// Define Menu Item data
		if ( FDM_MENUITEM_POST_TYPE == $post->post_type ) {

			$meta_ids['fdm_item_price'] = 'sanitize_text_field';

		}

		// Define Menu organizer metadata
		if ( FDM_MENU_POST_TYPE == $post->post_type ) {

			$meta_ids['fdm_menu_column_one'] = 'sanitize_text_field';
			$meta_ids['fdm_menu_column_two'] = 'sanitize_text_field';
			$meta_ids['fdm_menu_footer_content'] = 'wp_kses_post';

		}

		// Create filter so addons can add new data
		$meta_ids = apply_filters( 'fdm_save_meta', $meta_ids );

		// Save the metadata
		foreach ($meta_ids as $meta_id => $sanitize_callback) {

			if ( $meta_id == 'fdm_item_price' ) {
				delete_post_meta( $post_id, $meta_id );
				$new = isset( $_POST[$meta_id] ) ? array_map( $sanitize_callback, $_POST[$meta_id] ) : array();
				foreach( $new as $new_entry ) {
					if ( $new_entry !== '' ) {
						add_post_meta( $post_id, $meta_id, $new_entry );
					}
				}
			} else {
				$cur = get_post_meta( $post_id, $meta_id, true );
				$new = isset( $_POST[$meta_id] ) ? call_user_func( $sanitize_callback, $_POST[$meta_id] ) : '';
				if ( $new && $new != $cur ) {
					update_post_meta( $post_id, $meta_id, $new );
				} elseif ( $new == '' && $cur ) {
					delete_post_meta( $post_id, $meta_id, $cur );
				}
			}
		}

	}

	/**
	 * Add the menu section column header to the admin list of menu items
	 * @since 1.4
	 */
	public function menu_item_posts_columns( $columns ) {
		return array(
			'cb'		=> '<input type="checkbox" />',
			'title'		=> __( 'Title' ),
			'price'		=> __( 'Price', 'food-and-drink-menu' ),
			'sections'	=> __( 'Sections', 'food-and-drink-menu' ),
			'shortcode'	=> __( 'Shortcode', 'food-and-drink-menu' ),
			'date'		=> __( 'Date' ),
		);
	}

	/**
	 * Make new column headers sortable
	 * @since 1.4
	 */
	public function menu_item_posts_sortable_columns( $columns ) {
		$columns['price'] = 'price';

		return $columns;
	}

	/**
	 * Modify query rules to sort on new columns
	 * @since 1.4
	 */
	public function menu_item_posts_orderby( $query ) {

		if ( !is_admin() ) {
			return;
		}

		$orderby = $query->get( 'orderby' );

		if ( $orderby == 'price' ) {
			$query->set( 'orderby', 'meta_value' );
			$query->set( 'meta_key', 'fdm_item_price' );
		}

	}

	/**
	 * Add the menu sections to the admin list of menu items
	 * @since 1.4
	 */
	public function menu_item_posts_columns_content( $column, $post ) {

		if ( $column == 'price' ) {
			echo get_post_meta( $post, 'fdm_item_price', true );
		}

		if ( $column == 'sections' ) {
			$terms = wp_get_post_terms( $post, 'fdm-menu-section' );
			$output = array();
			foreach( $terms as $term ) {
				$output[] = '<a href="' . admin_url( 'edit-tags.php?action=edit&taxonomy=fdm-menu-section&tag_ID=' . $term->term_taxonomy_id . '&post_type=fdm-menu-item' ) . '">' . $term->name . '</a>';
			}

			echo join( __( ', ', 'Separator in list of Menu Sections', 'food-and-drink-menu' ), $output );
		}

		if ( $column == 'shortcode' ) {
			echo '[fdm-menu-item id=' . $post . ']';
		}
	}

	/**
	 * Add a filter to view by menu section on the admin list of menu items
	 * @since 1.4
	 */
	public function menu_item_posts_filters() {

		if ( !is_admin() ) {
			return;
		}

		$screen = get_current_screen();
		if ( is_object( $screen ) && $screen->post_type == 'fdm-menu-item' ) {

			$terms = get_terms( 'fdm-menu-section' );

			if ( !empty( $terms ) ) : ?>
				<select name="section">
					<option value=""><?php _e( 'All sections', 'food-and-drink-menu' ); ?></option>

					<?php foreach( $terms as $term ) : ?>
					<option value="<?php echo esc_attr( $term->term_id ); ?>"<?php if( !empty( $_GET['section'] ) && $_GET['section'] == $term->term_id ) : ?> selected="selected"<?php endif; ?>><?php echo esc_attr( $term->name ); ?></option>
					<?php endforeach; ?>

					<option value="-1"><?php _e( 'Unassigned items', 'food-and-drink-menu' ); ?></option>
				</select>
			<?php endif;
		}
	}

	/**
	 * Apply selected filters to the admin list of menu items
	 * @since 1.4
	 */
	public function menu_item_posts_filter_query( $query ) {

		if ( !is_admin() || ( !empty( $query->query['post_type'] ) && $query->query['post_type'] !== FDM_MENUITEM_POST_TYPE ) || !function_exists( 'get_current_screen' ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( is_object( $screen ) && $screen->post_type == FDM_MENUITEM_POST_TYPE && !empty( $_GET['section'] ) ) {
			$section = (int) $_GET['section'];

			// Get menu items not assigned to any section
			if ( $section === -1 ) {
				$terms = get_terms( 'fdm-menu-section', array( 'fields' => 'ids' ) );
				$query->query_vars['tax_query'] = array(
					array(
						'taxonomy'	=> 'fdm-menu-section',
						'field'		=> 'id',
						'terms'		=> $terms,
						'operator'	=> 'NOT IN',
					)
				);

			// Get menu items from a specific section
			} else {
				$query->query_vars['tax_query'] = array(
					array(
						'taxonomy'	=> 'fdm-menu-section',
						'field'		=> 'id',
						'terms'		=> $section,
					)
				);
			}
		}
	}

	/**
	 * Add the menu sections column header to the admin list of menus
	 * @since 1.4
	 */
	public function menu_posts_columns( $columns ) {
		return array(
			'cb'		=> '<input type="checkbox" />',
			'title'		=> __( 'Title' ),
			'sections'	=> __( 'Sections', 'food-and-drink-menu' ),
			'shortcode'	=> __( 'Shortcode', 'food-and-drink-menu' ),
			'date'		=> __( 'Date' ),
		);
	}

	/**
	 * Add the menu sections to the admin list of menu items
	 * @since 1.4
	 */
	public function menu_posts_columns_content( $column, $post ) {

		if ( $column == 'shortcode' ) {
			echo '[fdm-menu id=' . $post . ']';
		}

		if ( $column == 'sections' ) {
			$post_meta = get_post_meta( $post );

			$col1 = !empty( $post_meta['fdm_menu_column_one'] ) ? array_filter( explode( ',', $post_meta['fdm_menu_column_one'][0] ) ) : array();
			$col2 = !empty( $post_meta['fdm_menu_column_two'] ) ? array_filter( explode( ',', $post_meta['fdm_menu_column_two'][0] ) ) : array();

			if ( !empty( $col1 ) || !empty( $col2 ) ) :
				$terms = get_terms( 'fdm-menu-section', array( 'include' => array_merge( $col1, $col2 ), 'fields' => 'id=>name' ) );
				?>

				<table class="fdm-cols">
					<tr>
						<td>
					<?php foreach( $col1 as $id ) : ?>
						<?php if ( isset( $terms[ $id ] ) ) : ?>
							<p>
								<a href="<?php echo admin_url( 'edit-tags.php?action=edit&taxonomy=fdm-menu-section&tag_ID=' . $id . '&post_type=fdm-menu-item' ); ?>">
								<?php echo $terms[ $id ]; ?>
								</a>
							</p>
						<?php endif; ?>
					<?php endforeach; ?>
						</td>
						<td>
					<?php foreach( $col2 as $id ) : ?>
						<?php if ( isset( $terms[ $id ] ) ) : ?>
							<p>
								<a href="<?php echo admin_url( 'edit-tags.php?action=edit&taxonomy=fdm-menu-section&tag_ID=' . $id . '&post_type=fdm-menu-item' ); ?>">
									<?php echo $terms[ $id ]; ?>
								</a>
							</p>
						<?php endif; ?>
					<?php endforeach; ?>
						</td>
					</tr>
				</table>

			<?php endif;

		}
	}

}