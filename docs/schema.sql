CREATE DATABASE loja
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE loja;

CREATE TABLE `produtos` (
    `id` INT NOT NULL AUTO_INCREMENT ,
    `nome` VARCHAR(100) NOT NULL ,
    `descricao` TEXT NOT NULL ,
    `categoria` VARCHAR(50) NOT NULL ,
    `preco` DECIMAL(15,2) NOT NULL ,
    `imagem` VARCHAR(255) NOT NULL ,
     PRIMARY KEY (`id`)
) ENGINE = InnoDB;