
-- Module Settings Insert Quries
-- Note: Add Enum Type type as dms before running these quries
INSERT INTO module_settings (company_id, module_name, status, type, is_allowed) VALUES
(1, 'clients', 'active', 'dms', 1),
(1, 'projects', 'active', 'dms', 1),
(1, 'tickets', 'active', 'dms', 1),
(1, 'invoices', 'active', 'dms', 1),
(1, 'estimates', 'active', 'dms', 1),
(1, 'events', 'active', 'dms', 1),
(1, 'messages', 'active', 'dms', 1),
(1, 'tasks', 'active', 'dms', 1),
(1, 'timelogs', 'active', 'dms', 1),
(1, 'contracts', 'active', 'dms', 1),
(1, 'notices', 'active', 'dms', 1),
(1, 'payments', 'active', 'dms', 1),
(1, 'orders', 'active', 'dms', 1),
(1, 'knowledgebase', 'active', 'dms', 1),
(1, 'employees', 'active', 'dms', 1),
(1, 'attendance', 'active', 'dms', 1),
(1, 'expenses', 'active', 'dms', 1),
(1, 'leaves', 'active', 'dms', 1),
(1, 'leads', 'active', 'dms', 1),
(1, 'holidays', 'active', 'dms', 1),
(1, 'products', 'active', 'dms', 1),
(1, 'reports', 'active', 'dms', 1),
(1, 'settings', 'deactive', 'dms', 0),
(1, 'bankaccount', 'active', 'dms', 1),
(1, 'payroll', 'active', 'dms', 1),
(1, 'businesses', 'active', 'dms', 1),
(1, 'drivers', 'active', 'dms', 1),
(1, 'coordinatorReports', 'active', 'dms', 1),
(1, 'revenueReporting', 'active', 'dms', 1),
(2, 'clients', 'active', 'dms', 1),
(2, 'projects', 'active', 'dms', 1),
(2, 'tickets', 'active', 'dms', 1),
(2, 'invoices', 'active', 'dms', 1),
(2, 'estimates', 'active', 'dms', 1),
(2, 'events', 'active', 'dms', 1),
(2, 'messages', 'active', 'dms', 1),
(2, 'tasks', 'active', 'dms', 1),
(2, 'timelogs', 'active', 'dms', 1),
(2, 'contracts', 'active', 'dms', 1),
(2, 'notices', 'active', 'dms', 1),
(2, 'payments', 'active', 'dms', 1),
(2, 'orders', 'active', 'dms', 1),
(2, 'knowledgebase', 'active', 'dms', 1),
(2, 'employees', 'active', 'dms', 1),
(2, 'attendance', 'active', 'dms', 1),
(2, 'expenses', 'active', 'dms', 1),
(2, 'leaves', 'active', 'dms', 1),
(2, 'leads', 'active', 'dms', 1),
(2, 'holidays', 'active', 'dms', 1),
(2, 'products', 'active', 'dms', 1),
(2, 'reports', 'active', 'dms', 1),
(2, 'settings', 'deactive', 'dms', 0),
(2, 'bankaccount', 'active', 'dms', 1),
(2, 'payroll', 'active', 'dms', 1),
(2, 'businesses', 'active', 'dms', 1),
(2, 'drivers', 'active', 'dms', 1),
(2, 'coordinatorReports', 'active', 'dms', 1),
(2, 'revenueReporting', 'active', 'dms', 1);

INSERT INTO module_settings (company_id, module_name, status, type, is_allowed) VALUES
(1, 'revenueReporting', 'active', 'admin', 1),
(2, 'revenueReporting', 'active', 'admin', 1);

INSERT INTO module_settings (company_id, module_name, status, type, is_allowed) VALUES
(1, 'revenueReporting', 'active', 'employee', 1),
(2, 'revenueReporting', 'active', 'employee', 1);

INSERT INTO permissions (name, display_name, module_id, allowed_permissions) VALUES
('add_revenue_reporting', 'View Revenue Reporting', 40, '{"all":4, "none":5}');

INSERT INTO permission_role (permission_id, role_id, permission_type_id) VALUES
(512, 1, 4);

INSERT INTO permission_role (permission_id, role_id, permission_type_id) VALUES
(517, 1, 4);

INSERT INTO user_permissions (user_id, permission_id, permission_type_id) VALUES
(11, 512, 4);

