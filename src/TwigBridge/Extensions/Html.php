<?php

namespace TwigBridge\Extensions;

use Illuminate\Foundation\Application;
use Meido\HTML\HTML as Meido_HTML;
use Meido\Form\Form as Meido_Form;
use Twig_Function_Method;

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

    public function setApp(Application $app)
    {
        parent::setApp($app);

        $this->html = new Meido_HTML($app);
        $this->form = new Meido_Form($app);
    }

    public function getName()
    {
        return 'Html';
    }

    public function getFunctions()
    {
        return array(
            'script' => new Twig_Function_Method($this, 'htmlScript'),
            'style'  => new Twig_Function_Method($this, 'htmlStyle'),
            'span'  => new Twig_Function_Method($this, 'htmlSpan'),
            'to'  => new Twig_Function_Method($this, 'htmlTo'),
        );
    }

    public function htmlScript($url, $attributes = array())
    {
        return $this->html->script($url, $attributes);
    }

    public function htmlStyle($url, $attributes = array())
    {
        return $this->html->style($url, $attributes);
    }

    public function htmlSpan($url, $attributes = array())
    {
        return $this->html->span($url, $attributes);
    }

    public function htmlTo($url, $title = null, $attributes = array(), $parameters = array(), $https = null)
    {
        return $this->html->to($url, $title, $attributes, $parameters, $https);
    }

    public function htmlSecure($url, $title = null, $parameters = array(), $attributes = array())
    {
        return $this->html->secure($url, $title, $parameters, $attributes);
    }
}