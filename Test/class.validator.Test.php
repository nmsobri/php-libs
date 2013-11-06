<?php

include 'class.validator.php';
include 'validator/validator.strategy.php';
include 'validator/checkbox.validator.php';
include 'validator/compare.validator.php';
include 'validator/date.validator.php';
include 'validator/email.validator.php';
include 'validator/error.validator.php';
include 'validator/file.validator.php';
include 'validator/number.validator.php';
include 'validator/radio.validator.php';
include 'validator/regex.validator.php';
include 'validator/select.validator.php';
include 'validator/text.validator.php';

class ValidatorTest extends PHPUnit_Framework_TestCase
{

    public function invokeInternalMethod( &$object, $methodName, array $parameters = array() )
    {
        $reflection = new ReflectionClass( get_class( $object ) );
        $method = $reflection->getMethod( $methodName );
        $method->setAccessible( true );
        return $method->invokeArgs( $object, $parameters );
    }


    public function setInternalProperty( &$object, $property_name, $property_value )
    {
        $reflection = new ReflectionClass( get_class( $object ) );
        $property = $reflection->getProperty( $property_name );
        $property->setAccessible( true );
        $property->setValue( $object, $property_value );
    }


    public function getInternalProperty( $object, $property_name )
    {
        return PHPUnit_Framework_Assert::readAttribute( $object, $property_name );
    }


    ###########################################################################################################
    ######################################### ValidatorStrategy ###############################################
    ###########################################################################################################

    public function testGetMessageReturnsExpectedValue()
    {
        $mockValidatorStrategy = $this->getMockForAbstractClass( 'ValidatorStrategy' );
        $result = $mockValidatorStrategy->getMessage();
        $this->assertTrue( is_null( $result ) );
    }


    public function testGetMessageWithPredefinedValueReturnsExpectedValue()
    {
        $mockValidatorStrategy = $this->getMockForAbstractClass( 'ValidatorStrategy' );
        $this->setInternalProperty( $mockValidatorStrategy, 'messages', 'hi sup' );
        $result = $mockValidatorStrategy->getMessage();
        $expected_value = 'hi sup';
        $this->assertEquals( $expected_value, $result );
    }


    ###########################################################################################################
    ######################################### CheckBoxValidator ###############################################
    ###########################################################################################################

    /**
     * @param $name
     * @param $value
     * @param $attr
     * @dataProvider dataProvidertestCheckBoxValidatorConstructorExptectedToSetupPropertyProperly
     */
    public function testCheckBoxValidatorConstructorExptectedToSetupPropertyProperly( $name, $value, $attr, $expected_value )
    {
        $checkbox_validator = new CheckBoxValidator( $name, $value, $attr );
        $checkbox_internal_property = $this->getInternalProperty( $checkbox_validator, 'data' );

        $this->assertEquals( $expected_value['value'], $checkbox_internal_property['value'] );
        $this->assertEquals( $expected_value['errors'], $checkbox_internal_property['errors'] );
        $this->assertEquals( $expected_value['required'], $checkbox_internal_property['required'] );
        $this->assertEquals( $expected_value['field'], $checkbox_internal_property['field'] );

    }


    /**
     * @param $name
     * @param $value
     * @param $attr
     * @param $expected_value
     * @dataProvider dataProvidertestCheckboxValidatorIsValidReturnsExpectedResult
     */
    public function testCheckboxValidatorIsValidReturnsExpectedResult( $name, $value, $attr, $expected_value )
    {
        $checkbox_validator = new CheckBoxValidator( $name, $value, $attr );
        $result = $checkbox_validator->isValid();
        $this->assertEquals( $expected_value, $result );
    }


    /**
     * @param $name
     * @param $value
     * @param $attr
     * @param $expected_value
     * @dataProvider dataProvidertestCheckboxValidatorIsValidWhenFalseReturnExpectedErrorMessage
     */
    public function testCheckboxValidatorIsValidWhenFalseReturnExpectedErrorMessage( $name, $value, $attr, $expected_value )
    {
        $checkbox_validator = new CheckBoxValidator( $name, $value, $attr );
        $checkbox_validator->isValid();
        $result = $this->getInternalProperty( $checkbox_validator, 'messages' );
        $this->assertEquals( $expected_value, $result );
    }


    ###########################################################################################################
    ######################################### CompareValidator ################################################
    ###########################################################################################################
    /**
     * @param $name
     * @param $value
     * @param $attr
     * @dataProvider dataProvidertestCompareValidatorExptectedToSetupPropertyProperly
     */
    public function testCompareValidatorExptectedToSetupPropertyProperly( $name, $value, $comparison_value, $comparison_field, $attr, $expected_value )
    {
        $compare_validator = new CompareValidator( $name, $value, $comparison_value, $comparison_field, $attr );
        $compare_internal_property = $this->getInternalProperty( $compare_validator, 'data' );
        $this->assertEquals( $expected_value['to_compare'], $compare_internal_property['value'] );
        $this->assertEquals( $expected_value['compare_with'], $compare_internal_property['compare_value'] );
        $this->assertEquals( $expected_value['field_comparison'], $compare_internal_property['compare_field'] );
        $this->assertEquals( $expected_value['required'], $compare_internal_property['required'] );
        $this->assertEquals( $expected_value['errors']['empty'], $compare_internal_property['errors']['empty'] );
        $this->assertEquals( $expected_value['errors']['equal'], $compare_internal_property['errors']['equal'] );
        $this->assertEquals( $expected_value['field'], $compare_internal_property['field'] );

    }


    /**
     * @param $name
     * @param $value
     * @param $comparison_value
     * @param $comparison_field
     * @param $attr
     * @param $expected_value
     * @dataProvider dataProvidertestCompareValidatorIsValidReturnsExpectedResult
     */
    public function testCompareValidatorIsValidReturnsExpectedResult( $name, $value, $comparison_value, $comparison_field, $expected_value )
    {
        $compare_validator = new CompareValidator( $name, $value, $comparison_value, $comparison_field );
        $result = $compare_validator->isValid();
        $this->assertEquals( $expected_value, $result );
    }


    /**
     * @param $name
     * @param $value
     * @param $attr
     * @param $expected_value
     * @dataProvider dataProvidertestCompareValidatorIsValidWhenFalseReturnExpectedErrorMessage
     */
    public function testCompareValidatorIsValidWhenFalseReturnExpectedErrorMessage( $name, $value, $comparison_value, $comparison_field, $attr, $expected_value )
    {
        $compare_validator = new CompareValidator( $name, $value, $comparison_value, $comparison_field, $attr );
        $compare_validator->isValid();
        $result = $this->getInternalProperty( $compare_validator, 'messages' );
        $this->assertEquals( $expected_value, $result );
    }


    ###########################################################################################################
    ############################################ DateValidator ################################################
    ###########################################################################################################

    /**
     * @param $name
     * @param $value
     * @param $attr
     * @dataProvider dataProvidertestDateValidatorExptectedToSetupPropertyProperly
     */
    public function testDateValidatorExptectedToSetupPropertyProperly( $name, $value, $attr, $expected_value )
    {
        $date_validator = new DateValidator( $name, $value, $attr );
        $compare_internal_property = $this->getInternalProperty( $date_validator, 'data' );

        $this->assertEquals( $expected_value['value'], $compare_internal_property['value'] );
        $this->assertEquals( $expected_value['version'], $compare_internal_property['version'] );
        $this->assertEquals( $expected_value['required'], $compare_internal_property['required'] );
        $this->assertEquals( $expected_value['errors'], $compare_internal_property['errors'] );
        $this->assertEquals( $expected_value['field'], $compare_internal_property['field'] );

    }


    /**
     * @param $name
     * @param $value
     * @param $comparison_value
     * @param $comparison_field
     * @param $attr
     * @param $expected_value
     * @dataProvider dataProvidertestDateValidatorIsValidReturnsExpectedResult
     */
    public function testDateValidatorIsValidReturnsExpectedResult( $name, $value, $attr, $expected_value )
    {
        $date_validator = new DateValidator( $name, $value, $attr );
        $result = $date_validator->isValid();
        $this->assertEquals( $expected_value, $result );
    }


    /**
     * @param $name
     * @param $value
     * @param $attr
     * @param $expected_value
     * @dataProvider dataProvidertestDateValidatorIsValidWhenFalseReturnExpectedErrorMessage
     */
    public function testDateValidatorIsValidWhenFalseReturnExpectedErrorMessage( $name, $value, $attr, $expected_value )
    {
        $date_validator = new DateValidator( $name, $value, $attr );
        $date_validator->isValid();
        $result = $this->getInternalProperty( $date_validator, 'messages' );
        $this->assertEquals( $expected_value, $result );
    }


    ###########################################################################################################
    ############################################ EmailValidator ###############################################
    ###########################################################################################################

