<?php
/**
 * ZFDebug Doctrine ORM plugin from Danceric
 * 
 * @category   ZFDebug
 * @package    ZFDebug_Controller
 * @subpackage Plugins
 */
class ZFDebug_Controller_Plugin_Debug_Plugin_Doctrine
    extends ZFDebug_Controller_Plugin_Debug_Plugin
    implements ZFDebug_Controller_Plugin_Debug_Plugin_Interface
{
    /**
     * Contains plugin identifier name
     *
     * @var string
     */
    protected $_identifier = 'doctrine';

    /**
     * @var array Doctrine connection profiler that will listen to events
     */
    protected $_profilers = array();

    /**
     * Create ZFDebug_Controller_Plugin_Debug_Plugin_Variables
     *
     * @param Doctrine_Manager|array $options
     * @return void
     */
    public function __construct(array $options = array())
    {
        if(!isset($options['manager']) || !count($options['manager'])) {
            if (Doctrine_Manager::getInstance()) {
                $options['manager'] = Doctrine_Manager::getInstance();
            }
        }

        foreach ($options['manager']->getIterator() as $connection) {
            $this->_profilers[$connection->getName()] = new Doctrine_Connection_Profiler();
            $connection->addListener($this->_profilers[$connection->getName()]);
        }
    }

    /**
     * Gets identifier for this plugin
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->_identifier;
    }

    /**
     * Gets menu tab for the Debugbar
     *
     * @return string
     */
    public function getTab()
    {
        if (!$this->_profilers) {
            return 'No Profiler';
        }

        foreach ($this->_profilers as $profiler) {
            $queries = 0;
            $time = 0;
            foreach ($profiler as $event) {
                if (in_array($event->getCode(), $this->getQueryEventCodes())) {
                    $time += $event->getElapsedSecs();
                    $queries += 1;
                }
            }
            $profilerInfo[] = $queries . ' in ' . round($time*1000, 2)  . ' ms';
        }
        $html = implode(' / ', $profilerInfo);

        return $html;
    }

    /**
     * Gets content panel for the Debugbar
     *
     * @return string
     */
    public function getPanel()
    {
        if (!$this->_profilers) {
            return '';
        }

        $html = '<h4>Database queries</h4>';
        
        foreach ($this->_profilers as $name => $profiler) {
                $html .= '<h4>Connection: '.$name.'</h4><ol>';
                foreach ($profiler as $event) {
                    if (in_array($event->getCode(), $this->getQueryEventCodes())) {
                        $info = htmlspecialchars($event->getQuery());
                        $info = preg_replace('/\b(SELECT|AS|FROM|WHERE|ORDER BY|GROUP BY|LIMIT|ON|LEFT JOIN|JOIN)\b/','<strong>$1</strong>', $info);
                        
                        $html .= '<li><strong>[' . round($event->getElapsedSecs()*1000, 2) . ' ms]</strong> ';
                        $html .= $info;
                
                        $params = $event->getParams();
                        if(!empty($params)) {
                            $params = array_map('htmlspecialchars', $params);
                            $html .= '<ul><em>bindings:</em> <li>'. implode('</li><li>', $params) . '</li></ul>';
                        }
                        $html .= '</li>';
                    }
                }
                $html .= '</ol>';
        }

        return $html;
    }
    
    /**
     * return codes for 'query' type of event
     */
    protected function getQueryEventCodes()
    {
        return array(
            Doctrine_Event::CONN_EXEC, 
            Doctrine_Event::STMT_EXECUTE,
            Doctrine_Event::CONN_QUERY,
        );
    }

    /**
     * Returns the base64 encoded icon
     *
     * Doctrine Icon will be used if you're using ZFDebug > 1.5
     * icon taken from: http://code.google.com/p/zfdebug/issues/detail?id=20
     *
     * @return string
     **/
    public function getIconData()
    {
        return 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYwIDYxLjEzNDc3NywgMjAxMC8wMi8xMi0xNzozMjowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNSBNYWNpbnRvc2giIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6MTRDMkVCNzlFMDdDMTFFMEI4MUFEQzlCRjkxNDJENDgiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6MTRDMkVCN0FFMDdDMTFFMEI4MUFEQzlCRjkxNDJENDgiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDoxNEMyRUI3N0UwN0MxMUUwQjgxQURDOUJGOTE0MkQ0OCIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDoxNEMyRUI3OEUwN0MxMUUwQjgxQURDOUJGOTE0MkQ0OCIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PgIacrAAAAJwSURBVHjafJNNaFNBEMf/sy95aZKCFbS0Sg+2CnpoiwpWKAa0FJSA3wjiQa0eFT0oiooFLXoTFDx5Eg8iVTxoBUEtQpGCYvVSKz1IFZHEojE2efl4u+PkvfqSgHVhl/2a3+z8Z5aYGflLG/G3hUklQ2G+Cqhh4/IQERggaMNQBFgcAowFDI5690OVocFSAUDuDrGmToA7SaFddgYEwVigeZZKiF53FcjQo+CUcUjGh+I4/F8Afmrgl4YRl+LqIikarIHsYMKIQBoXBmSLQK4Eo8oSgu6VoQulPFBy5iHcL+zbMov8G2CR1xXRZdkaY6330IZ9oLXbfYgILTd2K8UjBtzgwA0Anoi8XMDEJ2R6AUYOF7WAtp4WqIS+tB389BpgxwSi+uZU4e4Ep3clal9QKuomtuhcJWAY0WN2Bvx62E9K70FQ8izgFmBYIw57ZyLSlqgLwco768W4uZJDtKwG2rrB7yQZnyd8SM9+0JZjgHY9VdnRfXUhqGXxVfidA206DOo/WVVIPAblkTgCOBnwqzvgULSDagFUdrKsZJr5JhlJi6GRJ5dE85gkb8k8zPjnSh7NNFcvYjH+FmE2/OGF4ukx/3JsMdSBGz5A1nzvFHjyORBtBGVy43UaUEdmimw9CrL9OCXpau8VoHmlt+YH533jSLwiwWzepif1gO6vItSXAdg841V9rAloXSNhFMH3z4DfP/aM5WO5P3Lu0e9FlQ60qfxGT33Xgnu9a4VViN5k424j8c5adEhN+zVAmBJNj0+mys9yRUbPrTdVDarNfJI3JontdUh93ExErYjGs/Kjx6H1SzaqoPyiDdofAQYApSb6lAC48JsAAAAASUVORK5CYII=';
    }
}