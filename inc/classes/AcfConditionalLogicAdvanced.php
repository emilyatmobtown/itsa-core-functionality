<?php

namespace ITSACoreFunctionality;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Nope!' );
}

if ( ! class_exists( 'AcfConditionalLogicAdvanced' ) ) {

	class AcfConditionalLogicAdvanced {
		/**
		 * Return singleton instance of class
		 *
		 * @return self
		 * @since  0.1.0
		 */
		public static function factory() {
			static $instance = false;
			if ( ! $instance ) {
				$instance = new self();
				$instance->setup();
			}
			return $instance;
		}

		/**
		 * Initialize class
		 *
		 * @since 0.1.0
		 */
		public function setup() {

			if ( is_admin() ) {
				require ICF_INC . 'classes/AcfAdminFieldGroupConditionalLogicAdvanced.php';
				$this->acf_admin_field_group_conditional_logic_advanced = AcfAdminFieldGroupConditionalLogicAdvanced::factory();
			}

			add_action( 'init', array( $this, 'register_assets' ), 5 );
			add_action( 'acf/input/admin_enqueue_scripts', array( $this, 'input_admin_enqueue_scripts' ) );
			add_action( 'acf/field_group/admin_enqueue_scripts', array( $this, 'field_group_admin_enqueue_scripts' ) );
			add_filter(
				'acf/update_field',
				function( $field ) {
					// clean up conditional logic keys
					if ( ! empty( $field['conditional_logic_advanced'] ) && is_array( $field['conditional_logic_advanced'] ) ) {
						// extract groups
						$groups = acf_extract_var( $field, 'conditional_logic_advanced' );
						// clean array
						$groups = array_filter( $groups );
						$groups = array_values( $groups );
						// clean rules
						foreach ( array_keys( $groups ) as $i ) {
							$groups[ $i ] = array_filter( $groups[ $i ] );
							$groups[ $i ] = array_values( $groups[ $i ] );
						}
						// reset conditional logic
						$field['conditional_logic_advanced'] = $groups;
					}
					return $field;
				}
			);
			add_action( 'acf/render_field', array( $this, 'acf_render_field' ) );

			$this->initialize();
		}

		public function initialize() {
			add_action( 'acf/render_field_settings', array( $this, 'render_field_settings' ) );
		}

		public function render_field_settings( $field ) {
			if ( 'clone' === $field['type'] ) {
				return;
			}

			$args = [
				'field' => $field,
			];

			require ICF_INC . 'acf-field-group-field-conditional-logic-advanced.php';
		}

		public function acf_render_field( $field ) {
			if ( empty( $field['conditional_logic_advanced'] ) ) {
				return;
			}

			$groups = $field['conditional_logic_advanced'];

			// convert taxonomy term from slug to id
			foreach ( $groups as $group_id => $group ) {
				foreach ( $group as $rule_id => $rule ) {
					if ( ! empty( $rule['field'] ) ) {
						continue;
					}

					if ( 'post_category' !== $rule['param'] ) {
						continue;
					}

					$param         = explode( ':', $rule['value'] );
					$taxonomy_term = get_term_by( 'slug', $param[1], $param[0] );

					$groups[ $group_id ][ $rule_id ]['value'] = array(
						'id'       => $taxonomy_term->term_id,
						'slug'     => $taxonomy_term->slug,
						'taxonomy' => $taxonomy_term->taxonomy,
						'name'     => $taxonomy_term->name,
					);
				}
			}
			?>
				<script type="text/javascript">
					if ( typeof acf !== 'undefined' ){ acf.conditional_logic_advanced.add( '<?php echo esc_attr( $field['key'] ); ?>', <?php echo wp_json_encode( $groups ); ?> ); }
				</script>
			<?php
		}

		public function register_assets() {
			wp_register_script(
				'acf-input-conditional-logic-advanced',
				ICF_URL . 'assets/js/acf-input.js',
				array( 'acf-input' ),
				ICF_VERSION,
				false
			);

			wp_register_script(
				'acf-field-group-conditional-logic-advanced',
				ICF_URL . 'assets/js/acf-field-group.js',
				array( 'acf-input-conditional-logic-advanced' ),
				ICF_VERSION,
				false
			);

			wp_register_style(
				'acf-input-conditional-logic-advanced',
				ICF_URL . 'assets/css/acf-input.css',
				array( 'acf-input' ),
				ICF_VERSION
			);
		}

		public function input_admin_enqueue_scripts() {
			wp_enqueue_script( 'acf-input-conditional-logic-advanced' );
			wp_enqueue_style( 'acf-input-conditional-logic-advanced' );
		}

		public function field_group_admin_enqueue_scripts() {
			wp_enqueue_script( 'acf-field-group-conditional-logic-advanced' );
		}
	}
}
