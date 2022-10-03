<?php
/**
 * @copyright Zicht Online <http://zicht.nl>
 */

namespace ZichtTest\Bundle\MenuBundle\Entity;

use PHPUnit\Framework\TestCase;
use Zicht\Bundle\MenuBundle\Entity\MenuItem;

class MenuItemTest extends TestCase
{
    public function testGettersSetters()
    {
        $refl = new \ReflectionClass('Zicht\Bundle\MenuBundle\Entity\MenuItem');

        $e = new MenuItem();
        foreach ($refl->getMethods() as $method) {
            $name = $method->getName();
            if (substr($name, 0, 3) === 'set') {
                $getter = 'get' . substr($name, 3);
                if (is_callable([$e, $getter])) {
                    if ($name === 'setParent') {
                        $value = new MenuItem();
                    } else {
                        $value = rand(0, 100);
                    }
                    $e->$name($value);
                    $this->assertEquals($value, $e->$getter());
                }
            }
        }
    }

    public function testIsAddToMenu()
    {
        $e = new MenuItem();
        foreach ([true, false] as $val) {
            $e->setAddToMenu($val);
            $this->assertEquals($val, $e->isAddToMenu());
        }
    }

    public function testIsRoot()
    {
        $e = new MenuItem();
        $e->setRoot(null);
        $this->assertTrue($e->isRoot());
        $e->setRoot(123);
        $this->assertFalse($e->isRoot());
    }

    public function testGetLeveledTitle()
    {
        $e = new MenuItem();
        $e->setTitle('Piet');
        $e->setLvl(3);
        $this->assertEquals('---Piet', $e->getLeveledTitle());
    }

    public function testStringRepr()
    {
        $e = new MenuItem();
        $e->setTitle('foo');
        $this->assertEquals('foo', (string)$e);
    }
}
