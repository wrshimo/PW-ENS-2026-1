<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Usuários do Banco de Dados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Usuários do Banco de Dados</h1>
        <?php
        require_once 'db.php';

        try {
            $pdo = getPdo();
            
            // 1. Usar prepare/execute - a forma moderna e segura
            $stmt = $pdo->prepare("SELECT user, host FROM mysql.user ORDER BY user");
            $stmt->execute();
            
            // 2. Usar FETCH_NUM para evitar problemas com nomes de colunas
            $users = $stmt->fetchAll(PDO::FETCH_NUM);

            if (count($users) > 0) {
                echo '<table class="table table-striped table-bordered">';
                echo '<thead class="table-dark">';
                echo '<tr><th>Usuário</th><th>Host</th></tr>';
                echo '</thead>';
                echo '<tbody>';
                foreach ($users as $user) {
                    echo '<tr>';
                    // 3. Acessar colunas por índice numérico
                    echo '<td>' . htmlspecialchars($user[0]) . '</td>';
                    echo '<td>' . htmlspecialchars($user[1]) . '</td>';
                    echo '</tr>';
                }
                echo '</tbody>';
                echo '</table>';
            } else {
                echo '<div class="alert alert-info">Nenhum usuário encontrado.</div>';
            }

        } catch (PDOException $e) {
            echo '<div class="alert alert-danger">';
            echo '<strong>Erro:</strong> Não foi possível conectar ao banco de dados e listar os usuários. <br>';
            echo 'Mensagem: ' . $e->getMessage();
            echo '</div>';
        }
        ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
