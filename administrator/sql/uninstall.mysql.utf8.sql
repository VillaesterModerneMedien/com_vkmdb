-- Uninstall
DROP TABLE IF EXISTS `#__vkmdb_eintraege`;
DELETE FROM `#__content_types` WHERE `type_alias` IN ('com_vkmdb.eintrag', 'com_vkmdb.category');
