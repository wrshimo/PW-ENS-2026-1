-- Inserção de dados na tabela de produtos

-- Limpa a tabela antes de inserir novos dados (opcional, mas recomendado para testes)
-- TRUNCATE TABLE `loja`.`produtos`;

INSERT INTO `produtos` (`id`, `nome`, `descricao`, `categoria`, `preco`, `imagem`) VALUES
(1, 'Smartphone Samsung Galaxy S23', 'Eletrônicos: Smartphone Samsung Galaxy S23. Produto demonstrativo para o laboratório de DOM e eventos.', 'Eletrônicos', 3599.00, 'https://placehold.co/400x400?text=Smartphone'),
(2, 'Fone de Ouvido Sony WH-1000XM5', 'Eletrônicos: Fone de Ouvido Sony WH-1000XM5. Produto demonstrativo para o laboratório de DOM e eventos.', 'Eletrônicos', 2499.00, 'https://placehold.co/400x400?text=Fone'),
(3, 'Smartwatch Apple Watch Series 9', 'Eletrônicos: Smartwatch Apple Watch Series 9. Produto demonstrativo para o laboratório de DOM e eventos.', 'Eletrônicos', 4199.00, 'https://placehold.co/400x400?text=Smartwatch'),
(4, 'Notebook Dell Inspiron 15', 'Eletrônicos: Notebook Dell Inspiron 15. Produto demonstrativo para o laboratório de DOM e eventos.', 'Eletrônicos', 3899.00, 'https://placehold.co/400x400?text=Notebook'),
(5, 'Teclado Mecânico Gamer Logitech', 'Eletrônicos: Teclado Mecânico Gamer Logitech. Produto demonstrativo para o laboratório de DOM e eventos.', 'Eletrônicos', 699.90, 'https://placehold.co/400x400?text=Teclado'),
(6, 'Camiseta Básica de Algodão', 'Roupas: Camiseta Básica de Algodão. Produto demonstrativo para o laboratório de DOM e eventos.', 'Roupas', 49.90, 'https://placehold.co/400x400?text=Camiseta'),
(7, 'Calça Jeans Masculina Slim', 'Roupas: Calça Jeans Masculina Slim. Produto demonstrativo para o laboratório de DOM e eventos.', 'Roupas', 149.90, 'https://placehold.co/400x400?text=Calca'),
(8, 'Jaqueta de Couro Sintético Feminina', 'Roupas: Jaqueta de Couro Sintético Feminina. Produto demonstrativo para o laboratório de DOM e eventos.', 'Roupas', 249.90, 'https://placehold.co/400x400?text=Jaqueta'),
(9, 'Tênis Esportivo Corrida Unissex', 'Roupas: Tênis Esportivo Corrida Unissex. Produto demonstrativo para o laboratório de DOM e eventos.', 'Roupas', 349.90, 'https://placehold.co/400x400?text=Tenis'),
(10, 'Vestido Midi Floral', 'Roupas: Vestido Midi Floral. Produto demonstrativo para o laboratório de DOM e eventos.', 'Roupas', 199.90, 'https://placehold.co/400x400?text=Vestido'),
(11, 'Livro: O Senhor dos Anéis - Box Completo', 'Livros: Livro: O Senhor dos Anéis - Box Completo. Produto demonstrativo para o laboratório de DOM e eventos.', 'Livros', 189.90, 'https://placehold.co/400x400?text=Livro'),
(12, 'Livro: 1984 - George Orwell', 'Livros: Livro: 1984 - George Orwell. Produto demonstrativo para o laboratório de DOM e eventos.', 'Livros', 39.90, 'https://placehold.co/400x400?text=Livro'),
(13, 'Livro: O Pequeno Príncipe', 'Livros: Livro: O Pequeno Príncipe. Produto demonstrativo para o laboratório de DOM e eventos.', 'Livros', 29.90, 'https://placehold.co/400x400?text=Livro'),
(14, 'Kit Ferramentas de Jardinagem 10 Peças', 'Casa e Jardim: Kit Ferramentas de Jardinagem 10 Peças. Produto demonstrativo para o laboratório de DOM e eventos.', 'Casa e Jardim', 129.90, 'https://placehold.co/400x400?text=Kit'),
(15, 'Cadeira de Escritório Ergonômica', 'Casa e Jardim: Cadeira de Escritório Ergonômica. Produto demonstrativo para o laboratório de DOM e eventos.', 'Casa e Jardim', 899.90, 'https://placehold.co/400x400?text=Cadeira'),
(16, 'Aspirador de Pó Robô Inteligente', 'Casa e Jardim: Aspirador de Pó Robô Inteligente. Produto demonstrativo para o laboratório de DOM e eventos.', 'Casa e Jardim', 1299.00, 'https://placehold.co/400x400?text=Aspirador'),
(17, 'Mouse Gamer RGB 12000 DPI', 'Eletrônicos: Mouse Gamer RGB 12000 DPI. Produto demonstrativo para o laboratório de DOM e eventos.', 'Eletrônicos', 259.90, 'https://placehold.co/400x400?text=Mouse'),
(18, 'Cafeteira Elétrica Programável', 'Casa e Jardim: Cafeteira Elétrica Programável. Produto demonstrativo para o laboratório de DOM e eventos.', 'Casa e Jardim', 449.00, 'https://placehold.co/400x400?text=Cafeteira'),
(19, 'Moletom Canguru com Capuz', 'Roupas: Moletom Canguru com Capuz. Produto demonstrativo para o laboratório de DOM e eventos.', 'Roupas', 129.90, 'https://placehold.co/400x400?text=Moletom');

-- Administrador com senha inicial criptografada: admin123
INSERT INTO usuarios (nome, usuario, senha)
VALUES (
  'Administrador',
  'admin',
  '$2y$10$Onxh6J4GdsMAgl2BqxEdtOtJS4UTGoecoAKkfJAcdoQ7Ad.w.rN1i'
);