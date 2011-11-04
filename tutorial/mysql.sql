-- Sappho Database Structure Definition
-- Database     : MySQL with InnoDB
-- Version      : 5.1.41
-- last update  : 2011-10-24

-- 
-- Database has to exist before applying this script!
-- The database has to be empty (no tables defined)!

-- This script is transactional!

--
-- SDBC Tutorial Database
--

SET AUTOCOMMIT=0;
START TRANSACTION;
-- --------------------------------------------------------
--        D R O P    O L D    T A B L E S
-- --------------------------------------------------------

-- We start with dropping all existing tables
DROP TABLE IF EXISTS login;
DROP TABLE IF EXISTS news;

-- --------------------------------------------------------
--            T A B L E    S T R U C T U R E
-- --------------------------------------------------------

-- User Login and ID data

CREATE TABLE login (
   id		INT(11) 		NOT NULL	AUTO_INCREMENT,
   name 	VARCHAR(30) 	NOT NULL,
   password VARCHAR(256)	NOT NULL,
  PRIMARY KEY (id)
) ENGINE = InnoDB;

-- News content
CREATE TABLE news (
   id			INT(11)			NOT NULL	AUTO_INCREMENT,
   title		VARCHAR(255)	NOT NULL,
   submitted	DATETIME		NOT NULL,
   content		TEXT			NOT NULL,
  PRIMARY KEY(id)
) ENGINE = InnoDB;
-- End transaction
COMMIT;

