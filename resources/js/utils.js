export function formatPrice(amount) {
    return '₹ ' + parseFloat(amount).toFixed(2);
}

export function showError(id, msg, doScroll = false, doFocus = false) {

    const $field = $('#' + id);

    // clear old error first
    clearError(id);

    $field.closest(".form-group").addClass("has-error");
    $field.addClass("is-invalid");

    var err_id = id + "_err";
    var $err_div = $('#' + err_id);

    if ($err_div.length === 0) {

        var new_err_div = "<div id='" + err_id + "'></div>";

        if ($field.closest(".form-group").length > 0) {
            $field.closest(".form-group").after(new_err_div);
        } else {
            $field.after(new_err_div);
        }

        $err_div = $('#' + err_id);
    }

    $err_div.html('<div class="mt-1 text-danger">' + msg + '</div>');

    if (doScroll) {

        const el = document.getElementById(id);

        if (el) {

            const top = el.getBoundingClientRect().top + window.pageYOffset - 120;

            $('html, body').animate({
                scrollTop: top
            }, 400);
        }
    }

    if (doFocus) {
        $field.trigger('focus');
    }
}

export function clearError(id) {

    const $field = $('#' + id);

    $field.closest(".form-group").removeClass("has-error");
    $field.removeClass("is-invalid");

    const $err = $('#' + id + "_err");

    if ($err.length) {
        $err.html('');
    }
}

export function showGlobalError(msg) {

    $('#global_error')
        .removeClass('d-none')
        .find('.global_error_text')
        .html(msg);

    $('html, body').animate({
        scrollTop: $('#global_error').offset().top - 100
    }, 300);
}

let slugTimer;
export function autoGenerateSlug(sourceSelector, targetSelector) {

    $(document).on('input', sourceSelector, function () {

        clearTimeout(slugTimer);

        let value = $(this).val();

        slugTimer = setTimeout(function () {

            $.ajax({
                url: '/generate_slug',
                method: 'POST',
                data: {
                    title: value
                },

                success: function (response) {

                    if (response.status) {
                        $(targetSelector).val(response.slug);
                    }
                },
                error: function (xhr, status, error) {
                    console.log(xhr.responseText);
                    console.log(status);
                    console.log(error);
                }
            });

        }, 300);

    });
}

