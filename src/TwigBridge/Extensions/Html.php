<?php

namespace TwigBridge\Extensions;

use Illuminate\Foundation\Application;
use Twig_Function_Method;
use Meido\HTML\HTML as Meido_HTML;
use Meido\Form\Form as Meido_Form;

class Html extends Extension
{
    /**
     * @var Meido\HTML\HTML
     */
    protected $html;

    /**
     * @var Meido\Form\Form
     */
    protected $form;

    /**
     * @var array
     */
    protected $replace = array(
        'secureasset'        => 'secureAsset',
        'opensecure'         => 'openSecure',
        'openforfiles'       => 'openForFiles',
        'opensecureforfiles' => 'openSecureForFiles',
    );

    public function setApp(Application $app)
    {
        $app['html'] = new Meido_HTML($app['url']);
        $app['form'] = new Meido_Form($app);

        parent::setApp($app);
    }

    public function getName()
    {
        return 'Html';
    }

    public function getFunctions()
    {
        return array(
            'html_script'       => new Twig_Function_Method($this, 'htmlScript'),
            'html_style'        => new Twig_Function_Method($this, 'htmlStyle'),
            'html_span'         => new Twig_Function_Method($this, 'htmlSpan'),
            'html_to'           => new Twig_Function_Method($this, 'htmlTo'),
            'html_asset'        => new Twig_Function_Method($this, 'htmlAsset'),
            'html_secure_asset' => new Twig_Function_Method($this, 'htmlSecureAsset'),
            'html_route'        => new Twig_Function_Method($this, 'htmlRoute'),
            'html_action'       => new Twig_Function_Method($this, 'htmlAction'),
            'html_mailto'       => new Twig_Function_Method($this, 'htmlMailto'),
            'html_email'        => new Twig_Function_Method($this, 'htmlEmail'),
            'html_image'        => new Twig_Function_Method($this, 'htmlImage'),
            'html_ol'           => new Twig_Function_Method($this, 'htmlOl'),
            'html_ul'           => new Twig_Function_Method($this, 'htmlUl'),
            'html_listing'      => new Twig_Function_Method($this, 'htmlListing'),
            'html_dl'           => new Twig_Function_Method($this, 'htmlDl'),

            'form_open'              => new Twig_Function_Method($this, 'formOpen'),
            'form_open_secure'       => new Twig_Function_Method($this, 'formOpenSecure'),
            'form_open_files'        => new Twig_Function_Method($this, 'formOpenForFiles'),
            'form_open_secure_files' => new Twig_Function_Method($this, 'formOpenSecureForFiles'),
            'form_close'             => new Twig_Function_Method($this, 'formClose'),
            'form_token'             => new Twig_Function_Method($this, 'formToken'),
            'csrf_token'             => new Twig_Function_Method($this, 'formToken'),
            'form_label'             => new Twig_Function_Method($this, 'formLabel'),
            'form_input'             => new Twig_Function_Method($this, 'formInput'),
            'form_text'              => new Twig_Function_Method($this, 'formText'),
            'form_password'          => new Twig_Function_Method($this, 'formPassword'),
            'form_hidden'            => new Twig_Function_Method($this, 'formHidden'),
            'form_search'            => new Twig_Function_Method($this, 'formSearch'),
            'form_email'             => new Twig_Function_Method($this, 'formEmail'),
            'form_telephone'         => new Twig_Function_Method($this, 'formTelephone'),
            'form_url'               => new Twig_Function_Method($this, 'formUrl'),
            'form_number'            => new Twig_Function_Method($this, 'formNumber'),
            'form_date'              => new Twig_Function_Method($this, 'formDate'),
            'form_file'              => new Twig_Function_Method($this, 'formFile'),
            'form_textarea'          => new Twig_Function_Method($this, 'formTextarea'),
            'form_select'            => new Twig_Function_Method($this, 'formSelect'),
            'form_checkbox'          => new Twig_Function_Method($this, 'formCheckbox'),
            'form_radio'             => new Twig_Function_Method($this, 'formRadio'),
            'form_submit'            => new Twig_Function_Method($this, 'formSubmit'),
            'form_reset'             => new Twig_Function_Method($this, 'formReset'),
            'form_image'             => new Twig_Function_Method($this, 'formImage'),
            'form_button'            => new Twig_Function_Method($this, 'formButton'),
        );
    }

    public function __call($name, $arguments)
    {
        $type = substr($name, 0, 4);          // html or form
        $name = strtolower(substr($name, 4)); // function name

        if (in_array($name, array_keys($this->replace))) {
            $name = $this->replace[$name];
        }

        return call_user_func_array(array($this->app->$type, $name), $arguments);
    }
}
