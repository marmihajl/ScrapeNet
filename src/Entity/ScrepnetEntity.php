<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *  @ORM\Table(indexes={@ORM\Index(name="fulltext_index",
columns={"title","description"},
flags={"fulltext"})})
 * @ORM\Entity(repositoryClass="App\Repository\ScrepnetEntityRepository")
 */
class ScrepnetEntity
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string", length=280)
     */
    private $slug;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $title=null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description=null;

    /**
     * @ORM\Column(type="string", length=280, nullable=true)
     */
    private $path = null;

    /**
     * @ORM\Column(type="string", length=280, nullable=true)
     */
    private $url=null;

    /**
     * @ORM\Column(type="string", length=280, nullable=true)
     */
    private $escapeUrl=null;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=280, nullable=true)
     */
    private $image=null;

    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param mixed $slug
     */
    public function setSlug($slug): void
    {
        $this->slug = $slug;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title): void
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param mixed $path
     */
    public function setPath($path): void
    {
        $this->path = $path;
    }


    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url): void
    {
        $this->url = $url;
    }

    /**
     * @return mixed
     */
    public function getEscapeUrl()
    {
        return $this->escapeUrl;
    }

    /**
     * @param mixed $escapeUrl
     */
    public function setEscapeUrl($escapeUrl): void
    {
        $this->escapeUrl = $escapeUrl;
    }



    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date): void
    {
        $this->date = $date;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $image
     */
    public function setImage($image): void
    {
        $this->image = $image;
    }
}
