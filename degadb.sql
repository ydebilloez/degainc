/*
 * database creation script
 */

USE `i5576int_dega`;

/* erase tables */

DROP TABLE IF EXISTS `achats`;
DROP TABLE IF EXISTS `ventes`;
DROP TABLE IF EXISTS `commandes`;
DROP TABLE IF EXISTS `fournisseurs`;
DROP TABLE IF EXISTS `clients`;
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

/* table fournisseurs */

CREATE TABLE `fournisseurs` (
    `fo_code` CHAR(8) NOT NULL COMMENT 'the fournisseur code',
    `fo_name` VARCHAR(60) NOT NULL COMMENT 'the fournisseur name'
);

ALTER TABLE `fournisseurs`
    ADD UNIQUE KEY `ukey_fo_code` (`fo_code`);

/* table clients */

CREATE TABLE `clients` (
    `cl_code` CHAR(8) NOT NULL COMMENT 'the fournisseur code',
    `cl_name` VARCHAR(60) NOT NULL COMMENT 'the fournisseur name'
);

ALTER TABLE `clients`
    ADD UNIQUE KEY `ukey_cl_code` (`cl_code`);

/* table commandes */

CREATE TABLE `commandes` (
    `date_commande` DATE NOT NULL,
    `co_type` ENUM('Achat', 'Vente') NOT NULL DEFAULT 'Achat',
    `fo_code` CHAR(8) NOT NULL,
    `pr_code` CHAR(8) NOT NULL,
    `quantite` DECIMAL(10,2) DEFAULT 1,
    `commentaires` VARCHAR(255) NOT NULL
);

ALTER TABLE `commandes`
    ADD `rowid` INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY FIRST;

ALTER TABLE `commandes`
    ADD CONSTRAINT `FK_commandes_fo_code`
    FOREIGN KEY (`fo_code`) REFERENCES `fournisseurs`(`fo_code`);

ALTER TABLE `commandes`
    ADD CONSTRAINT `FK_commandes_pr_code`
    FOREIGN KEY (`pr_code`) REFERENCES `products`(`pr_code`);

/* table achats */

CREATE TABLE `achats` (
    `commande_id` INT(10),
    `date_achat` DATE NOT NULL,
    `prix_achat` DECIMAL(10,2),
    `commentaires` VARCHAR(255) NOT NULL
);

ALTER TABLE `achats`
    ADD `rowid` INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY FIRST;

ALTER TABLE `achats`
    MODIFY `commande_id` INT(10) UNSIGNED;

ALTER TABLE `achats`
    ADD CONSTRAINT `FK_achats_commande_id`
    FOREIGN KEY (`commande_id`) REFERENCES `commandes`(`rowid`);

/* table ventes */

CREATE TABLE `ventes` (
    `vente_name` VARCHAR(60) NOT NULL
);

ALTER TABLE `ventes`
    ADD `rowid` INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY FIRST;

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