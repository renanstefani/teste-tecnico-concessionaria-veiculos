<?php

// Realizando conexão com o DB
$connection = mysqli_connect('localhost', 'root', '', 'db_garagem');


// Switch para alternar entre os métodos de requisição
$request_method = $_SERVER["REQUEST_METHOD"];
switch ($request_method) {
    case 'GET':
        // Consultando lista de veículos ou veículo específico
        if (!empty($_GET["id"])) {
            $id = intval($_GET["id"]);
            listar_veiculos($id);
        } else {
            listar_veiculos();
        }
        break;
    case 'POST':
        // Inserir veículo
        inserir_veiculo();
        break;
    case 'DELETE':
        // Remover veículo
        $id = intval($_GET["id"]);
        remover_veiculo($id);
        break;
    default:
        // Invalid Request Method
        header("Método de requisição inválido");
        break;
}

// Realizando consulta
function listar_veiculos($id = 0)
{
    global $connection;

    // executando a requisição de todos os veículos
    $query = "SELECT * FROM veiculos";

    // Se o id for diferente de 0 - devolva apenas o id especificado (consultando apenas um veiculo)
    if ($id != 0) {
        $query .= " WHERE id=" . $id . " LIMIT 1";
    }

    // array vazio para armazenarmos a response
    $response = array();

    // o resultado da requisicao sera aplicada a variavel result
    $result = mysqli_query($connection, $query);

    // enquanto houver linhas no resultado elas serão retornadas
    while ($row = mysqli_fetch_array($result)) {
        $response[] = $row;
    }

    header('Content-Type: application/json');

    echo json_encode($response);
}

// Inserindo um veículo novo
function inserir_veiculo()
{
    global $connection;

    // definindo dados de acordo com a tabela
    $fabricante = $_POST["fabricante"];
    $modelo = $_POST["modelo"];
    $cor = $_POST["cor"];
    $placa = $_POST["placa"];
    $ano = $_POST["ano"];
    $valor = $_POST["valor"];

    // realizando a query
    $query = "INSERT INTO veiculos SET fabricante='{$fabricante}', modelo='{$modelo}', cor='{$cor}', placa='{$placa}', ano={$ano}, valor={$valor}";
    if (mysqli_query($connection, $query)) {
        // se for adicionado um indice ao array o veículo foi adicionado ao DB
        $response = array(
            'status' => 1,
            'status_message' => 'Veículo adicionado com sucesso.'
        );
    } else {
        $response = array(
            'status' => 0,
            'status_message' => 'Não foi possível adicionar o veículo.'
        );
    }
    header('Content-Type: application/json');
    echo json_encode($response);
}

// Removendo veículo do banco de dados
function remover_veiculo($id)
{
    global $connection;

    // executando a requisição
    $query = "DELETE FROM veiculos WHERE id=" . $id;

    if (mysqli_query($connection, $query)) {
        // caso tenha um id especificado o veículo será removido do DB
        $response = array(
            'status' => 1,
            'status_message' => 'Veículo removido com sucesso.'
        );
    } else {
        $response = array(
            'status' => 0,
            'status_message' => 'Falha ao tentar remover o veículo.'
        );
    }
    header('Content-Type: application/json');
    echo json_encode($response);
}

?>