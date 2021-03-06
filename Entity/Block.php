<?php

namespace Raindrop\PageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sonata\BlockBundle\Model\Block as BaseBlock;
use Sonata\BlockBundle\Model\BlockInterface;
use Raindrop\PageBundle\Entity\BlockVariable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;

/**
 * @ORM\Entity(repositoryClass="Raindrop\PageBundle\Entity\BlockRepository")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="block")
 */
class Block extends BaseBlock
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column
     */
    protected $name;

    /**
     * @ORM\Column
     */
    protected $template;

    /**
     * @ORM\ManyToOne(targetEntity="Raindrop\PageBundle\Entity\Page", inversedBy="blocks")
     */
    protected $page;

    /**
     * @ORM\ManyToOne(targetEntity="Raindrop\PageBundle\Entity\Block", inversedBy="children")
     */
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="Raindrop\PageBundle\Entity\Block", mappedBy="parent", cascade={"persist", "remove", "merge"})
     */
    protected $children;

    /**
     * @ORM\OneToMany(targetEntity="Raindrop\PageBundle\Entity\BlockVariable", mappedBy="block", cascade={"persist", "remove", "merge"}, orphanRemoval=true)
     */
    protected $variables;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $position;

    /**
     * @ORM\Column(type="array")
     */
    protected $javascripts;

    /**
     * @ORM\Column(type="array")
     */
    protected $stylesheets;

    /**
     * @ORM\Column(nullable=true)
     */
    protected $layout;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $updated;

    /**
     *
     * @var type Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function resetId()
    {
        $this->id = null;
    }

    /**
     * @param type $entityManager Doctrine\ORM\EntityManager
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getEntityManager()
    {
        return $this->entityManager;
    }

    public function getType()
    {
        return 'raindrop_page.block.service.template';
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updated;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->setCreated(new \DateTime);
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->setUpdated(new \DateTime);
    }

    /**
     * Set template
     *
     * @param  string $template
     * @return Block
     */
    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Get template
     *
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Set page
     *
     * @param  string $page
     * @return Block
     */
    public function setPage($page)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * Get page
     *
     * @return string
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->children = new ArrayCollection();
    }

    /**
     * Set name
     *
     * @param  string $name
     * @return Block
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set created
     *
     * @param  \DateTime $created
     * @return Block
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param  \DateTime $updated
     * @return Block
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set parent
     *
     * @param  \Raindrop\PageBundle\Entity\Block $parent
     * @return Block
     */
    public function setParent(BlockInterface $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \Raindrop\PageBundle\Entity\Block
     */
    public function getParent()
    {
        return $this->parent;
    }

    public function setChildren($children)
    {
        $this->children = $children;
    }

    /**
     * Add children
     *
     * @param  \Raindrop\PageBundle\Entity\Block $children
     * @return Block
     */
    public function addChildren(BlockInterface $children)
    {
        $this->children[] = $children;

        $children->setParent($this);

        return $this;
    }

    /**
     * Remove children
     *
     * @param \Raindrop\PageBundle\Entity\Block $children
     */
    public function removeChildren(BlockInterface $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        // get a new ArrayIterator
        $iterator = $this->children->getIterator();

        // define ordering closure, using preferred comparison method/field
        $iterator->uasort(function ($first, $second) {
            return (int) $first->getPosition() > (int) $second->getPosition() ? 1 : -1;
        });

        // return the ordered iterator
        return $iterator;
    }

    public function hasChildren()
    {
        return !empty($this->children) && count($this->children);
    }

    public function getSortedChildren()
    {
        // get a new ArrayIterator
        $iterator = $this->children->getIterator();

        // define ordering closure, using preferred comparison method/field
        $iterator->uasort(function ($first, $second) {
            return (int) $first->getPosition() > (int) $second->getPosition() ? 1 : -1;
        });

        // return the ordered iterator
        return $iterator;
    }

    /**
     * Add variables
     *
     * @param  \Raindrop\PageBundle\Entity\BlockVariable $variables
     * @return Block
     */
    public function addVariable(BlockVariable $variables)
    {
        $this->variables[] = $variables;

        return $this;
    }

    /**
     * Remove variables
     *
     * @param \Raindrop\PageBundle\Entity\BlockVariable $variables
     */
    public function removeVariable(BlockVariable $variables)
    {
        $this->variables->removeElement($variables);
    }

    /**
     * Get variables
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVariables()
    {
        return $this->variables;
    }

    /**
     * Sets all the variables
     *
     * @param type $variables
     */
    public function setVariables(ArrayCollection $variables)
    {
        $this->variables = $variables;
    }

    /**
     * Set position
     *
     * @param  integer $position
     * @return Block
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    public function hasJavascripts()
    {
        return !empty($this->javascripts);
    }

    /**
     * Set javascripts
     *
     * @param  array $javascripts
     * @return Block
     */
    public function setJavascripts($javascripts)
    {
        $this->javascripts = $javascripts;

        return $this;
    }

    /**
     * Get javascripts
     *
     * @return array
     */
    public function getJavascripts()
    {
        return $this->javascripts;
    }

    public function hasStylesheets()
    {
        return !empty($this->stylesheets);
    }

    /**
     * Set stylesheets
     *
     * @param  array $stylesheets
     * @return Block
     */
    public function setStylesheets($stylesheets)
    {
        $this->stylesheets = $stylesheets;

        return $this;
    }

    /**
     * Get stylesheets
     *
     * @return array
     */
    public function getStylesheets()
    {
        return $this->stylesheets;
    }

    /**
     * Set layout
     *
     * @param  string $layout
     * @return Block
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;

        return $this;
    }

    /**
     * Get layout
     *
     * @return string
     */
    public function getLayout()
    {
        return $this->layout;
    }

    public function isRenderable()
    {
        $variables = $this->getVariables();
        foreach ($variables as $variable) {
            $content = $variable->getContent();
            if (empty($content)) {
                return false;
            }
        }

        return true;
    }

}
