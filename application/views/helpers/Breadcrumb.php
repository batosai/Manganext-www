<?php

class Zend_View_Helper_Breadcrumb extends Zend_View_Helper_Abstract
{
  public function breadcrumb($breadcrumb)
  {
    $view = '<ul class="breadcrumb">';

    foreach ($breadcrumb as $i => $b)
    {
      $b['active'] = !isset($b['active']) ? false: $b['active'];

      if (!$b['active']) {
        $view .= '<li><a href="' . $b['url'] . '">' . $b['name'] . '</a>';
        if ($i != count($breadcrumb)-1) {
          $view .= ' <span class="divider">/</span>';
        }
        $view .= '</li>';
      }
      else {
        $view .= '<li class="active">' . $b['name'] . '</li>';
      }
    }

    $view .= '</ul>';

    return $view;
  }
}