
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- flags
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `flags`;

CREATE TABLE `flags`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `code` VARCHAR(255) NOT NULL,
    `color` CHAR(7),
    `protected_status` TINYINT DEFAULT 1 NOT NULL,
    `position` INTEGER DEFAULT 0 NOT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB;

INSERT into `flags` (code, color, protected_status, position)
values
('cancelled','#d9534f','1','1'),
('delivered', '#5bc0de', '1', '2'),
('paid', '#5cb85c', '1', '3')
;

-- ---------------------------------------------------------------------
-- order_status_flags
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `order_status_flags`;

CREATE TABLE `order_status_flags`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `order_status_id` INTEGER NOT NULL,
    `flag_id` INTEGER NOT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `fi_order_status_id` (`order_status_id`),
    INDEX `fi_flag_id` (`flag_id`),
    CONSTRAINT `fk_order_status`
        FOREIGN KEY (`order_status_id`)
        REFERENCES `order_status` (`id`)
        ON UPDATE RESTRICT
        ON DELETE CASCADE,
    CONSTRAINT `fk_flag`
        FOREIGN KEY (`flag_id`)
        REFERENCES `flags` (`id`)
        ON UPDATE RESTRICT
        ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO `order_status_flags` (`id`, `order_status_id`, `flag_id`, `created_at`, `updated_at`) VALUES
(1,	5,	1,	CURRENT_TIMESTAMP,	CURRENT_TIMESTAMP),
(2,	2,	3,	CURRENT_TIMESTAMP,	CURRENT_TIMESTAMP),
(3,	3,	3,	CURRENT_TIMESTAMP,	CURRENT_TIMESTAMP),
(4,	4,	2,	CURRENT_TIMESTAMP,	CURRENT_TIMESTAMP),
(5,	4,	3,	CURRENT_TIMESTAMP,	CURRENT_TIMESTAMP),
(6,	6,	3,	CURRENT_TIMESTAMP,	CURRENT_TIMESTAMP),
(7,	6,	2,	CURRENT_TIMESTAMP,	CURRENT_TIMESTAMP);

-- ---------------------------------------------------------------------
-- flags_i18n
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `flags_i18n`;

