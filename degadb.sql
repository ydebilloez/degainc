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

/* create some metadata */

DELETE FROM `pme_symbols`
WHERE `sy_name` = 'UNITS'
    OR (`sy_name` = 'SYMBOL' AND `sy_code` = 'UNITS');

INSERT INTO `pme_symbols`
    (`sy_name`, `sy_code`, `sy_value`)
VALUES
    ('SYMBOL', 'UNITS', 'types of units, foreign key');

INSERT INTO `pme_symbols`
    (`sy_name`, `sy_code`, `sy_value`)
VALUES
    ('UNITS', 'Kg', 'Kilogram'),
    ('UNITS', 'g', 'gram'),
    ('UNITS', 'Pce', 'Pieces'),
    ('UNITS', 'L', 'Litre'),
    ('UNITS', 'ml', 'millilitre'),
    ('UNITS', '$', 'USD');

/* ingredients table */

CREATE TABLE `ingredients` (
    `in_code` CHAR(8) NOT NULL COMMENT 'the ingredient code, uppercase',
    `in_name` VARCHAR(60) NOT NULL COMMENT 'the ingredient code',
    `in_unite` CHAR(10) NOT NULL DEFAULT '',
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
    `pr_type` SET('Achat', 'Vente') NOT NULL DEFAULT 'Achat,Vente',
    `pr_unite` CHAR(10) NOT NULL DEFAULT '',
    `pr_prixunite` DECIMAL(10,2) DEFAULT 0.0,
    `pr_quantite` DECIMAL(10,2) DEFAULT 1,
    `pr_ingredients` INT(10) DEFAULT 0,
    `status_code` CHAR(1) DEFAULT 'C' COMMENT 'valid codes: see table pme_statuscodes'
);

ALTER TABLE `products`
    ADD UNIQUE KEY `ukey_pr_code` (`pr_code`);

ALTER TABLE `products`
    ADD CONSTRAINT `FK_products_statuscodes`
    FOREIGN KEY (`status_code`) REFERENCES `pme_statuscodes`(`code`);

DELIMITER $$

CREATE TRIGGER `products_before_insert`
    BEFORE INSERT ON `products` FOR EACH ROW
BEGIN
    IF (NEW.`status_code` IS NULL) OR (NEW.`status_code` = '') THEN
        SET NEW.`status_code` = 'C';
    END IF;
    SET NEW.`pr_code` = REPLACE(REPLACE(REPLACE(NEW.`pr_code`, ' ', ''), '\t', ''), '\n', '');
    IF (NEW.`pr_code` = '') THEN
       /* cannot create with empty product code */
        SET NEW.`pr_code` = LEFT(REPLACE(REPLACE(REPLACE(NEW.`pr_name`, ' ', ''), '\t', ''), '\n', ''),8);
    END IF;
    SET NEW.`pr_code` = UPPER(NEW.`pr_code`);
    IF (NEW.`pr_unite` = '') THEN SET NEW.`pr_unite` = 'Pce'; END IF;
END
$$

CREATE TRIGGER `products_before_update`
    BEFORE UPDATE ON `products` FOR EACH ROW
BEGIN
    IF (NEW.`status_code` = OLD.`status_code`) THEN
        /* set status code only if it has not been changed by the
           statement
        */
        SET NEW.`status_code` = 'M';
    ELSEIF (NEW.`status_code` IS NULL) OR (NEW.`status_code` = '') THEN 
        SET NEW.`status_code` = 'M';
    END IF;
    /* remove spaces from data-entry */
    SET NEW.`pr_code` = REPLACE(REPLACE(REPLACE(NEW.`pr_code`, ' ', ''), '\t', ''), '\n', '');
    IF (NEW.`pr_code` = '') THEN
        /* cannot empty product code */
        SET NEW.`pr_code` = OLD.`pr_code`;
    ELSEIF (NEW.`pr_code` != OLD.`pr_code`) THEN
        /* uppercase code */
        SET NEW.`pr_code` = UPPER(NEW.`pr_code`);
    END IF;
    IF (NEW.`pr_unite` = '') THEN SET NEW.`pr_unite` = 'Pce'; END IF;
END
$$

DELIMITER ;

INSERT INTO `products`
    (`pr_code`, `pr_name`, `pr_type`, `pr_unite`, `pr_quantite`)
VALUES
    ('HDP20', 'Huile de Palme Bidon 20L', 'Achat', 'L', 20),
    ('HDP5', 'Huile de Palme Bidon 5L', 'Vente', 'L', 5),
    ('MIEL500', 'Miel 500g', 'Vente', 'g', 500);

