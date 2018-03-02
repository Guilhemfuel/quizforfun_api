<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Player
 *
 * @ORM\Table(name="player")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PlayerRepository")
 */
class Player
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
     * @ORM\Column(name="name", type="string", length=20)
     */
    private $name;

    /**
     * @var bool
     *
     * @ORM\Column(name="owner", type="boolean", nullable=true)
     */
    private $owner;

    /**
     * @var int
     *
     * @ORM\Column(name="score", type="integer", nullable=true)
     */
    private $score;

    /**
     * @ORM\ManyToOne(targetEntity="Game", inversedBy="players", cascade={"persist"})
     * @ORM\JoinColumn(name="game_id", referencedColumnName="id", nullable=false)
     */
    private $game;


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
     * @param string $name
     *
     * @return Player
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
     * Set owner
     *
     * @param boolean $owner
     *
     * @return Player
     */
    public function setOwner($owner = 0)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return bool
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Set score
     *
     * @param integer $score
     *
     * @return Player
     */
    public function setScore($score = 0)
    {
        $this->score = $score;

        return $this;
    }

    /**
     * Get score
     *
     * @return int
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * Set game
     *
     * @param \AppBundle\Entity\Game $game
     *
     * @return Player
     */
    public function setGame(\AppBundle\Entity\Game $game)
    {
        $this->game = $game;

        return $this;
    }

    /**
     * Get game
     *
     * @return \AppBundle\Entity\Game
     */
    public function getGame()
    {
        return $this->game;
    }
}
