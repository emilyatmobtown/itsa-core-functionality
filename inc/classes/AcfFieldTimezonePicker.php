<?php
/**
 * Class to create and add an ACF field for a timezone picker
 *
 * @link https://github.com/AdvancedCustomFields/acf-field-type-template
 * @package ITSACoreFunctionality
 */

namespace ITSACoreFunctionality;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Nope!' );
}

if ( class_exists( 'acf_field' ) && ! class_exists( 'AcfFieldTimezonePicker' ) ) {

	class AcfFieldTimezonePicker extends \acf_field {

		/**
		 * Initialize class. Must be called 'initialize' to integrate with ACF.
		 *
		 * @since 0.1.0
		 */
		public function initialize() {
			$this->name = 'itsa_time_itsa_zone_picker';
			//translators: This is the label for the Timezone field.
			$this->label    = __( 'Timezone Picker', 'itsa-core-plugin' );
			$this->category = 'basic';
			$this->defaults = array(
				'default_time_zone' => '',
			);
		}

		/**
		 * Create settings for the timezone field.
		 *
		 * @param array
		 * @since 0.1.0
		 */
		public function render_field_settings( $field ) {

			if ( ! empty( $field ) && isset( $field ) ) {
				acf_render_field_setting(
					$field,
					array(
						//translators: This is the label for the Default Timezone field.
						'label'        => __( 'Default Timezone', 'itsa-core-plugin' ),
						//translators: This is the instructions for the Default Timezone field.
						'instructions' => __( 'Enter default timezone in the format of <Continent>/<City> with underscored names, i.e. America/New_York', 'itsa-core-plugin' ),
						'type'         => 'text',
						'name'         => 'default_time_zone',
					)
				);
			}
		}

		/**
		 * Create the HTML for the timezone field.
		 *
		 * @param array
		 * @since 0.1.0
		 */
		public function render_field( $field ) {
			$field_value = trim( $field['value'] );

			if ( ! $field_value && $field['default_time_zone'] ) {
				$field_value = trim( $field['default_time_zone'] );
			}
			?>

			<select name="<?php echo esc_attr( $field['name'] ); ?>">
				<option value=""></option>
				<?php
				foreach ( \DateTimeZone::listIdentifiers() as $tz ) {
					$current_tz  = new \DateTimeZone( $tz );
					$transition  = $current_tz->getTransitions();
					$abbr        = $transition[0]['abbr'];
					$is_selected = trim( $tz ) === $field_value ? ' selected="selected"' : '';
					?>
					<option value="<?php echo esc_attr( $tz ); ?>"<?php echo esc_attr( $is_selected ); ?>><?php echo esc_attr( $tz . ' (' . $abbr . ')' ); ?></option>
				<?php } ?>
			</select>
			<?php
		}
	}

	$n = function( $function ) {
		return __NAMESPACE__ . '\\' . $function;
	};

	\acf_register_field_type( $n( 'AcfFieldTimezonePicker' ) );
}
