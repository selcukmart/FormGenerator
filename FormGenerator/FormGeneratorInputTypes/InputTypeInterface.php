<?php
/**
 * @author selcukmart
 * 24.01.2021
 * 14:49
 */

namespace FormGenerator\FormGeneratorInputTypes;


use FormGenerator\FormGenerator;

interface InputTypeInterface
{
    public function __construct(FormGenerator $formGenerator);

    public function createInput(array $item):array;

    public function __destruct();
}