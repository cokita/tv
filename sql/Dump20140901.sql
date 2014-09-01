CREATE DATABASE  IF NOT EXISTS `tv` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `tv`;
-- MySQL dump 10.13  Distrib 5.6.17, for osx10.6 (i386)
--
-- Host: localhost    Database: tv
-- ------------------------------------------------------
-- Server version	5.6.19

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
-- Table structure for table `galeria_item`
--

DROP TABLE IF EXISTS `galeria_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `galeria_item` (
  `id_galeria_item` int(11) NOT NULL AUTO_INCREMENT,
  `id_youtube` int(11) DEFAULT NULL,
  `id_imagem` int(11) DEFAULT NULL,
  `data_inclusao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ativo` char(1) NOT NULL DEFAULT '1',
  `ordem` int(11) DEFAULT NULL,
  `id_galeria` int(11) DEFAULT '1',
  `id_usuario` int(11) NOT NULL,
  PRIMARY KEY (`id_galeria_item`),
  UNIQUE KEY `ordem_UNIQUE` (`ordem`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `galeria_item`
--

LOCK TABLES `galeria_item` WRITE;
/*!40000 ALTER TABLE `galeria_item` DISABLE KEYS */;
INSERT INTO `galeria_item` (`id_galeria_item`, `id_youtube`, `id_imagem`, `data_inclusao`, `ativo`, `ordem`, `id_galeria`, `id_usuario`) VALUES (26,NULL,33,'2014-09-01 00:15:21','1',1,1,1),(27,5,NULL,'2014-09-01 00:15:21','1',2,1,1),(28,NULL,32,'2014-09-01 00:15:21','1',3,1,1),(29,NULL,31,'2014-09-01 00:15:21','1',4,1,1),(30,6,NULL,'2014-09-01 00:15:21','1',5,1,1);
/*!40000 ALTER TABLE `galeria_item` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `imagem`
--

DROP TABLE IF EXISTS `imagem`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `imagem` (
  `id_imagem` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(200) NOT NULL,
  `extensao` varchar(10) NOT NULL,
  `caminho` varchar(200) NOT NULL,
  `caminho_thumb` varchar(200) DEFAULT NULL,
  `ativo` char(1) NOT NULL DEFAULT '1',
  `data_inclusao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id_usuario` int(11) NOT NULL,
  PRIMARY KEY (`id_imagem`),
  UNIQUE KEY `id_imagem_UNIQUE` (`id_imagem`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `imagem`
--

LOCK TABLES `imagem` WRITE;
/*!40000 ALTER TABLE `imagem` DISABLE KEYS */;
INSERT INTO `imagem` (`id_imagem`, `nome`, `extensao`, `caminho`, `caminho_thumb`, `ativo`, `data_inclusao`, `id_usuario`) VALUES (29,'lanternaverde.jpg','jpg','upload_files/1409524289.jpg','upload_files/thumb/1409524289.jpg','1','2014-08-31 19:31:29',1),(30,'tigre.jpg','jpg','upload_files/1409524297.jpg','upload_files/thumb/1409524297.jpg','0','2014-08-31 19:31:37',1),(31,'fullhd.jpg','jpg','upload_files/1409524319.jpg','upload_files/thumb/1409524319.jpg','1','2014-08-31 19:31:59',1),(32,'Tucano.jpg','jpg','upload_files/1409524327.jpg','upload_files/thumb/1409524327.jpg','1','2014-08-31 19:32:07',1),(33,'tigre.jpg','jpg','upload_files/1409524756.jpg','upload_files/thumb/1409524756.jpg','1','2014-08-31 19:39:16',1),(34,'lanternaverde.jpg','jpg','upload_files/1409531837.jpg','upload_files/thumb/1409531837.jpg','1','2014-08-31 21:37:17',1);
/*!40000 ALTER TABLE `imagem` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuario_administrador`
--

DROP TABLE IF EXISTS `usuario_administrador`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuario_administrador` (
  `id_usuario_administrador` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(200) NOT NULL,
  `senha` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `login` varchar(200) NOT NULL,
  `ativo` char(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_usuario_administrador`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario_administrador`
--

LOCK TABLES `usuario_administrador` WRITE;
/*!40000 ALTER TABLE `usuario_administrador` DISABLE KEYS */;
INSERT INTO `usuario_administrador` (`id_usuario_administrador`, `nome`, `senha`, `email`, `login`, `ativo`) VALUES (1,'Administrador','e19d5cd5af0378da05f63f891c7467af','cokitabr@gmail.com','admin','1');
/*!40000 ALTER TABLE `usuario_administrador` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `youtube`
--

DROP TABLE IF EXISTS `youtube`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `youtube` (
  `id_youtube` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(200) NOT NULL,
  `descricao` text,
  `thumbnail` varchar(200) NOT NULL,
  `duracao` varchar(45) NOT NULL,
  `ativo` char(1) NOT NULL DEFAULT '1',
  `data_inclusao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `url` varchar(200) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  PRIMARY KEY (`id_youtube`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `youtube`
--

LOCK TABLES `youtube` WRITE;
/*!40000 ALTER TABLE `youtube` DISABLE KEYS */;
INSERT INTO `youtube` (`id_youtube`, `titulo`, `descricao`, `thumbnail`, `duracao`, `ativo`, `data_inclusao`, `url`, `id_usuario`) VALUES (4,'vinheta do Big Brother Brasil 13','musica da 13°edição do BBB.','http://i.ytimg.com/vi/xKrSWNjqpTM/0.jpg','00:43','0','2014-08-31 19:32:55','https://www.youtube.com/watch?v=xKrSWNjqpTM',1),(5,'Cristiano Araújo - Igual Você Não Tem (Lançamento 2014)','Cristiano Araújo - Igual Você Não Tem Difícil é perceber e ver o sol nascer Mais uma vez eu acordar e não poder te ver Palavras, desencontros, pessoas, nem t...','http://i.ytimg.com/vi/BsbS3ugwUS4/0.jpg','03:17','1','2014-08-31 19:33:51','https://www.youtube.com/watch?v=BsbS3ugwUS4',1),(6,'Cristiano Araújo - Princesa Dos Meus Sonhos [OFICIAL]','Sigam no facebook - https://www.facebook.com/igor.ribeiro.9085.','http://i.ytimg.com/vi/THtXC0ylUqo/0.jpg','03:26','1','2014-08-31 19:34:02','https://www.youtube.com/watch?v=THtXC0ylUqo',1),(7,'A VERDADEIRA VOZ DO CRISTIANO ARAÚJO CANTANDO AO VIVO','NAO TEM PRA NINGUEM, ELE É AFINADO MESMO, O SEGREDO DESTA VOZ É CANTAR PASA SI MESMO!!! DESCUBRA PORQUE ELE É CAUSADOR DE EFEITOS O MAIOR SHOW DE TODOS OS TE...','http://i.ytimg.com/vi/hA_Q_DDI9ZA/0.jpg','04:05','1','2014-08-31 21:37:40','https://www.youtube.com/watch?v=hA_Q_DDI9ZA',1);
/*!40000 ALTER TABLE `youtube` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-09-01  0:24:45
