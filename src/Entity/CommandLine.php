<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CommandLineRepository")
 */
class CommandLine
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $Quantity;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Product", inversedBy="commandLines")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ProductOrder;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Order", inversedBy="commandLines")
     * @ORM\JoinColumn(nullable=false)
     */
    private $OrderCommand;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?int
    {
        return $this->Quantity;
    }

    public function setQuantity(int $Quantity): self
    {
        $this->Quantity = $Quantity;

        return $this;
    }

    public function getProductOrder(): ?Product
    {
        return $this->ProductOrder;
    }

    public function setProductOrder(?Product $ProductOrder): self
    {
        $this->ProductOrder = $ProductOrder;

        return $this;
    }

    public function getOrderCommand(): ?Order
    {
        return $this->OrderCommand;
    }

    public function setOrderCommand(?Order $OrderCommand): self
    {
        $this->OrderCommand = $OrderCommand;

        return $this;
    }
}
