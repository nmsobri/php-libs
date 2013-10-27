<?php

include 'class.form.php';

class FormTest extends PHPUnit_Framework_TestCase
{


    public function invokeMethod( &$object, $methodName, array $parameters = array() )
    {
        $reflection = new \ReflectionClass( get_class( $object ) );
        $method = $reflection->getMethod( $methodName );
        $method->setAccessible( true );
        return $method->invokeArgs( $object, $parameters );
    }


    public function setup()
    {
        $this->form = new Form();
    }


    public function testConfigElementReturnsArray()
    {
        $result = $this->invokeMethod( $this->form, 'configElement', array( 'user', array() ) );
        $this->assertTrue( is_array( $result ) );
    }


    /**
     * @param $name
     * @param $attr
     * @dataProvider dataProvidertestConfigElementReturnsExpectedValue
     */
    public function testConfigElementReturnsExpectedValue( $name, $attr, $expectedResult )
    {

        $result = $this->invokeMethod( $this->form, 'configElement', array( $name, $attr ) );
        $this->assertEquals( $expectedResult, $result );

    }


    public function testFormEndReturnsExpectedValue()
    {
        $result = $this->form->formEnd();
        $expected_value = '</form>';
        $this->assertEquals( $expected_value, $result );
    }


    /**
     * @param $form
     * @dataProvider dataProvidertestGetFormDataReturnsExpectedValue
     */
    public function testGetFormDataReturnsExpectedValue( $form, $expected_value )
    {
        $_POST = array( 'method' => 'post' );
        $_GET = array( 'method' => 'get' );
        $result = $this->invokeMethod( $form, 'getFormData', array() );
        $this->assertEquals( $expected_value, $result );
    }


    public function testExtractQueryStringReturnsExpectedValue()
    {
        $result = $this->invokeMethod( $this->form, 'extractQueryString', array( 'index.php?id=1&lang=en' ) );
        $expected_value = array( 'id' => 1, 'lang' => 'en' );
        $this->assertEquals( $expected_value, $result );
    }


    public function testFormActionReturnsExpectedValue()
    {
        $result = $this->invokeMethod( $this->form, 'formAction', array( 'index.php?id=1&user=zack', 'index.php?id=1&user=slier&lang=en' ) );
        $expected_result = 'index.php?id=1&user=slier&lang=en';
        $this->assertEquals( $expected_result, $result );

    }


    /**
     * @param $name
     * @param $default_value
     * @dataProvider dataProvidergetTextValue
     */
    public function testGetTextValueReturnsExpectedValue( $name, $default_value, $POST, $expected_value )
    {
        $mock_form = $this->getMockBuilder( 'Form' )->setMethods( array( 'getFormData' ) )->getMock();
        $mock_form->expects( $this->any() )->method( 'getFormData' )->will( $this->returnValue( $POST ) );

        if( is_array( @$POST[$name] ) ){
            foreach( $POST[$name] as $val ){
                $result = $this->invokeMethod( $mock_form, 'getTextValue', array( $name, $default_value ) );
            }
        }
        else{

            $result = $this->invokeMethod( $mock_form, 'getTextValue', array( $name, $default_value ) );
        }
        $this->assertEquals( $expected_value, $result );
    }


    /**
     * @param $name
     * @param $default_value
     * @param $attr
     * @dataProvider dataProvidertestTextReturnsExpectedValue
     */
    public function testTextReturnsExpectedValue( $name, $default_value, $attr, $POST, $expected_value )
    {
        $mock_form = $this->getMockBuilder( 'Form' )->setMethods( array( 'getFormData' ) )->getMock();
        $mock_form->expects( $this->any() )->method( 'getFormData' )->will( $this->returnValue( $POST ) );
        $result = $mock_form->text( $name, $default_value, $attr );
        $this->assertEquals( $expected_value, $result );
    }


    /**
     * @param $name
     * @param $default_value
     * @param $attr
     * @dataProvider dataProvidertestTextareaReturnsExpectedValue
     */
    public function testTextareaReturnsExpectedValue( $name, $default_value, $attr, $POST, $expected_value )
    {
        $mock_form = $this->getMockBuilder( 'Form' )->setMethods( array( 'getFormData' ) )->getMock();
        $mock_form->expects( $this->any() )->method( 'getFormData' )->will( $this->returnValue( $POST ) );
        $result = $mock_form->textarea( $name, $default_value, $attr );
        $this->assertEquals( $expected_value, $result );
    }


