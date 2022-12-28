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
    case 'PUT':
        // Editar veículo
        // $id = intval($_GET["id"]);
        editar_veiculo();
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

    // Takes raw data from the request
    $json = file_get_contents('php://input');

    // Converts it into a PHP object
    $data = json_decode($json);

    // $id = $data->id;
    $fabricante = $data->fabricante;
    $modelo = $data->modelo;
    $cor = $data->cor;
    $placa = $data->placa;
    $ano = $data->ano;
    $valor = $data->valor;

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
    $query = "DELETE FROM veiculos WHERE id= " . $id;

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

function editar_veiculo()
{
    global $connection;

    // Convertemos para string o conteudo do arquivo e inserimos em um array para uso:

    // parse_str(file_get_contents("php://input"), $post_vars);

    // Takes raw data from the request
    $json = file_get_contents('php://input');

    // Converts it into a PHP object
    $data = json_decode($json);



    // Definimos as variáveis de acordo com a declaração dela no array, visto que no método PUT não temos a variável $_PUT para utilizarmos como anteriormente
    $id = $data->id;
    $fabricante = $data->fabricante;
    $modelo = $data->modelo;
    $cor = $data->cor;
    $placa = $data->placa;
    $ano = $data->ano;
    $valor = $data->valor;

    $query = "UPDATE veiculos SET fabricante='{$fabricante}', modelo='{$modelo}', cor='{$cor}', placa='{$placa}', ano={$ano}, valor={$valor} WHERE id=" . $id;
    if (mysqli_query($connection, $query)) {
        $response = array(
            'status' => 1,
            'status_message' => 'Veículo editado com sucesso'
        );
    } else {
        $response = array(
            'status' => 0,
            'status_message' => 'Falha ao editar veículo.'
        );
    }
    header('Content-Type: application/json');
    echo json_encode($response);







    // parse_str(file_get_contents("php://input"), $post_vars);
    // $fabricante = $post_vars["fabricante"];
    // $modelo = $post_vars["modelo"];
    // $cor = $post_vars["cor"];
    // $placa = $post_vars["placa"];
    // $ano = $post_vars["ano"];
    // $valor = $post_vars["valor"];
}
