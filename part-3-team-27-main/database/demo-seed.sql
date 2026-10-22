USE team27;

INSERT INTO teams (id, name) VALUES
(1, 'Team Alpha'),
(2, 'Team Beta'),
(3, 'Team Gamma'),
(4, 'Team Delta');

INSERT INTO users (id, email, password, name, role, team_id) VALUES
(1,  'manager@gmail.com',     'manager123',  'Alice Morgan',     'project_manager', NULL),
(2,  'leader1@gmail.com',     'leader123',   'Bob Chen',         'team_leader', 1),
(3,  'leader2@gmail.com',     'leader123',   'Carol Davis',      'team_leader', 2),
(4,  'leader3@gmail.com',     'leader123',   'Daniel Martinez',  'team_leader', 3),
(5,  'leader4@gmail.com',     'leader123',   'Emma Thompson',    'team_leader', 4),
(6,  'employee@gmail.com',    'employee123', 'Dave Wilson',      'employee', 1),
(7,  'emp2@gmail.com',        'employee123', 'Eve Johnson',      'employee', 1),
(8,  'fharris@gmail.com',     'employee123', 'Fiona Harris',     'employee', 1),
(9,  'gpatel@gmail.com',      'employee123', 'George Patel',     'employee', 1),
(10, 'emp3@gmail.com',        'employee123', 'Frank Lee',        'employee', 2),
(11, 'emp4@gmail.com',        'employee123', 'Grace Kim',        'employee', 2),
(12, 'hwalker@gmail.com',     'employee123', 'Hannah Walker',    'employee', 2),
(13, 'iscott@gmail.com',      'employee123', 'Isaac Scott',      'employee', 2),
(14, 'jbrown@gmail.com',      'employee123', 'James Brown',      'employee', 3),
(15, 'kclark@gmail.com',      'employee123', 'Katie Clark',      'employee', 3),
(16, 'lturner@gmail.com',     'employee123', 'Liam Turner',      'employee', 3),
(17, 'mwright@gmail.com',     'employee123', 'Mia Wright',       'employee', 3),
(18, 'nrobinson@gmail.com',   'employee123', 'Noah Robinson',    'employee', 4),
(19, 'owhite@gmail.com',      'employee123', 'Olivia White',     'employee', 4),
(20, 'pking@gmail.com',       'employee123', 'Peter King',       'employee', 4),
(21, 'qadams@gmail.com',      'employee123', 'Quinn Adams',      'employee', 4);

INSERT INTO tasks (title, description, assigned_to, team_id, priority, difficulty, status, created_at, due_date, completed_at) VALUES

('Set up project repository', 'Initialize Git repo and README', 6, 1, 'high', 'easy', 'completed', '2025-11-04', '2025-11-11', '2025-11-10'),
('Design database schema', 'ER diagram and SQL scripts', 6, 1, 'high', 'hard', 'completed', '2025-11-18', '2025-12-02', '2025-12-01'),
('Implement login page', 'Authentication UI with Bootstrap', 6, 1, 'high', 'medium', 'completed', '2025-12-09', '2025-12-20', '2025-12-19'),
('Create API endpoints', 'REST endpoints for task CRUD', 6, 1, 'medium', 'hard', 'completed', '2026-01-12', '2026-01-26', '2026-01-28'),
('Write unit tests', 'PHPUnit tests for core modules', 6, 1, 'low', 'medium', 'completed', '2026-02-16', '2026-03-02', '2026-02-28'),
('Fix navigation bug', 'Navbar active state fix', 6, 1, 'medium', 'easy', 'completed', '2026-03-16', '2026-03-23', '2026-03-21'),
('API rate limiting', 'Request throttling middleware', 6, 1, 'high', 'hard', 'completed', '2026-04-06', '2026-04-20', '2026-04-18'),
('Documentation update', 'Update README instructions', 6, 1, 'low', 'easy', 'completed', '2026-04-21', '2026-05-01', '2026-04-29'),
('Integrate notifications', 'Email alerts for assignments', 6, 1, 'medium', 'hard', 'in_progress', '2026-04-28', '2026-05-12', NULL),
('Performance optimization', 'Query caching layer', 6, 1, 'high', 'hard', 'not_started', '2026-05-01', '2026-05-15', NULL),