    /**
     * @param $name
     * @param $value
     * @param $attr
     * @dataProvider dataProvidertestEmailValidatorExptectedToSetupPropertyProperly
     */
    public function testEmailValidatorExptectedToSetupPropertyProperly( $name, $value, $attr, $expected_value )
    {
        $email_validator = new EmailValidator( $name, $value, $attr );
        $compare_internal_property = $this->getInternalProperty( $email_validator, 'data' );

        $this->assertEquals( $expected_value['value'], $compare_internal_property['value'] );
        $this->assertEquals( $expected_value['required'], $compare_internal_property['required'] );
        $this->assertEquals( $expected_value['errors'], $compare_internal_property['errors'] );
        $this->assertEquals( $expected_value['field'], $compare_internal_property['field'] );

    }


    /**
     * @param $name
     * @param $value
     * @param $comparison_value
     * @param $comparison_field
     * @param $attr
     * @param $expected_value
     * @dataProvider dataProvidertestEmailValidatorIsValidReturnsExpectedResult
     */
    public function testEmailValidatorIsValidReturnsExpectedResult( $name, $value, $attr, $expected_value )
    {
        $email_validator = new EmailValidator( $name, $value, $attr );
        $result = $email_validator->isValid();
        $this->assertEquals( $expected_value, $result );
    }


    /**
     * @param $name
     * @param $value
     * @param $attr
     * @param $expected_value
     * @dataProvider dataProvidertestEmailValidatorIsValidWhenFalseReturnExpectedErrorMessage
     */
    public function testEmailValidatorIsValidWhenFalseReturnExpectedErrorMessage( $name, $value, $attr, $expected_value )
    {
        $email_validator = new EmailValidator( $name, $value, $attr );
        $email_validator->isValid();
        $result = $this->getInternalProperty( $email_validator, 'messages' );
        $this->assertEquals( $expected_value, $result );
    }


    ###########################################################################################################
    ############################################ NumberValidator ##############################################
    ###########################################################################################################

    /**
     * @param $name
     * @param $value
     * @param $attr
     * @dataProvider dataProvidertestNumberValidatorExptectedToSetupPropertyProperly
     */
    public function testNumberValidatorExptectedToSetupPropertyProperly( $name, $value, $attr, $expected_value )
    {
        $number_validator = new NumberValidator( $name, $value, $attr );
        $compare_internal_property = $this->getInternalProperty( $number_validator, 'data' );

        $this->assertEquals( $expected_value['value'], $compare_internal_property['value'] );
        $this->assertEquals( $expected_value['required'], $compare_internal_property['required'] );
        $this->assertEquals( $expected_value['decimal'], $compare_internal_property['decimal'] );
        $this->assertEquals( $expected_value['min_length'], $compare_internal_property['min_length'] );
        $this->assertEquals( $expected_value['max_length'], $compare_internal_property['max_length'] );
        $this->assertEquals( $expected_value['errors'], $compare_internal_property['errors'] );
        $this->assertEquals( $expected_value['field'], $compare_internal_property['field'] );

    }


    /**
     * @param $name
     * @param $value
     * @param $comparison_value
     * @param $comparison_field
     * @param $attr
     * @param $expected_value
     * @dataProvider dataProvidertestNumberValidatorIsValidReturnsExpectedResult
     */
    public function testNumberValidatorIsValidReturnsExpectedResult( $name, $value, $attr, $expected_value )
    {
        $number_validator = new NumberValidator( $name, $value, $attr );
        $result = $number_validator->isValid();
        $this->assertEquals( $expected_value, $result );
    }


    /**
     * @param $name
     * @param $value
     * @param $attr
     * @param $expected_value
     * @dataProvider dataProvidertestNumberValidatorIsValidWhenFalseReturnExpectedErrorMessage
     */
    public function testNumberValidatorIsValidWhenFalseReturnExpectedErrorMessage( $name, $value, $attr, $expected_value )
    {
        $number_validator = new NumberValidator( $name, $value, $attr );
        $number_validator->isValid();
        $result = $this->getInternalProperty( $number_validator, 'messages' );
        $this->assertEquals( $expected_value, $result );
    }


    ###########################################################################################################
    ############################################ RadioValidator ###############################################
    ###########################################################################################################

    /**
     * @param $name
     * @param $value
     * @param $attr
     * @dataProvider dataProvidertestRadioValidatorExptectedToSetupPropertyProperly
     */
    public function testRadioValidatorExptectedToSetupPropertyProperly( $name, $value, $attr, $expected_value )
    {
        $radio_validator = new RadioValidator( $name, $value, $attr );
        $compare_internal_property = $this->getInternalProperty( $radio_validator, 'data' );

        $this->assertEquals( $expected_value['value'], $compare_internal_property['value'] );
        $this->assertEquals( $expected_value['required'], $compare_internal_property['required'] );
        $this->assertEquals( $expected_value['errors'], $compare_internal_property['errors'] );
        $this->assertEquals( $expected_value['field'], $compare_internal_property['field'] );

    }


    /**
     * @param $name
     * @param $value
     * @param $comparison_value
     * @param $comparison_field
     * @param $attr
     * @param $expected_value
     * @dataProvider dataProvidertestRadioValidatorIsValidReturnsExpectedResult
     */
    public function testRadioValidatorIsValidReturnsExpectedResult( $name, $value, $attr, $expected_value )
    {
        $radio_validator = new RadioValidator( $name, $value, $attr );
        $result = $radio_validator->isValid();
        $this->assertEquals( $expected_value, $result );
    }


    /**
     * @param $name
     * @param $value
     * @param $attr
     * @param $expected_value
     * @dataProvider dataProvidertestRadioValidatorIsValidWhenFalseReturnExpectedErrorMessage
     */
    public function testRadioValidatorIsValidWhenFalseReturnExpectedErrorMessage( $name, $value, $attr, $expected_value )
    {
        $radio_validator = new RadioValidator( $name, $value, $attr );
        $radio_validator->isValid();
        $result = $this->getInternalProperty( $radio_validator, 'messages' );
        $this->assertEquals( $expected_value, $result );
    }


    ###########################################################################################################
    ############################################ RegexValidator ###############################################
    ###########################################################################################################

    /**
     * @param $name
     * @param $value
     * @param $attr
     * @dataProvider dataProvidertestRegexValidatorExptectedToSetupPropertyProperly
     */
    public function testRegexValidatorExptectedToSetupPropertyProperly( $name, $value, $regex, $attr, $expected_value )
    {
        $regex_validator = new RegexValidator( $name, $value, $regex, $attr );
        $compare_internal_property = $this->getInternalProperty( $regex_validator, 'data' );

        $this->assertEquals( $expected_value['value'], $compare_internal_property['value'] );
        $this->assertEquals( $expected_value['regex'], $compare_internal_property['regex'] );
        $this->assertEquals( $expected_value['required'], $compare_internal_property['required'] );
        $this->assertEquals( $expected_value['errors'], $compare_internal_property['errors'] );
        $this->assertEquals( $expected_value['field'], $compare_internal_property['field'] );

    }


    /**
     * @param $name
     * @param $value
     * @param $comparison_value
     * @param $comparison_field
     * @param $attr
     * @param $expected_value
     * @dataProvider dataProvidertestRegexValidatorIsValidReturnsExpectedResult
     */
    public function testRegexValidatorIsValidReturnsExpectedResult( $name, $value, $regex, $attr, $expected_value )
    {
        $regex_validator = new RegexValidator( $name, $value, $regex, $attr );
        $result = $regex_validator->isValid();
        $this->assertEquals( $expected_value, $result );
    }


    /**
     * @param $name
     * @param $value
     * @param $attr
     * @param $expected_value
     * @dataProvider dataProvidertestRegexValidatorIsValidWhenFalseReturnExpectedErrorMessage
     */
    public function testRegexValidatorIsValidWhenFalseReturnExpectedErrorMessage( $name, $value, $regex, $attr, $expected_value )
    {
        $regex_validator = new RegexValidator( $name, $value, $regex, $attr );
        $regex_validator->isValid();
        $result = $this->getInternalProperty( $regex_validator, 'messages' );
        $this->assertEquals( $expected_value, $result );
    }


    ###########################################################################################################
    ############################################ FileValidator ################################################
    ###########################################################################################################

    /**
     * @param $name
     * @param $value
     * @param $attr
     * @dataProvider dataProvidertestFileValidatorExptectedToSetupPropertyProperly
     */
    public function testFileValidatorExptectedToSetupPropertyProperly( $name, $file, $ext, $attr, $expected_value )
    {
        $file_validator = new FileValidator( $name, $file, $ext, $attr );
        $compare_internal_property = $this->getInternalProperty( $file_validator, 'data' );

        $this->assertEquals( $expected_value['value'], $compare_internal_property['value'] );
        $this->assertEquals( $expected_value['extension'], $compare_internal_property['extension'] );
        $this->assertEquals( $expected_value['required'], $compare_internal_property['required'] );
        $this->assertEquals( $expected_value['errors'], $compare_internal_property['errors'] );
        $this->assertEquals( $expected_value['field'], $compare_internal_property['field'] );

    }


