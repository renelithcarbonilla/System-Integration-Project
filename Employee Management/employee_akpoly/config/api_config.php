<?php
return [
    'attendance_api' => [
        'base_url' => 'https://api.your-attendance-system.com/v1',
        'api_token' => getenv('ATTENDANCE_API_TOKEN'),
        'auth_type' => 'bearer', // or 'basic', 'api_key'
        'timeout' => 30,
        'endpoints' => [
            'get_attendance' => '/attendance',
            'get_employees' => '/employees',
            'webhook_url' => 'https://your-domain.com/api/v1/attendance'
        ]
    ],
    
    'payroll_api' => [
        'base_url' => 'https://api.your-payroll-system.com',
        'username' => getenv('PAYROLL_API_USER'),
        'password' => getenv('PAYROLL_API_PASS'),
        'auth_type' => 'basic',
        'company_id' => getenv('PAYROLL_COMPANY_ID'),
        'endpoints' => [
            'submit_payroll' => '/payroll/entries',
            'get_payroll_status' => '/payroll/runs'
        ]
    ],
    
    'webhook_secret' => getenv('WEBHOOK_SECRET'),
    'sync_schedule' => [
        'auto_sync' => true,
        'schedule' => '0 20 * * 5', // Every Friday at 8 PM
        'timezone' => 'America/New_York'
    ]
];
?>