<?php

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

class Contact
{
    /**
     * @var string|null
     *
     * @Assert\NotBlank(message="Veuillez renseigner votre nom")
     */
    private $name;

    /**
     * @var string|null
     *
     * @Assert\NotBlank(message="Veuillez renseigner le sujet du message")
     */
    private $sujet;

    /**
     * @var string|null
     *
     * @Assert\NotBlank(message="Veuillez ajouter votre message")
     */
    private $message;

    /**
     * @var string|null
     *
     * @Assert\NotBlank(message="Veuillez ajouter votre mail")
     * @Assert\Email(message="Adresse mail non valide")
     */
    private $mail;

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return Contact
     */
    public function setName(?string $name): Contact
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSujet(): ?string
    {
        return $this->sujet;
    }

    /**
     * @param string|null $sujet
     * @return Contact
     */
    public function setSujet(?string $sujet): Contact
    {
        $this->sujet = $sujet;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @param string|null $message
     * @return Contact
     */
    public function setMessage(?string $message): Contact
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getMail(): ?string
    {
        return $this->mail;
    }

    /**
     * @param string|null $mail
     * @return Contact
     */
    public function setMail(?string $mail): Contact
    {
        $this->mail = $mail;
        return $this;
    }
}
