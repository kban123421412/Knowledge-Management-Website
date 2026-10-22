USE team27;

-- Teams
INSERT INTO teams (id, name) VALUES
(1, 'Team Alpha'),
(2, 'Team Beta'),
(3, 'Team Gamma'),
(4, 'Team Delta');

-- Users: 1 project manager, 4 team leaders, 16 employees = 21 users
INSERT INTO users (id, email, password, name, role, team_id) VALUES
-- Project Manager
(1,  'manager@gmail.com',     'manager123',  'Alice Morgan',     'project_manager', NULL),

-- Team Leaders
(2,  'leader1@gmail.com',     'leader123',   'Bob Chen',         'team_leader', 1),
(3,  'leader2@gmail.com',     'leader123',   'Carol Davis',      'team_leader', 2),
(4,  'leader3@gmail.com',     'leader123',   'Daniel Martinez',  'team_leader', 3),
(5,  'leader4@gmail.com',     'leader123',   'Emma Thompson',    'team_leader', 4),

-- Team Alpha employees
(6,  'employee@gmail.com',    'employee123', 'Dave Wilson',      'employee', 1),
(7,  'emp2@gmail.com',        'employee123', 'Eve Johnson',      'employee', 1),
(8,  'fharris@gmail.com',     'employee123', 'Fiona Harris',     'employee', 1),
(9,  'gpatel@gmail.com',      'employee123', 'George Patel',     'employee', 1),

-- Team Beta employees
(10, 'emp3@gmail.com',        'employee123', 'Frank Lee',        'employee', 2),
(11, 'emp4@gmail.com',        'employee123', 'Grace Kim',        'employee', 2),
(12, 'hwalker@gmail.com',     'employee123', 'Hannah Walker',    'employee', 2),
(13, 'iscott@gmail.com',      'employee123', 'Isaac Scott',      'employee', 2),

-- Team Gamma employees
(14, 'jbrown@gmail.com',      'employee123', 'James Brown',      'employee', 3),
(15, 'kclark@gmail.com',      'employee123', 'Katie Clark',      'employee', 3),
(16, 'lturner@gmail.com',     'employee123', 'Liam Turner',      'employee', 3),
(17, 'mwright@gmail.com',     'employee123', 'Mia Wright',       'employee', 3),

-- Team Delta employees
(18, 'nrobinson@gmail.com',   'employee123', 'Noah Robinson',    'employee', 4),
(19, 'owhite@gmail.com',      'employee123', 'Olivia White',     'employee', 4),
(20, 'pking@gmail.com',       'employee123', 'Peter King',       'employee', 4),
(21, 'qadams@gmail.com',      'employee123', 'Quinn Adams',      'employee', 4);

INSERT INTO tasks (title, description, assigned_to, team_id, priority, difficulty, status, created_at, due_date, completed_at) VALUES

-- === TEAM ALPHA === --

