<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Game
 *
 * @ORM\Table(name="game")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\GameRepository")
 */
class Game
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
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=4, unique=true)
     */
    private $code;

    /**
     * @var int
     *
     * @ORM\Column(name="nbPlayerMin", type="integer", nullable=true)
     */
    private $nbPlayerMin;

    /**
     * @var int
     *
     * @ORM\Column(name="nbPlayerMax", type="integer", nullable=true)
     */
    private $nbPlayerMax;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var bool
     *
     * @ORM\Column(name="isStarted", type="boolean", nullable=true)
     */
    private $isStarted = 0;

    /**
     * @var bool
     *
     * @ORM\Column(name="isFinished", type="boolean", nullable=true)
     */
    private $isFinished = 0;

    /**
     * @ORM\OneToMany(targetEntity="Player", mappedBy="game", cascade={"persist"})
     */
    private $players;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $code
     *
     * @return Game
     */
    public function setCode($code)
    {
        $this->code = strtoupper($code);

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set nbPlayerMin
     *
     * @param integer $nbPlayerMin
     *
     * @return Game
     */
    public function setNbPlayerMin($nbPlayerMin = 2)
    {
        if (!isset($nbPlayerMin) or empty($nbPlayerMin)) {
            $nbPlayerMin = 2;
        }

        $this->nbPlayerMin = $nbPlayerMin;

        return $this;
    }

    /**
     * Get nbPlayerMin
     *
     * @return int
     */
    public function getNbPlayerMin()
    {
        return $this->nbPlayerMin;
    }

    /**
     * Set nbPlayerMax
     *
     * @param integer $nbPlayerMax
     *
     * @return Game
     */
    public function setNbPlayerMax($nbPlayerMax = 10)
    {
        if (!isset($nbPlayerMax) or empty($nbPlayerMax)) {
            $nbPlayerMax = 10;
        }

        $this->nbPlayerMax = $nbPlayerMax;

        return $this;
    }

    /**
     * Get nbPlayerMax
     *
     * @return int
     */
    public function getNbPlayerMax()
    {
        return $this->nbPlayerMax;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Game
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set isStarted
     *
     * @param boolean $isStarted
     *
     * @return Game
     */
    public function setIsStarted($isStarted = 0)
    {
        $this->isStarted = $isStarted;

        return $this;
    }

    /**
     * Get isStarted
     *
     * @return bool
     */
    public function getIsStarted()
    {
        return $this->isStarted;
    }

    /**
     * Set isFinished
     *
     * @param boolean $isFinished
     *
     * @return Game
     */
    public function setIsFinished($isFinished = 0)
    {
        $this->isFinished = $isFinished;

        return $this;
    }

    /**
     * Get isFinished
     *
     * @return bool
     */
    public function getIsFinished()
    {
        return $this->isFinished;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->nbPlayerMin = 2;
        $this->nbPlayerMax = 10;
        $this->isStarted = 0;
        $this->isFinished = 0;
        $this->players = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add player
     *
     * @param \AppBundle\Entity\Player $player
     *
     * @return Game
     */
    public function addPlayer(\AppBundle\Entity\Player $player)
    {
        $this->players[] = $player;
        $player->setGame($this);

        return $this;
    }

    /**
     * Remove player
     *
     * @param \AppBundle\Entity\Player $player
     */
    public function removePlayer(\AppBundle\Entity\Player $player)
    {
        $this->players->removeElement($player);
    }

    /**
     * Get players
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPlayers()
    {
        return $this->players;
    }
}