CREATE TABLE `flags_i18n`
(
    `id` INTEGER NOT NULL,
    `locale` VARCHAR(5) DEFAULT 'en_US' NOT NULL,
    `title` VARCHAR(255),
    `description` LONGTEXT,
    `chapo` TEXT,
    `postscriptum` TEXT,
    PRIMARY KEY (`id`,`locale`),
    CONSTRAINT `flags_i18n_fk_cc20c0`
        FOREIGN KEY (`id`)
        REFERENCES `flags` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;


INSERT INTO `flags_i18n` (`id`, `locale`, `title`, `description`, `chapo`, `postscriptum`) VALUES

(1,	'cs_CZ',	NULL,	'',	'',	''),
(1,	'de_DE',	'Storniert',	'',	'',	''),
(1,	'en_US',	'Canceled',	'',	'',	''),
(1,	'es_ES',	'Cancelado',	'',	'',	''),
(1,	'fr_FR',	'Annulée',	'',	'',	''),
(1,	'it_IT',	NULL,	'',	'',	''),
(2,	'cs_CZ',	NULL,	'',	'',	''),
(2,	'de_DE',	'Gesendet',	'',	'',	''),
(2,	'en_US',	'Delivered',	'',	'',	''),
(2,	'es_ES',	'Enviado',	'',	'',	''),
(2,	'fr_FR',	'Livré',	'',	'',	''),
(2,	'it_IT',	NULL,	'',	'',	''),
(2,	'ru_RU',	'Выслан',	'',	'',	''),
(3,	'cs_CZ',	NULL,	'',	'',	''),
(3,	'de_DE',	'Bezahlt',	'',	'',	''),
(3,	'en_US',	'Paid',	'',	'',	''),
(3,	'es_ES',	'Pagado',	'',	'',	''),
(3,	'fr_FR',	'Payée',	'',	'',	''),
(3,	'it_IT',	NULL,	'',	'',	''),
(3,	'ru_RU',	'Оплачен',	'',	'',	'')
;

-- ---------------------------------------------------------------------
-- order_status_flags_i18n
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `order_status_flags_i18n`;

CREATE TABLE `order_status_flags_i18n`
(
    `id` INTEGER NOT NULL,
    `locale` VARCHAR(5) DEFAULT 'en_US' NOT NULL,
    `title` VARCHAR(255),
    `description` LONGTEXT,
    `chapo` TEXT,
    `postscriptum` TEXT,
    PRIMARY KEY (`id`,`locale`),
    CONSTRAINT `order_status_flags_i18n_fk_8c18c0`
        FOREIGN KEY (`id`)
        REFERENCES `order_status_flags` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO `order_status_flags_i18n` (`id`, `locale`, `title`, `description`, `chapo`, `postscriptum`) VALUES
(1,	'cs_CZ',	NULL,	'',	'',	''),
(1,	'de_DE',	'Nicht bezahlt',	'',	'',	''),
(1,	'en_US',	'Not paid',	'',	'',	''),
(1,	'es_ES',	'No pagados',	'',	'',	''),
(1,	'fr_FR',	'Non payée',	'',	'',	''),
(1,	'it_IT',	NULL,	'',	'',	''),
(1,	'ru_RU',	'Не оплачен',	'',	'',	''),
(2,	'cs_CZ',	NULL,	'',	'',	''),
(2,	'de_DE',	'Bezahlt',	'',	'',	''),
(2,	'en_US',	'Paid',	'',	'',	''),
(2,	'es_ES',	'Pagado',	'',	'',	''),
(2,	'fr_FR',	'Payée',	'',	'',	''),
(2,	'it_IT',	NULL,	'',	'',	''),
(2,	'ru_RU',	'Оплачен',	'',	'',	''),
(3,	'cs_CZ',	NULL,	'',	'',	''),
(3,	'de_DE',	'Bearbeitung',	'',	'',	''),
(3,	'en_US',	'Processing',	'',	'',	''),
(3,	'es_ES',	'Procesando',	'',	'',	''),
(3,	'fr_FR',	'Traitement',	'',	'',	''),
(3,	'it_IT',	NULL,	'',	'',	''),
(3,	'ru_RU',	'В обработке',	'',	'',	''),
(4,	'cs_CZ',	NULL,	'',	'',	''),
(4,	'de_DE',	'Gesendet',	'',	'',	''),
(4,	'en_US',	'Sent',	'',	'',	''),
(4,	'es_ES',	'Enviado',	'',	'',	''),
(4,	'fr_FR',	'Envoyée',	'',	'',	''),
(4,	'it_IT',	NULL,	'',	'',	''),
(4,	'ru_RU',	'Выслан',	'',	'',	''),
(5,	'cs_CZ',	NULL,	'',	'',	''),
(5,	'de_DE',	'Storniert',	'',	'',	''),
(5,	'en_US',	'Canceled',	'',	'',	''),
(5,	'es_ES',	'Cancelado',	'',	'',	''),
(5,	'fr_FR',	'Annulée',	'',	'',	''),
(5,	'it_IT',	NULL,	'',	'',	''),
(5,	'ru_RU',	'Отменен',	'',	'',	''),
(6,	'cs_CZ',	NULL,	'',	'',	''),
(6,	'de_DE',	'Zrückerstattet',	'',	'',	''),
(6,	'en_US',	'Refunded',	'',	'',	''),
(6,	'es_ES',	'Reembolsado',	'',	'',	''),
(6,	'fr_FR',	'Remboursé',	'',	'',	''),
(6,	'it_IT',	NULL,	'',	'',	''),
(6,	'ru_RU',	'Возвращен',	'',	'',	'');

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
