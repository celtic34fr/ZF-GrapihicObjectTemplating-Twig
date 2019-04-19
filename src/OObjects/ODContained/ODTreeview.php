<?php

namespace GraphicObjectTemplating\OObjects\ODContained;

use GraphicObjectTemplating\OObjects\ODContained;
use Zend\ServiceManager\ServiceManager;

/**
 * Class ODTreeview
 * @package GraphicObjectTemplating\OObjects\ODContained
 *
 * __construct($id)         constructeur de l'objet, obligation de fournir $id identifiant de l'objet
 * addLeaf($ref, $libel, $ord = null, $parent = null)
 * setLeaf($libel, $path)
 * getLeaf($ref)
 * enaIcon()
 * disIcon()
 * setLeafIco($leafIco)
 * getLeafIco()
 * setNodeOpenedIco($nodeOpenedIco)
 * getNodeOpenedIco()
 * setNodeClosedIco($nodeClosedIco)
 * getNodeClosedIco()
 * setSelectedLeaves(array $selectedLeaves)
 * getSelectedLeaves()
 * #evtClick($class, $method, $stopEvent = false)
 * #getClick()
 * #disClick()
 * enaMultiSelect
 * disMultiSelect
 * rmLeafNode($ref, $root = true)
 * setTitle($title)
 * getTitle()
 * setLeafLink($ref, $link, $target)
 * getLeafLink($ref)
 * enaSortable()
 * disSortable()
 * enaSelection()
 * disSelection()
 * selectNode($ref)
 * unselectNode($ref)
 * enaSelectNode($ref)
 * disSelectNode($ref)
 * enaSortableNode($ref, $andChilmdren = false)
 * disSortableNode($ref, $andChilmdren = false)
 * evtClickNode($class, $method, $stopEvent = false)
 * getClickNode()
 * disClickNode()
 * getParent($ref)
 * getChildLeaves($ref, $level = 0)
 *
 * méthodes de gestion de retour de callback
 * -----------------------------------------
 * dispatchEvents(ServiceManager $sm, array $params)
 * returnAddLeaf($parentPath, $ord)
 *
 * méthodes privées de la classe
 * -----------------------------
 * updateTree($tree, $path, $item)
 * validRefUnique($ref)
 * rmLeafTree($dataTree, $dataPath)
 * getTargetConstants()
 */
class ODTreeview extends ODContained
{

    const ODTREEVIEWTARGET_SELF     = '_self';
    const ODTREEVIEWTARGET_BLANK    = '_blank';
    const ODTREEVIEWTARGET_PARENT   = '_parent';
    const ODTREEVIEWTARGET_TOP      = '_top';

    const COLORCLASS_BLACK          = 'black';
    const COLORCLASS_DARKGREY       = 'dark-gray';
    const COLORCLASS_GRAY           = 'gray';
    const COLORCLASS_MIDGRAY        = 'mid-gray';
    const COLORCLASS_SILVER         = 'silver';
    const COLORCLASS_LIGHTGRAY      = 'light-gray';
    const COLORCLASS_WHITE          = 'white';

    const COLORCLASS_AQUA           = 'aqua';
    const COLORCLASS_BLUE           = 'blue';
    const COLORCLASS_NAVY           = 'navy';
    const COLORCLASS_TEAL           = 'teal';
    const COLORCLASS_GREEN          = 'green';
    const COLORCLASS_OLIVE          = 'olive';
    const COLORCLASS_LIME           = 'lime';

    const COLORCLASS_YELLOW         = 'yellow';
    const COLORCLASS_ORANGE         = 'orange';
    const COLORCLASS_RED            = 'red';
    const COLORCLASS_FUCHSIA        = 'fuchsia';
    const COLORCLASS_PURPLE         = 'purple';
    const COLORCLASS_MAROON         = 'maroon';


    private $const_target;


