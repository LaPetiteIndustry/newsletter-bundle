
        <service id="newsletter_factory" class="Lpi\NewsletterBundle\Integration\UserAuthenticationFactory"/>
        <service id="user.password.mailjet"
                 class="Lpi\NewsletterBundle\Integration\UserPasswordAuthentication"
                 factory-service="newsletter_factory"
                 factory-method="createUserPasswordAuthentication"
                >
            <argument>%mailjet_key%</argument>
            <argument>%mailjet_secret%</argument>
        </service>


        <service id="lpi.mailjet.client" class="Lpi\NewsletterBundle\Integration\Mailjet\MailjetClient">
            <argument type="service" id="user.password.mailjet"/>
            <argument type="service" id="logger"/>
        </service>

        <service id="lpi.mailjet.service" class="Lpi\NewsletterBundle\Integration\Mailjet\MailjetService">
            <tag name="monolog.logger" channel="newsletter"/>
            <argument type="service" id="lpi.mailjet.client"/>
            <argument type="service" id="logger"/>
        </service>

        <service id="lpi.newsletter_logger" class="Symfony\Bridge\Monolog\Logger">
            <tag name="monolog.logger" channel="newsletter"/>
            <argument>lpi.newsletter_logger</argument>
            <argument type="service" id="monolog.handler.newsletter"/>
        </service>