-- Dave Wilson (id=6)
('Set up project repository',         'Initialize Git repo and add README',                6, 1, 'high',   'easy',   'completed',   CURDATE() - INTERVAL 7 WEEK, CURDATE() - INTERVAL 6 WEEK + INTERVAL 2 DAY, CURDATE() - INTERVAL 6 WEEK + INTERVAL 1 DAY),
('Design database schema',            'Create ER diagram and SQL scripts',                 6, 1, 'high',   'hard',   'completed',   CURDATE() - INTERVAL 6 WEEK, CURDATE() - INTERVAL 5 WEEK,                  CURDATE() - INTERVAL 5 WEEK + INTERVAL 2 DAY),
('Implement login page',              'Build authentication UI with Bootstrap',            6, 1, 'high',   'medium', 'completed',   CURDATE() - INTERVAL 5 WEEK, CURDATE() - INTERVAL 4 WEEK,                  CURDATE() - INTERVAL 4 WEEK - INTERVAL 1 DAY),
('Create API endpoints',              'Build REST endpoints for task CRUD',                6, 1, 'medium', 'hard',   'in_progress', CURDATE() - INTERVAL 3 WEEK, CURDATE() + INTERVAL 3 DAY,                   NULL),
('Write unit tests',                  'Add PHPUnit tests for core modules',                6, 1, 'low',    'medium', 'not_started', CURDATE() - INTERVAL 1 WEEK, CURDATE() + INTERVAL 2 WEEK,                  NULL),
('Fix navigation bug',                'Navbar active state not updating correctly',        6, 1, 'medium', 'easy',   'completed',   CURDATE() - INTERVAL 4 WEEK, CURDATE() - INTERVAL 3 WEEK,                  CURDATE() - INTERVAL 3 WEEK - INTERVAL 2 DAY),
('Documentation update',              'Update README with setup instructions',             6, 1, 'low',    'easy',   'completed',   CURDATE() - INTERVAL 2 WEEK, NULL,                                         CURDATE() - INTERVAL 1 WEEK),
('Integrate email notifications',     'Send email alerts for task assignments',            6, 1, 'medium', 'hard',   'not_started', CURDATE() - INTERVAL 2 DAY,  CURDATE() + INTERVAL 3 WEEK,                  NULL),

-- Eve Johnson (id=7)
('Design landing page mockup',        'Create Figma mockup for homepage',                  7, 1, 'high',   'medium', 'completed',   CURDATE() - INTERVAL 7 WEEK, CURDATE() - INTERVAL 6 WEEK,                  CURDATE() - INTERVAL 6 WEEK - INTERVAL 1 DAY),
('Implement responsive CSS',          'Make all pages mobile-friendly',                    7, 1, 'medium', 'medium', 'completed',   CURDATE() - INTERVAL 5 WEEK, CURDATE() - INTERVAL 4 WEEK,                  CURDATE() - INTERVAL 3 WEEK + INTERVAL 3 DAY),
('Build notification component',      'Toast notifications for user actions',              7, 1, 'low',    'easy',   'in_progress', CURDATE() - INTERVAL 2 WEEK, CURDATE() + INTERVAL 1 WEEK,                  NULL),
('Accessibility audit',               'Check WCAG compliance across pages',                7, 1, 'medium', 'hard',   'not_started', CURDATE() - INTERVAL 1 WEEK, CURDATE() + INTERVAL 2 WEEK,                  NULL),
('Optimize image assets',             'Compress and convert images to WebP',               7, 1, 'low',    'easy',   'completed',   CURDATE() - INTERVAL 3 WEEK, CURDATE() - INTERVAL 2 WEEK,                  CURDATE() - INTERVAL 2 WEEK),

-- Fiona Harris (id=8)
('Create user registration flow',     'Build signup form with validation',                 8, 1, 'high',   'medium', 'completed',   CURDATE() - INTERVAL 6 WEEK, CURDATE() - INTERVAL 5 WEEK,                  CURDATE() - INTERVAL 5 WEEK + INTERVAL 1 DAY),
('Implement password reset',          'Email-based password recovery',                     8, 1, 'medium', 'medium', 'completed',   CURDATE() - INTERVAL 5 WEEK, CURDATE() - INTERVAL 4 WEEK,                  CURDATE() - INTERVAL 4 WEEK),
('Profile page UI',                   'Build user profile view and edit page',             8, 1, 'medium', 'easy',   'completed',   CURDATE() - INTERVAL 4 WEEK, CURDATE() - INTERVAL 3 WEEK,                  CURDATE() - INTERVAL 3 WEEK + INTERVAL 2 DAY),
('Input sanitization review',         'Audit all form inputs for XSS prevention',          8, 1, 'high',   'hard',   'in_progress', CURDATE() - INTERVAL 2 WEEK, CURDATE() - INTERVAL 1 DAY,                   NULL),
('Session timeout handling',          'Auto-logout after 30 min inactivity',               8, 1, 'low',    'medium', 'not_started', CURDATE() - INTERVAL 5 DAY,  CURDATE() + INTERVAL 2 WEEK,                  NULL),

