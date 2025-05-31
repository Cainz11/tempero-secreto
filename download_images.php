<?php
// Array com as URLs das imagens e seus nomes de arquivo
$images = [
    // Comida Japonesa
    'sushi-salmao.jpg' => 'https://images.pexels.com/photos/359993/pexels-photo-359993.jpeg', // Sushi de salmão fresco
    'tempura.jpg' => 'https://images.pexels.com/photos/14355612/pexels-photo-14355612.jpeg', // Tempurá de camarão
    'ramen.jpg' => 'https://images.pexels.com/photos/2664216/pexels-photo-2664216.jpeg', // Ramen japonês

    // Comida Mineira
    'feijao-tropeiro.jpg' => 'https://images.pexels.com/photos/5695890/pexels-photo-5695890.jpeg', // Feijão com farofa
    'frango-quiabo.jpg' => 'https://images.pexels.com/photos/5409009/pexels-photo-5409009.jpeg', // Frango com quiabo
    'tutu-feijao.jpg' => 'https://images.pexels.com/photos/5339079/pexels-photo-5339079.jpeg', // Tutu de feijão

    // Comida Nordestina
    'acaraje.jpg' => 'https://images.pexels.com/photos/4958641/pexels-photo-4958641.jpeg', // Acarajé típico
    'carne-sol-mandioca.jpg' => 'https://images.pexels.com/photos/6542793/pexels-photo-6542793.jpeg', // Carne de sol com mandioca
    'tapioca.jpg' => 'https://images.pexels.com/photos/5339122/pexels-photo-5339122.jpeg', // Tapioca recheada

    // Sem Lactose
    'bolo-chocolate-sem-lactose.jpg' => 'https://images.pexels.com/photos/291528/pexels-photo-291528.jpeg', // Bolo de chocolate
    'mousse-maracuja-sem-lactose.jpg' => 'https://images.pexels.com/photos/7474203/pexels-photo-7474203.jpeg', // Mousse de maracujá
    'pao-sem-lactose.jpg' => 'https://images.pexels.com/photos/1775043/pexels-photo-1775043.jpeg', // Pão caseiro

    // Sem Açúcar
    'bolo-banana-sem-acucar.jpg' => 'https://images.pexels.com/photos/4110541/pexels-photo-4110541.jpeg', // Bolo de banana
    'sorvete-morango-sem-acucar.jpg' => 'https://images.pexels.com/photos/5060304/pexels-photo-5060304.jpeg', // Sorvete natural
    'cookies-amendoim-sem-acucar.jpg' => 'https://images.pexels.com/photos/5717237/pexels-photo-5717237.jpeg' // Cookies de amendoim
];

// Criar diretório se não existir
$upload_dir = 'uploads/recipes/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Configurar contexto para ignorar SSL
$context = stream_context_create([
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false,
    ],
]);

// Fazer download de cada imagem
foreach ($images as $filename => $url) {
    $image_data = file_get_contents($url, false, $context);
    if ($image_data !== false) {
        file_put_contents($upload_dir . $filename, $image_data);
        echo "Imagem baixada com sucesso: $filename\n";
    } else {
        echo "Erro ao baixar imagem: $filename\n";
    }
}

echo "Download de imagens concluído!\n";