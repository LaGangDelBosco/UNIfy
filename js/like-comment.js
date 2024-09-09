function like_post(){
    document.querySelectorAll('.like-interact').forEach(button => {
        button.addEventListener('click', function() {
            const postId = this.getAttribute('data-post-id');

            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'like-post.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function () {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        const likeCountSpan = button.nextElementSibling;
                        likeCountSpan.textContent = response.likes_number;
                    } else {
                        alert('Errore: ' + response.message);
                    }
                }
            };
            xhr.send('post_id=' + postId);
        });
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const commentButtons = document.querySelectorAll('.comment-interact');
    commentButtons.forEach(commentButton => {
        const post_id = commentButton.getAttribute('id').split('_').pop();
        var isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
        let commentTextarea = document.getElementById(`comment_${post_id}`);
        if(isMobile || window.innerWidth < 768){
            commentTextarea = document.getElementById(`comment_mobile_${post_id}`);
        }       

        commentTextarea.addEventListener('input', function() {
            if (commentTextarea.value.trim() === '') {
                commentButton.disabled = true;
            } else {
                commentButton.disabled = false;
            }
        });

        // Initial check to disable the button if the textarea is empty on page load
        if (commentTextarea.value.trim() === '') {
            commentButton.disabled = true;
        }

        commentButton.addEventListener('click', function() {
            var comment = commentTextarea.value;

            var regex = /^[a-zA-Z0-9\s,.:"';!?àèìòùÀÈÌÒÙáéíóúýÁÉÍÓÚÝâêîôûÂÊÎÔÛãñõÃÑÕäëïöüÿÄËÏÖÜŸ\[\]\/]{3,300}$/;
            correct = regex.test(comment);
            document.getElementById(`comment_error_${post_id}`).innerHTML = "";

            if(!correct){
                document.getElementById(`comment_error_${post_id}`).innerHTML = "Il commento deve contenere almeno 3 caratteri e massimo 300";
                return;
            }

            commentTextarea.value = '';
            commentButton.disabled = true;

            fetch('comment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'post_id=' + post_id + '&comment=' + comment,
            })
                .then(response => response.text()) // Get the response as text
                .then(text => {
                    try {
                        const data = JSON.parse(text); // Parse the JSON
                        if (data.success) {
                            let commentList = document.getElementById(`comment_list_${post_id}`);
                            if(isMobile || window.innerWidth < 768){
                                commentList = document.getElementById(`comment_list_${post_id}_mobile`);
                            }
                            if (commentList) { // Check if commentList exists
                                const newComment = document.createElement('ul');
                                newComment.innerHTML = `<li><a href="profilo.php?user=${data.username}">${data.username}</a></li><li>${data.created_at}</li><li>${comment}</li>`;
                                commentList.insertBefore(newComment, commentList.firstChild);

                                newComment.classList.add('highlight');
                            } else {
                                console.error(`Comment list not found for post_id: ${post_id}`);
                            }
                        } else {
                            alert('Errore durante l\'invio del commento');
                        }
                    } catch (error) {
                        console.error('Error parsing JSON:', error);
                        console.error('Response text:', text); // Log the response text for debugging
                    }
                });
        });
    });
});