    /**
     * @param $name
     * @param $default_value
     * @param $expected_value
     * @dataProvider dataProvidertestPasswordReturnsExpectedValue
     */
    public function testPasswordReturnsExpectedValue( $name, $attr, $expected_value )
    {
        $result = $this->form->password( $name, $attr );
        $this->assertEquals( $expected_value, $result );
    }


    /**
     * @param $name
     * @param $options
     * @param $selected
     * @param $attr
     * @param $expected_value
     * @dataProvider dataProvidertestSelectReturnsExpectedValue
     */
    public function testSelectReturnsExpectedValue( $name, $options, $selected, $attr, $POST, $expected_value )
    {
        $mock_form = $this->getMockBuilder( 'Form' )->setMethods( array( 'getFormData' ) )->getMock();
        $mock_form->expects( $this->any() )->method( 'getFormData' )->will( $this->returnValue( $POST ) );

        $result = $mock_form->select( $name, $options, $selected, $attr );
        $this->assertEquals( $expected_value, $result );
    }


    /**
     * @param $name
     * @param $value
     * @param $checked
     * @param $attr
     * @dataProvider dataProvidertestRadioReturnsExpectedValue
     */
    public function testRadioReturnsExpectedValue( $name, $value, $checked, $attr, $POST, $expected_value )
    {
        $mock_form = $this->getMockBuilder( 'Form' )->setMethods( array( 'getFormData' ) )->getMock();
        $mock_form->expects( $this->any() )->method( 'getFormData' )->will( $this->returnValue( $POST ) );

        $result = $mock_form->radio( $name, $value, $checked, $attr );
        $this->assertEquals( $expected_value, $result );
    }


    /**
     * @param $name
     * @param $value
     * @param $checked
     * @param $attr
     * @param $POST
     * @param $expected_value
     * @dataProvider dataProvidertestCheckboxReturnsExpectedValue
     */
    public function testCheckboxReturnsExpectedValue( $name, $value, $checked, $attr, $POST, $expected_value )
    {

        $mock_form = $this->getMockBuilder( 'Form' )->setMethods( array( 'getFormData' ) )->getMock();
        $mock_form->expects( $this->any() )->method( 'getFormData' )->will( $this->returnValue( $POST ) );

        $result = $mock_form->checkbox( $name, $value, $checked, $attr );
        $this->assertEquals( $expected_value, $result );
    }


    /**
     * @param $name
     * @param $attr
     * @param $expected_value
     * @dataProvider dataProvidertestFileReturnsExpectedValue
     */
    public function testFileReturnsExpectedValue( $name, $attr, $expected_value )
    {

        $result = $this->form->file( $name, $attr );
        $this->assertEquals( $expected_value, $result );
    }


    /**
     * @param $name
     * @param $value
     * @param $attr
     * @param $POST
     * @param $expected_value
     * @dataProvider dataProvidertestHiddenReturnsExpectedValue
     */
    public function testHiddenReturnsExpectedValue( $name, $value, $attr, $POST, $expected_value )
    {
        $mock_form = $this->getMockBuilder( 'Form' )->setMethods( array( 'getFormData' ) )->getMock();
        $mock_form->expects( $this->any() )->method( 'getFormData' )->will( $this->returnValue( $POST ) );

        $result = $mock_form->hidden( $name, $value, $attr );
        $this->assertEquals( $expected_value, $result );
    }


    /**
     * @param $name
     * @param $value
     * @param $attr
     * @param $expected_value
     * @dataProvider dataProvidertestButtonReturnsExpectedValue
     */
    public function testButtonReturnsExpectedValue( $name, $value, $attr, $expected_value )
    {

        $result = $this->form->button( $name, $value, $attr );
        $this->assertEquals( $expected_value, $result );
    }


    /**
     * @param $name
     * @param $value
     * @param $attr
     * @dataProvider dataProvidertestSubmitReturnsExpectedValue
     */
    public function testSubmitReturnsExpectedValue( $name, $value, $attr, $expected_value )
    {
        $result = $this->form->submit( $name, $value, $attr );
        $this->assertEquals( $expected_value, $result );
    }


    /**
     * @param $action
     * @param $attr
     * @param $expected
     * @dataProvider dataProvidertestFormStartReturnsExpectedValue
     */
    public function testFormStartReturnsExpectedValue( $action, $attr, $expected )
    {
        $result = $this->form->formStart( $action, $attr );
        $this->assertEquals( $expected, $result );
    }


