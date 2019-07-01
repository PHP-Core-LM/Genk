<?php
/**
 * Created by PhpStorm.
 * User: dell
 * Date: 4/17/2019
 * Time: 11:28 AM
 */

class Post
{
    private $ID = "";
    private $Title = "";
    private $Thumbnail = "";
    private $Author = "";
    private $Date = "";
    private $Descript = "";
    private $Content = "";
    private $Images = null;


    /**
     * Post constructor.
     * @param $ID
     * @param $Title
     * @param $Thumbnail
     * @param $Author
     * @param $Date
     * @param $Descript
     * @param $Content
     * @param $Images
     */
    function __construct($ID, $Title, $Thumbnail, $Author, $Date, $Descript, $Content, $Images)
    {
        $this->ID = $ID;
        $this->Title = $Title;
        $this->Thumbnail = $Thumbnail;
        $this->Author = $Author;
        $this->Date = $Date;
        $this->Descript = $Descript;
        $this->Content = $Content;
        $this->Images = $Images;
    }


    /**
     * @return string
     */
    public function getID(): string
    {
        return $this->ID;
    }


    /**
     * @param string $ID
     */
    public function setID(string $ID): void
    {
        $this->ID = $ID;
    }


    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->Title;
    }


    /**
     * @param string $Title
     */
    public function setTitle(string $Title): void
    {
        $this->Title = $Title;
    }


    /**
     * @return string
     */
    public function getThumbnail(): string
    {
        return $this->Thumbnail;
    }


    /**
     * @param string $Thumbnail
     */
    public function setThumbnail(string $Thumbnail): void
    {
        $this->Thumbnail = $Thumbnail;
    }


    /**
     * @return string
     */
    public function getAuthor(): string
    {
        return $this->Author;
    }


    /**
     * @param string $Author
     */
    public function setAuthor(string $Author): void
    {
        $this->Author = $Author;
    }


    /**
     * @return string
     */
    public function getDate(): string
    {
        return $this->Date;
    }


    /**
     * @param string $Date
     */
    public function setDate(string $Date): void
    {
        $this->Date = $Date;
    }


    /**
     * @return string
     */
    public function getDescript(): string
    {
        return $this->Descript;
    }


    /**
     * @param string $Descript
     */
    public function setDescript(string $Descript): void
    {
        $this->Descript = $Descript;
    }


    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->Content;
    }


    /**
     * @param string $Content
     */
    public function setContent(string $Content): void
    {
        $this->Content = $Content;
    }
}