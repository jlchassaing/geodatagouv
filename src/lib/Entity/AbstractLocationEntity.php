<?php
/**
 * @author jlchassaing <jlchassaing@gmail.com>
 * @licence MIT
 */
namespace GeoDataGouv\Entity;

use Doctrine\ORM\Mapping as ORM;

abstract class AbstractLocationEntity
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type=integer)
     * @var integer
     */
    protected $id;

    /**
     * @ORM\Column(type=string, length=255, nullable=true)
     * @var string
     */
    protected $address;

    /**
     * @ORM\Column(type=float, nullable=true)
     * @var float
     */
    protected $longitude;

    /**
     * @ORM\Column(type=float, nullable=true)
     * @var float
     */
    protected $latitude;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    /**
     * @return float
     */
    public function getLongitude(): float
    {
        return $this->longitude;
    }

    /**
     * @param float $longitude
     */
    public function setLongitude(float $longitude): void
    {
        $this->longitude = $longitude;
    }

    /**
     * @return float
     */
    public function getLatitude(): float
    {
        return $this->latitude;
    }

    /**
     * @param float $latitude
     */
    public function setLatitude(float $latitude): void
    {
        $this->latitude = $latitude;
    }



}