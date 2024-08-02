document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.lprw-rate-button').forEach(function(button) {
        let postId = button.dataset.post_id;
        if (hasRated(postId)) {
            showThankYouMessage(button);
        } else {
            button.addEventListener('click', function() {
                let ratingContainer = document.createElement('div');
                ratingContainer.className = 'lprw-rating-stars';

                for (let i = 1; i <= 5; i++) {
                    let star = document.createElement('span');
                    star.className = 'star';
                    star.textContent = '☆';
                    star.dataset.rating = i;

                    star.addEventListener('mouseover', function() {
                        highlightStars(star.dataset.rating, ratingContainer);
                    });

                    star.addEventListener('mouseout', function() {
                        resetStars(ratingContainer);
                    });

                    star.addEventListener('click', function() {
                        submitRating(postId, star.dataset.rating, button, ratingContainer);
                    });

                    ratingContainer.appendChild(star);
                }

                button.replaceWith(ratingContainer);
            });
        }
    });

    function highlightStars(rating, container) {
        container.querySelectorAll('.star').forEach(function(star, index) {
            if (index < rating) {
                star.classList.add('filled');
            } else {
                star.classList.remove('filled');
            }
        });
    }

    function resetStars(container) {
        container.querySelectorAll('.star').forEach(function(star) {
            star.classList.remove('filled');
        });
    }

    function submitRating(postId, rating, button, container) {
        let xhr = new XMLHttpRequest();
        xhr.open('POST', lprw_ajax.url, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (xhr.status === 200) {
                let response = JSON.parse(xhr.responseText);
                if (response.success) {
                    setRated(postId);
                    showThankYouMessage(container);
                    updateAverageRating(postId, response.data.average_rating);
                } else {
                    alert('An error occurred. Please try again.');
                }
            }
        };
        xhr.send('action=lprw_rate&post_id=' + postId + '&rating=' + rating);
    }

    function updateAverageRating(postId, averageRating) {
        document.querySelectorAll('.lprw-post-info[data-post_id="' + postId + '"] .lprw-rating-average').forEach(function(element) {
            element.textContent = averageRating.toFixed(1);
        });
    }

    function hasRated(postId) {
        let ratedPosts = getCookie('rated_posts');
        if (ratedPosts) {
            ratedPosts = JSON.parse(ratedPosts);
            return ratedPosts.includes(postId);
        }
        return false;
    }

    function setRated(postId) {
        let ratedPosts = getCookie('rated_posts');
        if (ratedPosts) {
            ratedPosts = JSON.parse(ratedPosts);
        } else {
            ratedPosts = [];
        }
        ratedPosts.push(postId);
        setCookie('rated_posts', JSON.stringify(ratedPosts), 365);
    }

    function showThankYouMessage(container) {
        let thankYouMessage = document.createElement('div');
        thankYouMessage.className = 'thank-you';
        thankYouMessage.textContent = 'Thank you for your rating!';
        container.replaceWith(thankYouMessage);
    }

    function setCookie(name, value, days) {
        let expires = "";
        if (days) {
            let date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "") + expires + "; path=/";
    }

    function getCookie(name) {
        let nameEQ = name + "=";
        let ca = document.cookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) === ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }
});
