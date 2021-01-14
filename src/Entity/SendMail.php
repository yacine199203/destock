<?php

namespace App\Entity;

use App\Repository\SendMailRepository;
use Symfony\Component\Validator\Constraints as Assert;


class SendMail
{
 

    /**
     * @Assert\NotBlank(message="Ce champ est vide")
     */
    private $object;

    /**
     * @Assert\NotBlank(message="Ce champ est vide")
     */
    private $text;

    public function getObject(): ?string
    {
        return $this->object;
    }

    public function setObject(string $object): self
    {
        $this->object = $object;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }
}