-- George Patel (id=9)
('Build search functionality',        'Full-text search across tasks and users',           9, 1, 'high',   'hard',   'completed',   CURDATE() - INTERVAL 6 WEEK, CURDATE() - INTERVAL 5 WEEK,                  CURDATE() - INTERVAL 5 WEEK),
('Pagination component',              'Reusable table pagination for large datasets',      9, 1, 'medium', 'medium', 'completed',   CURDATE() - INTERVAL 5 WEEK, CURDATE() - INTERVAL 4 WEEK,                  CURDATE() - INTERVAL 4 WEEK + INTERVAL 1 DAY),
('Export to CSV feature',             'Allow data export from analytics tables',           9, 1, 'low',    'easy',   'in_progress', CURDATE() - INTERVAL 2 WEEK, CURDATE() + INTERVAL 5 DAY,                   NULL),
('Loading spinner component',         'Global loading indicator for async requests',       9, 1, 'low',    'easy',   'completed',   CURDATE() - INTERVAL 3 WEEK, CURDATE() - INTERVAL 2 WEEK,                  CURDATE() - INTERVAL 2 WEEK - INTERVAL 1 DAY),

-- Bob Chen - Team Leader (id=2)
('Sprint planning document',          'Prepare sprint 3 planning notes',                   2, 1, 'medium', 'easy',   'completed',   CURDATE() - INTERVAL 4 WEEK, CURDATE() - INTERVAL 3 WEEK,                  CURDATE() - INTERVAL 3 WEEK),
('Code review backlog',               'Review pending pull requests',                      2, 1, 'high',   'medium', 'in_progress', CURDATE() - INTERVAL 1 WEEK, CURDATE() + INTERVAL 2 DAY,                   NULL),

-- === TEAM BETA === --

-- Frank Lee (id=10)
('Set up CI/CD pipeline',             'Configure GitHub Actions for auto deploy',          10, 2, 'high',   'hard',   'completed',   CURDATE() - INTERVAL 7 WEEK, CURDATE() - INTERVAL 6 WEEK,                  CURDATE() - INTERVAL 6 WEEK + INTERVAL 1 DAY),
('Configure staging environment',     'Set up staging server and deploy scripts',          10, 2, 'high',   'medium', 'completed',   CURDATE() - INTERVAL 6 WEEK, CURDATE() - INTERVAL 5 WEEK,                  CURDATE() - INTERVAL 5 WEEK + INTERVAL 3 DAY),
('Database migration scripts',        'Create versioned migration system',                 10, 2, 'medium', 'hard',   'in_progress', CURDATE() - INTERVAL 3 WEEK, CURDATE() - INTERVAL 3 DAY,                   NULL),
('Server monitoring setup',           'Install and configure monitoring tools',            10, 2, 'medium', 'medium', 'not_started', CURDATE() - INTERVAL 1 WEEK, CURDATE() + INTERVAL 1 WEEK,                  NULL),
('Backup system implementation',      'Automated daily database backups',                  10, 2, 'high',   'medium', 'completed',   CURDATE() - INTERVAL 4 WEEK, CURDATE() - INTERVAL 3 WEEK,                  CURDATE() - INTERVAL 3 WEEK),