    /**
     * ODTreeview constructor.
     * @param $id
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function __construct($id)
    {
        parent::__construct($id, "oobjects/odcontained/odtreeview/odtreeview.config.php");

        $properties = $this->getProperties();
        if ($properties['id']) {
            $this->setDisplay();
            $width = $this->getWidthBT();
            if (!is_array($width) || empty($width)) $this->setWidthBT(12);
            $this->enable();
        }

        $this->saveProperties();
        return $this;
    }

    /**
     * @param $ref
     * @param $libel
     * @param null $ord
     * @param string $parent
     * @return ODTreeview|bool
     */
    public function addLeaf($ref, $libel, $ord = null, $parent = "0")
    {
        $properties = $this->getProperties();
        $ord        = (int) $ord;
        $parent     = (string) $parent;
        $ref        = (string) $ref;
        $libel      = (string) $libel;
        if (empty($parent)) { $parent = '0'; }

        $dataTree   = $properties['dataTree'];
        $dataPath   = $properties['dataPath'];
        $validAct   = false;

        if ($this->validRefUnique($ref)) {
            if ($parent == '0') {
                if ($ord == 0) { $ord = max(array_keys($dataTree)) + 1; }
                if (!isset($dataTree[$ord])) {
                    $item = [];
                    $item['libel']      = $libel;
                    $item['ord']        = $ord;
                    $item['ref']        = $ref;
                    $item['icon']       = 'none';
                    $item['link']       = 'none';
                    $item['targetL']    = 'none';
                    $item['parent']     = '0';
                    $item['check']      = false;
                    $item['selectable'] = true;
                    $item['sortable']	= true;

                    $dataTree[$ord]     = $item;
                    $dataPath[$ref]     = $ord;
                    $validAct           = true;
                    ksort($dataTree);
                }
            } else {
                $leaf   = $this->getLeaf($parent);
                if ($ord == 0 || !isset($leaf['children'][$ord])) {
                    if ($ord == 0) { $ord = max(array_keys($leaf['children'])) + 1; }

                    $item = [];
                    $item['libel']      = $libel;
                    $item['ord']        = $ord;
                    $item['ref']        = $ref;
                    $item['icon']       = 'none';
                    $item['link']       = 'none';
                    $item['targetL']    = 'none';
                    $item['parent']     = $parent;
                    $item['check']      = false;
                    $item['selectable'] = true;
                    $item['sortable']	= true;

                    $path               = explode('-', $dataPath[$parent]);
                    if ((int) $path[0] == 0) { unset($path[0]); }

                    $dataTree           = $this->updateTree($dataTree, $path, $item, true);
                    $dataPath[$ref]     = $dataPath[$parent].'-'.$ord;
                    $validAct           = true;
                }
            }

            if ($validAct) {
                $properties['dataTree'] = $dataTree;
                $properties['dataPath'] = $dataPath;
                $this->setProperties($properties);
                return $this;
            }
        }
        return false;
    }

    /**
     * @param $libel
     * @param $path
     * @return ODTreeview|bool
     */
    public function setLeaf($libel, $path)
    {
        $properties = $this->getProperties();
        $refs       = explode('.', $path);
        $leaf       = $properties['dataTree'];
        $dataTree   = $properties['dataTree'];
        $found      = true;
        foreach ($refs as $ref) {
            if (array_key_exists('children', $leaf)) {
                if (isset($leaf['children'][$ref])) {
                    $leaf = $leaf['children'][$ref];
                } else {
                    $found = false;
                    break;
                }
            } else {
                $found = false;
                break;
            }
        }
        if ($found) {
            $leaf['libel']          = $libel;
            $dataTree               = $this->updateTree($dataTree, $path, $leaf);
            $properties['dataTree'] = $dataTree;
            $this->setProperties($properties);
            return $this;
        }
        return false;
    }

    /**
     * @param $ref
     * @return bool|array
     */
    public function getLeaf($ref)
    {
        $properties = $this->getProperties();
        $dataPath   = $properties['dataPath'];

        return $this->getLeafByPath($dataPath[$ref]);
    }

