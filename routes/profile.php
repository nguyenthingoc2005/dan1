<?php
/**
 * ==============================================================================
 * PROFILE ROUTES
 * ==============================================================================
 * 
 * Pattern: ?act=profile, ?act=profile/edit, etc.
 * 
 * @version 1.0
 * @date 2024-12-02
 * ==============================================================================
 */

require_once CONTROLLERS_PATH . '/ProfileController.php';
$profileController = new ProfileController($pdo);

switch ($act) {
    case 'profile':
    case 'profile/index':
        $profileController->index();
        break;

    case 'profile/edit':
        $profileController->edit();
        break;

    case 'profile/update':
        $profileController->update();
        break;

    case 'profile/change-password':
        $profileController->changePassword();
        break;

    case 'profile/update-password':
        $profileController->updatePassword();
        break;

    default:
        http_response_code(404);
        require VIEWS_PATH . '/errors/404.php';
        break;
}
