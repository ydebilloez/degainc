/*
 * database creation script
 */

USE `i5576int_dega`;

/* erase tables */

DROP TABLE IF EXISTS `operations`;
DROP TABLE IF EXISTS `comdetails`;
DROP TABLE IF EXISTS `commandes`;
DROP TABLE IF EXISTS `partners`;
DROP TABLE IF EXISTS `prodcomposition`;
DROP TABLE IF EXISTS `ingredients`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `animaux`;

/* ingredients table */

CREATE TABLE `ingredients` (
    `in_code` CHAR(8) NOT NULL COMMENT 'the ingredient code, uppercase',
    `in_name` VARCHAR(60) NOT NULL COMMENT 'the ingredient code',
    `in_unite` ENUM('KG', 'L', '') NOT NULL DEFAULT '',
    `status_code` CHAR(1) DEFAULT 'C' COMMENT 'valid codes: see table pme_statuscodes'
);

ALTER TABLE `ingredients`
    ADD UNIQUE KEY `ukey_in_code` (`in_code`);

ALTER TABLE `ingredients`
    ADD CONSTRAINT `FK_ingredients_statuscodes`
    FOREIGN KEY (`status_code`) REFERENCES `pme_statuscodes`(`code`);

/* products table */

DROP TRIGGER IF EXISTS `products_before_insert`;
DROP TRIGGER IF EXISTS `products_before_update`;

CREATE TABLE `products` (
    `pr_code` CHAR(8) NOT NULL COMMENT 'the product code, uppercase',
    `pr_name` VARCHAR(60) NOT NULL COMMENT 'the product code',
    `pr_type` ENUM('Achat', 'Vente') NOT NULL DEFAULT 'Achat',
    `pr_unite` ENUM('KG', 'L', '') NOT NULL DEFAULT '',
    `pr_prixunite` DECIMAL(10,2) DEFAULT 0.0,
    `pr_quantite` DECIMAL(10,2) DEFAULT 1,
    `status_code` CHAR(1) DEFAULT 'C' COMMENT 'valid codes: see table pme_statuscodes'
);

ALTER TABLE `products`
    ADD UNIQUE KEY `ukey_pr_code` (`pr_code`);

ALTER TABLE `products`
    ADD CONSTRAINT `FK_products_statuscodes`
    FOREIGN KEY (`status_code`) REFERENCES `pme_statuscodes`(`code`);

DELIMITER $$

CREATE TRIGGER `products_before_insert` BEFORE INSERT
    ON `products` FOR EACH ROW
BEGIN
    IF (NEW.`pr_code` IS NULL) THEN SET NEW.`pr_code` = NEW.`pr_name`; END IF;
    SET NEW.`pr_code` = UPPER(NEW.`pr_code`);
    IF (NEW.`status_code` IS NULL) THEN SET NEW.`status_code` = 'C'; END IF;
END
$$

CREATE TRIGGER `products_before_update` BEFORE UPDATE
    ON `products` FOR EACH ROW
BEGIN
    IF NEW.`status_code` = OLD.`status_code` THEN
        /* set status code only if it has not been changed by the
           statement
        */
        SET NEW.`status_code` = 'M';
    END IF;
    IF (NEW.`pr_code` IS NULL) THEN SET NEW.`pr_code` = NEW.`pr_name`; END IF;
    IF NEW.`pr_code` != OLD.`pr_code` THEN
        SET NEW.`pr_code` = UPPER(NEW.`pr_code`);
    END IF;
END
$$

DELIMITER ;

INSERT INTO `products`
    (`pr_code`, `pr_name`, `pr_type`)
VALUES
    ('HDP5', 'Huile de Palme Bidon 5L', 'Vente'),
    ('MIEL500', 'Miel 500g', 'Vente');

/* table prodcomposition */

CREATE TABLE `prodcomposition` (
    `pr_code` CHAR(8) NOT NULL COMMENT 'the product code',
    `in_code` CHAR(8) NOT NULL COMMENT 'the ingredient code',
    `quantite` DECIMAL(10,2) DEFAULT 1
);

ALTER TABLE `prodcomposition`
    ADD `rowid` INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY FIRST;

