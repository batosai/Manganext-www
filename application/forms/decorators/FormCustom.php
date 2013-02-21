<?php
class Form_Decorator_FormCustom extends Zend_Form_Decorator_Form
{
    public function render($content)
    {
        $form    = $this->getElement();
        $view    = $form->getView();
        if (null === $view) {
            return $content;
        }

        if (empty($content))
        {
          foreach ($form->getElements() as $element)
          {
            $content .= $element->render();
          }
        }

        if ($form->getTitle()) {
          $content = "<legend>{$form->getTitle()}</legend>$content";
        }

        $content = "<fieldset>{$view->flashMessenger()}$content</fieldset>";

        $helper  = $this->getHelper();
        $attribs = $this->getOptions();
        $name    = $form->getFullyQualifiedName();
        return $view->$helper($name, $form->getAttribs(), $content);
    }
}