    /**
     * @param null $path
     * @return bool|array
     */
    public function getLeafByPath($path = null)
    {
        $properties = $this->getProperties();
        $leaf       = $properties['dataTree'];
        $found      = true;

        if ($path != '') {
            $first      = true;
            $refs       = explode('-', $path);
            if ((int) $refs[0] == 0) { unset($refs[0]); }
            foreach ($refs as $ref) {
                if ($first) {
                    $leaf = $leaf[$ref];
                    $first = false;
                } else {
                    if (array_key_exists('children', $leaf)) {
                        $children   = $leaf['children'];
                        if (array_key_exists($ref, $children)) {
                            $leaf = $children[$ref];
                        } else {
                            $found = false;
                            break;
                        }
                    } else {
                        $found = false;
                        break;
                    }
                }
            }
        }
        return ($found) ? $leaf : false;
    }

    /**
     * @return ODTreeview
     */
    public function enaIcon()
    {
        $properties = $this->getProperties();
        $properties['icon'] = true;
        $this->setProperties($properties);
        return $this;
    }

    /**
     * @return ODTreeview
     */
    public function disIcon()
    {
        $properties = $this->getProperties();
        $properties['icon'] = false;
        $this->setProperties($properties);
        return $this;
    }

    /**
     * @param $leafIco
     * @return ODTreeview
     */
    public function setLeafIco($leafIco)
    {
        $properties = $this->getProperties();
        $leafIco    = (string) $leafIco;
        $properties['leafIco']  = $leafIco;
        $this->setProperties($properties);
        return $this;
    }

    /**
     * @return bool
     */
    public function getLeafIco()
    {
        $properties = $this->getProperties();
        return array_key_exists('leafIco', $properties) ? $properties['leafIco'] : false;
    }

    /**
     * @param $nodeOpenedIco
     * @return ODTreeview
     */
    public function setNodeOpenedIco($nodeOpenedIco)
    {
        $properties     = $this->getProperties();
        $nodeOpenedIco  = (string) $nodeOpenedIco;
        $properties['nodeOpenedIco']  = $nodeOpenedIco;
        $this->setProperties($properties);
        return $this;
    }

    /**
     * @return bool
     */
    public function getNodeOpenedIco()
    {
        $properties = $this->getProperties();
        return array_key_exists('nodeOpenedIco', $properties) ? $properties['nodeOpenedIco'] : false;
    }

    /**
     * @param $nodeClosedIco
     * @return ODTreeview
     */
    public function setNodeClosedIco($nodeClosedIco)
    {
        $properties     = $this->getProperties();
        $nodeClosedIco  = (string) $nodeClosedIco;
        $properties['nodeClosedIco']  = $nodeClosedIco;
        $this->setProperties($properties);
        return $this;
    }

    /**
     * @return bool
     */
    public function getNodeClosedIco()
    {
        $properties = $this->getProperties();
        return array_key_exists('nodeClosedIco', $properties) ? $properties['nodeClosedIco'] : false;
    }

    /**
     * @param array $selectedLeaves
     * @return ODTreeview
     */
    public function setSelectedLeaves(array $selectedLeaves)
    {
        $properties = $this->getProperties();
        $properties['dataSelected'] = $selectedLeaves;
        $this->setProperties($properties);
        return $this;
    }

    /**
     * @return bool|array
     */
    public function getSelectedLeaves()
    {
        $properties = $this->getProperties();
        return array_key_exists('dataSelected', $properties) ? $properties['dataSelected'] : false;
    }

    /**
     * @param $class
     * @param $method
     * @param bool $stopEvent
     * @return bool|ODTreeview
     */
    public function evtClick($class, $method, $stopEvent = false)
    {
        if (!empty($class) && !empty($method)) {
            return $this->setEvent('click', $class, $method, $stopEvent);
        }
        return false;
    }

    /**
     * @return bool
     */
    public function getClick()
    {
        return $this->getEvent('click');
    }

    /**
     * @return bool|ODTreeview
     */
    public function disClick()
    {
        return $this->disEvent('click');
    }

