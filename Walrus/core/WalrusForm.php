<?php

/**
 * Walrus Framework
 * File maintained by: Nicolas Beauvais
 * Created: 15:43 12/06/14
 */

namespace Walrus\core;

use Walrus\core\WalrusException;
use Walrus\core\objects\Tag;

/**
 * Class WalrusForm
 * @package Walrus\core
 */
class WalrusForm
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $form;

    /**
     * @var array
     */
    private $fields;

    /**
     * @var array
     */
    private $errors;

    /**
     * @param $name
     * @throws WalrusException
     */
    public function __construct($name)
    {
        if (!isset($_ENV['W']['forms'][$name])) {
            throw new WalrusException('Can\'t render the ' . $name . ' form (doesn\'t exist)');
        }

        $form = $_ENV['W']['forms'][$name];

        $form['form'] = isset($form['form'])? $form['form'] : array();
        $form['fields'] = isset($form['fields'])? $form['fields'] : array();

        // set
        $this->name = $name;
        $this->form = $form['form'];
        $this->fields = $form['fields'];
    }

    /**
     * Check if a form as been submitted.
     * If a submitted form as been detected:
     *   - check each field validation
     *   - return an array of errors OR reroute if $controller & $action param are set, else return true
     * Else
     *   - return null
     *
     *
     * @param string $controller a controller name
     * @param string $action an action of the controller
     * @param array $param an array of the parameter to pass to the controller
     *
     * @return mixed
     */
    public function check($controller = null, $action = null, $param = array())
    {
        $errors = array();
        $data = $this->form['method'] == 'POST' ? $_POST : $_GET;

        // No form have been send
        if (empty($data)) {
            return null;
        }

        // check each field
        foreach ($this->fields as $name => $field) {

            $check = isset($field['check']) ? $field['check'] : array();
            $isKnown = in_array($field['type'], array('select', 'radio', 'checkbox'));

            if (empty($field['check']) && !$isKnown) {
                continue;
            }

            if ($isKnown && isset($data[$name])) {

                // process function
                if (isset($field['function'])) {
                    $cb = explode('::', $field['function']);
                    $field['options'] = WalrusRouter::reroute($cb[0], $cb[1]);
                }

                // if one simple value
                if (!is_array($data[$name])) {
                    $elements = array($data[$name]);
                } else {
                    $elements = $data[$name];
                }

                if (!isset($field['options'])) {
                    continue;
                }

                // check input integrity
                foreach ($elements as $element) {
                    if (!array_key_exists($element, $field['options'])) {
                        $errors[$name] = WalrusI18n::get('errors', 'messages', 'invalid', array(
                            'attribute' => $name
                        ));
                    }
                }

                if (isset($check['max_selected']) && count($elements) > $check['max_selected']) {// check max_selected
                    $errors[$name] = WalrusI18n::get('errors', 'messages', 'max_selected', array(
                        'attribute' => $name,
                        'count' => $check['max_selected']
                    ));
                }

                if (isset($check['min_selected']) && count($elements) < $check['min_selected']) {// check max_selected
                    $errors[$name] = WalrusI18n::get('errors', 'messages', 'min_selected', array(
                        'attribute' => $name,
                        'count' => $check['min_selected']
                    ));
                }

                if (isset($check['selected']) && count($elements) != $check['selected']) {// check max_selected
                    $errors[$name] = WalrusI18n::get('errors', 'messages', 'selected', array(
                        'attribute' => $name,
                        'count' => $check['selected']
                    ));
                }

            } elseif (!isset($check['required'])) {
                $check['required'] = true;
            }

            // check required
            if (isset($check['required']) && $check['required'] == true && !(isset($data[$name]))) {// check required
                $errors[$name] = WalrusI18n::get('errors', 'messages', 'required', array('attribute' => $name));
                continue;
            }
            if (isset($check['blank']) && $check['blank'] == true && isset($data[$name])) {// check blank
                $errors[$name] = WalrusI18n::get('errors', 'messages', 'blank', array('attribute' => $name));
                continue;
            }
            if (isset($check['empty']) && $check['empty'] == true && !empty($data[$name])) {// check empty
                $errors[$name] = WalrusI18n::get('errors', 'messages', 'empty', array('attribute' => $name));
                continue;
            }
            if (isset($check['empty']) && $check['empty'] == false && empty($data[$name])) {// check empty
                $errors[$name] = WalrusI18n::get('errors', 'messages', 'not_empty', array('attribute' => $name));
                continue;
            }
            if (isset($check['equal_to'])) {// check equal_to

                $equal_to = $check['equal_to'];

                if (strpos($check['equal_to'], '%') === 0) {
                    $equal_to = substr($check['equal_to'], 1, strlen($check['equal_to']));
                    $check['equal_to'] = isset($data[$equal_to]) ? $data[$equal_to] : null;
                }

                if ($check['equal_to'] !== $data[$name]) {
                    $errors[$name] = WalrusI18n::get('errors', 'messages', 'equal_to', array(
                        'attribute' => $name,
                        'value' => $equal_to
                    ));
                }
            }

            if (isset($check['even']) && $check['even'] == true && $data[$name] % 2 !== 0) {// check even
                $errors[$name] = WalrusI18n::get('errors', 'messages', 'even', array('attribute' => $name));
            }
            if (isset($check['greater_than']) && $check['greater_than'] <= $data[$name]) {// check greater_than
                $errors[$name] = WalrusI18n::get('errors', 'messages', 'greater_than', array(
                    'attribute' => $name,
                    'count' => $check['greater_than']
                ));
            }
            if (isset($check['greater_than_or_equal_to'])
                && $check['greater_than_or_equal_to'] < $data[$name]) {// check greater_than_or_equal_to
                $errors[$name] = WalrusI18n::get(
                    'errors',
                    'messages',
                    'greater_than_or_equal_to',
                    array($check['greater_than_or_equal_to'])
                );
            }
            if (isset($check['greater_than']) && $check['greater_than'] <= $data[$name]) {// check greater_than
                $errors[$name] = WalrusI18n::get('errors', 'messages', 'greater_than');
            }
            if (isset($check['number']) && $check['number'] == true && !is_numeric($data[$name])) {// check numb
                $errors[$name] = WalrusI18n::get('errors', 'messages', 'not_a_number', array('attribute' => $name));
            }
            if (isset($check['integer']) && $check['integer'] == true && !is_int($data[$name])) {// check int
                $errors[$name] = WalrusI18n::get('errors', 'messages', 'not_an_integer', array('attribute' => $name));
            }
            if (isset($check['odd']) && $check['odd'] == true && $data[$name] % 2 == 0) {// check odd
                $errors[$name] = WalrusI18n::get('errors', 'messages', 'odd', array('attribute' => $name));
            }
            if (isset($check['max']) && $check['max'] == true && strlen($data[$name]) > $check['max']) {// check max
                $errors[$name] = WalrusI18n::get('errors', 'messages', 'max', array(
                    'attribute' => $name,
                    'count' => $check['max']
                ));
            }
            if (isset($check['min']) && $check['min'] == true && strlen($data[$name]) < $check['min']) {// check min
                $errors[$name] = WalrusI18n::get('errors', 'messages', 'min', array(
                    'attribute' => $name,
                        'count' => $check['min']
                ));
            }
            if (isset($check['length']) && $check['length'] == true
                && strlen($data[$name]) !== $check['length']) {// check length
                $errors[$name] = WalrusI18n::get('errors', 'messages', 'length', array(
                    'attribute' => $name,
                    'count' => $check['min']
                ));
            }
            if (isset($check['validate']) && preg_match($check['validate'], $data[$name]) !== 1) {// check max
                $errors[$name] = WalrusI18n::get('errors', 'messages', 'invalid', array('attribute' => $name));
            }
            if (isset($check['function'])) {
                $cb = explode('::', $check['function']);
                if (!$isOk = WalrusRouter::reroute($cb[0], $cb[1])) {
                    $errors[$name] = $isOk;
                }
            }
        }

        if ($_ENV['W']['is_ajax']) {
            echo JSON_encode($errors);
            die;
        }

        if (empty($errors)) {

            if ($controller && $action) {
                return WalrusRouter::reroute($controller, $action, $param);
            }

            return true;
        }

        $this->errors = $errors;

        return $errors;
    }

    /**
     * @return mixed
     */
    public function render()
    {
        $this->verify();

        $form = $this->form;
        $fields = $this->fields;

        // Form HTML structure
        if (is_string($form['structure'])) {
            switch ($form['structure']) {

                case 'list':
                    $wrapper_elem = '';
                    $wrapper_row = 'li';
                    $wrapper_rows = 'ul';
                    break;

                case 'table':
                    $wrapper_elem = 'td';
                    $wrapper_row = 'tr';
                    $wrapper_rows = 'table';
                    break;
            }
        } elseif (is_array($form['structure'])) {
            $wrapper_elem = isset($form['structure']['wrapper_elem']) ? $form['structure']['wrapper_elem'] : null;
            $wrapper_row = isset($form['structure']['wrapper_row']) ? $form['structure']['wrapper_row'] : null;
            $wrapper_rows = isset($form['structure']['wrapper_rows']) ? $form['structure']['wrapper_rows'] : null;
        }

        // contain all inputs & labels
        $inputs = array();

        foreach ($fields as $key => $field) {

            // contain a label + input combo
            $row = array();

            // separate known data and attributes
            $check = isset($field['check']) ? $field['check'] : null;
            $options = isset($field['options']) ? $field['options'] : null;
            $function = isset($field['function']) ? $field['function'] : null;
            $label = isset($field['label']) ? $field['label'] : ucfirst($key);
            unset($field['check']);

            // default values
            $field['type'] = isset($field['type']) ? $field['type'] : 'text';
            $field['name'] = isset($field['name']) ? $field['name'] : $key;
            $field['id'] = isset($field['id']) ? $field['id'] : $this->name . '_' . $field['name'];
            $field['placeholder'] = isset($field['placeholder']) ? $field['placeholder'] : ucfirst($key);

            // remove all non attribute datas
            $removeKeys = array('required', 'validate', 'options', 'function');
            $field = array_diff_key($field, array_flip($removeKeys));

            if ($field['type'] == 'select') {

                if (!($options xor $function)) {
                    continue;
                }

                unset($field['placeholder']);

                // Create label
                if (!empty($label)) {

                    $Label = new Tag();
                    $Label->create('label');

                    $label = is_array($label)? array_merge($label, array('for' => $field['id']))
                        : array('text' => $label, 'for' => $field['id']);

                    $Label->setAttributes($label);

                    array_push($row, $Label);
                }

                // Create select
                $Tag = new Tag();
                $Tag->create('select');
                $Tag->setAttributes($field);

                // function cal
                if ($function) {
                    $cb = explode('::', $function);
                    $options = WalrusRouter::reroute($cb[0], $cb[1]);
                }

                foreach ($options as $inputKey => $text) {

                    // Create option
                    $Option = new Tag();
                    $Option->create('option');

                    if (is_array($text)) {
                        $Optgroup = new Tag();
                        $Optgroup->create('optgroup');
                        $Optgroup->setAttributes(array('label' => $inputKey));

                        foreach ($text as $keyText => $valueText) {
                            $Option->setAttributes(array('value' => $keyText));
                            $Option->inject($valueText);
                            $Optgroup->inject($Option);
                        }

                        $options = $Optgroup;

                    } else {
                        $Option->setAttributes(array('value' => $inputKey));
                        $Option->inject($text);
                        $options = $Option;
                    }

                    $Tag->inject($options);
                }

                array_push($row, $Tag);

                // Display errors
                if ($Error = $this->getError($field['name'])) {
                    array_push($row, $Error);
                }

                array_push($inputs, $row);
            } elseif ($field['type'] == 'radio' || $field['type'] == 'checkbox') {

                if (!$options) {
                    continue;
                }

                // remove useless attributes if exists
                unset($field['placeholder']);

                // Create label
                if (!empty($label)) {

                    $Label = new Tag();
                    $Label->create('label');

                    $label = is_array($label)? array_merge($label, array('for' => $field['id']))
                        : array('text' => $label, 'for' => $field['id']);

                    $Label->setAttributes($label);

                    array_push($row, $Label);
                }

                $originalName = $field['name'];
                $field['name'] .= '[]';

                foreach ($options as $inputKey => $attributes) {

                    $subrow = array();

                    if (is_string($attributes)) {
                        $value = $attributes;
                        $attributes = array();
                        $attributes['value'] = $value;
                    } else {
                        $attributes['value'] = isset($attributes['value']) ? $attributes['value'] : $inputKey;
                    }

                    $attributes['id'] = $field['id'] . '_' . $attributes['value'];

                    $attributes['label'] = isset($attributes['label']) ? $attributes['label'] : $inputKey;

                    // Create label
                    if (!empty($attributes['label'])) {

                        $Label = new Tag();
                        $Label->create('label');

                        if (is_string($attributes['label'])) {
                            $text = $attributes['label'];
                            $attributes['label'] = array();
                            $attributes['label']['text'] = $text;
                        }

                        $attributes['label']['for'] = isset($attributes['label']['for']) ?
                            $attributes['label']['for'] : $attributes['id'];

                        $Label->setAttributes($attributes['label']);
                    }
                    unset($attributes['label']);

                    // Create input
                    $Tag = new Tag();
                    $Tag->create('input');
                    $Tag->setAttributes(array_merge($field, $attributes));

                    array_push($subrow, $Tag);
                    array_push($subrow, $Label);
                    array_push($row, $subrow);
                }

                // Display errors
                if ($Error = $this->getError($originalName)) {
                    array_push($row, $Error);
                }

                array_push($inputs, $row);
            } else {

                // Create label
                if (!empty($label)) {

                    $Label = new Tag();
                    $Label->create('label');

                    $label = is_array($label)? array_merge($label, array('for' => $field['id']))
                        : array('text' => $label, 'for' => $field['id']);

                    $Label->setAttributes($label);

                    array_push($row, $Label);
                }

                // Create input
                $Tag = new Tag();
                $type = $field['type'] == 'textarea' ? 'textarea' : 'input';
                $Tag->create($type);
                $Tag->setAttributes($field);

                array_push($row, $Tag);

                // Display errors
                if ($Error = $this->getError($field['name'])) {
                    array_push($row, $Error);
                }

                array_push($inputs, $row);
            }
        }

        // Submit input
        $Submit = new Tag();
        $Submit->create('input');
        $Submit->setAttributes(array(
            'type' => 'submit',
            'value' => $form['submit']
        ));
        array_push($inputs, array($Submit));


        // Create the form tag
        $Form = new Tag();
        $Form->create('form');
        $Form->setAttributes(array(
            'method' => $form['method'],
            'action' => $form['action']
        ));

        // Create the row wrapper
        if ($wrapper_rows) {
            $Wrows = new Tag();
            $Wrows->create($wrapper_rows);
        }

        // Wrap tags
        foreach ($inputs as $keyRow => $row) {

            if ($wrapper_row) {
                $Wrow = new Tag();
                $Wrow->create($wrapper_row);
            }

            foreach ($row as $keyInput => $input) {

                if ($wrapper_elem) {

                    $Welem = new Tag();
                    $Welem->create($wrapper_elem);

                    // case for checkbox and radio input
                    if (is_array($input)) {
                        foreach ($input as $subelem) {
                            $Welem->inject($subelem->make());
                        }
                    } else {
                        $Welem->inject($input->make());
                    }

                    $inputs[$keyRow][$keyInput] = $Welem->make();
                } else {
                    if (is_array($input)) {
                        foreach ($input as $subelem) {
                            if (is_string($inputs[$keyRow][$keyInput])) {
                                $inputs[$keyRow][$keyInput] .= $subelem->make();
                            } else {
                                $inputs[$keyRow][$keyInput] = $subelem->make();
                            }
                        }
                    } else {
                        $inputs[$keyRow][$keyInput] = $input->make();
                    }
                }
            }

            $inputs[$keyRow] = implode('', $inputs[$keyRow]);

            if ($wrapper_row) {
                $inputs[$keyRow] = $Wrow->inject($inputs[$keyRow])->make();
            }

            // Create the row wrapper
            if ($wrapper_rows) {
                $Wrows->inject($inputs[$keyRow]);
            } else {
                $Form->inject($inputs[$keyRow]);
            }
        }

        if ($wrapper_rows) {
            $Form->inject($Wrows);
        }

        return $Form->make();
    }

    /**
     * @param $name
     *
     * @return bool|Tag
     */
    private function getError($name)
    {
        if (!isset($this->errors[$name])) {
            return false;
        }

        $Error = new Tag();
        $Error->create('span');
        $Error->setAttributes(array(
           'class' => 'error',
            'text' => $this->errors[$name]
        ));

        return $Error;
    }

    /**
     * Check the conformity of the form datas.
     */
    private function verify()
    {
        $form = $this->form;

        // default form configuration
        $form = is_array($form) ? $form : array();
        $form['method'] = strtoupper(isset($form['method']) ? $form['method'] : 'POST');
        $form['submit'] = isset($form['submit']) ? $form['submit'] : 'Submit';
        $form['structure'] = isset($form['structure']) ? $form['structure'] : 'list';
        $form['action'] = isset($form['action']) ? $form['action'] : $_ENV['W']['route_current'];

        $this->form = $form;
        return true;
    }
}
