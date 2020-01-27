--
-- Base Table
--
CREATE TABLE `skeletonrequest` (
  `Skeletonrequest_ID` int(11) NOT NULL,
  `label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_date` datetime NOT NULL,
  `modified_by` int(11) NOT NULL,
  `modified_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `skeletonrequest`
  ADD PRIMARY KEY (`Skeletonrequest_ID`);

ALTER TABLE `skeletonrequest`
  MODIFY `Skeletonrequest_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Permissions
--
INSERT INTO `permission` (`permission_key`, `module`, `label`, `nav_label`, `nav_href`, `show_in_menu`) VALUES
('add', 'OnePlace\\Skeletonrequest\\Controller\\SkeletonrequestController', 'Add', '', '', 0),
('edit', 'OnePlace\\Skeletonrequest\\Controller\\SkeletonrequestController', 'Edit', '', '', 0),
('index', 'OnePlace\\Skeletonrequest\\Controller\\SkeletonrequestController', 'Index', 'Skeletonrequests', '/skeletonrequest', 1),
('list', 'OnePlace\\Skeletonrequest\\Controller\\ApiController', 'List', '', '', 1),
('view', 'OnePlace\\Skeletonrequest\\Controller\\SkeletonrequestController', 'View', '', '', 0);

--
-- Form
--
INSERT INTO `core_form` (`form_key`, `label`) VALUES ('skeletonrequest-single', 'Skeletonrequest');

--
-- Index List
--
INSERT INTO `core_index_table` (`table_name`, `form`, `label`) VALUES
('skeletonrequest-index', 'skeletonrequest-single', 'Skeletonrequest Index');

--
-- Tabs
--
INSERT INTO `core_form_tab` (`Tab_ID`, `form`, `title`, `subtitle`, `icon`, `counter`, `sort_id`, `filter_check`, `filter_value`) VALUES ('skeletonrequest-base', 'skeletonrequest-single', 'Skeletonrequest', 'Base', 'fas fa-cogs', '', '0', '', '');

--
-- Buttons
--
INSERT INTO `core_form_button` (`Button_ID`, `label`, `icon`, `title`, `href`, `class`, `append`, `form`, `mode`, `filter_check`, `filter_value`) VALUES
(NULL, 'Save Skeletonrequest', 'fas fa-save', 'Save Skeletonrequest', '#', 'primary saveForm', '', 'skeletonrequest-single', 'link', '', ''),
(NULL, 'Edit Skeletonrequest', 'fas fa-edit', 'Edit Skeletonrequest', '/skeletonrequest/edit/##ID##', 'primary', '', 'skeletonrequest-view', 'link', '', ''),
(NULL, 'Add Skeletonrequest', 'fas fa-plus', 'Add Skeletonrequest', '/skeletonrequest/add', 'primary', '', 'skeletonrequest-index', 'link', '', '');

--
-- Fields
--
INSERT INTO `core_form_field` (`Field_ID`, `type`, `label`, `fieldkey`, `tab`, `form`, `class`, `url_view`, `url_ist`, `show_widget_left`, `allow_clear`, `readonly`, `tbl_cached_name`, `tbl_class`, `tbl_permission`) VALUES
(NULL, 'text', 'Name', 'label', 'skeletonrequest-base', 'skeletonrequest-single', 'col-md-3', '/skeletonrequest/view/##ID##', '', 0, 1, 0, '', '', '');

COMMIT;