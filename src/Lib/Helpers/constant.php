<?php

$env = env('APP_ENV');

// STATUS LISTS

defined('STAT_PUBLISHED')
    or define('STAT_PUBLISHED' , 'published' );
    
defined('STAT_PENDING')
    or define('STAT_PENDING' , 'pending' );

defined('STAT_DRAFT')
    or define('STAT_DRAFT' , 'draft' );

defined('STAT_APPROVED')
    or define('STAT_APPROVED' , 'approved' );

defined('STAT_TRASH')
    or define('STAT_TRASH' , 'trash' );

defined('STAT_SPAM')
    or define('STAT_SPAM' , 'spam' );


defined('STAT_INACTIVE')
    or define('STAT_INACTIVE' , 'inactive' );



// UPLOAD TYPE

defined('UPLOAD_LINK')
    or define('UPLOAD_LINK' , 'link' );

defined('UPLOAD_FILE')
    or define('UPLOAD_FILE' , 'file' );


defined('UPLOAD_PATH')
    or define('UPLOAD_PATH' , 'uploads' );

defined('UPLOAD_AVATAR')
    or define('UPLOAD_AVATAR' , 'avatar' );

defined('UPLOAD_LOGO')
    or define('UPLOAD_LOGO' , 'logo' );


defined('UPLOAD_APPIMAGE')
    or define('UPLOAD_APPIMAGE' , 'app-image' );


defined('UPLOAD_SCREENSHOT')
    or define('UPLOAD_SCREENSHOT' , 'screenshots' );


defined('UPLOAD_APP_APK')
    or define('UPLOAD_APP_APK' , 'apk' );