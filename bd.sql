-- phpMyAdmin SQL Dump
-- version 4.0.6deb1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tempo de Geração: 19/02/2014 às 15:32
-- Versão do servidor: 5.5.34-0ubuntu0.13.10.1
-- Versão do PHP: 5.5.3-1ubuntu2.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

use petroweek;
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Banco de dados: `petroweek`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `Configs`
--

CREATE TABLE IF NOT EXISTS `Configs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `meta_key` text,
  `meta_value` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;

--
-- Fazendo dump de dados para tabela `Configs`
--

INSERT INTO `Configs` (`id`, `meta_key`, `meta_value`) VALUES
(3, 'logo_evento', '0859f10a7439796d53eeca874eab39d7bae20f0c1392609928'),
(4, 'email_principal', 'nicholas@iotecnologia.com.br'),
(5, 'nome_evento', 'EPOCA 2013'),
(6, 'descricao_pagamento', 'Para realizar o pagamento realize o depÃ³sito nas contas abaixo:<br><br>Conta 1:<br>AgÃ«ncia XXXX<br>Conta: XXXX-XX<br><br>Conta 2:<br>AgÃªncia: XXXX<br>Conta: XXXX-XX<br>'),
(7, 'submissoes_ativas', '1'),
(8, 'valor_inscricao_com_submissao', '60'),
(9, 'valor_inscricao_sem_submissao', '40'),
(10, 'deadline_submissao', '19/02/2014'),
(11, 'descricao_evento', 'Escola Potiguar de ComputaÃ§Ã£o e Suas AplicaÃ§Ãµes'),
(12, 'descricao_inscricao', 'Para as visitas tÃ©cnicas Ã© necessÃ¡rio estar com equipamento de seguranÃ§a!<br>');

-- --------------------------------------------------------

--
-- Estrutura para tabela `Eventos`
--

CREATE TABLE IF NOT EXISTS `Eventos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) DEFAULT NULL,
  `descricao` text,
  `qtdInscricoes` int(11) NOT NULL DEFAULT '1',
  `status` char(2) NOT NULL,
  `submissoes` char(1) NOT NULL DEFAULT '0',
  `deadline_inicial` date NOT NULL,
  `deadline_final` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- Fazendo dump de dados para tabela `Eventos`
--

INSERT INTO `Eventos` (`id`, `nome`, `descricao`, `qtdInscricoes`, `status`, `submissoes`, `deadline_inicial`, `deadline_final`) VALUES
(1, 'Minicurso', 'teste', 1, 'AT', '1', '2014-02-17', '2014-03-03'),
(4, 'Visita TÃ©cnica', 'Visitas tÃ©cnicas a serem realizadas pelos alunos', 1, 'AT', '0', '2014-02-27', '2014-02-28'),
(8, 'Palestra', 'Palestras', 1, 'AT', '1', '2014-02-20', '2014-02-20');

-- --------------------------------------------------------

--
-- Estrutura para tabela `Inscricoes`
--