    /**
     * @return ODTreeview
     */
    public function enaMultiSelect()
    {
        $properties = $this->getProperties();
        $properties['multiSelect'] = true;
        $this->setProperties($properties);
        return $this;
    }

    /**
     * @return ODTreeview
     */
    public function disMultiSelect()
    {
        $properties = $this->getProperties();
        $properties['multiSelect'] = false;
        $this->setProperties($properties);
        return $this;
    }

    /**
     * @param $ref
     * @param bool $root
     * @return ODTreeview|bool
     */
    public function rmLeafNode($ref, $root = true)
    {
        $leaf       = $this->getLeaf($ref);

        if ($leaf) {
            if (array_key_exists('children', $leaf)) {
                $children = $leaf['children'];
                foreach ($children as $child) {
                    $this->rmLeafNode($child['ref'], false);
                }
            }

            $properties = $this->getProperties();
            $dataPath   = $properties['dataPath'];
            if ($root) {
                $dataTree   = $properties['dataTree'];
                $dataPathL  = explode('-', $dataPath[$ref]);
                $dataTree   = $this->rmLeafTree($dataTree, $dataPathL);
            }
            unset($dataPath[$ref]);
            $properties['dataPath'] = $dataPath;
            $properties['dataTree'] = $dataTree;

            $this->setProperties($properties);
            return $this;
        }
        return false;
    }

    /**
     * @param string $title
     * @return ODTreeview
     */
    public function setTitle($title = "")
    {
        $title = (string) $title;
        $properties = $this->getProperties();
        $properties['title'] = $title;
        $this->setProperties($properties);
        return $this;
    }

    /**
     * @return bool
     */
    public function getTitle()
    {
        $properties             = $this->getProperties();
        return (array_key_exists('title', $properties)) ? $properties['title'] : false ;
    }

    /**
     * @param $ref
     * @param $link
     * @param string $target
     * @return ODTreeview|bool
     * @throws \ReflectionException
     */
    public function setLeafLink($ref, $link, $target = self::ODTREEVIEWTARGET_SELF)
    {
        $leaf = $this->getLeaf($ref);
        if ($leaf) {
            $properties     = $this->getProperties();
            $dataTree       = $properties['dataTree'];
            $dataPath       = $properties['dataPath'];

            $leaf['link']   = $link;
            $targets        = $this->getTargetConstants();
            if (!in_array($target, $targets)) { $target = self::ODTREEVIEWTARGET_SELF; }

            $dataTree       = $this->updateTree($dataTree, $dataPath[$ref], $leaf);
            $properties['dataTree'] = $dataTree;
            $this->setProperties($properties);
            return $this;
        }
        return false;
    }

    /**
     * @param $ref
     * @return bool|string
     */
    public function getLeafLink($ref)
    {
        $leaf = $this->getLeaf($ref);
        if ($leaf) {
            return $leaf['link'];
        }
        return false;
    }

    /**
     * @return ODTreeview
     */
    public function enaSortable()
    {
        $properties = $this->getProperties();
        $properties['sortable'] = true;
        $this->setProperties($properties);
        return $this;
    }

    /**
     * @return ODTreeview
     */
    public function disSortable()
    {
        $properties = $this->getProperties();
        $properties['sortable'] = false;
        $this->setProperties($properties);
        return $this;
    }

    /**
     * @return ODTreeview
     */
    public function enaSelection()
    {
        $properties = $this->getProperties();
        $properties['selection'] = true;
        $this->setProperties($properties);
        return $this;
    }

    /**
     * @return ODTreeview
     */
    public function disSelection()
    {
        $properties = $this->getProperties();
        $properties['selection'] = false;
        $this->setProperties($properties);
        return $this;
    }

    /**
     * @param $ref
     * @return ODTreeview|bool
     */
    public function selectNode($ref)
    {
        $leaf   = $this->getLeaf($ref);
        if ($leaf) {
            $properties     = $this->getProperties();
            $dataTree       = $properties['dataTree'];
            $dataPath       = $properties['dataPath'];

            $leaf['check'] = true;

            $dataTree       = $this->updateTree($dataTree, $dataPath[$ref], $leaf);
            $properties['dataTree'] = $dataTree;
            $this->setProperties($properties);
            return $this;
        }
        return false;
    }