    /**
     * @param $name
     * @param $value
     * @param $checked
     * @param $attr
     * @param $POST
     * @param $expected_value
     * @dataProvider dataProvidertestCheckboxMultipleMarkElementProperly
     */
    public function testCheckboxMultipleMarkElementProperly( $name, $value, $checked, $attr, $POST, $expected_value )
    {
        $mock_form = $this->getMockBuilder( 'Form' )->setMethods( array( 'getFormData' ) )->getMock();
        $mock_form->expects( $this->any() )->method( 'getFormData' )->will( $this->returnValue( $POST ) );
        $result = $mock_form->checkbox( $name, $value, $checked, $attr );
        $this->assertEquals( $expected_value, $result );
    }


    /**
     * @param $name
     * @param $options
     * @param $selected
     * @param $attr
     * @param $POST
     * @param $expected_value
     * @dataProvider dataProvidertestSelectMultipleMarkElementProperly
     */
    public function testSelectMultipleMarkElementProperly( $name, $options, $selected, $attr, $POST, $expected_value )
    {
        $mock_form = $this->getMockBuilder( 'Form' )->setMethods( array( 'getFormData' ) )->getMock();
        $mock_form->expects( $this->any() )->method( 'getFormData' )->will( $this->returnValue( $POST ) );
        $result = $mock_form->select( $name, $options, $selected, $attr );
        $this->assertEquals( $expected_value, $result );
    }


    /**
     * @param $name
     * @param $value
     * @param $checked
     * @param $attr
     * @param $POST
     * @param $expected_value
     * @dataProvider dataProvidertestRadioMultipleMarkElementProperly
     */
    public function testRadioMultipleMarkElementProperly( $name, $value, $checked, $attr, $POST, $expected_value )
    {
        $mock_form = $this->getMockBuilder( 'Form' )->setMethods( array( 'getFormData' ) )->getMock();
        $mock_form->expects( $this->any() )->method( 'getFormData' )->will( $this->returnValue( $POST ) );
        $result = $mock_form->radio( $name, $value, $checked, $attr );
        $this->assertEquals( $expected_value, $result );
    }


