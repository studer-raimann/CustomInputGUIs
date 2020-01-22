<?php

namespace srag\CustomInputGUIs\MultiSelectSearchInputGUI;

use ilMultiSelectInputGUI;
use ilTableFilterItem;
use ilTemplate;
use ilToolbarItem;
use ilUtil;
use srag\DIC\DICTrait;

/**
 * Class MultiSelectSearchInputGUI
 *
 * @package srag\CustomInputGUIs\MultiSelectSearchInputGUI
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 * @author  Oskar Truffer <ot@studer-raimann.ch>
 * @author  Martin Studer <ms@studer-raimann.ch>
 */
class MultiSelectSearchInputGUI extends ilMultiSelectInputGUI implements ilTableFilterItem, ilToolbarItem
{

    use DICTrait;
    /**
     * @var string
     */
    protected $width;
    /**
     * @var string
     */
    protected $height;
    /**
     * @var string
     */
    protected $css_class;
    /**
     * @var int|null
     */
    protected $minimum_input_length = null;
    /**
     * @var string
     */
    protected $ajax_link;
    /**
     * @var ilTemplate
     */
    protected $input_template;
    /**
     * @var int|null
     */
    protected $limit_count = null;


    /**
     * MultiSelectSearchInputGUI constructor
     *
     * @param string $title
     * @param string $post_var
     */
    public function __construct(/*string*/
        $title = "", /*string*/
        $post_var = ""
    ) {
        if (substr($post_var, -2) != "[]") {
            $post_var = $post_var . "[]";
        }
        parent::__construct($title, $post_var);

        $dir = __DIR__;
        $dir = "./" . substr($dir, strpos($dir, "/Customizing/") + 1);

        self::dic()->mainTemplate()->addJavaScript($dir . "/../../node_modules/select2/dist/js/select2.full.min.js");
        self::dic()->mainTemplate()->addJavaScript($dir . "/../../node_modules/select2/dist/js/i18n/" . self::dic()->user()->getCurrentLanguage()
            . ".js");
        self::dic()->mainTemplate()->addCss($dir . "/../../node_modules/select2/dist/css/select2.min.css");
        self::dic()->mainTemplate()->addCss($dir . "/css/multiselectsearchinputgui.css");
        $this->setInputTemplate(new ilTemplate(__DIR__ . "/templates/tpl.multiple_select.html", true, true));
        $this->setWidth("308px");
    }


    /**
     * Check input, strip slashes etc. set alert, if input is not ok.
     *
     * @return boolean Input ok, true/false
     */
    public function checkInput()/*: bool*/
    {
        if ($this->getRequired() && empty($this->getValue())) {
            $this->setAlert(self::dic()->language()->txt("msg_input_is_required"));

            return false;
        }

        if ($this->getLimitCount() !== null && count($this->getValue()) > $this->getLimitCount()) {
            $this->setAlert(self::dic()->language()->txt("form_input_not_valid"));

            return false;
        }

        return true;
    }


    /**
     * @return array
     */
    public function getSubItems()/*: array*/
    {
        return array();
    }


    /**
     * @return string
     */
    public function render()/*: string*/
    {
        $tpl = $this->getInputTemplate();
        $values = $this->getValue();
        $options = $this->getOptions();

        $postvar = $this->getPostVar();
        /*if(substr($postvar, -3) == "[]]")
        {
            $postvar = substr($postvar, 0, -3)."]";
        }*/

        $tpl->setVariable("POST_VAR", $postvar);

        //Multiselect Bugfix
        //$id = substr($this->getPostVar(), 0, -2);
        $tpl->setVariable("ID", $this->getFieldId());
        //$tpl->setVariable("ID", $this->getPostVar());

        $tpl->setVariable("WIDTH", $this->getWidth());
        $tpl->setVariable("HEIGHT", $this->getHeight());
        $tpl->setVariable("PLACEHOLDER", "");
        $tpl->setVariable("MINIMUM_INPUT_LENGTH", $this->getMinimumInputLength());
        $tpl->setVariable("LIMIT_COUNT", $this->getLimitCount());
        $tpl->setVariable("Class", $this->getCssClass());

        if (!empty($this->getAjaxLink())) {
            $tpl->setVariable("AJAX_LINK", $this->getAjaxLink());
        }

        if ($this->getDisabled()) {
            $tpl->setVariable("ALL_DISABLED", "disabled=\"disabled\"");
        }

        if ($options) {
            foreach ($options as $option_value => $option_text) {
                $selected = in_array($option_value, $values);

                if (!empty($this->getAjaxLink()) && !$selected) {
                    continue;
                }

                $tpl->setCurrentBlock("item");
                if ($this->getDisabled()) {
                    $tpl->setVariable("DISABLED", " disabled=\"disabled\"");
                }
                if ($selected) {
                    $tpl->setVariable("SELECTED", "selected");
                }

                $tpl->setVariable("VAL", ilUtil::prepareFormOutput($option_value));
                $tpl->setVariable("TEXT", $option_text);
                $tpl->parseCurrentBlock();
            }
        }

        return self::output()->getHTML($tpl);
    }


