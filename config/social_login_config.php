<?php
// Google OAuth Configuration
define('GOOGLE_CLIENT_ID', '1047956936106-5dhv2av4f9p6915quc8054hktc4f1h05.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', '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');
define('GOOGLE_REDIRECT_URI', 'http://localhost/game_shop/pages/google_callback.php');

// Facebook OAuth Configuration
define('FACEBOOK_APP_ID', '702233045797120');
define('FACEBOOK_APP_SECRET', '6812bed9066fbeceb7214fa3413f98d9');
define('FACEBOOK_REDIRECT_URI', 'http://localhost/game_shop/pages/facebook_callback.php');
define('FACEBOOK_GRAPH_VERSION', 'v19.0');
define('FACEBOOK_OAUTH_URL', 'https://www.facebook.com/v19.0/dialog/oauth');

// Debug mode
error_reporting(E_ALL);
ini_set('display_errors', 1);
?> 