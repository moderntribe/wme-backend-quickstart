<?php declare( strict_types=1 );

abstract class WME_Sparkplug_Wizard {

	use WME_Sparkplug_Uses_Ajax;

	/**
	 * @var string
	 */
	protected $admin_page_slug;

	/**
	 * @var string
	 */
	protected $wizard_slug;

	/**
	 * Properties for wizard.
	 *
	 * @return array
	 */
	abstract public function props(): array;

	/**
	 * AJAX action for finishing wizard.
	 *
	 * @return void
	 */
	abstract public function finish(): void;

	/**
	 * Construct.
	 */
	public function __construct() {
		$this->register_hooks();
	}

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register_hooks(): void {

		$hook = sprintf( '%s/print_scripts', $this->admin_page_slug );
		add_action( $hook, [ $this, 'action__print_scripts' ] );

		if ( ! $this->maybe_register_ajax_action() ) {
			return;
		}

		$this->add_ajax_action( 'finish', [ $this, 'finish' ] );

	}

	/**
	 * Action: {$admin_page_slug}/print_scripts
	 *
	 * Print wizard properties to admin page.
	 *
	 * @uses $this->props()
	 *
	 * @return void
	 */
	public function action__print_scripts(): void {
		$props         = ( array ) $this->props();
		$default_props = [];

		if ( empty( $props ) ) {
			return;
		}

		if ( $this->supports_ajax() ) {
			$default_props['ajax'] = $this->ajax_props();
		}

		$wizard_slug = json_encode( ( string ) $this->wizard_slug );
		$props       = json_encode( wp_parse_args( $props, $default_props ) );

		printf( '<script>window[%s]["wizards"][%s] = %s</script>%s', json_encode( $this->admin_page_slug ), $wizard_slug, $props, PHP_EOL );
	}

}
