<?php

/** 
==================================================================
 File name   : include_bottom.php
 Version     : 1.0
 Begin       : 2023-01-04
 Last Update : 
 Author      : 
 Description : include all JS and OTHER SCRIPTS.
 =================================================================
 **/
?>

<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

<!-- Vendor JS Files -->
<script src="<?php echo BASE_URL; ?>assets/vendor/bootstrap/js/bootstrap.bundle.min.js?v=<?php echo FILE_VERSION; ?>"></script>

<!-- Main JS Files -->
<script type="text/javascript" src="<?php echo BASE_URL; ?>assets/js/jquery.min.js?v=<?php echo FILE_VERSION; ?>"></script>
<script type="text/javascript" src="<?php echo BASE_URL; ?>assets/js/main.js?v=<?php echo FILE_VERSION; ?>"></script>
<script type="text/javascript" src="<?php echo BASE_URL; ?>assets/js/app.js?v=<?php echo FILE_VERSION; ?>"></script>
<script type="text/javascript" src="<?php echo BASE_URL; ?>assets/js/sweetalert2.js?v=<?php echo FILE_VERSION; ?>"></script>
<script type="text/javascript" src="<?php echo BASE_URL; ?>/assets/js/tabulator.min.js?v=<?php echo FILE_VERSION; ?>"></script>
<script type="text/javascript" src="<?php echo BASE_URL; ?>assets/js/bootstrap-datetimepicker.min.js?v=<?php echo FILE_VERSION; ?>"></script>
<script type="text/javascript" src="<?php echo BASE_URL; ?>assets/js/selectize.js?v=<?php echo FILE_VERSION; ?>"></script>
<script type="text/javascript" src="<?php echo BASE_URL; ?>/assets/js/validate_mod.js?v=<?php echo FILE_VERSION; ?>"></script>

<!-- Template Main JS File -->
<script src="<?php echo BASE_URL; ?>assets/js/main-script.js?v=<?php echo FILE_VERSION; ?>"></script>

<!-- Scripts -->
<script>
    function msg_modal(title, msg, type) {
        Swal.fire(title, msg, type);
    }

    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        onOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });

    function error_notif(value = "", options) {
        opt = {
            position: 'top-end',
            timer: 3000,
            confirm: false,
            progress: true,
            bg: "#f27474",
            iconColor: "white"
        };
        if (!jQuery.isEmptyObject(options)) { // true)
            for (var prop in opt) {
                // skip loop if the property is from prototype
                if (!opt.hasOwnProperty(prop)) continue;
                if (options.hasOwnProperty(prop)) {
                    opt[prop] = options[prop];
                }

            }
        }

        Toast.fire({
            icon: 'error',
            iconColor: opt.iconColor,
            position: opt.position,
            showConfirmButton: opt.confirm,
            timer: opt.timer,
            timerProgressBar: opt.progress,
            background: opt.bg,
            customClass: {
                popup: 'colored-toast'
            },
            title: value
        })
    }

    function success_notif(value = "", options) {
        opt = {
            position: 'top-end',
            timer: 3000,
            confirm: false,
            progress: true,
            bg: "#a5dc86",
            iconColor: "white"
        };
        if (!jQuery.isEmptyObject(options)) { // true)
            for (var prop in opt) {
                // skip loop if the property is from prototype
                if (!opt.hasOwnProperty(prop)) continue;
                if (options.hasOwnProperty(prop)) {
                    opt[prop] = options[prop];
                }

            }
        }

        Toast.fire({
            icon: 'success',
            iconColor: opt.iconColor,
            position: opt.position,
            showConfirmButton: opt.confirm,
            timer: opt.timer,
            timerProgressBar: opt.progress,
            background: opt.bg,
            customClass: {
                popup: 'colored-toast'
            },
            title: value
        })
    }

    function warning_notif(value = "", options) {

        opt = {
            position: 'top-end',
            timer: 3000,
            confirm: false,
            progress: true,
            bg: "#f8bb86",
            iconColor: "white"
        };
        if (!jQuery.isEmptyObject(options)) { // true)
            for (var prop in opt) {
                // skip loop if the property is from prototype
                if (!opt.hasOwnProperty(prop)) continue;
                if (options.hasOwnProperty(prop)) {
                    opt[prop] = options[prop];
                }

            }
        }
        Toast.fire({
            icon: 'warning',
            iconColor: opt.iconColor,
            position: opt.position,
            showConfirmButton: opt.confirm,
            timer: opt.timer,
            timerProgressBar: opt.progress,
            background: opt.bg,
            customClass: {
                popup: 'colored-toast'
            },
            title: value
        })
    }

    function info_notif(value = "", options) {

        opt = {
            position: 'top-end',
            timer: 3000,
            confirm: false,
            progress: true,
            bg: "#3fc3ee",
            iconColor: "white"
        };
        if (!jQuery.isEmptyObject(options)) { // true)
            for (var prop in opt) {
                // skip loop if the property is from prototype
                if (!opt.hasOwnProperty(prop)) continue;
                if (options.hasOwnProperty(prop)) {
                    opt[prop] = options[prop];
                }

            }
        }
        Toast.fire({
            icon: 'info',
            iconColor: opt.iconColor,
            position: opt.position,
            showConfirmButton: opt.confirm,
            timer: opt.timer,
            timerProgressBar: opt.progress,
            background: opt.bg,
            customClass: {
                popup: 'colored-toast'
            },
            title: value
        })
    }

    function question_notif(value = "", options) {

        opt = {
            position: 'top-end',
            timer: 3000,
            confirm: false,
            progress: true,
            bg: "#87adbd",
            iconColor: "white"
        };
        if (!jQuery.isEmptyObject(options)) { // true)
            for (var prop in opt) {
                // skip loop if the property is from prototype
                if (!opt.hasOwnProperty(prop)) continue;
                if (options.hasOwnProperty(prop)) {
                    opt[prop] = options[prop];
                }

            }
        }
        Toast.fire({
            icon: 'info',
            iconColor: opt.iconColor,
            position: opt.position,
            showConfirmButton: opt.confirm,
            timer: opt.timer,
            timerProgressBar: opt.progress,
            background: opt.bg,
            customClass: {
                popup: 'colored-toast'
            },
            title: value
        })
    }

    // app_onsite main sweetalert
    function msg_alert(title, icon) {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-right',
            iconColor: 'white',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            },
            customClass: {
                popup: 'colored-toast'
            }
        })
        Toast.fire({
            title: title,
            icon: icon
        })
    }

    function msg_html(img, name, position, remark) {
        Swal.fire({
            html: `<div class="row">
                        <div class="col-12">` + img + `</div>
                        <div class="row">
                            <div class="col-12 fw-bold fs-3 text-dark">` + name + `</div>
                            <div class="col-12">` + position + `</div>
                        </div>
                    </div>`,
            footer: remark,
            showConfirmButton: false,
            timerProgressBar: true,
            width: 450,
            // background: '#438ff4',
            customClass: {
                popup: 'popup-custom',
                footer: 'footer-custom-breakin'
            },
            // timer: 2000
        });
    }

    function msg_error(title, msg) {
        Swal.fire({
            // position: 'top-end',
            title: title,
            text: msg,
            icon: 'error',
            timerProgressBar: true,
            showConfirmButton: false,
            timer: 2000
        });
    }

    function msg_warning(title, msg) {
        Swal.fire({
            // position: 'top-end',
            title: title,
            text: msg,
            icon: 'warning',
            timerProgressBar: true,
            showConfirmButton: false,
            timer: 2000
        });
    }
</script>