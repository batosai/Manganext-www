<?php
class Form_Decorator_ElementCustom extends Zend_Form_Decorator_Abstract
{
    protected $_format = '<div class="clearfix">
                            %s
                            <div class="input">%s</div>
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
        $label  .= $element->isRequired() ? '* :' : ' :';
        $description = $element->getDescription() ? '<span class="help-block">' . $element->getDescription() . '</span>' : '';

        $viewHelper = $view->$helper($name, $content, $attribs);

        if (method_exists($element,'getMultiOptions')) {
          $viewHelper = $view->$helper($name, $content, $attribs, $element->getMultiOptions());
        }

        if ($helper == 'formHidden') {
          $markup  = sprintf(
                      $this->_format,
                      '',
                      $viewHelper,
                      ''
                    );
        }
        else {
          $markup  = sprintf(
                      $this->_format,
                      $view->formLabel($element->getName(), $label),
                      $viewHelper,
                      $description
                    );
        }

        return $markup;
    }
}

