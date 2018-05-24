<?php

namespace Pronamic\WordPress\Pay\Extensions\FormidableForms;

use Pronamic\WordPress\Pay\Core\PaymentMethods;
use Pronamic\WordPress\Pay\Plugin;
use Pronamic\WordPress\Pay\Util;

/**
 * Title: Formidable Forms bank select field type
 * Description:
 * Copyright: Copyright (c) 2005 - 2018
 * Company: Pronamic
 *
 * @author  Remco Tolsma
 * @version 2.0.0
 * @since   1.0.0
 */
class BankSelectFieldType {
	/**
	 * The unique ID of this field type.
	 *
	 * @var string
	 */
	const ID = 'pronamic_bank_select';

	/**
	 * Construct and initializes an Formidable Forms bank select field type.
	 *
	 * @see https://formidableforms.com/knowledgebase/add-a-new-field/
	 */
	public function __construct() {
		// @see https://github.com/wp-premium/formidable/blob/2.0.21/classes/models/FrmField.php#L10-L23
		add_filter( 'frm_available_fields', array( $this, 'available_fields' ) );

		// @see https://github.com/wp-premium/formidable/blob/2.0.21/classes/controllers/FrmFieldsController.php#L74
		add_filter( 'frm_before_field_created', array( $this, 'before_field_created' ) );

		// @see https://github.com/wp-premium/formidable/blob/2.0.21/classes/views/frm-fields/show-build.php#L64
		add_action( 'frm_display_added_fields', array( $this, 'display_added_fields' ) );

		// @see https://github.com/wp-premium/formidable/blob/2.0.21/classes/views/frm-fields/input.php#L171
		add_action( 'frm_form_fields', array( $this, 'form_fields' ) );
	}

	/**
	 * Available fields.
	 *
	 * @see    https://formidableforms.com/knowledgebase/add-a-new-field/
	 * @see    https://github.com/wp-premium/formidable/blob/2.0.21/classes/models/FrmField.php#L10-L23
	 *
	 * @param array $fields
	 *
	 * @return $fields
	 */
	public function available_fields( $fields ) {
		$fields[ self::ID ] = __( 'Banks', 'pronamic_ideal' );

		return $fields;
	}

	/**
	 * Before field created.
	 *
	 * @see https://formidableforms.com/knowledgebase/add-a-new-field/
	 * @see https://github.com/wp-premium/formidable/blob/2.0.21/classes/controllers/FrmFieldsController.php#L74
	 *
	 * @param array $field_data
	 *
	 * @return array
	 */
	public function before_field_created( $field_data ) {
		if ( self::ID === $field_data['type'] ) {
			$field_data['name'] = __( 'Choose a bank for iDEAL payment', 'pronamic_ideal' );
		}

		return $field_data;
	}

	/**
	 * Display added fields.
	 *
	 * @see https://github.com/wp-premium/formidable/blob/2.0.21/classes/views/frm-fields/show-build.php#L64
	 *
	 * @param array $field
	 */
	public function display_added_fields( $field ) {
		if ( self::ID === $field['type'] ) {
			$this->render_admin_field( $field );
		}
	}

	/**
	 * Render admin field.
	 *
	 * @param array $field
	 */
	private function render_admin_field( $field ) {
		$this->render_field( $field );
	}

	/**
	 * Form fields.
	 *
	 * @see https://formidableforms.com/knowledgebase/add-a-new-field/
	 * @see https://github.com/wp-premium/formidable/blob/2.0.21/classes/views/frm-fields/input.php#L171
	 *
	 * @param array $field
	 */
	public function form_fields( $field ) {
		if ( self::ID === $field['type'] ) {
			$this->render_field( $field );
		}
	}

	/**
	 * Render field.
	 *
	 * @param array $field
	 */
	private function render_field( $field ) {
		$config_id = get_option( 'pronamic_pay_config_id' );

		$gateway = Plugin::get_gateway( $config_id );

		if ( ! $gateway ) {
			return;
		}

		// Always use iDEAL payment method for issuer field
		$payment_method = $gateway->get_payment_method();

		$gateway->set_payment_method( PaymentMethods::IDEAL );

		$issuer_field = $gateway->get_issuer_field();

		$error = $gateway->get_error();

		if ( is_wp_error( $error ) ) {
			printf(
				'%s<br /><em>%s</em>',
				esc_html( Plugin::get_default_error_message() ),
				esc_html( $error->get_error_message() )
			);
		} elseif ( $issuer_field ) {
			$choices = $issuer_field['choices'];
			$options = Util::select_options_grouped( $choices );

			printf(
				'<select name="%s" id="%s">',
				esc_attr( sprintf( 'item_meta[%s]', $field['id'] ) ),
				esc_attr( sprintf( 'field_%s', $field['field_key'] ) )
			);

			echo $options; // WPCS: xss ok.

			echo '</select>';
		}

		// Reset payment method to original value
		$gateway->set_payment_method( $payment_method );
	}
}