CREATE TABLE IF NOT EXISTS `Inscricoes` (
  `idSubEventos` int(11) NOT NULL,
  `idUsuarios` int(11) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`,`idSubEventos`,`idUsuarios`),
  KEY `fk_SubEventos_has_Usuarios_Usuarios1` (`idUsuarios`),
  KEY `fk_SubEventos_has_Usuarios_SubEventos1` (`idSubEventos`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

--
-- Fazendo dump de dados para tabela `Inscricoes`
--

INSERT INTO `Inscricoes` (`idSubEventos`, `idUsuarios`, `id`) VALUES
(1, 3, 5),
(5, 3, 6),
(1, 20, 3),
(5, 20, 4),
(2, 27, 7),
(5, 27, 8),
(5, 28, 10),
(2, 28, 12),
(1, 29, 13),
(5, 29, 14),
(6, 29, 15);

-- --------------------------------------------------------

--
-- Estrutura para tabela `SubEventos`
--

CREATE TABLE IF NOT EXISTS `SubEventos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idEventos` int(11) NOT NULL,
  `nome` varchar(255) DEFAULT NULL,
  `descricao` longtext,
  `nVagas` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`,`idEventos`),
  KEY `fk_SubEventos_Eventos` (`idEventos`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Fazendo dump de dados para tabela `SubEventos`
--

INSERT INTO `SubEventos` (`id`, `idEventos`, `nome`, `descricao`, `nVagas`) VALUES
(1, 1, 'Desenvolvimento de Jogos', 'Cadastre aqui a ementa e demais informaÃ§Ãµes do minicurso<br>', 25),
(2, 1, 'Desenvolvimento de Aplicativos Mobiles', 'Aqui vem a ementa<br>', 30),
(5, 4, 'Visita TÃ©cnica 1', 'Testando', -1),
(6, 8, 'ComputaÃ§Ã£o em Nuvem', 'Testandooo', -1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `Submissoes`
--

CREATE TABLE IF NOT EXISTS `Submissoes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` datetime DEFAULT NULL,
  `arquivo_inicial` text,
  `arquivo_final` text,
  `author_id` int(11) NOT NULL,
  `idEventos` int(11) NOT NULL,
  `titulo_trabalho` varchar(45) DEFAULT NULL,
  `status` char(2) NOT NULL DEFAULT 'AG',
  `comentarios` longtext NOT NULL,
  PRIMARY KEY (`id`,`author_id`,`idEventos`),
  KEY `fk_Submissoes_Usuarios1_idx` (`author_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=26 ;

--
-- Fazendo dump de dados para tabela `Submissoes`
--

INSERT INTO `Submissoes` (`id`, `created`, `arquivo_inicial`, `arquivo_final`, `author_id`, `idEventos`, `titulo_trabalho`, `status`, `comentarios`) VALUES
(20, '2014-02-18 23:39:19', '922108a5fd5cae2aee807fe1a3f78d438f461ec71392777559', '65dbbcc001905223a3a6a96bc94690c93a86c79c1392778753', 28, 8, 'Desenvolvendo Sistemas com PHP e MySql', 'AP', 'O usuÃ¡rio foi aprovado, notas:<br><ul><li>Texto: Muito bom</li><li>ReferÃªncias: Ã“timo<br></li></ul>'),
(23, '2014-02-19 00:18:41', '390613c5f68937c40b2a78737e79e383aa6b77131392779921', NULL, 29, 8, 'Criando aplicativos poderosos', 'AG', ''),
(24, '2014-02-19 00:22:28', '4a270d07d72e24b8f488fd0be1b59123f564c6281392780148', '3fd81d465daf2a8b352893994503ee5b2a06a96b1392781266', 29, 8, 'Desenvolvendo Sistemas com PHP e MySql', 'AP', 'Bla bla bla<br>');

--
-- Gatilhos `Submissoes`
--
DROP TRIGGER IF EXISTS `submissoes_log`;
DELIMITER //
CREATE TRIGGER `submissoes_log` AFTER INSERT ON `Submissoes`
 FOR EACH ROW BEGIN

		INSERT INTO Submissoes_Log (id,status,titulo_trabalho,comentarios,arquivo_inicial, arquivo_final,author_id,idEventos,operation,date) VALUES(NEW.id,NEW.status,NEW.titulo_trabalho, NEW.comentarios,NEW.arquivo_inicial, NEW.arquivo_final,NEW.author_id,NEW.idEventos, "INSERT",NOW());	

END
//
DELIMITER ;
DROP TRIGGER IF EXISTS `submissoes_log_delete`;
DELIMITER //
CREATE TRIGGER `submissoes_log_delete` AFTER DELETE ON `Submissoes`
 FOR EACH ROW BEGIN


		INSERT INTO Submissoes_Log (id,status,titulo_trabalho,comentarios,arquivo_inicial, arquivo_final,author_id,idEventos,operation,date) VALUES(OLD.id,OLD.status,OLD.titulo_trabalho, OLD.comentarios,OLD.arquivo_inicial, OLD.arquivo_final,OLD.author_id,OLD.idEventos, "DELETE", NOW());	


END
//
DELIMITER ;
DROP TRIGGER IF EXISTS `submissoes_log_update`;
DELIMITER //
CREATE TRIGGER `submissoes_log_update` AFTER UPDATE ON `Submissoes`
 FOR EACH ROW BEGIN


		INSERT INTO Submissoes_Log (id,status,titulo_trabalho,comentarios,arquivo_inicial, arquivo_final,author_id,idEventos,operation, date) VALUES(NEW.id,NEW.status,NEW.titulo_trabalho, NEW.comentarios,NEW.arquivo_inicial, NEW.arquivo_final,NEW.author_id,NEW.idEventos, "UPDATE", NOW());	

END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `Submissoes_autores`
--

CREATE TABLE IF NOT EXISTS `Submissoes_autores` (
  `Usuarios_id` int(11) NOT NULL,
  `Submissoes_id` int(11) NOT NULL,
  PRIMARY KEY (`Usuarios_id`,`Submissoes_id`),
  KEY `fk_Usuarios_has_Submissoes_Submissoes1_idx` (`Submissoes_id`),
  KEY `fk_Usuarios_has_Submissoes_Usuarios1_idx` (`Usuarios_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Fazendo dump de dados para tabela `Submissoes_autores`
--

INSERT INTO `Submissoes_autores` (`Usuarios_id`, `Submissoes_id`) VALUES
(3, 20),
(27, 23);

-- --------------------------------------------------------

--
-- Estrutura para tabela `Submissoes_Log`
--

CREATE TABLE IF NOT EXISTS `Submissoes_Log` (
  `id` int(11) DEFAULT NULL,
  `status` char(2) DEFAULT NULL,
  `titulo_trabalho` text,
  `comentarios` longtext,
  `arquivo_inicial` text,
  `arquivo_final` text,
  `author_id` int(11) DEFAULT NULL,
  `idEventos` int(11) DEFAULT NULL,
  `operation` varchar(255) DEFAULT NULL,
  `DATE` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Fazendo dump de dados para tabela `Submissoes_Log`
--

INSERT INTO `Submissoes_Log` (`id`, `status`, `titulo_trabalho`, `comentarios`, `arquivo_inicial`, `arquivo_final`, `author_id`, `idEventos`, `operation`, `DATE`) VALUES
(1, '12', 'asdasd', '123123', 'asdasd', 'asdasd', 12, 12, 'INSERT', '0000-00-00 00:00:00'),
(20, 'AP', 'Desenvolvendo Sistemas com PHP e MySql', 'O usuÃ¡rio foi aprovado, notas:<br><ul><li>Texto: Muito bom</li><li>ReferÃªncias: Ã“timo<br></li></ul>', '922108a5fd5cae2aee807fe1a3f78d438f461ec71392777559', '65dbbcc001905223a3a6a96bc94690c93a86c79c1392778753', 28, 8, 'UPDATE', '2014-02-19 11:21:33'),
(25, 'AG', 'Palestra 1', '', 'ad51c250aacad5017ce4c8359839ea906c9e71021392819717', NULL, 28, 8, 'INSERT', '2014-02-19 11:21:57'),
(25, 'AG', 'Palestra 1', '', 'ad51c250aacad5017ce4c8359839ea906c9e71021392819717', NULL, 28, 8, 'DELETE', '2014-02-19 11:22:07'),
(24, 'AP', 'Desenvolvendo Sistemas com PHP e MySql', 'Bla bla bla<br>', '4a270d07d72e24b8f488fd0be1b59123f564c6281392780148', '3fd81d465daf2a8b352893994503ee5b2a06a96b1392781266', 29, 8, 'UPDATE', '2014-02-19 11:35:33');

-- --------------------------------------------------------

--
-- Estrutura para tabela `Usuarios`
--

CREATE TABLE IF NOT EXISTS `Usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nomeCompleto` varchar(255) DEFAULT NULL,
  `cpf` varchar(50) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `celular` varchar(50) NOT NULL,
  `instituicao` varchar(255) NOT NULL,
  `senha` varchar(255) DEFAULT NULL,
  `submissao` varchar(2) DEFAULT NULL,
  `tipo` varchar(2) DEFAULT NULL,
  `status` varchar(2) DEFAULT NULL,
  `lastLogin` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `payment_receipt` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=30 ;

--
-- Fazendo dump de dados para tabela `Usuarios`
--

INSERT INTO `Usuarios` (`id`, `nomeCompleto`, `cpf`, `email`, `celular`, `instituicao`, `senha`, `submissao`, `tipo`, `status`, `lastLogin`, `created`, `payment_receipt`) VALUES
(1, 'NÃ­cholas AndrÃ© Pinho de Oliveira', '092.078.984-62', 'nicholas@iotecnologia.com.br', '(84) 9141-0179', 'UFERSA', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', 'S', 'AD', 'AP', '2014-02-19 12:11:15', '2013-06-27 08:48:34', ''),
(3, 'Nicholas Participante2', '', 'nicholasandreoliveira9@gmail.com', '', '', '7110eda4d09e062aa5e4a390b0a572ac0d2c0220', 'S', 'PA', 'PG', '2013-06-28 08:58:02', '2013-06-27 09:19:10', ''),
(8, 'Rosana Moniky', '266.086.857-34', 'nicholas2@uol.com.br', '(84) 9101-1234', 'UFERSA', '7110eda4d09e062aa5e4a390b0a572ac0d2c0220', 'N', 'PA', 'AP', NULL, '2013-06-28 08:49:01', ''),
(9, 'Pedro Bosta', '993.371.958-01', 'pedro@ufersa.edu.br', '', '', '7110eda4d09e062aa5e4a390b0a572ac0d2c0220', 'N', 'AD', 'AP', NULL, '2013-06-28 08:49:44', ''),
(10, 'Isadora', '238.644.819-37', 'nicholas@ig.com.br', '(84) 9141-0179', 'UFERSA', '7110eda4d09e062aa5e4a390b0a572ac0d2c0220', 'N', 'PA', 'PG', '2014-02-17 02:19:06', '2013-06-28 14:16:58', '46bb54263cc0edec96b043c611b98e6d2c1505911392605929'),
(11, 'Taynara', '537.201.125-43', 'nicholas@uol.com.us', '(84) 9141-0179', 'UFERSA', '7110eda4d09e062aa5e4a390b0a572ac0d2c0220', 'S', 'PA', 'AP', NULL, '2013-06-28 14:30:34', ''),
(12, 'Maspoly Paiva', '869.487.256-00', 'nick@uol.com.br', '(84) 9141-0179', 'UFERSA', '7110eda4d09e062aa5e4a390b0a572ac0d2c0220', 'N', 'PA', 'AP', NULL, '2013-06-28 14:43:52', ''),
(18, 'Breno Barros Do Telles', '426.825.537-02', 'nicholas@ig4.com.br', '(84) 9141-0179', 'UFERSA', 'ad8984bf11d1a12c6382ca9a89a3dab06724c442', 'N', 'AD', 'AP', NULL, '2013-06-28 15:13:06', ''),
(20, 'Aline', '854.664.142-04', 'nicholas@ig5.com.br', '(84) 9141-0179', 'UFERSA', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', 'N', 'PA', 'PG', '2013-06-30 23:49:28', '2013-06-28 15:15:47', ''),
(22, 'Melissa', '144.321.914-22', 'melissa@gmail.com', '(84) 9141-0179', 'UFERSa', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', 'N', 'PA', 'AP', NULL, '2013-06-28 15:24:55', ''),
(23, 'Melissa AndrÃ©', '322.133.475-30', 'nicholas@ig6.com.br', '(84) 9141-0389', 'UFERSA#', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', 'N', 'PA', 'AP', '2013-06-29 23:54:00', '2013-06-28 15:25:30', ''),
(24, 'Teste', '618.143.306-66', 'niacho@asdas.com.br', '(84) 9141-0179', '', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', 'N', 'PA', 'AP', '2013-06-30 00:17:21', '2013-06-28 22:30:25', ''),
(25, 'Testando', '222.580.228-94', 'nicholas@ig10.com.br', '(84) 9141-0179', 'UFERSA', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', 'N', 'PA', 'AP', NULL, '2013-06-28 23:22:54', ''),
(26, 'Teste34', '050.412.973-20', 'asdasd@asdas.com', '(84) 9141-2312', 'UERN', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', 'S', 'AD', 'PG', '2013-06-29 23:33:54', '2013-06-29 23:33:14', ''),
(27, 'Iggo GuimarÃ£es', '276.855.795-10', 'iggo@gmail.com', '(84) 1231-2312', 'UFERSA', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', 'N', 'PA', 'PG', '2014-02-16 18:43:12', '2014-02-16 17:33:08', '9a8f9d033bd182f0cde01cd1844a388039ad167e1392575557'),
(28, 'Ramiro JÃºnior', '515.576.644-39', 'ramiro@gmail.com', '(84) 9141-0179', 'UFERSA', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', 'S', 'PA', 'PG', '2014-02-19 11:19:42', '2014-02-17 12:39:30', 'c3d597dc1553c18ae3e0531fe144498e9f3225261392750583'),
(29, 'Felipe LeÃ£o', '517.589.402-12', 'felipe@gmail.com', '', 'UFERSA', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', 'S', 'PA', 'PG', '2014-02-19 00:16:03', '2014-02-19 00:06:09', 'ad969b42a3a1f38ca860aafd2365ca28ca3ed2931392779797');

--
-- Restrições para dumps de tabelas
--

--
-- Restrições para tabelas `Inscricoes`
--
ALTER TABLE `Inscricoes`
  ADD CONSTRAINT `fk_SubEventos_has_Usuarios_SubEventos1` FOREIGN KEY (`idSubEventos`) REFERENCES `SubEventos` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_SubEventos_has_Usuarios_Usuarios1` FOREIGN KEY (`idUsuarios`) REFERENCES `Usuarios` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Restrições para tabelas `SubEventos`
--
ALTER TABLE `SubEventos`
  ADD CONSTRAINT `fk_SubEventos_Eventos` FOREIGN KEY (`idEventos`) REFERENCES `Eventos` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Restrições para tabelas `Submissoes`
--
ALTER TABLE `Submissoes`
  ADD CONSTRAINT `fk_Submissoes_Usuarios1` FOREIGN KEY (`author_id`) REFERENCES `Usuarios` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Restrições para tabelas `Submissoes_autores`
--
ALTER TABLE `Submissoes_autores`
  ADD CONSTRAINT `fk_Usuarios_has_Submissoes_Submissoes1` FOREIGN KEY (`Submissoes_id`) REFERENCES `Submissoes` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Usuarios_has_Submissoes_Usuarios1` FOREIGN KEY (`Usuarios_id`) REFERENCES `Usuarios` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