/* table prodcomposition */

DROP TRIGGER IF EXISTS `prodcomposition_after_insert`;
DROP TRIGGER IF EXISTS `prodcomposition_after_delete`;

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

DELIMITER $$

CREATE TRIGGER `prodcomposition_after_insert`
    AFTER INSERT ON `prodcomposition` FOR EACH ROW
BEGIN
    UPDATE `products` SET `pr_ingredients` =
        (SELECT COUNT(`pr_code`) FROM `prodcomposition` WHERE `pr_code` = NEW.`pr_code`)
        WHERE `products`.`pr_code` = NEW.`pr_code`;
END
$$

CREATE TRIGGER `prodcomposition_after_delete`
    AFTER DELETE ON `prodcomposition` FOR EACH ROW
BEGIN
    UPDATE `products` SET `pr_ingredients` =
        (SELECT COUNT(`pr_code`) FROM `prodcomposition` WHERE `pr_code` = OLD.`pr_code`)
        WHERE `products`.`pr_code` = OLD.`pr_code`;
END
$$

DELIMITER ;

/* table partners */

DROP TRIGGER IF EXISTS `partners_before_insert`;
DROP TRIGGER IF EXISTS `partners_before_update`;
DROP TRIGGER IF EXISTS `partners_before_delete`;

CREATE TABLE `partners` (
    `pa_code` CHAR(8) NOT NULL COMMENT 'The partner code, cannot be changed once used',
    `pa_name` VARCHAR(60) NOT NULL COMMENT 'The partner name',
    `pa_phone` VARCHAR(60) COMMENT 'The partner phone',
    `pa_mail` VARCHAR(60) COMMENT 'The partner mail',
    `pa_type` SET('Fournisseur', 'Client', 'Usine'),
    `commentaires` TEXT,
    `status_code` CHAR(1) DEFAULT 'C' COMMENT 'valid codes: see table pme_statuscodes'
);

ALTER TABLE `partners`
    ADD UNIQUE KEY `ukey_pa_code` (`pa_code`);

ALTER TABLE `partners`
    ADD CONSTRAINT `FK_partenaires_statuscodes`
    FOREIGN KEY (`status_code`) REFERENCES `pme_statuscodes`(`code`);

DELIMITER $$

CREATE TRIGGER `partners_before_insert`
    BEFORE INSERT ON `partners` FOR EACH ROW
BEGIN
    IF (NEW.`status_code` IS NULL) OR (NEW.`status_code` = '') THEN
        SET NEW.`status_code` = 'C';
    END IF;
    SET NEW.`pa_code` = REPLACE(REPLACE(REPLACE(NEW.`pa_code`, ' ', ''), '\t', ''), '\n', '');
    IF (NEW.`pa_code` = '') THEN
        SET NEW.`pa_code` = LEFT(REPLACE(REPLACE(REPLACE(NEW.`pa_name`, ' ', ''), '\t', ''), '\n', ''),8);
    END IF;
    SET NEW.`pa_code` = UPPER(NEW.`pa_code`);
    IF (NEW.`pa_type` IS NULL) OR (NEW.`pa_type` = '') THEN
        SET NEW.`pa_type` = 'Client';
    END IF;
END
$$

CREATE TRIGGER `partners_before_update`
    BEFORE UPDATE ON `partners` FOR EACH ROW
BEGIN
    IF (OLD.`status_code` = 'S') THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'CANNOT UPDATE SYSTEM RECORDS';
    END IF;
    IF (NEW.`status_code` = OLD.`status_code`) THEN
        /* set status code only if it has not been changed by the
           statement
        */
        SET NEW.`status_code` = 'M';
    ELSEIF (NEW.`status_code` IS NULL) OR (NEW.`status_code` = '') THEN 
        SET NEW.`status_code` = 'M';
    END IF;
    IF (NEW.`pa_code` != OLD.`pa_code`) THEN
        SET NEW.`pa_code` = REPLACE(REPLACE(REPLACE(NEW.`pa_code`, ' ', ''), '\t', ''), '\n', '');
    END IF;
    IF (NEW.`pa_code` = '') THEN
        /* cannot empty partner code */
        SET NEW.`pa_code` = OLD.`pa_code`;
    ELSEIF (NEW.`pa_code` != OLD.`pa_code`) THEN
        SET NEW.`pa_code` = UPPER(NEW.`pa_code`);
    END IF;
    IF (NEW.`pa_type` IS NULL) OR (NEW.`pa_type` = '') THEN
        SET NEW.`pa_type` = 'Client';
    END IF;
