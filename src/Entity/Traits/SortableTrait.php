<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

trait SortableTrait
{
    /**
     * @Gedmo\SortablePosition
     * @ORM\Column(name="position", type="integer", options={"default": 0})
     */
    private $position;

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @param self $position
     * @return SortableTrait
     */
    public function setPosition(int $position): self
    {
        $this->position = $position;
        return $this;
    }


}