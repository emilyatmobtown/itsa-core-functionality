<?php

namespace ITSACoreFunctionality;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Nope!' );
}

if ( ! class_exists( 'AcfAdminFieldGroupConditionalLogicAdvanced' ) ) {

	class AcfAdminFieldGroupConditionalLogicAdvanced {
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
			add_action( 'wp_ajax_acf/field_group/render_conditional_logic_advanced_value', array( $this, 'ajax_render_conditional_logic_advanced_value' ) );
		}

		/**
		 * Renders an input containing location rule values for the given args
		 *
		 * @param array
		 * @since 0.1.0
		 */
		public function render_conditional_logic_advanced_value( $options ) {
			$options = wp_parse_args(
				$options,
				array(
					'field_id' => 0,
					'group_id' => 0,
					'rule_id'  => 0,
					'value'    => null,
					'param'    => null,
					'disabled' => false,
				)
			);

			// vars
			$choices = array();

			switch ( $options['param'] ) {

				case 'post_template':
					$choices = array(
						'default' => apply_filters( 'default_page_template_title', __('Default Template', 'itsa-core-plugin') ),
					);

					// get templates (WP 4.7)
					if ( acf_version_compare( 'wp', '>=', '4.7' ) ) {
						$templates = acf_get_post_templates();
						$choices   = array_merge( $choices, $templates );
					}
					break;

				case 'post_format':
					$choices = get_post_format_strings();
					break;

				case 'post_category':
					$taxonomies = array_map(
						function( $taxonomy_name ) {
							return get_taxonomy( $taxonomy_name );
						},
						acf_get_taxonomies()
					);

					$applicable_taxonomies = array_filter(
						$taxonomies,
						function( $taxonomy ) {
							if ( 'post_format' === $taxonomy->name ) {
								return;
							}
							return true;
						}
					);

					$choices = acf_get_taxonomy_terms(
						array_map(
							function( $taxonomy ) {
								return $taxonomy->name;
							},
							$applicable_taxonomies
						)
					);
					break;
			}

			// Allow custom location rules
			$choices = apply_filters( 'acf/conditional_logic_advanced/rule_values/' . $options['param'], $choices );

			// Create field
			acf_render_field(
				array(
					'type'     => 'select',
					'prefix'   => "acf_fields[{$options['field_id']}][conditional_logic_advanced][{$options['group_id']}][{$options['rule_id']}]",
					'name'     => 'value',
					'value'    => $options['value'],
					'choices'  => $choices,
					'disabled' => $options['disabled'],
				)
			);
		}

		/**
		 * Returns the result from the render_location_value function via an AJAX
		 * action.
		 *
		 * @since 0.1.0
		 */
		public function ajax_render_conditional_logic_advanced_value() {

			// Validate
			if ( ! acf_verify_ajax() ) {
				die();
			}

			// call function
			$this->render_conditional_logic_advanced_value( $_POST );

			// die
			die();
		}
	}
}