    public function dataProvidertestConfigElementReturnsExpectedValue()
    {

        return array(

            array( 'user',
                array( 'id' => null, 'class' => null, 'disabled' => null, 'readonly' => null, 'placeholder' => null, 'cols' => null, 'rows' => null, 'multiple' => null, 'size' => null, 'target' => null, 'upload' => null ),
                array( 'id' => 'user_id', 'class' => 'user_class', 'disabled' => null, 'readonly' => null, 'placeholder' => null, 'cols' => 20, 'rows' => 3, 'multiple' => null, 'size' => null, 'target' => '_self', 'upload' => null )
            ),

            array( 'user',
                array( 'disabled' => true, 'readonly' => null, 'placeholder' => null, 'cols' => null, 'rows' => null, 'multiple' => null, 'size' => null, 'target' => null, 'upload' => null ),
                array( 'id' => 'user_id', 'class' => 'user_class', 'disabled' => 'disabled', 'readonly' => null, 'placeholder' => null, 'cols' => 20, 'rows' => 3, 'multiple' => null, 'size' => null, 'target' => '_self', 'upload' => null )
            ),

            array( 'user',
                array( 'disabled' => null, 'readonly' => true, 'placeholder' => null, 'cols' => null, 'rows' => null, 'multiple' => null, 'size' => null, 'target' => null, 'upload' => null ),
                array( 'id' => 'user_id', 'class' => 'user_class', 'disabled' => null, 'readonly' => 'readonly', 'placeholder' => null, 'cols' => 20, 'rows' => 3, 'multiple' => null, 'size' => null, 'target' => '_self', 'upload' => null )
            ),

            array( 'user',
                array( 'disabled' => null, 'readonly' => null, 'placeholder' => 'this is placeholder', 'cols' => null, 'rows' => null, 'multiple' => null, 'size' => null, 'target' => null, 'upload' => null ),
                array( 'id' => 'user_id', 'class' => 'user_class', 'disabled' => null, 'readonly' => null, 'placeholder' => 'this is placeholder', 'cols' => 20, 'rows' => 3, 'multiple' => null, 'size' => null, 'target' => '_self', 'upload' => null )
            ),

            array( 'user',
                array( 'disabled' => null, 'readonly' => null, 'placeholder' => null, 'cols' => 20, 'rows' => null, 'multiple' => null, 'size' => null, 'target' => null, 'upload' => null ),
                array( 'id' => 'user_id', 'class' => 'user_class', 'disabled' => null, 'readonly' => null, 'placeholder' => null, 'cols' => 20, 'rows' => 3, 'multiple' => null, 'size' => null, 'target' => '_self', 'upload' => null )
            ),

            array( 'user',
                array( 'disabled' => null, 'readonly' => null, 'placeholder' => null, 'cols' => null, 'rows' => 3, 'multiple' => null, 'size' => null, 'target' => null, 'upload' => null ),
                array( 'id' => 'user_id', 'class' => 'user_class', 'disabled' => null, 'readonly' => null, 'placeholder' => null, 'cols' => 20, 'rows' => 3, 'multiple' => null, 'size' => null, 'target' => '_self', 'upload' => null )
            ),

            array( 'user',
                array( 'disabled' => null, 'readonly' => null, 'placeholder' => null, 'cols' => null, 'rows' => null, 'multiple' => true, 'size' => null, 'target' => null, 'upload' => null ),
                array( 'id' => 'user_id', 'class' => 'user_class', 'disabled' => null, 'readonly' => null, 'placeholder' => null, 'cols' => 20, 'rows' => 3, 'multiple' => 'multiple', 'size' => null, 'target' => '_self', 'upload' => null )
            ),

            array( 'user',
                array( 'disabled' => null, 'readonly' => null, 'placeholder' => null, 'cols' => null, 'rows' => null, 'multiple' => null, 'size' => 20, 'target' => null, 'upload' => null ),
                array( 'id' => 'user_id', 'class' => 'user_class', 'disabled' => null, 'readonly' => null, 'placeholder' => null, 'cols' => 20, 'rows' => 3, 'multiple' => null, 'size' => 'size="20"', 'target' => '_self', 'upload' => null )
            ),

            array( 'user',
                array( 'disabled' => null, 'readonly' => null, 'placeholder' => null, 'cols' => null, 'rows' => null, 'multiple' => null, 'size' => null, 'target' => '_blank', 'upload' => null ),
                array( 'id' => 'user_id', 'class' => 'user_class', 'disabled' => null, 'readonly' => null, 'placeholder' => null, 'cols' => 20, 'rows' => 3, 'multiple' => null, 'size' => null, 'target' => '_blank', 'upload' => null )
            ),

            array( 'user',
                array( 'disabled' => null, 'readonly' => null, 'placeholder' => null, 'cols' => null, 'rows' => null, 'multiple' => null, 'size' => null, 'target' => null, 'upload' => true ),
                array( 'id' => 'user_id', 'class' => 'user_class', 'disabled' => null, 'readonly' => null, 'placeholder' => null, 'cols' => 20, 'rows' => 3, 'multiple' => null, 'size' => null, 'target' => '_self', 'upload' => 'enctype="multipart/form-data"' )
            ),

            array( 'user',
                array( 'disabled' => null, 'readonly' => null, 'placeholder' => null, 'cols' => 'lol', 'rows' => true, 'multiple' => null, 'size' => true, 'target' => null, 'upload' => true ),
                array( 'id' => 'user_id', 'class' => 'user_class', 'disabled' => null, 'readonly' => null, 'placeholder' => null, 'cols' => 20, 'rows' => 3, 'multiple' => null, 'size' => null, 'target' => '_self', 'upload' => 'enctype="multipart/form-data"' )
            )

        );
    }


    public function dataProvidertestGetFormDataReturnsExpectedValue()
    {

        return array(

            array( new Form(), array( 'method' => 'post' ) ),
            array( new Form( 'post' ), array( 'method' => 'post' ) ),
            array( new Form( 'get' ), array( 'method' => 'get' ) )

        );
    }


    public function dataProvidergetTextValue()
    {

        return array(


            array( 'username', 'slier', array(), 'slier' ),
            array( 'username', 'slier', array( 'username' => 'zack_slier' ), 'zack_slier' ),
            array( 'username', 'slier', array( 'age' => 30 ), 'slier' ),
            array( 'username[]', 'slier', array( 'username' => array( 'sobri' ) ), 'sobri' ),
            array( 'username[]', 'slier', array(), 'slier' )

        );
    }


    public function dataProvidertestTextReturnsExpectedValue()
    {

        return array(

            array( 'username', 'slier', array(), array(), '<input type="text" name="username" id="username_id" class="username_class" value="slier" placeholder="">' ),
            array( 'username', 'slier', array( 'id' => 'slier' ), array(), '<input type="text" name="username" id="slier" class="username_class" value="slier" placeholder="">' ),
            array( 'username', 'slier', array( 'readonly' => false, 'disabled' => false ), array(), '<input type="text" name="username" id="username_id" class="username_class" value="slier" placeholder="">' ),
            array( 'username', 'slier', array( 'readonly' => true, 'disabled' => false ), array(), '<input type="text" name="username" id="username_id" class="username_class" value="slier" placeholder="" readonly>' ),
            array( 'username', 'slier', array( 'readonly' => false, 'disabled' => true ), array(), '<input type="text" name="username" id="username_id" class="username_class" value="slier" placeholder="" disabled>' ),
            array( 'username', 'slier', array( 'readonly' => true, 'disabled' => false, 'placeholder' => 'gah' ), array(), '<input type="text" name="username" id="username_id" class="username_class" value="slier" placeholder="gah" readonly>' )

        );
    }


