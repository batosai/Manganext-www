<?php
class Form_Decorator_Checkbox extends Zend_Form_Decorator_Abstract
{
    protected $_format = '<div class="clearfix elementCheckbox">
                            %s %s %s
                          </div>';
    public function render($content)
    {
        $element = $this->getElement();
        $view    = $element->getView();
        $helper  = $element->helper;

        $attribs = $element->getAttribs();
        unset($attribs['helper']);

        $name    = htmlentities($element->getFullyQualifiedName());
        $label   = $element->getLabel();
        $label   .= $element->isRequired() ? '*' : '';
        $description = $element->getDescription() ? '<span class="help-block">' . $element->getDescription() . '</span>' : '';

        $markup  = sprintf(
                    $this->_format,
                    $view->$helper($name, 1, $attribs),
                    $view->formLabel($element->getName(), $label),
                    $description
                  );

        return $markup;
    }
}

