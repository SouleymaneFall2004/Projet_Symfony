<?php

namespace App\Entity;

use App\Repository\DetailDetteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DetailDetteRepository::class)]
class DetailDette
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Dette $detteId = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Article $articleId = null;

    #[ORM\Column]
    private ?int $qte = null;

    public function getQte(): ?int
    {
        return $this->qte;
    }

    public function setQte(int $qte): static
    {
        $this->qte = $qte;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDetteId(): ?Dette
    {
        return $this->detteId;
    }

    public function setDetteId(?Dette $detteId): static
    {
        $this->detteId = $detteId;

        return $this;
    }

    public function getArticleId(): ?Article
    {
        return $this->articleId;
    }

    public function setArticleId(?Article $articleId): static
    {
        $this->articleId = $articleId;

        return $this;
    }
}