    public function dataProvidertestTextareaReturnsExpectedValue()
    {

        return array(

            array( 'username', 'slier', array(), array(), '<textarea name="username" id="username_id" class="username_class" placeholder="" cols="20" rows="3">slier</textarea>' ),
            array( 'username', 'slier', array( 'id' => 'slier' ), array(), '<textarea name="username" id="slier" class="username_class" placeholder="" cols="20" rows="3">slier</textarea>' ),
            array( 'username', 'slier', array( 'readonly' => false, 'disabled' => false ), array(), '<textarea name="username" id="username_id" class="username_class" placeholder="" cols="20" rows="3">slier</textarea>' ),
            array( 'username', 'slier', array( 'readonly' => true, 'disabled' => false ), array(), '<textarea name="username" id="username_id" class="username_class" placeholder="" cols="20" rows="3" readonly>slier</textarea>' ),
            array( 'username', 'slier', array( 'readonly' => false, 'disabled' => true ), array(), '<textarea name="username" id="username_id" class="username_class" placeholder="" cols="20" rows="3" disabled>slier</textarea>' ),
            array( 'username', 'slier', array( 'readonly' => true, 'disabled' => true ), array(), '<textarea name="username" id="username_id" class="username_class" placeholder="" cols="20" rows="3" readonly disabled>slier</textarea>' ),
            array( 'username', 'slier', array( 'readonly' => true, 'disabled' => false, 'placeholder' => 'gah' ), array(), '<textarea name="username" id="username_id" class="username_class" placeholder="gah" cols="20" rows="3" readonly>slier</textarea>' ),
            array( 'username', 'slier', array( 'readonly' => true, 'disabled' => false, 'placeholder' => 'gah', 'rows' => 10, 'cols' => 50 ), array(), '<textarea name="username" id="username_id" class="username_class" placeholder="gah" cols="50" rows="10" readonly>slier</textarea>' )

        );
    }


    public function dataProvidertestPasswordReturnsExpectedValue()
    {
        return array(


            array( 'password', array(), '<input type="password" name="password" id="password_id" class="password_class" placeholder="">' ),
            array( 'password', array( 'id' => 'slier' ), '<input type="password" name="password" id="slier" class="password_class" placeholder="">' ),
            array( 'password', array( 'class' => 'slier' ), '<input type="password" name="password" id="password_id" class="slier" placeholder="">' ),
            array( 'password', array( 'placeholder' => 'gah' ), '<input type="password" name="password" id="password_id" class="password_class" placeholder="gah">' ),
            array( 'password', array( 'disabled' => true ), '<input type="password" name="password" id="password_id" class="password_class" placeholder="" disabled>' ),
            array( 'password', array( 'readonly' => true ), '<input type="password" name="password" id="password_id" class="password_class" placeholder="" readonly>' ),
            array( 'password', array( 'readonly' => true, 'disabled' => true ), '<input type="password" name="password" id="password_id" class="password_class" placeholder="" readonly disabled>' )

        );
    }


