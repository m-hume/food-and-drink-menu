<?php

/**
 * Register, display and save an settings page in the WordPress admin menu.
 *
 * @since 1.0
 * @package Simple Admin Pages
 */

class sapAdminPage_1_0 {

	public $title;
	public $menu_title;
	public $description; // optional description for this page
	public $capability; // user permissions needed to edit this panel
	public $id; // id of this page
	public $icon = 'icon-options-general';
	public $sections = array(); // array of sections to display on this page

	private $section_class_name = 'sapAdminPageSection';


	/**
	 * Initialize the page
	 * @since 1.0
	 */
	public function __construct( $id, $title, $menu_title, $description, $capability ) {

		$this->id = esc_attr( $id ); // id of this page
		$this->title = $title;
		$this->menu_title = $menu_title;
		$this->description = $description;
		$this->capability = $capability;

	}

	/**
	 * Add the page to the appropriate menu slot.
	 * @note The default will be to post to the options page, but other classes
	 *			should override this function.
	 * @since 1.0
	 */
	public function add_admin_menu() {
		add_options_page( $this->title, $this->menu_title, $this->capability, $this->id, array( $this, 'display_admin_menu' ) );
	}

	/**
	 * Add a section to the page
	 * @since 1.0
	 */
	public function add_section( $section ) {
		if ( !$section ) {
			return;
		}

		$this->sections[ $section->id ] = $section;

	}

	/**
	 * Register the settings and sanitization callbacks for each setting
	 * @since 1.0
	 */
	public function register_admin_menu() {

		// Loop over each section
		foreach ( $this->sections as $section ) {
			$section->add_settings_section( $this->id );

			// Loop over each setting
			foreach ( $section->settings as $setting ) {
				$setting->add_register_setting( $this->id, $section->id );
			}
		}
	}


	/**
	 * Output the settings passed to this page
	 * @since 1.0
	 */
	public function display_admin_menu() {

		if ( !$this->title && !count( $this->settings ) ) {
			return;
		}
		?>

			<div class="wrap">

				<?php $this->display_page_title(); ?>

				<form method="post" action="options.php">
					<?php settings_fields( $this->id ); ?>
					<?php do_settings_sections( $this->id ); ?>
					<?php submit_button(); ?>
				 </form>
			</div>

		<?php
	}

	/**
	 * Output the title of the page
	 * @since 1.0
	 */
	public function display_page_title() {

		if ( !$this->title ) {
			return;
		}
		?>
			<div id="<?php echo $this->icon; ?>" class="icon32"><br /></div>
			<h2><?php echo $this->title; ?></h2>
		<?php
	}

	/**
	 * Loop over the sections and call the display function for each
	 * @since 1.0
	 */
	public function display_sections() {
		foreach ( $this->sections as $setting ) {
			$section->display_section();
		}
	}

	/**
	 * Display the submit button
	 * @since 1.0
	 */
	public function display_submit_button() {
		?>
			<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"  /></p>
		<?php
	}

}