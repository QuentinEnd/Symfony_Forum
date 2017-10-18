<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Vote
 *
 * @ORM\Table(name="vote")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\VoteRepository")
 */
class Vote
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
     * @var int
     *
     * @ORM\Column(name="nombreDeVote", type="integer")
     */
    private $nombreDeVote;

    /**
     * @var Answer
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Answer", inversedBy="votes")
     */
    private $answer;

    /**
     * @return Answer
     */
    public function getAnswer()
    {
        return $this->answer;
    }

    /**
     * @param Answer $answer
     * @return Vote
     */
    public function setAnswer($answer)
    {
        $this->answer = $answer;
        return $this;
    }

    /**
     * @var Author
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Author",
     *     inversedBy="votes")
     */
    private $author;


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
     * Set nombreDeVote
     *
     * @param integer $nombreDeVote
     *
     * @return Vote
     */
    public function setNombreDeVote($nombreDeVote)
    {
        $this->nombreDeVote = $nombreDeVote;

        return $this;
    }

    /**
     * Get nombreDeVote
     *
     * @return int
     */
    public function getNombreDeVote()
    {
        return $this->nombreDeVote;
    }
}

