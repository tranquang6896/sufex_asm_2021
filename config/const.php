<?php
define("PAGE_LIMIT", 100);
define("PAGE_MAX_LIMIT",500);
// PAGE LIMIT FOR CUSTOMER, CHECK STAFF
define("PAGE_LIMIT_SPECIFIC",200);
define("PAGE_LIMIT_EXTENT", 100);
define("PAGE_LIMIT_FULL", 9999);

class Constants
{
    static $event_color = array(
        'Patrol' => 'patrolButton',
        'Meeting (Customer)' => 'meetingButton',
        'Desk Work' => 'deskButton',
        'In-house Meeting' => 'inhouseButton',
        'Recruitment Activities' => 'recruitmentButton',
        'Security Guard/ Post Disposition' => 'securiryButton',
        'Others' => 'otherButton'
    );
}
