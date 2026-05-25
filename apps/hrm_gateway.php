<?php
require_onces();

$thisUser = new User();
$thisApp  = new HrmGateway();

require_once(HEADER);

if ($thisUser->isAuthenticated()) {
    $thisApp->run();
} else {
    $thisUser->goLogin();
}

require_once(FOOTER);
