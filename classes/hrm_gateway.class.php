<?php
class HrmGateway
{
    private static $allowed_pages = [
        'dashboard/Userhome',
        'employee',
        'staff_attendance',
        'leavemanage',
        'outstationduty',
        'salary_sheet',
        'voucher',
    ];

    public function run()
    {
        $page = getRequest('page');

        if (empty($page) || !in_array($page, self::$allowed_pages, true)) {
            $page = 'dashboard/Userhome';
        }

        $raw_token = $this->generateToken();

        if (!$this->storeToken($raw_token)) {
            echo '<div style="padding:20px;color:red;">Error: Could not create SSO token. Please try again.</div>';
            return;
        }

        $this->cleanExpiredTokens();

        $hrm_url = HRM_BASE_URL
            . '/index.php/sso/login'
            . '?token=' . urlencode($raw_token)
            . '&goto='  . urlencode($page);

        // Redirect the full browser window to HRM (full-screen, no iframe)
        echo '<script>window.location.replace(' . json_encode($hrm_url) . ');</script>';
    }

    private function generateToken()
    {
        if (function_exists('random_bytes')) {
            return bin2hex(random_bytes(32));
        }
        return bin2hex(openssl_random_pseudo_bytes(32));
    }

    private function storeToken($raw_token)
    {
        $token   = mysql_real_escape_string($raw_token);
        $userid  = mysql_real_escape_string((string) getFromSession('userid'));
        $expires = date('Y-m-d H:i:s', time() + SSO_TOKEN_EXPIRY);

        $sql = "INSERT INTO sso_tokens (token, userid, expires_at)
                VALUES ('$token', '$userid', '$expires')";

        return (bool) mysql_query($sql);
    }

    private function cleanExpiredTokens()
    {
        mysql_query("DELETE FROM sso_tokens WHERE expires_at < NOW() OR used = 1");
    }
}
