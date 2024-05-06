<?php

declare(strict_types=1);

namespace App\Html\Email;

use Lion\Bundle\Helpers\Commands\Html;
use Lion\Bundle\Interface\HtmlInterface;

/**
 * Password recovery template
 *
 * @package App\Html\Email
 */
class RecoveryAccountHtml extends Html implements HtmlInterface
{
    /**
     * {@inheritdoc}
     */
    public function template(): RecoveryAccountHtml
    {
        $this->add(
            <<<HTML
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Recovery code</title>
            </head>
            <body>
                <table cellpadding="0" cellspacing="0" border="0" width="100%" bgcolor="#f0f0f0">
                    <tr>
                        <td align="center">
                            <table cellpadding="0" cellspacing="0" border="0" width="600">
                                <tr>
                                    <td align="center" bgcolor="#ffffff" style="padding: 40px 0 30px 0; color: #333; font-size: 24px; font-weight: bold; font-family: Arial, sans-serif;">
                                        Recovery code
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" bgcolor="#ffffff" style="padding: 20px 30px 40px 30px; color: #333; font-size: 16px; font-family: Arial, sans-serif;">
                                        <p>This is your recovery code!</p>
                                        <p>Your recovery code is: <strong>{{ CODE_REPLACE }}</strong></p>
                                        <p>Enter this code on the confirmation page to recover your password.</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" bgcolor="#ffffff" style="padding: 20px 30px 40px 30px; color: #333; font-size: 14px; font-family: Arial, sans-serif;">
                                        If you didn't request this confirmation, please ignore this message.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </body>
            </html>
            HTML
        );

        return $this;
    }
}
