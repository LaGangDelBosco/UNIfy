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
        const commentTextarea = document.getElementById(`comment_${post_id}`);

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
                            const commentList = document.getElementById(`comment_list_${post_id}`);
                            if (commentList) { // Check if commentList exists
                                const newComment = document.createElement('ul');
                                newComment.innerHTML = `<li><a href="#">${data.username}</a></li><li>${data.created_at}</li><li>${comment}</li>`;
                                commentList.insertBefore(newComment, commentList.firstChild);
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