-- Grace Kim (id=11)
('User acceptance testing',           'Create and execute UAT test cases',                 11, 2, 'high',   'medium', 'completed',   CURDATE() - INTERVAL 6 WEEK, CURDATE() - INTERVAL 5 WEEK,                  CURDATE() - INTERVAL 5 WEEK),
('Performance testing',               'Load test API endpoints',                           11, 2, 'medium', 'hard',   'completed',   CURDATE() - INTERVAL 5 WEEK, CURDATE() - INTERVAL 4 WEEK,                  CURDATE() - INTERVAL 3 WEEK - INTERVAL 1 DAY),
('Bug report template',               'Standardize bug reporting process',                 11, 2, 'low',    'easy',   'completed',   CURDATE() - INTERVAL 4 WEEK, CURDATE() - INTERVAL 3 WEEK,                  CURDATE() - INTERVAL 3 WEEK + INTERVAL 1 DAY),
('Security vulnerability scan',       'Run OWASP ZAP scan on staging',                    11, 2, 'high',   'hard',   'in_progress', CURDATE() - INTERVAL 2 WEEK, CURDATE() - INTERVAL 2 DAY,                   NULL),
('Cross-browser testing',             'Test on Chrome, Firefox, Safari, Edge',             11, 2, 'medium', 'easy',   'completed',   CURDATE() - INTERVAL 3 WEEK, CURDATE() - INTERVAL 2 WEEK,                  CURDATE() - INTERVAL 2 WEEK + INTERVAL 1 DAY),

-- Hannah Walker (id=12)
('API documentation',                 'Write Swagger docs for all endpoints',              12, 2, 'medium', 'medium', 'completed',   CURDATE() - INTERVAL 6 WEEK, CURDATE() - INTERVAL 5 WEEK,                  CURDATE() - INTERVAL 5 WEEK - INTERVAL 1 DAY),
('Error logging system',              'Centralized error tracking and alerting',           12, 2, 'high',   'hard',   'completed',   CURDATE() - INTERVAL 5 WEEK, CURDATE() - INTERVAL 4 WEEK,                  CURDATE() - INTERVAL 4 WEEK + INTERVAL 2 DAY),
('Rate limiting middleware',          'Implement request throttling',                      12, 2, 'high',   'hard',   'in_progress', CURDATE() - INTERVAL 2 WEEK, CURDATE() + INTERVAL 3 DAY,                   NULL),
('Health check endpoint',             'Build /health route for monitoring',                12, 2, 'low',    'easy',   'completed',   CURDATE() - INTERVAL 3 WEEK, CURDATE() - INTERVAL 2 WEEK,                  CURDATE() - INTERVAL 2 WEEK),

-- Isaac Scott (id=13)
('Caching layer setup',               'Redis caching for frequent queries',                13, 2, 'high',   'hard',   'completed',   CURDATE() - INTERVAL 6 WEEK, CURDATE() - INTERVAL 5 WEEK,                  CURDATE() - INTERVAL 5 WEEK + INTERVAL 1 DAY),
('Database indexing optimization',     'Add indexes to improve query performance',         13, 2, 'medium', 'medium', 'completed',   CURDATE() - INTERVAL 4 WEEK, CURDATE() - INTERVAL 3 WEEK,                  CURDATE() - INTERVAL 3 WEEK),
('Connection pooling config',          'Optimize DB connection management',                13, 2, 'medium', 'hard',   'not_started', CURDATE() - INTERVAL 1 WEEK, CURDATE() + INTERVAL 2 WEEK,                  NULL),
('Query performance audit',            'Identify and fix slow queries',                    13, 2, 'high',   'hard',   'in_progress', CURDATE() - INTERVAL 2 WEEK, CURDATE() - INTERVAL 1 DAY,                   NULL),

-- Carol Davis - Team Leader (id=3)
('QA process documentation',          'Document testing procedures and standards',         3, 2, 'medium', 'easy',   'completed',   CURDATE() - INTERVAL 5 WEEK, CURDATE() - INTERVAL 4 WEEK,                  CURDATE() - INTERVAL 4 WEEK + INTERVAL 1 DAY),
('Team standup coordination',         'Set up daily standup schedule and agenda',          3, 2, 'low',    'easy',   'completed',   CURDATE() - INTERVAL 6 WEEK, NULL,                                         CURDATE() - INTERVAL 5 WEEK),

-- === TEAM GAMMA === --

