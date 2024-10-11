<?php

namespace Achinon\YamlClasserBundle\PhpCode;

use Achinon\ToolSet\Generator;
use Achinon\ToolSet\Parser;

class PhpCodeBuilder
{
    /**
     * @throws \Exception
     */
    public static function buildClassTree(iterable $a,
                                          string   $classPath): string
    {
        $classData = [];
        $subTreeCode = [];
        foreach($a as $b => $c) {
            //basic value
            if(!is_iterable($c)) {
                if(is_numeric($b)) {
                    //parsing, because property name cannot start with a number
                    $b = "_$b";
                }

                $classData[$b] = new PhpCodeProperty($b, $c, PhpCodePropertyType::ofVariable);
                continue;
            }

            //class value
            do {
                $subTreeClassPath = Generator::randomAlphanumericString();
            } while (ctype_digit($subTreeClassPath[0]));

            if(isset($subTreeCode[$subTreeClassPath])){
                throw new \Exception('If you see this error, go play lotto right now.');
            }
            $subTreeCode[$subTreeClassPath] = static::buildClassTree($c, $subTreeClassPath);

            $classData[] = new PhpCodeProperty($b, $subTreeClassPath, PhpCodePropertyType::ofClass);
        }

        $properties = $defines = [];
        /** @var PhpCodeProperty $property */
        foreach($classData as $property) {
            $constructors[] = $property->constructor();
            $definitions[] = $property->definition();
        }

        $constructors = Parser::arrayToString($constructors, PHP_EOL);
        $definitions = Parser::arrayToString($definitions, PHP_EOL);
        $treeClassString = static::buildClassString($classPath);

        Parser::replaceSubstringsInString([
            '__IN_CONSTRUCTOR__' => $constructors,
            '__DEFINITIONS__' => $definitions
        ], $treeClassString);

        $codeFullPack = Parser::arrayToString($subTreeCode, '');

        return $treeClassString.' '.$codeFullPack;
    }

    public static function buildFileIntro(?string $customNameSpace = null)
    {
        $namespace = $customNameSpace ?? 'Achinon\YamlClasserBundle\Generated';
        return <<<INTRO
<?php

namespace $namespace;

INTRO;

    }

    public static function buildClassString($className)
    {
        return <<<CLASS_DEFINITION
class $className {
    __DEFINITIONS__
    public function __construct()
    {
        __IN_CONSTRUCTOR__
    }
}
CLASS_DEFINITION;
    }
}