ALTER TABLE `prodcomposition`
    ADD CONSTRAINT `FK_prodcomposition_pr_code`
    FOREIGN KEY (`pr_code`) REFERENCES `products`(`pr_code`);

ALTER TABLE `prodcomposition`
    ADD CONSTRAINT `FK_prodcomposition_in_code`
    FOREIGN KEY (`in_code`) REFERENCES `ingredients`(`in_code`);

/* table partners */

DROP TRIGGER IF EXISTS `partners_before_insert`;
DROP TRIGGER IF EXISTS `partners_before_update`;

CREATE TABLE `partners` (
    `pa_code` CHAR(8) NOT NULL COMMENT 'The partner code, cannot be changed once used',
    `pa_name` VARCHAR(60) NOT NULL COMMENT 'The partner name',
    `pa_phone` VARCHAR(60) COMMENT 'The partner phone',
    `pa_mail` VARCHAR(60) COMMENT 'The partner mail',
    `pa_type` SET('Fournisseur', 'Client'),
    `commentaires` TEXT,
    `status_code` CHAR(1) DEFAULT 'C' COMMENT 'valid codes: see table pme_statuscodes'
);

ALTER TABLE `partners`
    ADD UNIQUE KEY `ukey_pa_code` (`pa_code`);

ALTER TABLE `partners`
    ADD CONSTRAINT `FK_partenaires_statuscodes`
    FOREIGN KEY (`status_code`) REFERENCES `pme_statuscodes`(`code`);

DELIMITER $$

CREATE TRIGGER `partners_before_insert` BEFORE INSERT
    ON `partners` FOR EACH ROW
BEGIN
    IF (NEW.`status_code` IS NULL) OR (NEW.`status_code` = '') THEN
        SET NEW.`status_code` = 'C';
    END IF;
    IF (NEW.`pa_code` IS NULL) OR (NEW.`pa_code` = '') THEN
        SET NEW.`pa_code` = LEFT(NEW.`pa_name`,8);
    END IF;
    SET NEW.`pa_code` = UPPER(NEW.`pa_code`);
    IF (NEW.`pa_type` IS NULL) OR (NEW.`pa_type` = '') THEN
        SET NEW.`pa_type` = 'Client';
    END IF;
END
$$

CREATE TRIGGER `partners_before_update` BEFORE UPDATE
    ON `partners` FOR EACH ROW
BEGIN
    IF NEW.`status_code` = OLD.`status_code` THEN
        /* set status code only if it has not been changed by the
           statement
        */
        SET NEW.`status_code` = 'M';
    ELSEIF (NEW.`status_code` IS NULL) OR (NEW.`status_code` = '') THEN 
        SET NEW.`status_code` = 'M';
    END IF;
    IF (NEW.`pa_code` IS NULL) OR (NEW.`pa_code` = '') THEN
        /* cannot empty partner code */
        SET NEW.`pa_code` = OLD.`pa_code`;
    END IF;
    IF NEW.`pa_code` != OLD.`pa_code` THEN
        SET NEW.`pa_code` = UPPER(NEW.`pa_code`);
    END IF;
    IF (NEW.`pa_type` IS NULL) OR (NEW.`pa_type` = '') THEN
        SET NEW.`pa_type` = 'Client';
    END IF;
END
$$

DELIMITER ;

/* table commandes */

DROP TRIGGER IF EXISTS `commandes_before_insert`;
DROP TRIGGER IF EXISTS `commandes_before_update`;

DELETE FROM `pme_symbols`
WHERE `sy_name` = 'OPER_TYPE'
    OR (`sy_name` = 'SYMBOL' AND `sy_code` = 'OPER_TYPE');

INSERT INTO `pme_symbols`
    (`sy_name`, `sy_code`, `sy_value`)
VALUES
    ('SYMBOL', 'OPER_TYPE', 'types of operations, foreign key');

INSERT INTO `pme_symbols`
    (`sy_name`, `sy_code`, `sy_value`)
VALUES
    ('OPER_TYPE', 'Achat', 'Achat'),
    ('OPER_TYPE', 'Vente', 'Vente');

CREATE TABLE `commandes` (
    `date_commande` DATE NOT NULL,
    `co_type` ENUM('Achat', 'Vente') NOT NULL,
    `pa_code` CHAR(8) NOT NULL,
    `date_paiement` DATE,
    `articles` INT(10) DEFAULT 0,
    `commentaires` TEXT
);