('Design landing mockup', 'Figma mockup for homepage', 7, 1, 'high', 'medium', 'completed', '2025-11-11', '2025-11-25', '2025-11-23'),
('Implement responsive CSS', 'Mobile-friendly pages', 7, 1, 'medium', 'medium', 'completed', '2025-12-15', '2025-12-29', '2026-01-02'),
('Build notification component', 'Toast notifications', 7, 1, 'low', 'easy', 'completed', '2026-02-02', '2026-02-16', '2026-02-14'),
('Accessibility audit', 'WCAG compliance check', 7, 1, 'medium', 'hard', 'completed', '2026-03-09', '2026-03-23', '2026-03-25'),
('Optimize image assets', 'WebP conversion', 7, 1, 'low', 'easy', 'completed', '2026-04-07', '2026-04-14', '2026-04-13'),
('Dark mode toggle', 'Theme switching', 7, 1, 'low', 'medium', 'in_progress', '2026-04-28', '2026-05-12', NULL),

('User registration flow', 'Signup form with validation', 8, 1, 'high', 'medium', 'completed', '2025-11-25', '2025-12-09', '2025-12-08'),
('Password reset flow', 'Email-based recovery', 8, 1, 'medium', 'medium', 'completed', '2026-01-05', '2026-01-19', '2026-01-18'),
('Profile page UI', 'User profile view and edit', 8, 1, 'medium', 'easy', 'completed', '2026-02-17', '2026-03-03', '2026-03-01'),
('Input sanitization', 'XSS prevention audit', 8, 1, 'high', 'hard', 'completed', '2026-03-24', '2026-04-07', '2026-04-09'),
('Session timeout handling', 'Auto-logout after inactivity', 8, 1, 'low', 'medium', 'completed', '2026-04-14', '2026-04-28', '2026-04-25'),
('Form validation library', 'Client-side validation utils', 8, 1, 'medium', 'medium', 'in_progress', '2026-04-28', '2026-05-10', NULL),

('Build search functionality', 'Full-text search', 9, 1, 'high', 'hard', 'completed', '2025-12-01', '2025-12-15', '2025-12-14'),
('Pagination component', 'Reusable table pagination', 9, 1, 'medium', 'medium', 'completed', '2026-01-19', '2026-02-02', '2026-02-01'),
('Export to CSV feature', 'Data export from analytics', 9, 1, 'low', 'easy', 'completed', '2026-03-02', '2026-03-16', '2026-03-14'),
('Loading spinner', 'Global loading indicator', 9, 1, 'low', 'easy', 'completed', '2026-04-06', '2026-04-13', '2026-04-12'),
('Error boundary component', 'Graceful error handling', 9, 1, 'medium', 'medium', 'completed', '2026-04-21', '2026-05-02', '2026-04-30'),
('Tooltip component', 'Hover tooltips for UI', 9, 1, 'low', 'easy', 'not_started', '2026-05-01', '2026-05-14', NULL),

('Sprint planning document', 'Sprint 3 planning notes', 2, 1, 'medium', 'easy', 'completed', '2026-02-23', '2026-03-02', '2026-03-01'),
('Code review backlog', 'Review pending PRs', 2, 1, 'high', 'medium', 'completed', '2026-04-14', '2026-04-25', '2026-04-23'),
('Team retrospective', 'Q1 retro facilitation', 2, 1, 'medium', 'easy', 'completed', '2026-04-28', '2026-05-02', '2026-05-01'),

('Set up CI/CD pipeline', 'GitHub Actions setup', 10, 2, 'high', 'hard', 'completed', '2025-11-10', '2025-11-24', '2025-11-25'),
('Configure staging env', 'Staging server setup', 10, 2, 'high', 'medium', 'completed', '2025-12-08', '2025-12-22', '2025-12-20'),
('Database migration scripts', 'Versioned migrations', 10, 2, 'medium', 'hard', 'completed', '2026-01-12', '2026-01-26', '2026-01-30'),
('Server monitoring setup', 'Monitoring tools install', 10, 2, 'medium', 'medium', 'completed', '2026-02-23', '2026-03-09', '2026-03-07'),
('Backup system', 'Automated daily backups', 10, 2, 'high', 'medium', 'completed', '2026-03-23', '2026-04-06', '2026-04-04'),
('Container orchestration', 'Docker compose setup', 10, 2, 'high', 'hard', 'completed', '2026-04-14', '2026-04-28', '2026-04-26'),
('Log aggregation', 'Centralized logging', 10, 2, 'medium', 'hard', 'in_progress', '2026-04-28', '2026-05-12', NULL),

