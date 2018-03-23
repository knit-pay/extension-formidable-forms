<?php

namespace Pronamic\WordPress\Pay\Extensions\FormidableForms;

use FrmAppHelper;
use Pronamic\WordPress\Pay\Core\Util;

/**
 * Title: WordPress pay Formidable Forms
 * Description:
 * Copyright: Copyright (c) 2005 - 2018
 * Company: Pronamic
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class FormidableForms {
	/**
	 * Version compare
	 *
	 * @param string $version  Version to compare with.
	 * @param string $operator Comparison operator.
	 *
	 * @return bool|mixed
	 */
	public static function version_compare( $version, $operator ) {
		$result = true;

		if ( class_exists( 'FrmAppHelper' ) && Util::class_method_exists( 'FrmAppHelper', 'plugin_version' ) ) {
			$result = version_compare( FrmAppHelper::plugin_version(), $version, $operator );
		}

		return $result;
	}
}