    /**
     * @param $name
     * @param $value
     * @param $comparison_value
     * @param $comparison_field
     * @param $attr
     * @param $expected_value
     * @dataProvider dataProvidertestFileValidatorIsValidReturnsExpectedResult
     */
    public function testFileValidatorIsValidReturnsExpectedResult( $name, $file, $ext, $attr, $expected_value )
    {
        $file_validator = new FileValidator( $name, $file, $ext, $attr );
        $result = $file_validator->isValid();
        $this->assertEquals( $expected_value, $result );
    }


    /**
     * @param $name
     * @param $value
     * @param $attr
     * @param $expected_value
     * @dataProvider dataProvidertestFileValidatorIsValidWhenFalseReturnExpectedErrorMessage
     */
    public function testFileValidatorIsValidWhenFalseReturnExpectedErrorMessage( $name, $file, $ext, $attr, $expected_value )
    {
        $file_validator = new FileValidator( $name, $file, $ext, $attr );
        $file_validator->isValid();
        $result = $this->getInternalProperty( $file_validator, 'messages' );
        $this->assertEquals( $expected_value, $result );
    }


    ###########################################################################################################
    ############################################ SelectValidator ##############################################
    ###########################################################################################################

    /**
     * @param $name
     * @param $value
     * @param $attr
     * @dataProvider dataProvidertestSelectValidatorExptectedToSetupPropertyProperly
     */
    public function testSelectValidatorExptectedToSetupPropertyProperly( $name, $value, $attr, $expected_value )
    {
        $select_validator = new SelectValidator( $name, $value, $attr );
        $compare_internal_property = $this->getInternalProperty( $select_validator, 'data' );

        $this->assertEquals( $expected_value['value'], $compare_internal_property['value'] );
        $this->assertEquals( $expected_value['required'], $compare_internal_property['required'] );
        $this->assertEquals( $expected_value['errors'], $compare_internal_property['errors'] );
        $this->assertEquals( $expected_value['field'], $compare_internal_property['field'] );

    }


    /**
     * @param $name
     * @param $value
     * @param $comparison_value
     * @param $comparison_field
     * @param $attr
     * @param $expected_value
     * @dataProvider dataProvidertestSelectValidatorIsValidReturnsExpectedResult
     */
    public function testSelectValidatorIsValidReturnsExpectedResult( $name, $value, $attr, $expected_value )
    {
        $select_validator = new SelectValidator( $name, $value, $attr );
        $result = $select_validator->isValid();
        $this->assertEquals( $expected_value, $result );
    }


    /**
     * @param $name
     * @param $value
     * @param $attr
     * @param $expected_value
     * @dataProvider dataProvidertestSelectValidatorIsValidWhenFalseReturnExpectedErrorMessage
     */
    public function testSelectValidatorIsValidWhenFalseReturnExpectedErrorMessage( $name, $value, $attr, $expected_value )
    {
        $select_validator = new SelectValidator( $name, $value, $attr );
        $select_validator->isValid();
        $result = $this->getInternalProperty( $select_validator, 'messages' );
        $this->assertEquals( $expected_value, $result );
    }


    ###########################################################################################################
    ############################################ TextValidator ################################################
    ###########################################################################################################


    /**
     * @param $name
     * @param $value
     * @param $attr
     * @dataProvider dataProvidertestTextValidatorExptectedToSetupPropertyProperly
     */
    public function testTextValidatorExptectedToSetupPropertyProperly( $name, $value, $attr, $expected_value )
    {
        $text_validator = new TextValidator( $name, $value, $attr );
        $compare_internal_property = $this->getInternalProperty( $text_validator, 'data' );

        $this->assertEquals( $expected_value['value'], $compare_internal_property['value'] );
        $this->assertEquals( $expected_value['required'], $compare_internal_property['required'] );
        $this->assertEquals( $expected_value['allow_num'], $compare_internal_property['allow_num'] );
        $this->assertEquals( $expected_value['allow_space'], $compare_internal_property['allow_space'] );
        $this->assertEquals( $expected_value['min_length'], $compare_internal_property['min_length'] );
        $this->assertEquals( $expected_value['max_length'], $compare_internal_property['max_length'] );
        $this->assertEquals( $expected_value['errors'], $compare_internal_property['errors'] );
        $this->assertEquals( $expected_value['field'], $compare_internal_property['field'] );

    }


    /**
     * @param $name
     * @param $value
     * @param $comparison_value
     * @param $comparison_field
     * @param $attr
     * @param $expected_value
     * @dataProvider dataProvidertestTextValidatorIsValidReturnsExpectedResult
     */
    public function testTextValidatorIsValidReturnsExpectedResult( $name, $value, $attr, $expected_value )
    {
        $text_validator = new TextValidator( $name, $value, $attr );
        $result = $text_validator->isValid();
        $this->assertEquals( $expected_value, $result );
    }


    /**
     * @param $name
     * @param $value
     * @param $attr
     * @param $expected_value
     * @dataProvider dataProvidertestTextValidatorIsValidWhenFalseReturnExpectedErrorMessage
     */
    public function testTextValidatorIsValidWhenFalseReturnExpectedErrorMessage( $name, $value, $attr, $expected_value )
    {
        $text_validator = new TextValidator( $name, $value, $attr );
        $text_validator->isValid();
        $result = $this->getInternalProperty( $text_validator, 'messages' );
        $this->assertEquals( $expected_value, $result );
    }


    ###########################################################################################################
    ############################################ ErrorValidator ###############################################
    ###########################################################################################################


    public function testErrorValidatorExptectedToSetupPropertyProperly()
    {
        $msg = 'validation failed';
        $error_validator = new ErrorValidator( $msg );
        $compare_internal_property = $this->getInternalProperty( $error_validator, 'data' );
        $this->assertEquals( $msg, $compare_internal_property['errors']['error'] );


    }


    public function testErrorValidatorIsValidReturnsExpectedResult()
    {
        $error_validator = new ErrorValidator( 'validation failed' );
        $result = $error_validator->isValid();
        $this->assertEquals( false, $result );
    }


    public function testErrorValidatorIsValidWhenFalseReturnExpectedErrorMessage()
    {
        $error_validator = new ErrorValidator( 'validation failed' );
        $error_validator->isValid();
        $result = $this->getInternalProperty( $error_validator, 'messages' );
        $this->assertEquals( 'validation failed', $result );
    }


    ###########################################################################################################
    ############################################### Validator #################################################
    ###########################################################################################################


    public function testAddValidatorReturnsExpectedObject()
    {


        $text_validator = new TextValidator( 'username', 'slier' );
        $validator = new Validator();
        $validator->addValidator( 'text', $text_validator );
        $validator_internal_property = $this->getInternalProperty( $validator, 'validators' );
        $this->assertEquals( $text_validator, $validator_internal_property['text'] );
    }


    /**
     * @param $validators
     * @param $expected_value
     * @dataProvider dataProvidertestIsValidReturnsExpectedResult
     */
    public function testIsValidReturnsExpectedResult( $validators, $expected_value )
    {
        $validator = new Validator;

        foreach( $validators as $key => $vdtr ){

            $validator->addValidator( $key, $vdtr );
        }

        $result = $validator->isValid();
        $this->assertEquals( $expected_value, $result );
    }


    public function testInvalidateFieldReturnsExpectedResult()
    {

        $validator = new Validator();
        $text_validator = new TextValidator( 'user', 'slier' );
        $validator->addValidator( 'user', $text_validator );
        $validator->invalidateField( 'user', 'account is not active' );
        $validator_internal_property = $this->getInternalProperty( $validator, 'isError' );
        $this->assertEquals( true, $validator_internal_property );
    }


    public function testInvalidateFieldSetExpectedMessage()
    {

        $validator = new Validator();
        $text_validator = new TextValidator( 'user', 'slier' );
        $validator->addValidator( 'user', $text_validator );
        $validator->invalidateField( 'user', 'account is not active' );
        $validator_internal_property = $this->getInternalProperty( $validator, 'validators' );
        $result = $this->getInternalProperty( $validator_internal_property['user'], 'messages' );
        $this->assertEquals( 'account is not active', $result );
    }


    public function testInvalidateValiationReturnsExpectedResult()
    {

        $validator = new Validator();
        $validator->invalidateValidation( 'error' );
        $result = $this->getInternalProperty( $validator, 'isError' );
        $this->assertEquals( true, $result );
    }


