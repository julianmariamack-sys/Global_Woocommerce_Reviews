(function () {
    'use strict';

    function initWidget(widget) {
        if (!widget || widget.dataset.gwcreReady === '1') return;
        widget.dataset.gwcreReady = '1';

        const prev = widget.querySelector('.gwcre-arrow-prev');
        const next = widget.querySelector('.gwcre-arrow-next');
        const stage = widget.querySelector('.gwcre-slide-container');
        const indicator = widget.querySelector('.gwcre-page-indicator');

        if (!prev || !next || !stage) return;

        function setButtons(page, totalPages) {
            prev.disabled = page <= 1;
            next.disabled = page >= totalPages;
        }

        function updateIndicator(page, totalPages) {
            if (indicator) indicator.textContent = page + ' / ' + totalPages;
        }

        function loadPage(page) {
            const totalPages = parseInt(widget.dataset.totalPages || '1', 10);
            if (page < 1 || page > totalPages || widget.classList.contains('gwcre-is-loading')) return;

            const params = new URLSearchParams();
            params.append('action', 'gwcre_get_page');
            params.append('nonce', (window.GWCRE && GWCRE.nonce) || '');
            params.append('page', String(page));
            params.append('per_page', widget.dataset.perPage || '1');
            params.append('sort', widget.dataset.sort || 'newest');
            params.append('rating', widget.dataset.rating || 'all');
            params.append('show_product', widget.dataset.showProduct || 'no');
            params.append('show_rating', widget.dataset.showRating || 'yes');
            params.append('show_verified', widget.dataset.showVerified || 'no');
            params.append('show_avatar', widget.dataset.showAvatar || 'yes');
            params.append('show_date', widget.dataset.showDate || 'yes');

            widget.classList.add('gwcre-is-loading');
            prev.disabled = true;
            next.disabled = true;

            fetch((window.GWCRE && GWCRE.ajaxUrl) || '/wp-admin/admin-ajax.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
                body: params.toString()
            })
                .then(function (response) {
                    if (!response.ok) throw new Error('Request failed');
                    return response.json();
                })
                .then(function (data) {
                    if (!data || !data.success || !data.data) throw new Error('Invalid response');

                    if (typeof data.data.html === 'string') {
                        stage.innerHTML = data.data.html;
                    }

                    const returnedTotalPages = parseInt(data.data.total_pages || totalPages, 10);
                    widget.dataset.totalPages = String(returnedTotalPages);
                    widget.dataset.page = String(page);
                    updateIndicator(page, returnedTotalPages);
                    setButtons(page, returnedTotalPages);
                })
                .catch(function () {
                    setButtons(parseInt(widget.dataset.page || '1', 10), totalPages);
                })
                .finally(function () {
                    widget.classList.remove('gwcre-is-loading');
                });
        }

        prev.addEventListener('click', function () {
            loadPage(parseInt(widget.dataset.page || '1', 10) - 1);
        });

        next.addEventListener('click', function () {
            loadPage(parseInt(widget.dataset.page || '1', 10) + 1);
        });

        setButtons(parseInt(widget.dataset.page || '1', 10), parseInt(widget.dataset.totalPages || '1', 10));
        updateIndicator(parseInt(widget.dataset.page || '1', 10), parseInt(widget.dataset.totalPages || '1', 10));
    }

    function initAll(scope) {
        (scope || document).querySelectorAll('.gwcre-widget').forEach(initWidget);
    }

    document.addEventListener('DOMContentLoaded', function () {
        initAll(document);
    });

    if (window.jQuery) {
        jQuery(window).on('elementor/frontend/init', function () {
            elementorFrontend.hooks.addAction('frontend/element_ready/gwcre-global-reviews.default', function ($scope) {
                initAll($scope[0]);
            });
        });
    }
})();