    public function dataProvidertestSelectReturnsExpectedValue()
    {

        return array(


            array( 'state', array( 'png' => 'Penang', 'kl' => 'K.Lumpur' ), null, array(), array( 'state' => 'png' ), '<select name="state" id="state_id" class="state_class"><option value="png" selected>Penang</option><option value="kl">K.Lumpur</option></select>' ),
            array( 'state', array( 'png' => 'Penang', 'kl' => 'K.Lumpur' ), null, array(), array( 'state' => array( 'Penang', 'K.Lumpur' ) ), '<select name="state" id="state_id" class="state_class"><option value="png">Penang</option><option value="kl">K.Lumpur</option></select>' ),
            array( 'country[]', array( 'png' => 'Penang', 'kl' => 'K.Lumpur' ), null, array(), array( 'state' => array( 0 => 'png' ) ), '<select name="country[0][]" id="country1_id" class="country_class"><option value="png">Penang</option><option value="kl">K.Lumpur</option></select>' ),
            array( 'state[]', array( 'png' => 'Penang', 'kl' => 'K.Lumpur' ), null, array(), array( 'state' => array( 0 => array( 'kl' ) ) ), '<select name="state[0][]" id="state2_id" class="state_class"><option value="png">Penang</option><option value="kl" selected>K.Lumpur</option></select>' ),
            array( 'state', array( 'png' => 'Penang', 'kl' => 'K.Lumpur' ), null, array( 'id' => 'slier' ), array(), '<select name="state" id="slier" class="state_class"><option value="png">Penang</option><option value="kl">K.Lumpur</option></select>' ),
            array( 'state', array( 'png' => 'Penang', 'kl' => 'K.Lumpur' ), null, array( 'class' => 'slier' ), array(), '<select name="state" id="state_id" class="slier"><option value="png">Penang</option><option value="kl">K.Lumpur</option></select>' ),
            array( 'state', array( 'png' => 'Penang', 'kl' => 'K.Lumpur' ), null, array( 'disabled' => true ), array(), '<select name="state" id="state_id" class="state_class" disabled><option value="png">Penang</option><option value="kl">K.Lumpur</option></select>' ),
            array( 'state', array( 'png' => 'Penang', 'kl' => 'K.Lumpur' ), null, array( 'readonly' => true ), array(), '<select name="state" id="state_id" class="state_class" readonly><option value="png">Penang</option><option value="kl">K.Lumpur</option></select>' ),
            array( 'state', array( 'png' => 'Penang', 'kl' => 'K.Lumpur' ), null, array( 'multiple' => true ), array(), '<select name="state" id="state_id" class="state_class" multiple><option value="png">Penang</option><option value="kl">K.Lumpur</option></select>' ),
            array( 'state', array( 'png' => 'Penang', 'kl' => 'K.Lumpur' ), null, array( 'size' => 10 ), array(), '<select name="state" id="state_id" class="state_class" size="10"><option value="png">Penang</option><option value="kl">K.Lumpur</option></select>' ),
            array( 'state', array( 'north' => array( 'kdh' => 'Kedah', 'png' => 'Penang', 'prk' => 'Perak' ) ), null, array(), array(), '<select name="state" id="state_id" class="state_class"><optgroup label="north"><option value="kdh">Kedah</option><option value="png">Penang</option><option value="prk">Perak</option></optgroup></select>' ),
            array( 'state', array( 'north' => array( 'kdh' => 'Kedah', 'png' => 'Penang', 'prk' => 'Perak' ) ), null, array( 'multiple' => true ), array(), '<select name="state" id="state_id" class="state_class" multiple><optgroup label="north"><option value="kdh">Kedah</option><option value="png">Penang</option><option value="prk">Perak</option></optgroup></select>' )

        );
    }


    public function dataProvidertestRadioReturnsExpectedValue()
    {
        return array(


            array( 'subscribe', 'yes', false, array(), array(), '<input type="radio" name="subscribe" id="subscribe_id" class="subscribe_class" value="yes">' ),
            array( 'subscribe[]', 'yes', false, array(), array( 'subscribe' => array( 'yes', 'no' ) ), '<input type="radio" name="subscribe[]" id="subscribe3_id" class="subscribe_class" value="yes" checked>' ),
            array( 'subscribe', 'yes', false, array(), array( 'subscribe' => 'yes' ), '<input type="radio" name="subscribe" id="subscribe_id" class="subscribe_class" value="yes" checked>' ),
            array( 'subscribe', 'yes', true, array(), array(), '<input type="radio" name="subscribe" id="subscribe_id" class="subscribe_class" value="yes" checked>' ),
            array( 'subscribe', 'yes', false, array( 'readonly' => true ), array(), '<input type="radio" name="subscribe" id="subscribe_id" class="subscribe_class" value="yes" readonly>' ),
            array( 'subscribe', 'yes', false, array( 'disabled' => true ), array(), '<input type="radio" name="subscribe" id="subscribe_id" class="subscribe_class" value="yes" disabled>' ),
            array( 'subscribe', 'yes', false, array( 'class' => 'slier' ), array(), '<input type="radio" name="subscribe" id="subscribe_id" class="slier" value="yes">' ),
            array( 'subscribe', 'yes', false, array( 'id' => 'slier' ), array(), '<input type="radio" name="subscribe" id="slier" class="subscribe_class" value="yes">' )

        );
    }


