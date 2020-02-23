<?php
/**
 * @package ITSACoreFunctionality
 */

namespace ITSACoreFunctionality\ConditionalLogic;

$field    = acf_extract_var( $args, 'field' );
$groups   = acf_extract_var( $field, 'conditional_logic_advanced' );
$disabled = empty( $groups ) ? 1 : 0;

// UI needs at least 1 conditional logic rule
if ( empty( $groups ) ) {

	$groups = array(
		// group 0
		array(
			// rule 0
			array(
				'param'    => 'post_template',
				'operator' => '==',
				'value'    => 'default',
			),
		),
	);

}

// vars
$rule_types = apply_filters(
	'acf/conditional_logic_advanced/rule_types',
	array(
		__( 'Post', 'itsa-core-plugin' ) => array(
			'post_template' => __( 'Post Template', 'itsa-core-plugin' ),
			'post_format'   => __( 'Post Format', 'itsa-core-plugin' ),
			'post_category' => __( 'Post Taxonomy', 'itsa-core-plugin' ),
		),
	)
);


// WP < 4.7
if ( acf_version_compare( 'wp', '<', '4.7' ) ) {
	unset( $rule_types[ __( 'Post', 'itsa-core-plugin' ) ]['post_template'] );
}

$rule_operators = apply_filters(
	'acf/conditional_logic_advanced/rule_operators',
	array(
		'==' => __( 'is equal to', 'itsa-core-plugin' ),
		'!=' => __( 'is not equal to', 'itsa-core-plugin' ),
	)
);

?>
<tr class="acf-field acf-field-true-false acf-field-setting-conditional_logic_advanced" data-type="true_false" data-name="conditional_logic_advanced">
	<td class="acf-label">
		<label><?php esc_html_e( 'Conditional Logic Advanced', 'itsa-core-plugin' ); ?></label>
	</td>
	<td class="acf-input">
		<?php

		acf_render_field(
			array(
				'type'   => 'true_false',
				'name'   => 'conditional_logic_advanced',
				'prefix' => $field['prefix'],
				'value'  => $disabled ? 0 : 1,
				'ui'     => 1,
				'class'  => 'conditional-logic-advanced-toggle',
			)
		);

		?>
		<div class="rule-groups" <?php if ( $disabled ) { ?>style="display:none;"<?php } ?>>
			<?php
			foreach ( $groups as $group_id => $group ) {
				if ( empty( $group ) ) {
					continue;
				}

				// $group_id must be completely different to $rule_id to avoid JS issues
				$group_id = 'group_' . $group_id;
				$h4       = ( 'group_0' === $group_id ) ? __( 'Show this field group if', 'itsa-core-plugin' ) : __( 'or', 'itsa-core-plugin' );
				?>

				<div class="rule-group" data-id="<?php echo esc_attr( $group_id ); ?>">
					<h4><?php echo esc_attr( $h4 ); ?></h4>
					<table class="acf-table -clear">
						<tbody>
							<?php
							foreach ( $group as $rule_id => $rule ) {

								// valid rule
								$rule = wp_parse_args(
									$rule,
									array(
										'field'    => '',
										'operator' => '==',
										'value'    => '',
									)
								);

								// $group_id must be completely different to $rule_id to avoid JS issues
								$rule_id = 'rule_{$rule_id}';
								$prefix  = "{$field['prefix']}[conditional_logic_advanced][{$group_id}][{$rule_id}]";
								?>
								<tr data-id="<?php echo esc_attr( $rule_id ); ?>">
								<td class="param">
									<?php

									// create field
									acf_render_field(
										array(
											'type'     => 'select',
											'prefix'   => $prefix,
											'name'     => 'param',
											'value'    => $rule['param'],
											'choices'  => $rule_types,
											'class'    => 'conditional-logic-advanced-rule-param',
											'disabled' => $disabled,
										)
									);
									?>
								</td>
								<td class="operator">
									<?php

									// create field
									acf_render_field(
										array(
											'type'     => 'select',
											'prefix'   => $prefix,
											'name'     => 'operator',
											'value'    => $rule['operator'],
											'choices'  => $rule_operators,
											'class'    => 'conditional-logic-advanced-rule-operator',
											'disabled' => $disabled,
										)
									);
									?>
								</td>
								<td class="value">
									<?php

									$this->acf_admin_field_group_conditional_logic_advanced->render_conditional_logic_advanced_value(
										array(
											'field_id' => $field['ID'],
											'group_id' => $group_id,
											'rule_id'  => $rule_id,
											'value'    => $rule['value'],
											'param'    => $rule['param'],
											'class'    => 'conditional-logic-advanced-rule-value',
											'disabled' => $disabled,
										)
									);
									?>
								</td>
								<td class="add">
									<a href="#" class="button add-conditional-logic-advanced-rule"><?php esc_html_e( 'and', 'itsa-core-plugin' ); ?></a>
								</td>
								<td class="remove">
									<a href="#" class="acf-icon -minus remove-conditional-logic-advanced-rule"></a>
								</td>
								</tr>
							<?php } ?>
						</tbody>
					</table>

				</div>
			<?php } ?>

			<h4><?php esc_html_e( 'or', 'itsa-core-plugin' ); ?></h4>

			<a href="#" class="button add-conditional-logic-advanced-group"><?php esc_html_e( 'Add rule group', 'itsa-core-plugin' ); ?></a>

		</div>
	</td>
</tr>