-- James Brown (id=14)
('Design system components',          'Build reusable UI component library',               14, 3, 'high',   'hard',   'completed',   CURDATE() - INTERVAL 7 WEEK, CURDATE() - INTERVAL 6 WEEK,                  CURDATE() - INTERVAL 6 WEEK),
('Form validation library',           'Client-side validation utility functions',          14, 3, 'medium', 'medium', 'completed',   CURDATE() - INTERVAL 5 WEEK, CURDATE() - INTERVAL 4 WEEK,                  CURDATE() - INTERVAL 4 WEEK - INTERVAL 1 DAY),
('Modal component',                   'Reusable modal with animation',                    14, 3, 'low',    'easy',   'completed',   CURDATE() - INTERVAL 4 WEEK, CURDATE() - INTERVAL 3 WEEK,                  CURDATE() - INTERVAL 3 WEEK),
('Tooltip component',                 'Hover tooltips for UI elements',                    14, 3, 'low',    'easy',   'in_progress', CURDATE() - INTERVAL 1 WEEK, CURDATE() + INTERVAL 1 WEEK,                  NULL),
('Date picker component',             'Custom date input with calendar dropdown',          14, 3, 'medium', 'medium', 'not_started', CURDATE() - INTERVAL 3 DAY,  CURDATE() + INTERVAL 2 WEEK,                  NULL),

-- Katie Clark (id=15)
('Dashboard wireframes',              'Low-fidelity wireframes for analytics views',       15, 3, 'high',   'medium', 'completed',   CURDATE() - INTERVAL 7 WEEK, CURDATE() - INTERVAL 6 WEEK,                  CURDATE() - INTERVAL 6 WEEK + INTERVAL 2 DAY),
('Icon set selection',                 'Choose and integrate consistent icon library',     15, 3, 'low',    'easy',   'completed',   CURDATE() - INTERVAL 6 WEEK, CURDATE() - INTERVAL 5 WEEK,                  CURDATE() - INTERVAL 5 WEEK),
('Color scheme refinement',           'Finalize brand colors and CSS variables',           15, 3, 'medium', 'easy',   'completed',   CURDATE() - INTERVAL 5 WEEK, CURDATE() - INTERVAL 4 WEEK,                  CURDATE() - INTERVAL 4 WEEK + INTERVAL 1 DAY),
('Typography standards',              'Define font sizes, weights, and line heights',      15, 3, 'medium', 'easy',   'completed',   CURDATE() - INTERVAL 4 WEEK, CURDATE() - INTERVAL 3 WEEK,                  CURDATE() - INTERVAL 3 WEEK - INTERVAL 1 DAY),
('Print stylesheet',                  'CSS for printer-friendly analytics reports',        15, 3, 'low',    'medium', 'not_started', CURDATE() - INTERVAL 5 DAY,  CURDATE() + INTERVAL 2 WEEK,                  NULL),

-- Liam Turner (id=16)
('WebSocket integration',             'Real-time updates for task status changes',         16, 3, 'high',   'hard',   'completed',   CURDATE() - INTERVAL 6 WEEK, CURDATE() - INTERVAL 5 WEEK,                  CURDATE() - INTERVAL 5 WEEK + INTERVAL 2 DAY),
('Chat message persistence',          'Store and retrieve chat history from DB',           16, 3, 'high',   'hard',   'completed',   CURDATE() - INTERVAL 5 WEEK, CURDATE() - INTERVAL 4 WEEK,                  CURDATE() - INTERVAL 4 WEEK),
('File upload handler',               'Support file attachments in chat',                  16, 3, 'medium', 'medium', 'in_progress', CURDATE() - INTERVAL 2 WEEK, CURDATE() + INTERVAL 4 DAY,                   NULL),
('Typing indicator',                  'Show when other users are typing',                  16, 3, 'low',    'easy',   'not_started', CURDATE() - INTERVAL 4 DAY,  CURDATE() + INTERVAL 2 WEEK,                  NULL),

