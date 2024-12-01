<?php
// Rate limiting configuration
return [
    // Default rate limit settings
    'default' => [
        'requests' => 60, // Number of requests
        'period' => 60,   // Time period in seconds
    ],
    
    // Rate limits for specific routes
    'routes' => [
        // Login attempts
        'users/login' => [
            'requests' => 5,
            'period' => 300, // 5 minutes
        ],
        
        // Registration attempts
        'users/register' => [
            'requests' => 3,
            'period' => 3600, // 1 hour
        ],
        
        // Menu view rate limit
        'menu/index' => [
            'requests' => 120,
            'period' => 60,
        ],
        
        // QR code generation
        'menu/generateQR' => [
            'requests' => 10,
            'period' => 300,
        ],
        
        // File uploads
        'upload/*' => [
            'requests' => 20,
            'period' => 300,
        ]
    ]
];