    /**
     * @param $ref
     * @return ODTreeview|bool
     */
    public function unselectNode($ref)
    {
        $leaf   = $this->getLeaf($ref);
        if ($leaf) {
            $properties     = $this->getProperties();
            $dataTree       = $properties['dataTree'];
            $dataPath       = $properties['dataPath'];

            $leaf['check']  = false;

            $dataTree       = $this->updateTree($dataTree, $dataPath[$ref], $leaf);
            $properties['dataTree'] = $dataTree;
            $this->setProperties($properties);
            return $this;
        }
        return false;
    }

    /**
     * @param $ref
     * @return ODTreeview|bool
     */
    public function enaSelectNode($ref) {
        $leaf   = $this->getLeaf($ref);
        if ($leaf) {
            $properties     = $this->getProperties();
            $dataTree       = $properties['dataTree'];
            $dataPath       = $properties['dataPath'];

            $leaf['selectable']  = true;

            $dataTree       = $this->updateTree($dataTree, $dataPath[$ref], $leaf);
            $properties['dataTree'] = $dataTree;
            $this->setProperties($properties);
            return $this;
        }
        return false;
    }

    /**
     * @param $ref
     * @return ODTreeview|bool
     */
    public function disSelectNode($ref) {
        $leaf   = $this->getLeaf($ref);
        if ($leaf) {
            $properties     = $this->getProperties();
            $dataTree       = $properties['dataTree'];
            $dataPath       = $properties['dataPath'];

            $leaf['selectable']  = false;

            $dataTree       = $this->updateTree($dataTree, $dataPath[$ref], $leaf);
            $properties['dataTree'] = $dataTree;
            $this->setProperties($properties);
            return $this;
        }
        return false;
    }

    /**
     * @param $ref
     * @param bool $andChildren
     * @return ODTreeview|bool
     */
    public function enaSortableNode($ref, $andChildren = false) {
		$leaf	= $this->getLeaf($ref);
		if (!empty($leaf)) {
			$properties				= $this->getProperties();
			$dataTree       		= $properties['dataTree'];
            $dataPath       		= $properties['dataPath'];

			$leaf['sortable']		= true;

			if (isset($leaf['children']) && !empty($leaf['children']) && $andChildren) {
				foreach ($leaf['children'] as $child) {
					if (!$this->enaSortableNode($child['ref'], $andChildren)) {
						return false;
					}
				}
			}

            $dataTree       		= $this->updateTree($dataTree, $dataPath[$ref], $leaf);
            $properties['dataTree'] = $dataTree;
            $this->setProperties($properties);
            return $this;
		}
		return false;
	}

    /**
     * @param $ref
     * @param bool $andChildren
     * @return ODTreeview|bool
     */
    public function disSortableNode($ref, $andChildren = false) {
		$leaf	= $this->getLeaf($ref);
		if (!empty($leaf)) {
			$properties				= $this->getProperties();
			$dataTree       		= $properties['dataTree'];
            $dataPath       		= $properties['dataPath'];

			$leaf['sortable']		= false;

			if (isset($leaf['children']) && !empty($leaf['children']) && $andChildren) {
				foreach ($leaf['children'] as $child) {
					if (!$this->disSortableNode($child['ref'], $andChildren)) {
						return false;
					}
				}
			}

            $dataTree       		= $this->updateTree($dataTree, $dataPath[$ref], $leaf);
            $properties['dataTree'] = $dataTree;
            $this->setProperties($properties);
            return $this;
		}
		return false;
	}

    /**
     * @param $class
     * @param $method
     * @param bool $stopEvent
     * @return bool|ODTreeview
     */
    public function evtClickNode($class, $method, $stopEvent = false)
    {
        if (!empty($class) && !empty($method)) {
            return $this->setEvent('clickNode', $class, $method, $stopEvent);
        }
        return false;
	}