-- Mia Wright (id=17)
('User presence system',              'Online/offline status indicators',                  17, 3, 'medium', 'medium', 'completed',   CURDATE() - INTERVAL 6 WEEK, CURDATE() - INTERVAL 5 WEEK,                  CURDATE() - INTERVAL 5 WEEK + INTERVAL 1 DAY),
('Notification preferences',          'User settings for notification types',              17, 3, 'low',    'medium', 'completed',   CURDATE() - INTERVAL 5 WEEK, CURDATE() - INTERVAL 4 WEEK,                  CURDATE() - INTERVAL 4 WEEK),
('Emoji picker integration',          'Add emoji support to chat messages',                17, 3, 'low',    'easy',   'completed',   CURDATE() - INTERVAL 4 WEEK, CURDATE() - INTERVAL 3 WEEK,                  CURDATE() - INTERVAL 3 WEEK + INTERVAL 2 DAY),
('Message read receipts',             'Track and display message read status',             17, 3, 'medium', 'hard',   'in_progress', CURDATE() - INTERVAL 2 WEEK, CURDATE() - INTERVAL 2 DAY,                   NULL),
('Chat search functionality',         'Search through message history',                    17, 3, 'medium', 'medium', 'not_started', CURDATE() - INTERVAL 3 DAY,  CURDATE() + INTERVAL 3 WEEK,                  NULL),

-- Daniel Martinez - Team Leader (id=4)
('Team capacity planning',            'Assess team workload and availability',             4, 3, 'high',   'medium', 'completed',   CURDATE() - INTERVAL 5 WEEK, CURDATE() - INTERVAL 4 WEEK,                  CURDATE() - INTERVAL 4 WEEK),
('Risk assessment report',            'Identify project risks and mitigations',            4, 3, 'medium', 'medium', 'in_progress', CURDATE() - INTERVAL 2 WEEK, CURDATE() + INTERVAL 3 DAY,                   NULL),

-- === TEAM DELTA === --

-- Noah Robinson (id=18)
('Mobile responsive testing',         'Test all pages on mobile viewports',                18, 4, 'high',   'medium', 'completed',   CURDATE() - INTERVAL 7 WEEK, CURDATE() - INTERVAL 6 WEEK,                  CURDATE() - INTERVAL 6 WEEK + INTERVAL 1 DAY),
('PWA manifest setup',                'Configure progressive web app settings',            18, 4, 'medium', 'medium', 'completed',   CURDATE() - INTERVAL 5 WEEK, CURDATE() - INTERVAL 4 WEEK,                  CURDATE() - INTERVAL 4 WEEK - INTERVAL 1 DAY),
('Service worker implementation',     'Offline caching with service workers',              18, 4, 'high',   'hard',   'completed',   CURDATE() - INTERVAL 4 WEEK, CURDATE() - INTERVAL 3 WEEK,                  CURDATE() - INTERVAL 3 WEEK + INTERVAL 1 DAY),
('Push notification system',          'Browser push notifications for updates',            18, 4, 'medium', 'hard',   'in_progress', CURDATE() - INTERVAL 2 WEEK, CURDATE() + INTERVAL 5 DAY,                   NULL),
('App install banner',                'Custom install prompt for PWA',                     18, 4, 'low',    'easy',   'not_started', CURDATE() - INTERVAL 3 DAY,  CURDATE() + INTERVAL 2 WEEK,                  NULL),

