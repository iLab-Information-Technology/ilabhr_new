

-- Modules Insert Queries
INSERT INTO modules (module_name, is_superadmin) VALUES('driverTypes', 1);

INSERT INTO modules (module_name, is_superadmin) VALUES('revenueReporting', 1);


-- Permission Insert Quries
INSERT INTO permissions (name, display_name, module_id, allowed_permissions)
VALUES('view_driver_types', 'View Driver Types', 39, '{"all":4, "none":5}');

INSERT INTO permissions (name, display_name, module_id, allowed_permissions)
VALUES('add_driver_types', 'Add Driver Types', 39, '{"all":4, "none":5}');

INSERT INTO permissions (name, display_name, module_id, allowed_permissions)
VALUES('edit_driver_types', 'Edit Driver Types', 39, '{"all":4, "none":5}');

INSERT INTO permissions (name, display_name, module_id, allowed_permissions)
VALUES('delete_driver_types', 'Delete Driver Types', 39, '{"all":4, "none":5}');

INSERT INTO permissions (name, display_name, module_id, allowed_permissions)
VALUES('view_revenue_reporting', 'View Revenue Reporting', 40, '{"all":4, "none":5}');


