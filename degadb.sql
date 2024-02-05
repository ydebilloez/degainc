/*
 * database creation script
 */

USE `i5576int_dega`;

/* erase tables */

DROP TABLE IF EXISTS `achats`;
DROP TABLE IF EXISTS `ventes`;
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
    `pa_code` CHAR(8) NOT NULL COMMENT 'the partner code',
    `pa_name` VARCHAR(60) NOT NULL COMMENT 'the parner name',
    `pa_phone` VARCHAR(60) COMMENT 'the parner phone',
    `pa_mail` VARCHAR(60) COMMENT 'the parner mail',
    `pa_type` SET('Fournisseur', 'Client') NOT NULL,
    `comments` VARCHAR(255) NOT NULL,
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
    ADD CONSTRAINT `FK_commandes_pa_code`
    FOREIGN KEY (`pa_code`) REFERENCES `partners`(`pa_code`);

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