-- Olivia White (id=19)
('User onboarding flow',              'First-time user tutorial walkthrough',              19, 4, 'high',   'medium', 'completed',   CURDATE() - INTERVAL 6 WEEK, CURDATE() - INTERVAL 5 WEEK,                  CURDATE() - INTERVAL 5 WEEK),
('Help documentation pages',          'In-app help center with FAQs',                     19, 4, 'medium', 'easy',   'completed',   CURDATE() - INTERVAL 5 WEEK, CURDATE() - INTERVAL 4 WEEK,                  CURDATE() - INTERVAL 4 WEEK + INTERVAL 2 DAY),
('Feedback form',                     'User feedback collection form',                     19, 4, 'low',    'easy',   'completed',   CURDATE() - INTERVAL 4 WEEK, CURDATE() - INTERVAL 3 WEEK,                  CURDATE() - INTERVAL 3 WEEK),
('Knowledge base articles',           'Write help articles for common tasks',              19, 4, 'medium', 'medium', 'in_progress', CURDATE() - INTERVAL 2 WEEK, CURDATE() - INTERVAL 1 DAY,                   NULL),
('Video tutorial scripts',            'Script walkthroughs for key features',              19, 4, 'low',    'medium', 'not_started', CURDATE() - INTERVAL 5 DAY,  CURDATE() + INTERVAL 3 WEEK,                  NULL),

-- Peter King (id=20)
('Role permission matrix',            'Define and implement RBAC rules',                   20, 4, 'high',   'hard',   'completed',   CURDATE() - INTERVAL 7 WEEK, CURDATE() - INTERVAL 6 WEEK,                  CURDATE() - INTERVAL 6 WEEK),
('Admin panel prototype',             'Build basic admin dashboard',                       20, 4, 'high',   'hard',   'completed',   CURDATE() - INTERVAL 5 WEEK, CURDATE() - INTERVAL 4 WEEK,                  CURDATE() - INTERVAL 4 WEEK + INTERVAL 1 DAY),
('Audit log system',                  'Track all user actions for compliance',             20, 4, 'medium', 'hard',   'in_progress', CURDATE() - INTERVAL 3 WEEK, CURDATE() + INTERVAL 2 DAY,                   NULL),
('Data export compliance',            'GDPR-compliant data export for users',              20, 4, 'high',   'medium', 'not_started', CURDATE() - INTERVAL 4 DAY,  CURDATE() + INTERVAL 2 WEEK,                  NULL),

-- Quinn Adams (id=21)
('Localization framework',            'i18n support for multi-language',                   21, 4, 'medium', 'hard',   'completed',   CURDATE() - INTERVAL 6 WEEK, CURDATE() - INTERVAL 5 WEEK,                  CURDATE() - INTERVAL 5 WEEK + INTERVAL 2 DAY),
('Timezone handling',                 'Proper timezone conversion across views',           21, 4, 'medium', 'medium', 'completed',   CURDATE() - INTERVAL 5 WEEK, CURDATE() - INTERVAL 4 WEEK,                  CURDATE() - INTERVAL 4 WEEK),
('Currency formatting',               'Locale-aware number and currency display',          21, 4, 'low',    'easy',   'completed',   CURDATE() - INTERVAL 4 WEEK, CURDATE() - INTERVAL 3 WEEK,                  CURDATE() - INTERVAL 3 WEEK - INTERVAL 1 DAY),
('RTL layout support',                'Right-to-left text layout for Arabic/Hebrew',       21, 4, 'low',    'hard',   'in_progress', CURDATE() - INTERVAL 2 WEEK, CURDATE() + INTERVAL 1 WEEK,                  NULL),
('Translation file management',       'Tooling for managing translation strings',          21, 4, 'low',    'medium', 'not_started', CURDATE() - INTERVAL 3 DAY,  CURDATE() + INTERVAL 3 WEEK,                  NULL),

-- Emma Thompson - Team Leader (id=5)
('Deployment checklist',              'Pre-deployment verification checklist',              5, 4, 'high',   'easy',   'completed',   CURDATE() - INTERVAL 4 WEEK, CURDATE() - INTERVAL 3 WEEK,                  CURDATE() - INTERVAL 3 WEEK + INTERVAL 1 DAY),
('Cross-team dependency mapping',     'Map dependencies between teams',                    5, 4, 'medium', 'medium', 'completed',   CURDATE() - INTERVAL 6 WEEK, CURDATE() - INTERVAL 5 WEEK,                  CURDATE() - INTERVAL 5 WEEK);




