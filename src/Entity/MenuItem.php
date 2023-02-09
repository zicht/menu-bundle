<?php
/**
 * @copyright Zicht Online <http://www.zicht.nl>
 */

namespace Zicht\Bundle\MenuBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(
 *      name="menu_item",
 *      indexes={
 *          @ORM\Index(columns={"name", "path"}),
 *          @ORM\Index(columns={"lft", "rgt", "root"}),
 *          @ORM\Index(columns={"root", "lft"}),
 *          @ORM\Index(columns={"lvl", "language"})
 *      }
 * )
 * @ORM\Entity(repositoryClass="Gedmo\Tree\Entity\Repository\NestedTreeRepository")
 * @Gedmo\Tree(type="nested")
 */
class MenuItem
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Gedmo\TreeLeft
     * @ORM\Column(name="lft", type="integer")
     */
    private $lft;

    /**
     * @Gedmo\TreeLevel
     * @ORM\Column(name="lvl", type="integer")
     */
    private $lvl;

    /**
     * @Gedmo\TreeRight
     * @ORM\Column(name="rgt", type="integer")
     */
    private $rgt;

    /**
     * @Gedmo\TreeRoot
     * @ORM\Column(name="root", type="integer", nullable=true)
     */
    private $root;

    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="MenuItem", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="MenuItem", mappedBy="parent")
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    private $children;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(name="language", type="string", length=5, nullable=true)
     */
    private $language = null;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=255, nullable=true)
     */
    private $path = null;

    /**
     * Optional menu item name, used to hook dynamic items into the menu.
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name = null;

    /**
     * Optional menu item name, used to hook dynamic items into the menu.
     *
     * @ORM\Column(name="json_data", type="json", nullable=true)
     */
    private $json_data = null;

    /** @var bool */
    protected $addToMenu = false;

    /**
     * @param null $title
     * @param null $path
     * @param string $name
     */
    public function __construct($title = null, $path = null, $name = '')
    {
        $this->children = new ArrayCollection();

        $this->setTitle($title);
        $this->setPath($path);
        $this->setName($name);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $title
     * @return MenuItem
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getLeveledTitle()
    {
        return str_repeat('-', $this->lvl) . $this->title;
    }

    /**
     * @param string $path
     * @return MenuItem
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param MenuItem $parent
     * @return MenuItem
     */
    public function setParent(MenuItem $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return MenuItem
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @return MenuItem
     * @deprecated addChildren (i.e. plural) is confusing, use addChild instead
     */
    public function addChildren(MenuItem $children)
    {
        return $this->addChild($children);
    }

    /**
     * @return $this
     */
    public function addChild(MenuItem $child)
    {
        $this->children[] = $child;
        return $this;
    }

    /**
     * @deprecated removeChildren (i.e. plural) is confusing, use removeChild instead
     */
    public function removeChildren(MenuItem $children)
    {
        return $this->removeChild($children);
    }

    /**
     * @return $this
     */
    public function removeChild(MenuItem $child)
    {
        $this->children->removeElement($child);
        return $this;
    }

    /**
     * @return Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->title;
    }

    /**
     * @param int $lft
     * @return MenuItem
     */
    public function setLft($lft)
    {
        $this->lft = $lft;

        return $this;
    }

    /**
     * @return int
     */
    public function getLft()
    {
        return $this->lft;
    }

    /**
     * @param int $lvl
     * @return MenuItem
     */
    public function setLvl($lvl)
    {
        $this->lvl = $lvl;

        return $this;
    }

    /**
     * @return int
     */
    public function getLvl()
    {
        return $this->lvl;
    }

    /**
     * @return mixed
     */
    public function getLevel()
    {
        // TODO renamed "lvl" to level in the db, so tree_title.html.twig in ZichtFrameworkExtraBundle won't die on us.
        return $this->lvl;
    }

    /**
     * @param int $rgt
     * @return MenuItem
     */
    public function setRgt($rgt)
    {
        $this->rgt = $rgt;

        return $this;
    }

    /**
     * @return int
     */
    public function getRgt()
    {
        return $this->rgt;
    }

    /**
     * @param int $root
     * @return MenuItem
     */
    public function setRoot($root)
    {
        $this->root = $root;

        return $this;
    }

    /**
     * @return int
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * @return bool
     */
    public function isRoot()
    {
        return $this->root == $this->id;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param bool $addToMenu
     */
    public function setAddToMenu($addToMenu)
    {
        $this->addToMenu = $addToMenu;
    }

    /**
     * @return bool
     */
    public function isAddToMenu()
    {
        return $this->addToMenu;
    }

    /**
     * @param string $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * @return mixed
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param mixed $json_data
     */
    public function setJsonData($json_data)
    {
        $this->json_data = $json_data;
    }

    /**
     * @return mixed
     */
    public function getJsonData()
    {
        if (!$this->json_data) {
            return [];
        }
        return $this->json_data;
    }
}