    /**
     * @param callable $func
     * @param $xpected_result
     * @dataProvider dataProvidertestIsErrorReturnsExpectedResult
     */
    public function testIsErrorReturnsExpectedResult( callable $func, $expected_result )
    {
        $result = $func();
        $this->assertEquals( $expected_result, $result );
    }


    /**
     * @param $func
     * @param $expected_result
     * @dataProvider dataProvidertestGetErrorReturnsExpectedErorMsg
     */
    public function testGetErrorReturnsExpectedErorMsg( callable $func, $expected_result )
    {

        $result = $func();
        $this->assertEquals( $expected_result, $result );
    }


    /**
     * @param $expected_value
     * @dataProvider dataProvidertestGetAllErrorReturnsExptectedValue
     */
    public function testGetAllErrorReturnsExptectedValue( $validators, $expected_value )
    {
        $validator = new Validator();

        foreach( $validators as $key => $vdtr ){
            $validator->addValidator( $key, $vdtr );
        }

        $validator->isValid();
        $result = $validator->getAllError();
        $this->assertEquals( $expected_value, $result );
    }


    /**
     * @param $validators
     * @param $expected_value
     * @dataProvider dataProvidertestShowErrorReturnsExpectedValue
     */
    public function testShowErrorReturnsExpectedValue( $validators, $expected_value )
    {

        $validator = new Validator();
        foreach( $validators as $key => $vdtr ){
            $validator->addValidator( $key, $vdtr );
        }
        $validator->isValid();
        $result = $validator->showError();
        $this->assertEquals( $expected_value, $result );
    }


    ###########################################################################################################
    ############################################ dataProvider #################################################
    ###########################################################################################################

    public function dataProvidertestCheckBoxValidatorConstructorExptectedToSetupPropertyProperly()
    {
        return array(

            array( 'subscribe', 'yes', null, array( 'value' => 'yes', 'errors' => array(
                'empty' => null
            ), 'required' => true, 'field' => 'subscribe' ) ),
            array( 'subscribe', 'yes', array( 'field' => 'newsletter' ), array( 'value' => 'yes', 'errors' => array(
                'empty' => null
            ), 'required' => true, 'field' => 'newsletter' ) ),
            array( 'subscribe', 'yes', array( 'required' => false ), array( 'value' => 'yes', 'errors' => array(
                'empty' => null
            ), 'required' => false, 'field' => 'subscribe' ) ),
            array( 'subscribe', 'yes', array( 'errors' => array( 'empty' => 'hai sup' ) ), array( 'value' => 'yes', 'errors' => array( 'empty' => 'hai sup' ), 'required' => true, 'field' => 'subscribe' ) )

        );
    }


    public function dataProvidertestCheckboxValidatorIsValidReturnsExpectedResult()
    {
        return array(

            array( 'subscribe', 'yes', array(), true ),
            array( 'subscribe', null, array(), false ),
            array( 'subscribe', null, array( 'required' => false ), true )

        );
    }


    public function dataProvidertestCheckboxValidatorIsValidWhenFalseReturnExpectedErrorMessage()
    {

        return array(

            array( 'subscribe', null, array(), 'Checkbox subscribe is not checked' ),
            array( 'subscribe', null, array( 'errors' => array( 'empty' => 'this is message' ) ), 'this is message' )

        );
    }


    public function dataProvidertestCompareValidatorExptectedToSetupPropertyProperly()
    {

        return array(


            array( 'repeat_password', 123, 123, 'password', array(), array( 'to_compare' => '123', 'compare_with' => '123', 'field_comparison' => 'password', 'required' => true, 'errors' => array( 'empty' => null, 'equal' => null ), 'field' => 'repeat_password' ) ),
            array( 'repeat_password', 123, 123, 'password', array( 'field' => 'field' ), array( 'to_compare' => '123', 'compare_with' => '123', 'field_comparison' => 'password', 'required' => true, 'errors' => array( 'empty' => null, 'equal' => null ), 'field' => 'field' ) ),
            array( 'repeat_password', 123, 123, 'password', array( 'unmatch_message' => 'this is an error' ), array( 'to_compare' => '123', 'compare_with' => '123', 'field_comparison' => 'password', 'required' => true, 'errors' => array( 'empty' => null, 'equal' => null ), 'field' => 'repeat_password' ) ),
            array( 'repeat_password', 123, 123, 'password', array( 'empty_message' => 'this is an error' ), array( 'to_compare' => '123', 'compare_with' => '123', 'field_comparison' => 'password', 'required' => true, 'errors' => array( 'empty' => null, 'equal' => null ), 'field' => 'repeat_password' ) ),
            array( 'repeat_password', 123, 123, 'password', array( 'required' => false ), array( 'to_compare' => '123', 'compare_with' => '123', 'field_comparison' => 'password', 'required' => false, 'errors' => array( 'empty' => null, 'equal' => null ), 'field' => 'repeat_password' ) )

        );

    }


    public function dataProvidertestCompareValidatorIsValidReturnsExpectedResult()
    {


        return array(


            array( 'repeat_password', 123, 123, 'password', true ),
            array( 'repeat_password', 123, 123, 'password', true ),
            array( 'repeat_password', 123, 1234, 'password', false )

        );
    }


    public function dataProvidertestCompareValidatorIsValidWhenFalseReturnExpectedErrorMessage()
    {


        return array(

            array( 'repeat_password', null, 321, 'password', array( 'required' => false ), '' ),
            array( 'repeat_password', null, 321, 'password', array(), 'Field repeat_password is empty' ),
            array( 'repeat_password', 123, 321, 'password', array( 'errors' => array( 'equal' => 'field x sama' ) ), 'field x sama' ),
            array( 'repeat_password', 123, 321, 'password', array(), 'Field repeat_password does not match field password' ),
            array( 'repeat_password', null, 321, 'password', array( 'errors' => array( 'empty' => 'field kosong' ) ), 'field kosong' )
        );
    }


    public function dataProvidertestDateValidatorExptectedToSetupPropertyProperly()
    {

        return array(


            array( 'dob', '2010-02-29', array(), array( 'value' => '2010-02-29', 'version' => 'us', 'required' => true, 'errors' => array(
                'empty' => null, 'date' => null
            ), 'field' => 'dob' ) ),
            array( 'dob', '2010-02-29', array( 'field' => 'lol' ), array( 'value' => '2010-02-29', 'version' => 'us', 'required' => true, 'errors' => array(
                'empty' => null, 'date' => null
            ), 'field' => 'lol' ) ),
            array( 'dob', '2010-02-29', array( 'message' => 'hi there' ), array( 'value' => '2010-02-29', 'version' => 'us', 'required' => true, 'errors' => array(
                'empty' => null, 'date' => null
            ), 'field' => 'dob' ) ),
            array( 'dob', '2010-02-29', array( 'required' => false ), array( 'value' => '2010-02-29', 'version' => 'us', 'required' => false, 'errors' => array(
                'empty' => null, 'date' => null
            ), 'field' => 'dob' ) ),
            array( 'dob', '2010-02-29', array( 'version' => 'en' ), array( 'value' => '2010-02-29', 'version' => 'en', 'required' => true, 'errors' => array(
                'empty' => null, 'date' => null
            ), 'field' => 'dob' ) )

        );
    }


    public function dataProvidertestDateValidatorIsValidReturnsExpectedResult()
    {

        return array(

            array( 'dob', null, array(), false ),
            array( 'dob', null, array( 'required' => false ), true ),
            array( 'dob', '00-02-2010', array( 'version' => 'eu' ), false ),
            array( 'dob', '2010-02-29', array( 'version' => 'en' ), true ),
            array( 'dob', '2010-02-00', array( 'version' => 'en' ), false ),
            array( 'dob', '29-02-2010', array( 'version' => 'eu' ), true )

        );

    }


    public function dataProvidertestDateValidatorIsValidWhenFalseReturnExpectedErrorMessage()
    {

        return array(

            array( 'dob', null, array(), 'Field dob is empty' ),
            array( 'dob', '2010-02-29', array( 'version' => 'eu' ), 'Field dob contains invalid date.Only date formatted as DD-MM-YY are allowed' ),
            array( 'dob', '2010-02-29', array( 'version' => 'eu', 'errors' => array( 'date' => 'invalid' ) ), 'invalid' ),
            array( 'dob', '29-02-2010', array(), 'Field dob contains invalid date.Only date formatted as YY-MM-DD are allowed' ),
            array( 'dob', '29-02-2010', array( 'errors' => array( 'date' => 'invalid' ) ), 'invalid' ),
            array( 'dob', null, array( 'errors' => array( 'empty' => 'failed' ) ), 'failed' )

        );
    }


