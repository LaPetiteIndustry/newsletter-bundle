<?php
namespace Lpi\NewsletterBundle\Integration\Mailjet;


use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Lpi\NewsletterBundle\Integration\Mailjet\Domain\Contact;
use Lpi\NewsletterBundle\Integration\Mailjet\Domain\ContactList;
use Lpi\NewsletterBundle\Integration\Mailjet\Domain\ContactMetadata;
use Lpi\NewsletterBundle\Integration\Mailjet\Domain\ListRecipient;
use PHPUnit_Framework_MockObject_MockObject;
use Symfony\Bridge\Monolog\Logger;

class MailetServiceTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $client;

    /**
     * @var MailjetService
     */
    private $service;

    protected function setUp()
    {
        $this->client = $this->getMockBuilder('Lpi\NewsletterBundle\Integration\Mailjet\MailjetClient')
            ->disableOriginalConstructor()
            ->setMethods(['send', 'getContact', 'getContactList', 'registerRecipientToContactList', 'updateMetadata', 'getListRecipients'])
            ->getMock();

        $logger = new Logger('mailjet');

        $this->service = new MailjetService($this->client, $logger);


    }

    private function getService()
    {
        return $this->service;
    }


    public function testCreateContactListShouldReturnFalseWhenBadRequestReturnedByService()
    {
        $this->client->method('send')->willThrowException(new \GuzzleHttp\Exception\ClientException('Unable to create contact List', new Request('contactlist', 'uri'), new Response(400)));


        $isCreated = $this->getService()->createList(new ContactList('test list'));
        $this->assertFalse($isCreated);
    }

    public function testCreateContactListShouldReturnTrue()
    {

        $this->client->method('send')->willReturn(new Response(200));
        $isCreated = $this->getService()->createList(new ContactList("test list"));
        $this->assertTrue($isCreated);
    }

    public function testCreateContactShouldReturnTrue()
    {
        $this->client->method('send')->willReturn(new Response(200));
        $this->assertTrue($this->getService()->createContact(new Contact('test-email')));
    }

    public function testCreateContactShouldReturnFalseWhenBadRequestReturnedByService()
    {
        $this->client->method('send')->willThrowException(new \GuzzleHttp\Exception\ClientException('Unable to create contact', new Request('contact', 'uri'), new Response(400)));
        $this->assertFalse($this->getService()->createContact(new Contact('test-email')));
    }

    public function testCreateContactMetaDataShouldReturnTrue()
    {
        $this->client->method('send')->willReturn(new Response(200));
        $contactMetadata = ContactMetadata::createStringMetadata('code postal');

        $isCreated = $this->getService()->addContactMetadata($contactMetadata);
        $this->assertTrue($isCreated);
    }

    public function testCreateContactMetaDataShouldReturnFalseWhenServerReject()
    {
        $this->client->method('send')->willThrowException(new \GuzzleHttp\Exception\ClientException('Unable to create', new Request('contactmetadata', 'uri'), new Response(400)));
        $contactMetadata = ContactMetadata::createStringMetadata('code postal');

        $isCreated = $this->getService()->addContactMetadata($contactMetadata);
        $this->assertFalse($isCreated);
    }

    public function testRegisterContactToContactListShouldReturnTrue()
    {
        $this->client->method('getContact')->willReturn('{ "Count" : 1, "Data" : [{ "CreatedAt" : "2015-07-10T15:26:35Z", "DeliveredCount" : 0, "Email" : "test@email.com", "ID" : 1, "IsOptInPending" : false, "IsSpamComplaining" : false, "LastActivityAt" : "2015-07-10T15:26:35Z", "LastUpdateAt" : "", "Name" : "", "UnsubscribedAt" : "", "UnsubscribedBy" : "" }], "Total" : 1 }');
        $this->client->method('getContactList')->with(242)->willReturn('{ "Count" : 1, "Data" : [{ "Address" : "dgjl8x3hu", "CreatedAt" : "2015-07-13T19:57:37Z", "ID" : 242, "IsDeleted" : false, "Name" : "list-command", "SubscriberCount" : 4 }], "Total" : 1 }');
        $this->client->method('registerRecipientToContactList')->with(new ListRecipient(1, 242))->willReturn(true);
        $list = new ContactList("list-test");
        $list->setID(242);
        $isRegistered = $this->getService()->registerContactToContactList(new Contact("test@email.com"), $list);
        $this->assertTrue($isRegistered);
    }


    public function testRegisterContactThrowIsUnableToRegister()
    {

        $this->client->method('getContact')->willReturn('{ "Count" : 1, "Data" : [{ "CreatedAt" : "2015-07-10T15:26:35Z", "DeliveredCount" : 0, "Email" : "test@email.com", "ID" : 1, "IsOptInPending" : false, "IsSpamComplaining" : false, "LastActivityAt" : "2015-07-10T15:26:35Z", "LastUpdateAt" : "", "Name" : "", "UnsubscribedAt" : "", "UnsubscribedBy" : "" }], "Total" : 1 }');
        $this->client->method('getContactList')->willThrowException(new ContactListNotFoundException());
        $this->client->method('registerRecipientToContactList')->with(new ListRecipient(1, 242))->willThrowException(new UnableToRegisterEmailToContactList());
        $this->assertFalse($this->getService()->registerContactToContactList(new Contact("test@email.com"), new ContactList("list-test")));
    }

    public function testUpdateMetaDataShouldReturnOK()
    {
        $this->client->method('getContact')->with('test@email')->willReturn('{ "Count" : 1, "Data" : [{ "CreatedAt" : "2015-07-10T15:26:35Z", "DeliveredCount" : 0, "Email" : "test@email", "ID" : 199, "IsOptInPending" : false, "IsSpamComplaining" : false, "LastActivityAt" : "2015-07-10T15:26:35Z", "LastUpdateAt" : "", "Name" : "", "UnsubscribedAt" : "", "UnsubscribedBy" : "" }], "Total" : 1 }');

        $metatadas = [
            ['key' => ContactMetadata::createStringMetadata('cp'), 'value' => '06000'],
            ['key' => ContactMetadata::createStringMetadata('name'), 'value' => 'familyName']
        ];
        $this->client->method('updateMetadata')->with('{"ContactID":199,"Data":[{"Name":"cp","Value":"06000"},{"Name":"name","Value":"familyName"}]}')->willReturn(true);
        $isUpdated = $this->getService()->updateContact(new Contact('test@email'), $metatadas);
        $this->assertTrue($isUpdated);
    }


    public function testUpdateMetaDataShouldReturnFalseIfCouldntRegister()
    {

        $this->client->method('getContact')->with('test@email')->willThrowException(new ContactNotFoundException('Unable to load contact'));

        $metatadas = [
            ['key' => ContactMetadata::createStringMetadata('cp'), 'value' => '06000'],
            ['key' => ContactMetadata::createStringMetadata('name'), 'value' => 'familyName']
        ];

        $isUpdated = $this->getService()->updateContact(new Contact('test@email'), $metatadas);
        $this->assertFalse($isUpdated);
    }

    public function testUpdateMetaDataShouldReturnFalseIfCouldntUpdate()
    {

        $this->client->method('getContact')->with('test@email')->willReturn('{ "Count" : 1, "Data" : [{ "CreatedAt" : "2015-07-10T15:26:35Z", "DeliveredCount" : 0, "Email" : "test@email", "ID" : 199, "IsOptInPending" : false, "IsSpamComplaining" : false, "LastActivityAt" : "2015-07-10T15:26:35Z", "LastUpdateAt" : "", "Name" : "", "UnsubscribedAt" : "", "UnsubscribedBy" : "" }], "Total" : 1 }');
        $this->client->method('updateMetadata')->with('{"ContactID":199,"Data":[{"Name":"cp","Value":"06000"},{"Name":"name","Value":"familyName"}]}')->willThrowException(new \GuzzleHttp\Exception\ClientException('Unable to create contact', new Request('contact', 'uri'), new Response(400)));

        $metatadas = [
            ['key' => ContactMetadata::createStringMetadata('cp'), 'value' => '06000'],
            ['key' => ContactMetadata::createStringMetadata('name'), 'value' => 'familyName']
        ];

        $isUpdated = $this->getService()->updateContact(new Contact('test@email'), $metatadas);
        $this->assertFalse($isUpdated);
    }

    public function testClearContactListShouldReturnTrue()
    {
        $this->client->method('getListRecipients')->willReturn('{ "Count" : 2, "Data" : [{ "ContactID" : 4, "ID" : 163, "IsActive" : false, "IsUnsubscribed" : false, "ListID" : 242, "ListName" : "list-test", "UnsubscribedAt" : "" }, { "ContactID" : 4, "ID" : 169, "IsActive" : false, "IsUnsubscribed" : false, "ListID" : 244, "ListName" : "list-test-new", "UnsubscribedAt" : "" }], "Total" : 2 }');
        $isContactListCleared = $this->getService()->clearContactList(new ContactList('list-test-new'));
        $this->assertTrue($isContactListCleared);
    }

    public function testClearContactListShouldThrowExceptionIfListNotFound()
    {
        $this->client->method('getListRecipients')->willReturn('{ "Count" : 2, "Data" : [{ "ContactID" : 4, "ID" : 163, "IsActive" : false, "IsUnsubscribed" : false, "ListID" : 242, "ListName" : "list-test", "UnsubscribedAt" : "" }, { "ContactID" : 4, "ID" : 169, "IsActive" : false, "IsUnsubscribed" : false, "ListID" : 244, "ListName" : "list-test-new", "UnsubscribedAt" : "" }], "Total" : 2 }');
        $this->assertFalse($this->getService()->clearContactList(new ContactList('list-test-new-not-found')));
    }


}