('User acceptance testing', 'UAT test cases', 11, 2, 'high', 'medium', 'completed', '2025-11-18', '2025-12-02', '2025-12-01'),
('Performance testing', 'Load test API endpoints', 11, 2, 'medium', 'hard', 'completed', '2026-01-05', '2026-01-19', '2026-01-21'),
('Bug report template', 'Standardize bug reporting', 11, 2, 'low', 'easy', 'completed', '2026-02-09', '2026-02-23', '2026-02-21'),
('Security scan', 'OWASP ZAP scan on staging', 11, 2, 'high', 'hard', 'completed', '2026-03-16', '2026-03-30', '2026-03-28'),
('Cross-browser testing', 'Chrome, Firefox, Safari, Edge', 11, 2, 'medium', 'easy', 'completed', '2026-04-13', '2026-04-20', '2026-04-19'),
('Regression test suite', 'Automated regression tests', 11, 2, 'medium', 'hard', 'completed', '2026-04-21', '2026-05-05', '2026-05-02'),
('API contract testing', 'Consumer-driven contracts', 11, 2, 'medium', 'hard', 'not_started', '2026-05-01', '2026-05-15', NULL),

('API documentation', 'Swagger docs for endpoints', 12, 2, 'medium', 'medium', 'completed', '2025-12-01', '2025-12-15', '2025-12-13'),
('Error logging system', 'Centralized error tracking', 12, 2, 'high', 'hard', 'completed', '2026-01-19', '2026-02-02', '2026-02-04'),
('Rate limiting middleware', 'Request throttling', 12, 2, 'high', 'hard', 'completed', '2026-03-02', '2026-03-16', '2026-03-18'),
('Health check endpoint', '/health route for monitoring', 12, 2, 'low', 'easy', 'completed', '2026-04-06', '2026-04-13', '2026-04-12'),
('Webhook system', 'Event-driven notifications', 12, 2, 'medium', 'hard', 'completed', '2026-04-21', '2026-05-02', '2026-04-30'),
('GraphQL prototype', 'GraphQL API exploration', 12, 2, 'low', 'hard', 'in_progress', '2026-04-28', '2026-05-15', NULL),

('Caching layer setup', 'Redis caching', 13, 2, 'high', 'hard', 'completed', '2025-11-25', '2025-12-09', '2025-12-10'),
('Database indexing', 'Query performance indexes', 13, 2, 'medium', 'medium', 'completed', '2026-02-02', '2026-02-16', '2026-02-14'),
('Connection pooling', 'DB connection optimization', 13, 2, 'medium', 'hard', 'completed', '2026-03-23', '2026-04-06', '2026-04-05'),
('Query performance audit', 'Fix slow queries', 13, 2, 'high', 'hard', 'completed', '2026-04-14', '2026-04-28', '2026-04-27'),
('Data archival system', 'Archive old records', 13, 2, 'low', 'medium', 'in_progress', '2026-04-28', '2026-05-12', NULL),

('QA process documentation', 'Testing procedures doc', 3, 2, 'medium', 'easy', 'completed', '2026-01-26', '2026-02-09', '2026-02-07'),
('Team standup coordination', 'Daily standup schedule', 3, 2, 'low', 'easy', 'completed', '2026-03-30', '2026-04-06', '2026-04-04'),
('Test strategy review', 'Q2 testing strategy', 3, 2, 'high', 'medium', 'completed', '2026-04-21', '2026-05-02', '2026-04-30'),

('Design system components', 'Reusable UI library', 14, 3, 'high', 'hard', 'completed', '2025-11-04', '2025-11-18', '2025-11-17'),
('Form validation library', 'Client-side validation', 14, 3, 'medium', 'medium', 'completed', '2025-12-15', '2025-12-29', '2025-12-28'),
('Modal component', 'Reusable modal with animation', 14, 3, 'low', 'easy', 'completed', '2026-01-26', '2026-02-09', '2026-02-08'),
('Tooltip component', 'Hover tooltips', 14, 3, 'low', 'easy', 'completed', '2026-03-09', '2026-03-16', '2026-03-15'),
('Date picker component', 'Calendar date input', 14, 3, 'medium', 'medium', 'completed', '2026-04-06', '2026-04-20', '2026-04-18'),
('Drag and drop system', 'Kanban board drag/drop', 14, 3, 'high', 'hard', 'completed', '2026-04-21', '2026-05-05', '2026-05-03'),
('Virtual scrolling', 'Large list performance', 14, 3, 'medium', 'hard', 'not_started', '2026-05-01', '2026-05-15', NULL),