-- 1. Update existing users with a starting status
-- (Matching emails from your loginPage.php)
UPDATE users SET status = 'online' WHERE email = 'employee@gmail.com';
UPDATE users SET status = 'offline' WHERE email = 'manager@gmail.com';

-- 2. Create a Private Conversation (between Bob Chen and Dave Wilson)
-- Dave Wilson is User 4, Bob Chen is User 2 in your seed.sql
INSERT INTO conversations (is_group) VALUES (FALSE);
SET @conv_id = LAST_INSERT_ID();

-- 3. Add Members to the Conversation
INSERT INTO conversation_members (conversation_id, user_id) VALUES 
(@conv_id, 2), 
(@conv_id, 4);

INSERT INTO messages (conversation_id, sender_id, content, created_at) VALUES
(@conv_id, 2, 'Pagination Testing Dummy Messages', '2026-02-01 10:00:00'),
(@conv_id, 4, 'Pagination Testing Dummy Messages 2', '2026-02-01 11:00:00'),
(@conv_id, 2, 'Pagination Testing Dummy Messages 3', '2026-02-01 12:00:00'),
(@conv_id, 4, 'Pagination Testing Dummy Messages 4', '2026-02-01 13:00:00'),
(@conv_id, 2, 'Pagination Testing Dummy Messages 5', '2026-02-01 14:00:00'),
(@conv_id, 4, 'Pagination Testing Dummy Messages 6', '2026-02-01 15:00:00'),
(@conv_id, 2, 'Pagination Testing Dummy Messages 7', '2026-02-01 16:00:00'),
(@conv_id, 4, 'Pagination Testing Dummy Messages 8', '2026-02-01 17:00:00'),
(@conv_id, 2, 'Pagination Testing Dummy Messages 9', '2026-02-01 18:00:00'),
(@conv_id, 4, 'Pagination Testing Dummy Messages 10', '2026-02-02 19:00:00'),
(@conv_id, 2, 'Pagination Testing Dummy Messages 11', '2026-02-02 20:00:00'),
(@conv_id, 4, 'Pagination Testing Dummy Messages 12', '2026-02-03 10:00:00'),
(@conv_id, 2, 'Pagination Testing Dummy Messages 13', '2026-02-03 10:05:00'),
(@conv_id, 4, 'Pagination Testing Dummy Messages 14', '2026-02-03 10:06:00'),
(@conv_id, 2, 'Pagination Testing Dummy Messages 15', '2026-02-03 10:07:00'),
(@conv_id, 4, 'Pagination Testing Dummy Messages 16', '2026-02-03 10:08:00'),
(@conv_id, 2, 'Pagination Testing Dummy Messages 17', '2026-02-03 10:09:00'),
(@conv_id, 4, 'Pagination Testing Dummy Messages 18', '2026-02-03 10:10:00'),
(@conv_id, 2, 'Pagination Testing Dummy Messages 19', '2026-02-03 10:11:00'),
(@conv_id, 4, 'Pagination Testing Dummy Messages 20', '2026-02-03 10:12:00');

-- 4. Insert Active Messages for the REST API to fetch
INSERT INTO messages (conversation_id, sender_id, content) VALUES 
-- TODO: these are in reverse order
(@conv_id, 2, 'Hey Dave, did you finish the database schema?'),
(@conv_id, 4, 'Yes, just uploaded it to the repository.'),
(@conv_id, 2, 'Great. Are we still using WebSockets for the live updates?'),
(@conv_id, 4, 'Yeah, I am starting on the Ratchet server now.'),
(@conv_id, 2, 'Check out this cat image for testing:'),
(@conv_id, 2, 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/15/Cat_August_2010-4.jpg/1920px-Cat_August_2010-4.jpg');

-- 5. Insert a "Soft Deleted" Message for your Trash Modal
INSERT INTO messages (conversation_id, sender_id, content, deleted_at) VALUES 
(@conv_id, 4, 'Wait, disregard my last message about Ratchet.', NOW());
