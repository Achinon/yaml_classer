<?php

namespace Achinon\YamlClasserBundle\PhpCode;

use Achinon\ToolSet\Parser;
use Exception;

class PhpCodeProperty
{
    const allowed_types = ['boolean', 'integer', 'double', 'string', 'null'];
    private PhpCodePropertyVisibility $visibility = PhpCodePropertyVisibility::public;
    private string $type;

    /**
     * @throws Exception
     */
    public function __construct(private string $name,
                                private mixed  $value,
                                private readonly PhpCodePropertyType $propertyType)
    {
        $this->type = strtolower(gettype($this->value));

        if($this->type === 'string'){
            $lower = strtolower($this->value);
            if(in_array($lower, ['true', 'false'])) {
                $this->type = 'boolean';
            }
        }

        if(!in_array($this->type, self::allowed_types)) {
            throw new Exception(sprintf('Datatype passed: %s, datatype expected: %s', $this->type, Parser::arrayToString(self::allowed_types, ', ')));
        }
    }

    protected function getValueString(): mixed
    {
        if($this->propertyType === PhpCodePropertyType::ofClass) {
            return sprintf("new %s()", $this->getTypeString());
        }

        if($this->type === 'string'){
            return sprintf("'%s'", $this->value);
        }
        if($this->type === 'boolean'){
            return $this->value ? 'true' : 'false';
        }
        if($this->type === 'null'){
            return 'null';
        }
        return "$this->value";
    }

    protected function getNameString(): string
    {
        $name = Parser::stripToAlphanumeric($this->name);
        if(ctype_digit($name[0])) {
            return "_" . $name;
        }
        return $name;
    }

    protected function getTypeString(): string
    {
        if($this->propertyType === PhpCodePropertyType::ofClass) {
            return $this->value;
        }
        switch ($this->type){
            case 'integer': return 'int';
            case 'string': return 'string';
            case 'boolean': return 'bool';
            case 'double': return 'float';
            case 'null': return 'null';
            default: throw new Exception('Unhandled type passed to property class.');
        }
    }

    protected function getVisibilityString(): string
    {
        return $this->visibility->name;
    }

    public function definition(): string
    {
        $returnString =  "%s readonly %s $%s;";

        return sprintf(
            $returnString,
            $this->getVisibilityString(),
            $this->getTypeString(),
            $this->getNameString(),
        );
    }

    public function constructor()
    {
        return sprintf('$this->%s = %s;', $this->getNameString(), $this->getValueString());
    }
}