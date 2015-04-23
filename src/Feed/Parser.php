<?php
/**
 * @package feed-parser
 * @author denisdeejay
 * Date: 23/04/2015
 * Time: 11:36
 */

namespace Feed;


/**
 * Class Parser
 * @package Feed
 */
class Parser {

    /** @var  \SimpleXMLElement */
    private $xmlFeed;


    /**
     * @param string|null $urlOrString
     */
    public function __construct($urlOrString = null){
        if(!is_null($urlOrString)){
            $this->setXmlFeed($urlOrString);
        }
    }


    /**
     * @param string $urlOrString
     * @return $this
     */
    public function setXmlFeed($urlOrString){
        $this->xmlFeed = new \SimpleXMLElement($urlOrString, NULL, TRUE);
        return $this;
    }


    /**
     * @return \SimpleXMLElement
     */
    public function getXmlFeed(){
        return $this->xmlFeed;
    }


    /**
     * Return the root element name in lower case
     * @return null|string
     */
    public function getNormalizedName(){
        if($this->getXmlFeed()){
            return strtolower($this->getXmlFeed()->getName());
        }
        return null;
    }


    /**
     * @return bool|null
     */
    public function isAtom(){
        if($this->getNormalizedName()){
            return $this->getNormalizedName() == 'feed';
        }
        return null;
    }


    /**
     * @return bool|null
     */
    public function isRss(){
        if($this->getNormalizedName()){
            return $this->getNormalizedName() == 'rss';
        }
        return null;
    }


    /**
     * Return the feed title if present
     * @return null|string
     */
    public function getTitle(){
        if($this->isAtom()){
            return isset($this->getXmlFeed()->title[0]) ? (string) $this->getXmlFeed()->title[0] : null;
        }
        if($this->isRss()){
            if(isset($this->getXmlFeed()->channel)){
                return isset($this->getXmlFeed()->channel->title[0]) ? (string) $this->getXmlFeed()->channel->title[0] : null;
            }
        }
        return null;
    }


    /**
     * Return the first link node value
     * @return null|string
     */
    public function getLink(){
        if($this->isAtom()){
            if(isset($this->getXmlFeed()->link[0])){
                $attr = $this->getXmlFeed()->link[0]->attributes();
                return isset($attr['href']) ? (string) $attr['href'] : null;
            }
        }
        if($this->isRss()){
            if(isset($this->getXmlFeed()->channel)){
                return isset($this->getXmlFeed()->channel->link[0]) ? (string) $this->getXmlFeed()->channel->link[0] : null;
            }
        }
        return null;
    }


    /**
     * @return \DateTime|null
     */
    public function getPubDate(){
        $dateValue = null;
        if($this->isAtom() && isset($this->getXmlFeed()->updated[0])){
            $dateValue = (string) $this->getXmlFeed()->updated[0];
        }
        if($this->isRss() && isset($this->getXmlFeed()->channel)){
            if(isset($this->getXmlFeed()->channel->pubDate[0])){
                $dateValue = (string) $this->getXmlFeed()->channel->pubDate[0];
            }
        }
        if($dateValue){
            return new \DateTime($dateValue);
        }
        return null;
    }


    /**
     * Return the list of items as an array
     *
     * <code>
     * array(
     *      'title' => 'Awesome title',
     *      'link' => 'http://www.denisdeejay.com',
     *      'description' => '<p>Awesome blog !</p>',
     *      'pubDate' => DateTime Object,
     *      'author' => 'denisdeejay'
     * )
     * </code>
     * @return array
     */
    public function getItemsAsArray(){
        $itemsNodes = null;
        if($this->isAtom()){
            if(isset($this->getXmlFeed()->entry)){
                $itemsNodes = $this->getXmlFeed()->entry;
            }
        }
        if($this->isRss()){
            if(isset($this->getXmlFeed()->channel) && isset($this->getXmlFeed()->channel->item)){
                $itemsNodes = $this->getXmlFeed()->channel->item;
            }
        }
        $items = array();
        if(!is_null($itemsNodes)){
            foreach($itemsNodes as $node){
                $item = array();
                $item['title'] = isset($node['title']) ? (string) $node['title'] : null;
                $item['link'] = null;
                $item['description'] = null;
                $item['pubDate'] = null;
                $item['author'] = null;
                if($this->isAtom()){
                    if(isset($node->link)){
                        $linkAttr = $node->link->attributes();
                        if(isset($linkAttr['href'])){
                            $item['link'] = (string) $linkAttr['href'];
                        }
                    }
                    /** @link http://fr.wikipedia.org/wiki/Atom#Diff.C3.A9rences_avec_RSS */
                    if(isset($node->summary)){
                        $item['description'] = (string) $node->summary;
                    }
                    if(isset($node->content)){
                        $item['description'] = (string) $node->content;
                    }
                    if(isset($node->published)){
                        $item['pubDate'] = new \DateTime((string) $node->published[0]);
                    }
                    if(isset($node->updated)){
                        $item['pubDate'] = new \DateTime((string) $node->updated[0]);
                    }
                    if(isset($node->author) && isset($node->author->name)){
                        $item['author'] = (string) $node->author->name;
                    }
                }
                if($this->isRss()){
                    if(isset($node->link)){
                        $item['link'] = (string) $node->link[0];
                    }
                    if(isset($node->description)){
                        $item['description'] = (string) $node->description[0];
                    }
                    if(isset($node->pubDate)){
                        $item['pubDate'] = new \DateTime((string) $node->pubDate[0]);
                    }
                    if(isset($node->author)){
                        $item['author'] = (string) $node->author;
                    }
                }

                $items[] = $item;
            }
        }
        return $items;
    }

}