    /**
     * @param string $height
     *
     * @deprecated setting inline style items from the controller is bad practice. please use the setClass together with an appropriate css class.
     */
    public function setHeight(/*string*/
        $height
    )/*: void*/
    {
        $this->height = $height;
    }


    /**
     * @return string
     *
     * @deprecated setting inline style items from the controller is bad practice. please use the setClass together with an appropriate css class.
     */
    public function getHeight()/*: string*/
    {
        return $this->height;
    }


    /**
     * @param string $width
     *
     * @deprecated setting inline style items from the controller is bad practice. please use the setClass together with an appropriate css class.
     */
    public function setWidth(/*string*/
        $width
    )/*: void*/
    {
        $this->width = $width;
    }


    /**
     * @return string
     *
     * @deprecated setting inline style items from the controller is bad practice. please use the setClass together with an appropriate css class.
     */
    public function getWidth()/*: string*/
    {
        return $this->width;
    }


    /**
     * @param string $css_class
     */
    public function setCssClass(/*string*/
        $css_class
    )/*: void*/
    {
        $this->css_class = $css_class;
    }


    /**
     * @return string
     */
    public function getCssClass()/*: string*/
    {
        return $this->css_class;
    }


    /**
     * @param int|null $minimum_input_length
     */
    public function setMinimumInputLength(/*?int*/ $minimum_input_length = null)/*: void*/
    {
        $this->minimum_input_length = $minimum_input_length;
    }


    /**
     * @return int
     */
    public function getMinimumInputLength()/*: int*/
    {
        if ($this->minimum_input_length !== null) {
            return $this->minimum_input_length;
        } else {
            return (!empty($this->getAjaxLink()) ? 1 : 0);
        }
    }


    /**
     * @param string $ajax_link setting the ajax link will lead to ignoration of the "setOptions" function as the link given will be used to get the
     */
    public function setAjaxLink(/*string*/
        $ajax_link
    )/*: void*/
    {
        $this->ajax_link = $ajax_link;
    }


    /**
     * @return string
     */
    public function getAjaxLink()/*: string*/
    {
        return $this->ajax_link;
    }


    /**
     * @param ilTemplate $input_template
     */
    public function setInputTemplate(/*ilTemplate*/
        $input_template
    )/*: void*/
    {
        $this->input_template = $input_template;
    }


    /**
     * @return ilTemplate
     */
    public function getInputTemplate()/*ilTemplate*/
    {
        return $this->input_template;
    }


    /**
     * This implementation might sound silly. But the multiple select input used parses the post vars differently if you use ajax. thus we have to do this stupid "trick". Shame on select2 project ;)
     *
     * @return string the real postvar.
     */
    protected function searchPostVar()/*: string*/
    {
        if (substr($this->getPostVar(), -2) == "[]") {
            return substr($this->getPostVar(), 0, -2);
        } else {
            return $this->getPostVar();
        }
    }


    /**
     * @param array $array
     */
    public function setValueByArray(/*array*/ $array)/*: void*/
    {
        //		print_r($array);

        $val = $array[$this->searchPostVar()];
        if (is_array($val)) {
            $val;
        } elseif (!$val) {
            $val = array();
        } else {
            $val = explode(",", $val);
        }
        $this->setValue($val);
    }


    /**
     * @param string $a_postvar
     */
    public function setPostVar(/*string*/
        $a_postvar
    )/*: void*/
    {
        if (substr($a_postvar, -2) != "[]") {
            $a_postvar = $a_postvar . "[]";
        }
        parent::setPostVar($a_postvar);
    }


    /**
     * @inheritDoc
     */
    public function getTableFilterHTML()/*: string*/
    {
        return $this->render();
    }


    /**
     * @inheritDoc
     */
    public function getToolbarHTML()/*: string*/
    {
        return $this->render();
    }


    /**
     * @return int|null
     */
    public function getLimitCount()/* : ?int*/
    {
        return $this->limit_count;
    }


    /**
     * @param int|null $limit_count
     */
    public function setLimitCount(/*?int*/ $limit_count)/* : void*/
    {
        $this->limit_count = $limit_count;
    }
}
