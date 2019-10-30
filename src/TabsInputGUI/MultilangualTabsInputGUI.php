<?php

namespace srag\CustomInputGUIs\TabsInputGUI;

use ilFormPropertyGUI;
use srag\CustomInputGUIs\PropertyFormGUI\PropertyFormGUI;
use srag\DIC\DICTrait;

/**
 * Class MultilangualTabsInputGUI
 *
 * @package srag\CustomInputGUIs\TabsInputGUI
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class MultilangualTabsInputGUI
{

    use DICTrait;


    /**
     * @param array $items
     *
     * @return array
     */
    public static function generate(array $items) : array
    {
        $tabs = [];

        foreach (self::dic()->language()->getInstalledLanguages() as $lang_key) {
            $tab_items = [];

            foreach ($items as $item_key => $item) {
                $tab_item = $item;
                $tab_items[$item_key . "_" . $lang_key] = $tab_item;
            }

            $tab = [
                PropertyFormGUI::PROPERTY_CLASS    => TabsInputGUITab::class,
                PropertyFormGUI::PROPERTY_SUBITEMS => $tab_items,
                "setTitle"                         => strtoupper($lang_key),
                "setActive"                        => ($lang_key === self::dic()->language()->getLangKey())
            ];

            $tabs[] = $tab;
        }

        return $tabs;
    }


    /**
     * @param TabsInputGUI        $tabs
     * @param ilFormPropertyGUI[] $inputs
     */
    public static function generateLegacy(TabsInputGUI $tabs, array $inputs)/*:void*/
    {
        foreach (self::dic()->language()->getInstalledLanguages() as $lang_key) {
            $tab = new TabsInputGUITab();
            $tab->setTitle(strtoupper($lang_key));
            $tab->setActive($lang_key === self::dic()->language()->getLangKey());

            foreach ($inputs as $input) {
                $tab_input = clone $input;
                $tab_input->setPostVar($input->getPostVar() . "_" . $lang_key);
                $tab->addInput($tab_input);
            }

            $tabs->addTab($tab);
        }
    }


    /**
     * MultilangualTabsInputGUI constructor
     */
    private function __construct()
    {

    }
}

