CREATE TABLE `{db.prefix}modules` (
  `id` int(16) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL DEFAULT '',
  `table` varchar(32) NOT NULL DEFAULT '',
  `slug` varchar(32) NOT NULL DEFAULT '',
  `parent_module` int(16) NOT NULL DEFAULT 0,
  `field_id` int(16) NOT NULL DEFAULT 0,
  `field_title` int(16) NOT NULL DEFAULT 0,
  `field_parent` int(16) NOT NULL DEFAULT 0,
  `field_order_by` int(16) NOT NULL DEFAULT 0,
  `order_by_direction` enum('DESC','ASC') NOT NULL DEFAULT 'DESC',
  `management_width` varchar(8) NOT NULL DEFAULT '',
  `type` varchar(255) NOT NULL DEFAULT '',
  `locked` tinyint(1) NOT NULL DEFAULT 0,
  `lock_records` tinyint(1) NOT NULL DEFAULT 0,
  `hidden` tinyint(1) NOT NULL DEFAULT 0,
  `core_module` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=62 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=62 ;

INSERT INTO `{db.prefix}modules` VALUES (3, 'Modules', 'modules', 'modules', 0, 6, 14, 10, 14, 'ASC', '40%', 'module', 1, 1, 0, 1);
INSERT INTO `{db.prefix}modules` VALUES (4, 'Fields', 'modules_fields', 'fields', 3, 17, 20, 0, 19, 'DESC', '20%', 'module_field', 1, 1, 0, 1);
INSERT INTO `{db.prefix}modules` VALUES (5, 'Validation', 'modules_fields_validation', 'validation', 3, 39, 40, 0, 39, 'DESC', '20%', 'module_field_validation', 1, 1, 0, 1);

CREATE TABLE `{db.prefix}modules_fields` (
  `id` int(16) NOT NULL AUTO_INCREMENT,
  `order` tinyint(2) NOT NULL DEFAULT 0,
  `module` int(32) NOT NULL DEFAULT 0,
  `name` varchar(32) NOT NULL DEFAULT '',
  `label` varchar(32) NOT NULL DEFAULT '',
  `type` varchar(32) NOT NULL DEFAULT '',
  `editable` tinyint(1) NOT NULL DEFAULT 0,
  `display_width` varchar(8) NOT NULL DEFAULT '',
  `tooltip` text NULL DEFAULT NULL,
  `fieldset` varchar(255) NOT NULL DEFAULT '',
  `specific_search` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=362 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=362 ;

INSERT INTO `{db.prefix}modules_fields` VALUES (6, 1, 3, 'id', 'Id', 'id', 0, '', '', '', 0);
INSERT INTO `{db.prefix}modules_fields` VALUES (8, 3, 3, 'table', 'Table', '', 1, '', '', '', 0);
INSERT INTO `{db.prefix}modules_fields` VALUES (9, 6, 3, 'slug', 'Slug', '', 1, '', '', '', 0);
INSERT INTO `{db.prefix}modules_fields` VALUES (10, 7, 3, 'parent_module', 'Module Parent', 'module', 1, '', '', '', 0);
INSERT INTO `{db.prefix}modules_fields` VALUES (11, 8, 3, 'field_id', 'Id Field', 'module_field_current', 1, '', '', '', 0);
INSERT INTO `{db.prefix}modules_fields` VALUES (12, 12, 3, 'order_by_direction', 'Default order direction', 'order_by_direction', 1, '', '', '', 0);
INSERT INTO `{db.prefix}modules_fields` VALUES (13, 13, 3, 'management_width', 'Options field width', 'text_small', 1, '', '', '', 0);
INSERT INTO `{db.prefix}modules_fields` VALUES (14, 2, 3, 'name', 'Name', '', 1, '55%', '', '', 0);
INSERT INTO `{db.prefix}modules_fields` VALUES (15, 10, 3, 'field_parent', 'Parent field', 'module_field_current', 1, '', '', '', 0);
INSERT INTO `{db.prefix}modules_fields` VALUES (16, 9, 3, 'field_title', 'Title Field', 'module_field_current', 1, '', '', '', 0);
INSERT INTO `{db.prefix}modules_fields` VALUES (25, 11, 3, 'field_order_by', 'Default order by field', 'module_field_current', 1, '', '', '', 0);
INSERT INTO `{db.prefix}modules_fields` VALUES (35, 9, 3, 'type', 'Type definition', '', 1, '', '', '', 0);
INSERT INTO `{db.prefix}modules_fields` VALUES (50, 10, 3, 'locked', 'Locked', 'yes_no', 1, '', '', '', 0);
INSERT INTO `{db.prefix}modules_fields` VALUES (94, 10, 3, 'core_module', 'Core module', 'yes_no', 1, '', '', '', 0);
INSERT INTO `{db.prefix}modules_fields` VALUES (91, 9, 3, 'lock_records', 'Lock Records', 'yes_no', 1, '', '', '', 0);
INSERT INTO `{db.prefix}modules_fields` VALUES (92, 9, 3, 'hidden', 'Hidden', 'yes_no', 1, '', 'Is this module hidden from the top navigation?', '', 0);
INSERT INTO `{db.prefix}modules_fields` VALUES (17, 1, 4, 'id', 'ID', 'id', 0, '', '', '', 0);
INSERT INTO `{db.prefix}modules_fields` VALUES (18, 3, 4, 'name', 'Name', '', 1, '', '', '', 0);
INSERT INTO `{db.prefix}modules_fields` VALUES (19, 6, 4, 'module', 'Module', 'module', 1, '30%', '', '', 1);
INSERT INTO `{db.prefix}modules_fields` VALUES (20, 2, 4, 'label', 'Label', '', 1, '30%', '', '', 0);
INSERT INTO `{db.prefix}modules_fields` VALUES (21, 5, 4, 'type', 'Type', 'type', 1, '', '', '', 0);
INSERT INTO `{db.prefix}modules_fields` VALUES (22, 8, 4, 'editable', 'Editable?', 'yes_no', 1, '', '', '', 0);
INSERT INTO `{db.prefix}modules_fields` VALUES (23, 7, 4, 'display_width', 'Display width', 'text_small', 1, '', '', '', 0);
INSERT INTO `{db.prefix}modules_fields` VALUES (34, 4, 4, 'order', 'Order', 'integer', 1, '', '', '', 0);
INSERT INTO `{db.prefix}modules_fields` VALUES (312, 2, 4, 'tooltip', 'Tooltip', 'textarea_small', 1, '', '', '', 0);
INSERT INTO `{db.prefix}modules_fields` VALUES (321, 3, 4, 'fieldset', 'Fieldset', '', 1, '', 'Fields of the same fieldset will be grouped together', '', 0);
INSERT INTO `{db.prefix}modules_fields` VALUES (327, 10, 4, 'specific_search', 'Specific search', 'yes_no', 1, '', 'If selected this column will be given it''s own search field in forms', '', 0);
INSERT INTO `{db.prefix}modules_fields` VALUES (39, 1, 5, 'id', 'ID', 'id', 0, '', '', '', 0);
INSERT INTO `{db.prefix}modules_fields` VALUES (40, 2, 5, 'name', 'Rule Name', 'module_validation_rule', 1, '30%', '', '', 0);
INSERT INTO `{db.prefix}modules_fields` VALUES (43, 4, 5, 'field_id', 'Field', 'module_field', 1, '30%', '', '', 0);

CREATE TABLE `{db.prefix}modules_fields_validation` (
  `id` int(16) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `field_id` int(32) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=27 ;

INSERT INTO `{db.prefix}modules_fields_validation` VALUES (16, 'instance', 19);
INSERT INTO `{db.prefix}modules_fields_validation` VALUES (17, 'instance', 18);
INSERT INTO `{db.prefix}modules_fields_validation` VALUES (18, 'instance', 20);
INSERT INTO `{db.prefix}modules_fields_validation` VALUES (19, 'unique_current', 18);
INSERT INTO `{db.prefix}modules_fields_validation` VALUES (20, 'unique', 8);
INSERT INTO `{db.prefix}modules_fields_validation` VALUES (21, 'instance', 8);
INSERT INTO `{db.prefix}modules_fields_validation` VALUES (22, 'instance', 14);
INSERT INTO `{db.prefix}modules_fields_validation` VALUES (23, 'unique', 35);
INSERT INTO `{db.prefix}modules_fields_validation` VALUES (24, 'instance', 35);
INSERT INTO `{db.prefix}modules_fields_validation` VALUES (25, 'instance', 9);
INSERT INTO `{db.prefix}modules_fields_validation` VALUES (26, 'boolean_true', 19);

CREATE TABLE `{db.prefix}modules_fields_validation_arguments` (
  `id` int(32) NOT NULL AUTO_INCREMENT,
  `validation_id` int(32) NOT NULL DEFAULT 0,
  `index` int(2) NOT NULL DEFAULT 0,
  `value` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=21 ;