('Dashboard wireframes', 'Analytics view wireframes', 15, 3, 'high', 'medium', 'completed', '2025-11-18', '2025-12-02', '2025-12-04'),
('Icon set selection', 'Consistent icon library', 15, 3, 'low', 'easy', 'completed', '2026-01-05', '2026-01-12', '2026-01-11'),
('Color scheme refinement', 'Brand colors and CSS vars', 15, 3, 'medium', 'easy', 'completed', '2026-02-16', '2026-02-23', '2026-02-22'),
('Typography standards', 'Font sizes and weights', 15, 3, 'medium', 'easy', 'completed', '2026-03-23', '2026-03-30', '2026-03-29'),
('Print stylesheet', 'Printer-friendly reports', 15, 3, 'low', 'medium', 'completed', '2026-04-14', '2026-04-28', '2026-04-25'),
('Design tokens system', 'Shared design variables', 15, 3, 'medium', 'medium', 'in_progress', '2026-04-28', '2026-05-12', NULL),

('WebSocket integration', 'Real-time task updates', 16, 3, 'high', 'hard', 'completed', '2025-12-01', '2025-12-15', '2025-12-17'),
('Chat message persistence', 'Chat history from DB', 16, 3, 'high', 'hard', 'completed', '2026-01-19', '2026-02-02', '2026-02-01'),
('File upload handler', 'File attachments in chat', 16, 3, 'medium', 'medium', 'completed', '2026-03-02', '2026-03-16', '2026-03-15'),
('Typing indicator', 'Show when users type', 16, 3, 'low', 'easy', 'completed', '2026-04-06', '2026-04-13', '2026-04-12'),
('Message search', 'Search through chat history', 16, 3, 'medium', 'medium', 'completed', '2026-04-21', '2026-05-05', '2026-05-01'),
('Voice message support', 'Audio recording in chat', 16, 3, 'low', 'hard', 'not_started', '2026-05-01', '2026-05-20', NULL),

('User presence system', 'Online/offline indicators', 17, 3, 'medium', 'medium', 'completed', '2025-11-11', '2025-11-25', '2025-11-24'),
('Notification preferences', 'User notification settings', 17, 3, 'low', 'medium', 'completed', '2026-01-12', '2026-01-26', '2026-01-25'),
('Emoji picker', 'Emoji support in chat', 17, 3, 'low', 'easy', 'completed', '2026-02-23', '2026-03-09', '2026-03-07'),
('Read receipts', 'Message read status', 17, 3, 'medium', 'hard', 'completed', '2026-03-30', '2026-04-13', '2026-04-15'),
('Chat search', 'Search message history', 17, 3, 'medium', 'medium', 'completed', '2026-04-21', '2026-05-02', '2026-04-30'),
('Thread replies', 'Reply to specific messages', 17, 3, 'medium', 'hard', 'in_progress', '2026-04-28', '2026-05-15', NULL),

('Team capacity planning', 'Workload assessment', 4, 3, 'high', 'medium', 'completed', '2026-02-09', '2026-02-23', '2026-02-21'),
('Risk assessment report', 'Project risk mitigations', 4, 3, 'medium', 'medium', 'completed', '2026-04-07', '2026-04-21', '2026-04-19'),
('Sprint velocity tracking', 'Track team velocity', 4, 3, 'high', 'medium', 'completed', '2026-04-28', '2026-05-05', '2026-05-02'),

('Mobile responsive testing', 'All pages mobile test', 18, 4, 'high', 'medium', 'completed', '2025-11-10', '2025-11-24', '2025-11-22'),
('PWA manifest setup', 'Progressive web app config', 18, 4, 'medium', 'medium', 'completed', '2025-12-08', '2025-12-22', '2025-12-21'),
('Service worker', 'Offline caching', 18, 4, 'high', 'hard', 'completed', '2026-01-19', '2026-02-02', '2026-02-04'),
('Push notifications', 'Browser push notifications', 18, 4, 'medium', 'hard', 'completed', '2026-03-02', '2026-03-16', '2026-03-14'),
('App install banner', 'Custom install prompt', 18, 4, 'low', 'easy', 'completed', '2026-04-06', '2026-04-13', '2026-04-12'),
('Offline sync', 'Background data sync', 18, 4, 'high', 'hard', 'completed', '2026-04-21', '2026-05-05', '2026-05-02'),
('Biometric auth', 'Fingerprint/Face ID login', 18, 4, 'medium', 'hard', 'not_started', '2026-05-01', '2026-05-20', NULL),

