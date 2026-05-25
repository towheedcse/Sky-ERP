<?php
    require_onces();

    $thisApp  = new PermissionManager();
    $thisUser = new User();

    // API commands skip the HTML header/footer
    $cmd      = getRequest('cmd');
    $isApiCmd = (strlen($cmd) >= 4 && substr($cmd, 0, 4) === 'api_');

    if (!$isApiCmd) {
        require_once(HEADER);
    }

    if ($thisUser->isAuthenticated()) {
        $thisApp->run();
    } else {
        if ($isApiCmd) {
            while (ob_get_level() > 0) { ob_end_clean(); }
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(array('status' => false, 'message' => 'Not authenticated'));
            exit;
        }
        $thisUser->goLogin();
    }
?>