    public function dataProvidertestCheckboxReturnsExpectedValue()
    {

        return array(


            array( 'subscribe', 'yes', false, array(), array(), '<input type="checkbox" name="subscribe" id="subscribe_id" class="subscribe_class" value="yes">' ),
            array( 'subscribe[]', 'yes', false, array(), array( 'subscribe' => array( 'yes', 'blah' ) ), '<input type="checkbox" name="subscribe[]" id="subscribe4_id" class="subscribe_class" value="yes" checked>' ),
            array( 'subscribe', 'yes', false, array(), array( 'subscribe' => 'yes' ), '<input type="checkbox" name="subscribe" id="subscribe_id" class="subscribe_class" value="yes" checked>' ),
            array( 'subscribe', 'yes', true, array(), array(), '<input type="checkbox" name="subscribe" id="subscribe_id" class="subscribe_class" value="yes" checked>' ),
            array( 'subscribe', 'yes', false, array( 'readonly' => true ), array(), '<input type="checkbox" name="subscribe" id="subscribe_id" class="subscribe_class" value="yes" readonly>' ),
            array( 'subscribe', 'yes', false, array( 'disabled' => true ), array(), '<input type="checkbox" name="subscribe" id="subscribe_id" class="subscribe_class" value="yes" disabled>' ),
            array( 'subscribe', 'yes', false, array( 'class' => 'slier' ), array(), '<input type="checkbox" name="subscribe" id="subscribe_id" class="slier" value="yes">' ),
            array( 'subscribe', 'yes', false, array( 'id' => 'slier' ), array(), '<input type="checkbox" name="subscribe" id="slier" class="subscribe_class" value="yes">' )


        );
    }


    public function dataProvidertestFileReturnsExpectedValue()
    {

        return array(

            array( 'image', array(), '<input type="file" name="image" id="image_id" class="image_class">' ),
            array( 'image', array( 'disabled' => true ), '<input type="file" name="image" id="image_id" class="image_class" disabled>' ),
            array( 'image', array( 'readonly' => true ), '<input type="file" name="image" id="image_id" class="image_class" readonly>' ),
            array( 'image', array( 'class' => 'slier' ), '<input type="file" name="image" id="image_id" class="slier">' ),
            array( 'image', array( 'id' => 'slier' ), '<input type="file" name="image" id="slier" class="image_class">' )

        );
    }


    public function dataProvidertestHiddenReturnsExpectedValue()
    {

        return array(


            array( 'user', '1', array(), array(), '<input type="hidden" name="user" value="1" id="user_id" class="user_class">' ),
            array( 'user', '1', array(), array( 'user' => 3 ), '<input type="hidden" name="user" value="3" id="user_id" class="user_class">' ),
            array( 'user', '1', array( 'id' => 'slier' ), array(), '<input type="hidden" name="user" value="1" id="slier" class="user_class">' ),
            array( 'user', '1', array( 'class' => 'slier' ), array(), '<input type="hidden" name="user" value="1" id="user_id" class="slier">' )
        );
    }


    public function dataProvidertestButtonReturnsExpectedValue()
    {

        return array(

            array( 'submit', 'Submit', array(), '<input type="button" name="submit" value="Submit" id="submit_id" class="submit_class">' ),
            array( 'submit', 'Submit', array( 'readonly' => true ), '<input type="button" name="submit" value="Submit" id="submit_id" class="submit_class" readonly>' ),
            array( 'submit', 'Submit', array( 'disabled' => true ), '<input type="button" name="submit" value="Submit" id="submit_id" class="submit_class" disabled>' ),
            array( 'submit', 'Submit', array( 'class' => 'slier' ), '<input type="button" name="submit" value="Submit" id="submit_id" class="slier">' ),
            array( 'submit', 'Submit', array( 'id' => 'slier' ), '<input type="button" name="submit" value="Submit" id="slier" class="submit_class">' )

        );
    }


    public function dataProvidertestSubmitReturnsExpectedValue()
    {

        return array(


            array( 'submit', 'Submit', array(), '<input type="submit" name="submit" id="submit_id" class="submit_class" value="Submit">' ),
            array( 'submit', 'Submit', array( 'readonly' => true ), '<input type="submit" name="submit" id="submit_id" class="submit_class" value="Submit" readonly>' ),
            array( 'submit', 'Submit', array( 'disabled' => true ), '<input type="submit" name="submit" id="submit_id" class="submit_class" value="Submit" disabled>' ),
            array( 'submit', 'Submit', array( 'class' => 'slier' ), '<input type="submit" name="submit" id="submit_id" class="slier" value="Submit">' ),
            array( 'submit', 'Submit', array( 'id' => 'slier' ), '<input type="submit" name="submit" id="slier" class="submit_class" value="Submit">' )

        );
    }


