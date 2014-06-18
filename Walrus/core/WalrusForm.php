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
                    $wrapper_elem = null;
                    $wrapper_row = 'li';
                    $wrapper_rows = 'ul';
                    $wrapper_form = null;
                    break;

                case 'table':
                    $wrapper_elem = 'td';
                    $wrapper_row = 'tr';
                    $wrapper_rows = 'table';
                    $wrapper_form = null;
                    break;
            }
        } elseif (is_array($form['structure'])) {
            $wrapper_elem = isset($form['structure']['wrapper_elem']) ? $form['structure']['wrapper_elem'] : null;
            $wrapper_row = isset($form['structure']['wrapper_row']) ? $form['structure']['wrapper_row'] : null;
            $wrapper_rows = isset($form['structure']['wrapper_rows']) ? $form['structure']['wrapper_rows'] : null;
            $wrapper_form = isset($form['structure']['wrapper_form']) ? $form['structure']['wrapper_form'] : null;
        }

        // Create the form tag
        $Form = new Tag();
        $Form->create('form');
        $Form->setAttributes(array(
            'method' => $form['method'],
            'action' => $form['action']
        ));

        // contain all inputs & labels
        $inputs = array();

        // contain a label + input combo
        $row = array();

        foreach ($fields as $key => $field) {

            // separate known data and attributes
            $required = isset($field['required']) ? $field['required'] : null;
            $validate = isset($field['validate']) ? $field['validate'] : null;
            $options = isset($field['options']) ? $field['options'] : null;
            $function = isset($field['function']) ? $field['function'] : null;
            $label = isset($field['label']) ? $field['label'] : ucfirst($key);

            // default values
            $field['type'] = isset($field['type']) ? $field['type'] : 'text';
            $field['name'] = isset($field['name']) ? $field['name'] : $key;
            $field['id'] = isset($field['id']) ? $field['id'] : $this->name . '_' . $field['name'];
            $field['placeholder'] = isset($field['placeholder']) ? $field['placeholder'] : ucfirst($key);

            // remove all non attribute datas
            $removeKeys = array('required', 'validate', 'options', 'function');
            $field = array_diff_key($field, array_flip($removeKeys));

            if ($field['type'] == 'select') {

                // @TODO: do from YAML & from Function

            } elseif ($field['type'] == 'radio' || $field['type'] == 'checkbox') {

                if (!$options) {
                    continue;
                }

                // remove useless attributes if exists
                unset($field['placeholder']);

                $field['name'] .= '[]';

                foreach ($options as $inputKey => $attributes) {

                    $input_id = $field['id'] . '_' . $inputKey;

                    if (is_string($attributes)) {
                        $value = $attributes;
                        $attributes = array();
                        $attributes['value'] = $value;
                    } else {
                        $attributes['value'] = isset($attributes['value']) ? $attributes['value'] : $inputKey;
                    }

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
                            $attributes['label']['for'] : $input_id;

                        $Label->setAttributes($attributes['label']);
                        array_push($row, $Label);
                    }
                    unset($attributes['label']);

                    // Create input
                    $Tag = new Tag();
                    $Tag->create('input');
                    $Tag->setAttributes(array_merge($attributes, $field));
                    array_push($row, $Tag);
                }

                array_push($inputs, $row);
            } else {

                // Create label
                if (!empty($label)) {

                    $Label = new Tag();
                    $Label->create('label');

                    if (is_string($label)) {
                        $Label->setAttributes('text', $label);
                    } elseif (is_array($label)) {
                        $Label->setAttributes($label);
                    }
                    array_push($row, $Label);
                }

                // Basic inputs tag
                $Tag = new Tag();
                $Tag->create('input');
                $Tag->setAttributes($field);
                array_push($row, $Tag);
            }

            array_push($inputs, $row);
        }

        // Submit input
        $Submit = new Tag();
        $Submit->create('input');
        $Submit->setAttributes(array(
            'type' => 'submit',
            'value' => $form['submit']
        ));
        array_push($inputs, $Submit);

        // Wrap tags
        foreach ($inputs as $row) {

            // Create the wrapper_elem tag
            if ($wrapper_elem) {
                $Welem = new Tag();
                $Welem->create($wrapper_elem);
            }

            foreach ($row as $input) {
                $Welem->inject($input);
            }

            echo $Welem->make();
            //$Form->inject($wrapped);
        }
        die;

        // Create the wrapper_form
        if ($wrapper_form) {
            $Wform = new Tag();
            $Wform->create($wrapper_form);
            $Wform->inject($Form);
            return $Wform->make();
        } else {
            return $Form->make();
        }
    }

    /**
     * Check the conformity of the form datas.
     */
    private function verify()
    {
        $form = $this->form;

        // default form configuration
        $form['form'] = isset($form['form'])? $form['form'] : array();
        $form['form']['method'] = isset($form['form']['method']) ? $form['form']['method'] : 'POST';
        $form['form']['submit'] = isset($form['form']['submit']) ? $form['form']['submit'] : 'Submit';
        $form['form']['structure'] = isset($form['form']['structure']) ? $form['form']['structure'] : 'list';
        $form['form']['action'] = isset($form['form']['action']) ?
            $form['form']['action'] : $_ENV['W']['route_current'];

        return true;
    }
}