    public function dataProvidertestEmailValidatorExptectedToSetupPropertyProperly()
    {
        return array(

            array( 'email', 'slier81@gmail.com', array(), array( 'value' => 'slier81@gmail.com', 'required' => true, 'errors' => array(
                'empty' => null, 'email' => null
            ), 'field' => 'email' ) ),
            array( 'email', 'slier81@gmail.com', array( 'field' => 'field_name' ), array( 'value' => 'slier81@gmail.com', 'required' => true, 'errors' => array(
                'empty' => null, 'email' => null
            ), 'field' => 'field_name' ) ),
            array( 'email', 'slier81@gmail.com', array( 'errors' => array( 'empty' => 'error' ) ), array( 'value' => 'slier81@gmail.com', 'required' => true, 'errors' => array( 'empty' => 'error', 'email' => null ), 'field' => 'email' ) ),
            array( 'email', 'slier81@gmail.com', array( 'required' => 'gaj' ), array( 'value' => 'slier81@gmail.com', 'required' => true, 'errors' => array(
                'empty' => null, 'email' => null
            ), 'field' => 'email' ) ),
            array( 'email', 'slier81@gmail.com', array( 'required' => false ), array( 'value' => 'slier81@gmail.com', 'required' => false, 'errors' => array(
                'empty' => null, 'email' => null
            ), 'field' => 'email' ) )

        );
    }


    public function dataProvidertestEmailValidatorIsValidReturnsExpectedResult()
    {
        return array(

            array( 'email', null, array(), false ),
            array( 'email', 'slier81@gmail.com', array(), true ),
            array( 'email', 'dsfadsfdasfad', array( 'required' => false ), false ),
            array( 'email', 'dsfadsfdasfad', array( 'required' => true ), false ),
            array( 'email', 'sfdasfds', array(), false ),
            array( 'email', null, array( 'required' => false ), true )

        );
    }


    public function dataProvidertestEmailValidatorIsValidWhenFalseReturnExpectedErrorMessage()
    {

        return array(


            array( 'email', null, array(), 'Field email is empty' ),
            array( 'email', 'dsfasdfads', array(), 'Field email contains invalid email' ),
            array( 'email', 'dsfasdfads', array( 'errors' => array( 'email' => 'error' ) ), 'error' ),
            array( 'email', 'dsfasdfads', array( 'errors' => array( 'email' => 'error' ) ), 'error' ),
            array( 'email', null, array( 'errors' => array( 'empty' => 'error' ) ), 'error' )
        );

    }


    public function dataProvidertestNumberValidatorExptectedToSetupPropertyProperly()
    {

        return array(

            array( 'position', 123, array(), array( 'value' => '123', 'required' => true, 'decimal' => 0, 'min_length' => 0, 'max_length' => 0, 'errors' => array(
                'empty' => null, 'number' => null, 'number_decimal' => null,
                'number_fixed' => null, 'number_range' => null, 'number_decimal_fixed' => null,
                'number_decimal_range' => null
            ), 'field' => 'position' ) ),
            array( 'position', 123, array( 'field' => 'field_name' ), array( 'value' => '123', 'required' => true, 'decimal' => 0, 'min_length' => 0, 'max_length' => 0, 'errors' => array(
                'empty' => null, 'number' => null, 'number_decimal' => null,
                'number_fixed' => null, 'number_range' => null, 'number_decimal_fixed' => null,
                'number_decimal_range' => null
            ), 'field' => 'field_name' ) ),
            array( 'position', 123, array( 'errors' => array( 'empty' => 'error' ) ), array( 'value' => '123', 'required' => true, 'decimal' => 0, 'min_length' => 0, 'max_length' => 0, 'errors' => array(
                'empty' => 'error', 'number' => null, 'number_decimal' => null,
                'number_fixed' => null, 'number_range' => null, 'number_decimal_fixed' => null,
                'number_decimal_range' => null
            ), 'field' => 'position' ) ),
            array( 'position', 123, array( 'length' => array( 'max' => 9 ) ), array( 'value' => '123', 'required' => true, 'decimal' => 0, 'min_length' => 1, 'max_length' => 9, 'errors' => array(
                'empty' => null, 'number' => null, 'number_decimal' => null,
                'number_fixed' => null, 'number_range' => null, 'number_decimal_fixed' => null,
                'number_decimal_range' => null
            ), 'field' => 'position' ) ),
            array( 'position', 123, array( 'length' => 7 ), array( 'value' => '123', 'required' => true, 'decimal' => 0, 'min_length' => 7, 'max_length' => 0, 'errors' => array(
                'empty' => null, 'number' => null, 'number_decimal' => null,
                'number_fixed' => null, 'number_range' => null, 'number_decimal_fixed' => null,
                'number_decimal_range' => null
            ), 'field' => 'position' ) ),
            array( 'position', 123, array( 'required' => 'dfa' ), array( 'value' => '123', 'required' => true, 'decimal' => 0, 'min_length' => 0, 'max_length' => 0, 'errors' => array(
                'empty' => null, 'number' => null, 'number_decimal' => null,
                'number_fixed' => null, 'number_range' => null, 'number_decimal_fixed' => null,
                'number_decimal_range' => null
            ), 'field' => 'position' ) ),
            array( 'position', 123, array( 'required' => false ), array( 'value' => '123', 'required' => false, 'decimal' => 0, 'min_length' => 0, 'max_length' => 0, 'errors' => array(
                'empty' => null, 'number' => null, 'number_decimal' => null,
                'number_fixed' => null, 'number_range' => null, 'number_decimal_fixed' => null,
                'number_decimal_range' => null
            ), 'field' => 'position' ) )

        );
    }


    public function dataProvidertestNumberValidatorIsValidReturnsExpectedResult()
    {

        return array(

            array( 'position', null, array(), false ),
            array( 'position', '13213132132', array(), true ),
            array( 'position', '13213132132', array( 'decimal' => 2 ), false ),
            array( 'position', '13213132132.00', array( 'decimal' => 2 ), true ),
            array( 'position', '123.00', array( 'length' => array( 'min' => 2, 'max' => 3 ), 'decimal' => 2 ), true ),
            array( 'position', '123444.00', array( 'length' => array( 'min' => 2, 'max' => 3 ), 'decimal' => 2 ), false ),
            array( 'position', '123', array( 'length' => array( 'min' => 2, 'max' => 3 ) ), true ),
            array( 'position', '12', array( 'length' => array( 'min' => 2, 'max' => 3 ) ), true ),
            array( 'position', '123.00', array( 'length' => 2 ), false ),
            array( 'position', '123.00', array( 'length' => 3 ), false ),
            array( 'position', '123.00', array( 'length' => 3, 'decimal' => 2 ), true ),
            array( 'position', '123', array( 'length' => 3 ), true ),
            array( 'position', '123', array( 'length' => 2 ), false ),
            array( 'position', '123', array( 'length' => 3, 'max_length' => 0 ), true ),
            array( 'position', null, array( 'required' => false ), true )
        );
    }


    public function dataProvidertestNumberValidatorIsValidWhenFalseReturnExpectedErrorMessage()
    {

        return array(

            array( 'position', null, array(), 'Field position is empty' ),
            array( 'position', '123', array( 'decimal' => 2 ), 'Field position contains error.Only number with 2 decimal places are allowed' ),
            array( 'position', '123.00', array( 'decimal' => 0 ), 'Field position contains error.Only numbers without decimal places are allowed' ),
            array( 'position', 'adfsfda', array( 'decimal' => 0 ), 'Field position contains error.Only numbers without decimal places are allowed' ),
            array( 'position', 'adfsfda', array(), 'Field position contains error.Only numbers without decimal places are allowed' ),
            array( 'position', '1.00', array( 'length' => array( 'min' => 2, 'max' => 4 ), 'decimal' => 2 ), 'Field position contains error.Only number with 2 decimal places and length between 2 and 4 are allowed' ),
            array( 'position', '1.00', array( 'length' => array( 'min' => 2, 'max' => 4 ) ), 'Field position contains error.Only number without decimal places and length between 2 and 4 are allowed' ),
            array( 'position', '1.00', array( 'length' => array( 'min' => 2, 'max' => 4 ), 'decimal' => 0 ), 'Field position contains error.Only number without decimal places and length between 2 and 4 are allowed' ),
            array( 'position', '123', array( 'length' => 4 ), 'Field position contains error.Only number without decimal places and length equal to 4 are allowed' ),
            array( 'position', '123', array( 'length' => array( 'min' => 4, 'max' => 0 ) ), 'Field position contains error.Only number without decimal places and length equal to 4 are allowed' ),
            array( 'position', '123', array( 'length' => 2, 'decimal' => 2 ), 'Field position contains error.Only number with 2 decimal places and length equal to 2 are allowed' ),
            array( 'position', '123.00', array( 'length' => array( 2, 0 ), 'decimal' => 2 ), 'Field position contains error.Only number with 2 decimal places and length equal to 2 are allowed' )
        );
    }


