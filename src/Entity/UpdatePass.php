<?php

namespace App\Entity;

use App\Repository\UpdatePassRepository;
use Symfony\Component\Validator\Constraints as Assert;
class UpdatePass
{

    /**
     * @Assert\NotBlank(message="Ce champ est vide")
     */
    private $oldPass;

    /**
     * @Assert\NotBlank(message="Ce champ est vide")
     */
    private $newPass;

    public function getOldPass(): ?string
    {
        return $this->oldPass;
    }

    public function setOldPass(string $oldPass): self
    {
        $this->oldPass = $oldPass;

        return $this;
    }

    public function getNewPass(): ?string
    {
        return $this->newPass;
    }

    public function setNewPass(string $newPass): self
    {
        $this->newPass = $newPass;

        return $this;
    }
}
