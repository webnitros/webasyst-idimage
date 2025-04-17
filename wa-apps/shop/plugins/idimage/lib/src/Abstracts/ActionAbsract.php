<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 22.02.2025
 * Time: 12:48
 */

namespace IdImage\Abstracts;


abstract class ActionAbsract
{

    protected $actions = [];
    private string $action;
    private string $prefix;
    private \xPDOObject $object;

    public function __construct(\xPDOObject $object, string $prefix)
    {
        $this->object = $object;
        $this->prefix = $prefix;
        $this->action = ucfirst($prefix);
    }


    public function getActions()
    {
        return $this->actions;
    }


    public function object()
    {
        return $this->object;
    }

    public function get($k, $format = null, $formatTemplate = null)
    {
        return $this->object()->get($k, $format, $formatTemplate);
    }


    public function add(string $key, $icon = null, $button = true, $menu = true, $cls = null, $multiple = false)
    {
        $icon = is_string($icon) ? 'icon '.$icon : '';
        $cls = is_string($cls) ? $cls : '';
        # 'multiple' => $this->modx->lexicon('ms2_ft_selected_activate'),

        $lex = strtolower('idimage_'.$this->prefix.'_action_'.$key);
        $one = $this->object->xpdo->lexicon($lex);
        $data = [
            'cls' => $cls,
            'icon' => $icon,
            'title' => $one,
            'action' => $key.$this->action,
            'button' => $button,
            'menu' => $menu,
        ];


        if ($multiple) {
            $lex = strtolower('idimage_'.$this->prefix.'_actions_'.$key);
            $text = $this->object->xpdo->lexicon($lex);
            if ($text === $lex) {
                $text = $one;
            }
            $data['multiple'] = $text;
        }

        $this->actions[] = $data;

        return $this;
    }

}
