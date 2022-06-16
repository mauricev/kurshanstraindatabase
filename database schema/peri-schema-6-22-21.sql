-- MySQL dump 10.13  Distrib 8.0.22, for osx10.14 (x86_64)
--
-- Host: localhost    Database: straindatabase-withtestdata
-- ------------------------------------------------------
-- Server version	8.0.22

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `allele_table`
--

DROP TABLE IF EXISTS `allele_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `allele_table` (
  `allele_id` int NOT NULL AUTO_INCREMENT,
  `alleleName_col` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `gene_fk` int DEFAULT NULL,
  `comments_col` varchar(768) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`allele_id`),
  UNIQUE KEY `alleleName_col` (`alleleName_col`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `antibiotic_table`
--

DROP TABLE IF EXISTS `antibiotic_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `antibiotic_table` (
  `antibiotic_id` int NOT NULL AUTO_INCREMENT,
  `antibioticName_col` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`antibiotic_id`),
  UNIQUE KEY `antibioticName` (`antibioticName_col`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `author_table`
--

DROP TABLE IF EXISTS `author_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `author_table` (
  `author_id` int NOT NULL AUTO_INCREMENT,
  `authorName_col` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email_col` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `hashedPassword_col` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `isActive_col` tinyint(1) NOT NULL DEFAULT '0',
  `verified_col` tinyint(1) NOT NULL DEFAULT '0',
  `token_col` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `adminUser_col` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`author_id`),
  UNIQUE KEY `recordedName_col` (`authorName_col`),
  UNIQUE KEY `hashedPassword_col` (`hashedPassword_col`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `balancer_table`
--

DROP TABLE IF EXISTS `balancer_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `balancer_table` (
  `balancer_id` int NOT NULL AUTO_INCREMENT,
  `balancerName_col` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `chromosomeName_col` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `chromosomeName2_col` varchar(3) NOT NULL,
  `comments_col` varchar(768) DEFAULT NULL,
  PRIMARY KEY (`balancer_id`) USING BTREE,
  UNIQUE KEY `balancerName_col` (`balancerName_col`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `coinjection_marker_table`
--

DROP TABLE IF EXISTS `coinjection_marker_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `coinjection_marker_table` (
  `coInjectionMarker_id` int NOT NULL AUTO_INCREMENT,
  `coInjectionMarkerName_col` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`coInjectionMarker_id`),
  UNIQUE KEY `coInjectionMarkerName_col` (`coInjectionMarkerName_col`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contributor_table`
--

DROP TABLE IF EXISTS `contributor_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contributor_table` (
  `contributor_id` int NOT NULL AUTO_INCREMENT,
  `contributorName_col` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`contributor_id`),
  UNIQUE KEY `contributorName_col` (`contributorName_col`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `counter_table`
--

DROP TABLE IF EXISTS `counter_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `counter_table` (
  `counter_id` int NOT NULL AUTO_INCREMENT,
  `transGeneExCounter_col` int DEFAULT NULL,
  `transGeneIsCounter_col` int DEFAULT NULL,
  `transGeneSiCounter_col` int NOT NULL,
  `alleleCounter_col` int NOT NULL,
  `strainCounter_col` int DEFAULT NULL,
  `freezerNumber_col` int DEFAULT NULL,
  `freezerLetter_col` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nitrogenNumber_col` int DEFAULT NULL,
  `nitrogenLetter_col` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`counter_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fluoro_tag_table`
--

DROP TABLE IF EXISTS `fluoro_tag_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fluoro_tag_table` (
  `fluoroTag_id` int NOT NULL AUTO_INCREMENT,
  `fluoroTagName_col` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`fluoroTag_id`),
  UNIQUE KEY `fluroTagName_col` (`fluoroTagName_col`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gene_table`
--

DROP TABLE IF EXISTS `gene_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gene_table` (
  `gene_id` int NOT NULL AUTO_INCREMENT,
  `geneName_col` varchar(9) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `chromosomeName_col` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `comments_col` varchar(768) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`gene_id`),
  UNIQUE KEY `geneName_col` (`geneName_col`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `plasmid_table`
--

DROP TABLE IF EXISTS `plasmid_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `plasmid_table` (
  `plasmid_id` int NOT NULL AUTO_INCREMENT,
  `plasmidName_col` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `contributor_fk` int DEFAULT NULL,
  `plasmidLocation_col` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `promotorGene_fk` int DEFAULT NULL,
  `gene_fk` int DEFAULT NULL,
  `other_cDNA_col` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `comments_col` varchar(768) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sequenceDataName_col` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sequence_data_col` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `author_fk` int DEFAULT NULL,
  `editor_fk` int DEFAULT NULL,
  PRIMARY KEY (`plasmid_id`),
  UNIQUE KEY `plasmidName_col` (`plasmidName_col`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `plasmid_to_antibiotic_table`
--

DROP TABLE IF EXISTS `plasmid_to_antibiotic_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `plasmid_to_antibiotic_table` (
  `plasmid_fk` int NOT NULL,
  `antibiotic_fk` int NOT NULL,
  PRIMARY KEY (`plasmid_fk`,`antibiotic_fk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `plasmid_to_fluoro_tag_table`
--

DROP TABLE IF EXISTS `plasmid_to_fluoro_tag_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `plasmid_to_fluoro_tag_table` (
  `plasmid_fk` int NOT NULL,
  `fluoro_tag_fk` int NOT NULL,
  `n_c_internal_col` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`plasmid_fk`,`fluoro_tag_fk`,`n_c_internal_col`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `promoter_table`
--

DROP TABLE IF EXISTS `promoter_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `promoter_table` (
  `promoter_id` int NOT NULL AUTO_INCREMENT,
  `promoterName_col` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `comments_col` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`promoter_id`),
  UNIQUE KEY `promoterName_col` (`promoterName_col`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `strain_table`
--

DROP TABLE IF EXISTS `strain_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
  PRIMARY KEY (`strain_id`),
  UNIQUE KEY `fullNitrogen_col` (`fullNitrogen_col`),
  UNIQUE KEY `fullFreezer_col` (`fullFreezer_col`),
  KEY `strainName_col` (`strainName_col`),
  KEY `comments_col` (`comments_col`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `strain_to_allele_table`
--

DROP TABLE IF EXISTS `strain_to_allele_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `strain_to_allele_table` (
  `strain_fk` int NOT NULL,
  `allele_fk` int NOT NULL,
  PRIMARY KEY (`strain_fk`,`allele_fk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `strain_to_balancer_table`
--

DROP TABLE IF EXISTS `strain_to_balancer_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `strain_to_balancer_table` (
  `strain_fk` int NOT NULL,
  `balancer_fk` int NOT NULL,
  PRIMARY KEY (`strain_fk`,`balancer_fk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `strain_to_parent_strain_table`
--

DROP TABLE IF EXISTS `strain_to_parent_strain_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `strain_to_parent_strain_table` (
  `strain_fk` int NOT NULL,
  `parent_strain_fk` int NOT NULL,
  PRIMARY KEY (`strain_fk`,`parent_strain_fk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `strain_to_transgene_table`
--

DROP TABLE IF EXISTS `strain_to_transgene_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `strain_to_transgene_table` (
  `strain_fk` int NOT NULL,
  `transgene_fk` int NOT NULL,
  PRIMARY KEY (`strain_fk`,`transgene_fk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `transgene_table`
--

DROP TABLE IF EXISTS `transgene_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transgene_table` (
  `transgene_id` int NOT NULL AUTO_INCREMENT,
  `transgeneName_col` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `chromosomeName_col` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `comments_col` varchar(768) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `parent_transgene_col` int DEFAULT NULL,
  `coInjectionMarker_fk` int DEFAULT NULL,
  `contributor_fk` int DEFAULT NULL,
  `author_fk` int DEFAULT NULL,
  `editor_fk` int DEFAULT NULL,
  PRIMARY KEY (`transgene_id`),
  UNIQUE KEY `transgene_name` (`transgeneName_col`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `transgene_to_plasmids_table`
--

DROP TABLE IF EXISTS `transgene_to_plasmids_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transgene_to_plasmids_table` (
  `transgene_fk` int NOT NULL,
  `plasmid_fk` int NOT NULL,
  PRIMARY KEY (`transgene_fk`,`plasmid_fk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2021-06-22 10:23:47