INSERT INTO user_permissions (user_id, permission_id, permission_type_id) VALUES
(11, 517, 4);

INSERT INTO module_settings (company_id, module_name, status, type, is_allowed) VALUES
(1, 'driverTypes', 'active', 'admin', 1),
(2, 'driverTypes', 'active', 'admin', 1);

INSERT INTO module_settings (company_id, module_name, status, type, is_allowed) VALUES
(1, 'driverTypes', 'active', 'employee', 1),
(2, 'driverTypes', 'active', 'employee', 1);

INSERT INTO module_settings (company_id, module_name, status, type, is_allowed) VALUES
(1, 'driverTypes', 'active', 'dms', 1),
(2, 'driverTypes', 'active', 'dms', 1);

-- DELETE QURIES FOR ROLE & PERMISSION MODULE
-- DELETE Employees and DMS Permissions


-- DELETE FROM permission_role table Permission Linking

-- Payroll Linking To Delete
DELETE FROM permission_role WHERE permission_id=359 AND role_id=2;
DELETE FROM permission_role WHERE permission_id=360 AND role_id=2;
DELETE FROM permission_role WHERE permission_id=361 AND role_id=2;
DELETE FROM permission_role WHERE permission_id=362 AND role_id=2;

-- Drivers Linking To Delete
DELETE FROM permission_role WHERE permission_id=367 AND role_id=2;
DELETE FROM permission_role WHERE permission_id=368 AND role_id=2;
DELETE FROM permission_role WHERE permission_id=369 AND role_id=2;
DELETE FROM permission_role WHERE permission_id=370 AND role_id=2;

-- Businesses Linking To Delete
DELETE FROM permission_role WHERE permission_id=371 AND role_id=2;
DELETE FROM permission_role WHERE permission_id=372 AND role_id=2;
DELETE FROM permission_role WHERE permission_id=373 AND role_id=2;
DELETE FROM permission_role WHERE permission_id=374 AND role_id=2;

-- Branches Linking To Delete
DELETE FROM permission_role WHERE permission_id=505 AND role_id=2;
DELETE FROM permission_role WHERE permission_id=506 AND role_id=2;
DELETE FROM permission_role WHERE permission_id=507 AND role_id=2;
DELETE FROM permission_role WHERE permission_id=508 AND role_id=2;

-- Revenue Reporting Linking To Delete
DELETE FROM permission_role WHERE permission_id=512 AND role_id=2;

-- Driver Types Linking To Delete
DELETE FROM permission_role WHERE permission_id=513 AND role_id=2;
DELETE FROM permission_role WHERE permission_id=514 AND role_id=2;
DELETE FROM permission_role WHERE permission_id=515 AND role_id=2;

-- Coordinator Report Linking To Delete
DELETE FROM permission_role WHERE permission_id=363 AND role_id=2;
DELETE FROM permission_role WHERE permission_id=364 AND role_id=2;
DELETE FROM permission_role WHERE permission_id=366 AND role_id=2;

-- -- Modules Insert Queries
-- INSERT INTO modules (module_name, is_superadmin) VALUES('driverTypes', 0);

-- INSERT INTO modules (module_name, is_superadmin) VALUES('revenueReporting', 0);

-- INSERT INTO modules (module_name, is_superadmin) VALUES('payroll', 0);


-- -- Permission Insert Quries
-- INSERT INTO permissions (name, display_name, module_id, allowed_permissions)
-- VALUES('view_driver_types', 'View Driver Types', 39, '{"all":4, "none":5}');

-- INSERT INTO permissions (name, display_name, module_id, allowed_permissions)
-- VALUES('add_driver_types', 'Add Driver Types', 39, '{"all":4, "none":5}');

-- INSERT INTO permissions (name, display_name, module_id, allowed_permissions)
-- VALUES('edit_driver_types', 'Edit Driver Types', 39, '{"all":4, "none":5}');

-- INSERT INTO permissions (name, display_name, module_id, allowed_permissions)
-- VALUES('delete_driver_types', 'Delete Driver Types', 39, '{"all":4, "none":5}');

-- INSERT INTO permissions (name, display_name, module_id, allowed_permissions)
-- VALUES('view_revenue_reporting', 'View Revenue Reporting', 40, '{"all":4, "none":5}');

-- INSERT INTO permissions (name, display_name, module_id, allowed_permissions)
-- VALUES('view_payroll', 'View Payroll', 40, '{"all":4, "none":5}');


