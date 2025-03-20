-- Uninstall
DROP TABLE IF EXISTS `#__vkmdb_items`;
DELETE FROM `#__content_types` WHERE `type_alias` IN ('com_vkmdb.item', 'com_vkmdb.category');