    public function dataProvidertestRadioValidatorExptectedToSetupPropertyProperly()
    {
        return array(

            array( 'subscribe', 'yes', array(), array( 'value' => 'yes', 'errors' => array(
                'empty' => null
            ), 'required' => true, 'field' => 'subscribe' ) ),
            array( 'subscribe', 'yes', array( 'field' => 'field_name' ), array( 'value' => 'yes', 'errors' => array(
                'empty' => null
            ), 'required' => true, 'field' => 'field_name' ) ),
            array( 'subscribe', 'yes', array( 'required' => false ), array( 'value' => 'yes', 'errors' => array(
                'empty' => null
            ), 'required' => false, 'field' => 'subscribe' ) ),
            array( 'subscribe', 'yes', array( 'errors' => array( 'empty' => 'error' ) ), array( 'value' => 'yes', 'errors' => array( 'empty' => 'error' ), 'required' => true, 'field' => 'subscribe' ) )

        );
    }


    public function dataProvidertestRadioValidatorIsValidReturnsExpectedResult()
    {

        return array(

            array( 'subscribe', null, array(), false ),
            array( 'subscribe', 'yes', array(), true ),
            array( 'subscribe', 'yes', array( 'required' => false ), true ),
            array( 'subscribe', null, array( 'required' => false ), true ),
            array( 'subscribe', null, array( 'required' => true ), false )
        );
    }


    public function dataProvidertestRadioValidatorIsValidWhenFalseReturnExpectedErrorMessage()
    {

        return array(

            array( 'subscribe', null, array(), 'Radio subscribe is not marked' ),
            array( 'subscribe', null, array( 'errors' => array( 'empty' => 'error' ) ), 'error' )
        );
    }


    public function dataProvidertestRegexValidatorExptectedToSetupPropertyProperly()
    {

        return array(

            array( 'address', 'penang', '#a-z#', array(), array( 'value' => 'penang', 'regex' => '#a-z#', 'required' => true, 'errors' => array(
                'empty' => null, 'regex' => null
            ), 'field' => 'address' ) ),
            array( 'address', 'penang', '#a-z#', array( 'field' => 'field_name' ), array( 'value' => 'penang', 'regex' => '#a-z#', 'required' => true, 'errors' => array(
                'empty' => null, 'regex' => null
            ), 'field' => 'field_name' ) ),
            array( 'address', 'penang', '#a-z#', array( 'errors' => array( 'empty' => 'error' ) ), array( 'value' => 'penang', 'regex' => '#a-z#', 'required' => true, 'errors' => array( 'empty' => 'error', 'regex' => null ), 'field' => 'address' ) ),
            array( 'address', 'penang', '#a-z#', array( 'required' => false ), array( 'value' => 'penang', 'regex' => '#a-z#', 'required' => false, 'errors' => array(
                'empty' => null, 'regex' => null
            ), 'field' => 'address' ) )
        );
    }


    public function dataProvidertestRegexValidatorIsValidReturnsExpectedResult()
    {
        return array(

            array( 'address', 'penang', '#a-z#', array(), false ),
            array( 'address', 'penang', '#[a-z]+#', array(), true ),
            array( 'address', null, '#a-z#', array(), false ),
            array( 'address', null, '#a-z#', array( 'required' => true ), false ),
            array( 'address', null, '#a-z#', array( 'required' => false ), true )
        );
    }


    public function dataProvidertestRegexValidatorIsValidWhenFalseReturnExpectedErrorMessage()
    {

        return array(

            array( 'address', null, '#[a-z]+#', array(), 'Field address is empty' ),
            array( 'address', 'address99', '#^[a-z]+$#', array(), 'Field address contain invalid character' ),
            array( 'address', 'address99', '#^[a-z]+$#', array( 'errors' => array( 'regex' => 'error' ) ), 'error' ),
            array( 'address', null, '#[a-z]+#', array( 'required' => true ), 'Field address is empty' ),
            array( 'address', null, '#[a-z]+#', array( 'required' => true, 'errors' => array( 'empty' => 'error' ) ), 'error' )

        );
    }


    public function dataProvidertestFileValidatorExptectedToSetupPropertyProperly()
    {

        return array(

            array( 'avatar', 'file', array( 'txt', 'doc' ), array(), array( 'value' => 'file', 'extension' => array( 'txt', 'doc' ), 'errors' => array(
                'empty' => null, 'extension' => null
            ), 'required' => true, 'field' => 'avatar' ) ),
            array( 'avatar', 'file', array(), array( 'field' => 'field_name' ), array( 'value' => 'file', 'extension' => array(), 'errors' => array(
                'empty' => null, 'extension' => null
            ), 'required' => true, 'field' => 'field_name' ) ),
            array( 'avatar', 'file', array(), array( 'required' => false ), array( 'value' => 'file', 'extension' => array(), 'errors' => array(
                'empty' => null, 'extension' => null
            ), 'required' => false, 'field' => 'avatar' ) ),
            array( 'avatar', 'file', array(), array( 'errors' => array( 'extension' => 'error' ) ), array( 'value' => 'file', 'extension' => array(), 'errors' => array( 'extension' => 'error', 'empty' => null ), 'required' => true, 'field' => 'avatar' ) )

        );
    }


    public function dataProvidertestFileValidatorIsValidReturnsExpectedResult()
    {

        return array(

            array( 'avatar', array( 'name' => 'avatar.jpg' ), array(), array(), false ),
            array( 'avatar', array( 'name' => 'avatar.jpg' ), array( 'jpg' ), array(), true ),
            array( 'avatar', array( 'name' => null ), array(), array(), false ),
            array( 'avatar', array( 'name' => null ), array(), array( 'required' => false ), true ),
            array( 'avatar', array( 'name' => null ), array(), array( 'required' => true ), false )

        );
    }


    public function dataProvidertestFileValidatorIsValidWhenFalseReturnExpectedErrorMessage()
    {

        return array(

            array( 'avatar', array( 'name' => 'avatar.jpg' ), array( 'pdf' ), array(), 'File avatar contains error.Only file with extension pdf are allowed' ),
            array( 'avatar', array( 'name' => 'avatar.jpg' ), array( 'gif, png' ), array(), 'File avatar contains error.Only file with extension gif, png are allowed' ),
            array( 'avatar', null, array(), array(), 'File avatar is empty' ),
            array( 'avatar', null, array(), array( 'errors' => array( 'empty' => 'error' ) ), 'error' ),
            array( 'avatar', null, array(), array( 'required' => true ), 'File avatar is empty' )

        );
    }


    public function dataProvidertestSelectValidatorExptectedToSetupPropertyProperly()
    {

        return array(

            array( 'state', 'png', array(), array( 'value' => 'png', 'errors' => array(
                'empty' => null
            ), 'required' => true, 'field' => 'state' ) ),
            array( 'state', 'png', array( 'field' => 'field_name' ), array( 'value' => 'png', 'errors' => array(
                'empty' => null
            ), 'required' => true, 'field' => 'field_name' ) ),
            array( 'state', 'png', array( 'required' => false ), array( 'value' => 'png', 'errors' => array(
                'empty' => null
            ), 'required' => false, 'field' => 'state' ) ),
            array( 'state', 'png', array( 'errors' => array( 'empty' => 'error' ) ), array( 'value' => 'png', 'errors' => array( 'empty' => 'error' ), 'required' => true, 'field' => 'state' ) )

        );
    }


    public function dataProvidertestSelectValidatorIsValidReturnsExpectedResult()
    {

        return array(

            array( 'state', null, array(), false ),
            array( 'state', null, array( 'required' => true ), false ),
            array( 'state', null, array( 'required' => false ), true ),
            array( 'state', 'png', array(), true ),
            array( 'state', 'png', array( 'required' => false ), true )
        );
    }


    public function dataProvidertestSelectValidatorIsValidWhenFalseReturnExpectedErrorMessage()
    {

        return array(

            array( 'state', null, array(), 'Selection state is not selected' ),
            array( 'state', null, array( 'errors' => array( 'empty' => 'error' ) ), 'error' )
        );
    }


