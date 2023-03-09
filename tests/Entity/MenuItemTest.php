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

    /**
     * @dataProvider copyProvider
     */
    public function testCopy(MenuItem $input, MenuItem $expected, ?string $title = null, ?string $path = null, ?string $name = '')
    {
        $this->assertEquals(MenuItem::copy($input, $title, $path, $name), $expected);
    }

    public function copyProvider(): iterable
    {
        $expected = new MenuItem();
        $expected->setJsonData([]); // empty array is returned in getter if "json_data" field is null
        yield 'empty menu item' => [
            new MenuItem(),
            $expected,
        ];

        $input = new MenuItem();
        $input->setTitle('this title should be overwritten');
        $input->setPath('this path should be overwritten');
        $input->setName('this name should be overwritten');
        $expected = new MenuItem();
        $expected->setTitle('title with priority');
        $expected->setPath('path with priority');
        $expected->setName('name with priority');
        $expected->setJsonData([]);
        yield 'constructor arguments has priority' => [
            $input,
            $expected,
            'title with priority',
            'path with priority',
            'name with priority',
        ];

        $input = new MenuItem('my title', 'my path', 'my name');
        $input->setLft(1);
        $input->setLvl(2);
        $input->setRgt(3);
        $input->setRoot(4);
        $input->setParent(new MenuItem('parent'));
        $input->setLanguage('nl');
        $input->setJsonData(['hello' => 'world']);
        $input->addChild(new MenuItem('test 1'));
        $input->addChild(new MenuItem('test 2'));
        $expected = new MenuItem('my title', 'my path', 'my name');
        $expected->setLft(1);
        $expected->setLvl(2);
        $expected->setRgt(3);
        $expected->setRoot(4);
        $expected->setParent(new MenuItem('parent'));
        $expected->setLanguage('nl');
        $expected->setJsonData(['hello' => 'world']);
        $expected->addChild(new MenuItem('test 1'));
        $expected->addChild(new MenuItem('test 2'));
        yield 'full data set' => [
            $input,
            $expected,
        ];
    }
}
