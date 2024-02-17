<?php

declare(strict_types=1);

namespace Tests;

use Lion\Mailer\Mailer;
use Lion\Mailer\Priority;
use Lion\Test\Test;

class MailhogTest extends Test
{
    public function testMail(): void
    {
        $this->assertTrue(
            Mailer::account(env->MAIL_NAME)
                ->subject('Test Priority')
                ->from('sleon@dev.com', 'Sleon')
                ->addAddress('jjerez@dev.com', 'Jjerez')
                ->body('Send Mailer')
                ->priority(Priority::HIGH)
                ->send()
        );
    }

    public function testMailSupp(): void
    {
        $this->assertTrue(
            Mailer::account(env->MAIL_NAME_SUPP)
                ->subject('Test Priority')
                ->from('sleon@dev.com', 'Sleon')
                ->addAddress('jjerez@dev.com', 'Jjerez')
                ->body('Send Mailer')
                ->priority(Priority::HIGH)
                ->send()
        );
    }
}
