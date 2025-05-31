<?php
// URLs das imagens de exemplo
$images = [
    'feijoada.jpg' => 'https://img.cybercook.com.br/receitas/776/feijoada-623x350.jpeg',
    'salada_caesar.jpg' => 'https://img.cybercook.com.br/receitas/722/salada-caesar-623x350.jpeg',
    'bolo_chocolate.jpg' => 'https://img.cybercook.com.br/receitas/591/bolo-de-chocolate-fofinho-623x350.jpeg'
];

// Criar diretório de uploads se não existir
if (!file_exists('uploads')) {
    mkdir('uploads', 0777, true);
}

// Fazer download e salvar as imagens
foreach ($images as $filename => $url) {
    $image_content = file_get_contents($url);
    if ($image_content !== false) {
        file_put_contents('uploads/' . $filename, $image_content);
        echo "Imagem $filename baixada com sucesso!<br>";
    } else {
        echo "Erro ao baixar a imagem $filename<br>";
    }
}

echo "Processo concluído!";
?> 