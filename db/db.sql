
DROP TABLE IF EXISTS `processors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `processors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

DROP TABLE IF EXISTS `submitters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `submitters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

DROP TABLE IF EXISTS `job_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `submitter_id` int(11) NOT NULL,
  `processor_id` int(11) DEFAULT NULL,
  `command` text NOT NULL,
  `priority` int(4) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `executed_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `job_list_FK` (`submitter_id`),
  KEY `job_list_FK_1` (`processor_id`),
  CONSTRAINT `job_list_FK` FOREIGN KEY (`submitter_id`) REFERENCES `submitters` (`id`),
  CONSTRAINT `job_list_FK_1` FOREIGN KEY (`processor_id`) REFERENCES `processors` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
