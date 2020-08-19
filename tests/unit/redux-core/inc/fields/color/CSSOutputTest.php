<?php
// file tests/unit/EmailValidatorTest.php
use Redux_Core;

class EmailValidatorTest extends Codeception\Test\Test {
	public function test_good_email_validation() {
		$core = Redux_Core::instance();
		$field_class = 'Redux_Color';
		$field_object = new $field_class( array(), array(), $core );

		if ( method_exists( $field_class, 'css_style' ) ) {
			$style_data = $field_object->css_style( $field_object->value );
		}


		//$this->assertTrue( $color->validate( 'not-an-email' ) );

	}​

}
