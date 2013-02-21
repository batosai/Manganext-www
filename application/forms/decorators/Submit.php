<?php
class Form_Decorator_Submit extends Zend_Form_Decorator_Abstract
{
    protected $_format = '<div class="actions">%s</div>';

    public function render($content)
    {
        $element = $this->getElement();
        $view    = $element->getView();
        $helper  = $element->helper;

        $attribs = $element->getAttribs();
        unset($attribs['helper']);

        $name    = htmlentities($element->getFullyQualifiedName());
        $label   = $element->getLabel();

        $markup  = sprintf(
                    $this->_format,
                    $view->$helper($name, $label, $attribs)
                  );

        return $markup;
    }
}

