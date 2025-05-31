// Função para dar like em uma receita
function likeRecipe(recipeId) {
    $.post(SITE_URL + '?route=like_recipe', {
        recipe_id: recipeId,
        csrf_token: CSRF_TOKEN
    })
    .done(function(response) {
        if (response.success) {
            // Atualizar o ícone e contador de likes
            const likeBtn = $('#like-btn-' + recipeId);
            const likesCount = $('#likes-count-' + recipeId);
            
            if (response.liked) {
                likeBtn.addClass('text-danger').removeClass('text-muted');
                likesCount.text(parseInt(likesCount.text()) + 1);
            } else {
                likeBtn.addClass('text-muted').removeClass('text-danger');
                likesCount.text(parseInt(likesCount.text()) - 1);
            }
        }
    });
}

// Função para marcar notificação como lida
function markNotificationAsRead(notificationId) {
    $.post(SITE_URL + '?route=mark_notification', {
        notification_id: notificationId,
        csrf_token: CSRF_TOKEN
    })
    .done(function(response) {
        if (response.success) {
            // Remover a notificação da lista
            $('#notification-' + notificationId).fadeOut();
            
            // Atualizar o contador de notificações
            const count = parseInt($('#notifications-count').text());
            if (count > 0) {
                $('#notifications-count').text(count - 1);
                if (count - 1 === 0) {
                    $('#notifications-count').hide();
                }
            }
        }
    });
}

// Função para pré-visualizar imagem antes do upload
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            $('#image-preview').attr('src', e.target.result).show();
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// Inicialização quando o documento estiver pronto
$(document).ready(function() {
    // Inicializar tooltips do Bootstrap
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Preview de imagem no upload
    $('input[type="file"]').change(function() {
        previewImage(this);
    });

    // Confirmação de exclusão
    $('.delete-confirm').click(function(e) {
        if (!confirm('Tem certeza que deseja excluir este item?')) {
            e.preventDefault();
        }
    });

    // Atualizar contadores em tempo real
    setInterval(function() {
        if (isLoggedIn) {
            $.get(SITE_URL + '?route=check_notifications')
                .done(function(response) {
                    if (response.unread_count > 0) {
                        $('#notifications-count').text(response.unread_count).show();
                    } else {
                        $('#notifications-count').hide();
                    }
                });
        }
    }, 60000); // Verificar a cada minuto
}); 