ALTER TABLE `commandes`
    ADD `rowid` INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY FIRST;

ALTER TABLE `commandes`
    ADD CONSTRAINT `FK_commandes_pa_code`
    FOREIGN KEY (`pa_code`) REFERENCES `partners`(`pa_code`);

DELIMITER $$

CREATE TRIGGER `commandes_before_insert` BEFORE INSERT
    ON `commandes` FOR EACH ROW
BEGIN
    IF NEW.`date_commande` IS NULL THEN
        SET NEW.`date_commande` = CURDATE();
    END IF;
END
$$

CREATE TRIGGER `commandes_before_update` BEFORE UPDATE
    ON `commandes` FOR EACH ROW
BEGIN
    IF NEW.`date_commande` IS NULL THEN
        SET NEW.`date_commande` = OLD.`date_commande`;
    END IF;
END
$$

DELIMITER ;

/* table comdetails */

DROP TRIGGER IF EXISTS `comdetails_after_insert`;
DROP TRIGGER IF EXISTS `comdetails_after_delete`;

CREATE TABLE `comdetails` (
    `commande_id` INT(10),
    `pr_code` CHAR(8) NOT NULL,
    `quantite` DECIMAL(10,2) DEFAULT 1,
    `commentaires` TEXT NOT NULL
);

ALTER TABLE `comdetails`
    ADD `rowid` INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY FIRST;

ALTER TABLE `comdetails`
    MODIFY `commande_id` INT(10) UNSIGNED;

ALTER TABLE `comdetails`
    ADD CONSTRAINT `FK_achats_commande_id`
    FOREIGN KEY (`commande_id`) REFERENCES `commandes`(`rowid`);

ALTER TABLE `comdetails`
    ADD CONSTRAINT `FK_comdetails_pr_code`
    FOREIGN KEY (`pr_code`) REFERENCES `products`(`pr_code`);

DELIMITER $$

CREATE TRIGGER `comdetails_after_insert` AFTER INSERT
    ON `comdetails` FOR EACH ROW
BEGIN
    UPDATE `commandes` SET `articles` =
        (SELECT COUNT(`commande_id`) FROM `comdetails` WHERE `commande_id` = NEW.`commande_id`)
        WHERE `commandes`.`rowid` = NEW.`commande_id`;
END
$$

CREATE TRIGGER `comdetails_after_delete` AFTER DELETE
    ON `comdetails` FOR EACH ROW
BEGIN
    UPDATE `commandes` SET `articles` =
        (SELECT COUNT(`commande_id`) FROM `comdetails` WHERE `commande_id` = OLD.`commande_id`)
        WHERE `commandes`.`rowid` = OLD.`commande_id`;
END
$$

DELIMITER ;

/* table achats */

CREATE TABLE `operations` (
    `commande_id` INT(10),
    `date_operation` DATE NOT NULL,
    `valuer_operation` DECIMAL(10,2),
    `commentaires` TEXT NOT NULL
);

ALTER TABLE `operations`
    ADD `rowid` INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY FIRST;

ALTER TABLE `operations`
    MODIFY `commande_id` INT(10) UNSIGNED;

ALTER TABLE `operations`
    ADD CONSTRAINT `FK_operations_commande_id`
    FOREIGN KEY (`commande_id`) REFERENCES `commandes`(`rowid`);

/* table animaux */

CREATE TABLE `animaux` (
    `an_code` CHAR(8) NOT NULL COMMENT 'the animal code, uppercase',
    `an_name` VARCHAR(60) NOT NULL COMMENT 'the animal name',
    `status_code` CHAR(1) DEFAULT 'C' COMMENT 'valid codes: see table pme_statuscodes'
);

/*
Nom animal
Maturité en mois
Age de première gestation (mois)
Nombre de petits par gestation
Nombre de mois entre chaque gestation
Prix de vente par tranche d'age
Mortalité par tranche d'age (par an ou mois)
*/

INSERT INTO `animaux`
    (`an_code`, `an_name`)
VALUES
    ('Poule', 'Poule'),
    ('Chevre', 'Chevre'),
    ('Mouton', 'Mouton'),
    ('Vache', 'Vache');

/* eof */