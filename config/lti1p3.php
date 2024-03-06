<?php 
// You can generate a private key here (2048 bit recommended):
// https://travistidwell.com/jsencrypt/demo/

return [

    'ENABLE_AUTH' => true,
    
    'VERIFY_HTTPS_CERTIFICATE' => true,

    'SIGNATURE_METHOD' => 'RS256',
    
    'KID' => 'default_kid', 
    
    'PRIVATE_KEY' => <<< EOD
    -----BEGIN RSA PRIVATE KEY-----
    MIICWgIBAAKBgFJBJE/76pA6sJ4oOiATvIY8VpOEXsGyRJm/s+bchFapvi/KGX5k
    c+AINMXUhruPNcN07kweZg5FNIjbj4Le5esLPwLPptXWn8rtAOp4Yjz3J079vd0J
    V1Y/sg6dGwOq6+4NsQk6tP5k05S5VGC/iyksVmS4VhmwplpDCLa3nayJAgMBAAEC
    gYAFtuLX769vD7S/eBn5qoVZag21t+eBW2oqqEfOTRmgj7BXz5mPz1jBgrBv4gDu
    PNfGWXRIs3/xk9s/eslwlR2x70y8bB8O+3jwEGaJ2xVLGqhFnm2X/xFIDwvfx3a+
    uTAjPBl0QcUAfp75bmGhxd4DcvTOdfYrVnlpiIaznsqvtQJBAKLxWELHj17QSZkt
    fu7tKCc/1d5k97VbH9sqGUZRjTVx0lB3Rh6Pxnl9zPEfKKl8ArAkSrQUOEDwvUSi
    RXdTT98CQQCBOvVW+uBxZ7ygp2FTMLL7AkcY+0+PLR9gp7x5j9HjIHWTd3qfBsjy
    9J4ngi9JZ/h+UWV8YNg8OEiZ2orOeHCXAkA3bIhtDpxBz+943vTSKHEECL6iiw2G
    7pwDXGqEdLDngPPc0vVS5+zG3nebfNHD9J6lc3LFliscS8bVVazzTa9ZAkBVM1Hd
    brnbCtHJ+ZCpEEpwQygsyEPD2bP+PZh9bNysKhJaj0NA5XBG/g/nbGVQuEOUxIVs
    Y00tJs524SbyyWUxAkBGW63QFvEhYKykOfGP0MlFkwoHS4kNFSflwXqPu476ys04
    LrVmql84RpD6ffocMNqJgrCZs/X6LnUlDMx9hyT0
    -----END RSA PRIVATE KEY-----
    EOD,
    
    'LTI_ADMIN_ROLES_SPEC' => [
        'Administrator', 
        'Developer',
        'ExternalDeveloper',
        'ExternalSupport', 
        'ExternalSystemAdministrator', 
        'Support', 
        'SystemAdministrator'
    ],

    'LTI_CONTENT_DEVELOPER_ROLES_SPEC' => [
        'ContentDeveloper', 
        'ContentExpert',
        'ExternalContentExpert', 
        'Librarian'
    ],

    'LTI_INSTRUCTOR_ROLES_SPEC' => [
        'ExternalInstructor',
        'Grader',
        'GuestInstructor',
        'Lecturer',
        'PrimaryInstructor',
        'SecondaryInstructor',
        'TeachingAssistant',
        'TeachingAssistantGroup',
        'TeachingAssistantOffering', 
        'TeachingAssistantSection',
        'TeachingAssistantSectionAssociation', 
        'TeachingAssistantTemplate', 
        'Instructor'
    ],

    'LTI_LEARNER_ROLES_SPEC' => [
        'GuestLearner',
        'ExternalLearner',
        'Instructor',
        'Learner',
        'NonCreditLearner',
        'Student'
    ],

    'LTI_MANAGER_ROLES_SPEC' => [
        'AreaManager', 
        'CourseCoordinator', 
        'ExternalObserver',
        'Manager', 
        'Observer'
    ],

    'LTI_MENTOR_ROLES_SPEC' => [
        'Advisor',
        'Auditor',
        'ExternalAdvisor',
        'ExternalAuditor',
        'ExternalLearningFacilitator',
        'ExternalMentor',
        'ExternalReviewer',
        'ExternalTutor',
        'LearningFacilitator',
        'Mentor',
        'Reviewer',
        'Tutor'
    ],

    'LTI_OFFICER_ROLES_SPEC' => [
        'Chair',
        'Communications',
        'Secretary',
        'Treasurer',
        'Vice-Chair'
    ],

    'LTI_TEST_USER_ROLE_SPEC' => [
        'TestUser'
    ],

    'LTI_MEMBER_ROLE_SPEC' => [
        'Member'
    ]

];