    public function dataProvidertestTextValidatorExptectedToSetupPropertyProperly()
    {

        return array(

            array( 'name', 'sobri', array(), array( 'value' => 'sobri', 'required' => true, 'allow_num' => false, 'allow_space' => false, 'min_length' => 0, 'max_length' => 0, 'errors' => array(
                'empty' => null, 'text' => null,
                'text_fixed' => null, 'text_range' => null,
                'text_number' => null, 'text_space' => null,
                'text_number_fixed' => null, 'text_space_fixed' => null,
                'text_number_range' => null, 'text_space_range' => null,
                'text_number_space' => null, 'text_number_space_fixed' => null,
                'text_number_space_range' => null
            ), 'field' => 'name' ) ),
            array( 'name', 'sobri', array( 'field' => 'field_name' ), array( 'value' => 'sobri', 'required' => true, 'allow_num' => false, 'allow_space' => false, 'min_length' => 0, 'max_length' => 0, 'errors' => array(
                'empty' => null, 'text' => null,
                'text_fixed' => null, 'text_range' => null,
                'text_number' => null, 'text_space' => null,
                'text_number_fixed' => null, 'text_space_fixed' => null,
                'text_number_range' => null, 'text_space_range' => null,
                'text_number_space' => null, 'text_number_space_fixed' => null,
                'text_number_space_range' => null
            ), 'field' => 'field_name' ) ),
            array( 'name', 'sobri', array( 'errors' => array( 'empty' => 'error' ) ), array( 'value' => 'sobri', 'required' => true, 'allow_num' => false, 'allow_space' => false, 'min_length' => 0, 'max_length' => 0, 'errors' => array(
                'empty' => 'error', 'text' => null,
                'text_fixed' => null, 'text_range' => null,
                'text_number' => null, 'text_space' => null,
                'text_number_fixed' => null, 'text_space_fixed' => null,
                'text_number_range' => null, 'text_space_range' => null,
                'text_number_space' => null, 'text_number_space_fixed' => null,
                'text_number_space_range' => null
            ), 'field' => 'name' ) ),
            array( 'name', 'sobri', array( 'length' => array( 'max' => 7 ) ), array( 'value' => 'sobri', 'required' => true, 'allow_num' => false, 'allow_space' => false, 'min_length' => 1, 'max_length' => 7, 'errors' => array(
                'empty' => null, 'text' => null,
                'text_fixed' => null, 'text_range' => null,
                'text_number' => null, 'text_space' => null,
                'text_number_fixed' => null, 'text_space_fixed' => null,
                'text_number_range' => null, 'text_space_range' => null,
                'text_number_space' => null, 'text_number_space_fixed' => null,
                'text_number_space_range' => null
            ), 'field' => 'name' ) ),
            array( 'name', 'sobri', array( 'length' => 9 ), array( 'value' => 'sobri', 'required' => true, 'allow_num' => false, 'allow_space' => false, 'min_length' => 9, 'max_length' => 0, 'errors' => array(
                'empty' => null, 'text' => null,
                'text_fixed' => null, 'text_range' => null,
                'text_number' => null, 'text_space' => null,
                'text_number_fixed' => null, 'text_space_fixed' => null,
                'text_number_range' => null, 'text_space_range' => null,
                'text_number_space' => null, 'text_number_space_fixed' => null,
                'text_number_space_range' => null
            ), 'field' => 'name' ) ),
            array( 'name', 'sobri', array( 'allow_space' => true ), array( 'value' => 'sobri', 'required' => true, 'allow_num' => false, 'allow_space' => true, 'min_length' => 0, 'max_length' => 0, 'errors' => array(
                'empty' => null, 'text' => null,
                'text_fixed' => null, 'text_range' => null,
                'text_number' => null, 'text_space' => null,
                'text_number_fixed' => null, 'text_space_fixed' => null,
                'text_number_range' => null, 'text_space_range' => null,
                'text_number_space' => null, 'text_number_space_fixed' => null,
                'text_number_space_range' => null
            ), 'field' => 'name' ) ),
            array( 'name', 'sobri', array( 'allow_num' => true ), array( 'value' => 'sobri', 'required' => true, 'allow_num' => true, 'allow_space' => false, 'min_length' => 0, 'max_length' => 0, 'errors' => array(
                'empty' => null, 'text' => null,
                'text_fixed' => null, 'text_range' => null,
                'text_number' => null, 'text_space' => null,
                'text_number_fixed' => null, 'text_space_fixed' => null,
                'text_number_range' => null, 'text_space_range' => null,
                'text_number_space' => null, 'text_number_space_fixed' => null,
                'text_number_space_range' => null
            ), 'field' => 'name' ) ),
            array( 'name', 'sobri', array( 'required' => false ), array( 'value' => 'sobri', 'required' => false, 'allow_num' => false, 'allow_space' => false, 'min_length' => 0, 'max_length' => 0, 'errors' => array(
                'empty' => null, 'text' => null,
                'text_fixed' => null, 'text_range' => null,
                'text_number' => null, 'text_space' => null,
                'text_number_fixed' => null, 'text_space_fixed' => null,
                'text_number_range' => null, 'text_space_range' => null,
                'text_number_space' => null, 'text_number_space_fixed' => null,
                'text_number_space_range' => null
            ), 'field' => 'name' ) )

        );
    }


    public function dataProvidertestTextValidatorIsValidReturnsExpectedResult()
    {

        return array(

            array( 'name', null, array(), false ),
            array( 'name', 'slier', array(), true ),
            array( 'name', '@#@#R ', array( 'allow_num' => true, 'allow_space' => true ), false ),
            array( 'name', 'slier', array( 'length' => 3, 'allow_space' => true ), false ),
            array( 'name', 'slier', array( 'length' => 5, 'allow_space' => true ), true ),
            array( 'name', 'slie ', array( 'length' => 5, 'allow_space' => true ), true ),
            array( 'name', 'sobri', array( 'length' => 20 ), false ),
            array( 'name', 'sobri', array(), true ),
            array( 'name', 'sobri', array( 'allow_num' => true, 'allow_space' => false ), true ),
            array( 'name', 'sobri ', array( 'allow_num' => true, 'allow_space' => false ), false ),
            array( 'name', 'sobri', array( 'allow_num' => true ), true ),
            array( 'name', 'sobri3', array( 'allow_num' => true ), true ),
            array( 'name', 'sobri3 ', array( 'allow_num' => true ), false ),
            array( 'name', 'sobri ', array( 'allow_num' => true ), false ),
            array( 'name', 'sobri', array( 'allow_num' => true, 'allow_space' => true ), true ),
            array( 'name', 'sobri ', array( 'allow_num' => true, 'allow_space' => true ), true ),
            array( 'name', 'sobri3', array( 'allow_num' => true, 'allow_space' => true ), true ),
            array( 'name', 'sobri3 4', array( 'allow_num' => true, 'allow_space' => true ), true ),
            array( 'name', 'sobri3', array(), false ),
            array( 'name', 'sobri ', array(), false ),
            array( 'name', 'sobri', array( 'allow_space' => true ), true ),
            array( 'name', 'sobr ', array( 'allow_space' => true ), true ),
            array( 'name', 'sobri ', array( 'allow_space' => true ), true ),
            array( 'name', 'sobri5', array( 'allow_space' => true ), false ),
            array( 'name', 'sobri', array( 'length' => array( 3, 5 ) ), true ),
            array( 'name', 'sobri', array( 'length' => array( 3, 5 ), 'allow_num' => true, 'allow_space' => true ), true ),
            array( 'name', 'sobr3', array( 'length' => array( 3, 5 ), 'allow_num' => true, 'allow_space' => true ), true ),
            array( 'name', 'sobr ', array( 'length' => array( 'min' => 3, 'max' => 5 ), 'allow_num' => true, 'allow_space' => true ), true ),
            array( 'name', 'sob3 ', array( 'length' => array( 'min' => 3, 'max' => 5 ), 'allow_num' => true, 'allow_space' => true ), true ),
            array( 'name', '3 ', array( 'length' => array( 'min' => 3, 'max' => 5 ), 'allow_num' => true, 'allow_space' => true ), false ),
            array( 'name', 'adfafdsfadsf', array( 'length' => array( 'min' => 3, 'max' => 5 ), 'allow_num' => true, 'allow_space' => true ), false ),
            array( 'name', 'adfafdsfa3 dfsdaf 2', array( 'length' => array( 'min' => 3, 'max' => 5 ), 'allow_num' => true, 'allow_space' => true ), false ),
            array( 'name', 'sobri', array( 'length' => array( 3, 5 ), 'allow_num' => true ), true ),
            array( 'name', 'sobr3', array( 'length' => array( 3, 5 ), 'allow_num' => true ), true ),
            array( 'name', 'so', array( 'length' => array( 3, 5 ), 'allow_num' => true ), false ),
            array( 'name', 'sobr ', array( 'length' => array( 'min' => 3, 'max' => 5 ), 'allow_num' => true ), false ),
            array( 'name', 'sobr ', array( 'length' => array( 'min' => 3, 'max' => 5 ), 'allow_space' => true ), true ),
            array( 'name', 'sob3 ', array( 'length' => array( 'min' => 3, 'max' => 5 ), 'allow_space' => true ), false ),
            array( 'name', 'sobr  ', array( 'length' => array( 'min' => 3, 'max' => 5 ), 'allow_space' => true ), false ),
            array( 'name', 'sobridsfasdf', array( 'length' => array( 'min' => 3, 'max' => 5 ) ), false ),
            array( 'name', 'sobr ', array( 'length' => array( 'min' => 3, 'max' => 5 ) ), false ),
            array( 'name', 'sobri', array( 'length' => 20, 'allow_num' => true ), false ),
            array( 'name', 'sobri', array( 'length' => 20, 'allow_num' => true, 'allow_space' => true ), false ),
            array( 'name', 'sobri', array( 'length' => 5 ), true ),
            array( 'name', 'sobr3', array( 'length' => 5 ), false ),
            array( 'name', 'sobr ', array( 'length' => 5 ), false ),
            array( 'name', 'sobr3', array( 'length' => 5, 'allow_num' => true ), true ),
            array( 'name', 'sob3 ', array( 'length' => 5, 'allow_num' => true ), false ),
            array( 'name', 'sob3 ', array( 'length' => 5, 'allow_num' => true, 'allow_space' => true ), true ),
            array( 'name', null, array( 'required' => true ), false ),
            array( 'name', null, array( 'required' => false ), true )

        );
    }


