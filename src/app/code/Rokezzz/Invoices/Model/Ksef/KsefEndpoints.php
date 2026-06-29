<?php

declare(strict_types=1);

namespace Rokezzz\Invoices\Model\Ksef;

class KsefEndpoints
{
    public const AUTH_CHALLENGE = '/api/v2/auth/challenge';
    public const SECURITY_PUBLIC_KEYS = '/api/v2/security/public-key-certificates';
    public const AUTH_KSEF_TOKEN = '/api/v2/auth/ksef-token';
    public const AUTH_STATUS = '/api/v2/auth/%s';
    public const AUTH_TOKEN_REDEEM = '/api/v2/auth/token/redeem';
    public const SESSIONS_ONLINE = '/api/v2/sessions/online';
    public const SESSIONS_ONLINE_INVOICES = '/api/v2/sessions/online/%s/invoices';
    public const SESSIONS_ONLINE_CLOSE = '/api/v2/sessions/online/%s/close';
    public const SESSION_INVOICE_STATUS = '/api/v2/sessions/%s/invoices/%s';
    public const SESSION_INVOICE_UPO = '/api/v2/sessions/%s/invoices/ksef/%s/upo';
}
