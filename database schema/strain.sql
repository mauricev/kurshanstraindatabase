-- MySQL dump 10.13  Distrib 5.7.24, for osx10.9 (x86_64)
--
-- Host: localhost    Database: straindatabase-withtestdata
-- ------------------------------------------------------
-- Server version	8.3.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `strain_table`
--

DROP TABLE IF EXISTS `strain_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `strain_table` (
  `strain_id` int NOT NULL AUTO_INCREMENT,
  `strainName_col` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `isolationName_col` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `dateFrozen_col` date DEFAULT NULL,
  `dateThawed_col` date DEFAULT NULL,
  `comments_col` varchar(768) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `fullFreezer_col` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `fullNitrogen_col` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `contributor_fk` int DEFAULT NULL,
  `author_fk` int DEFAULT NULL,
  `editor_fk` int DEFAULT NULL,
  `isLastVial_col` tinyint(1) DEFAULT NULL,
  `lastVialContributor_fk` int DEFAULT NULL,
  `dateHandedOff_col` date DEFAULT NULL,
  `dateSurvived_col` date DEFAULT NULL,
  `dateMoved_col` date DEFAULT NULL,
  PRIMARY KEY (`strain_id`),
  UNIQUE KEY `fullNitrogen_col` (`fullNitrogen_col`),
  UNIQUE KEY `fullFreezer_col` (`fullFreezer_col`),
  KEY `strainName_col` (`strainName_col`),
  KEY `comments_col` (`comments_col`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `strain_table`
--

LOCK TABLES `strain_table` WRITE;
/*!40000 ALTER TABLE `strain_table` DISABLE KEYS */;
INSERT INTO `strain_table` VALUES (1,'TV22469','','2024-03-19','2021-09-25','nrx phenotype is reduced cla-1 puncta and small rab-3 clusters in commissure','F-0001, A1-3','N-001, A1',1,3,1,1,6,'2024-03-15','2024-03-21',NULL),(2,'PTK1','','2020-07-28',NULL,'','F-0001, A4-6','N-001, A2',1,7,NULL,NULL,NULL,NULL,NULL,NULL),(3,'PTK2','','2020-07-28',NULL,'','F-0001, A7-9','N-001, A3',1,7,NULL,NULL,NULL,'2024-03-15','2024-03-21','2024-03-21'),(4,'PTK3','','2020-07-07',NULL,'','F-0001, B1-3','N-001, A4',1,7,1,0,NULL,'2024-03-15',NULL,NULL),(5,'PTK4','','2019-09-27',NULL,'','F-0001, B4-6','N-001, A5',1,7,1,NULL,NULL,'2024-03-15','2024-03-19',NULL),(6,'PTK5','','2024-03-19',NULL,'comment','F-0001, B7-9','N-001, A6',NULL,7,1,NULL,NULL,'2024-03-15','2024-03-21',NULL),(7,'PTK6','','2024-03-18',NULL,'','F-0001, C1-3','N-001, A7',1,7,1,NULL,NULL,'2024-03-15',NULL,NULL),(8,'PTK7','','2020-09-29','2022-04-23','','F-0001, C4-6','N-001, A8',1,8,1,0,NULL,'2024-03-15','2024-03-18','2024-03-21'),(9,'PTK8','','2020-10-06',NULL,'this is a test strain','F-0001, C7-9','N-001, A9',1,3,1,NULL,NULL,'2024-03-15','2024-03-19','2024-03-21'),(10,'PTK9','','2024-03-16',NULL,'','F-0001, D1-3','N-001, B1',NULL,3,1,NULL,NULL,'2024-03-15','2024-03-21',NULL),(11,'ERE464','','2020-11-06',NULL,'','F-0001, D4-6','N-001, B2',NULL,1,1,0,NULL,'2024-03-19','2024-03-21','2024-03-21'),(12,'PTK10','','2021-02-05',NULL,'comment for PTK10','F-0001, D7-9','N-001, B3',NULL,1,NULL,NULL,NULL,'2024-03-21',NULL,NULL),(13,'PTK11','','2021-01-28',NULL,'','F-0001, E1-3','N-001, B4',3,1,NULL,NULL,NULL,'2024-03-21',NULL,NULL),(17,'PTK12','','2021-05-11',NULL,'','F-0001, E4-6','N-001, B5',NULL,1,1,0,NULL,'2024-03-21',NULL,NULL),(18,'PTK13','','2021-05-11',NULL,'','F-0001, E7-9','N-001, B6',NULL,1,1,NULL,NULL,'2024-03-21',NULL,NULL),(19,'PTK14','','2021-05-11',NULL,'','F-0001, F1-3','N-001, B7',NULL,1,NULL,NULL,NULL,'2024-03-15','2024-03-18','2024-03-21'),(20,'PTK15','','2021-05-11',NULL,'','F-0001, F4-6','N-001, B8',NULL,1,NULL,NULL,NULL,'2024-03-15','2024-03-19',NULL),(21,'PTK16','','2021-05-17',NULL,'','F-0001, F7-9','N-001, B9',NULL,1,1,NULL,NULL,'2024-03-15','2024-03-18',NULL),(22,'PTK17','','2021-05-05','2022-04-29','','F-0001, G1-3','N-001, C1',NULL,1,1,0,NULL,'2024-03-15','2024-03-18',NULL),(23,'PEO304','','2021-05-03',NULL,'','F-0001, G4-6','N-001, C2',NULL,1,NULL,NULL,NULL,'2024-03-21',NULL,NULL),(24,'PTK18','','2022-03-14','2022-04-29','','F-0001, G7-9','N-001, C3',NULL,1,1,1,4,'2024-03-15','2024-03-18',NULL),(25,'PTK19','','2022-04-22',NULL,'','F-0001, H1-3','N-001, C4',NULL,1,NULL,NULL,NULL,'2024-03-15','2024-03-18',NULL),(26,'UEI8432','',NULL,NULL,'uei-8432 marked is off now','F-0001, H4-6','N-001, C5',NULL,1,1,0,NULL,'2024-03-15',NULL,NULL),(27,'PTK21','','2024-03-18',NULL,'set to 0 this time.','F-0001, H7-9','N-001, C6',NULL,1,NULL,0,NULL,'2024-03-15',NULL,'2024-03-18'),(28,'PTK23','','2022-04-15','2022-06-03','changing IRN4733 to ptk23','F-0001, I1-3','N-001, C7',NULL,1,1,0,NULL,'2024-03-15','2024-03-18','2024-03-19'),(29,'PTK24','','2022-04-06','2022-05-07','','F-0001, I4-6','N-001, C8',NULL,1,1,0,NULL,'2024-03-15',NULL,'2024-03-16'),(30,'PTK25','','2024-03-19','2022-02-02','','F-0001, I7-9','N-001, C9',NULL,1,1,1,5,'2024-03-15',NULL,NULL),(31,'HJQ2073','','2024-03-19',NULL,'','F-0002, A1-3','N-001, D1',7,1,NULL,0,NULL,'2024-03-15','2024-03-19',NULL),(32,'PTK26','',NULL,NULL,'','F-0002, A4-6','N-001, D2',NULL,1,NULL,0,NULL,'2024-03-15',NULL,NULL),(33,'PTK27','',NULL,NULL,'','F-0002, A7-9','N-001, D3',5,1,NULL,0,NULL,'2024-03-21',NULL,NULL),(34,'PTK28','',NULL,NULL,'','F-0002, B1-3','N-001, D4',3,1,NULL,0,NULL,'2024-03-21',NULL,NULL),(35,'PTK29','',NULL,NULL,'','F-0002, B4-6','N-001, D5',NULL,1,1,0,NULL,'2024-03-21',NULL,NULL),(36,'PTK30','',NULL,NULL,'','F-0002, B7-9','N-001, D6',NULL,10,NULL,0,NULL,'2024-03-21',NULL,NULL),(37,'PTK31','',NULL,NULL,'','F-0002, C1-3','N-001, D7',NULL,10,NULL,0,NULL,'2024-03-21',NULL,NULL),(38,'PTK32','',NULL,NULL,'','F-0002, C4-6','N-001, D8',NULL,10,NULL,0,NULL,'2024-03-21',NULL,NULL),(39,'PTK33','',NULL,NULL,'','F-0002, C7-9','N-001, D9',NULL,10,NULL,0,NULL,'2024-03-21',NULL,NULL),(40,'PTK34','',NULL,NULL,'','F-0002, D1-3','N-001, E1',NULL,10,NULL,0,NULL,'2024-03-21',NULL,NULL),(41,'PTK35','',NULL,NULL,'','F-0002, D4-6','N-001, E2',NULL,10,NULL,0,NULL,'2024-03-21',NULL,NULL),(42,'PTK36','',NULL,NULL,'','F-0002, D7-9','N-001, E3',NULL,10,NULL,0,NULL,'2024-03-21',NULL,NULL),(43,'PTK37','',NULL,NULL,'','F-0002, E1-3','N-001, E4',NULL,10,NULL,0,NULL,'2024-03-21',NULL,NULL),(44,'PTK38','',NULL,NULL,'','F-0002, E4-6','N-001, E5',NULL,10,NULL,0,NULL,'2024-03-21',NULL,NULL),(45,'PTK39','',NULL,NULL,'','F-0002, E7-9','N-001, E6',NULL,10,NULL,0,NULL,'2024-03-21',NULL,NULL),(46,'PTK40','',NULL,NULL,'','F-0002, F1-3','N-001, E7',NULL,1,NULL,0,NULL,'2024-03-21',NULL,NULL),(47,'PTK41','',NULL,NULL,'','F-0002, F4-6','N-001, E8',NULL,1,NULL,0,NULL,'2024-03-21',NULL,NULL),(48,'PTK42','',NULL,NULL,'','F-0002, F7-9','N-001, E9',NULL,1,NULL,0,NULL,'2024-03-21',NULL,NULL),(49,'PTK43','',NULL,NULL,'','F-0002, G1-3','N-001, F1',NULL,10,NULL,0,NULL,'2024-03-21',NULL,NULL),(50,'PTK44','','2024-03-21',NULL,'','F-0002, G4-6','N-001, F2',NULL,10,NULL,0,NULL,'2024-03-21',NULL,NULL),(51,'PTK45','','2024-03-21',NULL,'','F-0002, G7-9','N-001, F3',NULL,10,NULL,0,NULL,'2024-03-21','2024-03-21',NULL),(52,'PTK46','',NULL,NULL,'','F-0002, H1-3','N-001, F4',NULL,1,NULL,0,NULL,'2024-03-21',NULL,NULL),(53,'PTK47','',NULL,NULL,'','F-0002, H4-6','N-001, F5',NULL,1,NULL,0,NULL,'2024-03-21',NULL,NULL);
/*!40000 ALTER TABLE `strain_table` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-03-22  0:23:10