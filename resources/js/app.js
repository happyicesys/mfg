require('./bootstrap');

//select2
window.select2 = require('select2');
// window.moment = require('moment');
// window.pikaday = require('pikaday');

$(".sidebar-dropdown > a").click(function () {
    $(".sidebar-submenu").slideUp(200);
    if (
        $(this)
            .parent()
            .hasClass("active")
    ) {
        $(".sidebar-dropdown").removeClass("active");
        $(this)
            .parent()
            .removeClass("active");
    } else {
        $(".sidebar-dropdown").removeClass("active");
        $(this)
            .next(".sidebar-submenu")
            .slideDown(200);
        $(this)
            .parent()
            .addClass("active");
    }
});

$("#close-sidebar").click(function () {
    $(".page-wrapper").removeClass("toggled");
});
$("#show-sidebar").click(function () {
    $(".page-wrapper").addClass("toggled");
});

$(document).ready(function () {
    if ($(this).width() < 576) {
        $(".page-wrapper").removeClass("toggled");
    } else {
        $(".page-wrapper").addClass("toggled");
    }

    window.livewire.on('updated', () => {
        $('.modal').modal('hide');
        $('.modal-backdrop').remove();

        // setTimeout(function () {
        //     $(".alert").hide();
        // }, 5000);

        $(".alert").fadeTo(5000, 500).slideUp(500, function () {
            $(".alert").slideUp(500);
        });
    });

    //select2
    $('.select2').select2();
});


$('#checkAll').change(function () {
    var all = this;
    $(this).closest('table').find('input[type="checkbox"]').prop('checked', all.checked);
});

window.fetch = (fetch => function () {
    return new Promise((resolve) => {
        return fetch.apply(this, arguments).then(response => {
            if (new URL(response.url).pathname.startsWith('/livewire/message') && response.status === 419) {
                alert('Your session has expired.');
                return window.location = '/login';
            }

            resolve(response);
        });
    });
})(window.fetch);

//stack bootstrap 4 modal
$(document).on({
    'show.bs.modal': function () {
        var zIndex = 1040 + (10 * $('.modal:visible').length);
        $(this).css('z-index', zIndex);
        setTimeout(function () {
            $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
        }, 0);
    },
    'hidden.bs.modal': function () {
        if ($('.modal:visible').length > 0) {
            // restore the modal-open class to the body element, so that scrolling works
            // properly after de-stacking a modal.
            setTimeout(function () {
                $(document.body).addClass('modal-open');
            }, 0);
        }
    }
}, '.modal');
