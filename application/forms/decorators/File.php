<?php
class Form_Decorator_File extends Zend_Form_Decorator_File
{
    protected $_format = '<div class="clearfix">
                            %s
                            <div class="input">%s</div>
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
        $label   .= $element->isRequired() ? '* :' : ' :';

        $markup  = sprintf(
                    $this->_format,
                    $view->formLabel($element->getName(), $label),
                    $view->$helper($name, $attribs)
                  );

        return $markup;
    }
}