    public function dataProvidertestTextValidatorIsValidWhenFalseReturnExpectedErrorMessage()
    {

        return array(
            array( 'name', null, array(), 'Field name is empty' ),
            array( 'name', 'sobri', array( 'length' => 20 ), 'Field name contains error.Only text and length equal to 20 are allowed' ),
            array( 'name', 'sobri ', array( 'allow_num' => true, 'allow_space' => false ), 'Field name contains error.Only text and number are allowed' ),
            array( 'name', 'sobri3 ', array( 'allow_num' => true ), 'Field name contains error.Only text and number are allowed' ),
            array( 'name', 'sobri ', array( 'allow_num' => true ), 'Field name contains error.Only text and number are allowed' ),
            array( 'name', 'sobri3', array(), 'Field name contains error.Only text are allowed' ),
            array( 'name', 'sobri ', array(), 'Field name contains error.Only text are allowed' ),
            array( 'name', 'sobri5', array( 'allow_space' => true ), 'Field name contains error.Only text and space are allowed' ),
            array( 'name', '3 ', array( 'length' => array( 'min' => 3, 'max' => 5 ), 'allow_num' => true, 'allow_space' => true ), 'Field name contains error.Only text, number, space and length between 3 and 5 are allowed' ),
            array( 'name', 'adfafdsfadsf', array( 'length' => array( 'min' => 3, 'max' => 5 ), 'allow_num' => true, 'allow_space' => true ), 'Field name contains error.Only text, number, space and length between 3 and 5 are allowed' ),
            array( 'name', 'adfafdsfa3 dfsdaf 2', array( 'length' => array( 'min' => 3, 'max' => 5 ), 'allow_num' => true, 'allow_space' => true ), 'Field name contains error.Only text, number, space and length between 3 and 5 are allowed' ),
            array( 'name', 'so', array( 'length' => array( 'min' => 3, 'max' => 5 ), 'allow_num' => true ), 'Field name contains error.Only text, number and length between 3 and 5 are allowed' ),
            array( 'name', 'sobr ', array( 'length' => array( 'min' => 3, 'max' => 5 ), 'allow_num' => true ), 'Field name contains error.Only text, number and length between 3 and 5 are allowed' ),
            array( 'name', 'sob3 ', array( 'length' => array( 'min' => 3, 'max' => 5 ), 'allow_space' => true ), 'Field name contains error.Only text, space and length between 3 and 5 are allowed' ),
            array( 'name', 'sobr  ', array( 'length' => array( 'min' => 3, 'max' => 5 ), 'allow_space' => true ), 'Field name contains error.Only text, space and length between 3 and 5 are allowed' ),
            array( 'name', 'sobridsfasdf', array( 'length' => array( 'min' => 3, 'max' => 5 ) ), 'Field name contains error.Only text and length between 3 and 5 are allowed' ),
            array( 'name', 'sobr ', array( 'length' => array( 'min' => 3, 'max' => 5 ) ), 'Field name contains error.Only text and length between 3 and 5 are allowed' ),
            array( 'name', 'sobri', array( 'length' => array( 'min' => 20 ), 'allow_num' => true ), 'Field name contains error.Only text, number and length equal to 20 are allowed' ),
            array( 'name', 'sobri', array( 'length' => array( 'min' => 20 ), 'allow_num' => true, 'allow_space' => true ), 'Field name contains error.Only text, number, space and length equal to 20 are allowed' ),
            array( 'name', 'sobr3', array( 'length' => array( 'min' => 5 ) ), 'Field name contains error.Only text and length equal to 5 are allowed' ),
            array( 'name', 'sobr ', array( 'length' => array( 'min' => 5 ) ), 'Field name contains error.Only text and length equal to 5 are allowed' ),
            array( 'name', 'sob3 ', array( 'length' => array( 'min' => 5 ), 'allow_num' => true ), 'Field name contains error.Only text, number and length equal to 5 are allowed' ),
            array( 'name', null, array( 'required' => true ), 'Field name is empty' )
        );
    }


    public function dataProvidertestIsValidReturnsExpectedResult()
    {


        return array(

            array( array( new TextValidator( 'name', 'sobri' ) ), true ),
            array( array( new TextValidator( 'name', 'sobri' ), new NumberValidator( 'position', '123' ) ), true ),
            array( array( new NumberValidator( 'position', '123.00' ) ), false ),
            array( array( new NumberValidator( 'position', '123.00' ), new TextValidator( 'name', 'sobri' ) ), false ),
            array( array( new TextValidator( 'name', 'sobri ' ) ), false )

        );
    }


    public function dataProvidertestIsErrorReturnsExpectedResult()
    {

        return array(

            array( function () {

                $validator = new Validator();
                $validator->invalidateValidation( 'error' );
                return $validator->isError();
            }, true ),

            array( function () {

                $validator = new Validator();
                $validator->addValidator( 'name', new TextValidator( 'name', 'sobri' ) );
                $validator->addValidator( 'position', new NumberValidator( 'position', '123' ) );
                $validator->isValid();
                return $validator->isError();
            }, false ),

            array( function () {

                $validator = new Validator();
                $validator->addValidator( 'name', new TextValidator( 'name', 'sobri' ) );
                $validator->addValidator( 'position', new NumberValidator( 'position', 'adsf' ) );
                $validator->isValid();
                return $validator->isError();
            }, true ),

            array( function () {

                $validator = new Validator();
                $validator->addValidator( 'name', new TextValidator( 'name', 'sobri' ) );
                $validator->addValidator( 'position', new NumberValidator( 'position', '123' ) );
                $validator->isValid();
                $validator->invalidateField( 'name', 'name is suspended' );
                return $validator->isError();

            }, true )


        );
    }


    public function dataProvidertestGetErrorReturnsExpectedErorMsg()
    {

        return array(

            array( function () {

                $validator = new Validator();
                $validator->addValidator( 'user', new TextValidator( 'user', '' ) );
                $validator->isValid();
                return $validator->getError( 'user' );

            }, 'Field user is empty' ),

            array( function () {

                $validator = new Validator();
                $validator->addValidator( 'user', new TextValidator( 'user', 'slier9' ) );
                $validator->isValid();
                return $validator->getError( 'user' );

            }, 'Field user contains error.Only text are allowed' ),

        );
    }


    public function dataProvidertestGetAllErrorReturnsExptectedValue()
    {

        return array(

            array( new TextValidator( 'user', 'slier' ), null ),
            array( array( 'user' => new TextValidator( 'user', '123' ) ), array( 'user' => 'Field user contains error.Only text are allowed' ) ),
            array( array( 'position' => new NumberValidator( 'position', 'slier' ) ), array( 'position' => 'Field position contains error.Only numbers without decimal places are allowed' ) ),
            array( array( 'position' => new NumberValidator( 'position', 'slier' ), 'user' => new TextValidator( 'user', '123' ) ), array( 'position' => 'Field position contains error.Only numbers without decimal places are allowed', 'user' => 'Field user contains error.Only text are allowed' ) )
        );
    }


    public function dataProvidertestShowErrorReturnsExpectedValue()
    {

        return array(

            array( array( 'user' => new TextValidator( 'user', '123' ) ), 'Field user contains error.Only text are allowed<br>' ),
            array( array( 'position' => new NumberValidator( 'position', 'slier' ) ), 'Field position contains error.Only numbers without decimal places are allowed<br>' ),
            array( array( 'position' => new NumberValidator( 'position', 'slier' ), 'user' => new TextValidator( 'user', '123' ) ), 'Field position contains error.Only numbers without decimal places are allowed<br>Field user contains error.Only text are allowed<br>' )

        );
    }
}