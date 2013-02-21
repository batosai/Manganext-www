<?php
class Form_Decorator_Email extends Zend_Form_Decorator_Abstract
{
    protected $_format = '<div class="clearfix">
                            %s
                            <div class="input">
                              <div class="input-prepend">
                                <span class="add-on">@</span>
                                %s
                              </div>
                            </div>
                            %s
                          </div>';
    public function render($content)
    {
        $element = $this->getElement();
        $view    = $element->getView();
        $helper  = $element->helper;

        if (empty($content)) {
          $content = $element->getValue();
        }

        $attribs = $element->getAttribs();
        unset($attribs['helper']);

        $name    = htmlentities($element->getFullyQualifiedName());
        $label   = $element->getLabel();
        $label   .= $element->isRequired() ? '* :' : ' :';
        $description = $element->getDescription() ? '<span class="help-block">' . $element->getDescription() . '</span>' : '';

        $markup  = sprintf(
                    $this->_format,
                    $view->formLabel($element->getName(), $label),
                    $view->$helper($name, $content, $attribs),
                    $description
                  );

        return $markup;
    }
}