    /**
     * @return bool
     */
    public function getClickNode()
    {
        return $this->getEvent('clickNode');
    }

    /**
     * @return bool|ODTreeview
     */
    public function disClickNode()
    {
        return $this->disEvent('clickNode');
    }

    /**
     * @param $ref
     * @return bool|mixed
     */
    public function getParent($ref)
    {
        $leaf   = $this->getLeaf($ref);
        if ($leaf) { return $leaf['parent']; }
        return false;
    }

    /**
     * @param $ref
     * @param bool $level
     * @return array
     */
    public function getChildLeaves($ref, $level = false)
    {
        $leaf       = $this->getLeaf($ref);
        $children   = [];

        if (array_key_exists('children', $leaf)) {
            foreach ($leaf['children'] as $child) {
                if ($level && $level > 0 || $level === false) {
                    if (!$level) { $level--; }
                    $children[] = $child['ref'];

                    $children = array_merge($children, $this->getChildLeaves($child['ref'], $level));
                }
            }
        }
        return $children;
    }

    /** **************************************************************************************************
     * méthodes de gestion de retour de callback                                                         *
     * *************************************************************************************************** */

    /**
     * @param ServiceManager $sm
     * @param array $params
     * @return array
     * @throws \Exception
     */
    public function dispatchEvents(ServiceManager $sm, array $params)
    {
        $sessionObjects     = self::validateSession();
        $ret                = [];
        /** @var ODTreeview $object */
        $object             = self::buildObject($params['id'], $sessionObjects);
		switch ($params['event']) {
			case 'click':
			    $value      = $params['value'];
			    $leaves     = $object->getSelectedLeaves();
                foreach ($leaves as $leaf) {
                    $object->unselectNode($leaf);
			    }
			    $object->setSelectedLeaves($value["selected"]);
                foreach ($value["selected"] as $select) {
                    $object->selectNode($select);
			    }
                $object->saveProperties();

                $callback = $this->getClickNode();
                if (!empty($callback['class']) && !empty($callback['method'])) {
                    $results = call_user_func_array([$callback['class'], $callback['method']], [$sm, $params]);
                    $ret     = array_merge($ret, $results);
                }
				break;
			case 'sortupdate':
				break;
		}
		$object->saveProperties();
		return $ret;
	}

    /**
     * @param $parentPath
     * @param $ord
     * @return array
     */
    public function returnAddLeaf($parentPath, $ord)
    {
        $parentPath = (string) $parentPath;
        $ord        = (int) $ord;
        $ret        = [];

        if ($parentPath == "0" || empty($parentPath)) {
            $dataLvl    = "0";
            $dataOrd    = "0";
            $parent     = null;
        } else {
            $dataLvl    = $parentPath;
            $parent     = $this->getLeafByPath($parentPath);
            $dataOrd    = $parent['ord'];
        }
        $pathChild  = $dataLvl.'-'.$ord;
        $child      = $this->getLeafByPath($pathChild);

        // traitement ajout de feuille enfant
        $line   = '<li id="'.$this->getId().'Li-'.$dataLvl.'-'.$ord.'" class="leaf">';
        $itemIco = ($child['icon'] == 'none') ? $this->getLeafIco() : $child['icon'];
        $line .= '<i class="'.$itemIco.' icon leaf"></i>';
        $line  .= '<label>';
        $line  .= '<input class="hummingbird-end-node" id="'.$this->getId().'-'.$dataLvl.'-'.$ord.'" data-id="'.$dataLvl.'-'.$ord.'" type="checkbox">';
        $line .= $child['libel'];
        $line .= '</label>';
        $line .= '</li>';

        /** détermination sélecteur sur parent ligne à indérer */
        if (!empty($parent)) { $dataLvl = $parent['parent']; }
        $selector   = '#'.$this->getId().'Li-'.$dataLvl.'-'.$dataOrd.'';

        if ($parentPath != "0" && $parent && sizeof($parent['children']) == 1) {
            $node   = '<li id="'.$this->getId().'Li-'.$dataLvl.'-'.$dataOrd.'" class="node">';
            $node  .= '<i class="'.$this->getNodeOpenedIco().'"></i>';
            $node  .= '<label>';
            $node  .= '<input id="'.$this->getId().'-'.$dataLvl.'-'.$dataOrd.'" data-id="'.$dataLvl.'-'.$dataOrd.'" type="checkbox">';
            $node  .= $parent['libel'];
            $node  .= '</label>';
            $node  .= '<ul class="show">';
            $node  .= $line;
            $node  .= '</ul>';
            $node  .= '</li>';

            $code   = ['html' => $node, 'selector' => $selector];
            $mode   = 'updtTreeLeaf';
            /* mode update supprime - remplace sur sélecteur enfant */
        } else {
            // traitement ajout nouvelle feuille enfant
            // mode append sur sélecteur parent
            $code   = ['html' => $line, 'selector' => $selector.' > ul'];
            $mode       = 'appendTreeNode';
        }
        $ret[] = self::formatRetour($this->getId(), $this->getId(), $mode, $code);
        $ret[] = self::formatRetour($this->getId(), $this->getId(), 'exec', '$("#'.$this->getId().' .treeview").off().find("*").off();');
        $ret[] = self::formatRetour($this->getId(), $this->getId(), 'execID', $this->getId().'Script');
        return $ret ;
    }

