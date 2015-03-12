CREATE TABLE IF NOT EXISTS `idiomas` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `icone` text NOT NULL,
  `status` int(11) NOT NULL,
  `sigla` varchar(255) NOT NULL,
  `moeda` varchar(255) NOT NULL,
  `html_lang` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
INSERT INTO `idiomas` VALUES (1,'Ingles','',0,'en','','en'),(2,'Espanhol','',0,'es','','es'),(3,'Alemao','',1,'de','','de'),(4,'Frances','',0,'fr','','fr'),(5,'Chines','',0,'cn','','zh-Hant'),(6,'Italiano','',0,'it','','it'),(7,'Japones','',0,'jp','','ja');
CREATE TABLE IF NOT EXISTS `traducao` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `sigla_idioma` varchar(255) NOT NULL,
  `json` text NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;
CREATE TABLE IF NOT EXISTS `sistema` (
  `ID` int(11) NOT NULL,
  `config_email` text,
  `endereco` text NOT NULL,
  `palavras_chave` text NOT NULL,
  `cod_google` text NOT NULL,
  `API_key` text NOT NULL,
  `site_ids` varchar(255) NOT NULL,
  `telefones` varchar(255) NOT NULL,
  `redes_sociais` text NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
INSERT INTO `sistema` VALUES (1,'{\"Host\":\"smtp1.example.com\",\"Username\":\"user@example.com\",\"Password\":\"123\",\"SMTPSecure\":\"\",\"Port\":\"587\",\"From\":\"from@example.com\",\"FromName\":\"SITES\",\"addAddress\":\"joe@example.net|Joe User\"}','endereco','site,web,servico','<script></script>','','','(44)000-000','{\"facebook\":\"http://facebook.com.br\",\"twitter\":\"http://twitter.com\",\"linkedin\":\"https://br.linkedin.com/\",\"google_plus\":\"https://plus.google.comv\",\"youtube\":\"https://www.youtube.com/\",\"reddit\":\"http://www.reddit.com/\"}');
CREATE TABLE IF NOT EXISTS `usuarios` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `permissao` int(11) NOT NULL,
  `login` text NOT NULL,
  `senha` text NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
CREATE TABLE IF NOT EXISTS `textos` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) NOT NULL,
  `tipo` varchar(255) NOT NULL,
  `texto` text NOT NULL,
  `foto` text NOT NULL,
  `sigla_idioma` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;