END
$$

CREATE TRIGGER `partners_before_delete`
    BEFORE DELETE ON `partners` FOR EACH ROW
BEGIN
    IF (OLD.`status_code` = 'S') THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'CANNOT DELETE SYSTEM RECORDS';
    END IF;
END
$$

DELIMITER ;

INSERT INTO `partners`
    (`pa_code`, `pa_name`, `pa_type`, `status_code`)
VALUES
    ('MANUFACT', 'Manufactoring (Own)', 'Usine', 'S');

/* table commandes */

DROP TRIGGER IF EXISTS `commandes_before_insert`;
DROP TRIGGER IF EXISTS `commandes_before_update`;

DROP PROCEDURE IF EXISTS `commandes_recalctotal`;

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
    ('OPER_TYPE', 'Vente', 'Vente'),
    ('OPER_TYPE', 'Fabrication', 'Fabrication');

CREATE TABLE `commandes` (
    `date_commande` DATE NOT NULL,
    `co_type` ENUM('Achat', 'Vente', 'Fabrication') NOT NULL,
    `pa_code` CHAR(8) NOT NULL,
    `date_paiement` DATE,
    `articles` INT(10) DEFAULT 0,
    `prixtotal` DECIMAL(10,2) DEFAULT 0,
    `commentaires` TEXT
);

ALTER TABLE `commandes`
    ADD `rowid` INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY FIRST;

ALTER TABLE `commandes`
    ADD CONSTRAINT `FK_commandes_pa_code`
    FOREIGN KEY (`pa_code`) REFERENCES `partners`(`pa_code`);

DELIMITER $$

CREATE PROCEDURE commandes_recalctotal(in_commande_id INT)
BEGIN
    UPDATE `commandes` SET `prixtotal` = (
        SELECT SUM(`comdetails`.`quantite` * `products`.`pr_prixunite`)
        FROM `comdetails`, `products`
        WHERE `comdetails`.`commande_id` = in_commande_id
          AND `comdetails`.`pr_code` = `products`.`pr_code`)
    WHERE `commandes`.`rowid` = in_commande_id;
END
$$

CREATE TRIGGER `commandes_before_insert`
    BEFORE INSERT ON `commandes` FOR EACH ROW
BEGIN
    IF (NEW.`date_commande` IS NULL) THEN
        SET NEW.`date_commande` = CURDATE();
    END IF;
END
$$

CREATE TRIGGER `commandes_before_update`
    BEFORE UPDATE ON `commandes` FOR EACH ROW
BEGIN
    IF (NEW.`date_commande` IS NULL) THEN
        SET NEW.`date_commande` = OLD.`date_commande`;
    END IF;
END
$$

DELIMITER ;

/* table comdetails */

DROP TRIGGER IF EXISTS `comdetails_after_insert`;
DROP TRIGGER IF EXISTS `comdetails_after_update`;
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

CREATE TRIGGER `comdetails_after_insert`
    AFTER INSERT ON `comdetails` FOR EACH ROW
BEGIN
    UPDATE `commandes` SET `articles` =
        (SELECT COUNT(`commande_id`) FROM `comdetails` WHERE `commande_id` = NEW.`commande_id`)
        WHERE `commandes`.`rowid` = NEW.`commande_id`;
    CALL commandes_recalctotal(NEW.`commande_id`);
END
$$

CREATE TRIGGER `comdetails_after_update`
    AFTER UPDATE ON `comdetails` FOR EACH ROW
BEGIN
    CALL commandes_recalctotal(OLD.`commande_id`);
    IF OLD.`commande_id` != NEW.`commande_id` THEN
        CALL commandes_recalctotal(NEW.`commande_id`);
    END IF;
END
$$

CREATE TRIGGER `comdetails_after_delete`
    AFTER DELETE ON `comdetails` FOR EACH ROW
BEGIN
    UPDATE `commandes` SET `articles` =
        (SELECT COUNT(`commande_id`) FROM `comdetails` WHERE `commande_id` = OLD.`commande_id`)
        WHERE `commandes`.`rowid` = OLD.`commande_id`;
    CALL commandes_recalctotal(OLD.`commande_id`);
END
$$

DELIMITER ;

/* table achats */

CREATE TABLE `operations` (
    `commande_id` INT(10),
    `date_operation` DATE NOT NULL,
    `value_operation` DECIMAL(10,2) DEFAULT 0,
    `commentaires` TEXT
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