('User onboarding flow', 'Tutorial walkthrough', 19, 4, 'high', 'medium', 'completed', '2025-11-25', '2025-12-09', '2025-12-08'),
('Help documentation', 'In-app help center', 19, 4, 'medium', 'easy', 'completed', '2026-01-05', '2026-01-19', '2026-01-17'),
('Feedback form', 'User feedback collection', 19, 4, 'low', 'easy', 'completed', '2026-02-16', '2026-03-02', '2026-02-28'),
('Knowledge base articles', 'Help articles', 19, 4, 'medium', 'medium', 'completed', '2026-03-23', '2026-04-06', '2026-04-08'),
('Video tutorials', 'Script feature walkthroughs', 19, 4, 'low', 'medium', 'completed', '2026-04-14', '2026-04-28', '2026-04-26'),
('Interactive guides', 'Step-by-step guides', 19, 4, 'medium', 'medium', 'in_progress', '2026-04-28', '2026-05-12', NULL),

('Role permission matrix', 'RBAC rules implementation', 20, 4, 'high', 'hard', 'completed', '2025-11-04', '2025-11-18', '2025-11-17'),
('Admin panel prototype', 'Basic admin dashboard', 20, 4, 'high', 'hard', 'completed', '2025-12-15', '2025-12-29', '2025-12-30'),
('Audit log system', 'User action tracking', 20, 4, 'medium', 'hard', 'completed', '2026-02-02', '2026-02-16', '2026-02-18'),
('Data export compliance', 'GDPR data export', 20, 4, 'high', 'medium', 'completed', '2026-03-16', '2026-03-30', '2026-03-28'),
('Two-factor auth', '2FA implementation', 20, 4, 'high', 'hard', 'completed', '2026-04-14', '2026-04-28', '2026-04-26'),
('Permission audit tool', 'Check role permissions', 20, 4, 'medium', 'medium', 'in_progress', '2026-04-28', '2026-05-10', NULL),

('Localization framework', 'i18n multi-language', 21, 4, 'medium', 'hard', 'completed', '2025-11-18', '2025-12-02', '2025-12-03'),
('Timezone handling', 'Timezone conversion', 21, 4, 'medium', 'medium', 'completed', '2026-01-12', '2026-01-26', '2026-01-24'),
('Currency formatting', 'Locale-aware numbers', 21, 4, 'low', 'easy', 'completed', '2026-02-23', '2026-03-09', '2026-03-07'),
('RTL layout support', 'Right-to-left text', 21, 4, 'low', 'hard', 'completed', '2026-03-30', '2026-04-13', '2026-04-14'),
('Translation management', 'Translation string tooling', 21, 4, 'low', 'medium', 'completed', '2026-04-21', '2026-05-05', '2026-05-01'),
('Language detection', 'Auto-detect user language', 21, 4, 'low', 'medium', 'not_started', '2026-05-01', '2026-05-15', NULL),

('Deployment checklist', 'Pre-deploy verification', 5, 4, 'high', 'easy', 'completed', '2026-01-26', '2026-02-02', '2026-02-01'),
('Cross-team dependencies', 'Map team dependencies', 5, 4, 'medium', 'medium', 'completed', '2026-03-09', '2026-03-23', '2026-03-21'),
('Release notes process', 'Standardize release notes', 5, 4, 'low', 'easy', 'completed', '2026-04-21', '2026-04-28', '2026-04-27');

UPDATE users SET status = 'online' WHERE email = 'employee@gmail.com';
UPDATE users SET status = 'offline' WHERE email = 'manager@gmail.com';

INSERT INTO conversations (is_group) VALUES (FALSE);
SET @conv_id = LAST_INSERT_ID();
INSERT INTO conversation_members (conversation_id, user_id) VALUES (@conv_id, 2), (@conv_id, 4);
INSERT INTO messages (conversation_id, sender_id, content, created_at) VALUES
(@conv_id, 2, 'Hey Daniel, how is the sprint going?', '2026-05-01 09:00:00'),
(@conv_id, 4, 'Going well, we finished the drag and drop system yesterday.', '2026-05-01 09:05:00'),
(@conv_id, 2, 'Great work. Are we on track for the demo?', '2026-05-01 09:10:00'),
(@conv_id, 4, 'Yes, everything should be ready by Thursday.', '2026-05-01 09:15:00');
