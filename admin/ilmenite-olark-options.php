<?php

class Ilmenite_Olark_Options {

	/**
	 * Plugin Option Group
	 * @var string
	 */
	public $option_group;

	public function __construct() {

		// Define option group
		$this->option_group = 'ilmenite_olark';

		// Add the admin menu
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );

		// Add the Settings
		add_action( 'admin_init', array( $this, 'settings_init' ) );

	}

	/**
	 * Add the Admin Menu Link
	 */
	public function add_admin_menu() {
		add_options_page( __( 'Olark Integration', 'ilmenite-olark' ), __( 'Olark', 'ilmenite-olark' ), 'manage_options', 'ilmenite_olark', array( $this, 'options_page' ) );
	}

	/**
	 * Add the Settings Fields
	 */
	public function settings_init() {

		// Register Setting
		register_setting( $this->option_group, 'ilolark_settings' );

		// Settings Section: Activation & Site Key
		add_settings_section(
			'ilolark_general',
			__( 'General Configuration', 'ilmenite-olark' ),
			array( $this, 'callback_section_general' ),
			$this->option_group
		);

			// Settings Field: Activate
			add_settings_field(
				'ilolark_activated',
				__( 'Activate', 'ilmenite-olark' ),
				array( $this, 'callback_checkbox' ),
				$this->option_group,
				'ilolark_general',
				array(
					'label_for'			=> 'ilolark_activated',
					'name'				=> 'ilolark_activated',
					'checkbox-label' 	=> __( 'Active Olark on the site', 'ilmenite-olark' ),
				)
			);

			// Settings Field: Site ID
			add_settings_field(
				'ilolark_siteid',
				__( 'Olark Site ID', 'ilmenite-olark' ),
				array( $this, 'callback_text' ),
				$this->option_group,
				'ilolark_general',
				array(
					'label_for'			=> 'ilolark_siteid',
					'name'				=> 'ilolark_siteid',
					'field_description' => sprintf( __( 'Insert the Site ID from your Olark account here.<br>If you don\'t know your Site ID, you can find it <a href="%s" target="_blank">on Olark\'s install page here</a>. ', 'ilmenite-olark' ), 'https://www.olark.com/install' ),
				)
			);

		// Settings Section: Localization
		add_settings_section(
			'ilolark_localization',
			__( 'Localization', 'ilmenite-olark' ),
			array( $this, 'callback_section_localization' ),
			$this->option_group
		);

			// Settings Field: Translations
			add_settings_field(
				'ilolark_localize',
				__( 'Localization Support', 'ilmenite-olark' ),
				array( $this, 'callback_checkbox' ),
				$this->option_group,
				'ilolark_localization',
				array(
					'label_for'			=> 'ilolark_localize',
					'name'				=> 'ilolark_localize',
					'checkbox-label' 	=> __( 'Load Olark Localization Code' ),
				)
			);

	}

	/**
	 * HTML Callback for Section
	 */
	public function callback_section_general() {

		echo '<h3 class="title">' . $title . '</h3>';

	}

	/**
	 * HTML Callback for Localization Section
	 */
	public function callback_section_localization() {

		echo '<h3 class="title">' . $title . '</h3>';
		echo '<p>' . __( 'By default, Olark is only available in one configured language. By checking this box, we will load a script that localizes the chat box strings to the current website language using the plugin translations. This works well with multilingual plugins too.', 'ilmenite-olark' ) . '</p>';

	}

	/**
	 * HTML Callback for Text Fields
	 */
	public function callback_text( $args ) {

		// Get the options
		$options = (array) get_option( 'ilolark_settings' );

		// Set defaults
		$args = array_merge( array(
			'name' 				=> '',
			'field_description' => false,
			'type' 				=> 'text',
		), $args );

		$name 	= esc_attr( $args['name'] );
		$type 	= esc_attr( $args['type'] );
		$value 	= esc_attr( $options[ $name ] );

		echo '<input name="ilolark_settings[' . $name . ']" type="' . $type . '" id="' . $name . '" value="' . $value . '" class="regular-text">';

		if ( $args['field_description'] ) {
			echo '<p class="description">' . $args['field_description'] . '</p>';
		}

	}

	/**
	 * HTML Callback for Checkboxes
	 */
	public function callback_checkbox( $args ) {

		$options = (array) get_option( 'ilolark_settings' );

		// Set defaults
		$args = array_merge( array(
			'name' 				=> '',
			'field_description' => false,
			'default'			=> '1',
		), $args );

		$name 	= esc_attr( $args['name'] );

		?>

		<label for="<?php echo $name; ?>">
			<input name="ilolark_settings[<?php echo $name; ?>]" id="<?php echo $name; ?>" type="checkbox" value="1" <?php checked( $options[ $name ], 1 ); ?>> <?php echo $args['checkbox-label']; ?>
		</label>

		<?php
		if ( $args['field_description'] ) {
			echo '<p class="description">' . esc_attr( $args['field_description'] ) . '</p>';
		}

	}

	public function options_page() {

		ob_start(); ?>

		<form action='options.php' method='post'>

			<h2><?php _e( 'Olark Integration', 'ilmenite-olark' ); ?></h2>
			<p><?php _e( 'This (unofficial) Olark integration plugin offers an easy way to add the Olark code to your WordPress site and adds full localization support for your chat box.', 'ilmenite-olark' ); ?></p>
			<p><?php _e( '<strong>Getting Started</strong><br>Just a couple of steps are necessary to get your Olark chat box added to your site. Go through the settings on this page and set them as needed and you\'ll be up and running right away', 'ilmenite-olark' ); ?></p>
			<p><?php printf( __( '<strong>Need Help?</strong><br>If you find an issue with this plugin, please do report it over on <a href="%s">our GitHub repository</a>. If you need help with configuring Olark itself, you should get in touch with them.', 'ilmenite-olark' ), 'https://github.com/bernskioldmedia/ilmenite-olark/issues' ); ?></p>

			<?php
			settings_fields( $this->option_group );
			do_settings_sections( $this->option_group );
			submit_button();
			?>

		</form>

		<?php
		echo ob_get_clean();

	}

}