(function () {
    'use strict';

    function initReviewForm(widget) {
        if (!widget || widget.dataset.gwcreFormReady === '1') return;
        widget.dataset.gwcreFormReady = '1';

        const form = widget.querySelector('.gwcre-review-form');
        if (!form) return;

        const message = form.querySelector('.gwcre-form-message');
        const ratingInput = form.querySelector('input[name="rating"]');
        const ratingButtons = Array.prototype.slice.call(form.querySelectorAll('.gwcre-rating-button'));
        const submitButton = form.querySelector('.gwcre-review-submit');

        function setMessage(text, type) {
            if (!message) return;
            message.textContent = text || '';
            message.className = 'gwcre-form-message' + (text ? ' is-visible is-' + type : '');
        }

        function paintStars(value) {
            const numericValue = parseInt(value || '0', 10);
            ratingButtons.forEach(function (item) {
                const filled = parseInt(item.dataset.rating || '0', 10) <= numericValue;
                item.classList.toggle('is-selected', filled);
            });
        }

        ratingButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                const value = button.dataset.rating || '';
                if (ratingInput) ratingInput.value = value;
                paintStars(value);
                ratingButtons.forEach(function (item) {
                    const selected = item.dataset.rating === value;
                    item.setAttribute('aria-checked', selected ? 'true' : 'false');
                });
            });

            button.addEventListener('mouseenter', function () {
                paintStars(button.dataset.rating || '0');
            });
        });

        const ratingGroup = form.querySelector('.gwcre-rating-input');
        if (ratingGroup) {
            ratingGroup.addEventListener('mouseleave', function () {
                paintStars(ratingInput ? ratingInput.value : '0');
            });
        }

        form.addEventListener('submit', function (event) {
            event.preventDefault();
            setMessage('', '');

            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            if (!ratingInput || !ratingInput.value) {
                setMessage('Please select a star rating.', 'error');
                return;
            }

            const params = new URLSearchParams();
            params.append('action', 'gwcre_submit_review');
            params.append('nonce', (window.GWCRE && GWCRE.submitNonce) || '');
            params.append('product_id', form.querySelector('[name="product_id"]').value);
            params.append('rating', ratingInput.value);
            params.append('author', form.querySelector('[name="author"]').value);
            params.append('email', form.querySelector('[name="email"]').value);
            params.append('subject', form.querySelector('[name="subject"]').value);
            params.append('review', form.querySelector('[name="review"]').value);
            params.append('website', form.querySelector('[name="website"]').value);
            params.append('confirm_experience', form.querySelector('[name="confirm_experience"]').checked ? '1' : '0');
            params.append('require_email', widget.dataset.requireEmail || 'yes');

            if (submitButton) {
                submitButton.disabled = true;
                submitButton.dataset.originalText = submitButton.textContent;
                submitButton.textContent = (window.GWCRE && GWCRE.submitting) || 'Submitting…';
            }

            fetch((window.GWCRE && GWCRE.ajaxUrl) || '/wp-admin/admin-ajax.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
                body: params.toString()
            })
                .then(function (response) {
                    if (!response.ok) throw new Error('Request failed');
                    return response.json();
                })
                .then(function (data) {
                    if (!data || !data.success || !data.data) {
                        throw new Error(data && data.data && data.data.message ? data.data.message : 'Unable to submit review.');
                    }
                    setMessage(data.data.message || 'Thank you! Your review has been submitted.', 'success');
                    form.reset();
                    if (ratingInput) ratingInput.value = '';
                    ratingButtons.forEach(function (item) {
                        item.classList.remove('is-selected');
                        item.setAttribute('aria-checked', 'false');
                    });
                })
                .catch(function (error) {
                    setMessage(error.message || 'Unable to submit review. Please try again.', 'error');
                })
                .finally(function () {
                    if (submitButton) {
                        submitButton.disabled = false;
                        submitButton.textContent = submitButton.dataset.originalText || 'Submit Review';
                    }
                });
        });
    }

    function initForms(scope) {
        (scope || document).querySelectorAll('.gwcre-review-form-widget').forEach(initReviewForm);
    }

    document.addEventListener('DOMContentLoaded', function () {
        initForms(document);
    });

    if (window.jQuery) {
        jQuery(window).on('elementor/frontend/init', function () {
            elementorFrontend.hooks.addAction('frontend/element_ready/gwcre-global-review-form.default', function ($scope) {
                initForms($scope[0]);
            });
        });
    }
})();