    public function dataProvidertestFormStartReturnsExpectedValue()
    {

        return array(


            array( 'index.php', array(), '<form name="form1" id="form1_id" class="form1_class" method="post" action="index.php" target="_self">' ),
            array( 'index.php', array( 'id' => 'slier' ), '<form name="form2" id="slier" class="form2_class" method="post" action="index.php" target="_self">' ),
            array( 'index.php', array( 'class' => 'slier' ), '<form name="form3" id="form3_id" class="slier" method="post" action="index.php" target="_self">' ),
            array( 'index.php', array( 'upload' => true ), '<form name="form4" id="form4_id" class="form4_class" method="post" action="index.php" target="_self" enctype="multipart/form-data">' ),
            array( 'index.php?id=1&lang=en', array(), '<form name="form5" id="form5_id" class="form5_class" method="post" action="index.php?id=1&lang=en" target="_self">' )

        );
    }


    public function dataProvidertestSelectMultipleMarkElementProperly()
    {

        /*$data = array( 'negeri' => 'pls',
            'state' => array( 0 => array( 0 => 'kdh' ), 1 => array( 0 => 'png', 1 => 'prk' ) ),
            'country' => array( 0 => array( 0 => 'slr' ) )
        );

        return $data;*/

        return array(

            array( 'negeri', array( '' => 'Please Select', 'pls' => 'Perlis', 'kdh' => 'Kedah', 'png' => 'Penang', 'prk' => 'Perak', 'slr' => 'Selangor' ), null, null, array( 'negeri' => 'pls' ), '<select name="negeri" id="negeri_id" class="negeri_class"><option value="">Please Select</option><option value="pls" selected>Perlis</option><option value="kdh">Kedah</option><option value="png">Penang</option><option value="prk">Perak</option><option value="slr">Selangor</option></select>' ),
            array( 'state[]', array( '' => 'Please Select', 'pls' => 'Perlis', 'kdh' => 'Kedah', 'png' => 'Penang', 'prk' => 'Perak', 'slr' => 'Selangor' ), null, null, array( 'state' => array( 0 => array( 0 => 'kdh' ) ) ), '<select name="state[0][]" id="state7_id" class="state_class"><option value="">Please Select</option><option value="pls">Perlis</option><option value="kdh" selected>Kedah</option><option value="png">Penang</option><option value="prk">Perak</option><option value="slr">Selangor</option></select>' ),
            array( 'state[]', array( '' => 'Please Select', 'pls' => 'Perlis', 'kdh' => 'Kedah', 'png' => 'Penang', 'prk' => 'Perak', 'slr' => 'Selangor' ), array( 'kdh', 'png' ), array( 'multiple' => true ), array( 'state' => array( 0 => array( 0 => 'png', 1 => 'prk' ) ) ), '<select name="state[1][]" id="state8_id" class="state_class" multiple><option value="">Please Select</option><option value="pls">Perlis</option><option value="kdh">Kedah</option><option value="png" selected>Penang</option><option value="prk" selected>Perak</option><option value="slr">Selangor</option></select>' ),
            array( 'country[]', array( '' => 'Please Select', 'pls' => 'Perlis', 'kdh' => 'Kedah', 'png' => 'Penang', 'prk' => 'Perak', 'slr' => 'Selangor' ), null, array( 'multiple' => true ), array( 'country' => array( 0 => array( 0 => 'slr' ) ) ), '<select name="country[0][]" id="country9_id" class="country_class" multiple><option value="">Please Select</option><option value="pls">Perlis</option><option value="kdh">Kedah</option><option value="png">Penang</option><option value="prk">Perak</option><option value="slr" selected>Selangor</option></select>' )


        );
    }


    public function dataProvidertestCheckboxMultipleMarkElementProperly()
    {
        return array(

            array( 'user[]', 'zack', null, null, array( 'user' => array( 0 => 'zack', 1 => 'slier' ) ), '<input type="checkbox" name="user[]" id="user5_id" class="user_class" value="zack" checked>' ),
            array( 'user[]', 'slier', null, null, array( 'user' => array( 0 => 'slier' ) ), '<input type="checkbox" name="user[]" id="user6_id" class="user_class" value="slier" checked>' )

        );
    }


    public function dataProvidertestRadioMultipleMarkElementProperly()
    {

        return array(

            array( 'gender[]', 'male', null, null, array( 'gender' => array( 0 => 'male' ) ), '<input type="radio" name="gender[]" id="gender10_id" class="gender_class" value="male" checked>' ),
            array( 'gender[]', 'female', null, null, array( 'gender' => array( 0 => 'female' ) ), '<input type="radio" name="gender[]" id="gender11_id" class="gender_class" value="female" checked>' )

        );
    }
}

?>


