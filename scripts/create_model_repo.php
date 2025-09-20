<?php

if ($argc < 2) {
    die("Uso: php scripts/create_model_repo.php Nome\n");
}

$nome = $argv[1];
$nomeRepository = $nome . "Repository";

$modelsDir = __DIR__ . '/../models';
$repositoriesDir = __DIR__ . '/../repositories';

// Conteúdo do Model
$modelContent = "<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

class $nome extends Model {
    protected \$table = '$nome';
    protected \$fillable = [
        'item-1', 
        'item-2'
    ];
    public \$timestamps = false;
}";

// Conteúdo do Repository
$repositoryContent = "<?php

namespace Repositories;

use Models\\$nome;

class $nomeRepository {
    // pegando
    public static function getAll() {
        return $nome::all();
    }

    // pegando único
    public static function getOne(\$id) {
        return $nome::where('id', \$id)->first();
    }

    // atualizando 
    public static function update(\$id, \$dados) {
        return $nome::where('id', \$id)->update([
            'campo-1' => \$dados['exemplo-1'],
            'campo-2' => \$dados['exemplo-2']
        ]);
    }

    // deletando
    public static function delete(\$id) {
        return $nome::where('id', \$id)->delete();
    }
}";

// Criando arquivos
$modelPath = "$modelsDir/$nome.php";
$repositoryPath = "$repositoriesDir/{$nome}Repository.php";

file_put_contents($modelPath, $modelContent);
file_put_contents($repositoryPath, $repositoryContent);

echo "Model criado: $modelPath\n";
echo "Repository criado: $repositoryPath\n";
