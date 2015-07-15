<?php
/**
 * Created by IntelliJ IDEA.
 * User: david
 * Date: 12/07/2015
 * Time: 08:50
 */

namespace Lpi\NewsletterBundle\Integration\Mailjet\Domain;


use JMS\Serializer\Annotation\Type;

class ContactMetadata
{
    /**
     * @Type("string")
     */
    private $Datatype;

    /**
     * @Type("string")
     */
    private $Name;

    /**
     * @Type("string")
     */
    private $ID;

    /**
     * @Type("string")
     */
    private $NameSpace;

    const TYPE_STR = 'str';
    const TYPE_INT = 'int';
    const TYPE_FLOAT = 'float';
    const TYPE_BOOL = 'bool';

    const NS_STATIC = 'static';
    const NS_HISTORIC = 'historic';

    /**
     * ContactMetadata constructor.
     * @param $Datatype
     * @param $NameSpace
     * @param $Name
     */
    private function __construct($Datatype, $NameSpace, $Name)
    {
        $this->Datatype = $Datatype;
        $this->NameSpace = $NameSpace;
        $this->Name = $Name;
    }

    static public function createStringMetadata($name) {
        return new ContactMetadata(self::TYPE_STR,self::NS_STATIC,$name);
    }

    public function payload()
    {
        return json_encode([
            'DataType' => $this->Datatype,
            'Name' => $this->Name,
            'NameSpace' => $this->NameSpace
        ]);
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->Name;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->ID;
    }




}