    /**
     * @param $leafPath
     * @return array
     */
    public function returnDelLeaf($leafPath)
    {
        $leafPath = (string) $leafPath;
        $leaf     = $this->getLeaf($leafPath);
        $selector = $this->getId().'Li-'.$leaf['parent'].'-'.$leaf['ord'];

        $ret[] = self::formatRetour($this->getId(), $selector, 'delete');

        return $ret ;
    }

    /** **************************************************************************************************
     * méthodes privées de la classe                                                                     *
     * *************************************************************************************************** */

    /**
     * @param $tree
     * @param $path
     * @param $item
     * @param bool $addNode
     * @return mixed
     */
    private function updateTree($tree, $path, $item, $addNode = false)
    {
            if (!is_array($path)) { $path = explode('-', $path); }
            $id = array_shift($path);
            if (empty($path)) {
                if ($addNode) {
                    if (!isset($tree[$id]['children'])) { $tree[$id]['children'] = []; }
                    $tree[$id]['children'][$item['ord']] = $item;
                } else {
                    $tree[$id] = $item;
                }
            } else {
                $leaves = $tree[$id]['children'];
                $tree[$id]['children'] = $this->updateTree($leaves, $path, $item, $addNode);
            }
        ksort($tree);
        return $tree;
    }

    /**
     * @param $ref
     * @return bool
     */
    private function validRefUnique($ref)
    {
        $properties = $this->getProperties();
        $dataPath   = $properties['dataPath'];
        return (!array_key_exists($ref, $dataPath));
    }

    /**
     * @param $dataTree
     * @param $refs
     * @return mixed
     */
    private function rmLeafTree(array $dataTree, array $refs)
    {
        if ((int) $refs[0] == 0) { unset($refs[0]); }

        if (sizeof($refs) > 1) {
            $ref        = array_shift($refs);
            $localTree  = $this->rmLeafTree($dataTree[$ref]['children'], $refs);
            $dataTree[$ref]['children'] = $localTree;
        } else {
            unset($dataTree[$refs[0]]);
        }

        return $dataTree;
    }

    /**
     * @return array
     * @throws \ReflectionException
     */
    public function getTargetConstants()
    {
        $retour = [];
        if (empty($this->const_target)) {
            $constants = $this->getConstants();
            foreach ($constants as $key => $constant) {
                $pos = strpos($key, 'ODTREEVIEWTARGET_');
                if ($pos !== false) {
                    $retour[$key] = $constant;
                }
            }
            $this->const_target = $retour;
        } else {
            $retour = $this->const_target;
        }

        return $retour;
    }
}
